<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPublishedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel', function (Blueprint $table) {
            $table->dateTime('published_at')
                ->after('video_count')
                ->default(\Carbon\Carbon::now()->format('Y-m-d H:i:s'));
        });

        Schema::table('video', function (Blueprint $table) {
            $table->dateTime('published_at')
                ->after('genre')
                ->default(\Carbon\Carbon::now()->format('Y-m-d H:i:s'));
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
            $table->dropColumn('published_at');
        });

        Schema::table('video', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
}
