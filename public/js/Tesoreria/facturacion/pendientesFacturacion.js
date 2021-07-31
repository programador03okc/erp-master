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
                        return row["orden_am"] !== null ? row["orden_am"] : "";
                        // `<br><a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                        //     <span class="label label-success">Ver O.E.</span></a>
                        // <a href="${row['url_oc_fisica']}">
                        //     <span class="label label-warning">Ver O.F.</span></a>` : '');
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
                        return formatNumber.decimal(
                            row["monto_total"],
                            row["moneda_oc"] == "s" ? "S/" : "$",
                            2
                        );
                    },
                    className: "text-right"
                },
                {
                    data: "nombre_entidad",
                    name: "oc_propias_view.nombre_entidad"
                },
                {
                    data: "nombre_largo_responsable",
                    name: "oc_propias_view.nombre_largo_responsable"
                },
                {
                    render: function(data, type, row) {
                        return (
                            '<label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento(' +
                            row["id_requerimiento"] +
                            ')">' +
                            row["codigo_req"] +
                            "</label>"
                        );
                        // ' <br><strong>'+row['sede_descripcion_req']+'</strong>'+(row['tiene_transformacion'] ? '<br><i class="fas fa-random red" title="Tiene Transformación"></i>' : ''));
                    },
                    className: "text-center"
                },
                {
                    data: "serie",
                    name: "guia_ven.serie",
                    className: "text-center"
                },
                {
                    data: "numero",
                    name: "guia_ven.numero",
                    className: "text-center"
                },
                // {'data': 'nro_documento', 'name': 'adm_contri.nro_documento'},
                // {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
                { data: "fecha_emision", className: "text-center" },
                {
                    data: "sede_descripcion",
                    name: "sis_sede.descripcion",
                    className: "text-center"
                },
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
                    }
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
    documentosVer(id_doc);
});
