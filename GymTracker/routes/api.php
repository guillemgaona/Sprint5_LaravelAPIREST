<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importar Controladores
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EjercicioController;
use App\Http\Controllers\Api\SesionController;
use App\Http\Controllers\Api\SerieController;
use App\Http\Controllers\Api\StatsController;

// Importar API Resources (si se usan directamente en la ruta, como para /user)
use App\Http\Resources\UserResource;

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

// --- Autenticación & Rutas Públicas de Usuarios ---
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// --- Rutas Protegidas por Autenticación (Passport) ---
Route::middleware('auth:api')->group(function () {

    // Usuario Autenticado
    Route::get('/user', function (Request $request) {
        // Devuelve la información del usuario autenticado usando UserResource
        return new UserResource($request->user());
    })->name('api.user.authenticated');

    // Información de un usuario específico
    // El parámetro {user} se vinculará al modelo User usando la columna 'id' por defecto.
    Route::get('/users/{user}', [UserController::class, 'show'])->name('api.users.show');

    // --- Ejercicios (CRUD protegido para admin) ---
    Route::middleware('admin')->group(function() {
         Route::post('/exercises', [EjercicioController::class, 'store'])->name('api.exercises.store');
         // El parámetro {ejercicio} se vinculará al modelo Ejercicio usando 'id_ejercicio'
         // (gracias a getRouteKeyName() en el modelo Ejercicio).
         Route::put('/exercises/{ejercicio}', [EjercicioController::class, 'update'])->name('api.exercises.update');
         Route::delete('/exercises/{ejercicio}', [EjercicioController::class, 'destroy'])->name('api.exercises.destroy');
    });

    // --- Sesiones ---
    // Listar sesiones de un usuario específico
    // El parámetro {user} se vinculará al modelo User usando 'id'.
    Route::get('/users/{user}/sessions', [SesionController::class, 'indexByUser'])->name('api.users.sessions.index');

    // CRUD para Sesiones
    // El método index (GET /api/sessions) listará todas las sesiones (posiblemente para un admin).
    // El método store (POST /api/sessions) creará una nueva sesión para el usuario autenticado.
    // Los métodos show, update, destroy usarán {sesion}, que se vinculará al modelo Sesion
    // usando 'id_sesion' (gracias a getRouteKeyName() en el modelo Sesion).
    Route::apiResource('sessions', SesionController::class)->names([
        'index' => 'api.sessions.index',
        'store' => 'api.sessions.store',
        'show' => 'api.sessions.show',
        'update' => 'api.sessions.update',
        'destroy' => 'api.sessions.destroy',
    ]);


    // --- Series (Anidadas bajo sesiones) ---
    // Listar series de una sesión específica
    // El parámetro {sesion} se vinculará al modelo Sesion usando 'id_sesion'.
    Route::get('/sessions/{sesion}/series', [SerieController::class, 'indexBySesion'])->name('api.sessions.series.index');
    // Añadir una nueva serie a una sesión específica
    Route::post('/sessions/{sesion}/series', [SerieController::class, 'store'])->name('api.sessions.series.store');

    // Detalle, edición y borrado de una serie específica
    // El parámetro {serie} se vinculará al modelo Serie usando 'id_serie'
    // (gracias a getRouteKeyName() en el modelo Serie).
    Route::get('/series/{serie}', [SerieController::class, 'show'])->name('api.series.show');
    Route::put('/series/{serie}', [SerieController::class, 'update'])->name('api.series.update');
    Route::delete('/series/{serie}', [SerieController::class, 'destroy'])->name('api.series.destroy');

    // --- Estadísticas ---
    // El parámetro {user} se vinculará al modelo User usando 'id'.
    Route::get('/users/{user}/stats/volume', [StatsController::class, 'volume'])->name('api.users.stats.volume');
    Route::get('/users/{user}/stats/frequency', [StatsController::class, 'frequency'])->name('api.users.stats.frequency');
    Route::get('/users/{user}/personal-bests', [StatsController::class, 'personalBests'])->name('api.users.stats.personalBests');

});

// --- Rutas Públicas de Ejercicios ---
// Listar todos los ejercicios (paginado)
Route::get('/exercises', [EjercicioController::class, 'index'])->name('api.exercises.index');
// Mostrar detalle de un ejercicio específico
// El parámetro {ejercicio} se vinculará al modelo Ejercicio usando 'id_ejercicio'.
Route::get('/exercises/{ejercicio}', [EjercicioController::class, 'show'])->name('api.exercises.show');