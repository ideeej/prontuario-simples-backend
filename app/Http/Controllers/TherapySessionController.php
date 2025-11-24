<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TherapySessionController extends Controller
{
    public function index()
    {
        try {
            $sessions = Auth::user()
                ->sessions()
                ->with(['patients', 'charges', 'appointments'])
                ->get();

            return response()->json($sessions, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar as sessões',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function store(StoreSessionRequest $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();
            $patientIds = $validated['patient_ids'];
            $userPatientIds = $user->patients()->pluck('id')->toArray();

            $invalidIds = array_diff($patientIds, $userPatientIds);
            if (! empty($invalidIds)) {
                throw new Exception('Não autorizado.');
            }

            DB::beginTransaction();

            $sessionData = $validated;
            unset($sessionData['patient_ids']);

            $session = $user->sessions()->create($sessionData);

            // Associa os pacientes
            $session->patients()->attach($patientIds);

            // Retornar com relações carregadas
            $session->load(['patients', 'appointments', 'charges']);

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

            $therapySession->load(['patients', 'appointments', 'charges']);

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

    public function destroy()
    {
        try {
            $user = Auth::user();
            $therapySession = $user->sessions()
                ->where('user_id', $user->id)->first();

            // O relacionamento pivot é deletado automaticamente (cascade)
            $therapySession->delete();

            return response()->json(null, 204);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao deletar sessão.',
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
            $session->load(['patients', 'appointments', 'charges']);

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
}
