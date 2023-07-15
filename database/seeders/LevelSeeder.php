<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("levels")->insert(['name' => 'Bronze', 'level' => '1', 'min_exp' => 0, 'max_exp' => 100, 'badge_url' => 'Badges_02.png']);
        DB::table("levels")->insert(['name' => 'Silver', 'level' => '2', 'min_exp' => 101, 'max_exp' => 200, 'badge_url' => 'Badges_03.png']);
        DB::table("levels")->insert(['name' => 'Gold', 'level' => '3', 'min_exp' => 201, 'max_exp' => 400, 'badge_url' => 'Badges_04.png']);
        DB::table("levels")->insert(['name' => 'Emerald', 'level' => '4', 'min_exp' => 401, 'max_exp' => 800, 'badge_url' => 'Badges_05.png']);
        DB::table("levels")->insert(['name' => 'Ruby', 'level' => '5', 'min_exp' => 801, 'max_exp' => 1600, 'badge_url' => 'Badges_06.png']);
        DB::table("levels")->insert(['name' => 'Diamond', 'level' => '6', 'min_exp' => 1601, 'max_exp' => 3200, 'badge_url' => 'Badges_07.png']);
    }
}
