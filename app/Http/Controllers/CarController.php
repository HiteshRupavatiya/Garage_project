<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\City;
use App\Models\Country;
use App\Models\Garage;
use App\Models\ServiceType;
use App\Models\State;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $cars = Cars::query();

        $searchableFields = ['company_name', 'model_name', 'manufacturing_year'];
        $data = $this->filterSearchPagination($cars, $searchableFields);

        return ok('Cars Fetched Successfully', [
            'cars'  => $data['query']->where('owner_id', Auth::user()->id)->get(),
            'count' => $data['count']
        ]);
    }

    public function searchGarage(Request $request)
    {
        $request->validate([
            'service_type' => 'nullable|exists:service_types,id'
        ]);

        $service_type = ServiceType::with('garage')->where('id', $request->service_type)->get();

        return ok('Garage Fetched Successfully', $service_type);
    }

    public function create(Request $request)
    {
        $request->validate([
            'company_name'       => 'required|string|max:40',
            'model_name'         => 'required|string|max:30',
            'manufacturing_year' => 'required|date|date_format:Y-m-d|before:' . now(),
        ]);

        $car = Cars::create(
            $request->only(
                [
                    'company_name',
                    'model_name',
                    'manufacturing_year'
                ]
            ) + [
                'owner_id' => Auth::user()->id
            ]
        );

        // $car_service = CarServicing::create(
        //     $request->only(
        //         [
        //             'garage_id',
        //         ]
        //     ) + [
        //         'car_id'     => $car->id,
        //         'service_id' => $request->service_id
        //     ]
        // );

        return ok('Car Created Successfully', $car);
    }

    public function get($id)
    {
        $car = Cars::where('owner_id', Auth::user()->id)->find($id);
        if ($car) {
            return ok('Car Fetched Successfully', $car);
        }
        return error('Car Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name'       => 'required|string|max:40',
            'model_name'         => 'required|string|max:30',
            'manufacturing_year' => 'required|date|date_format:Y-m-d|before:' . now()
        ]);

        $car = Cars::where('owner_id', Auth::user()->id)->find($id);

        if ($car) {
            $car->update($request->only(
                [
                    'company_name',
                    'model_name',
                    'manufacturing_year'
                ]
            ));

            return ok('Car Updated Successfully');
        }
        return error('Car Not Found');
    }

    public function delete($id)
    {
        $car = Cars::where('owner_id', Auth::user()->id)->find($id);
        if ($car) {
            $car->delete();
            return ok('Car Deleted Successfully');
        }
        return error('Car Not Found');
    }

    public function forceDelete($id)
    {
        $car = Cars::onlyTrashed()->where('owner_id', Auth::user()->id)->find($id);
        if ($car) {
            $car->forceDelete();
            return ok('Car Forced Deleted Successfully');
        }
        return error('Car Not Found');
    }
}
