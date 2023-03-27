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
        $state = State::query();

        $searchableFields = ['state_name'];

        $data = $this->filterSearchPagination($state, $searchableFields);

        return ok('States Fetched Successfully', [
            'states' => $data['query']->with('country', 'cities')->get(),
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

        return ok('State Created Successfully', $state);
    }

    public function get($id)
    {
        $state = State::find($id);
        if ($state) {
            return ok('State Fetched Successfully', $state);
        }
        return error('State Not Found');
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

            return ok('State Updated Successfully');
        }

        return error('State Not Found');
    }

    public function delete($id)
    {
        $state = State::find($id);
        if ($state) {
            $state->delete();
            return ok('State Deleted Successfully');
        }
        return error('State Not Found');
    }

    public function forceDelete($id)
    {
        $state = State::onlyTrashed()->find($id);
        if ($state) {
            $state->forceDelete();
            return ok('State Forced Deleted Successfully');
        }
        return error('State Not Found');
    }
}
