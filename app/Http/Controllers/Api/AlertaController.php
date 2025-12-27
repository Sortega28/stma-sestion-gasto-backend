<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AlertaController extends Controller
{

    public function index(Request $request)
    {
        $anioActual = now()->year;

        // Paginación
        $page    = (int) $request->query('page', 1);
        $perPage = (int) $request->query('perPage', 10);
        $offset  = ($page - 1) * $perPage;

        // Filtros
        $proveedor   = $request->query('proveedor');
        $objeto      = $request->query('objeto');
        $tipo        = $request->query('tipo_alerta');
        $noRevisadas = $request->query('noRevisadas');

        // Subconsulta: gasto anual por proveedor y objeto
        $subGastoAnual = DB::table('solicitudgasto as sg')
            ->select(
                'sg.nombre_prov_final as proveedor',
                'sg.objeto',
                DB::raw('SUM(sg.importe) AS gasto_anual')
            )
            ->whereYear('sg.fechaalta', $anioActual)
            ->groupBy('sg.nombre_prov_final', 'sg.objeto');

        // Query principal
        $q = DB::table('alertas')
            ->where('alertas.anio', $anioActual)
            ->leftJoinSub($subGastoAnual, 'totales', function ($join) {
                $join->on('totales.proveedor', '=', 'alertas.proveedor')
                     ->on('totales.objeto', '=', 'alertas.objeto');
            });

        if (!empty($proveedor)) {
            $q->where('alertas.proveedor', 'ILIKE', "%{$proveedor}%");
        }

        if (!empty($objeto)) {
            $q->where('alertas.objeto', $objeto);
        }

        if (!empty($tipo)) {
            $q->where('alertas.tipo_alerta', $tipo);
        }

        if (!empty($noRevisadas)) {
            $q->where('alertas.revisada', false);
        }

        $q->select('alertas.*', 'totales.gasto_anual');

        $total = (clone $q)->count('alertas.id');

        $data = $q->orderBy('alertas.id', 'desc')
                  ->offset($offset)
                  ->limit($perPage)
                  ->get();

        return response()->json([
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ]);
    }

    // Marcar alerta como revisada
    public function updateEstado($id)
    {
        DB::table('alertas')
            ->where('id', $id)
            ->update([
                'revisada'   => true,
                'updated_at' => now(),
            ]);

        return response()->json(['ok' => true]);
    }

    //Regenerar alertas

    public function generarAlertas()
    {
        $anioActual = now()->year;

        // Eliminar alertas del año actual
        DB::table('alertas')
            ->where('anio', $anioActual)
            ->delete();

        // Calcular acumulados
        $acumulados = DB::table('solicitudgasto')
            ->select(
                'nombre_prov_final as proveedor',
                'objeto',
                DB::raw('SUM(importe) as total')
            )
            ->whereYear('fechaalta', $anioActual)
            ->groupBy('nombre_prov_final', 'objeto')
            ->get();

        $alertas = [];

        foreach ($acumulados as $a) {

            // Fraccionamiento (> 5.000 €)
            if ($a->total > 5000) {
                $alertas[] = $this->buildAlerta(
                    $a,
                    5000,
                    'FRACCIONAMIENTO',
                    $anioActual
                );
            }

            // Límite contrato
            $umbral = str_contains(strtolower($a->objeto), 'obra')
                ? 40000
                : 15000;

            if ($a->total > $umbral) {
                $alertas[] = $this->buildAlerta(
                    $a,
                    $umbral,
                    'LIMITE_CONTRATO',
                    $anioActual
                );
            }
        }

        // Inserción masiva
        if (!empty($alertas)) {
            DB::table('alertas')->insert($alertas);
        }

        return response()->json([
            'message' => 'Alertas regeneradas correctamente'
        ]);
    }

    //Constructor de alertas
    private function buildAlerta($a, $umbral, $tipo, $anio)
    {
        return [
            'proveedor'         => $a->proveedor,
            'objeto'            => $a->objeto,
            'importe'           => $a->total,
            'importe_acumulado' => $a->total,
            'umbral'            => $umbral,
            'tipo_alerta'       => $tipo,
            'anio'              => $anio,
            'revisada'          => false,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
