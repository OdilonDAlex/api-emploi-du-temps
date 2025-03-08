<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassRoom extends Model
{
    /** @use HasFactory<\Database\Factories\ClassRoomFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity'
    ];

    public function level(): HasOne {
        return $this->hasOne(Level::class, 'classroom_id');
    }
}
