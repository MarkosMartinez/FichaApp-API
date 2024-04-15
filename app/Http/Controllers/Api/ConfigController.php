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
        $configuracion = Config::first();

        if (!$configuracion) {
            return response()->json([
                'success' => false,
                'error' => 'No se encontró ningún registro en la base de datos de configuración',
            ]);
        }

        $configuracion->update($request->only(['language', 'app_name']));
        $configuracion->save();

        return response()->json([
            'success' => true,
        ]);
    }

    
}