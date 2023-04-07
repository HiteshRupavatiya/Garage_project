<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_name'
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
     * Country to state hasMany relation
     *
     * @return void
     */
    public function states()
    {
        return $this->hasMany(State::class, 'country_id', 'id');
    }

    /**
     * Country to city relation through state
     *
     * @return void
     */
    public function cities()
    {
        return $this->hasManyThrough(City::class, State::class);
    }

    /**
     * Country to garage relation 
     *
     * @return void
     */
    public function garages()
    {
        return $this->hasMany(Garage::class, 'country_id');
    }
}
