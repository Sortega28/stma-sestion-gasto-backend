<?php

namespace App\Exports;

use App\Models\SolicitudGasto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SolicitudesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return SolicitudGasto::select(
            'idsolicitudgasto',
            'fechaalta',
            'usuario',
            'cif_prov_final',
            'nombre_prov_final',
            'importe',
            'id_estado'
        )
        ->orderBy('fechaalta', 'desc')
        ->limit(500)
        ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha Alta',
            'Usuario',
            'CIF Proveedor',
            'Nombre Proveedor',
            'Importe',
            'Estado'
        ];
    }
}
