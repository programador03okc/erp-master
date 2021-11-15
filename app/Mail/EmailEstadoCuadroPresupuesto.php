<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailEstadoCuadroPresupuesto extends Mailable
{
    use Queueable, SerializesModels;

    public $listaFinalizados;
    public $listaRestablecidos;
    public $mensaje;
    // public $archivos;


    public function __construct($listaFinalizados, $listaRestablecidos)
    {
        $this->listaFinalizados = $listaFinalizados;
        $this->listaRestablecidos = $listaRestablecidos;
        $this->mensaje = '';
        // $this->archivos = $archivos;


    }


    public function build()
    {
        //Creación de asunto de correo
        $asunto=[];
        $this->mensaje=[];
        if(count($this->listaFinalizados) >0){
            $asunto[] = 'Se finalizo el cuadro de presupuesto ' . implode(",", array_column($this->listaFinalizados, 'codigo_cuadro_presupuesto'));
        
            foreach ($this->listaFinalizados as $lf) {
                $this->mensaje[]='Código CP: ' . $lf['codigo_cuadro_presupuesto'] . ' correspondiente al requerimiento ' . $lf['codigo_requerimiento'] .  '</li>';
            }
        }
        if(count($this->listaRestablecidos) >0){
            $asunto[] = 'Se restableció el estado del cuadro de presupuesto ' . implode(",", array_column($this->listaFinalizados, 'codigo_cuadro_presupuesto'));
        
            foreach ($this->listaRestablecidos as $lr) {
                $this->mensaje[]='Código CP: ' . $lr['codigo_cuadro_presupuesto'] . ' correspondiente al requerimiento ' . $lr['codigo_requerimiento'] .  '</li>';
            }
        }

        $this->mensaje[]=   '<br><p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Logística <br>'.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>';

        

        //Vista Email
        $vista = $this->view('logistica.requerimientos.email.estado_cuadro_presupuesto')->subject(implode(' | ', $asunto));
        // foreach ($this->archivos as $archivo) {
        //     $vista->attach($archivo);
        // }
        return $vista;
    }
}
