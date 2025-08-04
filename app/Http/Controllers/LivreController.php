<?php

namespace App\Http\Controllers;

use App\Models\Livres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LivreController extends Controller
{
     public function index()
    {
        return Livres::with(['categorie', 'user'])->get();
    }

 public function store(Request $request)
{
    $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'required|string',
        'date_sortie' => 'required|date',
        'categorie_id' => 'required|exists:categories,id',
    ]);

    $livre = new Livres();
    $livre->titre = $request->titre;
    $livre->description = $request->description;
    $livre->date_sortie = $request->date_sortie;
    $livre->categorie_id = $request->categorie_id;

    // ✅ Imposé automatiquement
    $livre->user_id = auth::id();
    $livre->slug = (string) Str::uuid();

    $livre->save();

    return response()->json(['message' => 'Livre ajouté avec succès', 'livre' => $livre], 201);
}


  public function show($slug)
{
    $livre = Livres::with(['categorie', 'user'])->where('slug', $slug)->firstOrFail();

    return response()->json([
        'message' => 'Livre récupéré avec succès',
        'livre' => $livre,
    ]);
}


public function update(Request $request, $slug)
{
    $livre = Livres::where('slug', $slug)->firstOrFail();

    $request->validate([
        'titre' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'date_sortie' => 'sometimes|date',
        'categorie_id' => 'sometimes|exists:categories,id',
    ]);

    $livre->update($request->only(['titre', 'description', 'date_sortie', 'categorie_id']));

    return response()->json(['message' => 'Livre mis à jour avec succès', 'livre' => $livre]);
}



    public function destroy($slug)
    {
        $livre = Livres::findOrFail($slug);
       if ($livre->user_id !== Auth::id()) {
    return response()->json(['message' => 'Non autorisé'], 403);
}

    }

    public function recherche(Request $request)
{
    $query = Livres::query();

    // 🔍 Filtrage par catégorie (optionnel)
    if ($request->has('categorie')) {
        $query->where('categorie_id', $request->categorie);
    }

    // 🔍 Filtrage par mot-clé dans le titre ou description (optionnel)
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('titre', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
        });
    }

    // 📅 Tri (optionnel)
    if ($request->has('sort_by') && $request->has('order')) {
        $query->orderBy($request->sort_by, $request->order);
    } else {
        $query->orderBy('created_at', 'desc'); // par défaut
    }

    // 📄 Pagination (10 livres par page par défaut)
    $livres = $query->paginate($request->get('per_page', 10));
if($livres){ 
    
    return response()->json($livres);

} 

    return response()->json(["message" => "Aucun résultat"]);
 

}

}
