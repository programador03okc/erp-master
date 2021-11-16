<?php

namespace App\Mail;

use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailFinalizacionCuadroPresupuesto extends Mailable
{
    use Queueable, SerializesModels;

    public $listaFinalizados;
    public $listaRestablecidos;
    public $mensaje;
    public $piePagina;


    public function __construct($listaFinalizados, $listaRestablecidos)
    {
        $this->listaFinalizados = $listaFinalizados;
        $this->listaRestablecidos = $listaRestablecidos;
        $this->mensaje = '';
        $this->piePagina = '<br><p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Logística <br>'.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>';
    }


    public function build()
    {
        //Creación de asunto de correo
        $asunto=[];
        $this->mensaje='';
        if(count($this->listaFinalizados) >0){
            $asunto[] = 'Ágil finalizó el cuadro de presupuesto ' . implode(",", array_column($this->listaFinalizados, 'codigo_cuadro_presupuesto'));
            $this->mensaje.='<h3><strong>Cuadro de presupuesto finalizado</strong></h3><br>';
            $this->mensaje.='<h4>Información de oportunidad:</h4><br>';
            $this->mensaje.='<ul>';
            foreach ($this->listaFinalizados as $lf) {
                $cc=CuadroCostoView::find($lf['id_cuadro_presupuesto']);
                $this->mensaje.='<li>Oportunidad : '.$cc->descripcion_oportunidad.'</li>';
                $this->mensaje.='<li>Responsable : '.$cc->name.'</li>';
                $this->mensaje.='<li>Fecha Limite : '.$cc->fecha_limite.'</li>';
                $this->mensaje.='<li>Cliente : '.$cc->nombre_entidad.'</li>';
                $this->mensaje.='<li>Tipo de negocio : '.($cc->tipo_cuadro =='am'?'Acuerdo marco':($cc->tipo_cuadro=='directa'?'Venta directa':$cc->tipo_cuadro)).'</li>';
            }
            $this->mensaje.='</ul>';
        }
        if(count($this->listaRestablecidos) >0){
            $asunto[] = 'Ágil restableció el estado del cuadro de presupuesto ' . implode(",", array_column($this->listaFinalizados, 'codigo_cuadro_presupuesto'));
            $this->mensaje.='<h3><strong>Cuadro de presupuesto restablecido</strong></h3><br>';
            $this->mensaje.='<h4>Información de oportunidad:</h4><br>';
            $this->mensaje.='<ul>';
            foreach ($this->listaRestablecidos as $lr) {
                $cc=CuadroCostoView::find($lr['id_cuadro_presupuesto']);
 
                $this->mensaje.='<li>Oportunidad : '.$cc->descripcion_oportunidad.'</li>';
                $this->mensaje.='<li>Responsable : '.$cc->name.'</li>';
                $this->mensaje.='<li>Fecha Limite : '.$cc->fecha_limite.'</li>';
                $this->mensaje.='<li>Cliente : '.$cc->nombre_entidad.'</li>';
                $this->mensaje.='<li>Tipo de negocio : '.($cc->tipo_cuadro =='am'?'Acuerdo marco':($cc->tipo_cuadro=='directa'?'Venta directa':$cc->tipo_cuadro)).'</li>';
            }
            $this->mensaje.='</ul>';
        }

        

        //Vista Email
        $vista = $this->view('logistica.requerimientos.email.finalizar_cuadro_presupuesto')->subject(implode(' | ', $asunto));
        return $vista;
    }
}
