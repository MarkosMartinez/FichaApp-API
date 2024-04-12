<?php

namespace App\Http\Controllers\Api;

use App\Models\Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAll()
    {
        $configuracion = Config::all();
        return response()->json([
            'success' => true,
            'config'    => $configuracion,
            ]);
    }

    public function set(Request $request)
{
    $validator = Validator::make($request->all(), [
        'key' => 'required',
        'value' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validator->errors(),
        ]);
    }

    $key = $request->input('key');
    $value = $request->input('value');

    $configuracion = Config::first();

    if (!$configuracion) {
        return response()->json([
            'success' => false,
            'error' => 'No se encontró ningún registro en la base de datos de configuración',
        ]);
    }

    if (!$configuracion->{$key}) {
        return response()->json([
            'success' => false,
            'error' => 'La clave especificada no existe en el registro de configuración',
        ]);
    }

    $configuracion->update([$key => $value]);
    $configuracion->save();

    return response()->json([
        'success' => true,
    ]);
}

    
}
