<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Only create the table if it doesn't exist (prevents deployment failures on existing DBs)
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->string('type')->index();       // revenue, expense, payroll, etc.
                $table->string('category')->nullable()->index();
                $table->decimal('amount', 14, 2)->default(0);
                $table->text('notes')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
                // $table->softDeletes(); // if you need soft deletes
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
