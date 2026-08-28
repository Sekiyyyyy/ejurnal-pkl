<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = ['major_id', 'category', 'parent_id', 'name', 'order_number'];

    // Relasi untuk sub-poin (Child)
    public function children()
    {
        return $this->hasMany(Assessment::class, 'parent_id')->orderBy('order_number');
    }
}