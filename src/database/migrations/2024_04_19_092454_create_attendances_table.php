<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('出勤中'); // 出勤中がデフォルト
            $table->dateTime('clock_in');
            $table->dateTime('clock_out')->nullable(); // NULLを許可する
            $table->integer('daily_attendance_count')->default(0); // 出勤回数カウント
            $table->timestamps();
        });
        // 既存のテーブルに新しいカラムを追加するため、create() ではなく table() を使用する
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('break_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
