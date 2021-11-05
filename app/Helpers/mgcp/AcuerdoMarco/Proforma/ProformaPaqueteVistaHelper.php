<?php

namespace App\Helpers\mgcp\AcuerdoMarco\Proforma;

use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\AcuerdoMarco\Proforma\Paquete\Paquete;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProformaPaqueteVistaHelper
{

    private Request $request;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function generarTablaEnvios($requerimiento, $empresas)
    {
        $destinos = Paquete::generarConsultaEnvios($requerimiento->requerimiento);
        $totalFilas = $destinos->count();
        $filaActual = 0;
        $lugarEntrega = '';
        $resultado = '';
        foreach ($destinos as $destino) {
            if ($lugarEntrega != $destino->lugar_entrega) {
                //$proformaActual = '';
                $lugarEntrega = $destino->lugar_entrega;
                $resultado .= '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="table-responsive">
                            <table style="margin-bottom: 0px;font-size: small" class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th class="text-right" style="width: 7%">Envío: </th>
                                        <td>' . $destino->lugar_entrega . '</td>
                                        <th class="text-right">Inicio de entrega: </th>
                                        <td>' . (new Carbon($destino->inicio_entrega))->format('d-m-Y') . '</td>
                                        <th class="text-right">Fin de entrega: </th>
                                        <td>' .  (new Carbon($destino->fin_entrega))->format('d-m-Y')  . '</td>
                                        <th class="text-right">Plazo días: </th>
                                        <td>' . $destino->plazo_publicar . '</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-condensed envio" style="width: 100%; font-size: small;margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 10%" class="text-center">Proforma</th>
                                    <th rowspan="2" style="width: 4%" class="text-center">Cant.</th>
                                    <th colspan="' . ($empresas->count()) . '" class="text-center">Empresas</th>
                                </tr>
                                <tr>';
                foreach ($empresas as $empresa) {
                    $estadoRequerimiento = Paquete::where('requerimiento', $requerimiento->requerimiento)->where('id_empresa', $empresa->id)->first();
                    $resultado .= '
                                    <th class="text-center separador-left separador-right" style="width: ' . (85 / $empresas->count()) . '%">
                                        ' . $empresa->semaforo . ' ' . $empresa->empresa . ($estadoRequerimiento != null ? '<br><span style="font-weight: normal; font-size: 0.85em">(' . $estadoRequerimiento->estado . ')</span>' : '') .
                        '</th>';
                }
                $resultado .= '
                            </thead>
                            <tbody>';
                $contadorInterno = $filaActual;
                while ($contadorInterno < $totalFilas && $destinos[$contadorInterno]->lugar_entrega == $destino->lugar_entrega) {
                    $resultado .= '
                    <tr class="separador-top">
                        <td rowspan="2" class="text-center text-small" style="width: 10%">
                            <div>' . $destinos[$contadorInterno]->proforma . '</div>(Soft. educ: ' . ($destinos[$contadorInterno]->software_educativo ? '<strong class="text-danger">SÍ</strong>' : 'NO') . ')
                        </td>
                        <td rowspan="2" class="text-center">' . $destinos[$contadorInterno]->cantidad . '</td>';
                    foreach ($empresas as $empresa) {
                        $resultado .= '<td class="producto text-small separador-left ' . $empresa->id . '-' . $destinos[$contadorInterno]->proforma . '"><div class="text-center">-</div></td>'; //Celdas que contienen la descripción del producto seleccionado, se llenarán por Javascript
                    }
                    $resultado .= '</tr>';
                    $resultado .= '<tr>';
                    foreach ($empresas as $empresa) {
                        $estadoRequerimiento = Paquete::where('requerimiento', $requerimiento->requerimiento)->where('id_empresa', $empresa->id)->first();
                        if ($estadoRequerimiento==null)
                        {
                            $resultado .= '<td class="text-center separador-left">-</td>';
                        } else {
                            $editable = $estadoRequerimiento->estado == 'PENDIENTE' && $destinos[$contadorInterno]->requiere_flete;
                            $resultado .= '<td data-requerimiento-entrega="'.$destinos[$contadorInterno]->nro_requerimiento_entrega.'" data-proforma="' . $destinos[$contadorInterno]->proforma . '" data-empresa="' . $empresa->id . '" class="' . $empresa->id . '-' . $destinos[$contadorInterno]->proforma . ' text-center decimal separador-left flete' . ($editable ? ' success" contenteditable="true"' : '"') . '>';
                            if ($destinos[$contadorInterno]->requiere_flete) {
                                $costoEnvio = Paquete::generarConsultaEnvioDetalle($destinos[$contadorInterno]->proforma, $empresa->id,$destinos[$contadorInterno]->nro_requerimiento_entrega);
                                if (!is_null($costoEnvio)) {
                                    $resultado .= ($costoEnvio->costo_envio_publicar == null ? '' : number_format($costoEnvio->costo_envio_publicar, 2));
                                }
                            } else {
                                $resultado .= 'N/R';
                            }
                            $resultado .= '</td>';
                        }
                        
                    }
                    $resultado .= '</tr>';
                    $contadorInterno++;
                }

                $resultado .= '
                            </tbody>
                        </table>
                    </div>
                </div>';
            }
            $filaActual++;
        }
        return $resultado;
    }

    public function generarTablaProductos($requerimiento, $empresas)
    {
        $resultado = '<div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="table-responsive">
                                <table style="margin-bottom: 0px;font-size: small" class="table table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="text-right" style="width: 7%">Productos: </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-condensed" style="width: 100%; font-size: small;margin-bottom: 10px;">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="width: 10%" class="text-center">Proforma</th>
                                        <th rowspan="2" style="width: 4%" class="text-center">Cant.</th>
                                        <th rowspan="2" style="width: 21%" class="text-center">Producto</th>
                                        <th rowspan="2" style="width: 5%" class="text-center">Herr.</th>
                                        <th colspan="' . ($empresas->count() * 2) . '" class="text-center">Empresas</th>
                                    </tr>
                                    <tr>';
        foreach ($empresas as $empresa) {
            $estadoRequerimiento = Paquete::where('requerimiento', $requerimiento->requerimiento)->where('id_empresa', $empresa->id)->first();
            $resultado .= '
                                        <th colspan="2" class="text-center separador-left separador-right" style="width: ' . (60 / $empresas->count()) . '%">
                                            ' . $empresa->semaforo . ' ' . $empresa->empresa . ($estadoRequerimiento != null ? '<br><span style="font-weight: normal; font-size: 0.85em">(' . $estadoRequerimiento->estado . ')</span>' : '') .
                '</th>';
        }
        $resultado .= '
                                </thead>
                                <tbody>';
        $proformas = Paquete::generarConsultaProformas($requerimiento->requerimiento)
            ->select([
                'requerimiento', 'proforma', 'marca', 'modelo', 'part_no', 'id_producto', 'id_empresa', 'categorias.descripcion AS categoria', 'cantidad', 'estado',
                'precio_unitario_base', 'moneda_ofertada', 'precio_publicar', 'proforma_paquete_producto_detalles.id AS id_detalle', 'proforma_paquete_producto_detalles.seleccionado AS detalle_seleccionado'
            ])->get();
        $filaActual = 0;
        $totalFilas = $proformas->count();
        //return "TOTAL FILAS ".$totalFilas;
        $proformaActual = '';

        foreach ($proformas as $proforma) {
            if ($proforma->proforma != $proformaActual) {
                $proformaActual = $proforma->proforma;
                $totalFilasProforma = 0;
                $contadorInterno = $filaActual;
                $productos = [];
                while ($contadorInterno < $totalFilas) {
                    if ($proformas[$contadorInterno]->proforma == $proformaActual) {
                        $productos[] = $proformas[$contadorInterno]->id_producto; //$proformas[$contadorInterno]->categoria . ': ' . $proformas[$contadorInterno]->marca . ' ' . $proformas[$contadorInterno]->modelo . ' ' . $proformas[$contadorInterno]->part_no;
                        $totalFilasProforma++;
                        $contadorInterno++;
                    } else {
                        break;
                    }
                }
                $productos = array_unique($productos);
                $resultado .= '
                                
                                    <tr class="separador-top">
                                        <td rowspan="' . (count($productos) * 2) . '" class="text-center text-small" style="width: 10%">
                                            <div>' . $proforma->proforma . '</div>(Soft. educ: ' . ($proforma->software_educativo ? '<strong class="text-danger">SÍ</strong>' : 'NO') . ')
                                        </td>
                                        <td rowspan="' . (count($productos) * 2) . '" class="text-center">' . $proforma->cantidad . '</td>';
                $productoActual = 0;
                for ($i = $filaActual; $i < $contadorInterno; $i++) {
                    if ($productoActual != $proformas[$i]->id_producto) {
                        $productoActual = $proformas[$i]->id_producto;
                        $resultado .=  '
                                        <td rowspan="2" class="text-small"><a title="Ver datos adicionales de producto" data-target="#modalDatosProducto" data-toggle="modal" href="#" class="producto" data-id="' . $proformas[$i]->id_producto . '">' . $proformas[$i]->categoria . ': ' . $proformas[$i]->marca . ' ' . $proformas[$i]->modelo . ' ' . $proformas[$i]->part_no . '</a></td>
                                        <td rowspan="2" class="text-center" style="width: 5%">
                                            <div class="dropdown">
                                                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="glyphicon glyphicon-th-list"></span>
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="ver-ofertas-oc" data-toggle="modal" data-target="#modalOfertasOc" data-marca="' . $proformas[$i]->marca . '" data-modelo="' . $proformas[$i]->modelo . '" data-nroparte="' . $proformas[$i]->part_no . '" href="#">
                                                            <span class="fa fa-bar-chart-o"></span> Ver precios en O/C públicas
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>';
                        $contadorInternoProductos = $i;
                        while ($contadorInternoProductos < $contadorInterno && $proformas[$contadorInternoProductos]->id_producto == $productoActual) {
                            $contadorInternoProductos++;
                        }

                        foreach ($empresas as $empresa) {
                            $posEncontrado = -1;
                            $resultado .= '<td colspan="2" class="text-center separador-left text-small">';
                            for ($j = $i; $j < $contadorInternoProductos; $j++) {
                                if ($proformas[$j]->id_empresa == $empresa->id) {
                                    $posEncontrado = $j;
                                }
                            }
                            if ($posEncontrado >= 0) {
                                $resultado .= ($proformas[$posEncontrado]->moneda_ofertada == 'USD' ? '$' : 'S/') . number_format($proformas[$posEncontrado]->precio_unitario_base, 2);
                            } else {
                                $resultado .= '-';
                            }
                            $resultado .= '</td>';
                        }
                        $resultado .= '</tr>';
                        $resultado .=   '<tr>';

                        foreach ($empresas as $empresa) {
                            $posEncontrado = -1;

                            for ($j = $i; $j < $contadorInternoProductos; $j++) {
                                if ($proformas[$j]->id_empresa == $empresa->id) {
                                    $posEncontrado = $j;
                                }
                            }
                            if ($posEncontrado >= 0) {
                                $editable = $proformas[$posEncontrado]->estado == 'PENDIENTE';
                                $resultado .= '<td class="text-center separador-left"><input ' . ($editable ? '' : 'disabled') . ' type="checkbox" data-producto="' . $proformas[$posEncontrado]->id_producto . '" ' . ($proformas[$posEncontrado]->detalle_seleccionado ? 'checked' : '') . ' class="' . $empresa->id . '-' . $proformas[$posEncontrado]->proforma . '" data-id="' . $proformas[$posEncontrado]->id_detalle . '"></td>
                                                    <td data-id="' . $proformas[$posEncontrado]->id_detalle . '" class="text-center precio separador-right decimal' . ($editable ? ' success' : '') . '" ' . ($editable ? 'contenteditable="true"' : '') . '>' . ($proformas[$posEncontrado]->precio_publicar == null ? '' : number_format($proformas[$posEncontrado]->precio_publicar, 2)) . '</td>';
                            } else {
                                $resultado .= '<td colspan="2" class="text-center separador-left separador-right">-</td>';
                            }
                            $resultado .= '</td>';
                        }
                        $resultado .=   '</tr>';
                    }
                }
            }

            $filaActual++;
        }

        $resultado .= '</tbody>
                    </table>
                </div><!-- Panel body-->
            </div><!-- Panel-->';
        return $resultado;
    }

    public function generarLista(User $usuario)
    {
        //$request->pagina=0;
        //$requerimientos =  Paquete::generarConsultaRequerimientos($this->request)->orderByRaw('mgcp_acuerdo_marco.proforma_co_monto_requerimiento(requerimiento) DESC');
        $requerimientos =  Paquete::generarConsultaRequerimientos($this->request)->orderByRaw('monto_total DESC')->offset(intval($this->request->pagina) <= 1 ? 0 : ((intval($this->request->pagina) - 1) * 10))->limit(10)->get();

        //$requerimientos = $requerimientos;
        $puedeVerTodosLosPrecios = $usuario->tieneRol(44);
        $puedeDeshacerTodasLasCotizaciones = $usuario->tieneRol(123);
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $resultado = '';

        foreach ($requerimientos as $requerimiento) {
            //Truco para no hacer consulta a la BD para obtener el semáforo de la entidad
            $entidad = new Entidad();
            $entidad->indicador_semaforo = $requerimiento->indicador_semaforo;
            $resultado .= '
            <div class="panel panel-primary panel-requerimiento">
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
                                    <th class="text-right" style="width: 5%">F.emisión: </th>
                                    <td style="width: 5%">' . $requerimiento->fecha_emision . '</td>
                                    <th class="text-right" style="width: 5%">F.límite: </th>
                                    <td style="width: 5%">' . $requerimiento->fecha_limite . '</td>
                                    <th class="text-right">Monto total.: </th>
                                    <td>' . number_format($requerimiento->monto_total, 2) . '</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>';
            $resultado .= '
                <div class="panel-body" style="display: none">';

            $resultado .= $this->generarTablaProductos($requerimiento, $empresas);
            $resultado .= $this->generarTablaEnvios($requerimiento, $empresas);
            $resultado .= '
                
                </div><!-- Panel body-->
            </div><!-- Panel primary-->';
        }
        if ($resultado == '') {
            $resultado = '<div class="text-center">Sin resultados</div>';
        }
        return $resultado;
    }

    public function generarPaginacionProformas()
    {
        $totalFilas = Paquete::generarConsultaRequerimientos($this->request)->get()->count();
        $paginas = $totalFilas == 0 ? 0 : ceil($totalFilas / 10);
        //return response()->json(array('body' => $totalFilas==0 ? 0 : $totalFilas/$this->request->pagina), 200);
        $footer = '
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

        $footer .= '</select>
                <div class="form-control-static"> de ' . $paginas . '</div>
                </div>
            </div>
        </div>
        <button title="Siguiente" type="button" class="btn btn-default btn-sm siguiente">&raquo;</button> ';
        return $footer;
    }
}
