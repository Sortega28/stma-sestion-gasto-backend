<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Solicitudes</title>

    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid #ccc;
            padding: 4px;
            text-align: left;
        }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Reporte de Solicitudes de Gasto</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Proveedor</th>
            <th>Importe</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($solicitudes as $s)
        <tr>
            <td>{{ $s->idsolicitudgasto }}</td>
            <td>{{ $s->fechaalta }}</td>
            <td>{{ $s->usuario }}</td>
            <td>{{ $s->nombre_prov_final }}</td>
            <td>{{ number_format($s->importe, 2) }} €</td>
            <td>{{ $s->estadoSolicitudGasto->desc_corta ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
