<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'student_id',
        'phase',
        'company_id',
        'teacher_id',
        'instructor_name',
        'instructor_position',
        'instructor_email',
        'instructor_phone',
        'start_date',
        'end_date',
        'status',
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

    // Relasi ke Tempat PKL
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relasi ke Guru Pembimbing
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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