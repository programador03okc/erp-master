<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Rrhh\Postulante;
use App\Models\Rrhh\Trabajador;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Trabajador::class, function (Faker $faker) {
    return [
        'id_postulante'=> factory(Postulante::class)->create()->id_postulante,
        'id_tipo_trabajador'=> 1,
        'id_categoria_ocupacional'=> 3,
        'id_tipo_planilla'=> 1,
        'condicion'=> 'NUEVO',
        'hijos'=> $faker->numerify('#'),
        'id_pension'=> 5,
        'cuspp'=> $faker->numerify('#######'),
        'seguro'=> $faker->numerify('#'),
        'confianza'=> true,
        'archivo_adjunto'=> null,
        'estado'=> 1,
        'fecha_registro'=> new Carbon()
    ];
});
