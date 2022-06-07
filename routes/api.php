<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group( function () {
    Route::get('user', [AuthController::class, 'user']);

    // post
    Route::prefix('post')->group(function () {
        Route::get('get', [PostController::class, 'get']);
        Route::get('show/{id}', [PostController::class, 'show']);
        Route::post('store', [PostController::class, 'store']);
        Route::post('update/{id}', [PostController::class, 'update']);
        Route::delete('destory/{id}', [PostController::class, 'destroy']);
    });

    // comment
    Route::prefix('comment')->group(function () {
        Route::post('store', [CommentController::class, 'doComment']);
    });

    // like
    Route::prefix('like')->group(function () {
        Route::post('store/{id}', [LikeController::class, 'store']);
    });
});
