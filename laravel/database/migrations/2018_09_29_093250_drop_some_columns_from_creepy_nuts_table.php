<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSomeColumnsFromCreepyNutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creepy_nuts', function (Blueprint $table) {
            $table->string('thumnail_default');
            $table->string('thumnail_medium');
            $table->string('thumnail_high');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('creepy_nuts', function (Blueprint $table) {
            $table->string('channelTitle');
            $table->string('channelId');
            $table->string('title');
            $table->string('videoId');
            
        });
    }
}
