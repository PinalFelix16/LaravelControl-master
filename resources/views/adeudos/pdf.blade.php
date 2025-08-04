<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Adeudos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #888; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Reporte de Adeudos</h2>
    <table>
        <thead>
            <tr>
                <th>ID Alumno</th>
                <th>Nombre</th>
                <th>Periodo</th>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Recargos</th>
                <th>Total a Pagar</th>
                <th>Fecha Límite</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($adeudos as $a)
                <tr>
                    <td>{{ $a->id_alumno }}</td>
                    <td>{{ $a->alumno->nombre ?? '' }} {{ $a->alumno->apellido ?? '' }}</td>
                    <td>{{ $a->periodo }}</td>
                    <td>{{ $a->concepto }}</td>
                    <td>${{ number_format($a->monto, 2) }}</td>
                    <td>${{ number_format($a->total_recargos ?? 0, 2) }}</td>
                    <td>${{ number_format($a->total_a_pagar ?? $a->monto, 2) }}</td>
                    <td>{{ $a->fecha_limite }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
