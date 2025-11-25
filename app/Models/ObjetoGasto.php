<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjetoGasto extends Model
{
    protected $table = 'm_objeto_gasto';
    protected $primaryKey = 'id_objeto';
    public $timestamps = false;
}
