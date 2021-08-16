let guias_seleccionadas = [];
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
                        if (row["nombre_corto_trans"] !== null) {
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
                        return `<div style="display: flex;">
                        ${
                            row["items_restantes"] > 0
                                ? `<button type="button" class="doc btn btn-success btn-xs btn-flat" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Factura" 
                            data-guia="${row["id_guia_ven"]}"
                            data-doc="${row["id_doc_ven"]}">
                            <i class="fas fa-plus"></i></button>`
                                : ""
                        }
                        ${
                            row["count_facturas"] > 0
                                ? `<button type="button" class="detalle btn btn-primary btn-xs btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row["id_guia_ven"]}" title="Ver Detalle" >
                                <i class="fas fa-chevron-down"></i></button>`
                                : ""
                        }<div/>`;
                    },
                    className: "text-center"
                }
            ],
            drawCallback: function() {
                $('#listaGuias tbody tr td input[type="checkbox"]').iCheck({
                    checkboxClass: "icheckbox_flat-blue"
                });
            },
            columnDefs: [
                // { aTargets: [0], sClass: "invisible" },
                {
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    className: "dt-body-center",
                    checkboxes: {
                        selectRow: true,
                        selectCallback: function(nodes, selected) {
                            $('input[type="checkbox"]', nodes).iCheck("update");
                        },
                        selectAllCallback: function(
                            nodes,
                            selected,
                            indeterminate
                        ) {
                            $('input[type="checkbox"]', nodes).iCheck("update");
                        }
                    }
                }
            ],
            order: [[0, "desc"]]
        });

        $(
            $("#listaGuias")
                .DataTable()
                .table()
                .container()
        ).on("ifChanged", ".dt-checkboxes", function(event) {
            var cell = $("#listaGuias")
                .DataTable()
                .cell($(this).closest("td"));
            cell.checkboxes.select(this.checked);

            var data = $("#listaGuias")
                .DataTable()
                .row($(this).parents("tr"))
                .data();
            console.log(this.checked);

            if (data !== null && data !== undefined) {
                if (this.checked) {
                    guias_seleccionadas.push(data);
                } else {
                    var index = guias_seleccionadas.findIndex(function(
                        item,
                        i
                    ) {
                        return item.id_guia_ven == data.id_guia_ven;
                    });
                    if (index !== null) {
                        guias_seleccionadas.splice(index, 1);
                    }
                }
            }
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
                    render: function(data, type, row) {
                        return `<div style="display: flex;">${
                            row["items_restantes"] > 0
                                ? `<button type="button" class="doc btn btn-success btn-xs btn-flat" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Factura" 
                            data-req="${row["id_requerimiento"]}"
                            data-doc="${row["id_doc_ven"]}">
                            <i class="fas fa-plus"></i></button>`
                                : ""
                        }
                            ${
                                row["count_facturas"] > 0
                                    ? `<button type="button" class="detalle btn btn-primary btn-xs btn-flat" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row["id_requerimiento"]}" title="Ver Detalle" >
                                    <i class="fas fa-chevron-down"></i></button>`
                                    : ""
                            }<div/>`;
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

$("#listaRequerimientos tbody").on("click", "a.archivos", function(e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");

    obtenerArchivosMgcp(id, tipo);
});
