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
        Schema::create('lead_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('from_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('to_team_id')->constrained('teams')->restrictOnDelete();
            $table->text('reason');
            $table->foreignId('sender_tl_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sender_tl_approved_at')->nullable();
            $table->foreignId('receiver_tl_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('receiver_tl_approved_at')->nullable();
            $table->boolean('is_auto_transferred')->default(false);
            $table->boolean('is_admin_override')->default(false);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamp('transferred_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_transfers');
    }
};
