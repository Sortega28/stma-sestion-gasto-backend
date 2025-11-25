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

        // --- Paginación ---
        $page    = (int) $request->query('page', 1);
        $perPage = (int) $request->query('perPage', 10);
        $offset  = ($page - 1) * $perPage;

        // --- Filtros ---
        $proveedor   = $request->query('proveedor');
        $objeto      = $request->query('objeto');
        $tipo        = $request->query('tipo_alerta');
        $noRevisadas = $request->query('noRevisadas');

        /*gasto anual REAL por proveedor basado en la tabla "solicitudgasto" y su campo fechaalta*/
        $subGastoAnual = DB::table('solicitudgasto as sg')
            ->select(
                'sg.nombre_prov_final as proveedor',
                DB::raw('SUM(sg.importe) AS gasto_anual')
            )
            ->whereYear('sg.fechaalta', $anioActual)
            ->groupBy('sg.nombre_prov_final');

         // --- ALERTAS DEL AÑO ACTUAL basadas en la columna "anio" ---
        $q = DB::table('alertas')->where('alertas.anio', $anioActual);

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

        // JOIN con gasto anual real desde solicitudgasto
        $q->leftJoinSub($subGastoAnual, 'totales', function ($join) {
            $join->on('totales.proveedor', '=', 'alertas.proveedor');
        });

        // Selección final
        $q->select('alertas.*', 'totales.gasto_anual');

        // Total de registros
        $total = (clone $q)->count('alertas.id');

        // Datos paginados
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

    public function updateEstado($id, Request $request)
    {
        DB::table('alertas')->where('id', $id)->update([
            'revisada'   => true,
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**GENERAR ALERTAS del año actual usando solicitudgasto.fechaalta*/
    public function generarAlertas()
{
    $anioActual = now()->year;

    //  Eliminar alertas del año actual (evita duplicados)
    DB::table('alertas')
      ->where('anio', $anioActual)
      ->delete();

    // Recalcular gasto anual
    $acumulados = DB::table('solicitudgasto')
        ->select(
            'nombre_prov_final as proveedor',
            'objeto',
            DB::raw('SUM(importe) as total'),
            DB::raw('EXTRACT(YEAR FROM fechaalta)::int as anio')
        )
        ->whereYear('fechaalta', $anioActual)
        ->groupBy('nombre_prov_final', 'objeto', 'anio')
        ->get();

    foreach ($acumulados as $a) {

        // Solo crear alertas cuando supere los umbrales:

        // 5.000 € — Fraccionamiento
        if ($a->total > 5000) {
            $this->crearAlerta($a, 5000, 'FRACCIONAMIENTO');
        }

        // 15.000 € / 40.000 € — Límite contrato
        $umbral = str_contains(strtolower($a->objeto), 'obra') ? 40000 : 15000;

        if ($a->total > $umbral) {
            $this->crearAlerta($a, $umbral, 'LIMITE_CONTRATO');
        }
    }
}

    private function crearAlerta($a, $umbral, $tipo)
    {
        $exists = DB::table('alertas')
            ->where('proveedor', $a->proveedor)
            ->where('objeto', $a->objeto)
            ->where('umbral', $umbral)
            ->where('tipo_alerta', $tipo)
            ->where('anio', $a->anio)
            ->exists();

        if (!$exists) {
            DB::table('alertas')->insert([
                'proveedor'         => $a->proveedor,
                'objeto'            => $a->objeto,
                'importe'           => $a->total,
                'importe_acumulado' => $a->total,
                'umbral'            => $umbral,
                'tipo_alerta'       => $tipo,
                'anio'              => $a->anio,
                'revisada'          => false,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
