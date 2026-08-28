<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'journal_id',
        'date',
        'status',
        'entry_time',
        'exit_time',
        'notes',
        'evidence_path',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }
}