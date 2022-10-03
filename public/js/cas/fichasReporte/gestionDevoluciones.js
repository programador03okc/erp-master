function listarDevoluciones() {
    var vardataTables = funcDatatables();
    let botones = [];
    // botones.push({
    //     text: ' Exportar Excel',
    //     action: function () {
    //         exportarIncidencias();
    //     }, className: 'btn-success btnExportarIncidencias'
    // });

    tableDevoluciones = $('#listaDevoluciones').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        serverSide: true,
        ajax: 'listarDevoluciones',
        // ajax: {
        //     url: "listarDevoluciones",
        //     type: "POST",
        //     data: function (params) {
        //         return Object.assign(params, objectifyForm($('#formFiltrosIncidencias').serializeArray()))
        //     }
        // },
        columns: [
            { 'data': 'id_devolucion' },
            {
                'data': 'codigo',
                render: function (data, type, row) {
                    return (
                        `<a href="#" class="ver-devolucion" data-id="${row["id_devolucion"]}">${row["codigo"]}</a>`
                    );
                }
            },
            {
                'data': 'estado_doc', name: 'devolucion_estado.descripcion',
                'render': function (data, type, row) {
                    return `<span class="label label-${row['bootstrap_color']}">${row['estado_doc']}</span>`;
                }, className: "text-center"
            },
            {
                data: 'fecha_registro',
                'render': function (data, type, row) {
                    return (row['fecha_registro'] !== undefined ? formatDate(row['fecha_registro']) : '');
                }
            },
            { 'data': 'tipo' },
            { 'data': 'razon_social', name: 'adm_contri.razon_social' },
            { 'data': 'almacen_descripcion', name: 'alm_almacen.descripcion' },
            { 'data': 'observacion' },
            {
                'render': function (data, type, row) {
                    if (row["count_fichas"] > 0) {
                        return `<a href="#" onClick="verFichasTecnicasAdjuntas(${row["id_devolucion"]});">${row["count_fichas"]} archivos adjuntos </a>`;
                    } else {
                        return ''
                    }
                }, className: "text-center"
            },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            {
                'data': 'usuario_conformidad', name: 'usuario_conforme.nombre_corto',
                'render': function (data, type, row) {
                    if (row["estado"] !== 1) {
                        return `${row["usuario_conformidad"]} el ${formatDateHour(row["fecha_revision"])}`;
                    } else {
                        return '';
                    }
                }, className: "text-center"
            },
            {
                'render':
                    function (data, type, row) {
                        return `
                        <div class="btn-group" role="group">
                            <button type="button" class="agregar btn btn-success boton" data-toggle="tooltip"
                            data-placement="bottom" data-id="${row['id_devolucion']}" title="Agregar ficha técnica" >
                            <i class="fas fa-plus"></i></button>

                            ${row['estado'] == 1 ?
                                `<button type="button" class="conformidad btn btn-primary boton" data-toggle="tooltip"
                            data-placement="bottom" data-id="${row['id_devolucion']}" title="Conformidad" >
                            <i class="fas fa-check"></i></button>`

                                : row['estado'] == 2 ?
                                    `<button type="button" class="revertir btn btn-danger boton" data-toggle="tooltip"
                            data-placement="bottom" data-id="${row['id_devolucion']}" title="Revertir conformidad" >
                            <i class="fas fa-backspace"></i></button>`
                                    : ''
                            }
                        </div>`;
                    }, className: "text-center"
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        order: [[0, "desc"]],
    });
}

$('#listaDevoluciones tbody').on("click", "button.agregar", function (e) {
    $(e.preventDefault());
    var data = $('#listaDevoluciones').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    $('#modal-fichaTecnica').modal({
        show: true
    });
    $('[name=id_ficha]').val('');
    $('.limpiarReporte').val('');

    $('[name=padre_id_devolucion]').val(data.id_devolucion);
});

$("#listaDevoluciones tbody").on("click", "a.ver-devolucion", function () {
    var id = $(this).data("id");
    console.log('id_devolucion ' + id);
    abrirDevolucion(id);
});

$('#listaDevoluciones tbody').on("click", "button.revertir", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    console.log(id);
    $.ajax({
        type: 'GET',
        url: 'revertirConformidad/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $("#listaDevoluciones").DataTable().ajax.reload(null, false);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

$('#listaDevoluciones tbody').on("click", "button.conformidad", function (e) {
    $(e.preventDefault());
    var data = $('#listaDevoluciones').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    $('#modal-devolucionRevisar').modal({
        show: true
    });
    $("[name=id_devolucion]").val(data.id_devolucion);
    $("[name=comentario_revision]").val('');
    $("[name=responsable_revision]").val('');
});

$("#form-devolucionRevisar").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    revisarDevolucion(data);
});

function revisarDevolucion(data) {
    Swal.fire({
        title: "¿Está seguro que dar su conformidad a ésta devolución?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Conforme"
    }).then(result => {
        if (result.isConfirmed) {
            console.log(result);
            $.ajax({
                type: 'POST',
                url: 'conformidadDevolucion',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    $("#listaDevoluciones").DataTable().ajax.reload(null, false);
                    Lobibox.notify(response.tipo, {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });
                    $('#modal-devolucionRevisar').modal('hide');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
}

$("#form-fichaTecnica").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea guardar la ficha técnica?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {
            guardarFichaTecnica();
        }
    });
});

function guardarFichaTecnica() {
    $("#submit_guardar_ficha").attr('disabled', true);
    var formData = new FormData($('#form-fichaTecnica')[0]);

    $.ajax({
        type: 'POST',
        url: 'guardarFichaTecnica',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
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

            $("#listaDevoluciones").DataTable().ajax.reload(null, false);
            $("#submit_guardar_ficha").attr('disabled', false);
            $('#modal-fichaTecnica').modal('hide');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function verFichasTecnicasAdjuntas(id_devolucion) {

    if (id_devolucion !== "") {
        $('#modal-verFichasTecnicasAdjuntas').modal({
            show: true
        });
        $('#adjuntosFichasTecnicas tbody').html('');

        $.ajax({
            type: 'GET',
            url: 'verFichasTecnicasAdjuntas/' + id_devolucion,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.length > 0) {
                    var html = '';
                    response.forEach(function (element) {
                        html += `<tr>
                            <td><a target="_blank" href="/files/cas/devoluciones/fichas/${element.adjunto}">${element.adjunto}</a></td>
                        </tr>`;
                    });
                    $('#adjuntosFichasTecnicas tbody').html(html);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function abrirDevolucion(id_devolucion) {
    console.log('abrirDevolucion' + id_devolucion);
    localStorage.setItem("id_devolucion", id_devolucion);
    // location.assign("/logistica/almacen/customizacion/hoja-transformacion/index");
    var win = window.open("/cas/garantias/devolucionCas/index", '_blank');
    win.focus();
}