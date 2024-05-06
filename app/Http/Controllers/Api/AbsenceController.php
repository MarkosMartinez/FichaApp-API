<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AbsenceController extends Controller
{

    //TODO Test it.
    public function addAbsence(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'type' => 'required|string|max:12',
            'notes' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 400);
        }

        $absence = Absence::create([
            'user_id' => $request->user_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'notes' => $request->notes,
            'approved' => null
        ]);

        return response()->json(['success' => true, 'data' => $absence], 201);
    }

    //TODO Test it.
    public function getAbsences(Request $request): JsonResponse
    {
        if(Auth::guard('api')->user()->role == "manager"){
            $user_id = $request->user_id ?? Auth::guard('api')->user()->id;
        }else{
            $user_id = Auth::guard('api')->user()->id;
        }

        $absences = Absence::where('user_id', $user_id)->get();

        return response()->json(['success' => true, 'data' => $absences], 200);
    }

    //TODO Test it.
    public function deleteAbsence(Request $request): JsonResponse
    {
        $absence = Absence::find($request->absence_id);

        if (!$absence) {
            return response()->json(['success' => false, 'error' => 'Absence no encontrada.'], 404);
        }

        if (($absence->user_id != Auth::guard('api')->user()->id && Auth::guard('api')->user()->role != 'manager') || $absence->approved != null) {
            return response()->json(['success' => false, 'error' => 'No estas autorizado para eliminar esa ausencia.'], 403);
        }

        $absence->delete();

        return response()->json(['success' => true, 'message' => 'Ausencia eliminada correctamente.'], 200);
    }

    //TODO Test it.
    public function aproveAbsence(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'absence_id' => 'required|integer',
            'approved' => 'required|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 400);
        }

        $absence = Absence::find($request->absence_id);

        if (!$absence) {
            return response()->json(['success' => false, 'error' => 'Ausencia no encontrada.'], 404);
        }

        $absence->approved = $request->approved;
        $absence->save();

        return response()->json(['success' => true, 'message' => 'Ausencia aprobada/denegada correctamente.'], 200);
    }



}
