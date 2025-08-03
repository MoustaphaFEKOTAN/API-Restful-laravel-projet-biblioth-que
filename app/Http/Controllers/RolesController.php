<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    /**
     * Affiche tous les rôles.
     */
    public function index()
    {
        $roles = Roles::all();
        return response()->json($roles);
    }

    /**
     * Crée un nouveau rôle.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string'
        ]);

        Roles::create([
            'nom' => $request->nom,
            'slug' => Str::uuid(),
        ]);

        return response()->json(['message' => 'Rôle créé avec succès'], 201);
    }

    /**
     * Affiche un rôle spécifique via son slug.
     */
    public function show($slug)
    {
        $role = Roles::where('slug', $slug)->firstOrFail();
        return response()->json($role);
    }

    /**
     * Met à jour un rôle existant.
     */
    public function update(Request $request, $slug)
    {
        $role = Roles::where('slug', $slug)->firstOrFail();

        $request->validate([
            'nom' => 'sometimes|required|string',
        ]);

        if ($request->has('nom')) {
            $role->nom = $request->nom;
        }

        $role->save();

        return response()->json(['message' => 'Mise à jour effectuée']);
    }

    /**
     * Supprime un rôle.
     */
    public function destroy($slug)
    {
        $role = Roles::where('slug', $slug)->firstOrFail();
        $role->delete();

        return response()->json(['message' => 'Rôle supprimé avec succès']);
    }
}
