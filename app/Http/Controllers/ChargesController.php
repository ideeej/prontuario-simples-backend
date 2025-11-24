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
            $charges = $user->charges()->with(['therapySessions'])->get();

            return response()->json(['charges' => $charges], 200);
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
                'charge' => $charge], 201);
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

            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao remover cobrança',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
