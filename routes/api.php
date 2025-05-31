<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;

Route::get('/teste', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API funcionando corretamente!',
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);

    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{patient}', [AppointmentController::class, 'show']);
    Route::put('/appointments/{patient}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{patient}', [AppointmentController::class, 'destroy']);
});
