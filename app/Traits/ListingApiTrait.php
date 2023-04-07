<?php

namespace App\Traits;

trait ListingApiTrait
{
    /*
     * Validation for listing APIs
     * 
     */
    public function ListingValidation()
    {
        $this->validate(request(), [
            'Page'          => 'nullable|integer',
            'PerPage'       => 'nullable|integer',
            'is_active'     => 'boolean',
            'search'        => 'nullable',
            'only_trashed'  => 'integer'
        ]);
        return true;
    }

    /*
     * Search and Pagination for listing APIs
     * 
     */
    public function filterSearchPagination($query, $searchable_fields)
    {

        /*For filtering by is_active field*/
        if (isset(request()->is_active)) {
            $query = $query->where('isActive', request()->is_active);
        }

        /* Get deleted record only */
        if (request()->only_trashed) {
            $query = $query->onlyTrashed();
        }

        /* Get Garege listing by country */
        $query = $query->where('country_id', request()->country);

        /* Get Garege listing by state */
        if (request()->state) {
            $query->orWhere('state_id', request()->state);
        }

        /* Get Garege listing by city */
        if (request()->city) {
            $query->orWhere('city_id', request()->city);
        }

        /* Get Garege listing by their service types */
        if (isset(request()->service_type)) {
            $query->orWhereHas('garageServiceTypes', function ($q) use ($query) {
                $q->where('service_type_id', request()->service_type);
            });
        }

        /* Search with searchable fields */
        if (request()->search) {
            $search = request()->search;
            $query  = $query->where(function ($q) use ($search, $searchable_fields) {
                /* adding searchable fields to orwhere condition */
                foreach ($searchable_fields as $searchable_field) {
                    $q->orWhere($searchable_field, 'like', "%$search%");
                }
            });
        }

        /* Pagination */
        $count          = $query->count();
        if (request()->Page || request()->PerPage) {
            $page       = request()->Page ?? 1;
            $perPage    = request()->PerPage ?? 10;
            $query      = $query->skip($perPage * ($page - 1))->take($perPage);
        }

        return ['query' => $query, 'count' => $count];
    }
}
