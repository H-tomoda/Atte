<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendances = $user->$attendances()->orderBy('create_at', 'desc')->get();
        return view('attendance.index' . compact('attendances'));
    }
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $attendance = new Attendance();
        $attendance->user_id = $user->id;
        $attendance->clock_in = now();
        $attendance->save();
        return redirect()->back();
    }
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->whereNull('clock_out')->first();
        $attendance->clock_out = now();
        $attendance->save();
        return redirect()->back();
    }
}
