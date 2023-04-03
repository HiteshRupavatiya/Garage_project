<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\CarServicing;
use App\Models\Garage;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $car_id = Cars::where('owner_id', Auth::user()->id)->find($request->car_id);

        if ($car_id) {
            // return $garage_service_type[0]->user->email;

            $car_servicing = Garage::where('id', $request->garage_id)->with('garageServiceTypes')->get()->pluck('garageServiceTypes');

            return $car_servicing[0];
            // return $car_servicing;

            if ($car_servicing) {
                $car_servicing1 = CarServicing::create(
                    $request->only(
                        [
                            'garage_id',
                            'car_id'
                        ]
                    ) + [
                        'service_id' => $request->service_type
                    ]
                );
                return ok('Car Service Added Successfully', $car_servicing1);
            }
        }
        return error('Car Service Already Exists');
    }

    public function update(Request $request, $id)
    {
    }
}
