function listarIncidencias() {
    var vardataTables = funcDatatables();
    $('#listaIncidencias').dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        ajax: {
            url: "listarIncidencias",
            type: "POST"
        },
        'columns': [
            { 'data': 'id_incidencia' },
            // { 'data': 'codigo' },
            {
                'data': 'codigo',
                render: function (data, type, row) {
                    return (
                        `<a href="#" class="incidencia" data-id="${row["id_incidencia"]}">${row["codigo"]}</a>`
                    );
                }, className: "text-center"
            },
            { 'data': 'empresa_razon_social', 'name': 'empresa.razon_social' },
            { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
            { 'data': 'concepto', 'name': 'alm_req.concepto' },
            {
                data: 'numero', name: 'guia_ven.numero',
                'render': function (data, type, row) {
                    return (row['serie'] !== null ? row['serie'] + '-' + row['numero'] : '');
                }
            },
            // { 'data': 'nombre', name: 'adm_ctb_contac.nombre' },
            {
                'data': 'nombre', name: 'adm_ctb_contac.nombre',
                render: function (data, type, row) {
                    if (row["nombre"] == null) {
                        return '';
                    } else {
                        return (
                            `<a href="#" class="contacto" 
                            data-nombre="${row["nombre"]}" 
                            data-cargo="${row["cargo"]}"
                            data-telefono="${row["telefono"]}"
                            data-direccion="${row["direccion"]}"
                            data-horario="${row["horario"]}"
                            data-email="${row["email"]}"
                            data-codigo="${row["codigo"]}"
                            data-usuario="${row["usuario_final"]}"
                            >${row["nombre"]}</a>`
                        );
                    }
                }, className: "text-center"
            },
            // { 'data': 'telefono', name: 'adm_ctb_contac.telefono' },
            // { 'data': 'cargo', name: 'adm_ctb_contac.cargo' },
            // { 'data': 'direccion', name: 'adm_ctb_contac.direccion' },
            // { 'data': 'horario', name: 'adm_ctb_contac.horario' },
            {
                data: 'fecha_reporte',
                'render': function (data, type, row) {
                    return (row['fecha_reporte'] !== undefined ? formatDate(row['fecha_reporte']) : '');
                }
            },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            { 'data': 'falla_reportada' },
            {
                'data': 'estado_doc', name: 'incidencia_estado.descripcion',
                'render': function (data, type, row) {
                    return `<span class="label label-${row['bootstrap_color']}">${row['estado_doc']}</span>`;
                }, className: "text-center"
            },
            {
                'render':
                    function (data, type, row) {
                        return `
                        <div class="btn-group" role="group">
                            <button type="button" class="agregar btn btn-success boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_incidencia']}" title="Agregar ficha reporte" >
                            <i class="fas fa-plus"></i></button>

                            <button type="button" class="cerrar btn btn-primary boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_incidencia']}" title="Cerrar incidencia" >
                            <i class="fas fa-calendar-check"></i></button>

                            <button type="button" class="cancelar btn btn-danger boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_incidencia']}" title="Cancelar incidencia" >
                            <i class="fas fa-ban"></i></button>
                        </div>`;
                    }, className: "text-center"
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}

$('#listaIncidencias tbody').on("click", "button.agregar", function (e) {
    $(e.preventDefault());
    var data = $('#listaIncidencias').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    // $('#modal-fichaReporte').show();
    $('#modal-fichaReporte').modal({
        show: true
    });
    $('[name=padre_id_incidencia]').val(data.id_incidencia);
    $('[name=id_incidencia_reporte]').val('');
    $('.limpiarReporte').val('');
});

$("#listaIncidencias tbody").on("click", "a.incidencia", function (e) {
    var id = $(this).data("id");
    localStorage.setItem("id_incidencia", id);
    var win = window.open("/cas/garantias/incidencias/index", '_blank');
    win.focus();
});

$("#listaIncidencias tbody").on("click", "a.contacto", function (e) {
    $(e.preventDefault());
    $('.limpiarTexto').text();

    $('#modal-datosContacto').modal({
        show: true
    });

    var nombre = $(this).data("nombre");
    var cargo = $(this).data("cargo");
    var telefono = $(this).data("telefono");
    var direccion = $(this).data("direccion");
    var horario = $(this).data("horario");
    var email = $(this).data("email");
    var codigo = $(this).data("codigo");
    var usuario = $(this).data("usuario");

    $(".nombre").text(nombre);
    $(".cargo").text(cargo);
    $(".telefono").text(telefono);
    $(".direccion").text(direccion);
    $(".horario").text(horario);
    $(".email").text(email);
    $("#codigo_incidencia").text(codigo);
    $(".usuario_final").text(usuario);
});

$("#form-fichaReporte").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea guardar la ficha reporte?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {

        if (result.isConfirmed) {
            var data = $(this).serialize();
            console.log(data);
            guardarFichaReporte(data);
        }
    });
});

function guardarFichaReporte(data) {
    $("#submit_guardar_reporte").attr('disabled', true);
    var id = $('[name=id_incidencia_reporte]').val();
    var url = '';

    if (id !== '') {
        url = 'actualizarFichaReporte';
    } else {
        url = 'guardarFichaReporte';
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });

            $("#submit_guardar_reporte").attr('disabled', false);
            $('#modal-fichaReporte').modal('hide');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}