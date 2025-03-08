<?php

use App\Models\ClassRoom;
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
        Schema::table('levels', function (Blueprint $table) {
            $table->foreignIdFor(ClassRoom::class, 'classroom_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(ClassRoom::class, 'classroom_id');
        });
    }
};
