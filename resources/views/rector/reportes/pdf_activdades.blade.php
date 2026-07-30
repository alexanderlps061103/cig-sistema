<!DOCTYPE html>
<html>
<head>
    <title>Reporte General de Actividades</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #2563eb; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SIACSACIG - Reporte de Verificación Académica</div>
        <div>Rectoría - {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de la Actividad</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Coordinador Responsable</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actividades as $act)
            <tr>
                <td>{{ $act->id }}</td>
                <td>{{ $act->nombre }}</td>
                <td>{{ $act->tipo->nombre ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($act->fecha_actividad)->format('d/m/Y') }}</td>
                <td>{{ $act->creador->nombres }} {{ $act->creador->apellidos }}</td>
                <td>{{ strtoupper($act->estado) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema SIACSACIG.
    </div>
</body>
</html>
