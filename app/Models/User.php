<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'remember_token',
        'email_verification_token',
        'profile_picture',
        'type',
        'billable_name',
        'address1',
        'address2',
        'zip_code',
        'city_id',
        'garage_id',
        'service_type_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * User to garage hasOne relation
     *
     * @return void
     */
    public function garage()
    {
        return $this->hasOne(Garage::class, 'owner_id', 'id');
    }

    /**
     * User to UserServiceType hasOne relation
     *
     * @return void
     */
    public function userServiceTypes()
    {
        return $this->hasOne(UserServiceType::class);
    }

    /**
     * User to Cars hasMany relation
     *
     * @return void
     */
    public function cars()
    {
        return $this->hasMany(Cars::class, 'owner_id', 'id');
    }
}
