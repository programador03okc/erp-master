let $tableReservas;

function listarReservasAlmacen(id_usuario) {
    console.log('list');
    var vardataTables = funcDatatables();
    let botones = [];

    $("#reservasAlmacen").on('search.dt', function () {
        $('#reservasAlmacen_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#reservasAlmacen").on('processing.dt', function (e, settings, processing) {
        if (processing) {
            $(e.currentTarget).LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                zIndex: 10,
                imageColor: "#3c8dbc"
            });
        } else {
            $(e.currentTarget).LoadingOverlay("hide", true);
        }
    });

    $tableReservas = $('#reservasAlmacen').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // destroy: true,
        pageLength: 10,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $("#reservasAlmacen_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscar" class="btn btn-default btn-sm btn-flat" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscar").trigger("click");
                }
            });
            $("#btnBuscar").on("click", e => {
                $tableReservas.search($input.val()).draw();
            });

            // const $form = $('#formFiltrosSalidasPendientes');
            // $('#reservasAlmacen_wrapper .dt-buttons').append(
            //     `<div style="display:flex">
            //         <label style="text-align: center;margin-top: 7px;margin-left: 10px;margin-right: 10px;">Mostrar: </label>
            //         <select class="form-control" id="selectMostrarPendientes">
            //             <option value="0" >Todos</option>
            //             <option value="1" >Priorizados</option>
            //             <option value="2" selected>Los de Hoy</option>
            //         </select>
            //     </div>`
            // );
            // $("#selectMostrarPendientes").on("change", function (e) {
            //     var sed = $(this).val();
            //     console.log('sel ' + sed);
            //     $('#formFiltrosSalidasPendientes').find('input[name=select_mostrar_pendientes]').val(sed);
            //     $("#reservasAlmacen").DataTable().ajax.reload(null, false);
            // });

        },
        drawCallback: function (settings) {
            $("#reservasAlmacen_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop("disabled", false);
            $("#reservasAlmacen_filter input").trigger("focus");
        },
        ajax: {
            url: 'listarReservasAlmacen',
            type: 'POST',
            // data: function (params) {
            //     var x = $('[name=select_mostrar_pendientes]').val();
            //     console.log(x);
            //     return Object.assign(params, objectifyForm($('#formFiltrosSalidasPendientes').serializeArray()))
            // }
        },
        columns: [
            { data: 'id_reserva' },
            { data: 'codigo', className: "text-center" },
            {
                data: 'codigo_req', name: 'alm_req.codigo', className: "text-center",
                'render': function (data, type, row) {
                    return (row['codigo_req'] !== null ? `<a href="/necesidades/requerimiento/elaboracion/index?id=${row['id_requerimiento']}" 
                        target="_blank" title="Abrir Requerimiento">${row['codigo_req'] ?? ''}</a>` : '') +
                        (row['estado_requerimiento'] == 38
                            ? ' <i class="fas fa-exclamation-triangle red" data-toggle="tooltip" data-placement="bottom" title="Requerimiento por regularizar"></i> '
                            : (row['estado_requerimiento'] == 39 ?
                                ' <i class="fas fa-pause orange" data-toggle="tooltip" data-placement="bottom" title="Requerimiento en pausa"></i> ' : ''))
                        + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '');
                }
            },
            {
                data: 'codigo_producto', name: 'alm_prod.codigo',
                'render': function (data, type, row) {
                    return `<a href="#" class="verProducto" data-id="${row['id_producto']}" >${row['codigo_producto']}</a>`
                }
            },
            { data: 'part_number', name: 'alm_prod.part_number' },
            { data: 'descripcion', name: 'alm_prod.descripcion' },
            { data: 'almacen', name: 'alm_almacen.descripcion', className: "text-center" },
            { data: 'stock_comprometido', className: "text-center" },
            // { data: 'numero', name: 'guia_com.numero', className: "text-center" },
            {
                data: 'numero', name: 'guia_com.numero', className: "text-center",
                'render': function (data, type, row) {
                    return row['numero'] !== null ? (row['serie'] + '-' + row['numero']) : '';
                }
            },
            { data: 'codigo_transferencia', name: 'trans.codigo', className: "text-center" },
            { data: 'codigo_transformado', name: 'transformacion.codigo', className: "text-center" },
            { data: 'codigo_materia', name: 'materia.codigo', className: "text-center" },
            { data: 'nombre_corto', name: 'sis_usua.nombre_corto', className: "text-center" },
            { data: 'fecha_registro', className: "text-center" },
            // { data: 'codigo_req', name: 'alm_req.codigo', className: "text-center" },
            {
                data: 'estado_doc', name: 'adm_estado_doc.bootstrap_color', className: "text-center",
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                }
            },
        ],
        columnDefs: [
            { 'aTargets': [0], 'sClass': 'invisible' },
            {
                'render': function (data, type, row) {

                    return `${id_usuario == '3' || id_usuario == '16' || id_usuario == '17' || id_usuario == '93' ?
                        `<button type="button" class="editar btn btn-primary btn-flat boton" data-toggle="tooltip" 
                    data-placement="bottom" title="Editar Reserva"  data-id="${row['id_reserva']}"
                    data-almacen="${row['id_almacen_reserva']}"  data-stock="${row['stock_comprometido']}"
                    data-codigo="${row['codigo_req']}">
                    <i class="fas fa-edit"></i></button>`: ''
                        }
                    <button type="button" class="anular btn btn-danger btn-flat boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Anular Reserva"  data-id="${row['id_reserva']}">
                        <i class="fas fa-trash"></i></button>`;

                }, targets: 15
            }
        ],
        'order': [[0, "desc"]],
    });
    vista_extendida();
}

$("#reservasAlmacen tbody").on("click", "button.editar", function () {
    var id = $(this).data("id");
    var alm = $(this).data("almacen");
    var stock = $(this).data("stock");
    var codigo = $(this).data("codigo");

    $('#modal-editarReserva').modal({
        show: true
    });

    $('[name=id_reserva]').val(id);
    $('[name=id_almacen_reserva]').val(alm);
    $('[name=stock_comprometido]').val(stock);
    $('#codigo_req').text(codigo);
});


$("#form-editarReserva").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea guardar los cambios?",
        text: "Los cambios son irreversibles",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {

            var data = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'actualizarReserva',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    Lobibox.notify("success", {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Se actualizó correctamente'
                    });
                    $('#modal-editarReserva').modal('hide');
                    $("#reservasAlmacen").DataTable().ajax.reload(null, false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
});


$("#reservasAlmacen tbody").on("click", "button.anular", function () {
    Swal.fire({
        title: "¿Está seguro que desea anular ésta reserva?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Anular"
    }).then(result => {

        if (result.isConfirmed) {
            var id = $(this).data("id");

            $.ajax({
                type: 'GET',
                url: 'anularReserva/' + id,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    if (response > 0) {
                        Lobibox.notify("success", {
                            title: false,
                            size: "mini",
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: 'Se anuló correctamente.'
                        });
                        $('#reservasAlmacen').DataTable().ajax.reload(null, false);
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });

});

$("#reservasAlmacen tbody").on("click", "a.verProducto", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    localStorage.setItem("id_producto", id);
    var win = window.open("/almacen/catalogos/productos/index", '_blank');
    win.focus();
});

function actualizarReservas() {
    $.ajax({
        type: 'GET',
        url: 'actualizarReservas',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Se actualizaron ' + response.reservas_actualizadas + ' reservas correctamente.'
            });
            $('#reservasAlmacen').DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}