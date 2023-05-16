@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Lista de gastos requerimiento logístico
@endsection

@section('estilos')
<link rel="stylesheet" href="{{asset('template/plugins/select2/select2.min.css')}}">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{route('finanzas.index')}}"><i class="fa fa-usd"></i> Finanzas</a></li>
        <li class="active"> @yield('cabecera')</li>
    </ol>
@endsection

@section('content')
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Listado a nivel de items</h3>
                <div class="box-tools pull-right">
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                    <div class="box box-widget">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaGastoRequerimientoLogistico" style="font-size: 1rem;">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="background-color:#787878;">Prioridad</th>
                                        <th rowspan="2" style="background-color:#787878;">Requerimiento</th>
                                        <th rowspan="2" style="background-color:#787878;">CDP</th>
                                        <th colspan="2" style="background-color:#787878;">Presupuesto anterior</th>
                                        <th colspan="3" style="background-color:#787878;">Partida (ppto. anterior) </th>
                                        <th colspan="2" style="background-color:#787878;">Presupuesto interno</th>
                                        <th colspan="3" style="background-color:#787878;">Partida (ppto, interno) </th>
                                        <th rowspan="2" style="background-color:#787878;">Cod. Padre Centro Costo</th>
                                        <th rowspan="2" style="background-color:#787878;">Des. Padre Centro Costo</th>
                                        <th rowspan="2" style="background-color:#787878;">Cod.Centro Costo</th>
                                        <th rowspan="2" style="background-color:#787878;">Des.Centro Costo</th>
                                        <th rowspan="2" style="background-color:#787878;">Proyecto</th>
                                        <th rowspan="2" style="background-color:#787878;" width="30">Motivo</th>
                                        <th rowspan="2" style="background-color:#787878;" width="80">Concepto</th>
                                        <th rowspan="2" style="background-color:#787878;">Item</th>
                                        <th rowspan="2" style="background-color:#787878;">Tipo Requerimiento</th>
                                        <th rowspan="2" style="background-color:#787878;">Empresa</th>
                                        <th rowspan="2" style="background-color:#787878;">Sede</th>
                                        <th rowspan="2" style="background-color:#787878;">Grupo</th>
                                        <th rowspan="2" style="background-color:#787878;">División</th>
                                        <th colspan="4" style="background-color:#787878;">Totales Item Requerimiento</th>
                                        <th colspan="9" style="background-color:#787878;">Totales Item Orden</th>
                                        <th colspan="10" style="background-color:#787878;">Salida de Almacén</th>
                                        <th rowspan="2" style="background-color:#787878;">Tipo Cambio</th>
                                        <th rowspan="2" style="background-color:#787878;" width="80">Observación</th>
                                        <th rowspan="2" style="background-color:#787878;">Fecha Emisión Req.</th>
                                        <th rowspan="2" style="background-color:#787878;">Fecha Registro</th>
                                        <th rowspan="2" style="background-color:#787878;">Hora Registro</th>
                                        <th rowspan="2" style="background-color:#787878;">Estado Requerimiento</th>
                                    </tr>
                                    <tr>
                                        <th style="background-color:#787878;" width="10">Cod.Prespuesto</th>
                                        <th style="background-color:#787878;" width="30">Des.Prespuesto</th>
                                        <th style="background-color:#787878;" width="30">Partida</th>
                                        <th style="background-color:#787878;" width="10">Cod.sub Partida</th>
                                        <th style="background-color:#787878;" width="20">Des.sub Partida</th>
                                        <th style="background-color:#787878;" width="10">Cod.Prespuesto</th>
                                        <th style="background-color:#787878;" width="30">Des.Prespuesto</th>
                                        <th style="background-color:#787878;" width="20">Partida</th>
                                        <th style="background-color:#787878;" width="10">Cod.sub Partida</th>
                                        <th style="background-color:#787878;" width="20">Des.sub Partida</th>

                                        <th style="background-color:#787878;">Cantidad</th>
                                        <th style="background-color:#787878;">Precio Unitario (Sin IGV)</th>
                                        <th style="background-color:#787878;">Subtotal</th>
                                        <th style="background-color:#787878;">Moneda</th>


                                        <th style="background-color:#787878;">Nro OC</th>
                                        <th style="background-color:#787878;">Cod. O/C</th>
                                        <th style="background-color:#787878;">Cantidad</th>
                                        <th style="background-color:#787878;">Precio Unitario (sin IGV)</th>
                                        <th style="background-color:#787878;">Subtotal</th>
                                        <th style="background-color:#787878;">Moneda</th>
                                        <th style="background-color:#787878;">Subtotal (considera incluir IGV)</th>
                                        <th style="background-color:#787878;">Estado Orden</th>
                                        <th style="background-color:#787878;">Estado Pago</th>
                                        
                                        <th style="background-color:#787878;">Estado Despacho</th>
                                        <th style="background-color:#787878;">Nro Salida int (ODI)</th>
                                        <th style="background-color:#787878;">Nro Salida Ext (ODE)</th>
                                        <th style="background-color:#787878;">Almacén</th>
                                        <th style="background-color:#787878;">Fecha Salida</th>
                                        <th style="background-color:#787878;">Código Salida</th>
                                        <th style="background-color:#787878;">Cant.</th>
                                        <th style="background-color:#787878;">Moneda</th>
                                        <th style="background-color:#787878;">Costo. Unit.</th>
                                        <th style="background-color:#787878;">Costo Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </div>

@endsection

@section('scripts')
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{asset('template/plugins/select2/select2.min.js')}}"></script>

    <script src="{{ asset('js/finanzas/reportes/gasto_requerimiento_logistico.js') }}"></script>

 
@endsection
