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
        Schema::table('meeting_points', function (Blueprint $table) {
            $table->text('description')->nullable()->default('')->after('point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meeting_points', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_points', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
