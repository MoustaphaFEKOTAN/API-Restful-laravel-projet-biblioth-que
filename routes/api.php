<?php


use App\Http\Controllers\CategorieController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;
use App\Models\Roles;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LivreController;
use Illuminate\Http\Request;


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
Route::post('/login', [AuthController::class, 'store']);

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





// Email
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'signed'])
    ->name('api.verification-verify');

Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail'])
    ->middleware(['auth:sanctum'])
    ->name('api.verification-send');

// Mot de passe oublié / reset
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('api.forgot-password');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('api.reset-password');

Route::middleware('auth:sanctum')->post('/change-password', [AuthController::class, 'changePassword'])
    ->name('api.change-password');


// --------------------------------------------------------------------------------------------------------------------------------

// liste des role a envoyé au formulaire d'inscription 
Route::get('/roles', function () {
    return Roles::select('id', 'nom')->get();
});


// Route::get('/', function () {
//     return 'ceci est un test réussi';
// });