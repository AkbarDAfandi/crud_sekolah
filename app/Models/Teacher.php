<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'subject_teacher';

    protected $fillable = [
        'teacher_id',
        'subject_id'
    ];

    public function user()
    {
        return $this->belongTo(User::class, 'teacher_id');
    }
    public function subject()
    {
        return $this->belongTo(Subject::class, 'subject_id');
    }
}
