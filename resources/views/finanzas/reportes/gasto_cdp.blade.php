@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Lista de gastos CDP
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
                            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaGastoCDP" style="font-size: 1rem;">
                                <thead>
                                    <tr>
                                        <th style="background-color:#787878;">Oportunidad</th>
                                        <th style="background-color:#787878;">Desc. Oportunidad</th>
                                        <th style="background-color:#787878;">Tipo Negocio</th>
                                        <th style="background-color:#787878;">Importe Oportunidad</th>
                                        <th style="background-color:#787878;">Fecha Oportunidad</th>
                                        <th style="background-color:#787878;">Estado Oportunidad</th>
                                        <th style="background-color:#787878;">Part-number</th>
                                        <th style="background-color:#787878;" width="90">Descripción</th>
                                        <th style="background-color:#787878;">P.V.U. O/C (sinIGV) S/ </th>
                                        <th style="background-color:#787878;">Flete O/C (sinIGV) S/ </th>
                                        <th style="background-color:#787878;">Cant.</th>
                                        <th style="background-color:#787878;">Garant. Meses</th>
                                        <th style="background-color:#787878;">Origen Costo</th>
                                        <th style="background-color:#787878;">Proveedor Seleccionado</th>
                                        <th style="background-color:#787878;">Costo Unit.(SinIGV)</th>
                                        <th style="background-color:#787878;">Plazo Prov.</th>
                                        <th style="background-color:#787878;">Flete S/ (SinIGV)</th>
                                        <th style="background-color:#787878;">Fondo Proveedor</th>
                                        <th style="background-color:#787878;">Costo de Compra</th>
                                        <th style="background-color:#787878;">Costo de compra en soles</th>
                                        <th style="background-color:#787878;">Total flete proveedor</th>
                                        <th style="background-color:#787878;">Costo compra + flete</th>
                                        <th style="background-color:#787878;">Creado por</th>
                                        <th style="background-color:#787878;">Fecha creación</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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

<script src="{{ asset('js/finanzas/reportes/gasto_cdp.js') }}"></script>


@endsection