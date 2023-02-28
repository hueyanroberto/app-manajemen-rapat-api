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
        Schema::table('user_meeting', function (Blueprint $table) {
            $table->integer('status')->default(2)->after("meeting_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_meeting', function (Blueprint $table) {
            if (Schema::hasColumn('user_meeting', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
