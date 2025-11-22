<?php

namespace App\Http\Controllers;

use App\Models\TherapySession;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TherapySessionController extends Controller
{
    /**
     * Listar todas as sessões
     */
    public function index()
    {

        $sessions = Auth::user()
            ->sessions()
            ->with(['patients', 'charges', 'appointments'])->get();

        return response()->json($sessions, 200);
    }

    /**
     * Criar nova sessão
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (Auth::id() !== $user->id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        // Validação
        $validated = $request->validate([
            'patient_ids' => 'required|array|min:1', // Agora é array!
            'patient_ids.*' => 'exists:patients,id',
            'date' => 'required|date',
            'notes' => 'required|string',
            'appointment_id' => 'nullable|exists:appointments,id',
            'charge_id' => 'nullable|exists:charges,id',
        ]);

        $patientIds = $validated['patient_ids'];
        $userPatientIds = Auth::user()->patients()->pluck('id')->toArray();

        $invalidIds = array_diff($patientIds, $userPatientIds);
        if (! empty($invalidIds)) {
            throw new Exception('Alguns pacientes não pertencem a você.');
        }

        try {
            DB::beginTransaction();

            $sessionData = $validated;
            unset($sessionData['patient_ids']);

            $session = Auth::user()->sessions()->create($sessionData);

            // Associa os pacientes (Many-to-Many)
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

    /**
     * Buscar sessão específica
     */
    public function show()
    {
        $user = Auth::user();
        $therapySession = $user->sessions()->where('user_id', $user->id)->first();
        try {
            $therapySession->load(['patients', 'appointments', 'charges']);

            return response()->json($therapySession, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Sessão não encontrada.',
            ], 404);
        }
    }

    /**
     * Atualizar sessão
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $therapySession = $user->sessions()->where('user_id', $user->id)->first();

        if ($therapySession->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }
        $validated = $request->validate([
            'therapy_record' => 'nullable|string',
            'appointment_id' => 'nullable|exists:appointments,id',
            'charge_id' => 'nullable|exists:charges,id',
            'patient_ids' => 'nullable|array|min:1',
            'patient_ids.*' => 'exists:patients,id',
            'roles' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Atualizar dados da sessão
            $therapySession->update([
                'therapy_record' => $validated['therapy_record'] ?? $therapySession->therapy_record,
                'appointment_id' => $validated['appointment_id'] ?? $therapySession->appointment_id,
                'charge_id' => $validated['charge_id'] ?? $therapySession->charge_id,
            ]);

            // Atualizar pacientes se fornecido
            if (isset($validated['patient_ids'])) {
                $therapySession->patients()->sync($validated['patient_ids']);
            }

            DB::commit();

            $therapySession->load(['patients', 'appointment', 'charge']);

            return response()->json($therapySession, 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao atualizar sessão.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Deletar sessão
     */
    public function destroy()
    {
        $user = Auth::user();
        $therapySession = $user->sessions()->where('user_id', $user->id)->first();
        try {
            // O relacionamento pivot é deletado automaticamente (cascade)
            $therapySession->delete();

            return response()->json(null, 204);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar sessão.',
            ], 400);
        }
    }

    /**
     * Adicionar paciente a uma sessão existente
     */
    public function attachPatient($sessionId, $patientId)
    {
        try {

            $session = Auth::user()->sessions()->findOrFail($sessionId);
            $patient = Auth::user()->patients()->findOrFail($patientId);

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

    /**
     * Remover paciente de uma sessão
     */
    public function detachPatient(string $sessionId, string $patientId)
    {
        try {
            $session = TherapySession::findOrFail($sessionId);
            $session->patients()->detach($patientId);

            $session->load('patients');

            return response()->json($session, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover paciente.',
            ], 400);
        }
    }
}
