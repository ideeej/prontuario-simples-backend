<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class TherapySessionController extends Controller
{
    public function index()
    {
        try {
            $sessions = Auth::user()
                ->sessions()
                ->with(['patients', 'charge', 'appointment'])
                ->get();

            return response()->json($sessions, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar as sessões',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function store(StoreSessionRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $sessionData = $request->validated();
            $patientsIds = $sessionData['patients_ids'];

            $userPatients = Auth::user()->patients()->pluck('id')->toArray();
            if (array_diff($patientsIds, $userPatients)) {
                return response()->json([
                    'message' => 'Erro ao criar sessão.',
                    'error' => new Exception('Paciente não encontrado.'),
                ], 400);
            }

            DB::beginTransaction();

            $session = $user->sessions()->create([
                'user_id' => $user->id,
            ]);
            $session->patients()->sync($patientsIds);
            $session->load(['patients', 'appointment', 'charge']);

            DB::commit();

            return response()->json($session, 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar sessão.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show()
    {
        try {
            $user = Auth::user();
            $therapySession = $user->sessions()
                ->where('user_id', $user->id)->first();

            $therapySession->load(['patients', 'appointment', 'charge']);

            return response()->json($therapySession, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar a sessão.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(UpdateSessionRequest $request)
    {
        try {
            $user = Auth::user();
            $therapySession = $user->sessions()
                ->where('user_id', $user->id)->first();
            $validated = $request->validated();

            DB::beginTransaction();

            // Atualizar dados da sessão
            $therapySession->update([
                'notes' => $validated['notes'] ?? $therapySession->notes,
            ]);

            DB::commit();

            return response()->json($therapySession, 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Falha ao atualizar sessão.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($sessionId)
    {
        try {
            $user = Auth::user();
            $therapySession = $user->sessions()->findOrFail($sessionId);
            $therapySession->delete();

            return response()->json(null, 204);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao deletar sessão.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Adicionar paciente a uma sessão existente
     */
    public function attachPatient($sessionId, $patientId)
    {
        try {
            $user = Auth::user();
            $session = $user->sessions()->findOrFail($sessionId);
            $patient = $user->patients()->findOrFail($patientId);

            $session->patients()->attach($patient->id);
            $session->load(['patients', 'appointment', 'charge']);

            return response()->json([
                'message' => 'Paciente adicionado à sessão com sucesso.',
                'session' => $session,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao adicionar paciente.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function detachPatient(string $sessionId, string $patientId)
    {
        try {
            $user = Auth::user();
            $session = $user->sessions()->findOrFail($sessionId);
            $patient = $user->patients()->findOrFail($patientId);

            $session->patients()->detach($patient->id);

            $session->load('patients');

            return response()->json([
                'message' => 'Paciente removido da sessão com sucesso.',
                'session' => $session,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover paciente.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function attachAppointment(string $sessionId, string $appointmentId)
    {
        try {
            $user = Auth::user();
            $session = $user->sessions()->findOrFail($sessionId);
            $appointment = $user->appointments()->findOrFail($appointmentId);

            $appointment->therapy_session()->associate($appointment->id);
            $session->save();
            $session->load(['appointment']);

            return response()->json([
                'message' => 'Agendamento adicionado à sessão com sucesso.',
                'session' => $session,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao incluir agendamento na sessão.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function detachAppointment(string $sessionId, string $appointmentId)
    {
        try {
            $user = Auth::user();
            $session = $user->sessions()->findOrFail($sessionId);
            $appointment = $user->appointments()->findOrFail($appointmentId);
            $session->appointments()->detach($appointment->id);
            $session->load('appointment');

            return response()->json([
                'message' => 'Agendamento removido da sessão com sucesso.',
                'session' => $session,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover agendamento da sessão.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
