<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;


class RegisteredUserController extends Controller
{
     protected $creator;

    public function __construct(CreateNewUser $creator)
    {
        $this->creator = $creator;
    }
/**
 * @group Authentification
 *
 * Inscription d’un utilisateur
 *
 * Ce endpoint permet à un utilisateur de s’inscrire.
 *
 * @bodyParam name string required Le nom de l’utilisateur.
 * @bodyParam email string required Email valide. 
 * @bodyParam password string required Le mot de passe (minimum 8 caractères). 
 * @bodyParam password_confirmation string required Confirmation du mot de passe. 
 * @bodyParam role_id integer required ID du rôle attribué à l’utilisateur. Ce champ est une clé étrangère liée à la table `roles`. 
 *
 * @response 201 {
 *   "message": "Inscription réussie, vérifiez votre e-mail.",
 *   "user": {
 *     "id": 1,
 *     "name": "Jean Dupont",
 *     "email": "jean@example.com",
 *    "role_id": 1
 *   }
 * }
 */

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




