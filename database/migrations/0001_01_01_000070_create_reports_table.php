<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
    
            $table->date('report_date')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // optional
            $table->timestamps();
            
            // Add indexes for performance
            $table->index(['tenant_id']);
            $table->index(['tenant_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
}
