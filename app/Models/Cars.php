<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cars extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'model_name',
        'manufacturing_year',
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

    public function carServices()
    {
        return $this->belongsToMany(CarServicing::class, 'car_servicings', 'car_id', 'garage_id');
    }
}
