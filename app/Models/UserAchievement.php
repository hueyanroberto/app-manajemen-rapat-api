<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserAchievement extends Model
{
    use HasFactory;

    protected $table = "user_achievement";
    protected $fillable = ['user_id', 'achievement_id', 'progress', 'status'];

    public function Achievement(): HasOne
    {
        return $this->hasOne(Achievement::class);
    }
}
