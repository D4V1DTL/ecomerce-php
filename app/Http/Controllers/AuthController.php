<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // espera password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos de registro inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Crear token JWT
        $credentials = $request->only(['email', 'password']);
        $token = JWTAuth::attempt($credentials);
        $user = auth()->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 200);
    }

    // Login (ya tienes este)
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $credentials = $request->only(['email', 'password']);
        $token = JWTAuth::attempt($credentials);
    
        if (!$token) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
        $user = auth()->user();
    
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    // Info usuario autenticado
    public function me(Request $request)
    {
        return response()->json([
            'message' => 'Usuario autenticado.',
            'user' => $request->user()
        ]);
    }

    // Logout: invalidar token
    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo cerrar la sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
