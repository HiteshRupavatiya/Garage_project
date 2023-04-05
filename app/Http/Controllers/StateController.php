<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class StateController extends Controller
{
    use ListingApiTrait;

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

    public function get($id)
    {
        $state = State::find($id);
        if ($state) {
            return ok('State fetched successfully', $state);
        }
        return error('State not found', type: 'notfound');
    }

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

    public function delete($id)
    {
        $state = State::find($id);
        if ($state) {
            $state->delete();
            return ok('State deleted successfully');
        }
        return error('State not found', type: 'notfound');
    }

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
