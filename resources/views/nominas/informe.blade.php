<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Informe de Nómina</title>
  <style>
    * { font-family: Arial, Helvetica, sans-serif; }
    html, body { margin: 0; padding: 0; }
    body { font-size: 12px; color:#000; }

    /* Margen de impresión */
    @page { margin: 20mm 15mm; }

    /* Contenedor centrado y ancho fijo */
    .page {
      width: 700px;   /* ~ 18 cm */
      margin: 0 auto;
      position: relative;
    }

    /*.marca-agua {
            position: absolute;
            top: 100px; /* 🔹 Ajusta para bajarla */
            /*left: 50%;
            transform: translateX(-50%);
            opacity: 0.1;
            width: 400px;
            z-index: -1;
        }*/

    /* Header */
    .header { display:flex; align-items:center; margin-top: 8px; }
    .logo { width: 90px; }
    .header-center { flex:1; text-align:center; }
    .title { font-weight:bold; font-size:18px; letter-spacing:.5px; }
    .meta  { margin-top: 4px; font-size: 12px; }

    /* Marca de agua: dos versiones (web/pdf) */
    .wm {
      position: absolute; top: 45%; left: 50%;
      transform: translate(-50%, -50%);
      opacity: 0.06; z-index: 0;
      width: 420px;
      pointer-events: none;
    }
    .wm-web { display:block; }
    .wm-pdf { display:none; }
    .logo-web { display:block; }
    .logo-pdf { display:none; }

    /* Tablas */
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    th, td { border:1px solid #000; padding:6px 8px; }
    th { background:#efefef; text-transform:uppercase; font-size:11px; }
    .right { text-align:right; }

    /* Bloque por maestro */
    .maestro { margin-top:18px; font-weight:bold; font-size:13px; }

    /* Totales a la derecha, caja compacta */
    .totales-wrap { display:flex; justify-content:flex-end; margin-top: 14px; }
    .totales { width: 320px; }
    .totales td { font-weight:bold; }
    .label { width: 55%; }
    .value { width: 45%; }

    /* Botón imprimir sólo en pantalla */
    .no-print { margin-top: 20px; }
    @media print {
      .no-print { display:none; }
      .wm { opacity: 0.08; }
    }

    /* Para DOMPDF (interpreta como "print"): mostrar imágenes por ruta absoluta */
    /* Truco: cuando DomPDF renderiza, suele ignorar asset(); usamos duplicadas */
    .pdf-mode .logo-web, .pdf-mode .wm-web { display:none !important; }
    .pdf-mode .logo-pdf, .pdf-mode .wm-pdf { display:block !important; }
  </style>
</head>
{{-- agregamos una clase condicional "pdf-mode" si viene desde el PDF --}}
<body class="{{ request()->is('api/nominas/*/informe/pdf') ? 'pdf-mode' : '' }}">
  <div class="page">
    {{-- Marca de agua (web) --}}
    <img class="wm wm-web" src="{{ asset('imagenes/mcdclogorecibo.png') }}" alt="Marca de agua">
    {{-- Marca de agua (pdf/dompdf) --}}
    <img class="wm wm-pdf" src="{{ public_path('imagenes/mcdclogorecibo.png') }}" alt="Marca de agua">

    <div class="header">
      {{-- Logo (web) --}}
      <img class="logo logo-web" src="{{ asset('imagenes/mcdclogorecibo.png') }}" alt="Logo">
      {{-- Logo (pdf/dompdf) --}}
      <img class="logo logo-pdf" src="{{ public_path('imagenes/mcdclogorecibo.png') }}" alt="Logo">

      <div class="header-center">
        <div class="title">INFORME DE NÓMINA GENERAL</div>
        <div class="meta">AUTOR: {{ $autor }}</div>
        <div class="meta">FECHA: {{ $fechanomina }}</div>
        <div class="meta">FOLIO: {{ $folio }}</div>
      </div>
    </div>

    {{-- Bloques por maestro (si no hay registros, esta sección no aparece) --}}
    @foreach($data as $bloque)
      <div class="maestro">{{ $bloque['nombre_maestro'] }}</div>
      <table>
        <thead>
        <tr>
          <th>Clase (Programa)</th>
          <th class="right">Total</th>
          <th class="right">Comisión</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bloque['clases'] as $c)
          <tr>
            <td>{{ $c['nombre_clase'] }}</td>
            <td class="right">${{ $c['total'] }}</td>
            <td class="right">${{ $c['comision'] }}</td>
          </tr>
        @endforeach
        <tr>
          <td style="font-weight:bold">TOTAL:</td>
          <td class="right" style="font-weight:bold">${{ $bloque['totalgenerado'] }}</td>
          <td class="right" style="font-weight:bold">${{ $bloque['totalmaestro'] }}</td>
        </tr>
        </tbody>
      </table>
    @endforeach

    {{-- Totales generales a la derecha --}}
    <div class="totales-wrap">
      <table class="totales">
        <tbody>
          <tr><td class="label">CLASES:</td><td class="value right">${{ $totals['mensualidades'] }}</td></tr>
          <tr><td class="label">INSCRIPCIONES:</td><td class="value right">${{ $totals['inscripciones'] }}</td></tr>
          <tr><td class="label">RECARGOS:</td><td class="value right">${{ $totals['recargos'] }}</td></tr>
          <tr><td class="label">TOTAL:</td><td class="value right">${{ $totals['total'] }}</td></tr>
          <tr><td class="label">COMISIONES:</td><td class="value right">${{ $totals['comisiones'] }}</td></tr>
          <tr><td class="label">TOTAL NETO:</td><td class="value right">${{ $totals['total_neto'] }}</td></tr>
        </tbody>
      </table>
    </div>

    <div class="no-print">
      <button onclick="window.print()">Imprimir</button>
    </div>
  </div>
</body>
</html>
