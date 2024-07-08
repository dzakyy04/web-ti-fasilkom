<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchField extends Model
{
    use HasFactory;

    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_research_field');
    }
}
