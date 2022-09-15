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
                        `<button type="button" class="detalle btn btn-primary btn-xs" data-toggle="tooltip"
                            data-placement="bottom" data-id="${row['id_devolucion']}" title="Ver Devolución" >
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <a href="#" class="devolucion" data-id="${row["id_devolucion"]}">${row["codigo"]}</a>`
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
            { 'data': 'observacion' },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            {
                'render':
                    function (data, type, row) {
                        if (row['estado'] == 1 || row['estado'] == 2) {
                            return `
                            <div class="btn-group" role="group">
                                <button type="button" class="agregar btn btn-success boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_incidencia']}" title="Agregar ficha técnica" >
                                <i class="fas fa-plus"></i></button>

                                <button type="button" class="cerrar btn btn-primary boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_incidencia']}" title="Conformidad" >
                                <i class="fas fa-check"></i></button>

                            </div>`;
                        } else {
                            return '';
                        }
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

    $('[name=padre_id_devolucion]').val(data.id_incidencia);
});

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