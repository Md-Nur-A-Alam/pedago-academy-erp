<?php

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
        Schema::create('duplicate_lead_reviews', function (Blueprint $table) {
            $table->id();
            $table->json('incoming_lead_data');
            $table->foreignId('matched_lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('match_reason', 255);
            $table->enum('status', ['Pending Review', 'Approved New', 'Merged', 'Discarded'])->default('Pending Review');
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duplicate_lead_reviews');
    }
};
