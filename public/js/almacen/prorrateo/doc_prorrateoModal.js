$(function () {
    /* Seleccionar valor del DataTable */
    $('#listaProrrateo tbody').on('click', 'tr', function () {
        if ($(this).hasClass('eventClick')) {
            $(this).removeClass('eventClick');
        } else {
            $('#listaProrrateo').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var usua = $(this)[0].childNodes[2].innerHTML;

        var page = $('.page-main').attr('type');

        if (page == "prorrateo") {
            $('[name=id_prorrateo]').val(myId);
            $('#registrado_por').text(usua);
            mostrar_prorrateo(myId);
        }
        $('#modal-prorrateo').modal('hide');
    });
});

function listar_prorrateos() {
    var vardataTables = funcDatatables();
    $('#listaProrrateo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'bDestroy': true,
        'ajax': 'mostrar_prorrateos',
        'columns': [
            { 'data': 'id_prorrateo' },
            { 'data': 'codigo' },
            // {'render':
            //     function (data, type, row){
            //         return ('PR-'+row['id_prorrateo']);
            //     }
            // },
            { 'data': 'nombre_corto' }
        ],
        'order': [['0', 'desc']],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}

function prorrateoModal() {
    $('#modal-prorrateo').modal({
        show: true
    });
    listar_prorrateos();
}
