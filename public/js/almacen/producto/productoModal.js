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
        var part = $(this)[0].childNodes[2].innerHTML;
        var desc = $(this)[0].childNodes[3].innerHTML;
        var unid = $(this)[0].childNodes[4].innerHTML;
        var abre = $(this)[0].childNodes[5].innerHTML;

        $('#modal-producto .modal-footer #id_producto').text(idTr);
        $('#modal-producto .modal-footer #codigo').text(code);
        $('#modal-producto .modal-footer #partnumber').text(part);
        $('#modal-producto .modal-footer #descripcion').text(desc);
        $('#modal-producto .modal-footer #unid_med').text(unid);
        $('#modal-producto .modal-footer #abreviatura').text(abre);

        var page = $('.page-main').attr('type');

        if (page == 'guia_venta'){
            var posicion = $(this)[0].childNodes[8].innerHTML;
            $('#modal-producto .modal-footer #posicion').text(posicion);
        }
    });
});

function productoModal(){
    var page = $('.page-main').attr('type');
    console.log('page: '+page);
    var abrir = false;
    $('.nuevo').hide();

    if (page == 'transformacion'){
        abrir = true;
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
        '<th>Part Number</th>'+
        // '<th>Código Antiguo</th>'+
        '<th>Descripción</th>'+
        '<th hidden>unid</th>'+
        '<th hidden>abrev</th>'+
    '</tr>';
    $('#listaProducto thead').html(html);
    $('.nuevo').show();

    var vardataTables = funcDatatables();
    $('#listaProducto').dataTable({
        'dom': vardataTables[1],
        // 'buttons': [],
        // 'buttons': [
        //     {
        //         text: "Crear Producto",
        //         className: 'btn btn-primary',
        //         action: function(){
        //             abrirProducto();
        //         }
        //     }
        // ],
        'language' : vardataTables[0],
        // 'processing': true,
        'bDestroy' : true,
        'ajax': 'mostrar_prods',
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            // {'data': 'cod_antiguo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'data': 'id_unidad_medida'},
            {'data': 'abreviatura'},
        ],
        'columnDefs': [{ 'aTargets': [0,4,5], 'sClass': 'invisible'}],
    });
}
function listarProductosAlmacen(id_almacen){
    var html = '<tr>'+
        '<th></th>'+
        '<th>Código</th>'+
        // '<th>Código Antiguo</th>'+
        '<th>Part Number</th>'+
        '<th>Descripción</th>'+
        '<th hidden>unid</th>'+
        '<th hidden>abreviatura</th>'+
        '<th>Posición</th>'+
        '<th>Stock Actual</th>'+
        '<th hidden>posicion</th>'+
    '</tr>';
    $('#listaProducto thead').html(html);
    $('.nuevo').hide();

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
            // {'data': 'cod_antiguo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'data': 'id_unidad_medida'},
            {'data': 'abreviatura'},
            {'data': 'cod_posicion'},
            {'data': 'stock'},
            {'data': 'id_posicion'},
        ],
        'columnDefs': [{ 'aTargets': [0,4,7,5], 'sClass': 'invisible'}],
    });
}
function selectProducto(){
    var myId = $('#modal-producto .modal-footer #id_producto').text();
    var code = $('#modal-producto .modal-footer #codigo').text();
    var part = $('#modal-producto .modal-footer #partnumber').text();
    var desc = $('#modal-producto .modal-footer #descripcion').text();
    var unid = $('#modal-producto .modal-footer #unid_med').text();
    var abre = $('#modal-producto .modal-footer #abreviatura').text();
    var posi = $('#modal-producto .modal-footer #posicion').text();

    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    if (form == undefined){
        var form = $('.page-main form[type=edition]').attr('id');
    }

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
    else if (page == "transformaciones"){
        console.log(desc);
        var sel = {
            'id_producto': myId,
            'part_number': part,
            'codigo': code,
            'descripcion': desc,
            'unid_med': abre
        }
        agregar_producto(sel);
    }
    else if (page == "transformacion"){
        console.log(desc);
        var sel = {
            'id_producto': myId,
            'part_number': part,
            'codigo': code,
            'descripcion': desc,
            'unid_med': abre
        }
        if (origen == 'transformado'){
            agregar_producto_transformado(sel);
        } 
        else if (origen == 'sobrante'){
            agregar_producto_sobrante(sel);
        }
        else if (origen == 'materia'){
            agregar_producto_materia(sel);
        }
    }
    else if (page == "requerimientosPendientes"){
        var producto = {
            'id_producto': parseInt(myId),
            'part_number': part,
            'codigo': code,
            'descripcion': desc,
            'abreviatura': abre,
            'id_unidad_medida': unid,
            'cantidad': 1,
            'id_detalle_requerimiento': null,
        }
        detalle_sale.push(producto);
        mostrarSale();
    }
    else if (page == 'ordenesPendientes'){
        var sel = {
            'id_producto': myId,
            'part_number': part,
            'codigo': code,
            'descripcion': desc,
            'id_unidad_medida': unid,
            'abreviatura': abre,
            'series': false
        }
        agregarProducto(sel);
    }
    $('#modal-producto').modal('hide');
}
