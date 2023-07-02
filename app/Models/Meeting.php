<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'start_time', 'end_time', 'location', 
        'description', 'code', 'organization_id', 
        'meeting_note', 'real_start', 'real_end'
    ];
}
