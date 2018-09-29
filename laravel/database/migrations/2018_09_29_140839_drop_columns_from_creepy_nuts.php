<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnsFromCreepyNuts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creepy_nuts', function (Blueprint $table) {
            $table->dropColumn(['thumnail_default', 'thumnail_medium', 'thumnail_high']);
        });

        Schema::table('creepy_nuts_video', function (Blueprint $table) {
            $table->dropColumn(['thumnail_default', 'thumnail_medium', 'thumnail_high']);
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
