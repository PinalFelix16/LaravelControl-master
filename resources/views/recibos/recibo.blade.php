<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; margin: 30px; }
        .header { text-align: center; }
        .detalle { margin-top: 20px; }
        .detalle th, .detalle td { text-align: left; padding: 8px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; }
        .box { border: 1px solid #000; padding: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Recibo de Pago</h2>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>
    </div>

    <div class="box">
        <p><strong>Alumno:</strong> {{ $pago->alumno->nombre }} {{ $pago->alumno->apellido }}</p>
        <p><strong>Concepto:</strong> {{ $pago->concepto }}</p>
        <p><strong>Monto:</strong> ${{ number_format($pago->monto, 2) }}</p>
        <p><strong>Forma de Pago:</strong> {{ $pago->forma_pago ?? 'N/A' }}</p>
        <p><strong>Referencia:</strong> {{ $pago->referencia ?? 'N/A' }}</p>
    </div>

    <div class="footer">
        <p>Gracias por su pago.</p>
    </div>
</body>
</html>
