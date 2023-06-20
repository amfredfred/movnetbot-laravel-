<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\RandomFileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatugramRequestsController;

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

Route::post('/random', [RandomFileController::class, 'random']);
Route::post('/search', [SearchController::class, 'search']);
Route::get('/watch', [StatugramRequestsController::class, 'watch']);
Route::post('/download', [StatugramRequestsController::class, 'download']);
Route::get('/webhook', FrontController::class);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
