<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas'],
            ]);
        }

        if (!$user->active) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está inativa. Entre em contato com o administrador.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id' => $user->id,
                'name' => $user->email,
                'crm' => $user->crm,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function register(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'crm' => 'required|string|unique:users',
            'phone' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'crm' => $request->crm,
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $user->only(['id', 'name', 'email', 'crm']),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
        
    } catch (\Exception $e) {
        \Log::error('Registration error: '.$e->getMessage());
        return response()->json([
            'message' => 'Erro no servidor',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'crm' => $user->crm,
                'specialty' => $user->specialty,
                'phone' => $user->phone,
                'role' => $user->role,
                'active' => $user->active,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string',
            'current_password' => 'required_with:password',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($request->has('password')) {
            if (!Hash::check($request->input('password'), $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['A senha atual está incorreta'],
                ]);
            }
        }

        $user->fill($request->only(['name', 'email', 'phone', 'specialty']));
        $user->save();

        return response()->json([
            'message' => 'Perfil atualizado com sucesso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'crm' => $user->crm,
                'specialty' => $user->specialty,
                'phone' => $user->phone,
                'role' => $user->role,
            ]
        ]);
    }
}
