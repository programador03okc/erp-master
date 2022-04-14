function listarTransferenciasRecibidas() {

    $("#listaTransferenciasRecibidas").on('search.dt', function () {
        $('#listaTransferenciasRecibidas_filter input').prop('disabled', true);
        $('#btnBuscarRecibidas').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#listaTransferenciasRecibidas").on('processing.dt', function (e, settings, processing) {
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

    tableTransferenciasRecibidas = $("#listaTransferenciasRecibidas").DataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        lengthChange: false,
        serverSide: true,
        pageLength: 20,
        initComplete: function (settings, json) {
            const $filter = $("#listaTransferenciasRecibidas_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscarRecibidas" class="btn btn-default btn-sm btn-flat" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscarRecibidas").trigger("click");
                }
            });
            $("#btnBuscarRecibidas").on("click", e => {
                tableTransferenciasRecibidas.search($input.val()).draw();
            });

            $('#listaTransferenciasRecibidas_wrapper .dt-buttons').append(
                `<div class="col-md-5" style="text-align: center;margin-top: 7px;"><label>Almacén Destino:</label></div>
                    <div class="col-md-4" style="display:flex">
                        <select class="form-control" id="selectAlmacenDestinoRecibido" >
                            <option value="0" selected>Todos los almacenes</option>
                        </select>
                    </div>`
            );
            mostrarAlmacenes('recibido');
            $("#selectAlmacenDestinoRecibido").on("change", function (e) {
                var alm = $(this).val();
                $('input[name=id_almacen_destino_recibida]').val(alm);
                $("#listaTransferenciasRecibidas").DataTable().ajax.reload(null, false);
            });
        },
        drawCallback: function (settings) {
            $("#listaTransferenciasRecibidas_filter input").prop("disabled", false);
            $("#btnBuscarRecibidas").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>'
            ).prop("disabled", false);
            $("#listaTransferenciasRecibidas_filter input").trigger("focus");
        },
        ajax: {
            url: "listarTransferenciasRecibidas",
            type: "POST",
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosRecibidas').serializeArray()))
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
            { data: "codigo" },
            { data: "guia_ven" },
            { data: "guia_com" },
            { data: "doc_ven", className: "text-center" },
            { data: "doc_com" },
            { data: "alm_origen_descripcion", name: "alm_origen.descripcion" },
            { data: "alm_destino_descripcion", name: "alm_destino.descripcion" },
            {
                data: "estado_doc", name: "adm_estado_doc.estado_doc",
                render: function (data, type, row) {
                    return (
                        `<span class="label label-${row["bootstrap_color"]}">${row["estado_doc"]}</span>`
                    );
                }
            },
            { data: "codigo_req", name: "alm_req.codigo" },
            { data: "concepto_req", name: "alm_req.concepto" },
            {
                orderable: false, searchable: false,
                render: function (data, type, row) {
                    if (valor_permiso == "1") {
                        return `<div style="display: flex;text-align:center;">
                            <button type="button" class="detalle btn btn-default btn-flat boton" data-toggle="tooltip"
                                data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                                <i class="fas fa-chevron-down"></i></button>
                            ${(row['doc_ven'] == '-') ?
                                `<button type="button" class="anular btn btn-danger boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row["id_transferencia"]}" data-guia="${row["id_guia_com"]}" data-ing="${row["id_ingreso"]}" title="Anular" >
                                <i class="fas fa-trash"></i></button>`: ''}
                            ${row['id_empresa_origen'] !== row['id_empresa_destino'] ?

                                `<button type="button" class="autogenerar btn btn-success boton btn-flat" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row["id_doc_ven"]}" data-dc="${row["doc_com"]}" title="Autogenerar Docs de Compra" >
                        <i class="fas fa-sync-alt"></i></button>`: ''}
                            
                            </div>`;
                    }
                },
                className: "text-center"
            }
        ],
        columnDefs: [
            { aTargets: [0], sClass: "invisible" },
            {
                render: function (data, type, row) {
                    return (row["guia_ven"] == '-' ? row["guia_ven"]
                        : `<a href="#" class="transferencia" title="Ver Detalle" data-id="${row["id_transferencia"]}" 
                            data-cod="${row["codigo"]}" data-guia="${row["guia_com"]}" 
                            data-origen="${row["alm_origen_descripcion"]}" data-destino="${row["alm_destino_descripcion"]}">
                            ${row["codigo"]} </a>`
                    );
                }, targets: 2, className: "text-center"
            },
            {
                render: function (data, type, row) {
                    return (row["guia_ven"] == '-' ? row["guia_ven"]
                        : '<a href="#" class="salida" data-id-salida="' + row["id_salida"] + '" title="Ver Salida">' + row["guia_ven"] + "</a>"
                    );
                }, targets: 3, className: "text-center"
            },
            {
                render: function (data, type, row) {
                    return (row["guia_com"] == '-' ? row["guia_com"]
                        : '<a href="#" class="ingreso" data-id-ingreso="' + row["id_ingreso"] + '" title="Ver Ingreso">' + row["guia_com"] + "</a>"
                    );
                }, targets: 4, className: "text-center"
            },
            {
                render: function (data, type, row) {
                    return (
                        row["doc_com"] + (row["doc_com"] !== '-' ? ` <i class="fas fa-info-circle blue verDocsAutogenerados" data-id-doc-compra="${row["id_doc_com"]}"
                            style="cursor: pointer;" title="Ver documentos autogenerados"></i>`: '')
                    );
                }, targets: 6, className: "text-center"

            },
            {
                render: function (data, type, row) {
                    if (row["codigo_req"] !== null) {
                        return (
                            `<label class="lbl-codigo" title="Abrir Guía" onClick="abrirRequerimiento(${row["id_requerimiento"]})">
                            ${row["codigo_req"]}</label>`
                        );
                    } else {
                        return "";
                    }
                }, targets: 10, className: "text-center"

            },
        ],
        order: [[0, "desc"]]
    });
}

