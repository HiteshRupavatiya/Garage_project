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

        return ok('Garages Fetched Successfully', [
            'garages' => $data['query']->get(),
            'count'   => $data['count']
        ]);
    }

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

        return ok('Garage Created Successfully', $garage);
    }

    public function get($id)
    {
        $garage = Garage::where('owner_id', Auth::user()->id)->find($id);
        if ($garage) {
            return ok('Garage Fetched Successfully', $garage);
        }
        return error('Garage Not Found');
    }

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

            return ok('Garage Updated Successfully');
        }
        return error('Garage Not Found');
    }

    public function delete($id)
    {
        $garage = Garage::where('owner_id', Auth::user()->id)->find($id);
        if ($garage) {
            $garage->delete();
            return ok('Garage Deleted Successfully');
        }
        return error('Garage Not Found');
    }

    public function forceDelete($id)
    {
        $garage = Garage::onlyTrashed()->where('owner_id', Auth::user()->id)->find($id);
        if ($garage) {
            $garage->forceDelete();
            $garage->garageServiceTypes()->detach();
            return ok('Garage Force Deleted Successfully');
        }
        return error('Garage Not Found');
    }

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

        return ok('Mechanic Registered Successfully', $user);
    }
}
