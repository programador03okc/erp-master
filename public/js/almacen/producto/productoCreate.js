function openProductoCreate(){
    $('#modal-producto').modal('hide');
    $('#modal-productoCreate').modal({
        show: true
    });
    $('[name=id_categoria]').val('');
    $('[name=id_subcategoria]').val('');
    $('[name=id_clasif]').val('');
    $('[name=descripcion]').val('');
    $('[name=part_number]').val('');
    $('[name=id_unidad_medida]').val('');
}

$("[name=id_categoria]").on('change', function() {
    var id = $('[name=id_producto]').val();
    if (id == ''){
        var sel = $(this).find('option:selected').text();
        console.log(sel);
        $('[name=descripcion]').val(sel);
    }
    console.log($(this).val());
});

$("[name=id_subcategoria]").on('change', function() {
    var id = $('[name=id_producto]').val();
    if (id == ''){
        var sel = $(this).find('option:selected').text();
        console.log(sel);
        var cat = $('select[name=id_categoria] option:selected').text();
        $('[name=descripcion]').val(cat+' '+sel+' ');
    }
});

function mayus(e) {
    e.value = e.value.toUpperCase();
}

$("#form-productoCreate").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    guardarProducto(data);
});

function guardarProducto(data){
    $.ajax({
        type: 'POST',
        url: 'guardar_producto',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            
            if (response['msj'].length > 0){
                alert(response['msj']);
            } else {
                var page = $('.page-main').attr('type');
                
                if (page == 'ordenesPendientes'){
                    $('#modal-productoCreate').modal('hide');
                    agregarProducto(response['producto']);
                }
                else if (page == "transformacion"){
                    var sel = {
                        'id_producto': response['producto'].id_producto,
                        'part_number': response['producto'].part_number,
                        'codigo': response['producto'].codigo,
                        'descripcion': response['producto'].descripcion,
                        'unid_med': response['producto'].id_unidad_medida
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
                    $('#modal-productoCreate').modal('hide');
                }
                else {
                    alert('Producto registrado con éxito');
                    $('#modal-productoCreate').modal('hide');
                    detalle_sale.push(response['producto']);
                    mostrarSale();
                }
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}