<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AirlineEventRequest;
use App\Http\Resources\AirlineEventResource;
use App\Models\AirlineEvent;

class AirlineEventController extends Controller
{
    /**
     * Display a list of airline events.
     */
    public function index(AirlineEventRequest $request)
    {
        return AirlineEventResource::collection(
            AirlineEvent::with('airline')->filter($request->only([
                'date_from',
                'date_to',
                'next_week_only',
                'activity_type',
                'departure_airport',
                'arrival_airport',
            ]))->latest()->paginate(30)
        );
    }
}
