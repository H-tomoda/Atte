<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out',
        'break_time',
        'daily_attendance_count',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function breaks()
    {
        return $this->hasMany(BreakAttendance::class);
    }
    public function calculateBreakTime($userId, $startTime, $endTime)
    {
        $breakStart = BreakAttendance::where('user_id', $userId)
            ->where('start_time', '>=', $startTime)
            ->where('start_time', '<=', $endTime)
            ->min('start_time');
        $breakEnd = BreakAttendance::where('user_id', $userId)
            ->where('end_time', '>=', $startTime)
            ->where('end_time', '<=', $endTime)
            ->max('end_time');
        if ($breakStart && $breakEnd) {
            return Carbon::parse($breakEnd)->diffInMinutes(Carbon::parse($breakStart)) / 60;
        } else {
            return 0;
        }
    }
}
