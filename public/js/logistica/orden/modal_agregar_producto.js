function catalogoProductosModal(){  
    $('#modal-catalogo-items').modal({
        show: true,
        backdrop: 'true',
        keyboard: true

    });
    document.querySelector("button[id='btn-crear-producto']").setAttribute('display','none');
    cambiarVisibilidadBtn("btn-crear-producto","ocultar");

    listarItems();

}

$('#listaItems tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaItems').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idItem = $(this)[0].children[0].innerHTML;
        var idProd = $(this)[0].children[1].innerHTML;
        var idServ = $(this)[0].children[2].innerHTML;
        var idEqui = $(this)[0].children[3].innerHTML;
        var codigo = $(this)[0].children[4].innerHTML;
        var partNum = $(this)[0].children[5].innerHTML;
        var categoria = $(this)[0].children[6].innerHTML;
        var subcategoria = $(this)[0].children[7].innerHTML;
        var descri = $(this)[0].children[8].innerHTML;
        var unidad = $(this)[0].children[9].innerHTML;
        var id_unidad = $(this)[0].children[10].innerHTML;
        $('.modal-footer #id_item').text(idItem);
        $('.modal-footer #codigo').text(codigo);
        $('.modal-footer #part_number').text(partNum);
        $('.modal-footer #descripcion').text(descri);
        $('.modal-footer #id_producto').text(idProd);
        $('.modal-footer #id_servicio').text(idServ);
        $('.modal-footer #id_equipo').text(idEqui);
        $('.modal-footer #unidad_medida').text(unidad);
        $('.modal-footer #id_unidad_medida').text(id_unidad);
        $('.modal-footer #categoria').text(categoria);
        $('.modal-footer #subcategoria').text(subcategoria);
});



function listarItems() {
    // console.log('listaItems');
    var vardataTables = funcDatatables();
   var tablaListaItems =  $('#listaItems').dataTable({
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        // "scrollX": true,
        'ajax': '/logistica/mostrar_items',
        'columns': [
            {'data': 'id_item'},
            {'data': 'id_producto'},
            {'data': 'id_servicio'},
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'categoria'},
            {'data': 'subcategoria'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida_descripcion'},
            {'data': 'id_unidad_medida'},
            {'render':
                function (data, type, row){
                    if(row.id_unidad_medida == 1){
                        return ('<button class="btn btn-sm btn-info" onClick="verSaldoProducto('+row.id_producto+ ');">Stock</button>');
                    }else{ 
                        return '';
                    }

                }
            }
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},
            { 'aTargets': [1], 'sClass': 'invisible'},
            { 'aTargets': [2], 'sClass': 'invisible'},
            { 'aTargets': [3], 'sClass': 'invisible'},
            { 'aTargets': [10], 'sClass': 'invisible'}
                    ],
        'order': [
            [8, 'asc']
        ],
        "initComplete": function(settings, json) {
        } 
    });

 

    let tablelistaitem = document.getElementById(
        'listaItems_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    
    let listaItems_filter = document.getElementById(
        'listaItems_filter'
    )
    listaItems_filter.querySelector("input[type='search']").style.width='100%';
}

makeId = () => {
    let ID = "";
    let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ( var i = 0; i < 12; i++ ) {
      ID += characters.charAt(Math.floor(Math.random() * 36));
    }
    return ID;
}

function selectItem(){  

    let data = {

    'id': makeId(),
    'cantidad': 1,
    'cantidad_a_comprar': 1,
    'codigo_item': null,
    'codigo_producto': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent,
    'codigo_requerimiento': "RC210005",
    'descripcion_adicional': null,
    'descripcion_producto': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent,
    'estado': 0,
    'garantia': null,
    'id_detalle_orden': null,
    'id_detalle_requerimiento': null,
    'id_item': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent,
    'id_tipo_item':1,
    'id_producto': parseInt(document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent),
    'id_requerimiento': null,
    'id_unidad_medida': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent,
    'lugar_despacho': null,
    'part_number': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent,
    'precio_unitario': 0,
    'id_moneda': 1,
    'stock_comprometido': null,
    'subtotal': 0,
    'tiene_transformacion': false,
    'unidad_medida': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='unidad_medida']").textContent
 
    };
    agregarProductoADetalleOrdenList(data);
    // agregarItemATablaListaDetalleRequerimiento(data);
    // quitarItemDeTablaDetalleCuadroCostos(data);

    $('#modal-catalogo-items').modal('hide');
}


function agregarProductoADetalleOrdenList(data){
    console.log(detalleOrdenList);
    if(typeof detalleOrdenList != 'undefined'){
        detalleOrdenList.push(data);
        loadDetailOrden(detalleOrdenList);

    }else{
        alert("Hubo un problema al agregar el producto al Listado");
    }

}