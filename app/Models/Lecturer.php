<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function researchFields()
    {
        return $this->belongsToMany(ResearchField::class, 'lecturer_research_field');
    }
}
