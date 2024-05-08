<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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

    public function edit(Request $request): JsonResponse
    {
        if($request->id && Auth::guard('api')->user()->role === 'manager'){

            $user = User::findOrFail($request->id);
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'role' => 'required',
                'id' => 'required',
            ]);
            
            if($validator->fails()){
                return response()->json([
                'success' => false,
                'message' => 'Error al modificar el usuario!',
                ]);
            }
            if($request->new_password){
                $validator = Validator::make($request->all(), [
                    'new_password' => 'required',
                    'c_new_password' => 'required|same:new_password',
                ]);
                if($validator->fails()){
                    return response()->json([
                    'success' => false,
                    'message' => 'Error al modificar el usuario! Las contraseñas no coinciden',
                ]);
                }
                $user->password = bcrypt($request->new_password);
            }
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Usuario modificado correctamente.',
                ]);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            if($validator->fails()){
                return response()->json([
                'success' => false,
                'message' => 'Error al modificar el usuario!',
                ]);
            }
            Auth::shouldUse('web');
            $user = Auth::guard('api')->user();
            if(Auth::attempt(['email' => Auth::guard('api')->user()->email, 'password' => $request->password])){
                if($request->new_password){
                    $validator = Validator::make($request->all(), [
                        'new_password' => 'required',
                        'c_new_password' => 'required|same:new_password',
                    ]);
                    if($validator->fails()){
                        return response()->json([
                        'success' => false,
                        'message' => 'Error al modificar el usuario! Las contraseñas no coinciden',
                    ]);
                    }
                    $user->password = bcrypt($request->new_password);
                }

                $user->name = $request->name;
                $user->email = $request->email;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Usuario modificado correctamente.',
                    ]);

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Error al modificar el usuario! Contraseña incorrecta.',
                    ]);
            }
        }
    }

    public function get(Request $request): JsonResponse
    {
        if($request->id && Auth::guard('api')->user()->role === 'manager'){
            $user = User::findOrFail($request->id);
        }else{
            $user = auth()->user();
        }
        return response()->json([
            'success' => true,
            'message' => 'Informacion del usuario obtenida.',
            'data' => $user,
            ]);
    }
}
