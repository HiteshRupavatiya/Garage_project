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

    /**
     * Get listing of customer car service requests for specific logged in garage owner with searching and pagination.
     *
     * @param  mixed $request
     * @return json response
     */
    public function list(Request $request)
    {
        $this->ListingValidation();
        $query = CarServicing::query()->with('car')->where('garage_id', Auth::user()->garage->id);

        $searchableFields = ['status'];

        $data = $this->filterSearchPagination($query, $searchableFields);

        return ok('Cars servicing list fetched successfully', [
            'car_servicing'  => $data['query']->get(),
            'count'          => $data['count']
        ]);
    }

    /**
     * Customer select garage, car, service type, and after apply for the service request to the garage owner via email.
     *
     * @param  mixed $request
     * @return json response
     */
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
                return error('Car service already exists', type: 'validation');
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
                return ok('Car service added successfully', $car_servicing);
            }
        }
        return error('Garage has no available requested service', type: 'notfound');
    }

    /**
     * Authenticate garage owner update car service status for specific car service which can be on their garage.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return json response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'string|in:In-Progress,Delay,Complete,Delivered',
        ]);

        $car_servicing = CarServicing::where([['id', $id], ['garage_id', Auth::user()->garage->id]])->first();
        if ($car_servicing) {
            if (isset(request()->status)) {
                $car_servicing->update($request->only('status'));
                return ok('Car service updated successfully');
            }
            return ok('Car service updated successfully');
        }
        return error('Car Service Not Found', type: 'notfound');
    }

    /**
     * Authenticate garage owner soft delete car service which can be on their garage.
     *
     * @param  mixed $id
     * @return json response
     */
    public function delete($id)
    {
        $car_servicing = CarServicing::where([['id', $id], ['garage_id', Auth::user()->garage->id]])->first();
        if ($car_servicing) {
            $car_servicing->delete();
            return ok('Car service deleted successfully');
        }
        return error('Car service not found', type: 'notfound');
    }

    /**
     * Authenticate garage owner force delete car service which can be on their garage.
     *
     * @param  mixed $id
     * @return json response
     */
    public function forceDelete($id)
    {
        $car_servicing = CarServicing::onlyTrashed()->where([['id', $id], ['garage_id', Auth::user()->garage->id]])->first();
        if ($car_servicing) {
            $car_servicing->forceDelete();
            return ok('Car service force deleted successfully');
        }
        return error('Car service not found', type: 'notfound');
    }
}
