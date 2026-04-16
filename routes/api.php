<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    // public
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/patient-register', [UserController::class, 'patientRegister']);

    // optional backward compatibility
    Route::post('/student-register', [UserController::class, 'studentRegister']);

    Route::get('/check', [UserController::class, 'authenticateToken']);

    // protected
    Route::middleware('checkAuth')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/me-role', [UserController::class, 'getMyRole']);
        Route::get('/profile', [UserController::class, 'getProfile']);
    });
});

Route::middleware('checkAuth')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/all', [UserController::class, 'all']);
    Route::get('/{id}', [UserController::class, 'show']);

    Route::post('/', [UserController::class, 'store']);
    Route::match(['put', 'patch'], '/{id}', [UserController::class, 'update']);

    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::post('/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/{id}/force', [UserController::class, 'forceDelete']);

    Route::patch('/{id}/password', [UserController::class, 'updatePassword']);
    Route::post('/{id}/image', [UserController::class, 'updateImage']);

    Route::post('/{uuid}/cv', [UserController::class, 'uploadCvByUuid']);
    Route::post('/import-csv', [UserController::class, 'importUsersCsv']);
});