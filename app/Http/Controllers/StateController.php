<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class StateController extends Controller
{
    use ListingApiTrait;

    /**
     * States listing with their country detail and pagination searching and sorting.
     *
     * @param  mixed $request
     * @return json response
     */
    public function list(Request $request)
    {
        $this->ListingValidation();
        $state = State::query()->with('country');

        $searchableFields = ['state_name'];

        $data = $this->filterSearchPagination($state, $searchableFields);

        return ok('States fetched successfully', [
            'states' => $data['query']->get(),
            'count'  => $data['count']
        ]);
    }

    /**
     * Add state details
     *
     * @param  mixed $request
     * @return json response
     */
    public function create(Request $request)
    {
        $request->validate([
            'state_name' => 'required|string|min:4|max:30|unique:states,state_name',
            'country_id' => 'required|numeric|exists:countries,id'
        ]);

        $state = State::create($request->only(
            [
                'state_name',
                'country_id'
            ]
        ));

        return ok('State created successfully', $state);
    }

    /**
     * Get specified details of state
     *
     * @param  mixed $id
     * @return json response
     */
    public function get($id)
    {
        $state = State::find($id);
        if ($state) {
            return ok('State fetched successfully', $state);
        }
        return error('State not found', type: 'notfound');
    }

    /**
     * Update specified state details
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return json response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'state_name' => 'required|string|min:4|max:30|unique:states,state_name',
        ]);

        $state = State::find($id);

        if ($state) {
            $state->update(
                $request->only(
                    [
                        'state_name'
                    ]
                )
            );

            return ok('State updated successfully');
        }

        return error('State not found', type: 'notfound');
    }

    /**
     * Soft delete specified state
     *
     * @param  mixed $id
     * @return json response
     */
    public function delete($id)
    {
        $state = State::find($id);
        if ($state) {
            $state->delete();
            return ok('State deleted successfully');
        }
        return error('State not found', type: 'notfound');
    }

    /**
     * Force delete specified state
     *
     * @param  mixed $id
     * @return json response
     */
    public function forceDelete($id)
    {
        $state = State::onlyTrashed()->find($id);
        if ($state) {
            $state->forceDelete();
            return ok('State forced deleted successfully');
        }
        return error('State not found', type: 'notfound');
    }
}
