<?php

namespace App\Http\Controllers;

use App\Mail\CarServiceRequestMail;
use App\Models\Cars;
use App\Models\Garage;
use App\Models\CarServicing;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CarServicingController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $request->validate([
            'country'      => 'required|exists:countries,id',
            'state'        => 'nullable|exists:states,id',
            'city'         => 'nullable|exists:cities,id',
            'service_type' => 'nullable|exists:service_types,id'
        ]);

        $query = Garage::query()->has('garageServiceTypes');

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

        $garages = $query->orderBy('id')->get();

        if (count($garages) > 0) {
            return ok('Garages Fetched Successfully', $garages);
        }
        return error('Garages Not Found');
    }

    public function create(Request $request)
    {
        $request->validate([
            'garage_id'    => 'required|exists:garages,id',
            'car_id'       => 'required|exists:cars,id',
            'service_type' => 'required|exists:service_types,id'
        ]);
        $car = Cars::with('user')->where('owner_id', Auth::user()->id)->find($request->car_id);
        $query = Garage::query();
        $garage_services = $query->where('id', $request->garage_id)->get()->pluck('garageServiceTypes');

        foreach ($garage_services as $garage_service) {
            foreach ($garage_service as $service) {
                $services[] = $service->id;
            }
        }

        if ($car && in_array($request->service_type, $services)) {
            $car_servicing_exists = CarServicing::where([['car_id', $request->car_id], ['service_id', $request->service_type]])->get();

            if (count($car_servicing_exists) > 0) {
                return error('Car Service Already Exists');
            } else {
                $car_servicing = CarServicing::create(
                    $request->only(
                        [
                            'garage_id',
                            'car_id'
                        ]
                    ) + [
                        'service_id' => $request->service_type
                    ]
                );

                $car['service_type'] = $car_servicing->service->service_name;
                $query = $query->with('user')->where('id', $request->garage_id)->first();
                $owner = $query->user;

                Mail::to($owner->email)->send(new CarServiceRequestMail($owner, $car));
                return ok('Car Service Added Successfully', $car_servicing);
            }
        }
        return error('Garage Has No Available Requested Service');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'string|in:In-Progress,Delay,Complete,Delivered',
        ]);

        $car_servicing = CarServicing::where([['id', $id], ['garage_id', Auth::user()->garage->id]])->first();
        if ($car_servicing) {
            if (isset(request()->status)) {
                $car_servicing->update($request->only('status'));
                return ok('Car Service Updated Successfully');
            }
            return ok('Car Service Updated Successfully');
        }
        return error('Car Service Not Found');
    }

    public function delete($id)
    {
        $car_servicing = CarServicing::where([['id', $id], ['garage_id', Auth::user()->garage->id]])->first();
        if ($car_servicing) {
            $car_servicing->delete();
            return ok('Car Service Deleted Successfully');
        }
        return error('Car Service Not Found');
    }

    public function forceDelete($id)
    {
        $car_servicing = CarServicing::onlyTrashed()->where([['id', $id], ['garage_id', Auth::user()->garage->id]])->first();
        if ($car_servicing) {
            $car_servicing->forceDelete();
            return ok('Car Service Force Deleted Successfully');
        }
        return error('Car Service Not Found');
    }
}
