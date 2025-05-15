<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EjercicioController;
use App\Http\Controllers\Api\SesionController;
use App\Http\Controllers\Api\SerieController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Resources\UserResource;

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:api')->group(function () {

    Route::get('/user', function (Request $request) {
        
        return new UserResource($request->user());
    })->name('api.user.authenticated');

    Route::get('/users/{user}', [UserController::class, 'show'])->name('api.users.show');

    Route::middleware('admin')->group(function() {
         Route::post('/exercises', [EjercicioController::class, 'store'])->name('api.exercises.store');
         Route::put('/exercises/{ejercicio}', [EjercicioController::class, 'update'])->name('api.exercises.update');
         Route::delete('/exercises/{ejercicio}', [EjercicioController::class, 'destroy'])->name('api.exercises.destroy');
    });

    Route::get('/users/{user}/sessions', [SesionController::class, 'indexByUser'])->name('api.users.sessions.index');

    Route::apiResource('sessions', SesionController::class)->names([
        'index' => 'api.sessions.index',
        'store' => 'api.sessions.store',
        'show' => 'api.sessions.show',
        'update' => 'api.sessions.update',
        'destroy' => 'api.sessions.destroy',
    ]);

    Route::get('/sessions/{sesion}/series', [SerieController::class, 'indexBySesion'])->name('api.sessions.series.index');
    Route::post('/sessions/{sesion}/series', [SerieController::class, 'store'])->name('api.sessions.series.store');

    Route::get('/series/{serie}', [SerieController::class, 'show'])->name('api.series.show');
    Route::put('/series/{serie}', [SerieController::class, 'update'])->name('api.series.update');
    Route::delete('/series/{serie}', [SerieController::class, 'destroy'])->name('api.series.destroy');

    Route::get('/users/{user}/stats/volume', [StatsController::class, 'volume'])->name('api.users.stats.volume');
    Route::get('/users/{user}/stats/frequency', [StatsController::class, 'frequency'])->name('api.users.stats.frequency');
    Route::get('/users/{user}/personal-bests', [StatsController::class, 'personalBests'])->name('api.users.stats.personalBests');

});

// Rutas Públicas Ejercicios
Route::get('/exercises', [EjercicioController::class, 'index'])->name('api.exercises.index');
Route::get('/exercises/{ejercicio}', [EjercicioController::class, 'show'])->name('api.exercises.show');