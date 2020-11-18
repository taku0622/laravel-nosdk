<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancelInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_informations', function (Blueprint $table) {
            $table->id(); //1
            $table->date('date'); //2
            $table->text('period'); //3
            $table->text('lecture_name'); //4
            $table->text('teacher_name'); //5
            $table->text('grade'); //6
            $table->text('department'); //7
            $table->text('class'); //8
            $table->text('note'); //9
            $table->date('posted_date'); //10
            $table->text('contributor'); //11
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cancel_informations');
    }
}
