<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOrdenAnulada extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreUsuarioEnSession;
    public $orden;
    public $mensaje;
    public $finalizadosORestablecido;
    public $piePagina;

    public function __construct($orden,$finalizadosORestablecido,$nombreUsuarioEnSession)
    {
        $this->nombreUsuarioEnSession = $nombreUsuarioEnSession;
        $this->orden = $orden;
        $this->finalizadosORestablecido = $finalizadosORestablecido;
        $this->piePagina = '<br><p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Logística <br>'.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>';
    }


    public function build()
    {
        
        $asunto = [];
            $orden = $this->orden;
            $asunto[] = 'Anulación de orden de compra '.$orden->codigo.' - '.$orden->sede->descripcion;

        $vista = $this->view('logistica.gestion_logistica.compras.ordenes.email.anular-orden')->subject(implode(' | ', $asunto));

        return $vista;
    }
}
