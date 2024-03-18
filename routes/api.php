<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::GET('test', function () {
    return response()->json(['estado' => 'ok', 'message' => 'La API funciona!']);
});
