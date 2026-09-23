<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cdr')) {
            Schema::create('cdr', function (Blueprint $table) {
                $table->integer('cdr_id')->autoIncrement();
                $table->date('date_order')->nullable();
                $table->date('date_expected')->nullable();
                $table->string('name', 150)->nullable();
                $table->string('mrn', 50)->nullable();
                $table->string('ward', 50)->nullable();
                $table->integer('age')->nullable();
                $table->string('sex', 10)->nullable();
                $table->integer('weight')->nullable();
                $table->integer('height')->nullable();
                $table->string('bsa', 10)->nullable();
                $table->string('diagnosis', 150)->nullable();
                $table->string('protocol', 150)->nullable();
                $table->integer('length')->nullable();
                $table->integer('rotation')->nullable();
                $table->string('orderedby', 150)->nullable();
                $table->string('orderbydetails', 250)->nullable();
                $table->integer('orderedbyid')->nullable();
                $table->timestamp('ordered')->useCurrent()->nullable();
                $table->string('nota', 200)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cdr');
    }
};