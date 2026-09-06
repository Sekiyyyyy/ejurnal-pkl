<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyApproval extends Model
{
    protected $fillable = [
        'journal_id', 'week_number', 'year', 'instructor_paraf', 'instructor_live_photo', 'approved_at',
        'is_rejected', 'rejection_note'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    protected static function booted()
    {
        static::deleting(function ($approval) {
            $filesToDelete = [
                $approval->instructor_paraf,
                $approval->instructor_live_photo,
            ];

            foreach ($filesToDelete as $file) {
                if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                }
            }
        });
    }
}
