<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Rrhh\Persona;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Persona::class, function (Faker $faker) {
    return [
        'id_documento_identidad'=>1,
        'nro_documento'=>$faker->numerify('########'), 
        'nombres'=> $faker->name,
        'apellido_paterno'=>$faker->lastName,
        'apellido_materno'=> $faker->lastName,
        'fecha_nacimiento'=> $faker->date(),
        'sexo'=>$faker->randomElement(['F','M']),
        'id_estado_civil'=>1,
        'estado'=>1,
        'fecha_registro'=>new Carbon(),
        'telefono'=>$faker->numerify('#######'),
        'direccion'=>$faker->address,
        'email'=>$faker->email
    ];
});
