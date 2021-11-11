<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailContactoDespacho extends Mailable
{
    use Queueable, SerializesModels;

    public $oportunidad;
    public $mensaje;

    public function __construct($oportunidad, $mensaje)
    {
        $this->oportunidad = $oportunidad;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        //Creación de asunto de correo
        $orden = $this->oportunidad->ordenCompraPropia;
        $mensaje = $this->mensaje;

        $asunto = [];
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
        $vista = $this->view(
            'almacen.distribucion.email.envio-contacto',
            compact('mensaje')
        )
            ->subject(implode(' | ', $asunto));

        return $vista;
    }
}
