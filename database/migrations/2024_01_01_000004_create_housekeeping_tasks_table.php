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
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('task_type', ['cleaning', 'maintenance', 'inspection', 'inventory'])->comment('Type of housekeeping task');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->comment('Task priority level');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->comment('Task status');
            $table->text('description')->nullable()->comment('Task description and notes');
            $table->timestamp('scheduled_at')->nullable()->comment('Scheduled completion time');
            $table->timestamp('started_at')->nullable()->comment('Actual start time');
            $table->timestamp('completed_at')->nullable()->comment('Actual completion time');
            $table->text('completion_notes')->nullable()->comment('Notes upon task completion');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('room_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('task_type');
            $table->index('priority');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_tasks');
    }
};