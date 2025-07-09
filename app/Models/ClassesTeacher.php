<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassesTeacher extends Model
{
    use HasFactory;

    protected $table = 'classes_teachers';

    protected $fillable = [
        'class_id',
        'teacher_id',
    ];

    public function class(): BelongsToMany
    {
        return $this->belongsToMany(Classes::class, 'classes_teachers', 'class_id', 'teacher_id');
    }

    public function teacher(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'classes_teachers', 'teacher_id', 'class_id');
    }
}
