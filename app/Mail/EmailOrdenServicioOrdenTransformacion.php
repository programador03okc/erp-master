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

    public $listaFinalizados;
    public $listaRestablecidos;
    public $mensaje;
    public $oportunidad;
    public $logo_empresa;
    public $codigo;

    public $piePagina;
     // public $archivos;


    public function __construct($listaFinalizados, $listaRestablecidos)
    {
        $this->listaFinalizados = $listaFinalizados;
        $this->listaRestablecidos = $listaRestablecidos;
        $this->logo_empresa = '';
        $this->codigo = '';
        $this->mensaje = '';
        $this->piePagina = '<br><p> *Este correo es generado de manera automática, por favor no responder.</p> 
        <br> Saludos <br> Módulo de Logística <br>'.config('global.nombreSistema') . ' '  . config('global.version') . ' </p>';
    }


    public function build()
    {
        
        $asunto = [];
        foreach ($this->listaFinalizados as $lf) {
            $cuadro = CuadroCosto::find($lf['id_cuadro_presupuesto']);
            $oportunidad = Oportunidad::find($cuadro->id_oportunidad);
            $this->oportunidad = $oportunidad;

            $transformacion =  Transformacion::select('transformacion.codigo', 'cc.id_oportunidad', 'adm_empresa.logo_empresa')
            ->leftjoin('mgcp_cuadro_costos.cc', 'cc.id', '=', 'transformacion.id_cc')
            ->join('almacen.alm_almacen', 'alm_almacen.id_almacen', '=', 'transformacion.id_almacen')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'alm_almacen.id_sede')
            ->join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'sis_sede.id_empresa')
            ->where('cc.id', $lf['id_cuadro_presupuesto'])
            ->first();
            
            $this->logo_empresa=$transformacion->logo_empresa??'';
            $this->codigo=$transformacion->codigo??'';
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
        }


        
        

        //Vista Email
        $vista = $this->view('almacen.customizacion.hoja-transformacion')->subject(implode(' | ', $asunto));
        // foreach ($this->archivos as $archivo) {
        //     $vista->attach($archivo);
        // }
        return $vista;
    }
}
