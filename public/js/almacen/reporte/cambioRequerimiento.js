let detalle = [];

function listarDetalleRequerimiento(id) {
    $.ajax({
        type: 'GET',
        url: 'listarDetalleRequerimiento/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('#detalleRequerimiento tbody').html('');
            detalle = [];
            response.forEach(function (element) {
                detalle.push({
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_producto': element.id_producto,
                    'codigo': element.codigo,
                    'part_number': element.part_number,
                    'descripcion': element.descripcion,
                    'cantidad': element.cantidad,
                    'entrega_cliente': element.entrega_cliente,
                    'tiene_transformacion': element.tiene_transformacion,
                });
            });

            mostrarDetalleRequerimiento();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarDetalleRequerimiento() {
    var html = '';

    detalle.forEach(function (element) {
        html += `<tr>
            <td><a href="#" class="verProducto" data-id="${element.id_producto}" >${element.codigo}</a></td>
            <td>${element.part_number !== null ? element.part_number : ''}</td>
            <td>${element.descripcion !== null ? element.descripcion : ''}</td>
            <td>${element.cantidad}</td>
            <td><input type="checkbox" class="tiene_transformacion" 
                value="${element.id_detalle_requerimiento}" ${element.tiene_transformacion ? 'checked' : ''}/></td>
            <td><input type="checkbox" class="entrega_cliente" 
                value="${element.id_detalle_requerimiento}" ${element.entrega_cliente ? 'checked' : ''}/></td>
        </tr>`;
    });
    $('#detalleRequerimiento tbody').html(html);
}

$("#detalleRequerimiento tbody").on("click", "input.tiene_transformacion", function (event) {
    let id = $(this).val();
    console.log($(this).prop('checked'));
    let det = detalle.find(element => element.id_detalle_requerimiento == id);
    det.tiene_transformacion = $(this).prop('checked');
    console.log(det.tiene_transformacion);
});

$("#detalleRequerimiento tbody").on("click", "input.entrega_cliente", function (event) {
    let id = $(this).val();
    console.log($(this).prop('checked'));
    let det = detalle.find(element => element.id_detalle_requerimiento == id);
    det.entrega_cliente = $(this).prop('checked');
    console.log(det.entrega_cliente);
});

$("#detalleRequerimiento tbody").on("click", "a.verProducto", function (e) {
    $(e.preventDefault());
    var id_producto = $(this).data("id");
    localStorage.setItem("id_producto", id_producto);
    var win = window.open("/almacen/catalogos/productos/index", '_blank');
    win.focus();
});


$("#form-cambio_requerimiento").on("submit", function (e) {

    e.preventDefault();
    var id = $('[name=id_almacen]').val();
    var req = $('[name=id_requerimiento]').val();
    var listaEnviar = [];

    Swal.fire({
        title: "¿Está seguro que desea guardar los cambios?",
        text: "Los cambios son irreversibles",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {

            detalle.forEach(function (element) {
                listaEnviar.push({
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_producto': element.id_producto,
                    'entrega_cliente': element.entrega_cliente,
                    'tiene_transformacion': element.tiene_transformacion,
                });
            });

            var data = 'id_requerimiento=' + req +
                '&id_almacen=' + id +
                '&detalle=' + JSON.stringify(listaEnviar);

            $.ajax({
                type: 'POST',
                url: 'cambioAlmacen',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    Lobibox.notify("success", {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Se actualizó al almacén: ' + response.codigo + '-' + response.descripcion
                    });
                    $('#modal-cambio_requerimiento').modal('hide');
                    $("#requerimientosAlmacen").DataTable().ajax.reload(null, false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
});
