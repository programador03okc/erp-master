function modalRequerimiento(){
    $('#modal-requerimiento').modal({
        show: true,
        backdrop: 'true'
    });
    document.querySelector("div[id='modal-requerimiento'] input[id='checkViewTodos']").checked = false; //default false
    listarRequerimiento('ONLY_ACTIVOS');
}

function listarRequerimiento(viewAnulados) {
    
    let url=rutaListaRequerimientoModal+'/'+viewAnulados;
    // if(viewAnulados == true){
        // console.log(url);
    //     url='/logistica/requerimientos_sin_estado';
    // }
    var vardataTables = funcDatatables();
    $('#listaRequerimiento').dataTable({
        bDestroy: true,
        order: [[7, 'desc']],
        info:     true,
        iDisplayLength:10,
        paging:   true,
        searching: true,
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        ajax:url,
        columns: [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'tipo_req_desc'},
            {'data': 'tipo_cliente_desc'},
            {'data': 'alm_req_concepto'},
            {'render':
                function (data, type, row, meta){
                    let cliente = '';
                    if(row.id_cliente != null){
                        cliente = row.cliente_razon_social;
                    } else 
                    if(row.id_persona != null){
                        cliente = row.nombre_persona;
                    }else 
                    if(row.id_almacen != null){
                        cliente = row.almacen_solicitante;
                    } 
                    
                    return cliente;
                }
            },
            {'data': 'usuario'},
            {'data': 'fecha_requerimiento'},
            {'data': 'estado_doc'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}]
    });

    let tablelistareq = document.getElementById(
        'listaRequerimiento_wrapper'
    )
    tablelistareq.childNodes[0].childNodes[0].hidden = true
    // var vardataTables = funcDatatables();
    // $('#listaRequerimiento').dataTable({
    //     'language' : vardataTables[0],
    //     'processing': true,
    //     "scrollX": true,
    //     "info":     false,
    //     "iDisplayLength":10,
    //     "paging":   true,
    //     "searching": true,
    //     "bDestroy": true,
    //     'ajax': url,
    //     'columns': [
    //         {'data': 'id_requerimiento'},
    //         {'data': 'codigo'},
    //         {'data': 'tipo_req_desc'},
    //         {'data': 'alm_req_concepto'},
    //         {'data': 'usuario'},
    //         {'data': 'fecha_requerimiento'},
    //         {'data': 'estado_doc'}
    //     ],
    //     'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    //     'order': [
    //         [5, 'desc']
    //     ]
    // });
}

function inicializarSelect(){
    listar_almacenes();
    listar_sedes();

}

function selectRequerimiento(){
    // console.log("selectRequerimiento");
    var id = $('#id_requerimiento').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    
    if (page=='transferencias'){
        ver_requerimiento(id);
    } else {
        inicializarSelect();
        clearForm(form); //function.js
        changeStateButton('historial'); //init.js
        mostrar_requerimiento(id); // mostrar.js

        var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
        disabledControl(btnTrazabilidadRequerimiento,false);
    }
        // console.log($(":file").filestyle('disabled'));
    $('#modal-requerimiento').modal('hide');
}

$('#listaRequerimiento tbody').on('click', 'tr', function(){
    if ($(this).hasClass('eventClick')){
        $(this).removeClass('eventClick');
    } else {
        $('#listaRequerimiento').dataTable().$('tr.eventClick').removeClass('eventClick');
        $(this).addClass('eventClick');
    }
    var idTr = $(this)[0].firstChild.innerHTML;
    $('.modal-footer #id_requerimiento').text(idTr);
    
});