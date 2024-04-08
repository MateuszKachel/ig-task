<?php

use App\Http\Controllers\Api\AirlineEventController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RosterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('airlineEvents', AirlineEventController::class)->only('index');
    Route::apiResource('rosters', RosterController::class)->only('store');
});
