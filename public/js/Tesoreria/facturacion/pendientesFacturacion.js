class Facturacion {
    constructor() {
        // this.permisoConfirmarDenegarFacturacion = permisoConfirmarDenegarFacturacion;
        //this.listarGuias();
    }

    listarGuias() {
        var vardataTables = funcDatatables();
        // console.time();
        tableGuias = $("#listaGuias").DataTable({
            dom: vardataTables[1],
            buttons: vardataTables[2],
            language: vardataTables[0],
            destroy: true,
            pageLength: 20,
            lengthChange: false,
            serverSide: true,
            ajax: {
                url: "listarGuiasVentaPendientes",
                type: "POST"
            },
            columns: [
                { data: "id_guia_ven" },
                {
                    render: function(data, type, row) {
                        return row["serie"] + "-" + row["numero"];
                    },
                    className: "text-center"
                },
                {
                    render: function(data, type, row) {
                        return formatDate(row["fecha_emision"]);
                    },
                    className: "text-center"
                },
                {
                    data: "sede_descripcion",
                    name: "sis_sede.descripcion",
                    className: "text-center"
                },
                { data: "razon_social", name: "adm_contri.razon_social" },
                {
                    render: function(data, type, row) {
                        if (row["nombre_corto"] !== null) {
                            return row["nombre_corto"];
                        } else if (row["nombre_corto_trans"] !== null) {
                            return row["nombre_corto_trans"];
                        } else {
                            return "";
                        }
                    },
                    className: "text-center"
                },
                { data: "codigo_trans", name: "trans.codigo" },
                {
                    render: function(data, type, row) {
                        return `${
                            row["items_restantes"] > 0
                                ? `<button type="button" class="doc btn btn-success btn-xs" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Factura" 
                            data-guia="${row["id_guia_ven"]}"
                            data-doc="${row["id_doc_ven"]}">
                            <i class="fas fa-plus"></i></button>`
                                : ""
                        }
                        ${
                            row["count_facturas"] > 0
                                ? `<button type="button" class="detalle btn btn-primary btn-xs" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row["id_guia_ven"]}" title="Ver Detalle" >
                                <i class="fas fa-chevron-down"></i></button>`
                                : ""
                        }`;
                    },
                    className: "text-center"
                }
            ],
            columnDefs: [{ aTargets: [0], sClass: "invisible" }]
        });
    }

    listarRequerimientos() {
        var vardataTables = funcDatatables();
        // console.time();
        tableRequerimientos = $("#listaRequerimientos").DataTable({
            dom: vardataTables[1],
            buttons: vardataTables[2],
            language: vardataTables[0],
            destroy: true,
            pageLength: 20,
            lengthChange: false,
            serverSide: true,
            ajax: {
                url: "listarRequerimientosPendientes",
                type: "POST"
            },
            columns: [
                { data: "id_requerimiento" },
                { data: "codigo", className: "text-center" },
                { data: "concepto" },
                {
                    data: "sede_descripcion",
                    name: "sis_sede.descripcion",
                    className: "text-center"
                },
                { data: "razon_social", name: "adm_contri.razon_social" },
                { data: "nombre_corto", name: "sis_usua.nombre_corto" },
                {
                    render: function(data, type, row) {
                        return (
                            // '<a href="#" class="archivos" data-id="' +
                            // row["id_oc_propia"] +
                            // '" data-tipo="' +
                            // row["tipo"] +
                            // '">' +
                            // row["nro_orden"] +
                            // "</a>" +
                            row["orden_am"] !== null
                                ? row["nro_orden"] +
                                      `<br><a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row["id_oc_propia"]}&ImprimirCompleto=1">
                            <span class="label label-success">Ver O.E.</span></a>
                            <a href="${row["url_oc_fisica"]}">
                            <span class="label label-warning">Ver O.F.</span></a>`
                                : ""
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
                    render: function(data, type, row) {
                        return `${
                            row["items_restantes"] > 0
                                ? `<button type="button" class="doc btn btn-success btn-xs" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Factura" 
                            data-req="${row["id_requerimiento"]}"
                            data-doc="${row["id_doc_ven"]}">
                            <i class="fas fa-plus"></i></button>`
                                : ""
                        }
                            ${
                                row["count_facturas"] > 0
                                    ? `<button type="button" class="detalle btn btn-primary btn-xs" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row["id_requerimiento"]}" title="Ver Detalle" >
                                    <i class="fas fa-chevron-down"></i></button>`
                                    : ""
                            }`;
                    },
                    className: "text-center"
                }
            ],
            columnDefs: [{ aTargets: [0], sClass: "invisible" }]
        });
    }
}

$("#listaGuias tbody").on("click", "button.doc", function() {
    var id_guia = $(this).data("guia");
    open_doc_ven_create(id_guia);
});

$("#listaRequerimientos tbody").on("click", "button.doc", function() {
    var id_req = $(this).data("req");
    open_doc_ven_requerimiento_create(id_req);
});

$("#listaRequerimientos tbody").on("click", "button.ver_doc", function() {
    var id_doc = $(this).data("doc");
    documentosVer(id_doc, "requerimiento");
});

$("#listaRequerimientos tbody").on("click", "a.archivos", function(e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");

    obtenerArchivosMgcp(id, tipo);
});

function obtenerArchivosMgcp(id, tipo) {
    console.log("id:" + id + "tipo: " + tipo);
    $.ajax({
        type: "POST",
        url:
            "https://mgcp.okccloud.com/mgcp/ordenes-compra/propias/obtener-informacion-adicional",
        data: { id: id, tipo: tipo },
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            // if (response > 0) {
            //     alert("Comprobante registrado con éxito");
            //     $("#modal-doc_ven_create").modal("hide");
            //     let facturacion = new Facturacion();
            //     facturacion.listarGuias();
            // }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
