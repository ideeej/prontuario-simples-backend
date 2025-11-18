<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use Illuminate\Http\Request;

class ChargesController extends Controller
{
    public function index(Request $request) {
        return  Charge::all();
    }
    public function store(Request $request) {
        $appointment = Charge::create($request->all());
        return response()->json(['message' => 'Cobrança criada com sucesso!'], 201);
    }
    public function show(Request $request, string $id) {
        $charge = Charge::find($id);
        return response()->json($charge, 200);
    }
    public function update(Request $request, string $id) {
        $charge = Charge::find($id);
        $charge->update($request->all());
        $charge->save();
        return response()->json(['message'=> 'Cobrança alterada com sucesso.'], 200);
    }
    public function destroy(Request $request, string $id) {
        Charge::destroy($id);
        return response()->json(['message' => 'Cobrança excluida com sucesso.'], 204);
    }
}
