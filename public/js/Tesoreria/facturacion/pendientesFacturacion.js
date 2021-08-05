class Facturacion {
    constructor() {
        // this.permisoConfirmarDenegarFacturacion = permisoConfirmarDenegarFacturacion;
        //this.listarGuias();
    }

    listarGuias() {
        var vardataTables = funcDatatables();
        // console.time();
        $("#listaGuias").DataTable({
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
                        return row["doc_ven"] !== null ? row["doc_ven"] : "";
                    },
                    className: "text-center"
                },
                {
                    render: function(data, type, row) {
                        return row["fecha_doc_ven"] !== null
                            ? formatDate(row["fecha_doc_ven"])
                            : "";
                    },
                    className: "text-center"
                },
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
                // { data: "fecha_emision", className: "text-center" },
                {
                    data: "sede_descripcion",
                    name: "sis_sede.descripcion",
                    className: "text-center"
                },
                // { data: "nro_documento", name: "adm_contri.nro_documento" },
                { data: "razon_social", name: "adm_contri.razon_social" },
                // { data: "nombre_corto", name: "sis_usua.nombre_corto" },
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
                // { data: "codigo_req", name: "alm_req.codigo" },
                // {
                //     render: function(data, type, row) {
                //         return row["orden_am"] !== null ? row["orden_am"] : "";
                //     },
                //     className: "text-center"
                // },
                // {
                //     data: "codigo_oportunidad",
                //     name: "oc_propias_view.codigo_oportunidad",
                //     className: "text-center"
                // },
                // {
                //     render: function(data, type, row) {
                //         if (row["monto_total"] !== null) {
                //             return formatNumber.decimal(
                //                 row["monto_total"],
                //                 row["moneda_oc"] == "s" ? "S/" : "$",
                //                 2
                //             );
                //         } else {
                //             return "";
                //         }
                //     },
                //     className: "text-right"
                // },
                {
                    render: function(data, type, row) {
                        return `<button type="button" class="${
                            row["count_facturas"] > 0 ? "ver_doc" : "doc"
                        } btn btn-${
                            row["count_facturas"] > 0 ? "info" : "default"
                        } btn-xs" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Factura" 
                            data-guia="${row["id_guia_ven"]}"
                            data-doc="${row["id_doc_ven"]}">
                            <i class="fas fa-file-medical"></i></button>`;
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
        $("#listaRequerimientos").DataTable({
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
                {
                    render: function(data, type, row) {
                        return row["doc_ven"] !== null ? row["doc_ven"] : "";
                    },
                    className: "text-center"
                },
                {
                    render: function(data, type, row) {
                        return row["fecha_doc_ven"] !== null
                            ? formatDate(row["fecha_doc_ven"])
                            : "";
                    },
                    className: "text-center"
                },
                { data: "codigo", className: "text-center" },
                { data: "concepto" },
                // {
                //     render: function(data, type, row) {
                //         return formatDate(row["fecha_requerimiento"]);
                //     },
                //     className: "text-center"
                // },
                // { data: "fecha_requerimiento", className: "text-center" },
                {
                    data: "sede_descripcion",
                    name: "sis_sede.descripcion",
                    className: "text-center"
                },
                // {
                //     render: function(data, type, row) {
                //         return row["nro_documento"] !== undefined
                //             ? row["nro_documento"]
                //             : "";
                //     },
                //     className: "text-center"
                // },
                // { data: "nro_documento", name: "adm_contri.nro_documento" },
                { data: "razon_social", name: "adm_contri.razon_social" },
                { data: "nombre_corto", name: "sis_usua.nombre_corto" },
                {
                    render: function(data, type, row) {
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
                // {
                //     render: function(data, type, row) {
                //         if (row["monto_total"] !== null) {
                //             return formatNumber.decimal(
                //                 row["monto_total"],
                //                 row["moneda_oc"] == "s" ? "S/" : "$",
                //                 2
                //             );
                //         } else {
                //             return "";
                //         }
                //     },
                //     className: "text-right"
                // },
                {
                    render: function(data, type, row) {
                        return `<button type="button" class="${
                            row["count_facturas"] > 0 ? "ver_doc" : "doc"
                        } btn btn-${
                            row["count_facturas"] > 0 ? "info" : "default"
                        } btn-xs" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Factura" 
                            data-req="${row["id_requerimiento"]}"
                            data-doc="${row["id_doc_ven"]}">
                            <i class="fas fa-file-medical"></i></button>`;
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

$("#listaGuias tbody").on("click", "button.ver_doc", function() {
    var id_doc = $(this).data("doc");
    documentosVer(id_doc, "guia");
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
