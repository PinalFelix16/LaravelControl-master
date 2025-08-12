<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Resumen semanal — {{ $desde }} a {{ $hasta }}</title>
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
    .totales { margin-top: 10px; text-align: right; }
    .empty { text-align:center; color:#666; }
  </style>
</head>
<body>

  <div class="header">
    @if(!empty($logoPath)) <img class="logo" src="{{ $logoPath }}" alt="logo"> @endif
    <div class="h1">{{ $info->nombre ?? 'MAKING CHEER & DANCE CENTER' }}</div>
    <div class="muted">RESUMEN SEMANAL — {{ $desde }} a {{ $hasta }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Folio</th>
        <th>Fecha</th>
        <th>Autor</th>
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @if(empty($rows))
        <tr><td colspan="5" class="empty">NO HUBO CORTES EN EL RANGO</td></tr>
      @else
        @foreach($rows as $r)
          <tr>
            <td class="right">{{ $r['num'] }}</td>
            <td>{{ $r['folio'] }}</td>
            <td>{{ $r['fecha'] }}</td>
            <td>{{ $r['autor'] }}</td>
            <td class="right">${{ number_format($r['total'],2) }}</td>
          </tr>
        @endforeach
      @endif
    </tbody>
  </table>

  <div class="totales">
    <strong>Total semanal:</strong> ${{ number_format($total ?? 0, 2) }}
  </div>

</body>
</html>
