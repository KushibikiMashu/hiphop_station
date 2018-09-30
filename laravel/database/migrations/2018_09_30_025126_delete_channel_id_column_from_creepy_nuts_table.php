<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteChannelIdColumnFromCreepyNutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creepy_nuts', function (Blueprint $table) {
            Schema::table('creepy_nuts', function (Blueprint $table) {
                $table->dropColumn('channelTitle');
            });
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
            //
        });
    }
}
