function listar() {
    var $tabla = $('#listaPeriodos').DataTable({
        // dom: vardataTables[1],
        dom: 'Bfrtip',
        buttons: {
            text: ' Nuevo',
            action: function () {
            }, className: 'btn-success btnNuevo'
        },
        language: vardataTables[0],
        pageLength: 20,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $('#listaPeriodos_filter');
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
            $('#listaPeriodos_filter input').prop('disabled', false);
            $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
            $('#listaPeriodos_filter input').trigger('focus');
        },
        order: [[0, 'desc']],
        ajax: {
            url: "listar",
            method: 'POST',
            // headers: { 'X-CSRF-TOKEN': csrf_token }
        },
        columns: [
            // {data: 'id_periodo', className: 'text-center'},
            { data: 'anio', className: 'text-center' },
            { data: 'mes', className: 'text-center' },
            { data: 'empresa', name: 'adm_contri.razon_social', className: 'text-lefth' },
            { data: 'sede', name: 'sis_sede.codigo', className: 'text-lefth' },
            { data: 'almacen', name: 'alm_almacen.descripcion', className: 'text-lefth' },
            // {data: 'estado_nombre', name:'periodo_estado.nombre', className: 'text-center'},
            {
                data: "estado_nombre", name: "periodo_estado.nombre",
                render: function (data, type, row) {
                    return (
                        `${row["estado"] == 1 ? '<span class="fas fa-lock-open red" ></span> ' : '<span class="fas fa-lock green"></span> '}` + row["estado_nombre"]
                    );
                },
                className: "text-center"
            },
            { data: 'accion', orderable: false, searchable: false, className: 'text-center' }
        ],
    });
    $tabla.on('search.dt', function () {
        $('#listaPeriodos_filter input').attr('disabled', true);
        $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
    });
    $tabla.on('init.dt', function (e, settings, processing) {
        $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
    });
    $tabla.on('processing.dt', function (e, settings, processing) {
        if (processing) {
            $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
        } else {
            $(e.currentTarget).LoadingOverlay("hide", true);
        }
    });
}

function openCierreApertura() {
    $('#title').text('Nuevo Cierre / Apertura');
    // $('#nuevo-cierre-apertura').attr('action', "{{ route('tesoreria.cierre-apertura.guardarVarios') }}");
    $('#modal-nuevo-cierre-apertura').modal('show');
}

$("#listaPeriodos tbody").on("click", "button.abrir", function () {
    $('#titleCierreApertura').text('Abrir Periodo');
    $('[name=ca_anio]').removeClass('color-cerrar');
    $('[name=ca_anio]').addClass('color-abrir');
    $('[name=ca_mes]').removeClass('color-cerrar');
    $('[name=ca_mes]').addClass('color-abrir');
    $('[name=ca_almacen]').removeClass('color-cerrar');
    $('[name=ca_almacen]').addClass('color-abrir');
    $('[name=ca_estado]').removeClass('color-cerrar');
    $('[name=ca_estado]').addClass('color-abrir');

    var data = $("#listaPeriodos").DataTable().row($(this).parents("tr")).data();
    console.log(data);

    $('#modal-cierre-apertura').modal('show');
    $('#cierre-apertura').attr('action', "{{ route('tesoreria.cierre-apertura.guardar') }}");

    $('[name=ca_anio]').val(data.anio);
    $('[name=ca_mes]').val(data.mes);
    $('[name=ca_id_estado]').val(1);
    $('[name=ca_estado]').val('Abrir');
    $('[name=ca_almacen]').val(data.almacen);
    $('[name=ca_id_periodo]').val(data.id_periodo);
    $('[name=ca_comentario]').val('');
});

$("#listaPeriodos tbody").on("click", "button.cerrar", function () {
    $('#titleCierreApertura').text('Cerrar Periodo');

    $('[name=ca_anio]').removeClass('color-abrir');
    $('[name=ca_anio]').addClass('color-cerrar');
    $('[name=ca_mes]').removeClass('color-abrir');
    $('[name=ca_mes]').addClass('color-cerrar');
    $('[name=ca_almacen]').removeClass('color-abrir');
    $('[name=ca_almacen]').addClass('color-cerrar');
    $('[name=ca_estado]').removeClass('color-abrir');
    $('[name=ca_estado]').addClass('color-cerrar');

    var data = $("#listaPeriodos").DataTable().row($(this).parents("tr")).data();
    console.log(data);

    $('#modal-cierre-apertura').modal('show');
    $('#cierre-apertura').attr('action', "{{ route('tesoreria.cierre-apertura.guardar') }}");

    $('[name=ca_anio]').val(data.anio);
    $('[name=ca_mes]').val(data.mes);
    $('[name=ca_id_estado]').val(2);
    $('[name=ca_estado]').val('Cerrar');
    $('[name=ca_almacen]').val(data.almacen);
    $('[name=ca_id_periodo]').val(data.id_periodo);
    $('[name=ca_comentario]').val('');
});

$("#listaPeriodos tbody").on("click", "button.historial", function () {
    $('#modal-historial-acciones').modal('show');
    var id_periodo = $(this).data("id");

    $.ajax({
        type: "GET",
        url: 'listaHistorialAcciones/' + id_periodo,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            html = '';
            response.forEach(element => {
                html += `<tr>
                <td>${element.anio}</td>
                <td>${element.mes}</td>
                <td>${element.empresa}</td>
                <td>${element.almacen}</td>
                <td>${element.estado_nombre}</td>
                <td>${element.comentario}</td>
                <td>${element.nombre_corto}</td>
                <td>${formatDateHour(element.fecha_registro)}</td>
                </tr>`;
            });
            $('#listaHistorialAcciones tbody').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});