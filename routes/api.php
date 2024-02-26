<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['prefix' => "/auth", 'controller' => AuthController::class], function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/verify', 'verify');
});
Route::group(['prefix' => "/tags", 'controller' => TagController::class], function () {
    Route::get('/index', 'index');
    Route::post('/store', 'store');
    Route::post('/update/{id}', 'update');
    Route::post('/destroy/{id}', 'destroy');
});
Route::group(['prefix' => "/posts", 'controller' => PostController::class], function () {
    Route::get('/index', 'index');
    Route::post('/store', 'store');
    Route::get('/show/{id}', 'show');
    Route::post('/update/{id}', 'update');
    Route::post('/destroy/{id}', 'destroy');
    Route::post('/restore/{id}', 'restore');
    Route::get('/posts-deleted', 'postDeleted');
});


Route::get('/stats', [StatsController::class, 'index']);
