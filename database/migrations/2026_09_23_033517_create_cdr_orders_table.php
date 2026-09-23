<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cdr_orders')) {
            Schema::create('cdr_orders', function (Blueprint $table) {
                $table->integer('cdr_orderid')->autoIncrement();
                $table->integer('order_id')->nullable(); // Foreign key mapping to cdr.cdr_id
                $table->date('date_order')->nullable();
                $table->date('date_use')->nullable();
                $table->string('ward', 50)->nullable();
                $table->string('name', 150)->nullable();
                $table->string('mrn', 50)->nullable();
                $table->string('orderstatus', 50)->default('ORDER RECEIVED');
                $table->string('collectedby', 150)->nullable();
                $table->integer('collectedbyid')->nullable();
                $table->string('collected', 100)->nullable();
                $table->string('remarks', 250)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cdr_orders');
    }
};