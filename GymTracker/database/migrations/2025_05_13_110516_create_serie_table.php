<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Serie', function (Blueprint $table) {
            $table->id('id_serie');
            $table->foreignId('id_sesion')->constrained('Sesion', 'id_sesion')->onDelete('cascade');
            $table->foreignId('id_ejercicio')->constrained('Ejercicio', 'id_ejercicio')->onDelete('cascade');
            $table->integer('serie_num');
            $table->integer('repeticiones');
            $table->decimal('peso', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Serie');
    }
};
