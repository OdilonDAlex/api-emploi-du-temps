<?php

use App\Models\Level;
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
        Schema::create('level_subject', function (Blueprint $table) {
            $table->foreignIdFor(Level::class, 'level_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Subject::class, 'subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['level_id', 'subject_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_subject');
    }
};
