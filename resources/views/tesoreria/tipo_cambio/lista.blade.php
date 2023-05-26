@extends('layout.main')
@include('layout.menu_tesoreria')

@section('cabecera') Tipo de Cambio @endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Tesorería</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="tipo_cambio">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Lista de Tipos de Cambio</h3>
        </div>
        <div class="box-body">
            <div class="col-md-8 col-md-offset-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered table-condensed" style="font-size: smaller;" id="tabla">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Compra</th>
                                        <th>Venta</th>
                                        <th>Promedio</th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-data" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modal-data">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formulario" method="POST">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id" value="0">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title" id="title"></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Compra</h5>
                            <input type="number" name="compra" class="form-control input-sm text-center" step="any" min="0" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <h5>Venta</h5>
                            <input type="number" name="venta" class="form-control input-sm text-center" step="any" min="0" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <h5>Promedio</h5>
                            <input type="number" name="promedio" class="form-control input-sm text-center" step="any" min="0" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success shadow-none">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('js/util.js')}}"></script>
    <script>
        // let csrf_token = '{{ csrf_token() }}';
        let vardataTables = funcDatatables();
        $(document).ready(function() {
            listar();

            $("#formulario").on("submit", function() {
                var data = $(this).serializeArray();
                data.push({_token: csrf_token});

                $.ajax({
                    type: "POST",
                    url : $(this).attr('action'),
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        if (response.response == 'ok') {
                            $('#modal-data').modal('hide');
                            $('#tabla').DataTable().ajax.reload(null, false);
                        }
                        Util.notify(response.alert, response.message);
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });
        });

        function listar() {
            var $tabla = $('#tabla').DataTable({
                dom: 'frtip',
                pageLength: 20,
                language: vardataTables[0],
                serverSide: true,
                initComplete: function (settings, json) {
                    const $filter = $('#tabla_filter');
                    const $input = $filter.find('input');
                    $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><i class="fas fa-search"></i></button>');
                    $input.off();
                    $input.on('keyup', (e) => {
                        if (e.key == 'Enter') {
                            $('#btnBuscar').trigger('click');
                        }
                    });
                    $('#btnBuscar').on('click', (e) => {
                        $tabla.search($input.val()).draw();
                    });
                },
                drawCallback: function (settings) {
                    $('#tabla_filter input').prop('disabled', false);
                    $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
                    $('#tabla_filter input').trigger('focus');
                },
                order: [[0, 'desc']],
                ajax: {
                    url: "{{ route('tesoreria.tipo-cambio.listar') }}",
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrf_token}
                },
                columns: [
                    {data: 'fecha', className: 'text-center'},
                    {data: 'compra', className: 'text-right'},
                    {data: 'venta', className: 'text-right'},
                    {data: 'promedio', className: 'text-right'},
                    {data: 'accion', orderable: false, searchable: false, className: 'text-center'}
                ]
            });
            $tabla.on('search.dt', function() {
                $('#tabla_filter input').attr('disabled', true);
                $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
            });
            $tabla.on('init.dt', function(e, settings, processing) {
                $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
            });
            $tabla.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
                } else {
                    $(e.currentTarget).LoadingOverlay("hide", true);
                }
            });
        }

        function editar(id) {
            $.ajax({
                type: 'POST',
                url: "{{ route('tesoreria.tipo-cambio.editar') }}",
                data: {
                    _token: csrf_token,
                    id: id,
                },
                dataType: 'JSON',
                success: function (response) {
                    var datax = response[0];
                    $('[name=id]').val(datax.id_tp_cambio);
                    $('[name=compra]').val(datax.compra);
                    $('[name=venta]').val(datax.venta);
                    $('[name=promedio]').val(datax.promedio);
                    $('#title').text('Editar Tipo de Cambio');
                    $('#formulario').attr('action', "{{ route('tesoreria.tipo-cambio.guardar') }}");
                    $('#modal-data').modal('show');
                }
            }).fail( function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    </script>
@endsection