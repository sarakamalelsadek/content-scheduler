<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\Api\PostController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']); 


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::group(['prefix' => 'post'], function () {
        Route::get('/', [PostController::class, 'index']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/analytics', [PostController::class, 'analytics']);
        Route::post('/{post}/update', [PostController::class, 'update']);
        Route::delete('/{post}', [PostController::class, 'destroy']);
    });

    Route::group(['prefix' => 'platform'], function () {
        Route::get('/', [PlatformController::class, 'userPlatForms']);
        Route::post('/{platform}/toggle', [PlatformController::class, 'toggleUserPlatform']);

    });
   
});
