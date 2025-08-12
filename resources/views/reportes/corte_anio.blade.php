<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Resumen anual — {{ $anio }}</title>
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
    .totales { margin-top:10px; text-align:right; }
  </style>
</head>
<body>

  <div class="header">
    @if(!empty($logoPath)) <img class="logo" src="{{ $logoPath }}" alt="logo"> @endif
<<<<<<< HEAD
    <div class="h1">{{ $info->nombre ?? 'INSTITUCIÓN' }}</div>
=======
    <div class="h1">{{ $info->nombre ?? 'MAKING CHEER & DANCE CENTER' }}</div>
>>>>>>> cortes-consultas
    <div class="muted">RESUMEN ANUAL — {{ $anio }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Mes</th>
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @if(empty($rows))
        <tr><td colspan="3" class="empty">SIN CORTES REGISTRADOS</td></tr>
      @else
        @foreach($rows as $i => $r)
          <tr>
            <td class="right">{{ $i+1 }}</td>
            <td>{{ $r['nombre'] }}</td>
            <td class="right">${{ number_format($r['total'],2) }}</td>
          </tr>
        @endforeach
      @endif
    </tbody>
  </table>

  <div class="totales">
    <strong>Total anual:</strong> ${{ number_format($totalAnual ?? 0, 2) }}
  </div>

</body>
</html>
