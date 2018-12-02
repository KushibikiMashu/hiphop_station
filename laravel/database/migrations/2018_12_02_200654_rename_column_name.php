<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel', function (Blueprint $table) {
            $table->renameColumn('published_at', 'published_at_original');
        });

        Schema::table('video', function (Blueprint $table) {
            $table->renameColumn('published_at', 'published_at_original');
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
            $table->renameColumn('published_at_original', 'published_at');
        });

        Schema::table('video', function (Blueprint $table) {
            $table->renameColumn('published_at_original', 'published_at');
        });
    }
}
