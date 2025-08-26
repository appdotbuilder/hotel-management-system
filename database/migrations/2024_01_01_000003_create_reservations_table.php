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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('confirmation_number')->unique()->comment('Unique booking confirmation code');
            $table->foreignId('guest_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('check_in_date')->comment('Scheduled check-in date');
            $table->date('check_out_date')->comment('Scheduled check-out date');
            $table->integer('adults')->default(1)->comment('Number of adult guests');
            $table->integer('children')->default(0)->comment('Number of child guests');
            $table->decimal('total_amount', 10, 2)->comment('Total booking amount');
            $table->enum('status', ['confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('confirmed')->comment('Reservation status');
            $table->text('special_requests')->nullable()->comment('Guest special requests');
            $table->timestamp('checked_in_at')->nullable()->comment('Actual check-in timestamp');
            $table->timestamp('checked_out_at')->nullable()->comment('Actual check-out timestamp');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('confirmation_number');
            $table->index('guest_id');
            $table->index('room_id');
            $table->index('status');
            $table->index('check_in_date');
            $table->index('check_out_date');
            $table->index(['check_in_date', 'check_out_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};