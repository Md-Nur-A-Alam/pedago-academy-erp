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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->text('conditions')->nullable();
            $table->enum('subject', ['English', 'Bangla', 'Mathematics', 'Science', 'Programming', 'General'])->default('General');
            $table->enum('version', ['Bangla', 'English'])->default('Bangla');
            $table->string('category', 100)->nullable();
            $table->decimal('base_fee', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
        });

        Schema::create('leads_source', function (Blueprint $table) {
            $table->id('source_id');
            $table->enum('channel', ['fb', 'wp', 'googleads', 'gptads', 'googleform', 'reference', 'website'])->default('website');
            $table->string('campaign_name', 150)->nullable();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads_source');
        Schema::dropIfExists('courses');
    }
};
