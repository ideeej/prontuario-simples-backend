<?php
namespace App\Http\Controllers;

use App\Models\TherapySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class TherapySessionController extends Controller
{
    /**
     * Listar todas as sessões
     */
    public function index(Request $request)
    {
        $sessions = TherapySession::with([
            'patients',      // Carrega os pacientes
            'appointment',   // Carrega o agendamento
            'charge'         // Carrega a cobrança
        ])->get();

        return response()->json($sessions, 200);
    }

    /**
     * Criar nova sessão
     */
    public function store(Request $request)
    {
        // Validação
        $validated = $request->validate([
            'therapy_record' => 'nullable|string',
            'appointment_id' => 'nullable|exists:appointments,id',
            'charge_id' => 'nullable|exists:charges,id',
            'patient_ids' => 'required|array|min:1',  // ARRAY de IDs!
            'patient_ids.*' => 'exists:patients,id',
            'roles' => 'nullable|array',              // Opcional: papel de cada paciente
        ]);

        try {
            DB::beginTransaction();

            // 1. Criar a sessão
            $session = TherapySession::create([
                'therapy_record' => $validated['therapy_record'] ?? null,
                'appointment_id' => $validated['appointment_id'] ?? null,
                'charge_id' => $validated['charge_id'] ?? null,
            ]);

            // 2. Associar os pacientes à sessão
            $session->patients()->attach($validated['patient_ids']);

            DB::commit();

            // 3. Retornar com relações carregadas
            $session->load(['patients', 'appointment', 'charge']);

            return response()->json($session, 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar sessão.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Buscar sessão específica
     */
    public function show(Request $request, string $id)
    {
        try {
            $session = TherapySession::with([
                'patients',
                'appointment',
                'charge'
            ])->findOrFail($id);

            return response()->json($session, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Sessão não encontrada.'
            ], 404);
        }
    }

    /**
     * Atualizar sessão
     */
    public function update(Request $request, string $id)
    {
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

            $session = TherapySession::findOrFail($id);

            // Atualizar dados da sessão
            $session->update([
                'therapy_record' => $validated['therapy_record'] ?? $session->therapy_record,
                'appointment_id' => $validated['appointment_id'] ?? $session->appointment_id,
                'charge_id' => $validated['charge_id'] ?? $session->charge_id,
            ]);

            // Atualizar pacientes se fornecido
            if (isset($validated['patient_ids'])) {
                $session->patients()->sync($validated['patient_ids']);
            }

            DB::commit();

            $session->load(['patients', 'appointment', 'charge']);

            return response()->json($session, 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao atualizar sessão.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Deletar sessão
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $session = TherapySession::findOrFail($id);

            // O relacionamento pivot é deletado automaticamente (cascade)
            $session->delete();

            return response()->json(null, 204);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar sessão.'
            ], 400);
        }
    }

    /**
     * Adicionar paciente a uma sessão existente
     */
    public function addPatient(Request $request, string $sessionId)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'role' => 'nullable|string',
        ]);

        try {
            $session = TherapySession::findOrFail($sessionId);

            // Adiciona o paciente (não duplica se já existir)
            $session->patients()->syncWithoutDetaching([
                $validated['patient_id'] => ['role' => $validated['role'] ?? null]
            ]);

            $session->load('patients');

            return response()->json($session, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao adicionar paciente.'
            ], 400);
        }
    }

    /**
     * Remover paciente de uma sessão
     */
    public function removePatient(string $sessionId, string $patientId)
    {
        try {
            $session = TherapySession::findOrFail($sessionId);
            $session->patients()->detach($patientId);

            $session->load('patients');

            return response()->json($session, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover paciente.'
            ], 400);
        }
    }
}
