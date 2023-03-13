<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('achievements')->insert(['name'=> 'Membuat rapat 10 kali||Create meeting 10 times', 'reward_exp' => 5, 'milestone' => 10, 'badge_url' => 'createMeeting1.png']);
        DB::table('achievements')->insert(['name'=> 'Membuat rapat 25 kali||Create meeting 25 times', 'reward_exp' => 10, 'milestone' => 25, 'badge_url' => 'createMeeting2.png']);
        DB::table('achievements')->insert(['name'=> 'Membuat rapat 50 kali||Create meeting 50 times', 'reward_exp' => 15, 'milestone' => 50, 'badge_url' => 'createMeeting3.png']);
        DB::table('achievements')->insert(['name'=> 'Memulai rapat tepat waktu 10 kali||Start meeting on time 10 times', 'reward_exp' => 5, 'milestone' => 10, 'badge_url' => 'startMeeting1.png']);
        DB::table('achievements')->insert(['name'=> 'Memulai rapat tepat waktu 25 kali||Start meeting on time 25 times', 'reward_exp' => 10, 'milestone' => 25, 'badge_url' => 'startMeeting2.png']);
        DB::table('achievements')->insert(['name'=> 'Memulai rapat tepat waktu 50 kali||Start meeting on time 50 times', 'reward_exp' => 15, 'milestone' => 50, 'badge_url' => 'startMeeting3.png']);
        DB::table('achievements')->insert(['name'=> 'Mengakhiri rapat tepat waktu 10 kali||End meeting on time 10 times', 'reward_exp' => 5, 'milestone' => 10, 'badge_url' => 'endMeeting1.png']);
        DB::table('achievements')->insert(['name'=> 'Mengakhiri rapat tepat waktu 25 kali||End meeting on time 25 times', 'reward_exp' => 10, 'milestone' => 25, 'badge_url' => 'endMeeting2.png']);
        DB::table('achievements')->insert(['name'=> 'Mengakhiri rapat tepat waktu 50 kali||End meeting on time 50 times', 'reward_exp' => 15, 'milestone' => 50, 'badge_url' => 'endMeeting3.png']);
        DB::table('achievements')->insert(['name'=> 'Menyelesaikan tugas tepat waktu 10 kali||Finish assignment on time 10 times', 'reward_exp' => 5, 'milestone' => 10, 'badge_url' => 'finishAssigment1.png']);
        DB::table('achievements')->insert(['name'=> 'Menyelesaikan tugas tepat waktu 25 kali||Finish assignment on time 25 times', 'reward_exp' => 10, 'milestone' => 25, 'badge_url' => 'finishAssigment2.png']);
        DB::table('achievements')->insert(['name'=> 'Menyelesaikan tugas tepat waktu 50 kali||Finish assignment on time 50 times', 'reward_exp' => 15, 'milestone' => 50, 'badge_url' => 'finishAssigment3.png']);
    }
}
