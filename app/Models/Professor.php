<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Model
{
    /** @use HasFactory<\Database\Factories\ProfessorFactory> */

    use HasFactory;

    protected $fillable = [
        'name',
        'firstname',
        'title_id'
    ];

    public function title(): BelongsTo{
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function subjects(): HasMany {
        return $this->hasMany(Subject::class, 'professor_id');
    }
}
