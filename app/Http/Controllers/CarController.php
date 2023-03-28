<?php

namespace App\Http\Controllers;

use App\Models\Cars;
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
            'cars'  => $data['query']->get(),
            'count' => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'company_name'       => 'required|string|max:40',
            'model_name'         => 'required|string|max:30',
            'manufacturing_year' => 'required|date|date_format:Y-m-d|before:' . now(),
            'garage_id'          => 'required|exists:garages,id',
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
