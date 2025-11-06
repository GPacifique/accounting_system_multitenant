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
        Schema::table('tasks', function (Blueprint $table) {
            // Change estimated_hours and actual_hours from integer to decimal
            $table->decimal('estimated_hours', 8, 2)->nullable()->change();
            $table->decimal('actual_hours', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert back to integer columns
            $table->integer('estimated_hours')->nullable()->change();
            $table->integer('actual_hours')->nullable()->change();
        });
    }
};
