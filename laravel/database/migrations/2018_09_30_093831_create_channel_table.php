<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('channel_hash');
            $table->string('published_at');
            $table->timestamps();
        });

        Schema::create('video', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_id');
            $table->string('title');
            $table->string('video_hash');
            $table->string('published_at');
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
        Schema::dropIfExists('channel');
    }
}
