<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Garage;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
use App\Mail\MechanicWelcomeEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class GarageController extends Controller
{
    use ListingApiTrait;

    /**
     * Garage searching list filter for search near by garage with service type,
     * country, state, city for customer with pagination and sorting.
     *
     * @param  mixed $request
     * @return json response
     */
    public function list(Request $request)
    {
        $this->ListingValidation();
        $query = Garage::query()->has('garageServiceTypes');

        $request->validate([
            'service_type' => 'nullable|exists:service_types,id'
        ]);

        $query = $query->where('country_id', $request->country);

        if ($request->state) {
            $query->orWhere('state_id', $request->state);
        }

        if ($request->city) {
            $query->orWhere('city_id', $request->city);
        }

        if (isset($request->service_type)) {
            $query->orWhereHas('garageServiceTypes', function ($q) use ($request) {
                $q->where('service_type_id', $request->service_type);
            });
        }

        $searchableFields = ['garage_name', 'address1', 'address2'];

        $data = $this->filterSearchPagination($query, $searchableFields);

        return ok('Garages fetched successfully', [
            'garages' => $data['query']->get(),
            'count'   => $data['count']
        ]);
    }

    /**
     * Add garage for login garage owner with multiple garage service types.
     *
     * @param  mixed $request
     * @return json response
     */
    public function create(Request $request)
    {
        $request->validate([
            'garage_name'      => 'required|string|min:5|max:30|unique:garages,garage_name',
            'address1'         => 'required|string|min:10|max:150',
            'address2'         => 'string|min:10|max:150',
            'zip_code'         => 'required|numeric|digits:6',
            'city_id'          => 'required|exists:cities,id',
            'state_id'         => 'required|exists:states,id',
            'country_id'       => 'required|exists:countries,id',
            'service_type_id'  => 'required|array|exists:service_types,id'
        ]);

        $user_id = Auth::user()->id;
        $address = $request->address2 ?? null;

        $garage = Garage::create(
            $request->only(
                [
                    'garage_name',
                    'address1',
                    'zip_code',
                    'city_id',
                    'state_id',
                    'country_id'
                ]
            ) + [
                'address2' => $address,
                'owner_id' => $user_id
            ]
        );

        $garage->garageServiceTypes()->attach($request->service_type_id);

        $user = User::where('id', $user_id)->first();
        if ($user->garage_id == null) {
            $user->update([
                'garage_id' => $garage->id
            ]);
        }

        return ok('Garage created successfully', $garage);
    }

    /**
     * Get specified detail of garage for logged in garage owner.
     *
     * @param  mixed $id
     * @return json response
     */
    public function get($id)
    {
        $garage = Garage::where('owner_id', Auth::user()->id)->find($id);
        if ($garage) {
            return ok('Garage fetched successfully', $garage);
        }
        return error('Garage not found', type: 'notfound');
    }

    /**
     * Update specified garage details with garage service types for logged in garage owner.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return json response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'garage_name'      => 'required|string|min:5|max:30|unique:garages,garage_name',
            'address1'         => 'required|string|min:10|max:150',
            'address2'         => 'required|string|min:10|max:150',
            'service_type_id'  => 'array|exists:service_types,id'
        ]);

        $garage = Garage::where('owner_id', Auth::user()->id)->find($id);

        if ($garage) {
            $garage->update($request->only(
                [
                    'garage_name',
                    'address1',
                    'address2'
                ]
            ));

            if ($request->has('service_type_id')) {
                $garage->garageServiceTypes()->sync($request->service_type_id);
            }

            return ok('Garage updated successfully');
        }
        return error('Garage not found', type: 'notfound');
    }

    /**
     * Soft delete specified garage of logged in garage owner.
     *
     * @param  mixed $id
     * @return json response
     */
    public function delete($id)
    {
        $garage = Garage::where('owner_id', Auth::user()->id)->find($id);
        if ($garage) {
            $garage->delete();
            return ok('Garage deleted successfully');
        }
        return error('Garage not found', type: 'notfound');
    }

    /**
     * Force delete specified garage with garage service types of logged in garage owner.
     *
     * @param  mixed $id
     * @return json response
     */
    public function forceDelete($id)
    {
        $garage = Garage::onlyTrashed()->where('owner_id', Auth::user()->id)->find($id);
        if ($garage) {
            $garage->forceDelete();
            $garage->garageServiceTypes()->detach();
            return ok('Garage force deleted successfully');
        }
        return error('Garage not found', type: 'notfound');
    }

    /**
     * Logged in garage owner add multiple mechanic to their garage and send welcome email to mechanic,
     * with their login credintials.
     *
     * @param  mixed $request
     * @return json response
     */
    public function addMechanic(Request $request)
    {
        $request->validate([
            'first_name'            => 'required|alpha|min:5|max:30',
            'last_name'             => 'required|alpha|min:5|max:30',
            'email'                 => 'required|email|unique:users,email|max:40',
            'phone'                 => 'required|numeric|digits:10|unique:users,phone',
            'password'              => 'required|min:8|max:15',
            'password_confirmation' => 'required|same:password',
            'profile_picture'       => 'required|image|mimes:png,jpg,jpeg',
            'type'                  => 'required|in:Mechanic',
            'address1'              => 'required|string|min:10|max:150',
            'address2'              => 'nullable|string|min:10|max:150',
            'zip_code'              => 'required|numeric|digits:6',
            'city_id'               => 'required|exists:cities,id',
            'service_type_id'       => 'required|exists:service_types,id'
        ]);

        $garage_id = Garage::where('owner_id', Auth::user()->id)->first();

        $address2 = $request->address2 ?? null;
        $password = $request->password;

        $file = $request->file('profile_picture');
        $file_name = time() . $file->getClientOriginalName();

        $user = User::create(
            $request->only(
                [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'address1',
                    'address2',
                    'zip_code',
                    'city_id',
                    'type',
                    'service_type_id'
                ]
            )
                + [
                    'password'                 => Hash::make($request->password),
                    'remember_token'           => Str::random(10),
                    'email_verification_token' => Str::random(64),
                    'profile_picture'          => $file_name,
                    'billable_name'            => $request->first_name . ' ' . $request->last_name,
                    'garage_id'                => $garage_id->id,
                    'address2'                 => $address2
                ]
        );

        $user->userServiceTypes()->attach($request->service_type_id);

        if ($user) {
            $file->move(public_path('storage/'), $file_name);
        }

        Mail::to($user->email)->send(new MechanicWelcomeEmail($user, $password));

        Mail::to($user->email)->send(new VerifyEmail($user));

        $token = $user->createToken('API Token')->accessToken;

        return ok('Mechanic added successfully', $user);
    }
}
