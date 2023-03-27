<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use App\Models\User;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GarageController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $garages = Garage::query();

        $searchableFields = ['garage_name', 'address1', 'address2'];

        $data = $this->filterSearchPagination($garages, $searchableFields);

        return ok('Garages Fetched Successfully', [
            'Garages' => $data['query']->get(),
            'count'   => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'garage_name' => 'required|string|min:5|max:30|unique:garages,garage_name',
            'address1'    => 'required|string|min:10|max:150',
            'address2'    => 'string|min:10|max:150',
            'zip_code'    => 'required|numeric|digits:6',
            'city_id'     => 'required|exists:cities,id',
            'state_id'    => 'required|exists:states,id',
            'country_id'  => 'required|exists:countries,id',
        ]);

        $user_id = Auth::user()->id;
        $address = $request->address2 ?? null;

        $garage = Garage::create(
            $request->only(
                [
                    'garage_name',
                    'address1',
                    'zip_code',
                    'city_id',
                    'state_id',
                    'country_id'
                ]
            ) + [
                'address2' => $address,
                'owner_id' => $user_id
            ]
        );

        $user = User::where('id', $user_id)->first();

        $user->update([
            'garage_id' => $garage->id
        ]);

        return ok('Garage Created Successfully');
    }

    public function get($id)
    {
        $garage = Garage::find($id);
        if ($garage) {
            return ok('Garage Fetched Successfully');
        }
        return error('Garage Not Found');
    }

    public function update(Request $request, $id)
    {
    }

    public function delete($id)
    {
    }
}
