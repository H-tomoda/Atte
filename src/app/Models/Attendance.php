<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'clock_in',
        'clock_out',
        'daily_attendance_count',
        'break_time',
        'total_work_time',
    ];

    // $dates プロパティを追加して、clock_in と clock_out を Carbon オブジェクトとして扱う
    protected $dates = ['clock_in', 'clock_out'];

    protected $casts = [
        'break_time' => 'integer', // break_time を整数型にキャスト
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakAttendance::class);
    }
    public function updateBreakTime($attendanceId, $breakTime)
    {
        // Eloquent を使用してデータベースのレコードを更新
        $attendance = Attendance::findOrFail($attendanceId);
        $attendance->break_time = (int)$breakTime; // 整数値にキャストして代入
        $attendance->save();
    }
}
