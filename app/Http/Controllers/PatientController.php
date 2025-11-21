<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Models\Patient;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        // Verifica se o usuário logado (Autenticado) está tentando ver os próprios pacientes
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
    public function store(StorePatientRequest $request, User $user)
    {
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
    public function show(User $user, Patient $patient)
    {
        if ($patient->user_id !== $user->id) {
            return response()->json(['message' => 'Paciente não encontrado para este usuário.'], 404);
        }

        if (Auth::id() !== $user->id) {
            return response()->json(['message' => 'Não autorizado ou Paciente não encontrado.'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $data = $request->all();

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
    public function destroy(Patient $patient)
    {
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
