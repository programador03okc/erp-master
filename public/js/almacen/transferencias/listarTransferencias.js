let valor_permiso = null;
let usuario_session = null;
let trans_seleccionadas = [];

let $tableRequerimientos;
let tablePorEnviar;
let tablePorRecibir;
let tableTransferenciasRecibidas;
var vardataTables = funcDatatables();

function iniciar(permiso, usuario) {
    valor_permiso = permiso;
    usuario_session = usuario;

    listarAlmacenes();
    listarRequerimientosPendientes();
    listarTransferenciasPorEnviar();
    listarTransferenciasPorRecibir();
    listarTransferenciasRecibidas();

    $('#myTabTransferencias a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let tab = $(e.target).attr("href") // activated tab
        if (tab == '#requerimientos') {
            $("#listaRequerimientos").DataTable().ajax.reload(null, false);
        }
        else if (tab == '#porEnviar') {
            $("#listaTransferenciasPorEnviar").DataTable().ajax.reload(null, false);
        }
        else if (tab == '#pendientes') {
            $("#listaTransferenciasPorRecibir").DataTable().ajax.reload(null, false);
        }
        else if (tab == '#recibidas') {
            $("#listaTransferenciasRecibidas").DataTable().ajax.reload(null, false);
        }
    });
    vista_extendida();
}


function listarRequerimientosPendientes() {

    $("#listaRequerimientos").on('search.dt', function () {
        $('#listaRequerimientos_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#listaRequerimientos").on('processing.dt', function (e, settings, processing) {
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
    $tableRequerimientos = $("#listaRequerimientos").DataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        pageLength: 20,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $("#listaRequerimientos_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscar" class="btn btn-default btn-flat btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscar").trigger("click");
                }
            });
            $("#btnBuscar").on("click", e => {
                $tableRequerimientos.search($input.val()).draw();
            });
        },
        drawCallback: function (settings, json) {
            $("#listaRequerimientos_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>')
                .prop("disabled", false);
            $("#listaRequerimientos").find('tbody tr td input[type="checkbox"]')
                .iCheck({
                    checkboxClass: "icheckbox_flat-blue"
                });
            $("#listaRequerimientos_filter input").trigger("focus");
        },
        ajax: {
            url: "listarRequerimientos",
            type: "POST"
        },
        columns: [
            { data: "id_requerimiento", name: "alm_req.id_requerimiento" },
            // { data: "codigo", name: "alm_req.codigo", className: "text-center" },
            {
                data: 'codigo', name: 'alm_req.codigo', className: "text-center",
                'render': function (data, type, row) {
                    return (row['codigo'] !== null ? row['codigo'] : '') + (row['estado'] == 38
                        ? ' <i class="fas fa-exclamation-triangle red" data-toggle="tooltip" data-placement="bottom" title="Requerimiento por regularizar"></i> '
                        : (row['estado'] == 39 ?
                            ' <i class="fas fa-pause orange" data-toggle="tooltip" data-placement="bottom" title="Requerimiento en pausa"></i> ' : ''))
                        + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '');
                }
            },
            { data: "concepto", name: "alm_req.concepto" },
            {
                data: "sede_descripcion",
                name: "sis_sede.descripcion",
                className: "text-center"
            },
            { data: "razon_social", name: "adm_contri.razon_social" },
            { data: "nombre_corto", name: "sis_usua.nombre_corto" },
            {
                render: function (data, type, row) {
                    return (row["nro_orden"] !== null ?
                        '<a href="#" class="archivos" data-id="' + row["id_oc_propia"] + '" data-tipo="' + row["tipo"] + '">' +
                        row["nro_orden"] + "</a>" : ''
                    );
                },
                className: "text-center"
            },
            {
                data: "codigo_oportunidad",
                name: "oc_propias_view.codigo_oportunidad",
                className: "text-center"
            },
            {
                render: function (data, type, row) {
                    return `<div style="display:flex;">
                            <button type="button" class="detalle btn btn-default btn-flat boton" data-toggle="tooltip"
                                data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                                <i class="fas fa-chevron-down"></i></button>
                            <button type="button" class="transferencia btn btn-success btn-flat boton" data-toggle="tooltip"
                                data-placement="bottom" ${(row['estado'] == 39 || row['estado'] == 38) ? 'disabled' : ''} 
                                data-id="${row["id_requerimiento"]}"
                                data-sede="${row["id_sede"]}" 
                                title="Crear Transferencia(s)" >
                                <i class="fas fa-exchange-alt"></i></button>
                            </div>`;
                },
                className: "text-center", orderable: false
            }
        ],
        columnDefs: [
            { aTargets: [0], sClass: "invisible" },
            {
                render: function (data, type, row) {
                    return (
                        '<a href="#" class="verRequerimiento" data-id="' + row["id_requerimiento"] + '" >' + row["codigo"] + "</a>" +
                        (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '')
                    );
                }, targets: 1

            },
        ]
    });

}

