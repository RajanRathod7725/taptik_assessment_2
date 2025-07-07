<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'type', 'start_time', 'end_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
