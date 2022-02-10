<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotificarUsuarioPropietarioDeDocumento extends Mailable
{
    use Queueable, SerializesModels;

    public $idTipoDocumento;
    public $requerimiento;
    public $sustento;
    public $nombreCompletoUsuarioPropietarioDelDocumento;
    public $nombreCompletoUsuarioRevisaAprueba;
    public $montoTotal;
    public $accion;

    public $piePagina;
     // public $archivos;


    public function __construct($idTipoDocumento,$requerimiento,$sustento,$nombreCompletoUsuarioPropietarioDelDocumento,$nombreCompletoUsuarioRevisaAprueba,$montoTotal,$accion)
    {
        $this->idTipoDocumento = $idTipoDocumento;
        $this->requerimiento = $requerimiento;
        $this->sustento = $sustento;
        $this->nombreCompletoUsuarioPropietarioDelDocumento = $nombreCompletoUsuarioPropietarioDelDocumento;
        $this->nombreCompletoUsuarioRevisaAprueba = $nombreCompletoUsuarioRevisaAprueba;
        $this->montoTotal = $montoTotal;
        $this->accion = $accion;
        $this->piePagina = '<br><p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Necesidades <br>'.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>';
    }


    public function build()
    {
        $asunto = [];

        if($this->idTipoDocumento ==1){
            $tipoDocumento ='Requerimiento de B/S';
        }elseif($this->idTipoDocumento ==11){
            $tipoDocumento ='Requerimiento de pago';
        }

        $asunto[] =  'El ' . $tipoDocumento .' con código '.$this->requerimiento->codigo. ' fue '.$this->accion;
 

        //Vista Email
        $vista = $this->view('necesidades.revisar_aprobar.notificacionRevisarAprobar')->subject(implode(' | ', $asunto));

        return $vista;
    }
}
