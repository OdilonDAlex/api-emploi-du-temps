<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'duration',
        'subject_id',
        'timetable_id'
    ];

    public function subject(): BelongsTo {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function timetable(): BelongsTo {
        return $this->belongsTo(Timetable::class, 'timetable_id');
    }
}
