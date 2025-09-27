<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
public function up()
{
Schema::create('workers', function (Blueprint $table) {
$table->id();
$table->string('first_name', 100);
$table->string('last_name', 100);
$table->string('email')->nullable()->unique();
$table->string('phone', 30)->nullable();
$table->string('position', 100)->nullable();
$table->bigInteger('salary_cents')->nullable();
$table->string('currency', 3)->nullable()->default('USD');
$table->timestamp('hired_at')->nullable();
$table->string('status', 50)->nullable()->default('active');
$table->text('notes')->nullable();
$table->timestamps();
$table->softDeletes();


$table->index(['last_name', 'position']);
});
}


public function down()
{
Schema::dropIfExists('workers');
}
};