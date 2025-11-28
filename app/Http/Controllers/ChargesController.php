<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChargeRequest;
use App\Http\Requests\UpdateChargeRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargesController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $charges = $user->charges()->with(['patient', 'therapy_session'])->get();

            return response()->json($charges, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar suas cobranças',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function store(StoreChargeRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();
            $charge = $user->charges()->create($data);

            return response()->json([
                'message' => 'Cobrança criada com sucesso!',
                'charge' => $charge,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar nova cobrança',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $user = Auth::user();
            $charge = $user->charges()->findOrFail($id);

            return response()->json($charge, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar cobrança',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(UpdateChargeRequest $request, string $id)
    {
        try {
            $user = Auth::user();
            $charge = $user->charges()->findOrFail($id);
            $charge->update($request->validated());
            $charge->save();

            return response()->json([
                'message' => 'Cobrança alterada com sucesso.',
                'charge' => $charge], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao alterar cobrança',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function destroy(Request $request, string $id)
    {
        try {
            $user = Auth::user();
            $charge = $user->charges()->findOrFail($id);
            $charge->delete();

            return response()->json(['message' => 'Cobrança removida com sucesso.'], 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao remover cobrança',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function attachSession($chargeId, $sessionId)
    {
        try {
            $user = Auth::user();
            $charge = $user->charges()->findOrFail($chargeId);
            $session = $user->sessions()->findOrFail($sessionId);

            $charge->therapy_session_id = $session->id;
            $charge->load(['therapy_session']);
            $charge->save();

            return response()->json([
                'message' => 'Sessão associada à cobrança com sucesso.',
                'session' => $charge,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao incluir sessão à cobrança',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function detachSession($chargeId, $sessionId)
    {
        try {
            $user = Auth::user();
            $charge = $user->charges()->findOrFail($chargeId);
            $session = $user->sessions()->findOrFail($sessionId);

            $charge->therapy_session_id = null;
            $charge->load('therapy_session');
            $charge->save();

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
