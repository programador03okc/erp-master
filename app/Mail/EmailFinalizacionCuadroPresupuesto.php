<?php

namespace App\Mail;

use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Debugbar;

class EmailFinalizacionCuadroPresupuesto extends Mailable
{
    use Queueable, SerializesModels;


    public $nombreUsuarioEnSession;
    public $codigoOportunidad;
    public $payload;

    public $requerimiento;
    public $cuadroCosto;
    public $ordenCompraPropia;
    public $oportunidad;
    

    public function __construct($codigoOportunidad,$payload,$nombreUsuarioEnSession)
    {

        $this->nombreUsuarioEnSession = $nombreUsuarioEnSession;
        $this->codigoOportunidad = $codigoOportunidad;
        $this->payload = $payload;

    }


    public function build()
    {
    $asunto[] = 'Finalización de cuadro de presupuesto ' . (implode(",",$this->codigoOportunidad)). ' por '.$this->nombreUsuarioEnSession;
    $vista = $this->view('logistica.requerimientos.email.finalizar_cuadro_presupuesto')->subject(implode(' | ', $asunto));
    return $vista;
    }
}
