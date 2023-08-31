<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class LockController extends Controller
{
    public function getLock()
    {
        try {
            return response()->json(['data' => Lock::all()->first(), 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function lock()
    {
        try {
            $lock = Lock::all()->first();
            $lock->update([
                'isLock' => !$lock->isLock
            ]);
            return response()->json(['data' => $lock, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function votingTime(Request $request)
    {
        try {
            $time = Carbon::now()->addHours($request->hours)->addMinutes($request->minutes);
            $lock = Lock::find(1);
            $lock->update([
                "votingTime" => $time
            ]);
            return response()->json(['data' => $lock, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }
}
