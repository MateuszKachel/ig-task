<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RosterRequest;
use App\RosterParsers\RosterHandler;

class RosterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(RosterRequest $request)
    {
        $rosterHandler = new RosterHandler(
            $request->only(['airline', 'system', 'file_type', 'file_content'])
        );

        $rosterHandler->store();

        return response()->json([
            'message' => 'Roster file has been successfully processed.',
        ], 201);
    }
}
