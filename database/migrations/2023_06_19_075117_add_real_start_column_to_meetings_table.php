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
        Schema::table('meetings', function (Blueprint $table) {
            $table->dateTime('real_end')->nullable()->after('end_time');
            $table->dateTime('real_start')->nullable()->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            if (Schema::hasColumn('meetings', 'real_start')) {
                $table->dropColumn('real_start');
            }
            if (Schema::hasColumn('meetings', 'real_end')) {
                $table->dropColumn('real_end');
            }
        });
    }
};
