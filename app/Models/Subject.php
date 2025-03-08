<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = ['name', 'professor_id'];

    public function levels(): BelongsToMany {
        return $this->belongsToMany(Level::class, 'level_subject', 'subject_id', 'level_id');
    }

    public function professor(): BelongsTo {
        return $this->belongsTo(Professor::class, 'professor_id');
    }
}
