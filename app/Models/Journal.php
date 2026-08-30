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
        'teacher_signature', 'kaprog_signature',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
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
}