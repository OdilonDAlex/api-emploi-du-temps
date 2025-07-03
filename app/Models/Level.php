<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Level extends Model
{
    /** @use HasFactory<\Database\Factories\LevelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function academicTracks(): HasMany
    {
        return $this->hasMany(AcademicTrack::class, 'level_id', 'id');
    }

    public function abbrName(): string
    {
        $exploded = str_split($this->name);
        return "{$exploded[0]}{$exploded[count($exploded) - 1]}";
    }
}
