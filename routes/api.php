<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SourcesController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::post('/users', [UserController::class, 'store']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::put('/users/{id}/password', [UserController::class, 'updatePassword']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::post('/sources', [SourcesController::class, 'store']);
Route::get('/sources', [SourcesController::class, 'index']);
Route::get('/sources/{id}', [SourcesController::class, 'show']);
Route::put('/sources/{id}', [SourcesController::class, 'update']);
Route::delete('/sources/{id}', [SourcesController::class, 'destroy']);

Route::post('/typeactions', [TypeActionsController::class, 'store']);
Route::get('/typeactions', [TypeActionsController::class, 'index']);
Route::get('/typeactions/{id}', [TypeActionsController::class, 'show']);
Route::put('/typeactions/{id}', [TypeActionsController::class, 'update']);
Route::delete('/typeactions/{id}', [TypeActionsController::class, 'destroy']);
