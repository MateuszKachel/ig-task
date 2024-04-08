<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirlineRoster extends Model
{
    protected $fillable = [
        'airline_id',
        'hash',
        'system',
        'file_type',
    ];

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }
}
