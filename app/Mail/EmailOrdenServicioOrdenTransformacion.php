<?php

namespace App\Mail;

use App\Models\almacen\Transformacion;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\Oportunidad\Oportunidad;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOrdenServicioOrdenTransformacion extends Mailable
{
    use Queueable, SerializesModels;

    public $oportunidad;
    public $mensaje;
    public $logo_empresa;
    public $codigo;

    public $piePagina;
     // public $archivos;


    public function __construct($oportunidad,$logoEmpresa,$codigoTransformacion)
    {
        $this->oportunidad = $oportunidad;
        $this->logo_empresa = $logoEmpresa;
        $this->codigo = $codigoTransformacion;
        $this->piePagina = '<br><p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Logística <br>'.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>';
    }


    public function build()
    {
        
        $asunto = [];
            //Creación de asunto de correo
            $orden = $this->oportunidad->ordenCompraPropia;
            $asunto[] = 'O. SERVICIO';
            if ($orden == null) {
                $asunto[] = 'SIN O/C';
            } else {
                $asunto[] = $orden->nro_orden;
                $asunto[] = $orden->entidad->nombre;
            }
            $asunto[] = $this->oportunidad->codigo_oportunidad;
            if ($orden != null) {
                $asunto[] = $orden->empresa->abreviado;
            }

        //Vista Email
        $vista = $this->view('almacen.customizacion.hoja-transformacion')->subject(implode(' | ', $asunto));

        return $vista;
    }
}
