
function listarIngresos() {
    var vardataTables = funcDatatables();
    let botones = [];
    if (acceso == '1') {
        botones.push({
            text: ' Ingresar Comprobante',
            action: function () {
                open_doc_create_seleccionadas();
            }, className: 'btn-success'
        });
    }

    $("#listaIngresosAlmacen").on('search.dt', function () {
        $('#listaIngresosAlmacen_filter input').prop('disabled', true);
        $('#btnBuscarIngreso').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#listaIngresosAlmacen").on('processing.dt', function (e, settings, processing) {
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

    let tableIngresos = $("#listaIngresosAlmacen").DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // bDestroy: true,
        serverSide: true,
        pageLength: 50,
        initComplete: function (settings, json) {
            const $filter = $("#listaIngresosAlmacen_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscarIngreso" class="btn btn-default btn-flat btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscarIngreso").trigger("click");
                }
            });
            $("#btnBuscarIngreso").on("click", e => {
                tableIngresos.search($input.val()).draw();
            });
        },
        // drawCallback: function () {
        //     $('#listaIngresosAlmacen tbody tr td input[type="checkbox"]').iCheck({
        //         checkboxClass: "icheckbox_flat-blue"
        //     });
        // },
        drawCallback: function (settings, json) {
            $("#listaIngresosAlmacen_filter input").prop("disabled", false);
            $("#btnBuscarIngreso").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>')
                .prop("disabled", false);
            $("#listaIngresosAlmacen").find('tbody tr td input[type="checkbox"]').iCheck({
                checkboxClass: "icheckbox_flat-blue"
            });
            $("#listaIngresosAlmacen_filter input").trigger("focus");
        },
        ajax: {
            url: "listarIngresos",
            type: "POST"
        },
        columns: [
            { data: "id_mov_alm" },
            { data: "id_mov_alm" },
            { data: "fecha_emision" },
            {
                data: "numero",
                name: "guia_com.numero",
                render: function (data, type, row) {
                    return row["serie"] + "-" + row["numero"];
                }
            },
            { data: "nro_documento", name: "adm_contri.nro_documento" },
            { data: "razon_social", name: "adm_contri.razon_social" },
            {
                data: "codigo",
                render: function (data, type, row) {
                    return row["codigo"] !== null
                        ? '<label class="lbl-codigo" title="Abrir Ingreso" onClick="abrir_ingreso(' +
                        row["id_mov_alm"] +
                        ')">' +
                        row["codigo"] +
                        "</label>"
                        : "";
                }
            },
            { data: "operacion_descripcion", name: "tp_ope.descripcion" },
            { data: "almacen_descripcion", name: "alm_almacen.descripcion" },
            { data: "nombre_corto", name: "sis_usua.nombre_corto" },
            { data: "id_mov_alm" },
            { data: "id_mov_alm" },
            { data: "id_guia_com" },
            { data: "id_mov_alm", searchable: false }
        ],
        columnDefs: [
            { targets: [0], className: "invisible" },
            {
                targets: 1,
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
            },
            {
                render: function (data, type, row) {
                    return row.ordenes_compra;
                },
                targets: 10
            },
            {
                render: function (data, type, row) {
                    return row.ordenes_soft_link;
                },
                targets: 11
            },
            {
                render: function (data, type, row) {
                    return row.comprobantes;
                },
                targets: 12
            },
            {
                render: function (data, type, row) {
                    if (acceso == "1") {
                        return (
                            '<div style="display:flex;"><button type="button" class="detalle btn btn-primary btn-xs btn-flat " data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Ver Detalle" data-id="' + row["id_guia_com"] + '" data-cod="' + row["codigo"] + '">' +
                            '<i class="fas fa-list-ul"></i></button>' +
                            // '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                            //     'data-placement="bottom" title="Ver Ingreso" data-id="'+row['id_mov_alm']+'">'+
                            //     '<i class="fas fa-file-alt"></i></button>'+
                            (row["id_operacion"] == 21
                                ? ""
                                : `<button type="button" class="cambio btn btn-warning btn-xs btn-flat" data-toggle="tooltip" 
                            data-placement="bottom" title="Cambiar Serie-Número" data-id="${row["id_mov_alm"]}" 
                            data-guia="${row["id_guia_com"]}"><i class="fas fa-sync-alt"></i></button>`) +
                            `${row["count_sedes_diferentes"] > 0 ||
                                row["count_sedes_diferentes_od"] > 0
                                ? row["count_transferencias"] == 0
                                    ? `<button type="button" class="transferencia btn btn-success btn-xs btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" title="Generar Transferencia" data-guia="${row["id_guia_com"]}">
                                <i class="fas fa-exchange-alt"></i></button>
                            <button type="button" class="anular btn btn-danger btn-xs btn-flat " data-toggle="tooltip" 
                                data-placement="bottom" title="Anular Ingreso" data-id="${row["id_mov_alm"]}" 
                                data-guia="${row["id_guia_com"]}" data-oc="${row["id_orden_compra"]}">
                                <i class="fas fa-trash"></i></button>`
                                    : ""
                                : row["id_operacion"] == 21
                                    ? ""
                                    : //falta verificar si ya se creo orden de despacho
                                    row["count_despachos_oc"] > 0
                                        ? ""
                                        : row["count_facturas"] > 0
                                            ? ""
                                            : `<button type="button" class="anular btn btn-danger btn-xs btn-flat " data-toggle="tooltip" 
                                        data-placement="bottom" title="Anular Ingreso" data-id="${row["id_mov_alm"]}" 
                                        data-guia="${row["id_guia_com"]}" data-oc="${row["id_orden_compra"]}">
                                        <i class="fas fa-trash"></i></button>`
                            }` +
                            (row["id_operacion"] == 2
                                ? `<button type="button" class="${row["count_facturas"] > 0 ? "ver_doc" : "doc"} btn btn-${row["count_facturas"] > 0 ? "info" : "default"
                                } btn-xs btn-flat" data-toggle="tooltip" data-placement="bottom" title="Generar Factura" data-guia="${row["id_guia_com"]
                                }" data-doc="${row["id_doc_com"]}"><i class="fas fa-file-medical"></i></button>
                                </div>`
                                : "</div>")
                        );
                    } else {
                        return (
                            '<button type="button" class="detalle btn btn-primary btn-xs btn-flat" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Ver Detalle" data-id="' +
                            row["id_mov_alm"] +
                            '" data-cod="' +
                            row["codigo"] +
                            '">' +
                            '<i class="fas fa-list-ul"></i></button>' +
                            '<button type="button" class="ingreso btn btn-warning btn-xs btn-flat" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Ver Ingreso" data-id="' +
                            row["id_mov_alm"] +
                            '">' +
                            '<i class="fas fa-file-alt"></i></button>'
                        );
                    }
                },
                targets: 13
            }
        ],
        select: "multi",
        order: [[0, "desc"]]
    });

    $($("#listaIngresosAlmacen").DataTable().table().container()).on("ifChanged", ".dt-checkboxes", function (event) {
        var cell = $("#listaIngresosAlmacen")
            .DataTable()
            .cell($(this).closest("td"));
        cell.checkboxes.select(this.checked);

        var data = $("#listaIngresosAlmacen")
            .DataTable()
            .row($(this).parents("tr"))
            .data();
        console.log(this.checked);

        if (data !== null && data !== undefined) {
            if (this.checked) {
                ingresos_seleccionados.push(data);
            } else {
                var index = ingresos_seleccionados.findIndex(function (item, i) {
                    return item.id_guia_com == data.id_guia_com;
                });
                if (index !== null) {
                    ingresos_seleccionados.splice(index, 1);
                }
            }
        }
    });
}

