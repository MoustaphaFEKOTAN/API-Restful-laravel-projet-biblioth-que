<?php

use App\Http\Controllers\CategorieController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;
use App\Models\Roles;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LivreController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// --------------------------------------------------------------------------------------------------------------------------------

//  LES ROUTES POUR LES ACTIONS SUR LA TABLE categories
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategorieController::class);
});


//  LES ROUTES POUR LES ACTIONS SUR LA TABLE Role
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('roles', RolesController::class);
});


//    LES ROUTES POUR LES ACTIONS SUR LA TABLE LIVRES
Route::prefix('livres')->group(function () {
    // 📖 Tout le monde peut voir les livres
    Route::get('/', [LivreController::class, 'index']);
    Route::get('/{slug}', [LivreController::class, 'show']);

    // ✍️ Seuls les auteurs connectés peuvent créer, modifier ou supprimer
    Route::middleware(['auth:sanctum', 'auteur'])->group(function () {
        Route::post('/store', [LivreController::class, 'store']);
        Route::put('/{slug}', [LivreController::class, 'update']);
        Route::delete('/{slug}', [LivreController::class, 'destroy']);
    });
});


Route::get('/recherche/livre', [LivreController::class, 'recherche'])->name('api.livres.index');

// --------------------------------------------------------------------------------------------------------------------------------

//    LES ROUTES POUR LES ACTIONS SUR LA TABLE USERS

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

/**
 * @group Authentification
 *
 * Déconnexion
 *
 * Ce endpoint permet à un utilisateur authentifié de se déconnecter (invalider le token).
 *
 * @authenticated
 *
 * @response 200 {
 *   "message": "Déconnexion réussie"
 * }
 */

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    //  $request->user()->tokens()->delete(); Déconnecter de toutes les sessions

    return response()->json(['message' => 'Déconnecté avec succès.']);
});



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


// ✅ Vérifier l’e-mail via le lien
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Marque l’e-mail comme vérifié
    return response()->json(['message' => 'Email vérifié avec succès.']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');


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
Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email déjà vérifié.']);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Lien de vérification envoyé.']);
})->middleware(['auth:sanctum'])->name('verification.send');



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
Route::post('/forgot-password', function (Request $request) {
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
})->name('api.forgot-password');

/**
 * @group Authentification
 *
 * Réinitialiser le mot de passe
 *
 * Réinitialise le mot de passe via le token reçu par e-mail.
 *
 * @bodyParam email string required Email de l’utilisateur. Exemple: jean@example.com
 * @bodyParam token string required Le token de réinitialisation. Exemple: abc123
 * @bodyParam password string required Nouveau mot de passe. Exemple: newpass456
 * @bodyParam password_confirmation string required Confirmation. Exemple: newpass456
 *
 * @response 200 {
 *   "message": "Mot de passe réinitialisé avec succès"
 * }
 */


//Nouveau de mot de passe 
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60), //Mettre a jour le remember_me pour invalidé toute autre section active
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Mot de passe réinitialisé avec succès.'])
        : response()->json(['message' => __($status)], 400);
});

/** @authenticated
 * 
 */
//Modification de mot de passe lorsqu'on est déjà connecté
Route::middleware('auth:sanctum')->post('/change-password', function (Request $request) {
    $request->validate([
        'current_password' => ['required'],
        'new_password' => ['required', 'string', 'min:8', 'confirmed'], // nécessite aussi new_password_confirmation
    ]);

    $user = $request->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['message' => 'Mot de passe actuel incorrect.'], 403);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'Mot de passe mis à jour avec succès.']);
});


// --------------------------------------------------------------------------------------------------------------------------------

// liste des role a envoyé au formulaire d'inscription 
Route::get('/roles', function () {
    return Roles::select('id', 'nom')->get();
});


// Route::get('/test', function () {
//     return 'ceci est un test réussi';
// });
