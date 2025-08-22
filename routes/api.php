<?php

use App\Http\Controllers\ActionsController;
use App\Http\Controllers\ActionsResponsableController;
use App\Http\Controllers\ActionsSuiviController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConstatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailAlertController;
use App\Http\Controllers\EmailConfigController;
use App\Http\Controllers\EmailMemberController;
use App\Http\Controllers\HistoriquesController;
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
Route::get('/constat/types-audit', [ConstatController::class, 'getTypesAudit']);
Route::get('/constat', [ConstatController::class, 'index']);
Route::get('/constat/{id}', [ConstatController::class, 'show']);
Route::put('/constat/{id}', [ConstatController::class, 'update']);
Route::delete('/constat/{id}', [ConstatController::class, 'destroy']);
//appel les informations
Route::get('/sourcesAI', [ActionsController::class, 'sourcesAI']);
Route::get('/sourcesPTA', [ActionsController::class, 'sourcesPTA']);
Route::get('/sourcesAE', [ActionsController::class, 'sourcesAE']);
Route::get('/sourcesCac', [ActionsController::class, 'sourcesCac']);
Route::get('/sourcesEnquete', [ActionsController::class, 'sourcesEnquete']);
Route::get('/sourcesSWOT', [ActionsController::class, 'sourcesSWOT']);
Route::get('/sourcesAII', [ActionsController::class, 'sourcesAII']);
Route::get('/typeactionsPTA', [ActionsController::class, 'typeActionsPTA']);
Route::get('/typeactionsAI', [ActionsController::class, 'typeActionsAI']);
Route::get('/typeactionsAE', [ActionsController::class, 'typeActionsAE']);
Route::get('/typeactionsCac', [ActionsController::class, 'typeActionsCac']);
Route::get('/typeactionsEnquete', [ActionsController::class, 'typeActionsEnquete']);
Route::get('/typeactionsSWOT', [ActionsController::class, 'typeActionsSWOT']);
Route::get('/typeactionsAII', [ActionsController::class, 'typeActionsAII']);
Route::get('/responsables', [ActionsController::class, 'responsables']);
Route::get('/suivis', [ActionsController::class, 'suivis']);
Route::get('/constatsAI', [ActionsController::class, 'constatsAI']);
Route::get('/constatsPTA', [ActionsController::class, 'constatsPTA']);
Route::get('/constatsAE', [ActionsController::class, 'constatsAE']);
Route::get('/constatsCac', [ActionsController::class, 'constatsCac']);
Route::get('/constatsEnquete', [ActionsController::class, 'constatsEnquete']);
Route::get('/constatsSWOT', [ActionsController::class, 'constatsSWOT']);
Route::get('/constatsAII', [ActionsController::class, 'constatsAII']);
Route::get('/users', [ActionsController::class, 'users']);
//actions audit interne
Route::get('/actions/createAI', [ActionsController::class, 'createAI']);
Route::get('/actions/auditinterne', [ActionsController::class, 'indexAI']);
//actions PTA
Route::get('/actions/createPTA', [ActionsController::class, 'createPTA']);
Route::get('/actions/pta', [ActionsController::class, 'indexPTA']);
//actions enquete
Route::get('/actions/createEnquete', [ActionsController::class, 'createEnquete']);
Route::get('/actions/enquete', [ActionsController::class, 'indexEnquete']);
//actions cac
Route::get('/actions/createCac', [ActionsController::class, 'createCac']);
Route::get('/actions/cac', [ActionsController::class, 'indexCac']);
//actions aii
Route::get('/actions/createAII', [ActionsController::class, 'createAII']);
Route::get('/actions/aii', [ActionsController::class, 'indexAII']);
//actions auditexterne
Route::get('/actions/createAE', [ActionsController::class, 'createAE']);
Route::get('/actions/ae', [ActionsController::class, 'indexAE']);
//actions swot
Route::get('/actions/createSWOT', [ActionsController::class, 'createSWOT']);
Route::get('/actions/swot', [ActionsController::class, 'indexSWOT']);
//actions audit interne & pta
Route::post('/actions', [ActionsController::class, 'store']);
Route::get('/actions/{id}', [ActionsController::class, 'show']);
Route::put('/actions/{id}', [ActionsController::class, 'update']);
// Nouvelles routes pour la gestion des statuts
Route::get('/actions/{id}/check-status', [ActionsController::class, 'checkStatus']);
Route::post('/actions/check-all-statuses', [ActionsController::class, 'checkAllStatuses']);
Route::get('/actions/active', [ActionsController::class, 'getActiveActions']);

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
Route::get('/constats/statistiques/PTA', [DashboardController::class, 'indexPTA']);
Route::get('/constats/statistiques/AE', [DashboardController::class, 'indexAE']);
Route::get('/constats/statistiques/CAC', [DashboardController::class, 'indexCAC']);
Route::get('/constats/statistiques/ES', [DashboardController::class, 'indexES']);
Route::get('/constats/statistiques/SWOT', [DashboardController::class, 'indexSWOT']);
Route::get('/users', [DashboardController::class, 'getUsers']);
Route::prefix('api')->group(function () {
    // Récupérer les statistiques pour le tableau de bord par préfixe
    Route::get('/actions/stats', [DashboardController::class, 'getStats']);

    // Récupérer les actions AI filtrées par statut
    Route::get('/actions/ai', [DashboardController::class, 'getAIActionsByStatus']);

    // Récupérer les actions PTA filtrées par statut
    Route::get('/actions/pta', [DashboardController::class, 'getPTAActionsByStatus']);

    // Récupérer les actions ES filtrées par statut
    Route::get('/actions/enquete', [DashboardController::class, 'getESActionsByStatus']);

    // Récupérer les actions AE filtrées par statut
    Route::get('/actions/ae', [DashboardController::class, 'getAEActionsByStatus']);

    // Récupérer les actions CAC filtrées par statut
    Route::get('/actions/cac', [DashboardController::class, 'getCACActionsByStatus']);

    // Récupérer les actions SWOT filtrées par statut
    Route::get('/actions/swot', [DashboardController::class, 'getSWOTActionsByStatus']);
});

