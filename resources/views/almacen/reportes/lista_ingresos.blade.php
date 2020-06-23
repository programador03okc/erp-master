@extends('layout.head')
@include('layout.menu_almacen')

@section('cabecera')
Lista de Ingresos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('content')
<div class="page-main" type="lista_ingresos">
    <legend class="mylegend">
        <h2>Lista de Ingresos</h2>
        <ol class="breadcrumb">
            <li>
                {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Kardex Sunat" 
                    onClick="downloadKardexSunat();">Kardex Sunat</button> --}}
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">
                    <i class="fas fa-search"></i>  Filtros</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaIngresos">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th hidden></th>
                        <th></th>
                        <th>Fecha Emisión</th>
                        <th>Cod.Ing</th>
                        <th>Fecha Guía</th>
                        <th>Guía</th>
                        <th>Fecha Doc</th>
                        <th>Tp</th>
                        <th>Serie-Número</th>
                        <th>RUC</th>
                        <th width="100px">Razon Social</th>
                        <th>Ordenes</th>
                        <th>Mn</th>
                        <th>Valor Neto</th>
                        <th>IGV</th>
                        <th>Total</th>
                        <th>Saldo</th>
                        <th>Condicion</th>
                        <th>Días</th>
                        <th>Operación</th>
                        <th>Fecha Vcmto</th>
                        <th>Responsable</th>
                        <th>T.Cambio</th>
                        <th>Almacén</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="col-md-2">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <input type="checkbox" name="no_revisado" onClick="search();" style="width:30px;"/>
                        </td>
                        <td><label>No Revisado(s)</label></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="revisado" onClick="search();" style="width:30px;"/>
                        </td>
                        <td><label>Revisado(s)</label></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="observado" onClick="search();" style="width:30px;"/>
                        </td>
                        <td><label>Observado(s)</label></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtros">
    <div class="modal-dialog">
        <div class="modal-content" style="width:600px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros de Ingresos de Almacén</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Empresas</h5>
                        <select class="form-control" name="id_empresa" >
                            @foreach ($empresas as $alm)
                                <option value="{{$alm->id_empresa}}">{{$alm->razon_social}}</option>
                            @endforeach
                        </select>
                        <div style="display:flex">
                            <input type="checkbox" name="todas_empresas" style="width:30px;margin-top:10px;"/>
                            <h5>Todas</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Almacén</h5>
                        <select class="form-control" name="almacen" multiple>
                            @foreach ($almacenes as $alm)
                                <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                            @endforeach
                        </select>
                        <div style="display:flex">
                            <input type="checkbox" name="todos_almacenes" style="width:30px;margin-top:10px;"/>
                            <h5>Todas</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Documentos</h5>
                        <select class="form-control" name="documento" multiple>
                            {{-- <option value="102">Factura</option>
                            <option value="104">Boleta de Venta</option>
                            <option value="108">Nota de Crédito</option>
                            <option value="109">Nota de Débito</option>
                            <option value="113">Ticket</option>
                            <option value="100">Orden de Compra</option> --}}
                            @foreach ($tp_doc_almacen as $alm)
                                <option value="{{$alm->id_tp_doc_almacen}}">{{$alm->descripcion}}</option>
                            @endforeach
                        </select>
                        <div style="display:flex">
                            <input type="checkbox" name="todos_documentos" style="width:30px;margin-top:10px;"/>
                            <h5>Todas</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Condiciones</h5>
                        <select class="form-control" name="condicion" multiple>
                            @foreach ($tp_operacion as $alm)
                                <option value="{{$alm->id_operacion}}">{{$alm->cod_sunat}} - {{$alm->descripcion}}</option>
                            @endforeach
                        </select>
                        <div style="display:flex">
                            <input type="checkbox" name="todas_condiciones" style="width:30px;margin-top:10px;"/>
                            <h5>Todas</h5>
                        </div>
                    </div>
                </div>
                <fieldset class="group-importes"><legend><h6>Filtros específicos</h6></legend>
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td colSpan="2" text-align="right">
                                    <div style="display:flex;">
                                        <span class="form-control" style="width:100px;"> Desde: </span>
                                        <input type="date" class="form-control" name="fecha_inicio">
                                        <span class="form-control" style="width:100px;"> Hasta: </span>
                                        <input type="date" class="form-control" name="fecha_fin">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">Proveedor: </td>
                                <td>
                                    <div style="display:flex;">
                                        <input class="oculto" name="id_proveedor"/>
                                        <input class="oculto" name="id_contrib"/>
                                        <input type="text" class="form-control" name="razon_social" placeholder="Seleccione un proveedor..." 
                                            aria-describedby="basic-addon1" readOnly>
                                        <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="proveedorModal();">
                                            <i class="fa fa-search"></i>
                                        </button>
                                        <button type="button" class="input-group-text btn-danger" id="basic-addon1" onClick="limpiar_proveedor();">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">Transportista: </td>
                                <td>
                                    <div style="display:flex;">
                                        <input class="oculto" name="id_proveedor_tra"/>
                                        <input class="oculto" name="id_contrib_tra"/>
                                        <input type="text" class="form-control" name="razon_social_tra" placeholder="Seleccione un transportista..." 
                                            aria-describedby="basic-addon1" readOnly>
                                        <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="transportistaModal('compra');">
                                            <i class="fa fa-search"></i>
                                        </button>
                                        <button type="button" class="input-group-text btn-danger" id="basic-addon1" onClick="limpiar_transportista();">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Responsable: </td>
                                <td>
                                    <div style="display:flex;">
                                        <select class="form-control" name="responsable">
                                            <option value="0" >Elija una opción</option>
                                            @foreach ($usuarios as $alm)
                                                <option value="{{$alm->id_usuario}}">{{$alm->nombre_trabajador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Moneda: </td>
                                <td>
                                    <div style="display:flex;">
                                        <select class="form-control" name="moneda_opcion">
                                            <option value="0" >Elija una opción</option>
                                            <option value="1" >Docs en Soles (S/)</option>
                                            <option value="2" >Docs en Dólares (US$)</option>
                                            <option value="3" >Docs en Soles y Dólares</option>
                                            <option value="4" >Convertir Docs en Soles</option>
                                            <option value="5" >Convertir Docs en Dólares</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Orden de Impresión: </td>
                                <td>
                                    <div style="display:flex;">
                                        <select class="form-control" name="moneda_opcion">
                                            <option value="0" >Elija una opción</option>
                                            <option value="1" >Tipo de Documento</option>
                                            <option value="2" >Fecha de Documento</option>
                                            <option value="3" >Fecha de Vencimiento</option>
                                            <option value="4" >Razon Social</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            {{-- <tr>
                                <td></td>
                                <td>Mostrar Documentos Referenciados: 
                                    <input type="checkbox" name="referenciado" style="width:30px;margin-top:10px;"/>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div class="modal-footer">
                <label id="mid_doc_com" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="actualizarLista();">Listar</button>
            </div>
        </div>
    </div>
</div>
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.transportistaModal')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>

    <script src="{{('/js/almacen/reporte/lista_ingresos.js')}}"></script>
    <script src="{{('/js/almacen/reporte/filtros.js')}}"></script>
    <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/transportistaModal.js')}}"></script>
@endsection