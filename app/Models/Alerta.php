<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas';

    protected $fillable = [
        'proveedor',
        'objeto',
        'importe',
        'importe_acumulado',
        'umbral',
        'tipo_alerta',
        'revisada'
    ];
}