// Route pour afficher toutes les actions responsables AI/PTA/AE/SWOT/CAC/ENQUETE/AII
Route::get('/actions-responsables-ai', [ActionsResponsableController::class, 'indexResponsablesAI']);
Route::get('/actions-responsables-pta', [ActionsResponsableController::class, 'indexResponsablesPTA']);
Route::get('/actions-responsables-ae', [ActionsResponsableController::class, 'indexResponsablesAE']);
Route::get('/actions-responsables-cac', [ActionsResponsableController::class, 'indexResponsablesCac']);
Route::get('/actions-responsables-swot', [ActionsResponsableController::class, 'indexResponsablesSWOT']);
Route::get('/actions-responsables-enquete', [ActionsResponsableController::class, 'indexResponsablesEnquete']);
Route::get('/actions-responsables-aii', [ActionsResponsableController::class, 'indexResponsablesAII']);

// Route pour récupérer une action responsable spécifique
Route::get('/actions-responsables/{id}', [ActionsResponsableController::class, 'showActionResponsable']);
Route::get('/actions-responsables/pta/{id}', [ActionsResponsableController::class, 'showActionResponsablePTA']);
Route::get('/actions-responsables/ae/{id}', [ActionsResponsableController::class, 'showActionResponsableAE']);
Route::get('/actions-responsables/cac/{id}', [ActionsResponsableController::class, 'showActionResponsableCac']);
Route::get('/actions-responsables/swot/{id}', [ActionsResponsableController::class, 'showActionResponsableSWOT']);
Route::get('/actions-responsables/enquete/{id}', [ActionsResponsableController::class, 'showActionResponsableEnquete']);
Route::get('/actions-responsables/aii/{id}', [ActionsResponsableController::class, 'showActionResponsableAII']);

