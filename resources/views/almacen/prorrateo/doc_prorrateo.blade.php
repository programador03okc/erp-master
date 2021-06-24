@extends('layout.main')
@include('layout.menu_almacen')

@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Prorrateo de Costos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
  <li>Movimientos</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="prorrateo">
    <!-- <div class="row"> -->
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;">

                <form id="form-prorrateo" type="register"  form="formulario">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaProrrateos">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo de Prorrateo</th>
                                        <th>Serie-Número</th>
                                        <th>Fecha Emisión</th>
                                        <th>Mnd</th>
                                        <th>Total</th>
                                        <th>Tipo Cambio</th>
                                        <th>Importe</th>
                                        <th>Importe Aplicado</th>
                                        <th width="10%">
                                            <i class="fas fa-plus-square icon-tabla green boton" 
                                                data-toggle="tooltip" data-placement="bottom" 
                                                title="Agregar Documento de Prorrateo" onClick="open_doc_prorrateo();"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="borde-group-verde">
                                <h4 style="margin:0;">Guía de Remisión</h4>
                                <table width="100%">
                                    <tr height="20px">
                                        <td></td>
                                        <td>Moneda</td>
                                        <td width="20">:</td>
                                        <td style="color: #398439;"><label id="moneda"></label></td>
                                        <td>Total</td>
                                        <td width="20">:</td>
                                        <td width="130"><input type="number" class="form-control right" name="total_suma" readOnly/></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="borde-group-rojo">
                                <h4 style="margin:0;">Costo Adicional Total</h4>
                                <table width="100%">
                                    <tr>
                                        <td></td>
                                        <td>Prorrateo Global</td>
                                        <td width="20">:</td>
                                        <td width="130"><input type="number" class="form-control right" name="total_comp" readOnly/></td>
                                        <td class="right">Prorrateo por Items</td>
                                        <td width="20">:</td>
                                        <td width="130"><input type="number" class="form-control right" name="total_items" readOnly/></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaDetalleProrrateo">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th width='10%'>Guía</th>
                                        <th width='5%'>Código</th>
                                        <th width='5%'>Part Number</th>
                                        <th width='30%'>Descripción</th>
                                        <th>Cant.</th>
                                        <th>Unid.</th>
                                        <th>Valor Compra</th>
                                        <th>Adicional</th>
                                        <th>Importe Prorrateado</th>
                                        <th>
                                            <i class="fas fa-plus-square icon-tabla green boton " 
                                                data-toggle="tooltip" data-placement="bottom" 
                                                title="Agregar Guia Compra" onClick="guia_compraModal();"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colSpan="7" class="right">
                                            <button type="button" class="btn-success" title="Copiar Precio Unitario" onClick="copiar_unitario();">
                                            <strong>Guardar Unitarios</strong></button>
                                        </td>
                                        <td><input type="text" class="form-control right" readOnly name="total_suma"/></td>
                                        <td><input type="text" class="form-control right" readOnly name="total_adicional"/></td>
                                        <td><input type="text" class="form-control right" readOnly name="total_costo"/></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@include('almacen.prorrateo.doc_prorrateo_create')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('almacen.guias.guia_compraModal')

@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/prorrateo/doc_prorrateo_create.js')}}"></script>
    <script src="{{ asset('js/logistica/proveedorModal.js')}}"></script>
    <script src="{{ asset('js/logistica/add_proveedor.js')}}"></script>
    <script src="{{ asset('js/almacen/guia/guia_compraModal.js')}}"></script>

@endsection