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
}
