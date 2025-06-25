<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlertController; // Importa tu controlador
use App\Http\Controllers\AuthController; // <-- Añade este

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Rutas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Protege también tus rutas de alertas si solo usuarios autenticados pueden ver/enviar
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::post('/alerts', [AlertController::class, 'store']); // Si tienes un método store
    // ... otras rutas de AlertController que quieras proteger
});

// Ruta para enviar alertas
// Route::post('/alerts', [AlertController::class, 'store']); // <-- Añade esta línea

// Opcional: Rutas para ver alertas
// Route::get('/alerts', [AlertController::class, 'index']);
// Route::get('/alerts/{alert}', [AlertController::class, 'show']);
