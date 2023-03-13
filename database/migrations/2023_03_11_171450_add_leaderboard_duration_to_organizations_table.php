<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->integer('leaderboard_period')->default(1)->after('leaderboard_end');
            $table->integer('leaderboard_duration')->default(1)->after('leaderboard_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'leaderboard_period')) {
                $table->dropColumn('leaderboard_period');
            }

            if (Schema::hasColumn('organizations', 'leaderboard_duration')) {
                $table->dropColumn('leaderboard_duration');
            }
        });
    }
};
