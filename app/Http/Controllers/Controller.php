<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    function index(Request $request) {}

    function store(Request $request) {}

    function show(Request $request, string $id) {}

    function update(Request $request, string $id) {}

    function destroy(Request $request, string $id) {}

}
