<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitudGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SolicitudesExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    //CONSULTA BASICA DE REPORTES (JSON)
    public function index(Request $request)
    {
        try {
            $desde     = $request->query('desde');
            $hasta     = $request->query('hasta');
            $estado    = $request->query('estado');
            $proveedor = $request->query('proveedor');
            $objeto    = $request->query('objeto');
            $unidad    = $request->query('unidad');

            $query = SolicitudGasto::with([
                'estadoSolicitudGasto',
                'proveedor',
                'unidad'
            ]);

            if ($desde) {
                $query->where('fechaalta', '>=', $desde);
            }

            if ($hasta) {
                $query->where('fechaalta', '<=', $hasta . ' 23:59:59');
            }

            if ($estado) {
                $query->where('id_estado', $estado);
            }

            if ($unidad) {
                $query->where('id_unidad_fk', $unidad);
            }

            if ($objeto) {
                $query->where('objeto', $objeto);
            }

            if ($proveedor) {
                $query->where('nombre_prov_final', 'ILIKE', "%$proveedor%");
            }

            $resultados = $query
                ->orderBy('fechaalta', 'desc')
                ->limit(500)
                ->get();

            return response()->json([
                'ok' => true,
                'total' => $resultados->count(),
                'data' => $resultados
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // EXPORTAR A PDF
    public function exportPdf()
    {
        $solicitudes = SolicitudGasto::with(['proveedor', 'unidad', 'estadoSolicitudGasto'])
            ->limit(200)
            ->get();

        $pdf = Pdf::loadView('reports.solicitudes', compact('solicitudes'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('solicitudes_gasto.pdf');
    }


    // EXPORTAR A EXCEL
    public function exportExcel()
    {
        return Excel::download(new SolicitudesExport, 'solicitudes_gasto.xlsx');
    }
}
