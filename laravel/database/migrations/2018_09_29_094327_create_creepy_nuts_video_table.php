<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreepyNutsVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creepy_nuts_video', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('cannelId');
            $table->string('publishedAt');
            $table->string('thumnail_default');
            $table->string('thumnail_medium');
            $table->string('thumnail_high');

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
        Schema::dropIfExists('creepy_nuts_video');
    }
}
