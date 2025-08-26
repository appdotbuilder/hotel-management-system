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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->comment('Guest first name');
            $table->string('last_name')->comment('Guest last name');
            $table->string('email')->unique()->comment('Guest email address');
            $table->string('phone')->nullable()->comment('Contact phone number');
            $table->text('address')->nullable()->comment('Guest address');
            $table->string('id_number')->nullable()->comment('Government ID number');
            $table->date('date_of_birth')->nullable()->comment('Guest date of birth');
            $table->enum('id_type', ['passport', 'drivers_license', 'national_id', 'other'])->nullable()->comment('Type of identification');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('email');
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};