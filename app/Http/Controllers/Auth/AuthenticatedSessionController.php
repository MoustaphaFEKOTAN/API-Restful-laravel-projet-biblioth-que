<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
 * @group Authentification
 *
 * Connexion d’un utilisateur
 *
 * Ce endpoint permet à un utilisateur de se connecter.
 *
 * @bodyParam email string required L’adresse email. 
 * @bodyParam password string required Le mot de passe. 
 *
 * @response 200 {
 *   "access_token": "token-sanctum-ici",
 *   "user": {
 *     "id": 1,
 *     "name": "Jean Dupont"
 *   }
 * }
 *
 * @response 401 {
 *   "message": "Identifiants invalides"
 * }
 */

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $email = $request->email;
        $ip = $request->ip();
        $key = 'login:' . $email . '|' . $ip;

        //Si nombre de tentative errones atteint 5
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.'
            ], 429);
        }

        $user = User::where('email', $email)->first();

        //Tant que le nombre de tentative n'atteint pas 5 au bout de 5s 
        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); //+1 a chaque fois qu'une tentative echoue dans un délai de 60s
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }


        RateLimiter::clear($key);

        $token = $user->createToken(
            'auth_token',
            [],
            now()->addDays($request->remember_me ? 30 : 1) //si se souvenir de moi est coché ,resté connecté pendant 30J , sinon 1j
        )->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}
