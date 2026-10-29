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
        Schema::create('user_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->time('shift_start_time')->default('10:00:00');
            $table->time('shift_end_time')->default('18:00:00');
            $table->boolean('is_on_duty_today')->default(true);
            $table->enum('availability_status', ['Available', 'Busy', 'Unavailable', 'Off Duty', 'Lazy', 'Resigned'])->default('Off Duty');
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value');
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_shifts');
        Schema::dropIfExists('system_settings');
    }
};
