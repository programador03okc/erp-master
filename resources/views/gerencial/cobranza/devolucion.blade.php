@extends('layout.main')
@include('layout.menu_gerencial')

@section('cabecera') Devolución de penalidades @endsection

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
            <h3 class="box-title">Lista de cobranza de devolución de penalidades</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-striped table-hover" id="tabla" width="100%">
                            <thead>
                                <tr>
                                    <th width="10">Emp</th>
                                    <th width="120">OCAM</th>
                                    <th width="200">Nombre del Cliente</th>
                                    <th width="50">Fact.</th>
                                    <th width="40">OC Fisica</th>
                                    <th width="30">SIAF</th>
                                    <th width="60">Gestión</th>
                                    <th>Pagador</th>
                                    <th width="10">Mon</th>
                                    <th width="100">Monto Pen.</th>
                                    <th width="100">Monto Cobrado</th>
                                    <th>Detalles</th>
                                    <th width="30">Estado</th>
                                    <th width="80"></th>
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

<div class="modal fade" id="modalDevolucion" tabindex="-1" role="dialog" aria-labelledby="modal-devolucion">
    <div class="modal-dialog modal-sm" role="document">
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
                        <div class="col-md-12">
                            <h6>Nombre del pagador (Entidad/Marca)</h6>
                            <input type="text" name="pagador_dev" class="form-control input-sm" placeholder="Ingrese nombre de la entidad">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Importe cobrado</h6>
                            <input type="text" name="importe_cobro_dev" class="form-control input-sm text-right numero" placeholder="0.00">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Observaciones de la devolución</h6>
                            <textarea name="motivo_dev" class="form-control" rows="3" placeholder="Escriba las observaciones"></textarea>
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
                <input type="hidden" name="cobranza_penalidad_id" value="0">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5>Cerrar cobranza</h5>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Fecha de cobro</h6>
                            <input type="date" name="fecha_cobro" class="form-control input-sm text-center" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Nro documento</h6>
                            <input type="text" name="nro_documento" class="form-control input-sm" placeholder="Ingrese nro del documento" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Pagador</h6>
                            <input type="text" name="pagador" class="form-control input-sm" placeholder="Ingrese nombre de la entidad" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Importe cobrado</h6>
                            <input type="text" name="importe_cobro" class="form-control input-sm text-right numero" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Observaciones del cobro</h6>
                            <textarea name="motivo" class="form-control" rows="3" placeholder="Escriba las observaciones del cobro" required></textarea>
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
    <script src="{{ asset('js/gerencial/cobranza/rc_devolucion.js') }}?v={{ filemtime(public_path('js/gerencial/cobranza/rc_devolucion.js')) }}"></script>
@endsection