$("#listaIngresosAlmacen tbody").on(
    "click",
    "button.transferencia",
    function () {
        var id_guia_com = $(this).data("guia");
        // console.log(data);
        ver_transferencia(id_guia_com);
    }
);

$("#listaIngresosAlmacen tbody").on("click", "button.detalle", function () {
    var id_guia_com = $(this).data("id");
    var codigo = $(this).data("cod");
    // console.log(data);
    open_detalle_movimiento(id_guia_com, codigo);
});

function abrir_ingreso(id_mov_alm) {
    var id = encode5t(id_mov_alm);
    window.open("imprimir_ingreso/" + id);
}

$("#listaIngresosAlmacen tbody").on("click", "button.anular", function () {
    var id_mov_alm = $(this).data("id");
    var id_guia = $(this).data("guia");
    var id_oc = $(this).data("oc");

    $("#modal-guia_com_obs").modal({
        show: true
    });

    $("[name=id_mov_alm]").val(id_mov_alm);
    $("[name=id_guia_com]").val(id_guia);
    $("[name=id_oc]").val(id_oc);
    $("[name=observacion]").val("");

    $("#submitGuiaObs").removeAttr("disabled");
});

$("#form-obs").on("submit", function (e) {
    console.log("submit");
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anular_ingreso(data);
});

function anular_ingreso(data) {
    $("#submitGuiaObs").attr("disabled", "true");
    $.ajax({
        type: "POST",
        url: "anular_ingreso",
        data: data,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            if (response.length > 0) {
                alert(response);
                $("#modal-guia_com_obs").modal("hide");
            } else {
                alert("Ingreso Almacén anulado con éxito");
                $("#modal-guia_com_obs").modal("hide");
                $("#listaIngresosAlmacen")
                    .DataTable()
                    .ajax.reload();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#listaIngresosAlmacen tbody").on("click", "button.cambio", function () {
    var id_mov_alm = $(this).data("id");
    var id_guia = $(this).data("guia");

    $("#modal-guia_com_cambio").modal({
        show: true
    });

    $("[name=id_ingreso]").val(id_mov_alm);
    $("[name=id_guia_com]").val(id_guia);
    $("[name=serie_nuevo]").val("");
    $("[name=numero_nuevo]").val("");

    $("#submit_guia_com_cambio").removeAttr("disabled");
});

$("#listaIngresosAlmacen tbody").on("click", "button.anular_sal", function () {
    var id_mov_alm = $(this).data("id");
    var id_guia = $(this).data("guia");
    var id_trans = $(this).data("trans");

    $("#modal-guia_ven_obs").modal({
        show: true
    });

    $("[name=id_salida]").val(id_mov_alm);
    $("[name=id_guia_ven]").val(id_guia);
    $("[name=id_trans]").val(id_trans);
    $("[name=observacion_guia_ven]").val("");

    $("#submitGuiaVenObs").removeAttr("disabled");
});

$("#listaIngresosAlmacen tbody").on("click", "button.doc", function () {
    var id_guia = $(this).data("guia");
    open_doc_create(id_guia, "ing");
});

$("#listaIngresosAlmacen tbody").on("click", "button.ver_doc", function () {
    var id_doc = $(this).data("doc");
    documentosVer(id_doc);
});
