function incidenciaProductoCreate() {
    $("#modal-incidenciaProducto").modal({
        show: true
    });
}

function agregarProductoIncidencia() {
    var id = $('[name=id_incidencia]').val();
    var producto = $('[name=producto]').val();
    var marca = $('[name=marca]').val();
    var modelo = $('[name=modelo]').val();
    var serie = $('[name=serie]').val();
    var id_tipo = $('[name=id_tipo]').val();

    listaSeriesProductos.push({
        "id_incidencia_producto": 0,
        "id_incidencia": id,
        "id_prod_serie": null,
        "serie": serie,
        "id_producto": null,
        "codigo": null,
        "part_number": null,
        "descripcion": producto,
        "id_tipo": id_tipo,
        "marca": marca,
        "modelo": modelo,
    });
    $("#modal-incidenciaProducto").modal('hide');
    mostrarListaSeriesProductos();
}