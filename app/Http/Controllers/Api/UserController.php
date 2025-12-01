<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // LISTAR USUARIOS (con paginación + búsqueda)
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 10);

        // Buscar por "nombre"
        $nombre = $request->query('search', $request->query('nombre'));

        $query = User::select('id', 'name', 'email', 'role', 'created_at');

        // Filtro texto
        if (!empty($nombre)) {
            $query->where(function($q) use ($nombre) {
                $q->where('name', 'LIKE', "%{$nombre}%")
                  ->orWhere('email', 'LIKE', "%{$nombre}%");
            });
        }

        $users = $query->paginate($perPage);

        // Añadir nombre + apellidos
        $users->getCollection()->transform(function ($u) {
            $partes = explode(' ', $u->name, 2);
            $u->nombre = $partes[0] ?? '';
            $u->apellidos = $partes[1] ?? '';
            return $u;
        });

        return response()->json($users);
    }

    // CREAR USUARIO
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|in:admin,auditor,user',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user'    => $user
        ]);
    }

    // MOSTRAR USUARIO
    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    // ACTUALIZAR USUARIO
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',  
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|string|in:admin,auditor,user',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        // Actualizar SIEMPRE todos los campos validados
        $user->fill($validated);
        $user->save();

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user'    => $user
        ]);
    }

    // ELIMINAR USUARIO
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
