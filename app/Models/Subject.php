<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'teacher_id'
    ];

    public function teachers(): BelongsToMany
    {
    return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'teacher_id');
    }
}
