
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
            {
                render: function (data, type, row) {
                    if (acceso == "1") {
                        return (
                            '<button type="button" class="guia btn btn-info boton" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Ingresar Guía" >' +
                            '<i class="fas fa-sign-in-alt"></i></button>'
                        );
                    }
                }
            }
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[0, "desc"]]
    });
}

$("#listaTransformaciones tbody").on("click", "button.guia", function () {
    var data = $("#listaTransformaciones").DataTable().row($(this).parents("tr")).data();
    open_transformacion_guia_create(data);
});

function abrir_transformacion(id_transformacion) {
    console.log('abrir_transformacio' + id_transformacion);
    localStorage.setItem("id_transfor", id_transformacion);
    var win = window.open("/cas/customizacion/hoja-transformacion/index", '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}