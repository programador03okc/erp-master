let valor_permiso = null;
let usuario_session = null;
let trans_seleccionadas = [];

function iniciar(permiso, usuario) {
    $("#tab-transferencias section:first form").attr("form", "formulario");
    valor_permiso = permiso;
    usuario_session = usuario;

    listarRequerimientosPendientes();
    console.log(permiso);

    $("ul.nav-tabs li a").on('click', function () {
        $("ul.nav-tabs li").removeClass("active");
        $(this)
            .parent()
            .addClass("active");
        $(".content-tabs section").attr("hidden", true);
        $(".content-tabs section form").removeAttr("type");
        $(".content-tabs section form").removeAttr("form");

        var activeTab = $(this).attr("type");
        var activeForm = "form-" + activeTab.substring(1);

        $("#" + activeForm).prop("type", "register");
        $("#" + activeForm).attr("form", "formulario");
        changeStateInput(activeForm, true);

        if (activeForm == "form-requerimientos") {
            //listarRequerimientosPendientes();
            $("#listaRequerimientos").DataTable().ajax.reload();

        } else if (activeForm == "form-pendientes") {
            listarTransferenciasPorRecibir();
        } else if (activeForm == "form-porEnviar") {
            listarTransferenciasPorEnviar();
        } else if (activeForm == "form-recibidas") {
            listarTransferenciasRecibidas();
        }
        $(activeTab).attr("hidden", false);
    });
    vista_extendida();
}

function listarRequerimientosPendientes() {
    var vardataTables = funcDatatables();

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
    const $tableRequerimientos = $("#listaRequerimientos").DataTable({
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
            { data: "codigo", name: "alm_req.codigo", className: "text-center" },
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
                    return (
                        '<a href="#" class="archivos" data-id="' +
                        row["id_oc_propia"] +
                        '" data-tipo="' +
                        row["tipo"] +
                        '">' +
                        row["nro_orden"] +
                        "</a>"
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
                    return `<button type="button" class="transferencia btn btn-success boton" data-toggle="tooltip"
                            data-placement="bottom" data-id="${row["id_requerimiento"]}" title="Crear Transferencia(s)" >
                            <i class="fas fa-exchange-alt"></i></button>`;
                },
                className: "text-center", orderable: false
            }
        ],
        columnDefs: [
            {
                aTargets: [0],
                sClass: "invisible"
            },
            {
                render: function (data, type, row) {
                    return (
                        '<a href="#" class="verRequerimiento" data-id="' + row["id_requerimiento"] + '" >' + row["codigo"] + "</a>"
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
    let url = "/logistica/gestion-logistica/requerimiento/elaboracion/index";
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
    ver_requerimiento(id);
});

function listarTransferenciasPorEnviar() {
    var alm_origen = $("[name=id_almacen_origen_lista]").val();
    var vardataTables = funcDatatables();
    let botones = [];
    if (valor_permiso == '1') {
        botones.push({
            text: ' Ingresar Guía',
            toolbar: 'Seleccione varias transferencias para una Guía.',
            action: function () {
                openGuiaTransferenciaCreate();
            }, className: 'btn-success'
        });
    }
    $("#listaTransferenciasPorEnviar").DataTable({
        dom: 'Bfrtip',
        buttons: botones,
        language: vardataTables[0],
        lengthChange: false,
        pageLength: 25,
        destroy: true,
        serverSide: true,
        ajax: {
            url: "listarTransferenciasPorEnviar/" + alm_origen,
            type: "POST"
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
            // { data: "fecha_registro" },
            { data: "alm_origen_descripcion", name: "origen.descripcion" },
            { data: "alm_destino_descripcion", name: "destino.descripcion" },
            {
                data: "cod_req",
                name: "alm_req.codigo",
                className: "text-center"
            },
            { data: "concepto", name: "alm_req.concepto" },
            {
                data: "sede_descripcion",
                name: "sis_sede.descripcion",
                className: "text-center"
            },
            { data: "nombre_corto", name: "sis_usua.nombre_corto" },
            {
                render: function (data, type, row) {
                    if (valor_permiso == "1") {
                        return `<div style="display: flex;text-align:center;">
                        <button type="button" class="guia btn btn-primary boton btn-flat" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row["id_transferencia"]}" data-cod="${row["id_requerimiento"]}" title="Generar Guía" >
                            <i class="fas fa-sign-in-alt"></i></button>
                        <button type="button" class="anular btn btn-danger boton btn-flat" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row["id_transferencia"]}" data-cod="${row["id_requerimiento"]}" title="Anular Transferencia" >
                            <i class="fas fa-trash"></i></button>
                        <div/>`;
                    }
                },
                className: "text-center"
            }
        ],
        drawCallback: function () {
            $(
                '#listaTransferenciasPorEnviar tbody tr td input[type="checkbox"]'
            ).iCheck({
                checkboxClass: "icheckbox_flat-blue"
            });
        },
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
                    selectAllCallback: function (
                        nodes,
                        selected,
                        indeterminate
                    ) {
                        $('input[type="checkbox"]', nodes).iCheck("update");
                    }
                }
            }
        ],
        select: "multi",
        order: [[2, "desc"]]
    });

    $(
        $("#listaTransferenciasPorEnviar")
            .DataTable()
            .table()
            .container()
    ).on("ifChanged", ".dt-checkboxes", function (event) {
        var cell = $("#listaTransferenciasPorEnviar")
            .DataTable()
            .cell($(this).closest("td"));
        cell.checkboxes.select(this.checked);

        var data = $("#listaTransferenciasPorEnviar")
            .DataTable()
            .row($(this).parents("tr"))
            .data();
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
    var data = $("#listaTransferenciasPorEnviar")
        .DataTable()
        .row($(this).parents("tr"))
        .data();
    console.log("data" + data);
    openGenerarGuia(data);
});

$("#listaTransferenciasPorEnviar tbody").on(
    "click",
    "button.anular",
    function () {
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
                        listarTransferenciasPorEnviar();
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        });
    }
);

