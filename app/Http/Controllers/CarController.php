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
        $query = Cars::query()->with('carServices')->where('owner_id', Auth::user()->id);

        $searchableFields = ['company_name', 'model_name'];

        $data = $this->filterSearchPagination($query, $searchableFields);

        return ok('Cars fetched successfully', [
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

        return ok('Car created successfully', $car);
    }

    public function get($id)
    {
        $car = Cars::where('owner_id', Auth::user()->id)->find($id);
        if ($car) {
            return ok('Car fetched successfully', $car);
        }
        return error('Car not found', type: 'notfound');
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

            return ok('Car updated successfully');
        }
        return error('Car not found', type: 'notfound');
    }

    public function delete($id)
    {
        $car = Cars::where('owner_id', Auth::user()->id)->find($id);
        if ($car) {
            $car->delete();
            return ok('Car deleted successfully');
        }
        return error('Car not found', type: 'notfound');
    }

    public function forceDelete($id)
    {
        $car = Cars::onlyTrashed()->where('owner_id', Auth::user()->id)->find($id);
        if ($car) {
            $car->forceDelete();
            return ok('Car forced deleted successfully');
        }
        return error('Car not found', type: 'notfound');
    }
}
