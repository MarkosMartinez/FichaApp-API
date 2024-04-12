<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ConfigController;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::GET('test', function () {
    return response()->json(['success' => true, 'message' => 'La API funciona!']);
});


# Gestion de sesiónes
Route::middleware('auth:api')->group( function () {
    Route::GET('logout', [AuthController::class, 'logout']);
    Route::GET('logoutall', [AuthController::class, 'logoutall']);
});
Route::POST('login', [AuthController::class, 'login']);

Route::GET('check-token', function () {
    return Auth::guard('api')->check() ? response()->json(['success' => true]) : response()->json(['success' => false]);
});


# Gestion de usuarios
Route::middleware('auth:api')->group( function () {
    Route::POST('add-user', [UserController::class, 'add']);
});

Route::middleware('manager')->group( function () {
    Route::GET('users', [UserController::class, 'index']);
});


# Configuración
Route::middleware('manager')->group( function () {
    Route::GET('get-config', [ConfigController::class, 'getAll']);
    Route::POST('set-config', [ConfigController::class, 'set']);
});
