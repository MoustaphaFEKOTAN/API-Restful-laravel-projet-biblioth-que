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
 * 
 * Créer un nouveau role
 * @authenticated
 */

    //les Méthodes aurait pu être dans un repository dédié pour une meilleure organisation, mais pour la simplicité du projet, elle est dans le controller.
    //les validations sont faites directement dans le controller, mais pour un projet plus complexe, j'aurais utiliser des Form Requests dédiés.
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
 * 
 * 
 * @authenticated
 */
    public function show($slug)
    {
        $role = Roles::where('slug', $slug)->firstOrFail();
        return response()->json($role);
    }

  /**
 * 
 * 
 * @authenticated
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
 * 
 * 
 * @authenticated
 */
    public function destroy($slug)
    {
        $role = Roles::where('slug', $slug)->firstOrFail();
        $role->delete();

        return response()->json(['message' => 'Rôle supprimé avec succès']);
    }
}
