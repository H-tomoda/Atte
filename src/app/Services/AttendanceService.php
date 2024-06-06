<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceService
{
    public static function autoClockOut()
    {
        $attendances = Attendance::whereNull('clock_out')->get();

        foreach ($attendances as $attendance) {
            $attendance->clock_out = Carbon::parse($attendance->clock_in)->endOfDay();
            $attendance->status = '2'; // 退勤済みステータス
            $attendance->save();
        }
    }
}
