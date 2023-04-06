<?php

namespace App\Http\Controllers;

use App\Models\CarServicing;
use App\Models\CarServicingJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarServicingJobController extends Controller
{
    /**
     * Authenticate garage owner can only add one mechanic to the car service process,
     * which can be in the garage and it provides same service type as a car service type. 
     *
     * @param  mixed $request
     * @return json response
     */
    public function create(Request $request)
    {
        $request->validate([
            'car_servicing_id' => 'required|exists:car_servicings,id',
            'mechanic_id'      => 'required|exists:users,id',
            'description'      => 'required|string'
        ]);

        $car_servicing = CarServicing::where([['id', $request->car_servicing_id], ['garage_id', Auth::user()->garage->id]])->first();
        $query = User::query();
        $mechanic = $query->where([['id', $request->mechanic_id], ['garage_id', Auth::user()->garage->id], ['service_type_id', $car_servicing->service_id]])->first();

        if ($car_servicing && $mechanic) {

            $car_servicing_job_exists = CarServicingJob::where([['car_servicing_id', $request->car_servicing_id], ['mechanic_id', $request->mechanic_id]])->get();

            if (count($car_servicing_job_exists) > 0) {
                return error('Car servicing job already exists', type: 'validation');
            }
            $car_servicing_job = CarServicingJob::create($request->only(
                [
                    'car_servicing_id',
                    'mechanic_id',
                    'description'
                ]
            ) + [
                'service_type_id' => $car_servicing->service_id,
                'status'          => 'In-Progress'
            ]);

            return ok('Car servicing job created successfully', $car_servicing_job);
        }
        return error('Car cannot be applying for servicing job', type: 'notfound');
    }

    /**
     * Authenticate garage owners mechanic can only update their assigned,
     * car service job status and the status also reflact to the car service status to parent.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return json response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:In-Progress,Complete',
            'description' => 'required|string',
        ]);

        $car_servicing_job = CarServicingJob::where('mechanic_id', Auth::user()->id)->find($id);

        if ($car_servicing_job) {
            if (isset($request->status)) {
                $car_servicing_job->update($request->only(
                    [
                        'status',
                        'description'
                    ]
                ));

                $car_servicing_job->carServiceJob->update([
                    'status' => $request->status
                ]);

                return ok('Car servicing job status updated successfully');
            }
        }
        return error('Car servicing job not found', type: 'notfound');
    }
}
