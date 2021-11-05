
function open_despacho_create(data) {

    $('#modal-orden_despacho_create').modal({
        show: true
    });
    $("#submit_orden_despacho").removeAttr("disabled");
    $('#codigo_req').text(data.codigo);
    $('#concepto').text(data.concepto);

    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=id_contribuyente]').val(data.id_contribuyente);

    $('[name=direccion]').val('');
    $('[name=ubigeo]').val('');
    $('[name=name_ubigeo]').val('');
    $('[name=telefono]').val('');
    $('[name=email]').val('');
    $('[name=nombre]').val('');
    $('[name=cargo]').val('');
    $('[name=horario]').val('');
    $('[name=id_contacto]').val('');

    if (data.id_contacto !== null) {
        $('[name=id_contacto]').val(data.id_contacto);
        verDatosContacto(data.id_contacto);
    }
}

function verDatosContacto(id_contacto) {

    if (id_contacto !== null) {
        $.ajax({
            type: 'GET',
            url: 'verDatosContacto/' + id_contacto,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                $('[name=direccion]').val(response.direccion !== null ? response.direccion : '');
                $('[name=ubigeo]').val(response.ubigeo !== null ? response.ubigeo : '');
                $('[name=name_ubigeo]').val(response.ubigeo_descripcion !== null ? response.ubigeo_descripcion : '');
                $('[name=telefono]').val(response.telefono !== null ? response.telefono : '');
                $('[name=email]').val(response.email !== null ? response.email : '');
                $('[name=nombre]').val(response.nombre !== null ? response.nombre : '');
                $('[name=cargo]').val(response.cargo !== null ? response.cargo : '');
                $('[name=horario]').val(response.horario !== null ? response.horario : '');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

$("#form-orden_despacho").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea guardar éstos datos de contacto?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {
            var msj = validaOrdenDespacho();

            if (msj.length > 0) {
                Swal.fire({
                    title: "Algunos campos estan en blanco!",
                    text: msj,
                    icon: "warning",
                });
            }
            else {
                var data = $(this).serialize();
                actualizaContacto(data);
            }
        }
    });
});

function actualizaContacto(data) {
    $("#submit_orden_despacho").attr('disabled', 'true');
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'actualizaDatosContacto',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response = 'ok') {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Datos de contacto actualizados correctamente.'
                });
                $('#modal-orden_despacho_create').modal('hide');
            } else {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Algo salió mal inténtelo de nuevo.'
                });
            }
            $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function validaOrdenDespacho() {
    var ubig = $('[name=ubigeo]').val();
    var dir = $('[name=direccion]').val();
    var telf = $('[name=telefono]').val();
    var cont = $('[name=nombre]').val();
    var msj = '';

    if (ubig == '') {
        msj += '\n Es necesario que ingrese un Ubigeo ';
    }
    if (dir.trim() == '') {
        msj += '\n Es necesario que ingrese una Dirección ';
    }
    if (telf.trim() == '') {
        msj += '\n Es necesario que ingrese un Teléfono';
    }
    if (cont.trim() == '') {
        msj += '\n Es necesario que ingrese una Nombre';
    }
    return msj;
}
