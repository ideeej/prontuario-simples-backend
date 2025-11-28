<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentsController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user(); // a
            $appointments = $user->appointments()->with(['patient', 'therapy_session'])->get();

            return response()->json($appointments, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao listar agendamentos',
                'error' => $e->getMessage()]);
        }
    }

    public function store(StoreAppointmentRequest $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();
            $appointment = $user->appointments()->create($validated);

            return response()->json([
                'message' => 'Agendamento criado com sucesso!',
                'appointment' => $appointment], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar novo agendamento',
                'error' => $e->getMessage()]);
        }
    }

    public function show(Request $request, string $id)
    {
        try {
            $user = Auth::user();
            $appointment = $user->appointments()->findOrFail($id);

            return response()->json($appointment, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao mostrar agendamento',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function update(UpdateAppointmentRequest $request, string $id)
    {
        try {
            $user = Auth::user();
            $appointment = $user->appointments()->findOrFail($id);
            $appointment->update($request->validated());
            $appointment->save();

            return response()->json(['message' => 'Agendamento alterado com sucesso.'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar agendamento',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function destroy(string $appointmentId)
    {
        try {
            $user = Auth::user();
            $appointment = $user->appointments()->findOrFail($appointmentId);
            $appointment->delete();

            return response()->json(['message' => 'Agendamento excluido com sucesso.'], 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar agendamento',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function attachSession($appointmentId, $sessionId)
    {
        try {
            $user = Auth::user();
            $appointment = $user->appointments()->findOrFail($appointmentId);
            $session = $user->sessions()->findOrFail($sessionId);

            $appointment->therapy_session_id = $session->id;
            $appointment->load(['therapy_session']);
            $appointment->save();

            return response()->json([
                'message' => 'Sessão associada ao agendamento com sucesso.',
                'session' => $appointment,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao incluir sessão ao agendamento',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function detachSession($appointmentId, $sessionId)
    {
        try {
            $user = Auth::user();
            $appointment = $user->appointments()->findOrFail($appointmentId);
            $session = $user->sessions()->findOrFail($sessionId);

            $appointment->therapy_session_id = null;
            $appointment->load('therapy_session');
            $appointment->save();

            return response()->json([
                'message' => 'Agendamento removido da sessão com sucesso.',
            ], 204);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover sessão ao agendamento.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
