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
        // Modificar la tabla historial_ofertas
        Schema::table('historial_ofertas', function (Blueprint $table) {
            // Cambiar el campo 'estado' para incluir 'completada'
            $table->enum('estado', ['aceptada', 'rechazada', 'completada'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el cambio (opcional)
        Schema::table('historial_ofertas', function (Blueprint $table) {
            $table->enum('estado', ['aceptada', 'rechazada'])->change();
        });
    }
};