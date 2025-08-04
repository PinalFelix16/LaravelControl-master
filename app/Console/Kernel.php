<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define los comandos de Artisan disponibles para la aplicación.
     */
    protected $commands = [
        Commands\GenerarRecargos::class, // <-- Registra aquí tu comando
    ];

    /**
     * Define la programación de comandos.
     */
    protected function schedule(Schedule $schedule)
    {
        // Aquí programas el comando automático, si lo deseas
        $schedule->command('generar:recargos')->dailyAt('00:05');
    }

    /**
     * Registra los comandos para la aplicación.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
