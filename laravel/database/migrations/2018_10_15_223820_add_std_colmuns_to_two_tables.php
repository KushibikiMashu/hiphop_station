<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStdColmunsToTwoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel_thumbnail', function (Blueprint $table) {
            $table->string('std')->after('channel_id');
        });

        Schema::table('video_thumbnail', function (Blueprint $table) {
            $table->string('std')->after('video_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channel_thumbnail', function (Blueprint $table) {
            $table->dropColumn('std');
        });

        Schema::table('video_thumbnail', function (Blueprint $table) {
            $table->dropColumn('std');
        });
    }
}
