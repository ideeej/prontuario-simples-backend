<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verifica se o usuário logado (Autenticado) está tentando ver os próprios pacientes
        $user = Auth::user();
        if (Auth::id() !== $user->id) {
            // return response()->json($user->patients);
            return response()->json(['message' => 'Não autorizado'], 403);

        }

        // Retorna APENAS os pacientes associados àquele usuário (Terapeuta)
        return response()->json($user->patients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $user = Auth::user();
        if (Auth::id() !== $user->id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $data = $request->validated();

        try {
            $patient = $user->patients()->create($data);

            return response()->json($patient, 201);

        } catch (Exception $e) {
            dd($e);

            return response()->json(['message' => 'Falha ao inserir o Paciente.'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patients()->findOrFail($user->id);

        if (Auth::id() !== $user->id) {
            return response()->json(['message' => 'Não autorizado ou Paciente não encontrado.'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Todo: create validated...
        $user = Auth::user();
        $data = $request->all();
        $patient = $user->patients()->findOrFail($data['patient_id']);

        if (Auth::id() !== $patient->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        try {
            $patient->update($data);

            return response()->json($patient, 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Falha ao atualizar o paciente'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $patient = $user->patients()->findOrFail($data['patient_id']);

        if (Auth::id() !== $patient->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        try {
            $patient->delete();

            return response()->json(null, 204);

        } catch (Exception $e) {
            return response()->json(['message' => 'Falha ao remover o paciente'], 400);
        }
    }
}
