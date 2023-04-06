<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;

class CityController extends Controller
{
    use ListingApiTrait;

    /**
     * Cities listing with their state detail and pagination searching and sorting.
     *
     * @param  mixed $request
     * @return json response
     */
    public function list(Request $request)
    {
        $this->ListingValidation();
        $city = City::query()->with('state');

        $searchableFields = ['city_name'];

        $data = $this->filterSearchPagination($city, $searchableFields);

        return ok('Cities fetched successfully', [
            'cities' => $data['query']->get(),
            'count'  => $data['count']
        ]);
    }

    /**
     * Add city details
     *
     * @param  mixed $request
     * @return json response
     */
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

        return ok('City created successfully', $city);
    }

    /**
     * Get specified detail of city
     *
     * @param  mixed $id
     * @return json response
     */
    public function get($id)
    {
        $city = City::find($id);
        if ($city) {
            return ok('City fetched successfully', $city);
        }
        return error('City not found', type: 'notfound');
    }

    /**
     * Update specified city details
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return json response
     */
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

            return ok('City updated successfully');
        }

        return error('City not found', type: 'notfound');
    }

    /**
     * Soft delete specified city
     *
     * @param  mixed $id
     * @return json response
     */
    public function delete($id)
    {
        $city = City::find($id);
        if ($city) {
            $city->delete();
            return ok('City deleted successfully');
        }
        return error('City not found', type: 'notfound');
    }

    /**
     * Force delete specified city
     *
     * @param  mixed $id
     * @return json response
     */
    public function forceDelete($id)
    {
        $city = City::onlyTrashed()->find($id);
        if ($city) {
            $city->forceDelete();
            return ok('City forced deleted successfully');
        }
        return error('City not found', type: 'notfound');
    }
}
