<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;

class CityController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $city = City::query();

        $searchableFields = ['city_name'];

        $data = $this->filterSearchPagination($city, $searchableFields);

        return ok('Cities Fetched Successfully', [
            'cities' => $data['query']->with('state')->get(),
            'count'  => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|min:4|max:30|unique:cities,city_name',
            'state_id'  => 'required|numeric|exists:states,id'
        ]);

        $city = City::create($request->only(
            [
                'city_name',
                'state_id'
            ]
        ));

        return ok('City Created Successfully', $city);
    }

    public function get($id)
    {
        $city = City::find($id);
        if ($city) {
            return ok('City Fetched Successfully', $city);
        }
        return error('City Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'city_name' => 'required|string|min:4|max:30|unique:cities,city_name',
        ]);

        $city = City::find($id);

        if ($city) {
            $city->update(
                $request->only(
                    [
                        'city_name'
                    ]
                )
            );

            return ok('City Updated Successfully');
        }

        return error('City Not Found');
    }

    public function delete($id)
    {
        $city = City::find($id);
        if ($city) {
            $city->delete();
            return ok('City Deleted Successfully');
        }
        return error('City Not Found');
    }

    public function forceDelete($id)
    {
        $city = City::onlyTrashed()->find($id);
        if ($city) {
            $city->forceDelete();
            return ok('City Forced Deleted Successfully');
        }
        return error('City Not Found');
    }
}
