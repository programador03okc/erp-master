$(function () {
    $('#listaTransformaciones tbody').on('click', 'tr', function () {
        if ($(this).hasClass('eventClick')) {
            $(this).removeClass('eventClick');
        } else {
            $('#listaTransformaciones').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        if (id !== '') {
            var page = $('.page-main').attr('type');

            if (page == 'transformacion') {
                mostrar_transformacion(id);
            }
            else if (page == 'customizacion') {
                mostrarCustomizacion(id);
            }
        }
        $('#modal-transformacion').modal('hide');
    });
});

function listarTransformaciones(tipo) {
    var vardataTables = funcDatatables();
    $('#listaTransformaciones').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'ajax': 'listar_transformaciones/' + tipo,
        'columns': [
            { 'data': 'id_transformacion' },
            { 'data': 'codigo' },
            { 'data': 'codigo_oportunidad' },
            { 'data': 'cod_req' },
            {
                'render':
                    function (data, type, row) {
                        return (row['serie'] !== null ? (row['serie'] + '-' + row['numero']) : '');
                    }
            },
            {
                'render':
                    function (data, type, row) {
                        return ('<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>');
                    }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        'order': [[0, "desc"]],
    });
}

function transformacionModal(tipo) {
    $('#modal-transformacion').modal({
        show: true
    });
    clearDataTable();
    listarTransformaciones(tipo);
}
