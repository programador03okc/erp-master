function listar_indirectos(id_transformacion){
    $('#listaCostosIndirectos tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_indirectos/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            var html = ''; 
            var suma_indirectos = 0;
            var est = $('[name=id_estado]').val();

            response.forEach(element => {
                suma_indirectos += parseFloat(element.valor_total);
                html += `<tr id="${element.id_indirecto}">
                    <td>${element.cod_item}</td>
                    <td>${element.tasa}</td>
                    <td>${element.parametro}</td>
                    <td>${element.valor_unitario}</td>
                    <td>${element.valor_total}</td>
                    <td style="padding:0px;">
                        ${(est == 24) ? `<i class="fas fa-trash icon-tabla red boton delete" 
                        data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>` : ''}
                    </td>
                </tr>`;
            });
            $('#listaCostosIndirectos tbody').html(html);
            $('[name=total_indirectos]').text(formatDecimalDigitos(suma_indirectos,2));
            actualizaTotales();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function editar_indirecto(id_indirecto){
//     $("#ind-"+id_indirecto+" td").find("input[name=ind_tasa]").removeAttr('disabled');
//     $("#ind-"+id_indirecto+" td").find("input[name=ind_parametro]").removeAttr('disabled');
//     $("#ind-"+id_indirecto+" td").find("input[name=ind_valor_unitario]").removeAttr('disabled');

//     $("#ind-"+id_indirecto+" td").find("i.blue").removeClass('visible');
//     $("#ind-"+id_indirecto+" td").find("i.blue").addClass('oculto');
//     $("#ind-"+id_indirecto+" td").find("i.green").removeClass('oculto');
//     $("#ind-"+id_indirecto+" td").find("i.green").addClass('visible');
// }
// function update_indirecto(id_indirecto){
//     var tasa = $("#ind-"+id_indirecto+" td").find("input[name=ind_tasa]").val();
//     var para = $("#ind-"+id_indirecto+" td").find("input[name=ind_parametro]").val();
//     var unit = $("#ind-"+id_indirecto+" td").find("input[name=ind_valor_unitario]").val();
//     var tota = $("#ind-"+id_indirecto+" td").find("input[name=ind_valor_total]").val();

//     var data = 'id_indirecto='+id_indirecto+
//             '&tasa='+tasa+
//             '&parametro='+para+
//             '&valor_unitario='+unit+
//             '&valor_total='+tota;
//     console.log(data);

//     $.ajax({
//         type: 'POST',
//         url: 'update_indirecto',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 // alert('Item actualizado con éxito');
//                 $("#ind-"+id_indirecto+" td").find("input").attr('disabled',true);
//                 $("#ind-"+id_indirecto+" td").find("i.blue").removeClass('oculto');
//                 $("#ind-"+id_indirecto+" td").find("i.blue").addClass('visible');
//                 $("#ind-"+id_indirecto+" td").find("i.green").removeClass('visible');
//                 $("#ind-"+id_indirecto+" td").find("i.green").addClass('oculto');
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// Delete row on delete button click
$('#listaCostosIndirectos tbody').on("click", ".delete", function(){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    
    if (anula){
        var idx = $(this).parents("tr")[0].id;
        $(this).parents("tr").remove();
        console.log(idx);
        if (idx !== ''){
            anular_indirecto(idx);
        }
    }
});
function anular_indirecto(id){
    $.ajax({
        type: 'GET',
        url: 'anular_indirecto/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item anulado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_indirectos(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function calcula_total(id_indirecto){
//     var cant = $('#ind-'+id_indirecto+' input[name=ind_tasa]').val();
//     var unit = $('#ind-'+id_indirecto+' input[name=ind_valor_unitario]').val();
//     console.log('cant'+cant+' unit'+unit);
//     if (cant !== '' && unit !== '') {
//         $('#ind-'+id_indirecto+' input[name=ind_valor_total]').val(cant * unit);
//     } else {
//         $('#ind-'+id_indirecto+' input[name=ind_valor_total]').val(0);
//     }
//     total_indirecto();
// }
// function total_indirecto(){
//     var total = 0;
//     $("input[name=ind_valor_total]").each(function(){
//         console.log($(this).val());
//         total += parseFloat($(this).val());
//     });
//     console.log('total='+total);
//     $('[name=total_indirectos]').val(total);
// }

$(".add-new-indirecto").on('click',function(){
    $(this).attr("disabled", "disabled");

    var row = `<tr>
        <td><input type="number" class="form-control" name="cod_item" ></td>
        <td><input type="number" class="form-control calcula" name="tasa" ></td>
        <td><input type="number" class="form-control" name="parametro" ></td>
        <td><input type="number" class="form-control calcula" name="unitario" ></td>
        <td><input type="number" class="form-control" name="total" readOnly></td>
        <td>
        <i class="fas fa-check icon-tabla blue boton add" 
            data-toggle="tooltip" data-placement="bottom" title="Agregar" ></i>
        <i class="fas fa-trash icon-tabla red boton delete" 
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    $("#listaCostosIndirectos").append(row);
});
// Calcula total
$('#listaCostosIndirectos tbody').on("change", ".calcula", function(){
    var tasa = $(this).parents("tr").find('input[name=tasa]').val();
    var unitario = $(this).parents("tr").find('input[name=unitario]').val();
    console.log('tasa'+tasa+' unitario'+unitario);
    if (tasa !== '' && unitario !== ''){
        $(this).parents("tr").find('input[name=total]').val(parseFloat(tasa/100) * parseFloat(unitario));
    } else {
        $(this).parents("tr").find('input[name=total]').val(0);
    }
});
// Add row on add button click
$('#listaCostosIndirectos tbody').on("click", ".add", function(){
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
        var cod_item = 0;
        var tasa = 0;
        var parametro = 0;
        var unitario = 0;
        var total = 0;

        input.each(function(){
            if ($(this)[0].name == 'cod_item'){
                cod_item = $(this).val();
            } 
            else if ($(this)[0].name == 'tasa'){
                tasa = $(this).val();
            }
            else if ($(this)[0].name == 'parametro'){
                parametro = $(this).val();
            }
            else if ($(this)[0].name == 'unitario'){
                unitario = $(this).val();
            }
            else if ($(this)[0].name == 'total'){
                total = $(this).val();
            }
            $(this).parent("td").html($(this).val());
        });
        $(this).addClass("hidden");

        var id_trans = $('[name=id_transformacion]').val();
        var data = 'id_transformacion='+id_trans+
            '&cod_item='+cod_item+
            '&tasa='+tasa+
            '&parametro='+parametro+
            '&valor_unitario='+unitario+
            '&valor_total='+total;
        guardar_indirecto(data);
    }		
});
function guardar_indirecto(data){
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_indirecto',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_indirectos(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
