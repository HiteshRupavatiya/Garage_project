<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garage extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function garageServiceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'garage_service_types', 'garage_id', 'service_type_id');
    }

    public function carServices()
    {
        return $this->hasMany(CarServicing::class, 'garage_id');
    }
}
