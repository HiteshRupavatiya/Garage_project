<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garage extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'garage_name',
        'address1',
        'address2',
        'zip_code',
        'city_id',
        'state_id',
        'country_id',
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
     * Inverse relation Garage to User
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Inverse relation garage to city
     *
     * @return void
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Inverse relation garage to state
     *
     * @return void
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Inverse relation garage to country
     *
     * @return void
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Garage to GarageServiceType belongsToMany relation
     *
     * @return void
     */
    public function garageServiceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'garage_service_types', 'garage_id', 'service_type_id');
    }

    /**
     * Garage to CarServicing hasMany relation
     *
     * @return void
     */
    public function carServices()
    {
        return $this->hasMany(CarServicing::class, 'garage_id');
    }
}
