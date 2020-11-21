<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->boolean('all_department')->nullable(); // 1
            $table->boolean('important')->nullable(); // 2
            $table->boolean('cs')->nullable(); // 3
            $table->boolean('bs')->nullable(); // 4
            $table->boolean('es')->nullable(); // 5
            $table->boolean('ms')->nullable(); // 6
            $table->boolean('hs')->nullable(); // 7
            $table->boolean('ds')->nullable(); // 8
            $table->boolean('me')->nullable(); // 9
            $table->boolean('ee')->nullable(); // 10
            $table->boolean('ac')->nullable(); // 11
            $table->boolean('nu')->nullable(); // 12
            $table->boolean('ce')->nullable(); // 13
            $table->boolean('pt')->nullable(); // 14
            $table->boolean('ot')->nullable(); // 15
            $table->boolean('mt')->nullable(); // 16
            $table->boolean('inhachi')->nullable(); // 17
            $table->boolean('inds')->nullable(); // 18
            $table->boolean('inkogaku')->nullable(); // 19
            $table->integer('information_id');
            $table->unique('information_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
