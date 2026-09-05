<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyApproval extends Model
{
    protected $fillable = [
        'journal_id', 'week_number', 'year', 'instructor_paraf', 'instructor_live_photo', 'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }
}
