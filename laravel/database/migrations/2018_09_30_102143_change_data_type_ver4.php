<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDataTypeVer4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video', function (Blueprint $table) {
            $table->integer('channel_id')->after('id');
        });

        Schema::table('video_thumbnails', function (Blueprint $table) {
            $table->integer('video_id')->after('id');
        });

        Schema::table('channel_thumbnails', function (Blueprint $table) {
            $table->integer('channel_id')->after('id');
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
