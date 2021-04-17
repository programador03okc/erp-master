@extends('layout.main')
@include('layout.menu_almacen')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Documento de Compra
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Comprobantes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')
<div class="page-main" type="doc_compra">
    <!-- <legend class="mylegend">
        <ol class="breadcrumb">
            <li><label id="tp_documento"></label></li>
            <li><label id="serie"></label></li>
            <li><label id="numero"></label>
        </ol>
    </legend> -->
    <!-- <div class="row">
            <div class="col-md-12"> -->
                <div class="panel-body" style="padding:0px;">
                    <fieldset class="group-table">
                    <!-- form cabecera -->
                        <form id="form-doc_compra" type="register" form="formulario">
                            <input type="text" class="oculto" name="id_doc_com" primary="ids">
                            <input type="text" class="oculto" name="id_guia_com">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Serie-Número</h5>
                                        <div class="input-group">
                                            <input type="text" class="form-control activation" name="serie" 
                                                placeholder="F001" disabled="true">
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="form-control activation" name="numero"
                                                placeholder="000000" disabled="true" onChange="ceros_numero();">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Tipo de Documento</h5>
                                        <select class="form-control activation js-example-basic-single" 
                                            name="id_tp_doc" disabled="true">
                                            <option value="0">Elija una opción</option>
                                            @foreach ($tp_doc as $tp)
                                                <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Emisión</h5>
                                        <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Condición</h5>
                                        <div style="display:flex;">
                                            <select class="form-control group-elemento activation" name="id_condicion" 
                                                onChange="change_dias();" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($condiciones as $cond)
                                                    <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" name="credito_dias" class="form-control activation group-elemento" style="text-align:right;" disabled/>
                                            <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Proveedor</h5>
                                        <div style="display:flex;">
                                            <input class="oculto" name="id_proveedor"/>
                                            <input class="oculto" name="id_contrib"/>
                                            <input type="text" class="form-control" name="prov_razon_social" placeholder="Seleccione un proveedor..." 
                                                aria-describedby="basic-addon1" disabled="true">
                                            <button type="button" class="input-group-text activation" id="basic-addon1" onClick="proveedorModal();">
                                                <i class="fa fa-search"></i>
                                            </button>
                                            <button type="button" class="btn-primary activation" title="Agregar Proveedor" onClick="agregar_proveedor();">
                                                <i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Fecha de Vencimiento</h5>
                                        <input type="date" class="form-control activation" name="fecha_vcmto" value="<?=date('Y-m-d');?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Moneda / Tipo de Cambio</h5>
                                        <div style="display:flex;">
                                            <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" value="S/" readOnly/>
                                            <input type="number" name="tipo_cambio" class="form-control group-elemento" style="text-align: right;"value="3.15248" readOnly/>
                                            <select class="form-control group-elemento activation" name="moneda" disabled="true">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($moneda as $mon)
                                                    <option value="{{$mon->id_moneda}}">{{$mon->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Responsable</h5>
                                        <select class="form-control activation js-example-basic-single" 
                                            name="usuario" disabled="true">
                                            <option value="0">Elija una opción</option>
                                            @foreach ($usuarios as $usu)
                                                <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- <input type="button" class="btn btn-primary" onClick="getTipoCambio();" value="Tipo Cambio"/> --}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 id="registrado_por">Registrado por: <label></label></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="oculto" name="cod_estado">
                                        <h5 id="estado">Estado: <label></label></h5>
                                    </div>
                                </div>
                        </form>
                    <!-- end form cabecera -->
                    </fieldset>
                    <br>
                    <fieldset class="group-table">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Guía(s) de Remisión</h6>
                                <table id="ListaGuiaRemision" class="mytable table table-condensed table-bordered table-okc-view dataTable no-footer" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Guía Nro</th>
                                            <th>Fecha Emisión</th>
                                            <th>Proveedor</th>
                                            <th>Tp Operación</th>
                                            <th width="10%">
                                            <button type="button" class="btn-success" title="Agregar Guía" name="btnAgregarGuia" onClick="guia_compraModal();" disabled="disabled">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Orden de Compra</h6>
                                <table id="ordenes" class="mytable table table-condensed table-bordered table-okc-view dataTable no-footer" width="100%">
                                    <thead>
                                        <tr>
                                            <th hidden>Id Orden</th>
                                            <th>Código Orden</th>
                                            <th>Fecha Emisión</th>
                                            <th>Proveedor</th>
                                            <!-- <th>Tp Operación</th> -->
                                            <th width="10%">
                                                <button type="button" class="btn-success" title="Agregar Orden" name="btnAgregarOrden" onClick="orden_compraModal();" disabled="disabled">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="group-importes">
                                    <legend><h6>Items del Documento de Compra</h6></legend>
                                    <table class="mytable table table-condensed table-bordered table-okc-view dataTable no-footer" width="100%" id="listaDetalleComprobanteCompra">
                                        <thead>
                                            <tr>
                                                <th>Guía Nro.</th>
                                                <th>Código</th>
                                                <th>Descripción</th>
                                                <th>Cantidad</th>
                                                <th>Unidad</th>
                                                <th>Precio U.</th>
                                                <th>%Dscto</th>
                                                <th>Total Dscto</th>
                                                <th>Precio Total</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </fieldset>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- <button type="button" class="btn btn-secondary" data-toggle="tooltip" 
                                data-placement="bottom" title="pruebs" 
                                onClick="actualiza_totales();">Actualizar</button> -->
                            </div>
                            <div class="col-md-6">
                                <table class="mytable table table-condensed table-bordered table-okc-view dataTable no-footer" width="100%" id="TablaDetalleComprobanteCompra">
                                    <tbody>
                                        <tr>
                                            <td width="50%">SubTotal</td>
                                            <td width="20%"></td>
                                            <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="sub_total" readOnly value="0"/></td>
                                        </tr>
                                        <tr>
                                            <td>Descuentos</td>
                                            <td>
                                                <input type="number" class="porcen activation" name="porcen_dscto" onChange="calcTotalPorcentajeDescuento(event);" value="0"/>
                                                <label>%</label>
                                            </td>
                                            <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="total_dscto" readOnly value="0"/></td>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td></td>
                                            <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="total" readOnly value="0"/></td>
                                        </tr>
                                        <tr>
                                            <td>IGV</td>
                                            <td>
                                                <input type="number" class="porcen" name="porcen_igv" readOnly/>
                                                <label>%</label>
                                            </td>
                                            <td><label name="simbolo_moneda"></label><input type="number" class="importe" name="total_igv" readOnly value="0"/></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><strong>Importe Total</strong></td>
                                            <td></td>
                                            <td><label name="simbolo_moneda"></label> <input type="number" class="importe" name="total_a_pagar" readOnly value="0"/></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </div>
            <!-- </div>
        </div> -->
</div>
@include('logistica.comprobantes.doc_compraModal')
@include('logistica.comprobantes.orden_compraModal')
@include('logistica.comprobantes.detalle_ordenModal')
@include('logistica.comprobantes.doc_com_detalle')
@include('almacen.guias.guia_compraModal')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@endsection

@section('scripts')
    <script>
    var igv = {!! json_encode($igv) !!};
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='porcen_igv']").value = igv.porcentaje;

    </script>

    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{('/js/logistica/comprobantes/doc_compra.js')}}"></script>
    <script src="{{('/js/logistica/comprobantes/doc_compraModal.js')}}"></script>
    <script src="{{('/js/logistica/comprobantes/orden_compraModal.js')}}"></script>
    <script src="{{('/js/logistica/comprobantes/doc_com_detalle.js')}}"></script>
    <script src="{{('/js/logistica/proveedorModal.js')}}"></script>
    <script src="{{('/js/logistica/add_proveedor.js')}}"></script>
    <script src="{{('/js/almacen/guia/guia_compraModal.js')}}"></script>

@endsection