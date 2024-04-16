<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{

    public function punchin(Request $request)
    {
        $user = auth()->user();

        $latestTimeLog = TimeLog::where('userid', $user->id)
        ->orderBy('enter_time', 'desc')
        ->first();

        if ($latestTimeLog && $latestTimeLog->exit_time) {
            TimeLog::create([
                'userid' => $user->id,
                'enter_time' => Carbon::now(),
                'exit_time' => null,
            ]);
        } else if ($latestTimeLog){
            TimeLog::where('userid', $user->id)
                ->where('enter_time', $latestTimeLog->enter_time)
                ->update([
                    'exit_time' => Carbon::now(),
                ]);
        } else {
            TimeLog::create([
                'userid' => $user->id,
                'enter_time' => Carbon::now(),
                'exit_time' => null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Fichado correctamente']);
    }

    public function getSignings(Request $request)
    {
        $user = auth()->user();

        $signings = TimeLog::where('userid', $user->id)
            ->orderBy('enter_time', 'desc')
            ->take(7)
            ->get();

        return response()->json(['signings' => $signings]);
    }

}
