<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\AlertaController;

class GenerarAlertas extends Command
{
    /** Nombre del comando para ejecutar por consola.*/
    protected $signature = 'alertas:generar';

    /*Descripción del comando (para php artisan list).*/
    protected $description = 'Genera todas las alertas a partir de los datos de solicitudgasto';

    /**Lógica del comando*/
    public function handle()
    {
        $this->info('Generando alertas...');

        // Se usa el controlador, pero en un proceso separado del endpoint
        $controller = new AlertaController();
        $controller->generarAlertas();

        $this->info('Alertas generadas correctamente.');
        return 0;
    }
}
