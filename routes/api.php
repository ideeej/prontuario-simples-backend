<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\ChargesController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/users', UserController::class);
Route::apiResource('/patients', PatientController::class);
Route::apiResource('/sessions', TherapySessionController::class);
Route::apiResource('/appointments', AppointmentsController::class);
Route::apiResource('/charges', ChargesController::class);
Route::post('sessions/{sessionId}/patients', [TherapySessionController::class, 'addPatient']);
Route::delete('sessions/{sessionId}/patients/{patientId}', [TherapySessionController::class, 'removePatient']);

