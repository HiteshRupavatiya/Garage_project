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
        'owner_id',
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

    public function garage_service_types()
    {
        return $this->hasMany(GarageServiceType::class, 'garage_id');
    }

    public function car_services()
    {
        return $this->hasMany(CarServicing::class, 'garage_id');
    }
}
