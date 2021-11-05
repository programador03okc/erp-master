<?php

namespace App\Helpers\mgcp\AcuerdoMarco\Proforma;

use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria;
use App\Models\mgcp\AcuerdoMarco\Proforma\GranCompra;
use App\Models\User;
use Illuminate\Http\Request;

class ProformaIndividualNuevaVistaHelper
{
    private Request $request;
    const TOTAL_FILAS_VISTA=400;

    function __construct(Request $request)
    {
        $this->request=$request;
    }
    
    public function generarLista(User $usuario)
    {
        $requerimientos =  $this->request->tipoProforma == 1 ? CompraOrdinaria::generarConsultaRequerimientos($this->request)->orderByRaw('monto_total DESC') : 
            GranCompra::generarConsultaRequerimientos($this->request)->orderByRaw('monto_total DESC');

        $requerimientos=$requerimientos->offset(intval($this->request->pagina) <= 1 ? 0 : ((intval($this->request->pagina) - 1) * self::TOTAL_FILAS_VISTA))->limit(self::TOTAL_FILAS_VISTA)->get();
        $puedeVerTodosLosPrecios = $usuario->tieneRol(44);
        $puedeDeshacerTodasLasCotizaciones = $usuario->tieneRol(123);
        $resultado = '';
        /*Generación de cabecera de requerimiento*/
        foreach ($requerimientos as $requerimiento) {
            //Truco para no hacer consulta a la BD para obtener el semáforo de la entidad
            $entidad = new Entidad();
            $entidad->indicador_semaforo = $requerimiento->indicador_semaforo;
            $resultado .= '
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="table-responsive">
                        <table style="margin-bottom: 0px;font-size: small" class="table table-condensed requerimiento">
                            <thead>
                                <tr>
                                    <td style="width: 2%"><button class="btn btn-primary btn-xs mostrar"><span class="glyphicon glyphicon-plus"></span></button></td>
                                    <th class="text-right" style="width: 10%">Requerimiento: </th>
                                    <td style="width: 10%">' . $requerimiento->requerimiento . '</td>
                                    <th class="text-right" style="width: 10%">Entidad: </th>
                                    <td style="width: 30%">' . $entidad->semaforo . ' <a data-id="' . $requerimiento->id_entidad . '" data-ruc="' . $requerimiento->ruc_entidad . '" title="Ver información de entidad" class="entidad" href="#">' . $requerimiento->entidad . '</a></td>
                                    <th class="text-right" style="width: 10%">Lugar de entrega: </th>
                                    <td style="width: 10%"><a class="lugar-entrega" href="#" data-requerimiento="' . str_replace('<strong style="color:red">(Emergencia COVID-19)</strong>', '', $requerimiento->requerimiento) . '" data-entrega="' . $requerimiento->lugar_entrega . '" title="' . $requerimiento->lugar_entrega . '">' . $requerimiento->departamento . '</a></td>
                                    <th class="text-right">Monto total.: </th>
                                    <td>' . number_format($requerimiento->monto_total, 2) . ' ' . $requerimiento->moneda_ofertada . '</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>';
            /*Generación de contenido de proformas*/
            $proformas = $this->request->tipoProforma == 1 ? CompraOrdinaria::generarConsultaProformas($this->request)->where('requerimiento', $requerimiento->requerimiento) : 
                GranCompra::generarConsultaProformas($this->request)->where('requerimiento', $requerimiento->requerimiento);
            $proformas=$proformas->select([
                    'nro_proforma', 'proforma', 'fecha_emision', 'fecha_limite', 'inicio_entrega', 'fin_entrega', 'marca', 'modelo', 'part_no',
                    'id_entidad', 'id_empresa', 'id_producto', 'software_educativo', 'cantidad', 'precio_unitario_base', 'moneda_ofertada',
                    'id_ultimo_usuario', 'estado', 'costo_envio_publicar', 'proforma', 'categorias.descripcion AS categoria', 'requiere_flete',
                    'precio_publicar', 'plazo_publicar','users.nombre_corto AS nombre_usuario', 'restringir','puede_restringir'
                ])->get();
            $proformaInicial = 'x';
            $productoInicial = 0;
            $totalFilas = $proformas->count();
            $contador = 0;
            $contadorFilaIngreso = 0;
            foreach ($proformas as $proforma) {
                $contador++;
                if ($proformaInicial != $proforma->proforma) {
                    $proformaInicial = $proforma->proforma;
                    $productoInicial = 0;
                    $contadorFilaIngreso = $contador - 1;
                    //
                    $resultado .= '
                <div class="panel-body" style="display: none">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="table-responsive">
                                <table style="margin-bottom: 0px;font-size: small" class="table table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="text-right">Proforma: </th>
                                            <td>' . $proforma->proforma . '</td>
                                            <th class="text-right">Fecha de emisión: </th>
                                            <td>' . $proforma->fecha_emision . '</td>
                                            <th class="text-right">Fecha límite: </th>
                                            <td>' . $proforma->fecha_limite . '</td>
                                            <th class="text-right">Inicio de entrega: </th>
                                            <td>' . $proforma->inicio_entrega . '</td>
                                            <th class="text-right">Fin de entrega: </th>
                                            <td>' . $proforma->fin_entrega . '</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-condensed" style="width: 100%; font-size: small;margin-bottom: 10px;">
                                <thead>
                                    <tr>
                                        <th style="width: 10%" class="text-center">Categoría</th>
                                        <th style="width: 15%" class="text-center">Producto</th>
                                        <th class="text-center">Nro. parte</th>
                                        <th style="width: 7%" title="Herramientas" class="text-center">Herram.</th>
                                        <th style="width: 7%" class="text-center">Soft. educ.</th>
                                        <th style="width: 7%" class="text-center">Cant.</th>
                                        <th style="width: 45%" class="text-center">Ingreso de datos</th>
                                    </tr>
                                </thead>
                                <tbody>';
                }


                /*Tabla de productos a cotizar*/
                if ($productoInicial != $proforma->id_producto) {
                    $productoInicial = $proforma->id_producto;
                    $resultado .= '
                <tr>
                    <td class="text-center">' . $proforma->categoria  . '</td>
                    <td class="text-center"><a title="Ver datos adicionales de producto" data-target="#modalDatosProducto" data-toggle="modal" href="#" class="producto" data-id="' . $proforma->id_producto . '">' . $proforma->marca . ' ' . $proforma->modelo . '</a></td>
                    <td class="text-center">' . $proforma->part_no . '</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-th-list"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="ver-ofertas-oc" data-toggle="modal" data-target="#modalOfertasOc" data-marca="' . $proforma->marca . '" data-modelo="' . $proforma->modelo . '" data-nroparte="' . $proforma->part_no . '" href="#"><span class="fa fa-bar-chart-o"></span>Ver precios en O/C públicas</a></li>
                                <!--<li><a class="comentarios" data-id="' . $proforma->nro_proforma . '" data-proforma="' . $proforma->proforma . '" href="#"><span class="glyphicon glyphicon-comment"></span>Comentarios en proforma</a></li>-->
                                <li><a class="calculadora" data-cantidad=' . $proforma->cantidad . ' data-requerimiento="'.str_replace('<strong style="color:red">(Emergencia COVID-19)</strong>', '', $requerimiento->requerimiento).'" data-proforma="' . $proforma->proforma . '" data-tipo="'.$this->request->tipoProforma.'" data-producto="' . $proforma->id_producto . '" href="#"><span class="glyphicon glyphicon-th"></span>Calculadora</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="text-center">' . $proforma->software_educativo . '</td>
                    <td class="text-center">' . $proforma->cantidad . '</td>
                    <td style="padding: 0px !important;;">
                        <table class="table table-condensed table-bordered ingreso-datos" style="margin-bottom: 0px !important;">
                            <thead>
                                <tr>
                                    <th class="text-center">Empresa</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Precio base</th>
                                    <th class="text-center">Plazo</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Flete</th>
                                    <th title="Restringir" class="text-center">Restr.</th>
                                </tr>
                            </thead>
                            <tbody>';
                }
                /*Tabla de llenado de datos*/

                while ($contadorFilaIngreso < $totalFilas && $proforma->proforma == $proformas[$contadorFilaIngreso]->proforma && $proforma->id_producto == $proformas[$contadorFilaIngreso]->id_producto) {
                    $resultado .= '
                <tr>
                    <td class="text-center" style="width: 25%"> ' . $proformas[$contadorFilaIngreso]->empresa->semaforo . ' ' . $proformas[$contadorFilaIngreso]->empresa->empresa . '</td>
                    <td class="text-center estado" style="width: 15%" title="Cotizada por ' . ($proformas[$contadorFilaIngreso]->id_ultimo_usuario != null ? $proformas[$contadorFilaIngreso]->nombre_usuario : '') . '">' . $proformas[$contadorFilaIngreso]->estado . '</td>
                    <td class="text-center" style="width: 20%">' . number_format($proformas[$contadorFilaIngreso]->precio_unitario_base, 2) . ' ' . $proformas[$contadorFilaIngreso]->moneda_ofertada . '</td>';
                    if ($this->request->tipoProforma==2 && $proformas[$contadorFilaIngreso]->estado == 'PENDIENTE') {
                        $resultado .= '<td style="width: 10%" '.($proformas[$contadorFilaIngreso]->restringir ? '' : 'contenteditable="true"').' data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '" data-campo="plazo_publicar" class="plazo '.($proformas[$contadorFilaIngreso]->restringir ? '' : 'success ').'text-center entero">' . $proformas[$contadorFilaIngreso]->plazo_publicar . '</td>';
                    }
                    else
                    {
                        $resultado .= '<td class="text-center entero '.($this->request->tipoProforma==2 ? 'plazo' : '').'" data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '" data-campo="plazo_publicar" style="width: 10%">' . $proformas[$contadorFilaIngreso]->plazo_publicar . '</td>';
                    }
                    if ($proformas[$contadorFilaIngreso]->estado == 'PENDIENTE') {
                        $resultado .= '<td style="width: 15%" '.($proformas[$contadorFilaIngreso]->restringir ? '' : 'contenteditable="true"').' data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '" data-campo="precio_publicar" class="'.($proformas[$contadorFilaIngreso]->restringir ? '' : 'success ').'text-center decimal precio">' . ($proformas[$contadorFilaIngreso]->precio_publicar == null ? '' : number_format($proformas[$contadorFilaIngreso]->precio_publicar, 2)) . '</td>';
                    } else {
                        $resultado .= '<td style="width: 15%" data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '" data-campo="precio_publicar" class="text-center decimal precio">';
                        //Ocultar precios
                        if ($proformas[$contadorFilaIngreso]->estado == 'COTIZADA' || $proformas[$contadorFilaIngreso]->estado == 'SELECCIONADA') {
                            if ($puedeVerTodosLosPrecios || $proformas[$contadorFilaIngreso]->id_ultimo_usuario == $usuario->id) {
                                $resultado .= number_format($proformas[$contadorFilaIngreso]->precio_publicar, 2);
                            } else {
                                $resultado .= '(Oculto)';
                            }
                        } else {
                            $resultado .= number_format($proformas[$contadorFilaIngreso]->precio_publicar, 2);
                        }
                        //Deshacer cotizaciones
                        if ($proformas[$contadorFilaIngreso]->puede_deshacer_cotizacion && ($proformas[$contadorFilaIngreso]->id_ultimo_usuario == $usuario->id || $puedeDeshacerTodasLasCotizaciones)) {
                            $resultado .= '<div><a href="#" class="deshacer" data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '">(Deshacer)</a></div>';
                        }
                        $resultado .= '</td>';
                    }
                    //Permitir ingreso de flete
                    if ($proformas[$contadorFilaIngreso]->estado == 'PENDIENTE' && $proformas[$contadorFilaIngreso]->requiere_flete) {
                        $resultado .= '<td style="width: 15%" '.($proformas[$contadorFilaIngreso]->restringir ? '' : 'contenteditable="true"').' data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '" data-requiere="1" data-campo="costo_envio_publicar" class="'.($proformas[$contadorFilaIngreso]->restringir ? '' : 'success ').'text-center decimal flete">';
                    } else {
                        $resultado .= '<td style="width: 15%" data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '" data-requiere="' . ($proformas[$contadorFilaIngreso]->requiere_flete ? '1' : '0') . '" data-campo="costo_envio_publicar" class="text-center decimal flete">';
                    }
                    $resultado .= ($proformas[$contadorFilaIngreso]->requiere_flete ? ($proformas[$contadorFilaIngreso]->costo_envio_publicar == null ? '' : number_format($proformas[$contadorFilaIngreso]->costo_envio_publicar, 2)) : 'N/R') . '</td>';
                    $resultado .='<td class="text-center restringir">';
                    if ($proformas[$contadorFilaIngreso]->puede_restringir)
                    {
                        $resultado.='<input type="checkbox" name="restringir" '.($proformas[$contadorFilaIngreso]->restringir ? 'checked' : '').' data-id="' . $proformas[$contadorFilaIngreso]->nro_proforma . '">';
                    }
                    $resultado .='</td>';
                    $resultado .= '</tr>';
                    $contadorFilaIngreso++;
                }

                if ($contador == $totalFilas || $proforma->id_producto != $proformas[$contador]->id_producto) {
                    $resultado .= '        
                            </tbody>
                        </table>
                    </td>
                </tr>';
                }

                /*Cerrar la tabla*/
                if ($contador == $totalFilas || $contador < $totalFilas &&  $proformaInicial != $proformas[$contador]->proforma) {
                    $resultado .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>';
                }
            }
            $resultado .= '</div>';
        }
        if ($resultado == '') {
            $resultado = '<div class="text-center">Sin resultados</div>';
        }
        return $resultado;
    }

