<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Informe de Nómina</title>
  <style>
    * { font-family: Arial, Helvetica, sans-serif; }
    body { font-size: 12px; margin: 0; padding: 0; color:#111; }

    .wrapper { width: 850px; margin: 0 auto; padding: 24px 16px 60px; position: relative; }

    /* Logo esquina */
    .logo { position: absolute; left: 24px; top: 16px; width: 90px; z-index: 2; }

    /* Marca de agua (imagen grande al centro) */
    .watermark {
      position: absolute; left: 50%; top: 50px; transform: translateX(-50%);
      width: 420px; opacity: .12; z-index: 0;
    }

    .header { position: relative; z-index: 2; text-align: center; margin-top: 8px; }
    .title  { font-weight: bold; font-size: 18px; margin-bottom: 6px; letter-spacing:.3px; }
    .meta   { font-size: 12px; margin: 2px 0; }

    /* Tablas */
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border: 1px solid #000; padding: 6px; }
    th { background: #f2f2f2; text-transform: uppercase; font-size: 11px; }
    td.right { text-align: right; white-space: nowrap; }

    /* Totales: caja centrada */
    .totales-box { width: 360px; margin: 12px auto 24px auto; z-index: 2; }

    /* Bloques por maestro */
    .maestro { margin-top: 22px; z-index: 2; position: relative; }
    .maestro-nombre { font-weight: bold; font-size: 13px; margin-bottom: 6px; }

    /* Subtotales por maestro */
    .subtotales td { font-weight: bold; }

    /* Saltos de página para PDF si se alarga */
    .page-break { page-break-after: always; }

    /* Botón imprimir solo en web */
    @media print { .no-print { display: none; } }
  </style>
</head>
<body>
@php
  // Asegura bandera (por si no la mandan)
  $isPdf = $isPdf ?? false;

  // Rutas imágenes compatibles Web/DomPDF
  $logoSrc = $isPdf
    ? public_path('imagenes/mcdclogorecibo.png')
    : asset('imagenes/mcdclogorecibo.png');

  $wmSrc = $isPdf
    ? public_path('imagenes/marcaagua.png')
    : asset('imagenes/marcaagua.png');

  // Autor puede venir como $autor o $nombre
  $autorView = $autor ?? ($nombre ?? '—');
@endphp

<div class="wrapper">

  {{-- Imágenes --}}
  <img class="logo" src="{{ $logoSrc }}" alt="Logo">
  <img class="watermark" src="{{ $wmSrc }}" alt="Marca de agua">

  {{-- Encabezado --}}
  <div class="header">
    <div class="title">INFORME DE NÓMINA GENERAL</div>
    <div class="meta">AUTOR: {{ $autorView }}</div>
    <div class="meta">FECHA: {{ $fechanomina }}</div>
    <div class="meta">FOLIO: {{ $folio }}</div>
  </div>

  {{-- =======================
       TABLA DE TOTALES
     ======================= --}}
  <div class="totales-box">
    <table>
      <tbody>
        <tr>
          <th style="width:65%;">CLASES:</th>
          <td class="right" style="width:35%;">${{ $totals['mensualidades'] ?? '0.00' }}</td>
        </tr>
        <tr>
          <th>INSCRIPCIONES:</th>
          <td class="right">${{ $totals['inscripciones'] ?? '0.00' }}</td>
        </tr>
        <tr>
          <th>RECARGOS:</th>
          <td class="right">${{ $totals['recargos'] ?? '0.00' }}</td>
        </tr>
        <tr>
          <th>TOTAL:</th>
          <td class="right">${{ $totals['total'] ?? '0.00' }}</td>
        </tr>
        <tr>
          <th>COMISIONES:</th>
          <td class="right">${{ $totals['comisiones'] ?? '0.00' }}</td>
        </tr>
        <tr>
          <th>TOTAL NETO:</th>
          <td class="right">${{ $totals['total_neto'] ?? '0.00' }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- ===================================
       DESGLOSE POR MAESTRO (formato clásico)
     =================================== --}}
  @if(!empty($data))
    @foreach($data as $i => $bloque)
      <div class="maestro">
        <div class="maestro-nombre">{{ $bloque['nombre_maestro'] }}</div>

        <table>
          <thead>
            <tr>
              <th>CLASE (PROGRAMA)</th>
              <th style="width:80px; text-align:center;">TRANS.</th>
              <th style="width:140px;">INGRESOS</th>
              <th style="width:140px;">COMISIÓN</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bloque['clases'] as $c)
              <tr>
                <td>
                  {{ $c['nombre_clase'] }}
                  @if(!empty($c['nombre_programa']))
                    ({{ $c['nombre_programa'] }})
                  @endif
                </td>
                <td style="text-align:center;">{{ $c['transacciones'] }}</td>
                <td class="right">${{ $c['total'] }}</td>
                <td class="right">${{ $c['comision'] }}</td>
              </tr>
            @empty
              <tr><td colspan="4" style="text-align:center;">Sin clases registradas.</td></tr>
            @endforelse

            <tr class="subtotales">
              <td>SUBTOTAL ({{ $bloque['nombre_maestro'] }})</td>
              <td style="text-align:center;">—</td>
              <td class="right">${{ $bloque['totalgenerado'] }}</td>
              <td class="right">${{ $bloque['totalmaestro'] }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- Salto de página cada 3 maestros (ajústalo si necesitas) --}}
      @if( ($i+1) % 3 === 0 )
        <div class="page-break"></div>
      @endif
    @endforeach
  @else
    <p style="margin-top:18px;">Sin datos para esta nómina.</p>
  @endif

  <div class="no-print" style="margin-top: 28px;">
    <button onclick="window.print()">Imprimir</button>
  </div>
</div>
</body>
</html>