function listarTransferenciasPorRecibir() {
    var alm_destino = $("[name=id_almacen_destino_lista]").val();

    if (alm_destino !== "" && alm_destino !== "") {
        var vardataTables = funcDatatables();
        $("#listaTransferenciasPorRecibir").DataTable({
            // dom: 'Bfrtip',
            // buttons: vardataTables[2],
            language: vardataTables[0],
            lengthChange: false,
            pageLength: 25,
            destroy: true,
            ajax: "listarTransferenciasPorRecibir/" + alm_destino,
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
                {
                    render: function (data, type, row) {
                        if (row["id_guia_ven"] !== null) {
                            return formatDate(row["fecha_guia"]);
                        } else {
                            return "";
                        }
                    }
                },
                { data: "guia_ven" },
                { data: "alm_origen_descripcion" },
                { data: "alm_destino_descripcion" },
                { data: "nombre_origen" },
                { data: "nombre_destino" },
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
                            return row["id_guia_ven"] !== null
                                ? `<div style="display: flex;text-align:center;">
                                <button type="button" class="atender btn btn-success boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" title="Recibir" >
                                <i class="fas fa-share"></i></button>
                                <button type="button" class="salida btn btn-primary boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id-salida="${row["id_salida"]}" title="Imprimir Salida" >
                                <i class="fas fa-file-alt"></i></button>
                                <button type="button" class="anularSalida btn btn-danger boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row["id_guia_ven"]}" data-id-salida="${row["id_salida"]}" title="Anular Salida" >
                                <i class="fas fa-trash"></i></button>
                            </div>`
                                : "";
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
                }
            ]
        });
    }
}

$("#listaTransferenciasPorRecibir tbody").on(
    "click",
    "button.atender",
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

$("#listaTransferenciasPorRecibir tbody").on(
    "click",
    "button.salida",
    function () {
        var idSalida = $(this).data("idSalida");
        console.log(idSalida);
        if (idSalida !== "") {
            var id = encode5t(idSalida);
            window.open("imprimir_salida/" + id);
        }
    }
);

$("#listaTransferenciasPorRecibir tbody").on(
    "click",
    "button.anularSalida",
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
                // alert(response);
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
                    // width: 500,
                    msg:
                        "Salida anulada con éxito. La transferencia ha regresado a la lista de pendientes de envío."
                });
                $("#modal-guia_ven_obs").modal("hide");
                listarTransferenciasPorRecibir();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
