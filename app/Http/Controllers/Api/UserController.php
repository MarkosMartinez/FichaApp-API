<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $usuarios = User::all();
        return response()->json([
            'success' => true,
            'users'    => $usuarios,
            ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json([
            'success' => false,
            'message' => 'Error al añadir el usuario!',
            ]);     

        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        //$success['token'] =  $user->createToken('FichaApp')->accessToken;
        $success['name'] =  $user->name;

        return response()->json([
            'success' => true,
            'data'    => $success,
            'message' => 'Usuario registrado correctamente.',
            ]);
    }

}
