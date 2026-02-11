<?php

namespace App\Http\Controllers;

use App\Models\Livres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LivreController extends Controller
{
     public function index()
    {
        return Livres::with(['categorie', 'user'])->get();
    }

 /**
 * Ajouter un livre //Nom affiché dans l'onglet du doc
 * 
 * @authenticated
 */


    //les Méthodes aurait pu être dans un repository dédié pour une meilleure organisation, mais pour la simplicité du projet, elle est dans le controller.
    //les validations sont faites directement dans le controller, mais pour un projet plus complexe, j'aurais utiliser des Form Requests dédiés.
    public function store(Request $request)
{
    $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'required|string',
        'date_sortie' => 'required|date',
        'categorie_id' => 'required|exists:categories,id',
        'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $livre = new Livres();
    $livre->titre = $request->titre;
    $livre->description = $request->description;
    $livre->date_sortie = $request->date_sortie;
    $livre->categorie_id = $request->categorie_id;

    $livre->user_id = auth::id();
    $livre->slug = (string) Str::uuid();

       // Gestion de l’image
    if ($request->hasFile('cover')) {
        $path = $request->file('cover')->store('covers', 'public');
        $livre->cover = $path;
    }

    $livre->save();

    return response()->json(['message' => 'Livre ajouté avec succès', 
    'livre' => $livre,  
    'cover_url' => $livre->cover ? asset('storage/' . $livre->cover) : null], 201);
}


  public function show($slug)
{
    $livre = Livres::with(['categorie', 'user'])->where('slug', $slug)->firstOrFail();

    return response()->json([
        'message' => 'Livre récupéré avec succès',
        'livre' => $livre,
    ]);
}

 /**
 * 
 * 
 * @authenticated
 */
public function update(Request $request, $slug)
{
    $livre = Livres::where('slug', $slug)->firstOrFail();
    if ($livre->user_id !== Auth::id()) {

        return response()->json(['message' => 'Non autorisé'], 403);
    }

    $request->validate([
        'titre' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'date_sortie' => 'sometimes|date',
        'categorie_id' => 'sometimes|exists:categories,id',
        'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
    ]);

    $livre->update($request->only(['titre', 'description', 'date_sortie', 'categorie_id']));

      // Si une nouvelle image est envoyée
    if ($request->hasFile('cover')) {
        $old_image = $livre->cover;
         if ($old_image) {
        Storage::disk('public')->delete($livre->cover);
    }
        $path = $request->file('cover')->store('covers', 'public');
        $livre->cover = $path;
        $livre->save();
    }

    return response()->json(['message' => 'Livre mis à jour avec succès',
    'livre' => $livre,  'cover_url' => $livre->cover ? asset('storage/' . $livre->cover) : null]);
}


 /**
 * 
 * 
 * @authenticated
 */
    public function destroy($slug)
    {
         $livre = Livres::where('slug', $slug)->firstOrFail();
       if ($livre->user_id !== Auth::id()) {

    return response()->json(['message' => 'Non autorisé'], 403);
}

$livre->delete();

    }

    public function recherche(Request $request)
{
    $query = Livres::query();

    // Filtrage par catégorie (optionnel)
    if ($request->has('categorie')) {
        $query->where('categorie_id', $request->categorie);
    }

    //Filtrage par mot-clé dans le titre ou description (optionnel)
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('titre', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
        });
    }

    //Tri (optionnel)
    if ($request->has('sort_by') && $request->has('order')) {
        $query->orderBy($request->sort_by, $request->order);
    } else {
        $query->orderBy('created_at', 'desc'); // par défaut
    }

    //Pagination (10 livres par page par défaut)
    $livres = $query->paginate($request->get('per_page', 10));
    
  if ($livres->isEmpty()) {
        return response()->json([
            'message' => 'Aucun résultat',
            'data' => [],
            'total' => 0,
        ], 200);
    }

    return response()->json($livres);
 

}

}
