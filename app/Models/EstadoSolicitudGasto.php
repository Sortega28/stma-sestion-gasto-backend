<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoSolicitudGasto extends Model
{
    
    protected $table = 'm_estados_solicitud_gasto';

    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = ['desccorta', 'desclarga'];
}
