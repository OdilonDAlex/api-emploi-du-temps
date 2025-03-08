<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Professor extends Model
{
    /** @use HasFactory<\Database\Factories\ProfessorFactory> */

    use HasFactory;

    protected $fillable = [
        'name',
        'firstname'
    ];

    public function title(): BelongsTo{
        return $this->belongsTo(Title::class, 'title_id');
    }
}
