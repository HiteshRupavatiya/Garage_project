<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $country = Country::query();

        $searchableFields = ['country_name'];

        $data = $this->filterSearchPagination($country, $searchableFields);

        return ok('Countries Fetched Successfully', [
            'countries' => $data['query']->with('states')->get(),
            'count'     => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|min:4|max:30|unique:countries,country_name'
        ]);

        $country = Country::create($request->only(
            [
                'country_name'
            ]
        ));

        return ok('Country Created Successfully', $country);
    }

    public function get($id)
    {
        $country = Country::find($id);
        if ($country) {
            return ok('Country Fetched Successfully', $country);
        }
        return error('Country Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'country_name' => 'required|string|min:4|max:30|unique:countries,country_name'
        ]);

        $country = Country::find($id);

        if ($country) {
            $country->update(
                $request->only(
                    [
                        'country_name'
                    ]
                )
            );

            return ok('Country Updated Successfully');
        }

        return error('Country Not Found');
    }

    public function delete($id)
    {
        $country = Country::find($id);
        if ($country) {
            $country->delete();
            return ok('Country Deleted Successfully');
        }
        return error('Country Not Found');
    }

    public function forceDelete($id)
    {
        $country = Country::onlyTrashed()->find($id);
        if ($country) {
            $country->forceDelete();
            return ok('Country Forced Deleted Successfully');
        }
        return error('Country Not Found');
    }
}
