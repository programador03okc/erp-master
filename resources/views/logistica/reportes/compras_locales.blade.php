@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
@endsection

@section('cabecera')
Reporte de compras locales
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Reportes</li>
    <li class="active">Compras locales</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="reporte_compras_locales">
    @if (in_array(276,$array_accesos)||in_array(277,$array_accesos))
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <div class="box box-widget">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" id="listaComprasLocales">
                                <thead>
                                    <tr>
                                        <th style="text-align:center; width:10%;">Cod. Ord.</th>
                                        <th style="text-align:center; width:10%;">Cod. Req.</th>
                                        <th style="text-align:center; width:10%;">Cod. prod.</th>
                                        <th style="text-align:center; width:10%;">Bien comprado/ servicio contratado</th>
                                        <th style="text-align:center; width:10%;">Rubro Proveedor</th>
                                        <th style="text-align:center; width:10%;">Razón Social del Proveedor</th>
                                        <th style="text-align:center; width:5%;">RUC del Proveedor</th>
                                        <th style="text-align:center; width:10%;">Domicilio Fiscal/Principal</th>
                                        <th style="text-align:center; width:10%;">Provincia</th>
                                        <th style="text-align:center; width:5%;">Fecha de presentación del comprobante de pago.</th>
                                        <th style="text-align:center; width:5%;">Fecha de cancelación del comprobante de pago</th>
                                        <th style="text-align:center; width:5%;">Tiempo de cancelación(# días)</th>
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


<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtro-reporte-compra-locales" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width: 38%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="font-weight:bold;">Filtros</h3>
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
                                        <label title="Empresa">
                                            <input type="checkbox" name="chkEmpresa"> Empresa
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleChangeFiltroEmpresa handleUpdateValorFiltro" name="empresa" readOnly>
                                        @foreach ($empresas as $emp)
                                        <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Sede">
                                            <input type="checkbox" name="chkSede"> Sede
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleUpdateValorFiltro" name="sede" readOnly>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Sede">
                                            <input type="checkbox" name="chkGrupo"> Grupo
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleUpdateValorFiltro" name="grupo" readOnly>
                                    @foreach ($grupos as $grupo)
                                        <option value="{{$grupo->id_grupo}}">{{$grupo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Proyecto">
                                            <input type="checkbox" name="chkProyecto"> Proyecto
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleUpdateValorFiltro" name="proyecto" readOnly>
                                    @foreach ($proyectos as $proyecto)
                                        <option value="{{$proyecto->id_proyecto}}">{{$proyecto->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Estado de Pago">
                                            <input type="checkbox" name="chkEstadoPago"> Estado pago
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm handleUpdateValorFiltro" name="estadoPago" readOnly>
                                    @foreach ($estadosPago as $estado)
                                        <option value="{{$estado->id_requerimiento_pago_estado}}">{{$estado->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--  <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Observación en orden">
                                            <input type="checkbox" name="chkObservacionOrden"> Coincidencia en observacion de orden
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control handleUpdateValorFiltro" name="observacionOrden" value="COMPRA LOCAL" readOnly>

                                </div>
                            </div>  -->
                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Fecha de creación">
                                            <input type="checkbox" name="chkFechaRegistro"> Fecha presentacion
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-4">
                                    <input type="date" name="fechaRegistroDesde" class="form-control handleUpdateValorFiltro" readOnly>
                                    <small class="help-block">Desde (dd-mm-aaaa)</small>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" name="fechaRegistroHasta" class="form-control handleUpdateValorFiltro" readOnly>
                                    <small class="help-block">Hasta (dd-mm-aaaa)</small>
                                </div>
                            </div>

                            {{-- --- --}}
                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="Fecha de cancelacion">
                                            <input type="checkbox" name="chkFechaCancelacion"> Fecha de cancelación
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-4">
                                    <input type="date" name="fechaCancelacionDesde" class="form-control handleUpdateValorFiltro" readOnly>
                                    <small class="help-block">Desde (dd-mm-aaaa)</small>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" name="fechaCancelacionHasta" class="form-control handleUpdateValorFiltro" readOnly>
                                    <small class="help-block">Hasta (dd-mm-aaaa)</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">
                                    <div class="checkbox">
                                        <label title="razon_social_proveedor">
                                            <input type="checkbox" name="chkRazonSocialProveedor"> Razon social del proveedor
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="razon_social_proveedor" class="form-control handleUpdateValorFiltro" readOnly>
                                    <small class="help-block">razon social del proveedor</small>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" class="close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@include('logistica.reportes.modal_lista_adjuntos')

@endsection

@section('scripts')
    <script src="{{ asset('js/util.js')}}"></script>
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>

    <script src="{{('/js/logistica/reportes/comprasLocales.js')}}?v={{filemtime(public_path('/js/logistica/reportes/comprasLocales.js'))}}"></script>
    
    <script>
        var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
        $(document).ready(function() {
            seleccionarMenu(window.location);
            const comprasLocales = new ComprasLocales();
            comprasLocales.mostrar('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 3 , 'SIN_FILTRO', 6);

                comprasLocales.ActualParametroGrupo = 3;
                comprasLocales.ActualParametroEstadoPago = 6;

            comprasLocales.initializeEventHandler();
        });
    </script>
@endsection


