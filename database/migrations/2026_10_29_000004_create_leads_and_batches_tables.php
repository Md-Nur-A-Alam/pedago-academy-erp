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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id');
            $table->string('student_name', 150);
            $table->string('contact_number', 25)->index();
            $table->string('alternative_contact_number', 25)->nullable()->index();
            $table->string('email', 120)->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('class', 50)->nullable();
            $table->foreignId('interested_course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->enum('status', [
                'New',
                'Assigned',
                'Contacted',
                'Follow-up',
                'Interested',
                'Enrolled - Pending Payment',
                'Payment Under Review',
                'Converted',
                'Not Interested',
                'Unreachable',
                'Lost',
            ])->default('New')->index();
            $table->foreignId('current_owner_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->foreign('source_id')->references('source_id')->on('leads_source')->restrictOnDelete();
        });

        Schema::create('lead_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('batch_size')->default(3);
            $table->enum('status', ['Active', 'Completed'])->default('Active');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('lead_batches')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->string('action_taken', 50)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'lead_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_batch_items');
        Schema::dropIfExists('lead_batches');
        Schema::dropIfExists('leads');
    }
};
