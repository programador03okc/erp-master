function listarIncidencias() {
    var vardataTables = funcDatatables();
    // let botones = [];
    // botones.push({
    //     text: ' Exportar Excel',
    //     action: function () {
    //         exportarIncidencias();
    //     }, className: 'btn-success btnExportarIncidencias'
    // });

    const buttonDescargarExcelIncidencias = ({
        text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar Incidencias',
        attr: {
            id: 'btnDescargarListaIncidenciasCabeceraExcel'
        },
        action: () => {
            descargarExcelIncidencias();

        },
        className: 'btn-success btn-sm'
    }),
    buttonDescargarExcelIncidenciasConHistorial = ({
        text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar a Incidencia con historial',
        attr: {
            id: 'buttonDescargarExcelIncidenciasConHistorial'
        },
        action: () => {
            descargarExcelIncidenciasConHistorial();

        },
        className: 'btn-success btn-sm'
    });

    tableIncidenciasx = $('#listaIncidencias').DataTable({
        dom: vardataTables[1],
        buttons: [buttonDescargarExcelIncidencias,buttonDescargarExcelIncidenciasConHistorial],
        language: vardataTables[0],
        serverSide: true,
        ajax: {
            url: "listarIncidencias",
            type: "POST",
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosIncidencias').serializeArray()))
            }
        },
        'columns': [
            { 'data': 'id_incidencia' },
            // { 'data': 'codigo' },
            {
                'data': 'codigo',
                render: function (data, type, row) {
                    return (
                        `<button type="button" class="detalle btn btn-primary btn-xs" data-toggle="tooltip"
                            data-placement="bottom" data-id="${row['id_incidencia']}" title="Ver fichas reporte" >
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <a href="#" class="incidencia" data-id="${row["id_incidencia"]}">${row["codigo"]}</a>`
                    );
                }
            },
            {
                'data': 'estado_doc', name: 'incidencia_estado.descripcion',
                'render': function (data, type, row) {
                    return `<span class="label label-${row['bootstrap_color']}">${row['estado_doc']}</span>`;
                }, className: "text-center"
            },
            { 'data': 'empresa_razon_social', 'name': 'empresa.razon_social' },
            { 'data': 'cliente' },
            { 'data': 'nro_orden' },
            // {
            //     data: 'numero', name: 'guia_ven.numero',
            //     'render': function (data, type, row) {
            //         return (row['serie'] !== null ? row['serie'] + '-' + row['numero'] : '');
            //     }
            // },
            { 'data': 'factura' },
            {
                'data': 'nombre_contacto',
                render: function (data, type, row) {
                    if (row["nombre_contacto"] == null) {
                        return '';
                    } else {
                        return (
                            `<a href="#" class="contacto"
                            data-nombre="${row["nombre_contacto"]}"
                            data-cargo="${row["cargo_contacto"]}"
                            data-telefono="${row["telefono_contacto"]}"
                            data-direccion="${row["direccion_contacto"]}"
                            data-horario="${row["horario"]}"
                            data-email="${row["email"]}"
                            data-codigo="${row["codigo"]}"
                            data-usuario="${row["usuario_final"]}"
                            >${row["nombre_contacto"]}</a>`
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
            {
                data: 'fecha_documento',
                'render': function (data, type, row) {
                    return (row['fecha_documento'] != null ? formatDate(row['fecha_documento']) : '');
                    
                }
            },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            { 'data': 'falla_reportada' },

            {
                'render':
                    function (data, type, row) {
                        if (row['estado'] == 1 || row['estado'] == 2) {
                            return `
                            <div class="btn-group" role="group">
                                <button type="button" class="btn-clonar btn btn-dark boton"
                                    data-id="${row['id_incidencia']}" title="Clonar registro." >
                                    <i class="fas fa-clone"></i>
                                </button>

                                <button type="button" class="agregar btn btn-success boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_incidencia']}" title="Agregar ficha de atención" >
                                <i class="fas fa-plus"></i></button>

                                <button type="button" class="cerrar btn btn-primary boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_incidencia']}" title="Cerrar incidencia" >
                                <i class="fas fa-calendar-check"></i></button>

                                <button type="button" class="cancelar btn btn-danger boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_incidencia']}" title="Cancelar incidencia" >
                                <i class="fas fa-ban"></i></button>
                            </div>`;
                        } else {
                            return '';
                        }
                    }, className: "text-center"
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        order: [[8, "desc"]],
    });
}

function descargarExcelIncidencias(){
    window.open(`incidenciasExcel`);

}

function descargarExcelIncidenciasConHistorial(){
    window.open(`incidenciasExcelConHistorial`);
 
}



$('#listaIncidencias tbody').on("click", "button.agregar", function (e) {
    $(e.preventDefault());
    var data = $('#listaIncidencias').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    // $('#modal-fichaReporte').show();
    $('#modal-fichaReporte').modal({
        show: true
    });
    $('[name=id_incidencia_reporte]').val('');
    $('.limpiarReporte').val('');

    $('[name=padre_id_incidencia]').val(data.id_incidencia);
    $('[name=id_usuario]').val(data.id_responsable);
    $('[name=fecha_reporte]').val(fecha_actual());
});

$('#listaIncidencias tbody').on("click", "button.cerrar", function (e) {
    $(e.preventDefault());
    var data = $('#listaIncidencias').DataTable().row($(this).parents("tr")).data();
    console.log(data);

    $('#modal-cierreIncidencia').modal({
        show: true
    });
    // $('[name=id_incidencia_cierre]').val('');
    $('.limpiarReporte').val('');

    $('[name=id_incidencia_cierre]').val(data.id_incidencia);
    $('[name=fecha_cierre]').val(fecha_actual());
});

$('#listaIncidencias tbody').on("click", "button.cancelar", function (e) {
    $(e.preventDefault());
    var data = $('#listaIncidencias').DataTable().row($(this).parents("tr")).data();
    console.log(data);

    $('#modal-cancelarIncidencia').modal({
        show: true
    });
    $('.limpiarReporte').val('');

    $('[name=id_incidencia_cancelacion]').val(data.id_incidencia);
    $('[name=fecha_cancelacion]').val(fecha_actual());
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

    var nombre = $(this).data("nombre_contacto");
    var cargo = $(this).data("cargo_contacto");
    var telefono = $(this).data("telefono_contacto");
    var direccion = $(this).data("direccion_contacto");
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

// function exportarIncidencias() {
//     $('#formFiltrosIncidencias').trigger('submit');
// }

$(document).on('click','.btn-clonar',function () {
    var id = $(this).attr('data-id');
    Swal.fire({
        title: 'Clonar',
        text: "¿Está seguro de clonar este registro?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'no',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: 'POST',
                url: 'clonarIncidencia',
                data: {id:id},
                // processData: false,
                // contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    // console.log(data);
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        },
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.status===200) {
                $('#listaIncidencias').DataTable().ajax.reload();
                Swal.fire({
                    title: 'Éxito',
                    text: "Se clono con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        // refrescar pagina o tabla

                    }
                })
            }
        }
    });
});
