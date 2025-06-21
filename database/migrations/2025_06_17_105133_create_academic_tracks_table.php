<?php

use App\Models\ClassRoom;
use App\Models\Level;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('studentsNumber');
            $table->foreignIdFor(Level::class, 'level_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ClassRoom::class, 'classroom_id')->nullable()->contrainted()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_tracks');
    }
};
