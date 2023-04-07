<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'state_name',
        'country_id'
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
     * Inverse relation state to country
     *
     * @return void
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * State to city hasMany relation
     *
     * @return void
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'state_id', 'id');
    }
}
