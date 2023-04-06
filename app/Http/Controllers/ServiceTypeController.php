<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    use ListingApiTrait;

    /**
     * Service types listing details with pagination searching and sorting.
     *
     * @param  mixed $request
     * @return json response
     */
    public function list(Request $request)
    {
        $this->ListingValidation();
        $state = ServiceType::query();

        $searchableFields = ['service_name'];

        $data = $this->filterSearchPagination($state, $searchableFields);

        return ok('Service types fetched successfully', [
            'service_types' => $data['query']->get(),
            'count'         => $data['count']
        ]);
    }

    /**
     * Add service type details
     *
     * @param  mixed $request
     * @return json response
     */
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

        return ok('Service type created successfully', $serviceType);
    }

    /**
     * Get specified detail of service type
     *
     * @param  mixed $id
     * @return json response
     */
    public function get($id)
    {
        $serviceType = ServiceType::find($id);
        if ($serviceType) {
            return ok('Service type fetched successfully', $serviceType);
        }
        return error('Service type not found', type: 'notfound');
    }

    /**
     * Update specified service type details
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return json response
     */
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

            return ok('Service type updated successfully');
        }
        return error('Service type not found', type: 'notfound');
    }

    /**
     * Soft delete specified service type
     *
     * @param  mixed $id
     * @return json response
     */
    public function delete($id)
    {
        $serviceType = ServiceType::find($id);
        if ($serviceType) {
            $serviceType->delete();
            return ok('Service type deleted successfully');
        }
        return error('Service type not found', type: 'notfound');
    }

    /**
     * Force delete specified service type
     *
     * @param  mixed $id
     * @return json response
     */
    public function forceDelete($id)
    {
        $serviceType = ServiceType::onlyTrashed()->find($id);
        if ($serviceType) {
            $serviceType->forceDelete();
            return ok('Service type forced deleted successfully');
        }
        return error('Service type not found', type: 'notfound');
    }
}