    public function generarPaginacionProformas()
    {
        $totalFilas = $this->request->tipoProforma == 1 ? CompraOrdinaria::generarConsultaRequerimientos($this->request)->get()->count() : GranCompra::generarConsultaRequerimientos($this->request)->get()->count();
        $paginas = $totalFilas == 0 ? 0 : ceil($totalFilas / self::TOTAL_FILAS_VISTA);
        //return response()->json(array('body' => $totalFilas==0 ? 0 : $totalFilas/$request->pagina), 200);
        $footer = '
        <div>
            <button title="Anterior" type="button" class="btn btn-default btn-sm anterior">&laquo;</button>
            <div class="btn-group" role="group" style="margin-left: 10px; margin-right: 10px; padding-bottom: 3px"> 
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control input-sm pagina">';
        //Select de páginas            
        if ($totalFilas == 0) {
            $footer .= '<option value="0">0</option>';
        } else {
            for ($i = 1; $i <= $paginas; $i++) {
                $footer .= '<option value="' . $i . '" ' . ($this->request->pagina == $i ? 'selected' : '') . '>' . $i . '</option>';
            }
        }

        $footer .= '
                        </select>
                        <div class="form-control-static"> de ' . $paginas . '</div>
                    </div>
                </div>
            </div>
            <button title="Siguiente" type="button" class="btn btn-default btn-sm siguiente">&raquo;</button>
        </div>';
        return $footer;
        /*
        <div class="text-center"><small style="color: #777">';
        $footer.=$totalFilas.($totalFilas == 1 ? ' fila' : ' filas');
        $footer.='</small></div>
        */
    }
}
