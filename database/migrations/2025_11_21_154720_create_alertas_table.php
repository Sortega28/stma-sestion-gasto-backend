<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();

            $table->string('proveedor');
            $table->string('objeto'); // Servicio / Suministro / Obra
            $table->decimal('importe', 12, 2); // total acumulado
            $table->decimal('umbral', 12, 2);  // 5000 / 15000 / 40000
            $table->string('tipo_alerta'); // "Supera contrato menor", etc.

            $table->boolean('revisada')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
