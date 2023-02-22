<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'profile_pic', 'leaderboard_start', 'leaderboard_end', 'code'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_organization');
    }
}
