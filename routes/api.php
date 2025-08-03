<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivresController;
use App\Models\Roles;

// --------------------------------------------------------------------------------------------------------------------------------

//  LES ROUTES POUR LES ACTIONS SUR LA TABLE categories
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategorieController::class);
});


// --------------------------------------------------------------------------------------------------------------------------------

//    LES ROUTES POUR LES ACTIONS SUR LA TABLE LIVRES
Route::prefix('livres')->group(function () {
    // 📖 Tout le monde peut voir les livres
    Route::get('/', [LivresController::class, 'index']);
    Route::get('/{slug}', [LivresController::class, 'show']);

    // ✍️ Seuls les auteurs connectés peuvent créer, modifier ou supprimer
    Route::middleware(['auth:sanctum', 'auteur'])->group(function () {
        Route::post('/', [LivresController::class, 'store']);
        Route::put('/{slug}', [LivresController::class, 'update']);
        Route::delete('/{slug}', [LivresController::class, 'destroy']);
    });
});


// --------------------------------------------------------------------------------------------------------------------------------

//    LES ROUTES POUR LES ACTIONS SUR LA TABLE USERS

Route::post('/register', [AuthController::class, 'register']);


// liste des role
Route::get('/roles', function () {
    return Roles::select('slug', 'nom')->get();
});


// Route::get('/test', function () {
//     return 'rrr';
// });
