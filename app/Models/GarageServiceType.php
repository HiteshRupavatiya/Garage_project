<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarageServiceType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_type_id',
        'garage_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Inverse relation GarageServiceType to Garage
     *
     * @return void
     */
    public function garage()
    {
        return $this->belongsToMany(Garage::class, 'garages', 'garage_id', 'service_type_id');
    }

    /**
     * Inverse relation GarageServiceType to ServiceType
     *
     * @return void
     */
    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
