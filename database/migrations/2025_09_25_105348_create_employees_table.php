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
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // ID (auto-increment primary key)
            $table->string('first_name'); // First Name
            $table->string('last_name');  // Last Name
            $table->string('email')->unique(); // Email
            $table->string('phone')->nullable(); // Phone
            $table->string('position')->nullable(); // Position
            $table->decimal('salary', 10, 2)->default(0); // Salary
            $table->date('date_of_joining')->nullable(); // Date of Joining
            $table->string('department')->nullable(); // Department
            $table->timestamps(); // Created At & Updated At
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
