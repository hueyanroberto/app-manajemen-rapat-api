<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingPoint extends Model
{
    use HasFactory;

    protected $fillable = ['meeting_id', 'user_id', 'point', 'description'];
}
