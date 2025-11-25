<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('alertas', function (Blueprint $table) {
        $table->decimal('importe_acumulado', 12, 2)->default(0);
    });
}

public function down()
{
    Schema::table('alertas', function (Blueprint $table) {
        $table->dropColumn('importe_acumulado');
    });
}

};
