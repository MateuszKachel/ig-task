<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airline extends Model
{
    protected $fillable = [
        'name',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(AirlineEvent::class);
    }

    public function rosters(): HasMany
    {
        return $this->hasMany(AirlineRoster::class);
    }
}
