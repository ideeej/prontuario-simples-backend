<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Autenticação)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Requerem Autenticação)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // ========================================
    // Autenticação
    // ========================================
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // ========================================
    // Usuários (Admin)
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
    // Relatórios e Estatísticas (Exemplos)
    // ========================================
    Route::get('/reports/sessions-summary', [TherapySessionController::class, 'summary']);
    Route::get('/reports/patients-progress', [PatientController::class, 'progress']);
});

/*
|--------------------------------------------------------------------------
| Exemplo de Rotas Admin (Opcional)
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
//     Route::get('/users', [UserController::class, 'index']);
//     Route::get('/users/{user}', [UserController::class, 'show']);
//     Route::delete('/users/{user}', [UserController::class, 'destroy']);

//     Route::get('/statistics', [UserController::class, 'statistics']);
// });
