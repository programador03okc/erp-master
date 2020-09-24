function listar_directos(id_transformacion){
    $('#listaServiciosDirectos tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_directos/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            var html = ''; 
            var i = 1;
            response.forEach(element => {
                html += `<tr id="${element.id_directo}">
                    <td>${element.descripcion}</td>
                    <td>${element.valor_total}</td>
                    <td style="padding:0px;">
                        <i class="fas fa-trash icon-tabla red boton delete" 
                        data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
                    </td>
                </tr>`;
                i++;
            });
            $('#listaServiciosDirectos tbody').html(html);
            // total_directo();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function editar_directo(id_directo){
//     $("#dir-"+id_directo+" td").find("input[name=dir_cantidad]").removeAttr('disabled');
//     $("#dir-"+id_directo+" td").find("input[name=dir_valor_unitario]").removeAttr('disabled');

//     $("#dir-"+id_directo+" td").find("i.blue").removeClass('visible');
//     $("#dir-"+id_directo+" td").find("i.blue").addClass('oculto');
//     $("#dir-"+id_directo+" td").find("i.green").removeClass('oculto');
//     $("#dir-"+id_directo+" td").find("i.green").addClass('visible');
// }
// function update_directo(id_directo){
//     var cant = $("#dir-"+id_directo+" td").find("input[name=dir_cantidad]").val();
//     var unit = $("#dir-"+id_directo+" td").find("input[name=dir_valor_unitario]").val();
//     var tota = $("#dir-"+id_directo+" td").find("input[name=dir_valor_total]").val();

//     var data = 'id_directo='+id_directo+
//             '&cantidad='+cant+
//             '&valor_unitario='+unit+
//             '&valor_total='+tota;
//     console.log(data);

//     $.ajax({
//         type: 'POST',
//         url: 'update_directo',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 // alert('Item actualizado con éxito');
//                 $("#dir-"+id_directo+" td").find("input").attr('disabled',true);
//                 $("#dir-"+id_directo+" td").find("i.blue").removeClass('oculto');
//                 $("#dir-"+id_directo+" td").find("i.blue").addClass('visible');
//                 $("#dir-"+id_directo+" td").find("i.green").removeClass('visible');
//                 $("#dir-"+id_directo+" td").find("i.green").addClass('oculto');
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// Delete row on delete button click
$('#listaServiciosDirectos tbody').on("click", ".delete", function(){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    
    if (anula){
        var idx = $(this).parents("tr")[0].id;
        $(this).parents("tr").remove();
        console.log(idx);
        anular_directo(idx);
    }
    // var index = lista_servicios.findIndex(function(item, i){
    //     console.log('idx'+idx+' index'+item.index);
    //     return parseInt(item.index) == parseInt(idx);
    // });
    // console.log(index);
    // if (index !== -1){
    //     lista_servicios.splice(index,1);
    // }
});
function anular_directo(id){
    $.ajax({
        type: 'GET',
        url: 'anular_directo/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item anulado con éxito');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function calcula_directo(id_directo){
    var cant = $('#dir-'+id_directo+' input[name=dir_cantidad]').val();
    var unit = $('#dir-'+id_directo+' input[name=dir_valor_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#dir-'+id_directo+' input[name=dir_valor_total]').val(cant * unit);
    } else {
        $('#dir-'+id_directo+' input[name=dir_valor_total]').val(0);
    }
    total_directo();
}
function total_directo(){
    var total = 0;
    $("input[name=dir_valor_total]").each(function(){
        console.log($(this).val());
        total += parseFloat($(this).val());
    });
    console.log('total='+total);
    $('[name=total_directos]').val(total);

    var tot_materias = $('[name=total_materias]').val();
    var costo_primo = parseFloat(tot_materias) + total;
    console.log('costo_primo:'+costo_primo);
    $('[name=costo_primo]').val(costo_primo);

}

$(".add-new-servicio").on('click',function(){
    $(this).attr("disabled", "disabled");

    var row = `<tr>
        <td><input type="text" class="form-control" name="descripcion" id="descripcion"></td>
        <td><input type="number" class="form-control" name="total" id="total"></td>
        <td>
        <i class="fas fa-check icon-tabla blue boton add" 
            data-toggle="tooltip" data-placement="bottom" title="Agregar" ></i>
        <i class="fas fa-trash icon-tabla red boton delete" 
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    $("#listaServiciosDirectos").append(row);
});

// Add row on add button click
$('#listaServiciosDirectos tbody').on("click", ".add", function(){
    var empty = false;
    var input = $(this).parents("tr").find('input');
    input.each(function(){
        if(!$(this).val()){
            $(this).addClass("error");
            empty = true;
        } else{
            $(this).removeClass("error");
        }
    });
    $(this).parents("tr").find(".error").first().focus();
    if(!empty){
        var descripcion = '';
        var total = 0;

        input.each(function(){
            if ($(this)[0].name == 'descripcion'){
                descripcion = $(this).val();
            } 
            else if ($(this)[0].name == 'total'){
                total = $(this).val();
            }
            $(this).parent("td").html($(this).val());
        });
        $(this).addClass("hidden");

        var id_trans = $('[name=id_transformacion]').val();
        var data = 'id_transformacion='+id_trans+
            '&descripcion='+descripcion+
            '&valor_total='+total;
        guardar_directo(data);
    }		
});

function guardar_directo(data){
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_directo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_directos(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}