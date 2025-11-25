<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ejecuta la generación de alertas cada día a las 03:00 AM para mejorar el rendimiento de la app
Schedule::command('alertas:generar')->dailyAt('03:00');


