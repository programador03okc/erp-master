@extends('layout.main')
@include('layout.menu_gerencial')

@section('cabecera') Fondos y Auspicios @endsection

@section('estilos')
    <link href='{{ asset("template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css") }}' rel="stylesheet" type="text/css" />
    <style>
        .group-okc-ini {
            display: flex;
            justify-content: start;
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('gerencial.index')}}"><i class="fas fa-tachometer-alt"></i> Gerencial</a></li>
    <li>Cobranzas</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="cobranza-fondo">
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">Lista de cobranza de fondos y auspicios</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-striped table-hover" id="tabla" width="100%">
                            <thead>
                                <tr>
                                    <th width="80">Fecha</th>
                                    <th>Tipo</th>
                                    <th>Negocio</th>
                                    <th>Nombre de la entidad</th>
                                    <th>CLAIM</th>
                                    <th>Moneda</th>
                                    <th>Importe</th>
                                    <th>Documento</th>
                                    <th>Forma de pago</th>
                                    <th width="100">Plazos</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th width="70"></th>
                                </tr>
                            </thead>
                            <tbody id="resultado"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFondo" tabindex="-1" role="dialog" aria-labelledby="modal-fondo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formulario" method="POST" autocomplete="off">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" value="0">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <h6>Fecha de solicitud</h6>
                            <input type="date" name="fecha_solicitud" class="form-control input-sm text-center" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <h6>Periodo</h6>
                            <select name="periodo_id" class="form-control input-sm" required>
                                <option value="" selected disabled>Elija una opción</option>
                                @foreach ($periodos as $periodo)
                                    <option value="{{ $periodo->id_periodo }}" {{ $periodo->descripcion == date('Y') ? 'selected' : '' }}>{{ $periodo->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h6>Tipo de gestión</h6>
                            <select name="tipo_gestion_id" class="form-control input-sm" required>
                                <option value="" selected disabled>Elija una opción</option>
                                @foreach ($tipoGestion as $tipog)
                                    <option value="{{ $tipog->id }}">{{ $tipog->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h6>Tipo de negocio</h6>
                            <select name="tipo_negocio_id" class="form-control input-sm" required>
                                <option value="" selected disabled>Elija una opción</option>
                                @foreach ($tipoNegocio as $tipon)
                                    <option value="{{ $tipon->id }}">{{ $tipon->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Entidad</h6>
                            <select name="cliente_id" class="selectpicker" title="Elija una entidad" data-live-search="true" data-width="100%" data-actions-box="true" data-size="10" required>
                                @foreach ($clientes as $cliente)
                                    @if (isset($cliente['contribuyente']))
                                        @if ($cliente['contribuyente']['nro_documento'] != null || $cliente['contribuyente']['nro_documento'] != '')
                                            <option value="{{ $cliente->id_cliente }}">[{{ $cliente['contribuyente']['nro_documento'] }}] - {{ $cliente['contribuyente']['razon_social'] }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <h6>Forma de pago</h6>
                            <select name="forma_pago_id" class="form-control input-sm" required>
                                <option value="" selected disabled>Elija una opción</option>
                                @foreach ($formaPago as $forma)
                                    <option value="{{ $forma->id }}">{{ $forma->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Importe</h6>
                                <div class="group-okc-ini">
                                    <select name="moneda_id" class="form-control input-sm" style="width: 50%;">
                                        @foreach ($monedas as $moneda)
                                            <option value="{{ $moneda->id_moneda }}">{{ $moneda->codigo_divisa }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="importe" class="form-control input-sm numero" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6>Nro documento</h6>
                            <input type="text" name="nro_documento" class="form-control input-sm text-center" placeholder="nro del documento" required>
                        </div>
                        <div class="col-md-3">
                            <h6>Responsable</h6>
                            <select name="responsable_id" class="selectpicker" title="Elija un responsable" data-live-search="true" data-width="100%" data-actions-box="true" data-size="5" required>
                                @foreach ($responsables as $resp)
                                    @if ($resp->nombre_corto != null || $resp->nombre_corto != '')
                                        <option value="{{ $resp->id_usuario }}">{{ $resp->nombre_corto }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6>Nombre del pagador (Entidad/Marca)</h6>
                            <input type="text" name="pagador" class="form-control input-sm" placeholder="Ingrese nombre de la entidad" required>
                        </div>
                        <div class="col-md-3">
                            <h6>CLAIM</h6>
                            <input type="text" name="claim" class="form-control input-sm" placeholder="Ingrese código CLAIM" required>
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Fecha de inicio</h6>
                                    <input type="date" name="fecha_inicio" class="form-control input-sm text-center" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <h6>Fecha de vencimiento</h6>
                                    <input type="date" name="fecha_vencimiento" class="form-control input-sm text-center" value="{{ date('Y-m-d', strtotime(date('Y-m-d').'+ 1 month')) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Motivo de la solicitud</h6>
                            <textarea name="detalles" class="form-control" rows="3" placeholder="Escriba el motivo de la solicitud" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-pill btn-default shadow-none" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-pill btn-success shadow-none">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalControl" tabindex="-1" role="dialog" aria-labelledby="modal-control">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="formulario-cobro" method="POST" autocomplete="off">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="cobranza_fondo_id" value="0">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5>Cerrar cobranza</h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Fecha de cobro</h6>
                            <input type="date" name="fecha_cobranza" class="form-control input-sm text-center" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Nro documento</h6>
                            <input type="text" name="nro_documento_cobro" class="form-control input-sm text-center" placeholder="nro del documento" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Observaciones del cobro</h6>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Escriba las observaciones del cobro" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-pill btn-default shadow-none" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-pill btn-success shadow-none">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    <script src="{{ asset('js/util.js') }}"></script>

    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script>
        // let csrf_token = '{{ csrf_token() }}';
        const idioma = {
            sProcessing: "<div class='spinner'></div>",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sInfoPostFix: "",
            sSearch: "Buscar:",
            sUrl: "",
            sInfoThousands: ",",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            },
            oAria: {
                sSortAscending:
                    ": Activar para ordenar la columna de manera ascendente",
                sSortDescending:
                    ": Activar para ordenar la columna de manera descendente"
            }
        };

        $(document).ready(function() {
            $('.main-header nav.navbar.navbar-static-top').find('a.sidebar-toggle').click()
            seleccionarMenu(window.location);
            $('.numero').number(true, 2);
        });
    </script>
    <script src="{{ asset('js/gerencial/cobranza/rc_fondos.js') }}?v={{ filemtime(public_path('js/gerencial/cobranza/rc_fondos.js')) }}"></script>
@endsection
