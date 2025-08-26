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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique()->comment('Unique room identifier');
            $table->enum('type', ['single', 'double', 'suite', 'deluxe', 'presidential'])->comment('Room type category');
            $table->decimal('price_per_night', 8, 2)->comment('Nightly rate in currency');
            $table->enum('status', ['available', 'occupied', 'maintenance', 'cleaning'])->default('available')->comment('Current room status');
            $table->text('amenities')->nullable()->comment('JSON string of room amenities');
            $table->integer('capacity')->default(1)->comment('Maximum occupancy');
            $table->text('description')->nullable()->comment('Room description and features');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('room_number');
            $table->index('type');
            $table->index('status');
            $table->index(['status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};