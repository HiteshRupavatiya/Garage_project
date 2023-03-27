<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $state = ServiceType::query();

        $searchableFields = ['service_name'];

        $data = $this->filterSearchPagination($state, $searchableFields);

        return ok('Service Types Fetched Successfully', [
            'service_types' => $data['query']->get(),
            'count'         => $data['count']
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|min:5|max:40|unique:service_types,service_name'
        ]);

        $serviceType = ServiceType::create($request->only(
            [
                'service_name'
            ]
        ));

        return ok('Service Type Created Successfully', $serviceType);
    }

    public function get($id)
    {
        $serviceType = ServiceType::find($id);
        if ($serviceType) {
            return ok('Service Type Fetched Successfully', $serviceType);
        }
        return error('Service Type Not Found');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string|min:5|max:40|unique:service_types,service_name'
        ]);

        $serviceType = ServiceType::find($id);
        if ($serviceType) {
            $serviceType->update(
                $request->only(
                    [
                        'service_name'
                    ]
                )
            );

            return ok('Service Type Updated Successfully');
        }
        return error('Service Type Not Found');
    }

    public function delete($id)
    {
        $serviceType = ServiceType::find($id);
        if ($serviceType) {
            $serviceType->delete();
            return ok('Service Type Deleted Successfully');
        }
        return error('Service Type Not Found');
    }

    public function forceDelete($id)
    {
        $serviceType = ServiceType::onlyTrashed()->find($id);
        if ($serviceType) {
            $serviceType->forceDelete();
            return ok('Service Type Forced Deleted Successfully');
        }
        return error('Service Type Not Found');
    }
}
