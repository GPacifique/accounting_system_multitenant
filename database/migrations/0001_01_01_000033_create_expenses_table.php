<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
                $table->date('date');
                $table->string('category'); // e.g. equipment, utilities, marketing, staff_salary, maintenance
                $table->text('description');
                $table->decimal('amount', 12, 2);
                $table->string('payment_method')->nullable(); // e.g. cash, card, bank_transfer
                $table->string('vendor')->nullable(); // Who was paid
                $table->string('receipt_number')->nullable();
                $table->string('status')->default('paid'); // paid, pending, cancelled
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who recorded the expense
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['tenant_id', 'category']);
                $table->index(['tenant_id', 'date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
