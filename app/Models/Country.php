<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'country_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function states()
    {
        return $this->hasMany(State::class, 'country_id', 'id')->with('cities');
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class, State::class);
    }
}
