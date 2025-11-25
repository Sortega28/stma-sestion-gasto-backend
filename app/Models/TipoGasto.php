<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGasto extends Model
{
    protected $table = 'tipos_gasto';
    protected $primaryKey = 'idtipo_gasto';
    public $timestamps = false;
}
