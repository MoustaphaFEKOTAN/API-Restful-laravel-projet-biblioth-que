<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;


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


     
/**
 * @group Authentification
 *
 * Renvoyer le lien de vérification
 *
 * Ce endpoint renvoie un e-mail de vérification à l’utilisateur connecté.
 *
 * @authenticated
 *
 * @response 200 {
 *   "message": "Lien de vérification renvoyé"
 * }
 */

// ✅ Renvoyer le lien de vérification
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Lien de vérification envoyé.']);
    }


    

    /**
 * @group Authentification
 *
 * Vérification de l’e-mail
 *
 * Ce endpoint valide l’e-mail de l’utilisateur via un lien.
 *
 * @urlParam id integer required ID de l’utilisateur. Exemple: 1
 * @urlParam hash string required Hash de vérification. Exemple: abcd1234
 *
 * @response 200 {
 *   "message": "Email vérifié avec succès"
 * }
 */

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json(['message' => 'Email vérifié avec succès.']);
    }



    /**
 * @group Authentification
 *
 * Demande de réinitialisation du mot de passe
 *
 * Envoie un lien de réinitialisation du mot de passe à l’e-mail fourni.
 *
 * @bodyParam email string required Email de l’utilisateur. Exemple: jean@example.com
 *
 * @response 200 {
 *   "message": "Lien envoyé"
 * }
 */

//Mot de passe oublié(MAIL DE CHANGEMENT)
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Lien de réinitialisation envoyé.'])
            : response()->json(['message' => 'Impossible d\'envoyer le lien.'], 500);
    }




    /**
 * @group Authentification
 *
 * Réinitialiser le mot de passe
 *
 *
 * @bodyParam email string required Email de l’utilisateur. 
 * @bodyParam token string required Le token de réinitialisation. 
 * @bodyParam password string required Nouveau mot de passe.
 * @bodyParam password_confirmation string required Confirmation. 
 *
 * @response 200 {
 *   "message": "Mot de passe réinitialisé avec succès"
 * }
 */


//Nouveau de mot de passe 
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                app(ResetUserPassword::class)->reset($user, $request->all());
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Mot de passe réinitialisé avec succès.'])
            : response()->json(['message' => __($status)], 400);
    }


    /** @authenticated
 *  UPDATE MOT DE PASSE 
 */
//Modification de mot de passe lorsqu'on est déjà connecté
    public function changePassword(Request $request)
    {
        try {
            app(UpdateUserPassword::class)->update($request->user(), $request->all());

            return response()->json([
                'message' => 'Mot de passe mis à jour avec succès.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }


}