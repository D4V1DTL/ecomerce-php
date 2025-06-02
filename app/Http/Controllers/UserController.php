<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Mostrar perfil del usuario autenticado
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // Actualizar perfil del usuario autenticado
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    // Listar todos los usuarios (puedes proteger con middleware si quieres)
    public function index()
    {
        return User::paginate(10);
    }
}
