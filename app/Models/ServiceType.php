<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'service_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function garageServiceTypes()
    {
        return $this->hasMany(GarageServiceType::class);
    }

    public function garage()
    {
        return $this->belongsToMany(Garage::class, 'garage_service_types');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function userServiceTypes()
    {
        return $this->hasMany(UserServiceType::class);
    }
}
