<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdeudoPrograma;
use App\Models\AdeudoSecundario;
use Carbon\Carbon;
use App\Models\Alerta;

class GenerarRecargos extends Command
{
    protected $signature = 'generar:recargos';
    protected $description = 'Genera recargos automáticos por adeudos vencidos';

    public function handle()
    {
        $hoy = Carbon::now()->toDateString();

        // Busca adeudos vencidos de programas
        $adeudos = AdeudoPrograma::whereDate('fecha_limite', '<', $hoy)->get();

        foreach ($adeudos as $adeudo) {
            // Calcula días vencidos
            $dias_vencidos = Carbon::parse($adeudo->fecha_limite)->diffInDays($hoy);

            // Por cada día vencido, verifica si ya existe recargo
            for ($i = 1; $i <= $dias_vencidos; $i++) {
                $fecha_recargo = Carbon::parse($adeudo->fecha_limite)->addDays($i)->toDateString();

                // Revisa si ya existe un recargo para ese día/alumno/periodo
                $existe = AdeudoSecundario::where('id_alumno', $adeudo->id_alumno)
                    ->where('concepto', 'RECARGO')
                    ->where('periodo', $adeudo->periodo)
                    ->where('corte', $fecha_recargo)
                    ->exists();

                if (!$existe) {
                    // Crea el recargo de $50
                    AdeudoSecundario::create([
                        'id_alumno' => $adeudo->id_alumno,
                        'concepto' => 'RECARGO',
                        'periodo' => $adeudo->periodo,
                        'monto' => 50,
                        'descuento' => 0,
                        'corte' => $fecha_recargo,
                    ]);

                    Alerta::create([
                    'id_alumno' => $adeudo->id_alumno,
                    'mensaje'   => "Se generó un recargo de $50 por atraso en el pago del periodo {$adeudo->periodo}.",
                    'tipo'      => 'recargo',
                    'leido'     => false,
                    ]);
                }

            }
        }

        $this->info('Recargos generados correctamente.');
    }
}