$("#listaRequerimientos tbody").on("click", "a.verRequerimiento", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    localStorage.setItem("idRequerimiento", id);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, "_blank");
    win.focus();
});

$("#listaRequerimientos tbody").on("click", "a.archivos", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");
    obtenerArchivosMgcp(id, tipo);
});

$("#listaRequerimientos tbody").on("click", "button.transferencia", function () {
    var id = $(this).data("id");
    var sede = $(this).data("sede");
    ver_requerimiento(id, sede);
});

var iTableCounter = 1;
var oInnerTable;

$('#listaRequerimientos tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = $tableRequerimientos.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        format(iTableCounter, id, row);
        tr.addClass('shown');
        oInnerTable = $('#listaRequerimientos_' + iTableCounter).dataTable({
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

function listarTransferenciasPorEnviar() {

    let botones = [];
    if (valor_permiso == '1') {
        botones.push({
            text: ' Ingresar Guía',
            toolbar: 'Seleccione varias transferencias para una Guía.',
            action: function () {
                openGuiaTransferenciaCreate();
            }, className: 'btn-success btn-flat'
        });
    }

    $("#listaTransferenciasPorEnviar").on('search.dt', function () {
        $('#listaTransferenciasPorEnviar_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#listaTransferenciasPorEnviar").on('processing.dt', function (e, settings, processing) {
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

    tablePorEnviar = $("#listaTransferenciasPorEnviar").DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        lengthChange: false,
        serverSide: true,
        pageLength: 20,
        initComplete: function (settings, json) {
            const $filter = $("#listaTransferenciasPorEnviar_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscarPorEnviar" class="btn btn-default btn-sm btn-flat" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscarPorEnviar").trigger("click");
                }
            });
            $("#btnBuscarPorEnviar").on("click", e => {
                tablePorEnviar.search($input.val()).draw();
            });

            $('#listaTransferenciasPorEnviar_wrapper .dt-buttons').append(
                `<div class="col-md-4" style="text-align: center;margin-top: 7px;"><label>Almacén Origen:</label></div>
                <div class="col-md-4" style="display:flex">
                    <select class="form-control" id="selectAlmacenOrigen" >
                        <option value="0" selected>Todos los almacenes</option>
                    </select>
                </div>`
            );
            mostrarAlmacenes('origen');
            $("#selectAlmacenOrigen").on("change", function (e) {
                var alm = $(this).val();
                $('input[name=id_almacen_origen]').val(alm);
                $("#listaTransferenciasPorEnviar").DataTable().ajax.reload(null, false);
            });
        },
        drawCallback: function (settings) {
            $("#listaTransferenciasPorEnviar_filter input").prop("disabled", false);
            $("#btnBuscarPorEnviar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>'
            ).prop("disabled", false);
            $("#listaTransferenciasPorEnviar_filter input").trigger("focus");
            $('#listaTransferenciasPorEnviar tbody tr td input[type="checkbox"]').iCheck({
                checkboxClass: "icheckbox_flat-blue"
            });
        },
        // ajax: "listarTransferenciasPorEnviar/" + alm_origen,
        ajax: {
            url: "listarTransferenciasPorEnviar",
            type: "POST",
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosPorEnviar').serializeArray()))
            }
        },
        columns: [
            { data: "id_transferencia" },
            {
                orderable: false, searchable: false,
                render: function (data, type, row) {
                    if (row['id_empresa_origen'] !== row['id_empresa_destino']) {
                        return `<span class="label label-primary">Venta Interna</span>`;
                    } else {
                        return `<span class="label label-success">Transferencia</span>`;
                    }
                },
                className: "text-center"
            },
            { data: "codigo", className: "text-center" },
            { data: "alm_origen_descripcion", name: "origen.descripcion" },
            { data: "alm_destino_descripcion", name: "destino.descripcion" },
            {
                data: "cod_req", name: "alm_req.codigo", className: "text-center"
            },
            { data: "concepto", name: "alm_req.concepto" },
            { data: "nombre_corto", name: "sis_usua.nombre_corto" },
            { data: "guia_ven", className: "text-center" },
            {
                render: function (data, type, row) {
                    return (
                        '<span class="label label-' + row["bootstrap_color"] + '">' +
                        row["estado_doc"] + "</span>"
                    );
                }, className: "text-center"
            },
            {
                render: function (data, type, row) {
                    if (valor_permiso == "1") {
                        return `<div style="display: flex;text-align:center;">
                        ${row["estado"] == 1
                                ? `<button type="button" class="guia btn btn-primary boton btn-flat" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row["id_transferencia"]}" data-cod="${row["id_requerimiento"]}" 
                                    title="Generar Guía" ><i class="fas fa-sign-in-alt"></i></button>

                                <button type="button" class="anular btn btn-danger boton btn-flat" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row["id_transferencia"]}" data-cod="${row["id_requerimiento"]}" title="Anular Transferencia" >
                                    <i class="fas fa-trash"></i></button>`

                                : `<button type="button" class="anularSalida btn btn-danger boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row["id_guia_ven"]}" data-id-salida="${row["id_salida"]}" title="Anular Salida" >
                                <i class="fas fa-trash"></i></button>`}
                        <div/>`;
                    }
                }, className: "text-center"
            }
        ],
        columnDefs: [
            {
                targets: 0,
                searchable: false,
                orderable: false,
                className: "dt-body-center",
                checkboxes: {
                    selectRow: true,
                    selectCallback: function (nodes, selected) {
                        $('input[type="checkbox"]', nodes).iCheck("update");
                    },
                    selectAllCallback: function (nodes, selected, indeterminate) {
                        $('input[type="checkbox"]', nodes).iCheck("update");
                    }
                }
            },
            {
                render: function (data, type, row) {
                    return (row["guia_ven"] == '-' ? row["guia_ven"]
                        : '<a href="#" class="salida" data-id-salida="' + row["id_salida"] + '" title="Ver Salida">' + row["guia_ven"] + "</a>"
                    );
                }, targets: 8
            },
        ],
        select: "multi",
        order: [[0, "desc"]]
    });

    $($("#listaTransferenciasPorEnviar").DataTable().table().container()).on("ifChanged", ".dt-checkboxes", function (event) {
        var cell = $("#listaTransferenciasPorEnviar").DataTable().cell($(this).closest("td"));
        cell.checkboxes.select(this.checked);

        var data = $("#listaTransferenciasPorEnviar").DataTable().row($(this).parents("tr")).data();
        console.log(this.checked);

        if (data !== null && data !== undefined) {
            if (this.checked) {
                trans_seleccionadas.push(data);
            } else {
                var index = trans_seleccionadas.findIndex(function (item, i) {
                    return item.id_transferencia == data.id_transferencia;
                });
                if (index !== null) {
                    trans_seleccionadas.splice(index, 1);
                }
            }
        }
    });
}

