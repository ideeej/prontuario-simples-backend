<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Exception;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Patient::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            $patient = new Patient();
            $patient->fill(attributes: $data);
            $patient->save();
            return response()->json($patient, 201);

        } catch(Exception $e) {
            return response()->json(['message' => 'Falha ao inserir o Paciente.'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $patient = Patient::findOrFail($id);
            return response()->json($patient->toResource(), 200);
        } catch(Exception $e) {
            return response()->json(['message' => 'Paciente não encontrado.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        try {
            $patient = Patient::findOrFail($id);
            $patient->update(attributes: $data);
            return response()->json($patient, 200);

        } catch(Exception $e) {
            return response()->json(['message' => 'Falha ao atualizar o paciente'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {

            $removed = Patient::destroy($id);
            if(!$removed) {
                throw new Exception();
            }
            return response()->json(null, 204);

        } catch(Exception $e) {
            return response()->json(['message' => 'Falha ao remover o paciente'], 400);
        }
    }
}
