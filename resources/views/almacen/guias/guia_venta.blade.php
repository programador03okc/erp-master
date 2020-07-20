@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Guía de Venta - Salida
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Movimientos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="guia_venta">
    <input type="text" class="oculto" name="modo">
    <div class="col-md-12" id="tab-guia_venta" style="padding-left:0px;padding-right:0px;">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#general">Datos Generales</a></li>
            <li class=""><a type="#detalle">Detalle de Items</a></li>
        </ul>
        <div class="content-tabs">
            <section id="general" hidden>
                <form id="form-general" type="register">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_guia_ven" primary="ids">
                <input type="hidden" name="id_transferencia">
                <input type="hidden" name="id_tp_doc">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Estado:  <span id="des_estado"></span></h5>
                    </div>
                    <div class="col-md-2">
                        <label id="codigo_trans"></label>
                    </div>
                    <div class="col-md-7" style="text-align:right;">
                        <button type="submit" class="btn btn-success" onClick="generar_salida();" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Salida de Almacén" >Generar Salida </button>
                        <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                            data-placement="bottom" title="Imprimir Guia de Venta" 
                            onClick="imprimir_guia();"><i class="fas fa-print"></i></button>
                        <button type="button" class="btn btn-info" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Salida de Almacén" 
                            onClick="abrir_salida();"><i class="fas fa-file-alt"></i></button>
                        <button type="button" class="btn btn-warning" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Transferencia" 
                            onClick="open_transferencia();"><i class="fas fa-file-alt"></i></button>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Tipo de Documento</h5>
                            <select class="form-control activation js-example-basic-single" name="id_tp_doc_almacen" disabled="true" 
                            onChange="actualiza_titulo();">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_doc_almacen as $prov)
                                    <option value="{{$prov->id_tp_doc_almacen}}">{{$prov->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Serie-Número</h5>
                            <input type="text" class="oculto" name="id_serie_numero">
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="serie" readOnly
                                    placeholder="0000" >
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="numero" readOnly
                                    placeholder="0000000" onBlur="ceros_numero('numero');">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Empresa-Sede</h5>
                            <select class="form-control activation js-example-basic-single" 
                            name="id_sede" disabled="true" onChange="cargar_almacenes();">
                                <option value="0">Elija una opción</option>
                                @foreach ($sedes as $tp)
                                    <option value="{{$tp->id_sede}}">{{$tp->razon_social}} - {{$tp->codigo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Tipo de Operación</h5>
                            <select class="form-control activation js-example-basic-single" name="id_operacion" disabled="true"
                                onChange="valida_tipo_operacion();">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_operacion as $tp)
                                    <option value="{{$tp->id_operacion}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Almacén</h5>
                            <input type="date" class="form-control activation" name="fecha_almacen" value="<?=date('Y-m-d');?>" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén</h5>
                            <select class="form-control activation" name="id_almacen" onChange="direccion();" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->codigo}} - {{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="col-md-5">
                            <h5>Cliente</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_cliente"/>
                                <input class="oculto" name="id_contrib"/>
                                <input type="text" class="form-control" name="cliente_razon_social" placeholder="Seleccione un cliente..." 
                                    aria-describedby="basic-addon1" disabled>
                                <button type="button" class="input-group-text activation btn-primary" id="basic-addon1" onClick="clienteModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button type="button" class="input-group-text activation btn-success" id="basic-addon1" onClick="agregar_cliente();">
                                    <strong>+</strong>
                                </button>
                            </div>
                        </div>                        
                        <div class="col-md-3">
                            <h5>Responsable</h5>
                            <select class="form-control activation js-example-basic-single" 
                                name="usuario" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-3">
                            <h5>Motivo del Traslado</h5>
                            <select class="form-control activation js-example-basic-single" name="id_motivo" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($motivos as $mot)
                                    <option value="{{$mot->id_motivo}}">{{$mot->descripcion}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Transportista</h5>
                            <select class="form-control activation js-example-basic-single" 
                                name="transportista" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($proveedores as $prov)
                                    <option value="{{$prov->id_proveedor}}">{{$prov->nro_documento}} - {{$prov->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Guía Transportista Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="tra_serie" 
                                    placeholder="000">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="tra_numero"
                                    placeholder="000000" onBlur="ceros_numero('tra_numero');">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Traslado</h5>
                            <input type="date" class="form-control activation" name="fecha_traslado" value="<?=date('Y-m-d');?>" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Punto de Partida</h5>
                            <input type="text" class="form-control activation" name="punto_partida">
                        </div>
                        <div class="col-md-5">
                            <h5>Punto de Llegada</h5>
                            <input type="text" class="form-control activation" name="punto_llegada">
                        </div>
                        <div class="col-md-3">
                            <h5>Marca/Modelo/Placa</h5>
                            <input type="text" class="form-control activation" name="placa">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                        </div>
                        <div class="col-md-5">
                            <h5 id="registrado_por">Registrado por: <label></label></h5>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="cod_estado" hidden/>
                            <h5 id="estado">Estado: <label></label></h5>
                        </div>
                    </div>
                </form>
            </section>
            <section id="detalle" hidden>
                <form id="form-detalle" type="register">
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-importes"><legend><h6>Documentos Relacionados</h6></legend>
                                <table id="oc" class="table-group">
                                    <thead>
                                        <tr>
                                            <td colSpan="7">
                                                <div style="width: 100%; display:flex;">
                                                    <div style="width:30%;">
                                                        <select class="form-control js-example-basic-single" name="tipo" onChange="onChangeTipo();">
                                                            <option value="0" disabled>Seleccione un tipo</option>
                                                            <option value="1" selected>Guía de Compra</option>
                                                            <option value="2">Requerimiento</option>
                                                            {{-- <option value="3">Orden de Compra Cliente</option> --}}
                                                            <option value="3">Comprobante de Pago Venta</option>
                                                        </select>
                                                    </div>
                                                    <div style="width:60%;">
                                                        <select class="form-control js-example-basic-single" name="docs_sustento">
                                                        </select>
                                                    </div>
                                                    <div style="width:10%;">
                                                        <button type="button" class="btn btn-success boton"  
                                                            style="padding:5px;height:29px;width:100px;font-size:12px;" 
                                                            data-toggle="tooltip" data-placement="bottom" title="Agregar"
                                                            onClick="agrega_sustento();">
                                                            Agregar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <th width="10%">Código</th>
                                            <th width="10%">Fecha Emisión</th>
                                            <th width="40%">Proveedor</th>
                                            <th>Condición</th>
                                            <th>Fecha Entrega</th>
                                            <th>Lugar Entrega</th>
                                            <th>Acción</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-importes"><legend><h6>Items de la Guía de Venta</h6></legend>
                                <table class="table-group" width="100%"
                                    id="listaDetalle">
                                    <thead style="color:white;">
                                        <tr>
                                            <th width='10%'>Doc Nro.</th>
                                            <th width='10%'>Código</th>
                                            <th width='40%'>Descripción</th>
                                            <th>Posición</th>
                                            <th width='10%'>Cant.</th>
                                            <th>Unid.</th>
                                            {{-- <th>Unit.</th>
                                            <th>Total</th> --}}
                                            <th width='5%'>
                                                <i class="fas fa-plus-square icon-tabla green boton" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Producto" onClick="productoModal();"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@include('almacen.guias.guia_ventaModal')
@include('almacen.guias.guia_venta_oc')
@include('almacen.guias.guia_ven_series')
@include('almacen.guias.seriesModal')
@include('almacen.guias.guia_ven_obs')
@include('almacen.transferencias.transferencia')
@include('almacen.producto.productoModal')
@include('logistica.cotizaciones.clienteModal')
@include('logistica.ordenes.occModal')
@include('proyectos.variables.add_cliente')
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/guia/guia_venta.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_venta_oc.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_venta_detalle.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_ventaModal.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_ven_series.js')}}"></script>
    <script src="{{ asset('js/almacen/variables/seriesModal.js')}}"></script>
    <script src="{{ asset('js/almacen/transferencias/transferencia.js')}}"></script>
    <script src="{{ asset('js/almacen/producto/productoModal.js')}}"></script>
    <script src="{{ asset('js/logistica/clienteModal.js')}}"></script>
    <script src="{{ asset('js/logistica/occModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/variables/add_cliente.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection