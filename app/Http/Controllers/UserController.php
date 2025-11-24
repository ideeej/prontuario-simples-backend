<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::orderByDesc('created_at')->paginate(10);

            return response()->json($users->toResourceCollection(), 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar os usuários',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $user = User::create(attributes: $data);

            return response()->json($user->toResource(), 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao inserir o usuário',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);

            return new UserResource($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao mostrar o usuário',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update(attributes: $data);

            return new UserResource($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao atualizar o usuário',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao remover o usuário',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
