<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        // IMPORTANTE: Força role 'user' (segurança!)
        $data['role'] = 'user';

        try {
            $user = User::create($data);

            // Cria token para login automático após registro
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuário registrado com sucesso',
                'user' => $user->toResource(),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao registrar o usuário',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            $user = User::where('email', $data['email'])->firstOrFail();
            if (! $user || ! Hash::check($data['password'], $user->password)) {
                throw new Exception;
            }
            // $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user->toResource(),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao registrar o usuário',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'user' => $user->toResource(),
            'statistics' => [
                'total_patients' => $user->patients()->count(),
                'total_sessions' => $user->sessions()->count(),
            ],
        ]);
    }

    public function logout(): JsonResponse
    {
        // Pega o usuário autenticado
        $user = Auth::user();

        // Revoga o token atual
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ], 200);
    }
}
