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
            { 'data': 'codigo' },
            { 'data': 'empresa_razon_social', 'name': 'empresa.razon_social' },
            { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
            { 'data': 'concepto', 'name': 'alm_req.concepto' },
            {
                data: 'numero', name: 'guia_ven.numero',
                'render': function (data, type, row) {
                    return (row['serie'] !== null ? row['serie'] + '-' + row['numero'] : '');
                }
            },
            { 'data': 'nombre', name: 'adm_ctb_contac.nombre' },
            { 'data': 'telefono', name: 'adm_ctb_contac.telefono' },
            { 'data': 'cargo', name: 'adm_ctb_contac.cargo' },
            { 'data': 'direccion', name: 'adm_ctb_contac.direccion' },
            { 'data': 'horario', name: 'adm_ctb_contac.horario' },
            {
                data: 'fecha_reporte',
                'render': function (data, type, row) {
                    return (row['fecha_reporte'] !== undefined ? formatDate(row['fecha_reporte']) : '');
                }
            },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            { 'data': 'falla_reportada' },
            {
                'data': 'estado_doc', name: 'estado.estado_doc',
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + '">' +
                        row['estado_doc'] + '</span>';
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
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}