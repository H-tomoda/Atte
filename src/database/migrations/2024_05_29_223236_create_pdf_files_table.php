<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdfFilesTable extends Migration
{
    public function up()
    {
        Schema::create('pdf_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('document_type')->nullable(); // 証票種別
            $table->date('transaction_date')->nullable(); // 取引日付
            $table->string('client')->nullable(); // 取引先
            $table->integer('transaction_amount')->nullable(); // 取引金額
            $table->text('remarks')->nullable(); // 補足事項
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pdf_files');
    }
}
