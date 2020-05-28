$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaOrdenes tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaOrdenes').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        // var idCo = $(this)[0].childNodes[1].innerHTML;
        // var idPr = $(this)[0].childNodes[2].innerHTML;
        // var idCn = $(this)[0].childNodes[3].innerHTML;
        // var des = $(this)[0].childNodes[6].innerHTML;
        
        $('.modal-footer #id_orden_compra').text(idTr);
        // $('.modal-footer #cot_razon_social').text(des);
        // $('.modal-footer #id_cotizacion').text(idCo);
        // $('.modal-footer #id_prov').text(idPr);
        // $('.modal-footer #id_contri').text(idCn);
    });
});

function ordenModal(){
    clearDataTable();
    var abrir = true;
    let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
    if (formName =='orden'){
        listar_ordenes();
    } 
    else if (formName =='guia_compra'){
        var id_proveedor = $('[name=id_proveedor]').val();
        if (id_proveedor !== null && id_proveedor !== '' && id_proveedor !== 0){
            listar_ordenes_proveedor(id_proveedor);
        } else {
            alert('No ha ingresado un proveedor!');
            abrir = false;
        }
    }
    if (abrir){
        $('#modal-ordenes').modal({
            show: true
        });    
    }
}

function listar_ordenes(){
    var vardataTables = funcDatatables();
    $('#listaOrdenes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': '/listar_ordenes',
        'bDestroy': true,
        'retrieve': true,
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'monto_total'},
            {'data': 'fecha'},
        ],
        'order': [
            [5, 'desc']
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listar_ordenes_proveedor(id_proveedor){
    var vardataTables = funcDatatables();
    $('#listaOrdenes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': '/listar_ordenes_proveedor/'+id_proveedor,
        'bDestroy': true,
        'retrieve': true,
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'monto_total'},
            {'data': 'fecha'},
        ],
        'order': [
            [5, 'desc']
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function selectOrden(){
    var myId = $('.modal-footer #id_orden_compra').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "orden"){
        $('[name=id_orden_compra]').val(myId);
        listar_detalle_orden(myId);
        mostrar_orden(myId);
    }
    else if (page == "guia_compra"){
        if (myId !== null && myId !== ""){

            var filas = document.querySelectorAll('#oc tbody tr');
            var existe = false;
            filas.forEach(function(e){
                if (e.id == myId){
                    existe = true;
                }
            });
            if (existe){
                alert('La Orden ya esta relacionada!');
            } else {
                open_orden_detalle(myId);
            }
        } else {
            alert('Debe seleccionar una Orden');
        }
    }
    
    $('#modal-ordenes').modal('hide');
}