
function listarTransferenciasRecibidas() {
    var destino = $("[name=id_almacen_dest_recibida]").val();

    if (destino !== null && destino !== "") {
        var vardataTables = funcDatatables();
        $("#listaTransferenciasRecibidas").DataTable({
            // dom: 'Bfrtip',
            // buttons: vardataTables[2],
            language: vardataTables[0],
            pageLength: 25,
            destroy: true,
            ajax: "listarTransferenciasRecibidas/" + destino,
            // 'ajax': {
            //     url:'listar_transferencias_pendientes/'+alm_origen+'/'+alm_destino,
            //     dataSrc:''
            // },
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
                // {
                //     render: function (data, type, row) {
                //         return formatDate(row["fecha_guia"]);
                //     }
                // },
                { data: "codigo" },
                { data: "guia_ven" },
                { data: "guia_com" },
                { data: "doc_ven" },
                { data: "doc_com" },
                { data: "alm_origen_descripcion" },
                { data: "alm_destino_descripcion" },
                // { data: "nombre_origen" },
                // { data: "nombre_destino" },
                {
                    render: function (data, type, row) {
                        return (
                            `<span class="label label-${row["bootstrap_color"]}">${row["estado_doc"]}</span>`
                        );
                    }
                },
                {
                    render: function (data, type, row) {
                        if (row["codigo_req"] !== null) {
                            return (
                                '<label class="lbl-codigo" title="Abrir Guía" onClick="abrirRequerimiento(' +
                                row["id_requerimiento"] +
                                ')">' +
                                row["codigo_req"] +
                                "</label>"
                            );
                        } else {
                            return "";
                        }
                    }
                },
                {
                    render: function (data, type, row) {
                        if (row["concepto_req"] !== null) {
                            return row["concepto_req"];
                        } else {
                            return "";
                        }
                    }
                },
                {
                    render: function (data, type, row) {
                        if (valor_permiso == "1") {
                            return `<div style="display: flex;text-align:center;">
                            <button type="button" class="detalle btn btn-primary boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" title="Ver Detalle" data-id="${row["id_transferencia"]}" 
                                data-cod="${row["codigo"]}" data-guia="${row["guia_com"]}" 
                                data-origen="${row["alm_origen_descripcion"]}" data-destino="${row["alm_destino_descripcion"]}">
                                <i class="fas fa-list-ul"></i></button>

                            <button type="button" class="ingreso btn btn-warning boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id-ingreso="${row["id_ingreso"]}" title="Ver Ingreso" >
                                <i class="fas fa-file-alt"></i></button>

                            <button type="button" class="anular btn btn-danger boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row["id_transferencia"]}" data-guia="${row["id_guia_com"]}" data-ing="${row["id_ingreso"]}" title="Anular" >
                                <i class="fas fa-trash"></i></button>

                            <button type="button" class="autogenerar btn btn-success boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row["id_doc_ven"]}" data-dc="${row["doc_com"]}" title="Autogenerar Docs de Compra" >
                                <i class="fas fa-sync-alt"></i></button>
                            </div>`;
                        } else {
                            return `<button type="button" class="detalle btn btn-primary boton btn-flat" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Detalle" data-id="${row["id_transferencia"]}" 
                            data-cod="${row["codigo"]}" data-guia="${row["guia_com"]}" 
                            data-origen="${row["alm_origen_descripcion"]}" data-destino="${row["alm_destino_descripcion"]}">
                            <i class="fas fa-list-ul"></i></button>`;
                        }
                    },
                    className: "text-center"
                }
            ],
            columnDefs: [{ aTargets: [0], sClass: "invisible" }],
            order: [[0, "desc"]]
        });
    }
}

$("#listaTransferenciasRecibidas tbody").on("click", "button.ingreso", function () {
    var idIngreso = $(this).data("idIngreso");
    if (idIngreso !== "") {
        var id = encode5t(idIngreso);
        window.open("imprimir_ingreso/" + id);
    }
});

$("#listaTransferenciasRecibidas tbody").on("click", "button.detalle", function () {
    var id_transferencia = $(this).data("id");
    var codigo = $(this).data("cod");
    var guia = $(this).data("guia");
    var origen = $(this).data("origen");
    var destino = $(this).data("destino");

    if (id_transferencia !== "") {
        $("#modal-transferenciaDetalle").modal({
            show: true
        });
        console.log(codigo);
        $("#codigo_transferencia").text(codigo);
        $("#nro_guia").text(guia);
        $("[name=det_almacen_origen]").val(origen);
        $("[name=det_almacen_destino]").val(destino);
        detalle_transferencia(id_transferencia);
    }
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
                Lobibox.notify("warning", {
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
    let url = "/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, "_blank");
    win.focus();
}
