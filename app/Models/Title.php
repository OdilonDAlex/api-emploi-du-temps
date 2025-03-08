<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Title extends Model
{
    /** @use HasFactory<\Database\Factories\TitleFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function abbr(): string {
        return Str::limit($this->name, '3', '.');
    }

    public function professors(): HasMany{
        return $this->hasMany(Professor::class, 'title_id');
    }
}
