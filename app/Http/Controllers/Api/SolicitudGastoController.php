<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitudGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudGastoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $perPage = $request->query('perPage', 10);

            $conceptos = $request->query('conceptos');
            $texto = $request->query('texto');

            $conceptos = $conceptos ? explode(',', $conceptos) : [];

            $mapa = [
                'cif'         => 'cif_prov_final',
                'proveedor'   => 'nombre_prov_final',
                'importe'     => 'importe',
                'estado'      => 'estado',
                'solicitante' => 'usuario',
                'objeto'      => 'objeto'
            ];

            $conceptos = array_map(fn($c) => $mapa[$c] ?? $c, $conceptos);

            $query = SolicitudGasto::select(
                'idsolicitudgasto',
                'nombre_prov_final',
                'cif_prov_final',
                'importe',
                'usuario',
                'objeto',
                'id_estado',
            )
            ->with([
                'estadoSolicitudGasto:id_estado,desc_corta'
            ]);

            if ($texto && count($conceptos) > 0) {
                $query->where(function ($q) use ($conceptos, $texto) {
                    foreach ($conceptos as $campo) {
                        if ($campo === 'estado') {
                            $q->orWhereHas('estadoSolicitudGasto', function ($estado) use ($texto) {
                                $estado->where('desc_corta', 'ILIKE', "%$texto%");
                            });
                            continue;
                        }
                        $q->orWhere($campo, 'ILIKE', "%$texto%");
                    }
                });
            }

            $data = $query->orderBy('idsolicitudgasto', 'DESC')
                          ->paginate($perPage, ['*'], 'page', $page);

            foreach ($data as $item) {
                $item->estado_nombre = $item->estadoSolicitudGasto->desc_corta ?? null;
            }

            return response()->json([
                'data'    => $data->items(),
                'total'   => $data->total(),
                'page'    => $data->currentPage(),
                'perPage' => $data->perPage()
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error en /solicitudes: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    public function show($id)
{
    try {
        $solicitud = SolicitudGasto::with([
            'estadoSolicitudGasto:id_estado,desc_corta',
            'proveedor:id,desccorta',
            'tipoGasto:idtipo_gasto,desccorta',
            'unidad:id_unidad_pk,descripcion'
        ])
        ->findOrFail($id);

        $solicitud->estado_nombre = $solicitud->estadoSolicitudGasto->desc_corta ?? null;

        return response()->json($solicitud);

    } catch (\Throwable $e) {
        \Log::error("Error en GET /solicitudes/$id: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 404);
    }
}



    public function update(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['auditor', 'admin'])) {
            return response()->json(['error' => 'No tienes permisos para editar solicitudes'], 403);
        }

        try {
            $solicitud = SolicitudGasto::findOrFail($id);
            $solicitud->update($request->all());

            return response()->json([
                'message' => 'Solicitud actualizada correctamente',
                'data' => $solicitud
            ]);

        } catch (\Throwable $e) {
            \Log::error("Error en PUT /solicitudes/$id: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    public function validarMasivo(Request $request)
    {
        if (!in_array(auth()->user()->role, ['auditor', 'admin'])) {
            return response()->json(['error' => 'No tienes permisos para validar solicitudes'], 403);
        }

        try {
            $ids = $request->input('ids');
            $estadoNuevo = $request->input('estado');

            DB::table('solicitudgasto')
                ->whereIn('idsolicitudgasto', $ids)
                ->update(['id_estado' => $estadoNuevo]);

            return response()->json([
                'message' => 'Solicitudes actualizadas correctamente',
                'ids' => $ids,
                'nuevo_estado' => $estadoNuevo
            ]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function rechazarMasivo(Request $request)
    {
        if (!in_array(auth()->user()->role, ['auditor', 'admin'])) {
            return response()->json(['error' => 'No tienes permisos para rechazar solicitudes'], 403);
        }

        try {
            $ids = $request->input('ids');

            DB::table('solicitudgasto')
                ->whereIn('idsolicitudgasto', $ids)
                ->update(['id_estado' => 170]);

            return response()->json([
                'message' => 'Solicitudes rechazadas correctamente',
                'ids' => $ids
            ]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'codigo_proyecto'     => 'nullable|string|max:255',
                'idtipo_gasto'        => 'nullable|integer',
                'cif_prov_final'      => 'required|string|max:50',
                'nombre_prov_final'   => 'required|string|max:255',
                'id_estado'           => 'nullable|integer',
                'id_proveedor_final'  => 'nullable|integer',
                'id_unidad_fk'        => 'nullable|integer',
                'fecha_pago'          => 'nullable|date',
                'importe'             => 'required|numeric|min:0',
                'concepto'            => 'nullable|string|max:500',
                'id_proyecto_gpic_fk' => 'nullable|integer',
                'objeto'              => 'required|string|max:500'
            ]);

            $validated['fechaalta'] = now();  
            $validated['usuario'] = auth()->user()->name ?? 'Sistema';
            $validated['id_estado'] = $validated['id_estado'] ?? 1;
            $validated['cod_peticion'] = 'SG-' . time();

            $solicitud = SolicitudGasto::create($validated);

            return response()->json([
                'message' => 'Solicitud de gasto creada correctamente',
                'data' => $solicitud
            ], 201);

        } catch (\Throwable $e) {
            \Log::error("Error en POST /solicitudes: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
