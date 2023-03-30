<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarServicing extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'garage_id',
        'car_id',
        'service_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }

    public function carServicingJobs()
    {
        return $this->hasMany(CarServicingJob::class, 'car_servicing_id');
    }
}
