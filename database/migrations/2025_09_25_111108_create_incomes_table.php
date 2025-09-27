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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');  
            $table->string('invoice_number');
            $table->decimal('amount_received', 15, 2);
            $table->enum('payment_status', ['Paid', 'Pending','partially paid','Overdue'])->default('Pending'   );
             $table->decimal('amount_remaining', 15, 2);
            $table->date('received_at');        
            $table->text('notes')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
