<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Classes;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nipd',
        'name',
        'class_id',
        'gender',
        'date_of_birth',
        'image_url'
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the attendance records for the student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
}
