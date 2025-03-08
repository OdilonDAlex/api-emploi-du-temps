<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Level extends Model
{
    /** @use HasFactory<\Database\Factories\LevelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'studentsNumber'
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'level_subject', 'level_id', 'subject_id');
    }

    public function preferenceClassRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }

    /**
     * @param Array<int, ClassRoom> $classrooms
     * @return int | null
     */
    public function getPreferenceClassRoomIndex($classrooms)
    {
        $preferenceClassRoom = $this->preferenceClassRoom;
        foreach ($classrooms as $index => $classroom) {
            if ($classroom === $preferenceClassRoom) {
                return $index;
            }
        }
        return null;
    }
}
