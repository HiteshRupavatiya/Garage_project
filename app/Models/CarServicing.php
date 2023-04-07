<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarServicing extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'garage_id',
        'car_id',
        'service_id',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Inverse relation CarServicing to Car
     *
     * @return void
     */
    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }

    /**
     * CarServicing to CarServicingJob hasMany relation
     *
     * @return void
     */
    public function carServicingJobs()
    {
        return $this->hasMany(CarServicingJob::class, 'car_servicing_id');
    }

    /**
     * Inverse CarServicing to ServiceType relation
     *
     * @return void
     */
    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_id', 'id');
    }
}
