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
        Schema::create('Ejercicio', function (Blueprint $table) {
            $table->id('id_ejercicio');
            $table->string('nombre', 100);
            $table->enum('grupo_muscular', ['pecho', 'espalda', 'piernas', 'hombros', 'brazos', 'core', 'otros']);
            $table->text('descripcion')->nullable();
            $table->string('imagen_demo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Ejercicio');
    }
};
