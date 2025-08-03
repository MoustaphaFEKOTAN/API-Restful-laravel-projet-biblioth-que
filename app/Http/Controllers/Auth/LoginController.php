<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $email = $request->email;
        $ip = $request->ip();
        $key = 'login:' . $email . '|' . $ip;

        // Si trop de tentatives
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.'
            ], 429);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); // bloqué pendant 60 secondes
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Adresse e-mail non vérifiée. Un lien vient de vous être renvoyé.'
            ], 403);
        }

        // Tout est bon, on supprime les tentatives
        RateLimiter::clear($key);

        $token = $user->createToken(
            'auth_token',
            [],
            now()->addDays($request->remember_me ? 30 : 1) // expire après 30 jours si remember_me activé
        )->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}
