<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_name'
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
     * ServiceType to GarageServiceType hasMany relation
     *
     * @return void
     */
    public function garageServiceTypes()
    {
        return $this->hasMany(GarageServiceType::class);
    }

    /**
     * ServiceType to Garage through many GarageServiceType
     *
     * @return void
     */
    public function garage()
    {
        return $this->belongsToMany(Garage::class, 'garage_service_types');
    }

    /**
     * ServiceType to User hasMany relation
     *
     * @return void
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * ServiceType to UserServiceType hasMany relation
     *
     * @return void
     */
    public function userServiceTypes()
    {
        return $this->hasMany(UserServiceType::class);
    }

    /**
     * ServiceType to CarServicing hasMany relation
     *
     * @return void
     */
    public function carService()
    {
        return $this->hasMany(CarServicing::class, 'service_id');
    }
}
