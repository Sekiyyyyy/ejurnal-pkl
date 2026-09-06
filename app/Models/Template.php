<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_id',
        'name',
        'file_path',
        'is_active',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    protected static function booted()
    {
        static::deleting(function ($template) {
            if ($template->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($template->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($template->file_path);
            }
        });
    }
}