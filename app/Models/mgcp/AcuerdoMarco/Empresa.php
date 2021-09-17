<?php

namespace App\Models\mgcp\AcuerdoMarco;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'mgcp_acuerdo_marco.empresas';
    public $timestamps = false;
    protected $appends = ['semaforo'];
    protected $hidden = array('ruc', 'password','usuario2','password2');

    public function getSemaforoAttribute()
    {
        switch ($this->attributes['indicador_semaforo']) {
            case '0':
                $color = 'green';
                break;
            case '1':
                $color = 'yellow';
                break;
            case '2':
                $color = 'orange';
                break;
            case '3':
                $color = 'red';
                break;
            default:
                $color = 'silver';
                break;
        }
        return '<i style="color: ' . $color . '" class="fa fa-circle" aria-hidden="true"></i>';
    }
}
