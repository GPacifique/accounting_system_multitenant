<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
                $table->string('type'); // financial, membership, attendance, trainer_performance, etc.
                $table->string('title');
                $table->text('content')->nullable();
                $table->json('data')->nullable(); // Store report data as JSON
                $table->date('report_date');
                $table->date('period_start')->nullable(); // For reports covering a period
                $table->date('period_end')->nullable();
                $table->string('status')->default('draft'); // draft, final, archived
                $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                
                // Add indexes for performance
                $table->index(['tenant_id', 'type']);
                $table->index(['tenant_id', 'report_date']);
                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
}
