<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Rrhh\Persona;
use App\Models\Rrhh\Postulante;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Postulante::class, function (Faker $faker) {
    return [
        'id_persona' => factory(Persona::class)->create()->id_persona,
        'direccion' => $faker->address,
        'telefono' => $faker->numerify('#######'),
        'correo' => $faker->email,
        'brevette' => $faker->numerify('#######'),
        'id_pais' => 170,
        'ubigeo' => $faker->numerify('###'),
        'fecha_registro' => new Carbon()
    ];
});
