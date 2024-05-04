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
            $table->integer('break_time')->nullable(); // 1日総休憩時間を整数型に変更し、NULLを許可する
            $table->string('total_work_time')->nullable(); // 文字列型に変更し、NULLを許可する
            $table->timestamps();
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
