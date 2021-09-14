<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Helpers\StringHelper;
use App\Model;
use App\Models\Logistica\Proveedor;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Proveedor::class, function (Faker $faker) {
    return [
        // 'id_contribuyente'=>
        // 'estado'=>
        // 'fecha_registro'=>
        // 'id_condicion_pago'=>
        // 'codigo'=>
        // 'observacion'=>

        // 'id_trabajador'=>factory(Trabajador::class)->create()->id_trabajador,
        // 'usuario'=>$faker->userName,
        // 'clave'=>StringHelper::encode5t($faker->password),
        // 'estado'=>1,
        // 'fecha_registro'=> new Carbon(),
        // 'acceso'=>1,
        // 'rol'=>null,
        // 'nombre_corto'=>$faker->userName,
        // 'codvend_softlink'=>null
    ];
});
