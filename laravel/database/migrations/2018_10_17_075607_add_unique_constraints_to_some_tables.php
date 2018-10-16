<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueConstraintsToSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel', function (Blueprint $table) {
            $table->unique(['title', 'hash']);
        });

        Schema::table('video', function (Blueprint $table) {
            $table->unique('hash');
        });

        Schema::table('channel_thumbnail', function (Blueprint $table) {
            $table->unique(['std', 'medium', 'high']);
        });

        Schema::table('video_thumbnail', function (Blueprint $table) {
            $table->unique(['std', 'medium', 'high']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channel', function (Blueprint $table) {
            $table->dropUnique(['title', 'hash']);
        });

        Schema::table('video', function (Blueprint $table) {
            $table->dropUnique('hash');
        });

        Schema::table('channel_thumbnail', function (Blueprint $table) {
            $table->dropUnique(['std', 'medium', 'high']);
        });

        Schema::table('video_thumbnail', function (Blueprint $table) {
            $table->dropUnique(['std', 'medium', 'high']);
        });
    }
}
