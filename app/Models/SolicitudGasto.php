<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudGasto extends Model
{
    protected $table = 'solicitudgasto';
    protected $primaryKey = 'idsolicitudgasto';
    public $incrementing = true;
    protected $keyType = 'int'; 
    public $timestamps = false;

    protected $fillable = [
        'cod_peticion',
        'fechaalta',
        'usuario',
        'codigo_proyecto',
        'idtipo_gasto',
        'cif_prov_final',
        'nombre_prov_final',
        'id_estado',
        'num_OG',
        'id_proveedor_final',
        'id_unidad_fk',
        'fecha_pago',
        'importe',
        'concepto',
        'id_proyecto_gpic_fk',
        'objeto'
    ];

    // Tipo de gasto
    public function tipoGasto()
    {
        return $this->belongsTo(
            \App\Models\TipoGasto::class,
            'idtipo_gasto',
            'idtipo_gasto'
        );
    }

    // Unidad
    public function unidad()
    {
        return $this->belongsTo(
            \App\Models\Unidad::class,
            'id_unidad_fk',
            'id_unidad_pk'
        );
    }

    // Proveedor
    public function proveedor()
    {
        return $this->belongsTo(
            \App\Models\Proveedor::class,
            'id_proveedor_final',
            'id'
        );
    }

    // Estado de solicitud
    public function estadoSolicitudGasto()
    {
        return $this->belongsTo(
            \App\Models\EstadoSolicitudGasto::class,
            'id_estado',
            'id_estado'
        );
    }
}
