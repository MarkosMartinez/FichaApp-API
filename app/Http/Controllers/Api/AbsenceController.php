<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AbsenceController extends Controller
{

    public function addAbsence(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'type' => 'required|string|max:12'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 400);
        }

        if(Auth::guard('api')->user()->role == "manager"){
            if (isset($request->userid)) {
                $userid = User::find($request->userid);
                if (!$userid) {
                    return response()->json(['success' => false, 'error' => 'Usuario no encontrado.'], 404);
                }
            }else{
                $userid = Auth::guard('api')->user()->id;
            }

        }else{
            $userid = Auth::guard('api')->user()->id;
        }

        $absence = Absence::create([
            'userid' => $userid,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'notes' => $request->notes ?? null,
            'approved' => null
        ]);

        return response()->json(['success' => true, 'data' => $absence], 201);
    }

    public function getAbsences(Request $request): JsonResponse
    {
        if(Auth::guard('api')->user()->role == "manager"){
            $userid = $request->userid ?? Auth::guard('api')->user()->id;
        }else{
            $userid = Auth::guard('api')->user()->id;
        }

        $absences = Absence::where('userid', $userid)->get();

        return response()->json(['success' => true, 'data' => $absences], 200);
    }

    public function deleteAbsence(Request $request): JsonResponse
    {
        $absence = Absence::find($request->absenceid);

        if (!$absence) {
            return response()->json(['success' => false, 'error' => 'Absence no encontrada.'], 404);
        }

        if (($absence->userid != Auth::guard('api')->user()->id && Auth::guard('api')->user()->role != 'manager') || $absence->approved != null) {
            return response()->json(['success' => false, 'error' => 'No estas autorizado para eliminar esa ausencia.'], 403);
        }

        $absence->delete();

        return response()->json(['success' => true, 'message' => 'Ausencia eliminada correctamente.'], 200);
    }

    public function aproveAbsence(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'absenceid' => 'required|integer',
            'approved' => 'required|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 400);
        }

        $absence = Absence::find($request->absenceid);

        if (!$absence) {
            return response()->json(['success' => false, 'error' => 'Ausencia no encontrada.'], 404);
        }
        if ($absence->approved != null) {
            return response()->json(['success' => false, 'error' => 'Ausencia ya aprobada/denegada.'], 404);
        }

        $absence->approved = $request->approved;
        $absence->save();

        return response()->json(['success' => true, 'message' => 'Ausencia aprobada/denegada correctamente.'], 200);
    }



}
