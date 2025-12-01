<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\AlertaController;

class GenerarAlertas extends Command
{

    protected $signature = 'alertas:generar';

    protected $description = 'Genera todas las alertas a partir de los datos de solicitudgasto';

    public function handle()
    {
        $this->info('Generando alertas...');

        $controller = new AlertaController();
        $controller->generarAlertas();

        $this->info('Alertas generadas correctamente.');
        return 0;
    }
}
