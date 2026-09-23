<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cdr_drugs')) {
            Schema::create('cdr_drugs', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('cdr_id')->nullable();
                $table->string('drug_name', 150)->nullable();
                $table->string('dose', 50)->nullable();
                $table->string('diluent', 100)->nullable();
                $table->string('volume', 50)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cdr_drugs');
    }
};