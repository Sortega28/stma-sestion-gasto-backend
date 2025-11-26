<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitudGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function resumen(Request $request)
    {
        try {

            // Total gasto acumulado
            $totalGasto = SolicitudGasto::sum('importe') ?? 0;

            // Gasto mensual actual
            $hoy = Carbon::now();
            $gastoMensual = SolicitudGasto::whereYear('fechaalta', $hoy->year)
                ->whereMonth('fechaalta', $hoy->month)
                ->sum('importe') ?? 0;

            // Órdenes activas
            $ordenesActivas = SolicitudGasto::whereIn('id_estado', [150, 160])->count() ?? 0;

            // Top proveedor 
            $topProveedor = SolicitudGasto::select(
                    'nombre_prov_final as nombre',
                    DB::raw('SUM(importe) as total')
                )
                ->groupBy('nombre_prov_final')
                ->orderByDesc('total')
                ->first();

            if (!$topProveedor) {
                $topProveedor = [
                    'nombre' => null,
                    'total'  => 0
                ];
            }

            // Ultimas órdenes
            $ultimasOrdenes = SolicitudGasto::with('estadoSolicitudGasto')
                ->orderByDesc('fechaalta')
                ->limit(5)
                ->get([
                    'idsolicitudgasto',
                    'fechaalta',
                    'usuario',
                    'cif_prov_final',
                    'nombre_prov_final',
                    'importe',
                    'id_estado'
                ])
                ->map(function ($o) {
                    return [
                        'idsolicitudgasto' => $o->idsolicitudgasto,
                        'fechaalta' => $o->fechaalta,
                        'usuario' => $o->usuario,
                        'cif_prov_final' => $o->cif_prov_final,
                        'nombre_prov_final' => $o->nombre_prov_final,
                        'importe' => $o->importe,
                        'estado' => [
                            'desc_corta' => $o->estadoSolicitudGasto?->desc_corta ?? ''
                        ]
                    ];
                });

            // Evolución mensual 
            $desde = Carbon::now()->subMonths(5)->startOfMonth();

            $evolucionMensual = SolicitudGasto::select(
                    DB::raw("TO_CHAR(fechaalta::timestamp, 'YYYY-MM') as mes"),
                    DB::raw("SUM(importe) as total")
                )
                ->where('fechaalta', '>=', $desde)
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->map(function ($row) {
                    return [
                        'mes' => $row->mes,
                        'total' => $row->total ?? 0
                    ];
                });

            return response()->json([
                'totalGasto'       => $totalGasto,
                'gastoMensual'     => $gastoMensual,
                'ordenesActivas'   => $ordenesActivas,
                'topProveedor'     => $topProveedor,
                'ultimasOrdenes'   => $ultimasOrdenes,
                'evolucionMensual' => $evolucionMensual,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error al obtener el resumen',
                'msg' => $e->getMessage() 
            ], 500);
        }
    }
}
