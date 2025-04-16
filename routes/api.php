<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConstatController;
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
Route::get('/users', [UserController::class, 'index']);
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
