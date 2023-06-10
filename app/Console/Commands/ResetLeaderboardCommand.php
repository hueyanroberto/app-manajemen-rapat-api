<?php

namespace App\Console\Commands;

use App\Models\LeaderboardHistory;
use App\Models\Organization;
use App\Models\UserOrganization;
use Illuminate\Console\Command;

class ResetLeaderboardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:resetLeaderboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set("Asia/Jakarta");
        $currDate = date("Y-m-d");
        $organizations = Organization::where('leaderboard_end', '<', $currDate)->get();

        foreach ($organizations as $organization) {
            $duration = $organization->leaderboard_duration;
            $period = $organization->leaderboard_period;

            $userOrganizations = UserOrganization::where('organization_id', $organization->id)->get();
            foreach ($userOrganizations as $userOrganization) {
                $leaderboardHistory = new LeaderboardHistory();
                $leaderboardHistory->user_id = $userOrganization->user_id;
                $leaderboardHistory->organization_id = $userOrganization->organization_id;
                $leaderboardHistory->period = $period;
                $leaderboardHistory->point = $userOrganization->points_get;
                $leaderboardHistory->save();

                $userOrganization->update(['points_get' => 0]);

                //TODO sendNotif
            }

            $leaderboardStart = date("Y-m-d");
            $currTime = strtotime($leaderboardStart);
            $leaderboardEnd = date("Y-m-d", strtotime("+". $duration ." month", $currTime));

            $organization->update(['leaderboard_start' => $leaderboardStart, 'leaderboard_end' => $leaderboardEnd, 'leaderboard_period' => $period + 1]);
        }
    }
}
