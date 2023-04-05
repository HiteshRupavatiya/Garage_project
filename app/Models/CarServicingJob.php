<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarServicingJob extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'car_servicing_id',
        'mechanic_id',
        'service_type_id',
        'status',
        'description'
    ];

    public function carServiceJob()
    {
        return $this->belongsTo(CarServicing::class, 'car_servicing_id');
    }
}
