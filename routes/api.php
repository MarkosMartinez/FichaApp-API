<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::GET('test', function () {
    return response()->json(['success' => true, 'message' => 'La API funciona!']);
});

# Gestion de usuarios/sesión
Route::middleware('auth:api')->group( function () {
    Route::POST('add-user', [AuthController::class, 'add']);
    Route::GET('logout', [AuthController::class, 'logout']);
    Route::GET('logoutall', [AuthController::class, 'logoutall']);
});
Route::POST('login', [AuthController::class, 'login']);

Route::GET('check-token', function () {
    return Auth::guard('api')->check() ? response()->json(['success' => true]) : response()->json(['success' => false]);
});
