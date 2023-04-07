<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cars extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_name',
        'model_name',
        'manufacturing_year',
        'owner_id'
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
     * Inverse relation Cars to User
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Cars to Carservicing hasMany relation
     *
     * @return void
     */
    public function carServices()
    {
        return $this->hasMany(CarServicing::class, 'car_id', 'id');
    }

    /**
     * Cars to CarServicingJob relation through CarServicing
     *
     * @return void
     */
    public function carServicingJobs()
    {
        return $this->hasManyThrough(CarServicingJob::class, CarServicing::class, 'car_id', 'car_servicing_id');
    }
}
