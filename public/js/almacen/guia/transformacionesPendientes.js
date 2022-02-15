
function listarTransformaciones() {
    var vardataTables = funcDatatables();
    $("#listaTransformaciones").DataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        bDestroy: true,
        serverSide: true,
        // "scrollX": true,
        ajax: {
            url: "listarTransformacionesFinalizadas",
            type: "POST"
        },
        columns: [
            { data: "id_transformacion" },
            // { data: "orden_am", name: "oc_propias.orden_am" },
            // {
            //     data: "codigo_oportunidad",
            //     name: "oportunidades.codigo_oportunidad"
            // },
            // { data: "oportunidad", name: "oportunidades.oportunidad" },
            // { data: "nombre", name: "entidades.nombre" },
            // { data: "codigo" },
            {
                data: "codigo",
                render:
                    function (data, type, row) {
                        return ('<label class="lbl-codigo" title="Abrir Transformación" onClick="abrir_transformacion(' + row['id_transformacion'] + ')">' + row['codigo'] + '</label>');
                    },
                className: "text-center"
            },
            { data: "fecha_transformacion", name: "transformacion.fecha_transformacion", className: "text-center" },
            { data: "almacen_descripcion", name: "alm_almacen.descripcion", className: "text-center" },
            { data: "nombre_responsable", name: "sis_usua.nombre_corto", className: "text-center" },
            { data: "observacion", name: "transformacion.observacion" },
            { data: "cod_req", name: "alm_req.codigo", className: "text-center" },
            { data: "cod_od", name: "orden_despacho.codigo", className: "text-center" },
            // {
            //     render: function (data, type, row) {
            //         return row["serie"] !== null
            //             ? row["serie"] + "-" + row["numero"]
            //             : "";
            //     }
            // },
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

function abrir_transformacion(id_transformacion) {
    console.log('abrir_transformacio' + id_transformacion);
    localStorage.setItem("id_transfor", id_transformacion);
    // location.assign("/logistica/almacen/customizacion/hoja-transformacion/index");
    var win = window.open("/cas/customizacion/hoja-transformacion/index", '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}