<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChargesController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // ========================================
    // Autenticação
    // ========================================
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // ========================================
    // Usuários
    // ========================================
    Route::apiResource('users', UserController::class);

    // ========================================
    // Pacientes do Usuário Autenticado
    // ========================================
    // GET    /patients           -> Lista MEUS pacientes
    // POST   /patients           -> Cria UM paciente para MIM
    // GET    /patients/{id}      -> Mostra UM dos MEUS pacientes
    // PUT    /patients/{id}      -> Atualiza UM dos MEUS pacientes
    // DELETE /patients/{id}      -> Remove UM dos MEUS pacientes
    Route::apiResource('patients', PatientController::class);

    // ========================================
    // Sessões de Terapia
    // ========================================
    // GET    /sessions           -> Lista MINHAS sessões
    // POST   /sessions           -> Cria UMA sessão para MIM
    // GET    /sessions/{id}      -> Mostra UMA das MINHAS sessões
    // PUT    /sessions/{id}      -> Atualiza UMA das MINHAS sessões
    // DELETE /sessions/{id}      -> Remove UMA das MINHAS sessões

    // Ações específicas em sessões
    Route::post('/sessions/{session}/patients/{patient}', [TherapySessionController::class, 'attachPatient']);
    Route::delete('/sessions/{session}/patients/{patient}', [TherapySessionController::class, 'detachPatient']);

    Route::apiResource('sessions', TherapySessionController::class)->except(['post', 'delete']);

    // ========================================
    // Cobranças
    // ========================================
    // GET    /charges           -> Lista MINHAS cobranças
    // POST   /charges           -> Cria UMA cobrança para MIM
    // GET    /charges/{id}      -> Mostra UMA das MINHAS cobranças
    // PUT    /charges/{id}      -> Atualiza UMA das MINHAS cobranças
    // DELETE /charges/{id}      -> Remove UMA das MINHAS cobranças
    Route::apiResource('charges', ChargesController::class);
    // ========================================
    // Agendamentos
    // ========================================
    // GET    /appointments           -> Lista MEUS agendamentos
    // POST   /appointments           -> Cria UM agendamento para MIM
    // GET    /appointments/{id}      -> Mostra UM dos MEUS agendamentos
    // PUT    /appointments/{id}      -> Atualiza UM dos MEUS agendamentos
    // DELETE /appointments/{id}      -> Remove UM dos MEUS agendamentos
    Route::apiResource('appointments', AppointmentsController::class);
});

/*
|--------------------------------------------------------------------------
| Rotas Admin
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
//     Route::get('/users', [UserController::class, 'index']);
//     Route::get('/users/{user}', [UserController::class, 'show']);
//     Route::delete('/users/{user}', [UserController::class, 'destroy']);

//     Route::get('/statistics', [UserController::class, 'statistics']);
// });
