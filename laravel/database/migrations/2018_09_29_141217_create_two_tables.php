<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cannnel_thumbnails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_id');
            $table->string('default');
            $table->string('medium');
            $table->string('high');
            $table->timestamps();
        });

        Schema::create('video_thumbnails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('video_id');
            $table->string('default');
            $table->string('medium');
            $table->string('high');
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
        //
    }
}
