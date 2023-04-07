<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use ListingApiTrait;

    /**
     * Country listing with their states detail and pagination searching and sorting
     *
     * @param  mixed $request
     * @return json response
     */
    public function list(Request $request)
    {
        $this->ListingValidation();
        $query = Country::query()->with('states');

        $searchableFields = ['country_name'];

        $data = $this->filterSearchPagination($query, $searchableFields);

        return ok('Countries fetched successfully', [
            'countries' => $data['query']->get(),
            'count'     => $data['count']
        ]);
    }

    /**
     * Add country details
     *
     * @param  mixed $request
     * @return json response
     */
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

        return ok('Country created successfully', $country);
    }

    /**
     * Get specified detail of country
     *
     * @param  mixed $id
     * @return json response
     */
    public function get($id)
    {
        $country = Country::find($id);
        if ($country) {
            return ok('Country fetched successfully', $country);
        }
        return error('Country not found', type: 'notfound');
    }

    /**
     * Update specified country detail 
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return json response
     */
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

            return ok('Country updated successfully');
        }

        return error('Country not found', type: 'notfound');
    }

    /**
     * Soft delete specified country
     *
     * @param  mixed $id
     * @return json response
     */
    public function delete($id)
    {
        $country = Country::find($id);
        if ($country) {
            $country->delete();
            return ok('Country deleted Successfully');
        }
        return error('Country not found', type: 'notfound');
    }

    /**
     * Force delete specified country
     *
     * @param  mixed $id
     * @return json response
     */
    public function forceDelete($id)
    {
        $country = Country::onlyTrashed()->find($id);
        if ($country) {
            $country->forceDelete();
            return ok('Country forced deleted successfully');
        }
        return error('Country not found', type: 'notfound');
    }
}
