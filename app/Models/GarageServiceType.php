<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarageServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'garage_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function garage()
    {
        return $this->belongsToMany(Garage::class, 'garages', 'garage_id', 'service_type_id');
    }

    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
