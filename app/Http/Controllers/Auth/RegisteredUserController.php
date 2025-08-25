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
     * @OA\Post(
     *     path="/api/register",
     *     summary="Créer un nouvel utilisateur",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Moussa"),
     *             @OA\Property(property="email", type="string", format="email", example="moussa@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation échouée")
     * )
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




