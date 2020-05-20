$(function(){
    document.getElementById('btnHistorial').setAttribute('disabled',true);

    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');
    
    $('#listaHistorialAprobación').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar-historial-aprobacion',
        'columns': [
            {'data': 'id_aprobacion'},
            {'data': 'nombre_flujo'},
            {'data': 'codigo_doc'},
            {'data': 'descripcion_vobo'},
            {'data': 'detalle_observacion'},
            {'data': 'nombre_completo_usuario'},
            {'data': 'descripcion_rol_concepto'},
            {'data': 'descripcion_area'},
            {'data': 'fecha_vobo'}
         ],
        'order': [
            [1, 'asc']
        ]
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){

        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        // mostrar_documento(id);
        changeStateButton('historial');
        document.getElementById('btnHistorial').setAttribute('disabled',true);

    });
    resizeSide();

});