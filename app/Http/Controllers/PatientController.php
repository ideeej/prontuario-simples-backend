<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            return response()->json($user->patients);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar os pacientes',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function store(StorePatientRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();
            $patient = $user->patients()->create($data);

            return response()->json([
                'message' => 'Paciente criado com sucesso',
                'patient' => $patient,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar Paciente',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($patientId)
    {
        try {
            $user = Auth::user();
            $patient = $user->patients()->findOrFail($patientId);

            return response()->json($patient, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar o paciente',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function update(UpdatePatientRequest $request, $patientId)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();
            $patient = $user->patients()->findOrFail($patientId);

            $patient->update($data);

            return response()->json([
                'message' => 'Paciente atualizado com sucesso.',
                'patient' => $patient,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao atualizar o paciente',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($patientId)
    {
        try {
            $user = Auth::user();
            $patient = $user->patients()->findOrFail($patientId);

            $patient->delete();

            return response()->json([
                'message' => 'Paciente removido com sucesso.',
            ], 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao remover o paciente',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
