let $tableReservas;

function listarReservasAlmacen() {
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
        pageLength: 50,
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
            // {
            //     'render': function (data, type, row) {

            //         return `<button type="button" class="detalle btn btn-default btn-flat boton" data-toggle="tooltip"
            //                     data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
            //                     <i class="fas fa-chevron-down"></i></button>` +

            //             (row['suma_reservas'] !== null && row['suma_cantidad'] !== null && row['suma_reservas'] >= row['suma_cantidad'] ?
            //                 (`<button type="button" class="guia btn btn-warning btn-flat boton" data-toggle="tooltip" 
            //                     data-placement="bottom" title="Generar Guía" >
            //                     <i class="fas fa-sign-in-alt"></i></button>`) : '');

            //     }, targets: 10
            // }
        ],
        'order': [[0, "desc"]],
    });

    $("#reservasAlmacen tbody").on("click", "a.verProducto", function (e) {
        $(e.preventDefault());
        var id = $(this).data("id");
        localStorage.setItem("id_producto", id);
        var win = window.open("/almacen/catalogos/productos/index", '_blank');
        win.focus();
    });
}