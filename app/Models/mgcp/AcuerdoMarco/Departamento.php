<?php

namespace App\Models\mgcp\AcuerdoMarco;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'mgcp_acuerdo_marco.departamentos';
    public $timestamps = false;

    public static function obtenerPorDireccion($direccion)
    {
        //$direccion = "PLAZA PRINCIPAL DE SAN FRANCISCO, 4 DE OCTUBRE S/N AYACUCHO/LA MAR/AYNA";
        $direccionArray = explode("/", $direccion);
        $departamentoArray = explode(" ", $direccionArray[count($direccionArray) - 3]);
        $departamento = $departamentoArray[count($departamentoArray) - 1];
        if ($departamento == 'ICA') {
            return Departamento::find(11);
        } else {
            return Departamento::where('nombre', 'like', '%' . $departamento)->first();
        }
    }
}
