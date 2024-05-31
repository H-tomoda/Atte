<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // ユーザーID
            $table->date('date');  // 日付
            $table->time('start_time');  // 開始時間
            $table->time('end_time');  // 終了時間
            $table->string('title');  // 活動のタイトル
            $table->string('activity');  // 活動内容
            $table->string('location')->nullable();  // 場所（必要に応じてnull可）
            $table->text('description')->nullable();  // 詳細説明（必要に応じてnull可）
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');  // ユーザーが削除された場合、関連するスケジュールも削除される
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
