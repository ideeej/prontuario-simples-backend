<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);

// Rotas protegidas (exigem o Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', controller: UserController::class);

    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/{user}', [UserController::class, 'me']);
    Route::apiResource('/patients', controller: PatientController::class)->except(['index', 'store', 'show']);
    Route::get('/{user}/patients', [PatientController::class, 'index']);
    Route::post('/{user}/patients', [PatientController::class, 'store']);
    Route::get('/{user}/patients/{patient}', [PatientController::class, 'show']);

    Route::apiResource('/sessions', TherapySessionController::class)->except(['index', 'store']);
    Route::get('/{user}/sessions', [TherapySessionController::class, 'index']);
    Route::post('/{user}/sessions', [TherapySessionController::class, 'store']);

});
