<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'nombre'     => 'required|string',
            'apellido'   => 'required|string',
            'email'      => 'required|string|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'rol'        => 'required|string|exists:roles,name', // Verifica que el rol exista en la tabla roles
        ]);

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Creación del usuario
            $user = User::create([
                'nombre'   => $request->nombre,
                'apellido' => $request->apellido,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Asignar el rol al usuario
            $user->assignRole($request->rol);

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'message' => 'Usuario creado correctamente.',
                'user'    => $user,
            ], 201);
        } catch (\Exception $e) {
            // En caso de error, hacer rollback
            DB::rollBack();

            return response()->json([
                'message' => 'Error al registrar el usuario.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales incorrectas.'], 401);
        }

        $user = JWTAuth::user();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'roles' => $user->getRoleNames(), // Los roles del usuario
            'permissions' => $user->getAllPermissions()->pluck('name'), // Solo los nombres de los permisos
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}
