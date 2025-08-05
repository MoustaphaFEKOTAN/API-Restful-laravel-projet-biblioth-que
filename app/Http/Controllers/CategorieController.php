<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $categories = Categories::all();
    return response()->json($categories);
}


 /**
 * 
 * 
 * @authenticated
 */
   
   public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
    ]);

    $categorie = new Categories();
    $categorie->nom = $request->nom;
    $categorie->slug = (string) Str::uuid(); // 💡 sécuriser bien avec cast string

    $categorie->save();

    return response()->json([
        'message' => 'Catégorie ajoutée avec succès',
        'categorie' => $categorie
    ], 201);
}


   
     /**
 * 
 * 
 * @authenticated
 */
  public function show($slug)
{
    $categorie = Categories::where('slug', $slug)->firstOrFail();
    return response()->json($categorie);
}


    /**
     * Update the specified resource in storage.
     *  @authenticated
     */
  public function update(Request $request, $slug)
{
    $request->validate([
        'nom' => 'sometimes|string|max:255',
    ]);

    $categorie = Categories::where('slug', $slug)->firstOrFail();
    if ($request->has('nom')) {
        $categorie->nom = $request->nom;
    }

    $categorie->save();

    return response()->json([
        'message' => 'Catégorie mise à jour avec succès',
        'categorie' => $categorie
    ]);
}


    /**
     * Remove the specified resource from storage.
     *  @authenticated
     */
   public function destroy($slug)
{
    $categorie = Categories::where('slug', $slug)->firstOrFail();
    $categorie->delete();

    return response()->json([
        'message' => 'Catégorie supprimée avec succès'
    ]);
}

}
