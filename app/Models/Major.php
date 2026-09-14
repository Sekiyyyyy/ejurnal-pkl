<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'code', 'name', 'head_name', 'head_nip', 'head_signature_path'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function kaprodis()
    {
        return $this->hasMany(Kaprodi::class);
    }
}