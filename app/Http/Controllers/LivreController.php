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
}
