
function listarTransformaciones() {
    var vardataTables = funcDatatables();
    $("#listaTransformaciones").DataTable({
        // dom: 'Bfrtip',
        // buttons: vardataTables[2],
        language: vardataTables[0],
        bDestroy: true,
        serverSide: true,
        // "scrollX": true,
        ajax: {
            url: "listarTransformacionesProcesadas",
            type: "POST"
        },
        columns: [
            { data: "id_transformacion" },
            { data: "orden_am", name: "oc_propias.orden_am" },
            {
                data: "codigo_oportunidad",
                name: "oportunidades.codigo_oportunidad"
            },
            { data: "oportunidad", name: "oportunidades.oportunidad" },
            { data: "nombre", name: "entidades.nombre" },
            { data: "codigo" },
            {
                data: "fecha_transformacion",
                name: "transformacion.fecha_transformacion"
            },
            { data: "almacen_descripcion", name: "alm_almacen.descripcion" },
            { data: "nombre_responsable", name: "sis_usua.nombre_corto" },
            { data: "cod_od", name: "orden_despacho.codigo" },
            { data: "cod_req", name: "alm_req.codigo" },
            {
                render: function (data, type, row) {
                    return row["serie"] !== null
                        ? row["serie"] + "-" + row["numero"]
                        : "";
                }
            },
            { data: "observacion", name: "transformacion.observacion" },
            {
                render: function (data, type, row) {
                    if (acceso == "1") {
                        return (
                            '<button type="button" class="guia btn btn-info boton" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Ingresar Guía" >' +
                            '<i class="fas fa-sign-in-alt"></i></button>'
                        );

                        // '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                        // 'data-placement="bottom" title="Ver Detalle" >'+
                        // '<i class="fas fa-list-ul"></i></button>';
                    } else {
                        // return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                        //     'data-placement="bottom" title="Ver Detalle" >'+
                        //     '<i class="fas fa-list-ul"></i></button>';
                    }
                }
            }
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[0, "desc"]]
    });
}

$("#listaTransformaciones tbody").on("click", "button.guia", function () {
    var data = $("#listaTransformaciones")
        .DataTable()
        .row($(this).parents("tr"))
        .data();
    open_transformacion_guia_create(data);
});
