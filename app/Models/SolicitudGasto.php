<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudGasto extends Model
{
    protected $table = 'solicitudgasto';
    protected $primaryKey = 'idsolicitudgasto';
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

    // Tipo gasto
    public function tipoGasto()
    {
        return $this->belongsTo(TipoGasto::class, 'idtipo_gasto', 'idtipo_gasto');
    }

    // Unidad
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad_fk', 'id_unidad_pk');
    }

    // Proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor_final', 'id');
    }

    // Estado solicitud
    public function estadoSolicitudGasto()
    {
        return $this->belongsTo(
            \App\Models\EstadoSolicitudGasto::class,
            'id_estado',
            'id_estado'
        );
    }
}