// Route pour mettre à jour une action responsable
Route::put('/actions-responsables/{id}', [ActionsResponsableController::class, 'updateActionResponsable']);
Route::put('/actions-responsables/pta/{id}', [ActionsResponsableController::class, 'updateActionResponsablePTA']);
Route::put('/actions-responsables/ae/{id}', [ActionsResponsableController::class, 'updateActionResponsableAE']);
Route::put('/actions-responsables/cac/{id}', [ActionsResponsableController::class, 'updateActionResponsableCac']);
Route::put('/actions-responsables/swot/{id}', [ActionsResponsableController::class, 'updateActionResponsableSWOT']);
Route::put('/actions-responsables/enquete/{id}', [ActionsResponsableController::class, 'updateActionResponsableEnquete']);
Route::put('/actions-responsables/aii/{id}', [ActionsResponsableController::class, 'updateActionResponsableAII']);

//Route pour afficher toutes les suivis AI/PTA/AE/CAC/SWOT/ENQUETE/AII
Route::get('/suivis-ai', [ActionsSuiviController::class, 'indexSuiviAI']);
Route::get('/suivis-pta', [ActionsSuiviController::class, 'indexSuiviPTA']);
Route::get('/suivis-ae', [ActionsSuiviController::class, 'indexSuiviAE']);
Route::get('/suivis-cac', [ActionsSuiviController::class, 'indexSuiviCac']);
Route::get('/suivis-swot', [ActionsSuiviController::class, 'indexSuiviSWOT']);
Route::get('/suivis-enquete', [ActionsSuiviController::class, 'indexSuiviEnquete']);
Route::get('/suivis-aii', [ActionsSuiviController::class, 'indexSuiviAII']);

// Route pour récupérer un suivi spécifique
Route::get('/suivis/{id}', [ActionsSuiviController::class, 'showSuivi']);
Route::get('/suivis/pta/{id}', [ActionsSuiviController::class, 'showSuiviPTA']);
Route::get('/suivis/ae/{id}', [ActionsSuiviController::class, 'showSuiviAe']);
Route::get('/suivis/cac/{id}', [ActionsSuiviController::class, 'showSuiviCac']);
Route::get('/suivis/swot/{id}', [ActionsSuiviController::class, 'showSuiviSWOT']);
Route::get('/suivis/enquete/{id}', [ActionsSuiviController::class, 'showSuiviEnquete']);
Route::get('/suivis/aii/{id}', [ActionsSuiviController::class, 'showSuiviAII']);

// Route pour mettre à jour un suivi
Route::put('/suivis/{id}', [ActionsSuiviController::class, 'updateSuivi']);
Route::put('/suivis/pta/{id}', [ActionsSuiviController::class, 'updateSuiviPTA']);
Route::put('/suivis/ae/{id}', [ActionsSuiviController::class, 'updateSuiviAE']);
Route::put('/suivis/cac/{id}', [ActionsSuiviController::class, 'updateSuiviCac']);
Route::put('/suivis/swot/{id}', [ActionsSuiviController::class, 'updateSuiviSWOT']);
Route::put('/suivis/enquete/{id}', [ActionsSuiviController::class, 'updateSuiviEnquete']);
Route::put('/suivis/aii/{id}', [ActionsSuiviController::class, 'updateSuiviAII']);

// Routes pour les alertes par email
Route::prefix('email')->group(function () {
    Route::post('/toggle-notifications', [EmailConfigController::class, 'toggleNotifications']);
    Route::post('/save-config', [EmailConfigController::class, 'saveConfig']);
    Route::get('/get-config', [EmailConfigController::class, 'getConfig']);
    Route::post('/send-alert', [EmailConfigController::class, 'sendAlert']);
});

// Route::post('/actions/create-responsables-suivis-missing', [ActionsController::class, 'createResponsablesSuivisMissing']);

Route::get('/historiques/auditinterne', [HistoriquesController::class, 'indexAI']);
Route::get('/historiques/ae', [HistoriquesController::class, 'indexAE']);
Route::get('/historiques/enquete', [HistoriquesController::class, 'indexEnquete']);
Route::get('/historiques/swot', [HistoriquesController::class, 'indexSWOT']);
Route::get('/historiques/cac', [HistoriquesController::class, 'indexCAC']);
Route::get('/historiques/pta', [HistoriquesController::class, 'indexPTA']);
Route::get('/historiques/details/{id}', [HistoriquesController::class, 'show']);
