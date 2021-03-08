function cleanCharacterReference(text){
    let str = text;
    characterReferenceList=['&nbsp;','nbsp;','&amp;','amp;',"&lt;",/(\r\n|\n|\r)/gm];
    characterReferenceList.forEach(element => {
        while (str.search(element) > -1) {
            str=  str.replace(element,"");

        }
    });
        return str;

}
function crearProducto(){
    $('#modal-crear-nuevo-producto').modal({
        show: true
    });
    $('[name=id_categoria]').val('');
    $('[name=id_subcategoria]').val('');
    // $('[name=id_clasif]').val('');
    $('[name=descripcion]').val('');
    $('[name=part_number]').val('');
    // $('[name=id_unidad_medida]').val('');

    fixCloseModalKeyEscapeDetect();


    var ordenP_Cuadroc = JSON.parse(sessionStorage.getItem('ordenP_Cuadroc'));
    if(ordenP_Cuadroc !== null && ordenP_Cuadroc.hasOwnProperty('tipo_cuadro') && ordenP_Cuadroc.hasOwnProperty('id_cc')){
        // console.log(tempDetalleItemCCSelect);
            document.querySelector("div[id='modal-crear-nuevo-producto'] input[name='part_number']").value = tempDetalleItemCCSelect.part_number?cleanCharacterReference(tempDetalleItemCCSelect.part_number):null;
            document.querySelector("div[id='modal-crear-nuevo-producto'] textarea[name='descripcion']").value= tempDetalleItemCCSelect.descripcion?cleanCharacterReference(tempDetalleItemCCSelect.descripcion):null;

        }

        // console.log(detalleItemsParaCompraCCSelected);
        if(typeof detalleItemsParaCompraCCSelected !== "undefined"){
            if(detalleItemsParaCompraCCSelected.hasOwnProperty('descripcion')){
                if(detalleItemsParaCompraCCSelected.descripcion.length >0){
                    document.querySelector("div[id='modal-crear-nuevo-producto'] input[name='part_number']").value = detalleItemsParaCompraCCSelected.part_no?detalleItemsParaCompraCCSelected.part_no:null;
                    document.querySelector("div[id='modal-crear-nuevo-producto'] textarea[name='descripcion']").value= detalleItemsParaCompraCCSelected.descripcion?detalleItemsParaCompraCCSelected.descripcion:null;
                }
            }
        }


}

function fixCloseModalKeyEscapeDetect(){
    document.onkeydown = function(evt) {
        evt = evt || window.event;
        var isEscape = false;
        if ("key" in evt) {
            isEscape = (evt.key === "Escape" || evt.key === "Esc");
        } else {
            isEscape = (evt.keyCode === 27);
        }
        if (isEscape) {
            // console.log("Escape");
            document.querySelector('div[class=\'modal-backdrop fade in\']').remove();
            document.querySelector('div[class=\'modal-backdrop fade in\']').remove();

        }
    };
}

$("[name=id_categoria]").on('change', function() {
    var id = $('[name=id_producto]').val();
    if (id == ''){
        var sel = $(this).find('option:selected').text();
        // console.log(sel);
        // $('[name=descripcion]').val(sel);
    }
    // console.log($(this).val());
});

$("[name=id_subcategoria]").on('change', function() {
    var id = $('[name=id_producto]').val();
    if (id == ''){
        var sel = $(this).find('option:selected').text();
        // console.log(sel);
        // var cat = $('select[name=id_categoria] option:selected').text();
        // $('[name=descripcion]').val(cat+' '+sel+' ');
    }
});

function mayus(e) {
    e.value = e.value.toUpperCase();
}

$("#form-crear-nuevo-producto").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    // console.log(data);
    guardarProducto(data);
});

function guardarProducto(data){
    $.ajax({
        type: 'POST',
        url: 'guardar-producto',
        data: data,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            
            if (response['msj'].length > 0){
                alert(response['msj']);
            } else {
                alert('Producto registrado con éxito');
                $('#modal-crear-nuevo-producto').modal('hide');
                $('#modal-catalogo-items').modal('hide');

                let id_cc_am_filas = null;
                let id_cc_venta_filas=null;
                if( tempDetalleItemCCSelect.hasOwnProperty('id_cc_am_filas')){
                    id_cc_am_filas = tempDetalleItemCCSelect.id_cc_am_filas;
                }else if(tempDetalleItemCCSelect.hasOwnProperty('id_cc_venta_filas')){
                    id_cc_venta_filas = tempDetalleItemCCSelect.id_cc_venta_filas;
                }

                let item = {
                    'id_detalle_requerimiento': null,
                    'id_item': response.id_item,
                    'codigo': response.producto.codigo,
                    'part_number': response.producto.part_number?response.producto.part_number:'',
                    'des_item': response.producto.descripcion?response.producto.descripcion:'',
                    'cantidad': tempDetalleItemCCSelect.cantidad?tempDetalleItemCCSelect.cantidad:1,
                    'id_producto': response.producto.id_producto,
                    'id_servicio': null,
                    'id_equipo': null,
                    'id_tipo_item': 1,
                    'id_unidad_medida': response.producto.id_unidad_medida,
                    'categoria': response.producto.categoria,
                    'subcategoria': response.producto.subcategoria,
                    'precio_referencial':null,
                    'id_tipo_moneda':1,
                    'lugar_entrega':null,
                    'id_partida':null,
                    'cod_partida':null,
                    'des_partida':null,
                    'id_almacen_reserva':null,
                    'almacen_descripcion':null,
                    'id_cc_am_filas':id_cc_am_filas,
                    'id_cc_venta_filas': id_cc_venta_filas,
                    'tiene_transformacion':document.querySelector("form[id='form-requerimiento'] input[name='tiene_transformacion']").value,
                    'estado':1
                };
                agregarItemATablaListaDetalleRequerimiento(item);
                // listarItems();
                // console.log(response['producto']);
                // let tablaListaItems =  $('#listaItems').dataTable();
                // tablaListaItems .api().search(response['producto']['descripcion']).draw();


            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}