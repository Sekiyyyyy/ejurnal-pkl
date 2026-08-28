<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalAssessment extends Model
{
    protected $fillable = ['journal_id', 'assessment_id', 'is_yes', 'score', 'description'];

    // Menghitung Kualifikasi secara dinamis (Tidak perlu simpan di DB agar tidak duplikat)
    public function getQualificationAttribute()
    {
        if ($this->score === null) return '-';
        if ($this->score >= 90) return 'Sangat Kompeten';
        if ($this->score >= 80) return 'Kompeten';
        if ($this->score >= 70) return 'Cukup Kompeten';
        return 'Belum Kompeten';
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}