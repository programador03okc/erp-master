@extends('layout.main')
@include('layout.menu_almacen')

@section('cabecera')
Saldos Actuales
@endsection

@section('estilos')
    <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
    <style>
        button.botones {
            margin-top: 35px;
        }
        table {
            font-size: smaller;
        }
        table.table-bordered.dataTable tbody td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li>Reportes</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

@if (in_array(159,$array_accesos) || in_array(160,$array_accesos) || in_array(161,$array_accesos))
<div class="page-main" type="saldos">
    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="formulario" method="POST">
                        @csrf
                        <div class="row">
                            @if (in_array(159,$array_accesos))
                            <div class="col-md-3">
                                <div class="form-group">
                                    <h5>Almacén</h5>
                                    <select name="almacen[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                        @foreach ($almacenes as $item)
                                            <option value="{{ $item->id_almacen }}">{{ $item->codigo }} - {{ $item->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-9">
                                <div class="row">
                                    @if (in_array(159,$array_accesos))
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <h5>Hasta</h5>
                                            <input type="date" name="fecha" class="form-control text-center" value="{{ $fecha->format('Y-m-d') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-block botones btn-success" onclick="procesar();">
                                            <i class="fas fa-search"></i> Procesar
                                        </button>
                                    </div>
                                    @endif
                                    @if (in_array(160,$array_accesos))
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-block botones btn-primary export" onclick="exportar();" disabled>
                                            <i class="fas fa-download"></i> Exportar
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-block botones btn-warning export" onclick="exportarSeries();" disabled>
                                            <i class="fas fa-download"></i> Exportar series
                                        </button>
                                    </div>
                                    @endif
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-block botones btn-info" onclick="exportarAntiguedades();">
                                            <i class="fas fa-download"></i> Antiguedades
                                        </button>
                                    </div>
                                    @if (in_array(161,$array_accesos))
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-block botones btn-danger" onclick="exportarSoftLink();">
                                            <i class="fas fa-download"></i> Stock Valorizado
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="mytable table table-condensed table-bordered table-okc-view" id="tablaSaldos">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cód. SoftLink</th>
                                    <th>Part Number</th>
                                    <th>SubCategoría</th>
                                    <th width="40%">Descripción</th>
                                    <th>Mon</th>
                                    <th>Valorizacion</th>
                                    <th>Costo Promedio</th>
                                    <th>Und</th>
                                    <th>Stock Actual</th>
                                    <th>Reserva</th>
                                    <th>Disponible</th>
                                    <th width="15%">Almacén</th>
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
@else
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger pulse" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            Solicite los accesos
        </div>
    </div>
</div>
@endif
@include('almacen.reportes.verRequerimientoReservas')
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
            seleccionarMenu(window.location);
            vista_extendida();
            listar(1);

            $('.selectpicker').on('change', function(e) {
                $(".export").attr('disabled', true);
            });
        });

        function procesar() {
            var almacen = $(".selectpicker").val();
            var fecha = $("[name=fecha]").val();
            $(".export").removeAttr('disabled');

            if (almacen.length > 0) {
                $.ajax({
                    type: "POST",
                    url : "{{ route('almacen.reportes.saldos.filtrar') }}",
                    data: {almacen: almacen, fecha: fecha},
                    dataType: "JSON",
                    success: function (response) {
                        listar(2);
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            } else {
                Util.notify("info", "Debe seleccionar un almacén como mínimo");
            }
        }

        function listar(type) {
            var $tabla = $('#tablaSaldos').DataTable({
                dom: 'frtip',
                pageLength: 30,
                language: vardataTables[0],
                destroy: true,
                serverSide: true,
                initComplete: function (settings, json) {
                    const $filter = $('#tablaSaldos_filter');
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
                    $('#tablaSaldos_filter input').prop('disabled', false);
                    $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
                    $('#tablaSaldos_filter input').trigger('focus');
                },
                order: [[3, 'asc']],
                ajax: {
                    url: "{{ route('almacen.reportes.saldos.listar') }}",
                    method: 'POST',
                    data: {type: type},
                    headers: {'X-CSRF-TOKEN': csrf_token}
                },
                columns: [
                    {data: 'codigo'},
                    {data: 'cod_softlink'},
                    {data: 'part_number'},
                    {data: 'categoria'},
                    {data: 'producto'},
                    {data: 'simbolo', className: 'text-center'},
                    {data: 'valorizacion', className: 'text-center',searchable: false},
                    {data: 'costo_promedio', className: 'text-center',searchable: false},
                    {data: 'abreviatura', className: 'text-center',searchable: false},
                    {data: 'stock', className: 'text-center', searchable: false, orderable: true},
                    {data: 'reserva', className: 'text-center', searchable: false, orderable: true},
                    {data: 'disponible', className: 'text-center', searchable: false, orderable: true},
                    {data: 'almacen_descripcion'}
                ],
                columnDefs: [
                    // {
                    //     render: function (data, type, row) {
                    //         return $.number(row['valorizacion'], 2, '.', ',');
                    //     },targets: 6
                    // },
                    // {
                    //     render: function (data, type, row) {
                    //         return $.number(row['costo_promedio'], 2, '.', ',');
                    //     },targets: 7
                    // },
                    {
                        render: function (data, type, row) {
                            return `
                            <a class="label label-danger"
                                onclick="verRequerimiento(`+ row['id_producto'] +`, `+ row['id_almacen'] +`);" style="font-size: 11px">`+ row['reserva'] +`
                            </a>`;
                        },targets: 10
                    }

                ]
            });
            $tabla.on('search.dt', function() {
                $('#tablaSaldos_filter input').attr('disabled', true);
                $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
            });
            $tabla.on('init.dt', function(e, settings, processing) {
                $('#tablaSaldos tbody').LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
            });
            $tabla.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#tablaSaldos tbody').LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
                } else {
                    $('#tablaSaldos tbody').LoadingOverlay("hide", true);
                }
            });
        }

        function verRequerimiento(id, almacen) {
            $('#nombreEstado').text('Requerimientos que generan la Reserva');
            $('#modal-verRequerimientoEstado').modal('show');
            verRequerimientosReservados(id, almacen);
        }

        function verRequerimientosReservados(id_producto, id_almacen) {
            var baseUrl = 'verRequerimientosReservados/' + id_producto + '/' + id_almacen;
            var html = '';
            var footer = '';
            var total = 0;

            $.ajax({
                type: 'GET',
                url: baseUrl,
                dataType: 'JSON',
                success: function (response) {
                    var datax = response;

                    if (datax.length > 0) {
                        datax.forEach(function (element, index) {
                            html += `
                            <tr id="`+ element.id_requerimiento +`">
                                <td class="text-center">
                                    <label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento(`+ element.id_requerimiento +`)">
                                `+ element.codigo +`</label>
                                </td>
                                <td>`+ element.concepto +`</td>
                                <td class="text-center">`+ element.almacen_descripcion +`</td>
                                <td class="text-center">`+ (element.stock_comprometido !== null ? element.stock_comprometido : 0) +`</td>
                                <td class="text-center">`+ (element.nombre_corto !== null ? element.nombre_corto : '') +`</td>
                                <td class="text-center">`+ (element.guia_com !== null ? element.guia_com : '') +`</td>
                                <td class="text-center">`+ (element.codigo_trans !== null ? element.codigo_trans : '') +`</td>
                                <td class="text-center">`+ (element.codigo_transfor_materia !== null ? element.codigo_transfor_materia :
                                    (element.codigo_transfor_transformado !== null ? element.codigo_transfor_transformado : '')) +`</td>
                            </tr>`;
                            total += parseFloat(element.stock_comprometido);
                        });
                        footer += `<tr><td colspan="3"></td><td class="text-center">`+ total +`</td><td colSpan="4"></td></tr>`;
                    } else {
                        html += '<tr><td colspan="8">No se encontraron requerimientos para reserva</td></tr>';
                    }
                    $('#listaRequerimientosEstado tbody').html(html);
                    $('#listaRequerimientosEstado tfoot').html(footer);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }

        function abrir_requerimiento(id_requerimiento) {
            localStorage.setItem("idRequerimiento", id_requerimiento);
            let url = "/necesidades/requerimiento/elaboracion/index";
            var win = window.open(url, '_blank');
            win.focus();
        }

        function exportar() {
            window.location.href = "{{ route('almacen.reportes.saldos.exportar') }}";
        }

        function exportarSeries() {
            window.location.href = "{{ route('almacen.reportes.saldos.exportarSeries') }}";
        }

        function exportarAntiguedades() {
            window.location.href = "{{ route('almacen.reportes.saldos.exportarAntiguedades') }}";
        }

        function exportarSoftLink() {
            var route = "{{ route('almacen.reportes.saldos.exportar-valorizacion') }}";
            var almacen = $(".selectpicker").val();
            var fecha = $("[name=fecha]").val();

            if (almacen.length > 0) {
                if (almacen.length > 1) {
                    Util.notify("info", "Debe seleccionar solo un almacén");
                } else {
                    var form = $('<form action="' + route + '" method="post" target="_blank">' +
                        '<input type="hidden" name="_token" value="' + csrf_token + '" />' +
                        '<input type="hidden" name="almacen" value="' + almacen + '" />' +
                        '<input type="hidden" name="fecha" value="' + fecha + '" />' +
                    '</form>');
                    $('body').append(form);
                    form.submit();
                }
            } else {
                Util.notify("info", "Debe seleccionar un almacén");
            }
        }
    </script>
@endsection
