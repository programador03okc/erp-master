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
    
    public $piePagina;


    public function __construct($codigoOportunidad,$payload,$nombreUsuarioEnSession)
    {

        $this->nombreUsuarioEnSession = $nombreUsuarioEnSession;
        $this->codigoOportunidad = $codigoOportunidad;
        $this->payload = $payload;
        $this->piePagina = '<br><p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Logística <br>'.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>';
    }


    public function build()
    {
    $asunto[] = 'Finalización de cuadro de presupuesto ' . $this->codigoOportunidad. ' por '.$this->nombreUsuarioEnSession;
    $vista = $this->view('logistica.requerimientos.email.finalizar_cuadro_presupuesto')->subject(implode(' | ', $asunto));
    return $vista;
    }
}
