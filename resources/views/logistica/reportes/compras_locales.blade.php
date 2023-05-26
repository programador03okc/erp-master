@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera') Reporte de compras locales @endsection

@section('estilos')
    <link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
    <style>
        table tbody tr td {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Reportes</li>
    <li class="active">Compras locales</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="reporte_compras">
    @if (in_array(276,$array_accesos)||in_array(277,$array_accesos))
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <div class="box box-widget">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaCompras">
                                <thead>
                                    <tr>
                                        <th style="text-align:center; width:10%;">Cod. Ord.</th>
                                        <th style="text-align:center; width:10%;">Cod. Req.</th>
                                        <th style="text-align:center; width:10%;">Cod. prod.</th>
                                        <th style="text-align:center; width:10%;">Bien comprado/ servicio contratado</th>
                                        {{--  <th style="text-align:center; width:10%;">Rubro Proveedor</th>  --}}
                                        <th style="text-align:center; width:10%;">Razón Social del Proveedor</th>
                                        <th style="text-align:center; width:5%;">RUC del Proveedor</th>
                                        {{--  <th style="text-align:center; width:10%;">Domicilio Fiscal/Principal</th>  --}}
                                        {{--  <th style="text-align:center; width:10%;">Provincia</th>  --}}
                                        <th style="text-align:center; width:5%;">Fecha de presentación del comprobante de pago.</th>
                                        <th style="text-align:center; width:5%;">Fecha de cancelación del comprobante de pago</th>
                                        <th style="text-align:center; width:5%;">Tiempo de cancelación(nro días)</th>
                                        <th style="text-align:center; width:8%;">Cantidad</th>
                                        <th style="text-align:center; width:10%;">Moneda</th>
                                        <th style="text-align:center; width:10%;">Precio Soles</th>
                                        <th style="text-align:center; width:10%;">Precio Dólares</th>
                                        <th style="text-align:center; width:10%;">Monto Total Soles inc IGV</th>
                                        <th style="text-align:center; width:10%;">Monto Total Dólares inc IGV</th>
                                        <th style="text-align:center; width:10%;">Tipo de Comprobante de Pago</th>
                                        <th style="text-align:center; width:10%;">N° Comprobante de Pago</th>
                                        <th style="text-align:center; width:10%;">Empresa - sede</th>
                                        <th style="text-align:center; width:10%;">Grupo</th>
                                        <th style="text-align:center; width:20%;">Proyecto</th>
                                        <th style="text-align:center; width:5%;">C.L</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger pulse" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only">Error de Accesos:</span>
                Solicite los accesos
            </div>
        </div>
    </div>
    @endif
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtros" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <form id="formulario-filtros" method="POST">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" style="font-weight:bold;">Filtros para la búsqueda</h3>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal" id="formFiltroReporteOrdenesCompra">
                        <div class="form-group">
                            <div class="col-md-12">
                                <small>Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</small>
                            </div>
                        </div>
                        <div class="container-filter" style="margin: 0 auto;">
                            <fieldset class="group-table">
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="Fecha de creación">
                                                <input type="checkbox" name="chkFechaRegistro" @if (session('clFechaRegistroDesde') !== null) checked @endif> Fecha presentacion
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="date" name="fechaRegistroDesde" class="form-control" value="@if (session('ocFiltroFechaPublicacionDesde') !== null){{ session('ocFiltroFechaPublicacionDesde') }}@else{{ date('Y-m-m') }}@endif">
                                        <small class="help-block">Desde (día-mes-año)</small>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="date" name="fechaRegistroHasta" class="form-control" value="@if (session('ocFiltroFechaPublicacionHasta') !== null){{ session('ocFiltroFechaPublicacionHasta') }}@else{{ date('Y-m-d', strtotime(date('Y-m-d').'+1 month')) }}@endif">
                                        <small class="help-block">Hasta (día-mes-año)</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="Fecha de cancelacion">
                                                <input type="checkbox" name="chkFechaCancelacion" @if (session('clFechaCancelacionDesde') !== null) checked @endif> Fecha de cancelación
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="date" name="fechaCancelacionDesde" class="form-control" value="@if (session('clFechaCancelacionDesde') !== null){{ session('clFechaCancelacionDesde') }}@else{{ date('Y-m-m') }}@endif">
                                        <small class="help-block">Desde (día-mes-año)</small>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="date" name="fechaCancelacionHasta" class="form-control" value="@if (session('clFechaCancelacionHasta') !== null){{ session('clFechaCancelacionHasta') }}@else{{ date('Y-m-d', strtotime(date('Y-m-d').'+1 month')) }}@endif">
                                        <small class="help-block">Hasta (día-mes-año)</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="Empresa">
                                                <input type="checkbox" name="chkEmpresa" @if (session('clEmpresa') !== null) checked @endif> Empresa
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="empresa">
                                            @foreach ($empresas as $emp)
                                                <option value="{{ $emp->id_empresa }}" @if (session('clEmpresa') == $emp->id_empresa) selected @else '' @endif>{{ $emp->razon_social }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="Sede">
                                                <input type="checkbox" name="chkGrupo" @if (session('clGrupo') !== null) checked @endif> Grupo
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="grupo">
                                        @foreach ($grupos as $grupo)
                                            <option value="{{ $grupo->id_grupo }}" @if (session('clGrupo') == $grupo->id_grupo) selected @else '' @endif>{{ $grupo->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="Proyecto">
                                                <input type="checkbox" name="chkProyecto" @if (session('clProyecto') !== null) checked @endif> Proyecto
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="proyecto">
                                        @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id_proyecto }}" @if (session('clProyecto') == $proyecto->id_proyecto) selected @else '' @endif>{{ $proyecto->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="Estado de Pago">
                                                <input type="checkbox" name="chkEstadoPago" @if (session('clEstadoPago') !== null) checked @endif> Estado pago
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="estadoPago">
                                        @foreach ($estadosPago as $estado)
                                            <option value="{{ $estado->id_requerimiento_pago_estado }}" @if (session('clEstadoPago') == $estado->id_requerimiento_pago_estado) selected @else '' @endif>{{ $estado->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="razon_social_proveedor">
                                                <input type="checkbox" name="chkRazonSocialProveedor" @if (session('clProveedor') !== null) checked @endif> Proveedor
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="razon_social_proveedor" class="form-control" value="@if (session('clProveedor') !== null){{ session('clProveedor') }}@endif">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">
                                        <div class="checkbox">
                                            <label title="tipo_reporte">
                                                <input type="checkbox" name="chkCompraLocal" @if (session('clTipoReporte') !== null) checked @endif> Tipo de reporte
                                            </label>
                                        </div>
                                    </label>
                                    <div class="col-sm-8">
                                        <select name="tipo_reporte" class="form-control">
                                            <option value="false" @if (session('clTipoReporte') == false) selected @endif>Compras generales</option>
                                            <option value="true" @if (session('clTipoReporte') == true) selected @endif>Compras locales</option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary" class="close" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('logistica.reportes.modal_lista_adjuntos')
@endsection

@section('scripts')
    <script src="{{ asset('js/util.js') }}"></script>
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>

    //<script src="{{('/js/logistica/reportes/comprasLocales.js')}}?v={{filemtime(public_path('/js/logistica/reportes/comprasLocales.js'))}}"></script>
    <script src="{{ ('/js/logistica/reportes/compras.js') }}?v={{ filemtime(public_path('/js/logistica/reportes/compras.js')) }}"></script>

    <script>
        //let csrf_token = '{{ csrf_token() }}';
        let array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
        const idioma = {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate":
            {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria":
            {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        };

        $(document).ready(function() {
            seleccionarMenu(window.location);
            $(".sidebar-mini").addClass("sidebar-collapse");
            const compras = new Compras(csrf_token, array_accesos, idioma);
            compras.listar();
            compras.actualizarFiltros();

            //comprasLocales.mostrar('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 3 , 'SIN_FILTRO', 6);

                //comprasLocales.ActualParametroGrupo = 3;
                //comprasLocales.ActualParametroEstadoPago = 6;

            //comprasLocales.initializeEventHandler();
        });
    </script>
@endsection


