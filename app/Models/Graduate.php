<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Graduate extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_competencies',
        'support_competencies',
        'graduate_competencies',
    ];

    protected $casts = [
        'main_competencies' => 'array',
        'support_competencies' => 'array',
        'graduate_competencies' => 'array',
    ];
}
