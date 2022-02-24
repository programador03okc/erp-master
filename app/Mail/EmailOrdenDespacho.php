<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOrdenDespacho extends Mailable
{
    use Queueable, SerializesModels;

    public $oportunidad;
    public $mensaje;
    public $archivos;
    public $requerimiento;

    public function __construct($oportunidad, $mensaje, $archivos, $requerimiento)
    {
        $this->oportunidad = $oportunidad;
        $this->mensaje = $mensaje;
        $this->archivos = $archivos;
        $this->requerimiento = $requerimiento;
    }

    public function build()
    {
        //Creación de asunto de correo
        $asunto = [];
        if ($this->oportunidad !== null) {
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
        } else if ($this->requerimiento !== null) {
            $asunto[] = 'DESPACHO DEL ' . $this->requerimiento->codigo . ' - ' . $this->requerimiento->concepto;
        }
        //Vista Email
        $vista = $this->view('almacen.distribucion.email.orden-despacho')->subject(implode(' | ', $asunto));
        foreach ($this->archivos as $archivo) {
            $vista->attach($archivo);
        }
        return $vista;
    }
}
