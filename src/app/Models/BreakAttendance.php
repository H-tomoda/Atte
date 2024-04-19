<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakAttendance extends Model
{
    use HasFactory;
    protected $table = 'breaks'; //テーブル名の明示

    public function isOnBreak($userId)
    {
        //ユーザーが休憩中であるかの判定実装
        $break = self::where('user_id', $userId)->whereNull('end_time')->first();
        return $break !== null;
    }
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    public function calculateBreakTime($startTime, $endTime)
    {
        $breakStart = Carbon::prase($startTime);
        $breakEnd = Carbon::prase($endTime);
        //休憩時間を分単位で計算
        $breakMinutes = $breakEnd->diffInMinutes($breakStart);
        //分単位の休憩時間をhh:mmに変換
        $hours = floor($breakMinutes / 60);
        $minutes = $breakMinutes % 60;
        $breakTime = sprintf('%02d:%02d', $hours, $minutes);
        return $breakTime;
    }
}
