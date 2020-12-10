<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actives', function (Blueprint $table) {
            $table->text('user_id');
            $table->integer('question_count');
            $table->integer('important_count');
            $table->integer('new_count');
            $table->integer('canel_count');
            $table->integer('event_count');
            $table->integer('setting_count');
            $table->integer('other_count');
            $table->integer('push_important_count');
            $table->integer('push_new_count');
            $table->integer('push_cancel_count');
            $table->integer('push_event_count');
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actives');
    }
}
