<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserServiceType extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsToMany(User::class, 'users', 'user_id', 'service_type_id');
    }

    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