$('#listaTransferenciasRecibidas tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableTransferenciasRecibidas.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        format(iTableCounter, id, row);
        tr.addClass('shown');
        oInnerTable = $('#listaTransferenciasRecibidas_' + iTableCounter).dataTable({
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

$("#listaTransferenciasRecibidas tbody").on("click", "a.salida", function () {
    var idSalida = $(this).data("idSalida");
    console.log(idSalida);
    if (idSalida !== "") {
        // var id = encode5t(idSalida);
        window.open("imprimir_salida/" + idSalida);
    }
}
);

$("#listaTransferenciasRecibidas tbody").on("click", "a.ingreso", function () {
    var idIngreso = $(this).data("idIngreso");
    if (idIngreso !== "") {
        // var id = encode5t(idIngreso);
        window.open("imprimir_ingreso/" + idIngreso);
    }
});

$("#listaTransferenciasRecibidas tbody").on("click", "a.transferencia", function () {
    var id_transferencia = $(this).data("id");
    if (id_transferencia !== "") {
        window.open("imprimir_transferencia/" + id_transferencia);
    }
    // var codigo = $(this).data("cod");
    // var guia = $(this).data("guia");
    // var origen = $(this).data("origen");
    // var destino = $(this).data("destino");

    // if (id_transferencia !== "") {
    //     $("#modal-transferenciaDetalle").modal({
    //         show: true
    //     });
    //     console.log(codigo);
    //     $("#codigo_transferencia").text(codigo);
    //     $("#nro_guia").text(guia);
    //     $("[name=det_almacen_origen]").val(origen);
    //     $("[name=det_almacen_destino]").val(destino);
    //     detalle_transferencia(id_transferencia);
    // }
});

$("#listaTransferenciasRecibidas tbody").on("click", "button.autogenerar", function () {
    var id = $(this).data("id");
    var dc = $(this).data("dc");
    console.log(id);
    if (id !== null) {
        if (dc == '-') {
            Swal.fire({
                title: "¿Está seguro que desea autogenerar los documentos de compra?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00a65a", //"#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "Cancelar",
                confirmButtonText: "Si, Autogenerar"
            }).then(result => {
                if (result.isConfirmed) {
                    autogenerarDocsCompra(id);
                }
            });
        } else {
            Lobibox.notify("warning", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: "Ya se autogeneraron los documentos de compra."
            });
        }
    } else {
        Lobibox.notify("error", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: "No existe un documento de venta."
        });
    }
});

$("#listaTransferenciasRecibidas tbody").on("click", "i.verDocsAutogenerados", function () {
    var id = $(this).data("idDocCompra");
    console.log('prueba: ' + id);
    $("#modal-verDocsAutogenerados").modal({
        show: true
    });
    verDocumentosAutogenerados(id);
});

function autogenerarDocsCompra(id_doc_ven) {

    $.ajax({
        type: "GET",
        url: "autogenerarDocumentosCompra/" + id_doc_ven,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            if (response == 'ok') {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "Se ha autogenerado los documentos de compra correctamente."
                });
                $("#listaTransferenciasRecibidas").DataTable().ajax.reload();
            } else {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "No se ha podido autogenerar los documentos de compra."
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function detalle_transferencia(id_transferencia) {
    $.ajax({
        type: "GET",
        url: "listarTransferenciaDetalle/" + id_transferencia,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            var html = "";
            var i = 1;
            response.forEach(element => {
                html += `<tr>
                <td>${i}</td>
                <td>${element.codigo}</td>
                <td style="background-color: LightCyan;">${element.part_number !== null ? element.part_number : ""}</td>
                <td style="background-color: LightCyan;">${element.descripcion}</td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                <td>${element.serie !== null
                        ? element.serie + "-" + element.numero
                        : ""
                    }</td>
                <td><span class="label label-${element.bootstrap_color}">${element.estado_doc
                    }</span></td>
                <td>${element.series
                        ? `<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Ver Series" onClick="listarSeries(${element.id_guia_com_det});"></i>`
                        : ""
                    }</td>
                </tr>`;
                i++;
            });
            $("#listaTransferenciaDetalle tbody").html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#listaTransferenciasRecibidas tbody").on("click", "button.anular", function () {
    var id_transferencia = $(this).data("id");
    var id_mov_alm = $(this).data("ing");
    var id_guia = $(this).data("guia");

    if (
        id_transferencia !== null &&
        id_mov_alm !== null &&
        id_guia !== null
    ) {
        Swal.fire({
            title: "¿Está seguro que desea anular el ingreso por transferencia?",
            text: "No podrás revertir esto.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Si, anular"
        }).then(result => {
            if (result.isConfirmed) {
                $("#modal-guia_com_obs").modal({
                    show: true
                });

                $("[name=id_mov_alm]").val(id_mov_alm);
                $("[name=id_transferencia]").val(id_transferencia);
                $("[name=id_guia_com]").val(id_guia);
                $("[name=observacion]").val("");

                $("#submitGuiaObs").removeAttr("disabled");
            }
        });
    }
});

$("#form-obs").on("submit", function (e) {
    console.log("submit");
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anularTransferenciaIngreso(data);
});

function anularTransferenciaIngreso(data) {
    $("#submitGuiaObs").attr("disabled", "true");
    $.ajax({
        type: "POST",
        url: "anularTransferenciaIngreso",
        data: data,
        dataType: "JSON",
        success: function (response) {
            if (response.length > 0) {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: response
                });
                $("#modal-guia_com_obs").modal("hide");
            } else {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg:
                        "Ingreso anulado con éxito. La transferencia ha regresado a la lista de pendientes de recepción."
                });
                $("#modal-guia_com_obs").modal("hide");
                $("#listaTransferenciasRecibidas").DataTable().ajax.reload();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function abrirRequerimiento(id_requerimiento) {
    localStorage.setItem("idRequerimiento", id_requerimiento);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, "_blank");
    win.focus();
}