$("#listaTransferenciasPorEnviar tbody").on("click", "button.guia", function () {
    var data = $("#listaTransferenciasPorEnviar").DataTable().row($(this).parents("tr")).data();
    console.log("data" + data);
    openGenerarGuia(data);
});

$("#listaTransferenciasPorEnviar tbody").on("click", "button.anular", function () {
    var id = $(this).data("id");
    Swal.fire({
        title: "¿Está seguro que desea anular ésta transferencia?",
        text: "No podrás revertir esto.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Si, anular"
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                type: "GET",
                url: "anular_transferencia/" + id,
                dataType: "JSON",
                success: function (response) {
                    Lobibox.notify("success", {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        // width: 500,
                        msg: "Transferencia anulada con éxito."
                    });
                    // listarTransferenciasPorEnviar();
                    $("#listaTransferenciasPorEnviar").DataTable().ajax.reload(null, false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
});

$("#listaTransferenciasPorEnviar tbody").on("click", "a.salida",
    function () {
        var idSalida = $(this).data("idSalida");
        console.log(idSalida);
        if (idSalida !== "") {
            // var id = encode5t(idSalida);
            window.open("imprimir_salida/" + idSalida);
        }
    }
);

$("#listaTransferenciasPorEnviar tbody").on("click", "button.anularSalida",
    function () {
        var idSalida = $(this).data("idSalida");
        var idGuia = $(this).data("id");
        console.log(idSalida);
        if (idSalida !== "") {
            Swal.fire({
                title:
                    "Esta seguro que desea anular la salida por transferencia?",
                text: "No podrás revertir esto.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "Cancelar",
                confirmButtonText: "Si, anular"
            }).then(result => {
                if (result.isConfirmed) {
                    $("#modal-guia_ven_obs").modal({
                        show: true
                    });

                    $("[name=id_salida]").val(idSalida);
                    // $('[name=id_transferencia]').val('');
                    $("[name=id_guia_ven]").val(idGuia);
                    $("[name=observacion_guia_ven]").val("");

                    $("#submitGuiaVenObs").removeAttr("disabled");
                }
            });
        }
    }
);

$("#form-guia_ven_obs").on("submit", function (e) {
    console.log("submit");
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anularTransferenciaSalida(data);
});

function anularTransferenciaSalida(data) {
    $("#submitGuiaVenObs").attr("disabled", "true");
    $.ajax({
        type: "POST",
        url: "anularTransferenciaSalida",
        data: data,
        dataType: "JSON",
        success: function (response) {
            if (response.length > 0) {
                Lobibox.notify("warning", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    // width: 500,
                    msg: response
                });
                $("#modal-guia_ven_obs").modal("hide");
            } else {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg:
                        "Salida anulada con éxito. La transferencia ha regresado a la lista de pendientes de envío."
                });
                $("#modal-guia_ven_obs").modal("hide");
                // listarTransferenciasPorEnviar();
                $("#listaTransferenciasPorEnviar").DataTable().ajax.reload(null, false);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


function listarTransferenciasPorRecibir() {

    $("#listaTransferenciasPorRecibir").on('search.dt', function () {
        $('#listaTransferenciasPorRecibir_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#listaTransferenciasPorRecibir").on('processing.dt', function (e, settings, processing) {
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

    tablePorRecibir = $("#listaTransferenciasPorRecibir").DataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        lengthChange: false,
        serverSide: true,
        pageLength: 20,
        initComplete: function (settings, json) {
            const $filter = $("#listaTransferenciasPorRecibir_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscarPorRecibir" class="btn btn-default btn-sm btn-flat" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscarPorRecibir").trigger("click");
                }
            });
            $("#btnBuscarPorRecibir").on("click", e => {
                tablePorRecibir.search($input.val()).draw();
            });

            $('#listaTransferenciasPorRecibir_wrapper .dt-buttons').append(
                `<div class="col-md-5" style="text-align: center;margin-top: 7px;">
                    <label>Almacén Destino:</label>
                </div>
                <div class="col-md-4" style="display:flex">
                    <select class="form-control" id="selectAlmacenDestino" >
                        <option value="0" selected>Todos los almacenes</option>
                    </select>
                </div>`
            );
            mostrarAlmacenes('destino');
            $("#selectAlmacenDestino").on("change", function (e) {
                var alm = $(this).val();
                $('input[name=id_almacen_destino]').val(alm);
                $("#listaTransferenciasPorRecibir").DataTable().ajax.reload(null, false);
            });
        },
        drawCallback: function (settings) {
            $("#listaTransferenciasPorRecibir_filter input").prop("disabled", false);
            $("#btnBuscarPorRecibir").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>'
            ).prop("disabled", false);
            $("#listaTransferenciasPorRecibir_filter input").trigger("focus");
        },
        ajax: {
            url: "listarTransferenciasPorRecibir",
            type: "POST",
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosPorRecibir').serializeArray()))
            }
        },
        // ajax: "listarTransferenciasPorRecibir/" + alm_destino,
        columns: [
            { data: "id_guia_ven" },
            {
                orderable: false, searchable: false,
                render: function (data, type, row) {
                    if (row['id_empresa_origen'] !== row['id_empresa_destino']) {
                        return `<span class="label label-primary">Venta Interna</span>`;
                    } else {
                        return `<span class="label label-success">Transferencia</span>`;
                    }
                },
                className: "text-center"
            },
            { data: "codigo", className: "text-center" },
            { data: "guia_ven", className: "text-center" },
            {
                render: function (data, type, row) {
                    return row.requerimientos;
                }, className: "text-center"
            },
            { data: "alm_origen_descripcion" },
            { data: "alm_destino_descripcion" },
            { data: "nombre_origen", className: "text-center" },
            { data: "nombre_destino", className: "text-center" },
            {
                render: function (data, type, row) {
                    return (
                        '<span class="label label-' +
                        row["bootstrap_color"] +
                        '">' +
                        row["estado_doc"] +
                        "</span>"
                    );
                }
            },
            {
                render: function (data, type, row) {
                    if (valor_permiso == "1") {
                        return (row["id_guia_ven"] !== null
                            ? `<div style="display: flex;text-align:center;">
                                <button type="button" class="atender btn btn-success boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" title="Recibir" >
                                <i class="fas fa-share"></i></button>
                            </div>`
                            : "");
                    } else {
                        return "";
                    }
                },
                className: "text-center"
            }
        ],
        columnDefs: [
            {
                aTargets: [0],
                sClass: "invisible"
            },
            {
                render: function (data, type, row) {
                    return (row["guia_ven"] == '-' ? row["guia_ven"]
                        : '<a href="#" class="salida" data-id-salida="' + row["id_salida"] + '" title="Ver Salida">' + row["guia_ven"] + "</a>"
                    );
                }, targets: 4
            },
        ]
    });
    // }
}

$("#listaTransferenciasPorRecibir tbody").on("click", "button.atender",
    function () {
        var data = $("#listaTransferenciasPorRecibir")
            .DataTable()
            .row($(this).parents("tr"))
            .data();
        console.log(data);
        if (data !== undefined) {
            open_transferencia_detalle(data);
        }
    }
);

$("#listaTransferenciasPorRecibir tbody").on("click", "a.salida",
    function () {
        var idSalida = $(this).data("idSalida");
        console.log(idSalida);
        if (idSalida !== "") {
            // var id = encode5t(idSalida);
            window.open("imprimir_salida/" + idSalida);
        }
    }
);
let selectAlmacenes = '';
function listarAlmacenes() {
    $.ajax({
        type: "GET",
        url: "almacenesPorUsuario",
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            var option = '<option value="0">Todos las almacenes</option>';
            response.forEach(element => {
                if (response.length == 1) {
                    option +=
                        '<option value="' + element.id_almacen + '" selected>' + element.descripcion + "</option>";
                } else {
                    option +=
                        '<option value="' + element.id_almacen + '">' + element.descripcion + "</option>";
                }
            });
            selectAlmacenes = option;
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function mostrarAlmacenes(origen) {
    if (origen == 'origen') {
        $("#selectAlmacenOrigen").html(selectAlmacenes);
    } else if (origen == 'destino') {
        $("#selectAlmacenDestino").html(selectAlmacenes);
    } else if (origen == 'recibido') {
        $("#selectAlmacenDestinoRecibido").html(selectAlmacenes);
    }
}