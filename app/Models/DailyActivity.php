<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    protected $fillable = [
        'journal_id',
        'date',
        'division',
        'activity',
        'start_time',
        'end_time',
        'character_values',
        'instructor_notes',
        'is_approved'
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_approved' => 'boolean',
        ];
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }
}