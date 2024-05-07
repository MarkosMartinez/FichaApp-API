<?php

namespace App\Http\Controllers\Api;

use App\Models\Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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

    public function getTime()
    {
        return response()->json([
            'success' => true,
            'serverTime' => now()->toDateTimeString(),
            ]);
    }

    public function resetDb(Request $request)
    {
        $sql = file_get_contents('../secrets/sql/FichaApp-DB.sql');
        if($sql) DB::unprepared($sql);

        return response()->json([
            'success' => true,
        ]);
    }
    
}
