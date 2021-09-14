<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Helpers\StringHelper;
use App\Model;
use App\Models\Configuracion\Usuario;
use App\Models\Rrhh\Trabajador;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Usuario::class, function (Faker $faker) {
    return [
        'id_trabajador'=>factory(Trabajador::class)->create()->id_trabajador,
        'usuario'=>$faker->userName,
        'clave'=>StringHelper::encode5t($faker->password),
        'estado'=>1,
        'fecha_registro'=> new Carbon(),
        'acceso'=>1,
        'rol'=>null,
        'nombre_corto'=>$faker->userName,
        'codvend_softlink'=>null
    ];
});
