<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Timetable extends Model
{
    /** @use HasFactory<\Database\Factories\TimetableFactory> */

    use HasFactory;
    protected $fillable = ['author_id', 'weekOf'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'timetable_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
