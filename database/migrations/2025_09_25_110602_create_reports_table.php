<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->index();  // payroll, expenses, etc.
            $table->text('description')->nullable();
            $table->json('filters')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->string('file_path')->nullable(); // store generated report file
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
