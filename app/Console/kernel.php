<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * You can either list commands here or use $this->load in the boot method.
     *
     * @var array
     */
    protected $commands = [
        // Si prefieres listar los comandos explícitamente puedes hacerlo aquí:
        // \App\Console\Commands\GenerateCertificatesCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Cargar todos los comandos en app/Console/Commands automáticamente
        $this->load(__DIR__.'/Commands');

        // Ejecutar el command cada 10 minutos (ajusta según necesites)
        $schedule->command('certificados:generar --hours=24')->everyTenMinutes();
    }

    protected function commands()
    {
        // Cargar rutas de comandos si las tienes definidas
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
