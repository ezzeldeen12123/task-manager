<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);

      Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/show/{user}', [UserController::class, 'show']);
        Route::post('/update/{user}', [UserController::class, 'update']);
        Route::put('/change-password/{user}', [UserController::class, 'changePassword'])->name('user.change_password');
        Route::delete('/{id}', [UserController::class, 'delete']);
      });

      Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::get('/show', [TaskController::class, 'show']);
        Route::get('/statistics', [TaskController::class, 'statistics']);
        Route::post('/save', [TaskController::class, 'save']);
        Route::post('/action/{task}', [TaskController::class, 'action']);
        Route::delete('/{id}', [TaskController::class, 'delete']);
      });
    });
});
