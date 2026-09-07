<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'student_id', 'phase', 'status',
        'company_name', 'company_address', 
        'teacher_name', 'teacher_address', 'teacher_phone', // Update
        'instructor_name', 'instructor_position', 'instructor_email', 'instructor_phone', 'instructor_address', // Update
        'start_date', 'end_date',
        'student_signature', 'parent_signature', 'instructor_signature', 'instructor_paraf',
        'teacher_signature', 'teacher_live_photo', 'kaprog_signature', 'monitoring_locked_at',
        'instructor_live_photo', 'teacher_rejection_note', 'instructor_rejection_note'
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'monitoring_locked_at' => 'datetime',
        ];
    }

    // Relasi ke Siswa
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class);
    }

    public function assessments()
    {
        return $this->hasMany(JournalAssessment::class);
    }

    public function weeklyApprovals()
    {
        return $this->hasMany(WeeklyApproval::class);
    }

    protected static function booted()
    {
        static::deleting(function ($journal) {
            $filesToDelete = [
                $journal->student_signature,
                $journal->parent_signature,
                $journal->instructor_signature,
                $journal->instructor_paraf,
                $journal->teacher_signature,
                $journal->kaprog_signature,
                $journal->instructor_live_photo,
                $journal->teacher_live_photo,
            ];

            foreach ($filesToDelete as $file) {
                if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                }
            }
            
            // Delete weekly approvals (which will trigger their own deleting events if they have any, but we will add it there too)
            // Wait, using ->delete() on the relation like $journal->weeklyApprovals()->delete() does NOT trigger eloquent events.
            // But we already loop through weeklyApprovals in StudentController destroy. So here we just loop again for safety if they are deleted via model.
            $journal->weeklyApprovals->each->delete();
            $journal->dailyActivities()->delete();
            $journal->attendances()->delete();
            $journal->assessments()->delete();
        });
    }
}