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
    var origen = $('[name=origen]').val();
    const $boton = $(this);
    $boton.prop('disabled', true);

    if (origen == 'despacho') {
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
            alert("error")
        });
    } else {
        $('[name=id_contacto_od]').val(id_contacto);
        $('[name=id_contacto]').val(id_contacto);
        mostrarContactos();
        let contacto = listaContactos.find(element => element.id_datos_contacto == id_contacto);
        console.log(contacto);
        $("[name=nombre_contacto]").val(contacto.nombre);
        $("[name=cargo_contacto]").val(contacto.cargo);
        $("[name=telefono_contacto]").val(contacto.telefono);
        $("[name=direccion_contacto]").val(contacto.direccion);
        $("[name=id_ubigeo_contacto]").val(contacto.ubigeo);
        $("[name=ubigeo_contacto]").val(contacto.departamento !== null ? (contacto.departamento + '-' + contacto.provincia + '-' + contacto.distrito) : '');
        $(".horario_contacto").text(contacto.horario);
        $(".email_contacto").text(contacto.email);
    }
});

$('#listaContactos tbody').on("click", "button.editar", function () {
    var id_contacto = $(this).data('id');
    mostrarContacto(id_contacto);
});

function mostrarContacto(id_contacto) {
    const $boton = $(this);
    $boton.prop('disabled', true);
    $.ajax({
        type: 'GET',
        url: 'mostrarContacto/' + id_contacto,
        dataType: 'JSON',
    }).done(function (response) {
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

    }).always(function () {
        $boton.prop('disabled', false);

    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

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
            title: "Algunos campos están vacíos",
            text: msj,
            icon: "warning",
        });
    }
    else {
        var id_requerimiento = $('[name=id_requerimiento]').val();
        var origen = $('[name=origen]').val();
        var data = $(this).serialize();
        data += '&id_requerimiento=' + id_requerimiento
            + '&origen=' + origen;
        console.log(data);
        actualizaContacto(data, origen);
    }
});

function actualizaContacto(data, origen) {
    $("#submit_contacto").attr('disabled', 'true');
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

                if (origen == 'incidencia') {
                    $('[name=id_contacto]').val(response.id_contacto);
                    $("[name=nombre_contacto]").val(response.contacto.nombre);
                    $("[name=cargo_contacto]").val(response.contacto.cargo);
                    $("[name=telefono_contacto]").val(response.contacto.telefono);
                    $("[name=direccion_contacto]").val(response.contacto.direccion);
                    $("[name=id_ubigeo_contacto]").val(response.contacto.ubigeo);
                    $("[name=ubigeo_contacto]").text(response.contacto.departamento !== null ? (response.contacto.departamento + '-' + response.contacto.provincia + '-' + response.contacto.distrito) : '');
                    $(".horario_contacto").text(response.contacto.horario);
                    $(".email_contacto").text(response.contacto.email);
                }
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
