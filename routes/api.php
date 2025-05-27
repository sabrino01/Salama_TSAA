<?php

use App\Http\Controllers\ActionsController;
use App\Http\Controllers\ActionsResponsableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConstatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailAlertController;
use App\Http\Controllers\EmailConfigController;
use App\Http\Controllers\EmailMemberController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\SourcesController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\TypeActionsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//login
Route::post('/login', [AuthController::class, 'login']);
//ajout membre et voir et modifier profile
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/all', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::put('/users/{id}/password', [UserController::class, 'updatePassword']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
//sources crud
Route::post('/sources', [SourcesController::class, 'store']);
Route::get('/sources', [SourcesController::class, 'index']);
Route::get('/sources/{id}', [SourcesController::class, 'show']);
Route::put('/sources/{id}', [SourcesController::class, 'update']);
Route::delete('/sources/{id}', [SourcesController::class, 'destroy']);
//type d'actions crud
Route::post('/typeactions', [TypeActionsController::class, 'store']);
Route::get('/typeactions', [TypeActionsController::class, 'index']);
Route::get('/typeactions/{id}', [TypeActionsController::class, 'show']);
Route::put('/typeactions/{id}', [TypeActionsController::class, 'update']);
Route::delete('/typeactions/{id}', [TypeActionsController::class, 'destroy']);
//responsable crud
Route::post('/responsable', [ResponsableController::class, 'store']);
Route::get('/responsable', [ResponsableController::class, 'index']);
Route::get('/responsable/{id}', [ResponsableController::class, 'show']);
Route::put('/responsable/{id}', [ResponsableController::class, 'update']);
Route::delete('/responsable/{id}', [ResponsableController::class, 'destroy']);
//suivi crud
Route::post('/suivi', [SuiviController::class, 'store']);
Route::get('/suivi', [SuiviController::class, 'index']);
Route::get('/suivi/{id}', [SuiviController::class, 'show']);
Route::put('/suivi/{id}', [SuiviController::class, 'update']);
Route::delete('/suivi/{id}', [SuiviController::class, 'destroy']);
//constat crud
Route::post('/constat', [ConstatController::class, 'store']);
Route::get('/constat', [ConstatController::class, 'index']);
Route::get('/constat/{id}', [ConstatController::class, 'show']);
Route::put('/constat/{id}', [ConstatController::class, 'update']);
Route::delete('/constat/{id}', [ConstatController::class, 'destroy']);
//appel les informations
Route::get('/sourcesAI', [ActionsController::class, 'sourcesAI']);
Route::get('/sourcesPTA', [ActionsController::class, 'sourcesPTA']);
Route::get('/typeactionsPTA', [ActionsController::class, 'typeActionsPTA']);
Route::get('/typeactionsAI', [ActionsController::class, 'typeActionsAI']);
Route::get('/responsables', [ActionsController::class, 'responsables']);
Route::get('/suivis', [ActionsController::class, 'suivis']);
Route::get('/constats', [ActionsController::class, 'constats']);
Route::get('/users', [ActionsController::class, 'users']);
//actions audit interne
Route::get('/actions/createAI', [ActionsController::class, 'createAI']);
Route::get('/actions/auditinterne', [ActionsController::class, 'indexAI']);
//actions PTA
Route::get('/actions/createPTA', [ActionsController::class, 'createPTA']);
Route::get('/actions/pta', [ActionsController::class, 'indexPTA']);
//actions audit externe & pta
Route::post('/actions', [ActionsController::class, 'store']);
Route::get('/actions/{id}', [ActionsController::class, 'show']);
Route::put('/actions/{id}', [ActionsController::class, 'update']);
Route::delete('/actions/{id}', [ActionsController::class, 'destroy']);
Route::post('/actions/import', [ActionsController::class, 'import']);
Route::post('/actions/check-num-actions', [ActionsController::class, 'checkExistingNumActions']);
//notifications
Route::get('/notifications', [ActionsController::class, 'notifications']);
Route::get('/notifications/all', [ActionsController::class, 'getAllNotifications']);
Route::prefix('email-config')->group(function () {
    Route::get('/{userId}', [EmailConfigController::class, 'getConfig']);
    Route::post('/{userId}', [EmailConfigController::class, 'saveConfig']);
    Route::post('/test', [EmailConfigController::class, 'testConfig']);
});

Route::prefix('email-notifications')->group(function () {
    Route::post('/{userId}/toggle', [EmailConfigController::class, 'toggleNotifications']);
});

Route::prefix('email-members')->group(function () {
    Route::get('/{userId}', [EmailMemberController::class, 'getMembers']);
    Route::post('/{userId}/add', [EmailMemberController::class, 'addMember']);
    Route::post('/{userId}/remove', [EmailMemberController::class, 'removeMember']);
});

// Routes pour dashboard
Route::get('/constats/statistiques/AI', [DashboardController::class, 'indexAI']);
Route::get('/users', [DashboardController::class, 'getUsers']);
Route::get('/constats/statistiques/PTA', [DashboardController::class, 'indexPTA']);
Route::prefix('api')->group(function () {
    // Récupérer les statistiques pour le tableau de bord par préfixe
    Route::get('/actions/stats', [DashboardController::class, 'getStats']);

    // Récupérer les actions AI filtrées par statut
    Route::get('/actions/ai', [DashboardController::class, 'getAIActionsByStatus']);

    // Récupérer les actions PTA filtrées par statut
    Route::get('/actions/pta', [DashboardController::class, 'getPTAActionsByStatus']);
});

// Route pour afficher toutes les actions responsables AI (fonction indexResponsablesAI)
Route::get('/actions-responsables-ai', [ActionsResponsableController::class, 'indexResponsablesAI']);

// Route pour récupérer une action responsable spécifique
Route::get('/actions-responsables/{id}', [ActionsResponsableController::class, 'showActionResponsable']);

// Route pour mettre à jour une action responsable
Route::put('/actions-responsables/{id}', [ActionsResponsableController::class, 'updateActionResponsable']);

Route::prefix('email')->group(function () {
    Route::post('/toggle-notifications', [EmailConfigController::class, 'toggleNotifications']);
    Route::post('/save-config', [EmailConfigController::class, 'saveConfig']);
    Route::get('/get-config', [EmailConfigController::class, 'getConfig']);
    Route::post('/send-alert', [EmailConfigController::class, 'sendAlert']);
});
