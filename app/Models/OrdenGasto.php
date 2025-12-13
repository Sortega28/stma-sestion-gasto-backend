<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenGasto extends Model
{
    protected $table = 'solicitudgasto';
    protected $primaryKey = 'idsolicitudgasto';
    public $timestamps = false;

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor_final', 'id')
                    ->select('id', 'desccorta')
                    ->withDefault([
                        'desccorta' => '(Sin proveedor)'
                    ]);
    }
}
