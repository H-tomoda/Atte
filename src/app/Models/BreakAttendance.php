<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BreakAttendance extends Model
{
    use HasFactory;

    protected $table = 'breaks'; // テーブル名の明示

    protected $fillable = [
        'attendance_id',
        'start_time',
        'end_time',
    ];
    public function isOnBreak($attendanceId)
    {
        //ユーザーが休憩中であるかの判定実装
        $break = self::where('attendance_id', $attendanceId)->whereNull('end_time')->first();
        return $break !== null;
    }
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    public function calculateBreakTime($startTime, $endTime)
    {
        $breakStart = Carbon::parse($startTime);
        $breakEnd = Carbon::parse($endTime);
        //休憩時間を分単位で計算
        $breakMinutes = $breakEnd->diffInMinutes($breakStart);
        //分単位の休憩時間をhh:mmに変換
        $hours = floor($breakMinutes / 60);
        $minutes = $breakMinutes % 60;
        $breakTime = sprintf('%02d:%02d', $hours, $minutes);
        return $breakTime;
    }
}
