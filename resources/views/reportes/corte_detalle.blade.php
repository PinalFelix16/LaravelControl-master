<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Corte #{{ $folio }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#222; }
    .header { text-align:center; margin-bottom: 10px; position: relative; }
    .logo { position:absolute; left:20px; top:0; height:60px; }
    .h1 { font-size: 18px; font-weight: bold; letter-spacing:.4px; }
    .muted { color:#555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border: 1px solid #ddd; padding: 6px; }
    th { background: #f2f2f2; text-transform: uppercase; font-size: 11px; }
    .right { text-align: right; }
    .empty { text-align:center; color:#666; }
    .grid { margin-top:8px; }
  </style>
</head>
<body>

  <div class="header">
    @if(!empty($logoPath ?? $logoData)) <img class="logo" src="{{ $logoPath ?? $logoData }}" alt="logo"> @endif
    <div class="h1">{{ $info->nombre ?? 'MAKING CHEER & DANCE CENTER' }}</div>
    <div class="muted">CORTE #{{ $folio }} — {{ $fechaLarga }}</div>
    <div class="muted">Autor: {{ $corte['autor'] ?? '-' }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Recibo</th>
        <th>Alumno</th>
        <th>Concepto</th>
        <th>Periodo</th>
        <th>Fecha</th>
        <th class="right">Monto</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $it)
        <tr>
          <td class="right">{{ $it['num'] }}</td>
          <td>{{ $it['recibo'] }}</td>
          <td>{{ $it['alumno'] }}</td>
          <td>{{ $it['concepto'] }}</td>
          <td>{{ $it['periodo'] }}</td>
          <td>{{ $it['fecha'] }}</td>
          <td class="right">${{ $it['monto'] }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="empty">SIN MOVIMIENTOS</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="grid">
    <strong>Total (DB):</strong> ${{ number_format($corte['total_db'] ?? 0, 2) }}<br>
    <strong>Total (recalculado):</strong> ${{ number_format($corte['total_recalculado'] ?? 0, 2) }}
  </div>

</body>
</html>
