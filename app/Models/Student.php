<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'major_id', 'nisn', 'name', 'class', 'gender', 
        'birth_place', 'birth_date', 'religion', 'address', 'phone',
        'parent_name', 'parent_address', 'parent_phone'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}