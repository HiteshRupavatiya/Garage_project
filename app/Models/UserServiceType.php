<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserServiceType extends Model
{
    use HasFactory;

    /**
     * Inverse relation UserServiceType to User
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Inverse relation UserServiceType to ServiceType
     *
     * @return void
     */
    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
