<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderboardHistory extends Model
{
    use HasFactory;

    protected $table = "leaderboard_histories";
    protected $fillable = ['user_id', 'organization_id', 'period', 'point'];
}
