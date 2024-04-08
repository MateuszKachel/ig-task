<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

class AirlineEvent extends Model
{
    protected $fillable = [
        'airline_id',
        'date',
        'dc',
        'check_in_time_utc',
        'check_out_time_utc',
        'activity_type',
        'activity',
        'activity_remark',
        'departure_airport',
        'departure_time_utc',
        'arrival_airport',
        'arrival_time_utc',
        'ac_hotel',
        'block_hours',
        'flight_time',
        'night_time',
        'duration',
        'ext',
        'pax_booked',
        'tail_number',
    ];

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    public function scopeFilter(QueryBuilder|Builder $query, array $filters): void
    {
        $currentFakeDate = new Carbon('2022-01-14');

        if (!empty($filters['next_week_only'])) {
            $query->whereBetween(
                'date',
                [
                    $currentFakeDate->endOfWeek()->addDay()->format('Y-m-d'),
                    $currentFakeDate->endOfWeek()->addDays(7)->format('Y-m-d')
                ]
            );
        } else {
            $query->when(
                $filters['date_from'] ?? null,
                fn($query) => $query->where('date', '>=', $filters['date_from'])
            )->when(
                $filters['date_to'] ?? null,
                fn($query) => $query->where('date', '<=', $filters['date_to'])
            );
        }

        $query->when(
            $filters['activity_type'] ?? null,
            fn($query, $activityType) => $query->where('activity_type', $filters['activity_type'])
        )->when(
            $filters['departure_airport'] ?? null,
            fn($query, $departureAirport) => $query->where('departure_airport', $filters['departure_airport'])
        )->when(
            $filters['arrival_airport'] ?? null,
            fn($query, $arrivalAirport) => $query->where('arrival_airport', $filters['arrival_airport'])
        );
    }

}
