<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    public function index(Request $request) {
        return  Appointment::all();
    }
    public function store(Request $request) {
        $appointment = Appointment::create($request->all());
        return response()->json(['message' => 'Agendamento criado com sucesso!'], 201);
    }
    public function show(Request $request, string $id) {
        $appointment = Appointment::find($id);
        return response()->json($appointment, 200);
    }
    public function update(Request $request, string $id) {
        $appointment = Appointment::find($id);
        $appointment->update($request->all());
        $appointment->save();
        return response()->json(['message'=> 'Agendamento alterado com sucesso.'], 200);
    }
    public function destroy(Request $request, string $id) {
        Appointment::destroy($id);
        return response()->json(['message' => 'Agendamento excluido com sucesso.'], 204);
    }
}
