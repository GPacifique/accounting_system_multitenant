<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create the table if it doesn't exist
        if (!Schema::hasTable('worker_payments')) {
            Schema::create('worker_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
                $table->date('paid_on');
                $table->decimal('amount', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['worker_id', 'paid_on']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_payments');
    }
};
