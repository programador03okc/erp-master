function agregarContacto() {
    $('#modal-agregar-contacto').modal({
        show: true
    });
    $("#submit_contacto").removeAttr("disabled");

    var id_contribuyente = $('[name=id_contribuyente]').val();

    $('[name=id_contribuyente_contacto]').val(id_contribuyente);
    $('[name=id_contacto]').val('');

    $('[name=direccion]').val('');
    $('[name=ubigeo]').val('');
    $('[name=name_ubigeo]').val('');
    $('[name=telefono]').val('');
    $('[name=email]').val('');
    $('[name=nombre]').val('');
    $('[name=cargo]').val('');
    $('[name=horario]').val('');
}

$('#listaContactos tbody').on("click", "button.seleccionar", function () {
    var id_contacto = $(this).data('id');
    var id_requerimiento = $('[name=id_requerimiento]').val();
    const $boton = $(this);
    $boton.prop('disabled', true);

    $.ajax({
        type: 'GET',
        url: 'seleccionarContacto/' + id_contacto + '/' + id_requerimiento,
        dataType: 'JSON',
    }).done(function (response) {
        $('[name=id_contacto_od]').val(id_contacto);
        mostrarContactos();

    }).always(function () {
        $boton.prop('disabled', false);

    }).fail(function () {
        //console.log('fail');
        alert("error")
        //Cerrar el modal
    });
});

$('#listaContactos tbody').on("click", "button.editar", function () {
    var id_contacto = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: 'mostrarContacto/' + id_contacto,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            $('#modal-agregar-contacto').modal({
                show: true
            });
            $('[name=id_contacto]').val(id_contacto);
            $('[name=id_contribuyente_contacto]').val(response.id_contribuyente);

            $('[name=direccion]').val(response.direccion);
            $('[name=ubigeo]').val(response.ubigeo);
            $('[name=name_ubigeo]').val(response.name_ubigeo);
            $('[name=telefono]').val(response.telefono);
            $('[name=email]').val(response.email);
            $('[name=nombre]').val(response.nombre);
            $('[name=cargo]').val(response.cargo);
            $('[name=horario]').val(response.horario);

            $("#submit_contacto").removeAttr("disabled");
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

$('#listaContactos tbody').on("click", "button.anular", function () {
    var id_contacto = $(this).data('id');

    Swal.fire({
        title: "¿Está seguro que desea anular el contacto?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Anular"
    }).then(result => {

        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: 'anularContacto/' + id_contacto,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    var id_contribuyente = $('[name=id_contribuyente]').val();
                    // var id_contacto = $('[name=id_contacto_od]').val();
                    listarContactos(id_contribuyente);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
});

$("#form-contacto").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var msj = validaContacto();

    if (msj.length > 0) {
        Swal.fire({
            title: "Algunos campos estan en blanco!",
            text: msj,
            icon: "warning",
        });
    }
    else {
        var id_requerimiento = $('[name=id_requerimiento]').val();
        var data = $(this).serialize();
        data += '&id_requerimiento=' + id_requerimiento;
        actualizaContacto(data);
    }
});

function actualizaContacto(data) {
    $("#submit_contacto").attr('disabled', 'true');
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'actualizaDatosContacto',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log('actualizaDatosContacto');
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.id_contacto !== null) {
                $('#modal-agregar-contacto').modal('hide');
                var id_contribuyente = $('[name=id_contribuyente_contacto]').val();
                $('[name=id_contacto_od]').val(response.id_contacto);
                listarContactos(id_contribuyente);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function validaContacto() {
    var telf = $('[name=telefono]').val();
    var cont = $('[name=nombre]').val();
    var msj = '';

    if (telf.trim() == '') {
        msj += '\n Es necesario que ingrese un Teléfono';
    }
    if (cont.trim() == '') {
        msj += '\n Es necesario que ingrese una Nombre';
    }
    return msj;
}
