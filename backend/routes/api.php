<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\GamesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);
Route::get('/fixtures', [GamesController::class, 'fixtures']);
Route::get('/results',  [GamesController::class, 'results']);
Route::get('/next-match', [GamesController::class, 'nextMatch']);

Route::get('/games', [GamesController::class, 'index']);
Route::get('/games/{id}', [GamesController::class, 'show']);