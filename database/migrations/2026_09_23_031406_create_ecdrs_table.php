<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecdrs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->string('patient_name', 100);
            $table->string('mrn', 50);
            $table->string('ward', 20);
            $table->string('bed', 20)->default('-');
            $table->string('drug_name', 150);
            $table->string('dose', 50);
            $table->string('diluent', 100)->default('-');
            $table->string('volume', 50)->default('-');
            $table->string('status', 35)->default('PREPARATION');
            $table->string('prepared_by', 100)->default('-');
            $table->string('checked_by', 100)->default('-');
            $table->string('remarks', 100)->default('-');
            $table->time('statusready')->default('00:00:00');
            $table->time('statusdispensed')->default('00:00:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecdrs');
    }
};