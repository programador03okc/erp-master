@extends('layout.main')
@include('layout.menu_notificacion')

@section('cabecera') Lista de notificaciones @endsection

@section('estilos')
<style>
    table {
        font-size: small;
    }

    table.table td {
        vertical-align: middle !important;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('modulos')}}"><i class="fas fa-envelope"></i> Notificaciones</a></li>
    <li class="active">Lista de notificaciones pendientes</li>
</ol>
@endsection

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <table id="tablaNotificacion" class="table table-condensed">
                    <thead>
                        <tr>
                            <th style="width: 10%" class="text-center">Fecha</th>
                            <th class="text-center">Mensaje</th>
                            <th style="width: 10%" class="text-center">Enlace visitado</th>
                            <th style="width: 10%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('js/util.js')}}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script>
    const vardataTables = funcDatatables();
    var iTableCounter = 1;
    var oInnerTable;
    $(document).ready(function() {
        Util.seleccionarMenu(window.location);

        const $tabla = $('#tablaNotificacion').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            language: vardataTables[0],
            serverSide: true,
            initComplete: function(settings, json) {
                let $filter = $('#tablaNotificacion_filter');
                let $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.unbind();
                $input.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').click(function() {
                    $tabla.search($input.val()).draw();
                });
            },
            drawCallback: function(settings) {
                $('#tablaNotificacion_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tablaNotificacion_filter input').focus();
            },
            order: [
                [0, "desc"]
            ],
            ajax: {
                url: "{{ route('notificaciones.lista-pendientes') }}",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}"
                },

            },
            columns: [{
                    data: 'fecha',
                    searchable: false
                },
                {
                    data: 'mensaje'
                },
                {
                    data: 'leido',
                    searchable: false
                },
            ],
            columnDefs: [{
                    orderable: false,
                    targets: [3]
                },
                {
                    className: "text-center",
                    targets: [0, 2, 3]
                },
                {
                    render: function(data, type, row) {
                        return row.mensaje;
                    },
                    targets: 1
                },
                {
                    render: function(data, type, row) {
                        return (row.leido == 1) ? "Si" : "No";
                    },
                    targets: 2
                },
                {
                    render: function(data, type, row) {
                        var botones = `
                            <button data-id="` + row.id + `" title="Desplegar" class="btn btn-default btn-xs desplegar" data-comentario="${row.comentario}"><span class="fas fa-chevron-down fa-sm"></span></button>
                            <a target="_blank" href="` + "{{ url('/notificaciones/ver') }}/" + row.id + `" title="Ver" class="btn btn-primary btn-xs visitar">
                                <span class="glyphicon glyphicon-eye-open"></span>
                            </a>
                            <button data-id="` + row.id + `" title="Eliminar" class="btn btn-danger btn-xs eliminar"><span class="glyphicon glyphicon-remove"></span></button>
                            `;
                        return botones;
                    },
                    targets: 3
                },
            ],
            buttons: [],
            rowCallback: function(row, data) {
                if (data.leido == '0') {
                    $(row).addClass('bg-info');
                }
            }
        });
        $tabla.on('search.dt', function() {
            $('#tablaNotificacion_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
        $tabla.on('processing.dt', function(e, settings, processing) {
            if (processing) {
                $(e.currentTarget).LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });

        $('#tablaNotificacion').on('click', 'button.desplegar', function(e) {
            e.preventDefault();
            let tr = e.currentTarget.closest('tr');
            var row = $tabla.row(tr);
            var id = e.currentTarget.dataset.id;
            if (row.child.isShown()) {
                //  This row is already open - close it
                row.child.hide();
                tr.classList.remove('shown');
            } else {
                // Open this row
                //    row.child( format(iTableCounter, id) ).show();
                buildFormat(e.currentTarget, iTableCounter, id, row);
                tr.classList.add('shown');
                // try datatable stuff
                oInnerTable = $('#tablaNotificacion_' + iTableCounter).dataTable({
                    //    data: sections,
                    autoWidth: true,
                    deferRender: true,
                    info: false,
                    lengthChange: false,
                    ordering: false,
                    paging: false,
                    scrollX: false,
                    scrollY: false,
                    searching: false,
                    columns: []
                });
                iTableCounter = iTableCounter + 1;
            }
        });

        $('#tablaNotificacion').on('click', 'a.visitar', function(e) {
            e.preventDefault();
            $(this).closest('tr').removeClass('bg-info');
            $(this).closest('tr').find('td:eq(2)').html("Sí");
            window.open($(this).attr('href'));
        });

        $('#tablaNotificacion').on('click', 'button.eliminar', function(e) {
            var $boton = $(this);
            $.ajax({
                url: '{{ route("notificaciones.eliminar") }}',
                data: {
                    id: $boton.data('id'),
                    _token: '{{csrf_token()}}'
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $boton.prop('disabled', true);
                },
                success: function(data) {
                    Util.notify(data.tipo, data.mensaje);
                    if (data.tipo == 'info') {
                        $boton.closest('tr').fadeOut(function() {
                            $tabla.ajax.reload();
                        });
                    }
                },
                error: function() {
                    Util.notify("error", "Hubo un problema al eliminar la notificación. Por favor actualice la página e intente de nuevo");
                }
            });
        });
    });


    function buildFormat(obj, table_id, id, row) {
        var html = '';
        if (obj.dataset.comentario.length > 0) {
            html += `<tr>
                        <td style="border: none; text-align:center;">${obj.dataset.comentario}</td>
                        </tr>`;

            var tabla = `<table class="table table-condensed table-bordered"
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none; text-align:center;">Comentario</th>
                    </tr>
                </thead>
                <tbody style="background: #e7e8ea;">${html}</tbody>
                </table>`;
        } else {
            var tabla = `<table class="table table-sm" style="border: none;"
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No existe comentario para mostrar</td></tr>
                </tbody>
                </table>`;
        }

        row.child(tabla).show();
    }

    function listar() {

    }
</script>
@endsection