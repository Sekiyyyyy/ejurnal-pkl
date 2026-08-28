<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'address', 'contact_person', 'phone'
    ];

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}