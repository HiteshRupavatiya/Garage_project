<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarServicingJob extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'car_servicing_id',
        'mechanic_id',
        'service_type_id',
        'status',
        'description'
    ];

    /**
     * Inverse relation CarServicingJob to CarServicing
     *
     * @return void
     */
    public function carServiceJob()
    {
        return $this->belongsTo(CarServicing::class, 'car_servicing_id');
    }

    /**
     * Inverse relation CarServicingJob to User
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }
}
