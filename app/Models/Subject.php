<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = ['name', 'professor_id'];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'subject_id');
    }

    public function academicTracks(): BelongsToMany
    {
        return $this->belongsToMany(AcademicTrack::class, 'academic_track_subject', 'subject_id', 'academic_track_id');
    }
}
