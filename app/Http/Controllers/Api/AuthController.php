<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    
    public function add(Request $request): JsonResponse

    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
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

    public function login(Request $request): JsonResponse

    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('FichaApp')-> accessToken; 
            $success['name'] =  $user->name;
            
            return response()->json([
                'success' => true,
                'data'=> $success,
                'message' => 'Usuario logueado correctamente.',
                ]);
        }else{ 
            return response()->json([
                'success' => false,
                'message' => 'Unauthorised',
                'error'=>'Unauthorised',
                ]);
        } 
    }

    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->delete();
        return response()
        ->json([
            'success' => true,
            'message' => 'Sesion cerrada correctamente!',
        ]);
    }
        public function logoutall(Request $request)
    {
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });
        return response()->json([
            'success' => true,
            'message' => 'Sesiones cerradas correctamente!',
        ]);
    }



}
