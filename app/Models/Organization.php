<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'profile_pic', 'leaderboard_start', 
        'leaderboard_end', 'code', 'leaderboard_duration', 'leaderboard_period'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_organization');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'organization_id', 'id');
    }
}
