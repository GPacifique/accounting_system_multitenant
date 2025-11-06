<?php
// database/migrations/2025_01_01_000000_create_expenses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            
            $table->string('category')->nullable(); // e.g. travel, supplies
            $table->text('description')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('method')->nullable();      // e.g. cash, card
            $table->string('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // optional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
