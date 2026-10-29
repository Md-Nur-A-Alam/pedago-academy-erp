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
        Schema::create('lead_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('call_status', 50);
            $table->string('customer_interest', 50);
            $table->text('customer_concern')->nullable();
            $table->text('sales_note');
            $table->string('next_action', 50);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('lead_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('followup_date_time')->index();
            $table->text('note')->nullable();
            $table->boolean('is_done')->default(false);
            $table->boolean('overdue_alert_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_followups');
        Schema::dropIfExists('lead_feedbacks');
    }
};
