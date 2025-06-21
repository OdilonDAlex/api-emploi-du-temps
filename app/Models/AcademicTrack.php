<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicTrack extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicTrackFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'studentsNumber',
        'classroom_id',
        'level_id'
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    public function preferedClassRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'classroom_id', 'id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'academic_track_subject', 'academic_track_id', 'subject_id');
    }
}
