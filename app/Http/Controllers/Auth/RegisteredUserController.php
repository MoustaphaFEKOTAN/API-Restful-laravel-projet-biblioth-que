<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
     protected $creator;

    public function __construct(CreateNewUser $creator)
    {
        $this->creator = $creator;
    }

    public function store(Request $request)
    {
        // Appelle Fortify (CreateNewUser gère tout)
        $user = $this->creator->create($request->all());

        //  Connexion automatique
        // Auth::login($user);

        //  Générer un token API (ex: si tu fais une app mobile ou SPA)
        // $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur inscrit avec succès',
            'user' => $user,
            //   'token' => $token,
        ], 201);
    }
}




