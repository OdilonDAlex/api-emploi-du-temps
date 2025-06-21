<?php

use App\Models\AcademicTrack;
use App\Models\Subject;
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
        Schema::create('academic_track_subject', function (Blueprint $table) {
            $table->foreignIdFor(AcademicTrack::class, 'academic_track_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Subject::class, 'subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['academic_track_id', 'subject_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_track_subject');
    }
};
