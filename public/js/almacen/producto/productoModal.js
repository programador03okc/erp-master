let accion_origen = null;

$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaProducto tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaProducto').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var code = $(this)[0].childNodes[1].innerHTML;
        var desc = $(this)[0].childNodes[3].innerHTML;
        var unid = $(this)[0].childNodes[5].innerHTML;

        $('.modal-footer #id_producto').text(idTr);
        $('.modal-footer #codigo').text(code);
        $('.modal-footer #descripcion').text(desc);
        $('.modal-footer #unid_med').text(unid);

        var page = $('.page-main').attr('type');

        if (page == 'guia_venta'){
            var posicion = $(this)[0].childNodes[8].innerHTML;
            $('.modal-footer #posicion').text(posicion);
        }
    });
});

function productoModal(){
    var page = $('.page-main').attr('type');
    console.log('page: '+page);
    var abrir = false;

    if (page == 'transformacion'){
        var est = $('[name=cod_estado]').val();
        console.log('estado: '+est);
        if (est == '1'){
            abrir = true;
        } else {
            alert('La transformación ya fue procesada y/o anulada');
        }
    }
    else if (page == 'guia_venta'){
        var estado = $('[name=cod_estado]').val();
        if (estado == 1){
            abrir = true;
        } else {
            alert('Solo puede agregar Productos a las Guías Elaboradas');
        }
    }
    else {
        abrir = true;
    }

    if (abrir){
        $('#modal-producto').modal({
            show: true
        });
        clearDataTable();

        if (page == 'guia_venta'){
            var id_alm = $('[name=id_almacen]').val();
            listarProductosAlmacen(id_alm);
        } 
        else {
            listarProductos();
        }
    }
}
function listarProductos(){
    var html = '<tr>'+
        '<th></th>'+
        '<th>Código</th>'+
        '<th>Código Antiguo</th>'+
        '<th>Descripción</th>'+
        '<th>Part Number</th>'+
        '<th hidden>unid</th>'+
    '</tr>';
    $('#listaProducto thead').html(html);

    var vardataTables = funcDatatables();
    $('#listaProducto').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        // 'processing': true,
        'bDestroy' : true,
        'ajax': 'mostrar_prods',
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'cod_antiguo'},
            {'data': 'descripcion'},
            {'data': 'part_number'},
            {'data': 'id_unidad_medida'},
        ],
        'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
    });
}
function listarProductosAlmacen(id_almacen){
    var html = '<tr>'+
        '<th></th>'+
        '<th>Código</th>'+
        '<th>Código Antiguo</th>'+
        '<th>Descripción</th>'+
        '<th>Part Number</th>'+
        '<th hidden>unid</th>'+
        '<th>Posición</th>'+
        '<th>Stock Actual</th>'+
        '<th hidden>posicion</th>'+
    '</tr>';
    $('#listaProducto thead').html(html);

    var vardataTables = funcDatatables();
    $('#listaProducto').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'mostrar_prods_almacen/'+id_almacen,
        'bDestroy' : true,
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'cod_antiguo'},
            {'data': 'descripcion'},
            {'data': 'part_number'},
            {'data': 'id_unidad_medida'},
            {'data': 'cod_posicion'},
            {'data': 'stock'},
            {'data': 'id_posicion'},
        ],
        'columnDefs': [{ 'aTargets': [0,5,8], 'sClass': 'invisible'}],
    });
}
function selectProducto(){
    var myId = $('.modal-footer #id_producto').text();
    var code = $('.modal-footer #codigo').text();
    var desc = $('.modal-footer #descripcion').text();
    var unid = $('.modal-footer #unid_med').text();
    var posi = $('.modal-footer #posicion').text();

    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    if (form == undefined){
        var form = $('.page-main form[type=edition]').attr('id');
    }
    console.log('form:'+form);

    if (page == "producto"){
        if (form == "form-general"){
            clearForm(form);
            mostrar_producto(myId);
            changeStateButton('historial');
        } 
        else if (form == "form-promocion"){
            // clearDataTable();
            if (accion_origen == 'crear_promocion'){
                crear_promocion(myId);
            } else {
                listar_promociones(myId);
                $('[name=id_producto]').val(myId);
            }
        } 
        else if (form == "form-ubicacion"){
            // clearDataTable();
            listar_ubicaciones(myId);
            var abr = $('[name=abr_id_unidad_medida]').text();
            $('[name=id_producto]').val(myId);
            $('[name=abreviatura]').text(abr);
        } 
        else if (form == "form-serie"){
            // clearDataTable();
            listar_series(myId);
            $('[name=id_producto]').val(myId);
        }
    }
    else if (page == "guia_compra"){
        guardar_guia_detalle(myId,unid);
    }
    else if (page == "guia_venta"){
        guardar_guia_detalle(myId,unid,posi);
    }
    else if (page == "doc_venta"){
        guardar_doc_detalle(myId,unid);
    }
    else if (page == "kardex_detallado"){
        $('[name=id_producto]').val(myId);
        $('[name=descripcion]').val(code+' - '+desc);
        datos_producto(myId);
    }
    else if (page == "transformacion"){
        var acordion = $('#accordion .in')[0].id;
        console.log($('#accordion .in')[0].id);
        if (acordion == "collapseOne"){//materias primas
            guardar_materia(myId);
        }
        else if (acordion == "collapseFour"){//sobrantes
            guardar_sobrante(myId);
        }
        else if (acordion == "collapseFive"){//productos transformados
            guardar_transformado(myId);
        }
    }
    $('#modal-producto').modal('hide');
}
