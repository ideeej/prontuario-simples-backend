<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthUserUpdateRequest;
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
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
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
            ], status: 400);
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
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
            ], 400);
        }
    }

    public function update(AuthUserUpdateRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);

                $access_token = $user->currentAccessToken();

                if ($access_token instanceof PersonalAccessToken) {
                    $access_token->delete();
                }
            }

            $user->update(attributes: $data);

            return response()->json([
                'message' => 'Informações do usuário alteradas com sucesso.',
                'user' => $user->toResource(),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao atualizar as informações do usuário.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json([
                'user' => $user->toResource(),
                'statistics' => [
                    'total_patients' => $user->patients()->count(),
                    'total_sessions' => $user->sessions()->count(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha mostrar usuário',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function logout(): JsonResponse
    {
        // Pega o usuário autenticado
        $user = Auth::user();

        $token = $user->currentAccessToken();

        // Revoga o token atual
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ], 200);
    }
}
