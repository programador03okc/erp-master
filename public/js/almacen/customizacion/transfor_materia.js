let sel_producto_materia = null;
//Materias Primas
function agregar_producto_materia(sel){
    sel_producto_materia = sel;
 
    console.log(sel);
    var row = `<tr>
        <td>${sel.codigo}</td>
        <td>${sel.part_number!==null?sel.part_number:''}</td>
        <td>${sel.descripcion}</td>
        <td><input type="number" class="form-control calcula" name="cantidad" id="cantidad"></td>
        <td>${sel.unid_med}</td>
        <td><input type="number" class="form-control calcula" name="unitario" id="unitario"></td>
        <td><input type="number" class="form-control" name="total" readOnly id="total"></td>
        <td style="background:Thistle;"></td>
        <td style="background:Thistle;"></td>
        <td style="background:Thistle;"></td>
        <td style="background:Thistle;"></td>
        <td>
        <i class="fas fa-check icon-tabla blue boton add" 
            data-toggle="tooltip" data-placement="bottom" title="Agregar" ></i>
        <i class="fas fa-trash icon-tabla red boton delete" 
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    $("#listaMateriasPrimas").append(row);
}
// Calcula total
$('#listaMateriasPrimas tbody').on("change", ".calcula", function(){
    var cantidad = $(this).parents("tr").find('input[name=cantidad]').val();
    var unitario = $(this).parents("tr").find('input[name=unitario]').val();
    console.log('cantidad'+cantidad+' unitario'+unitario);
    if (cantidad !== '' && unitario !== ''){
        $(this).parents("tr").find('input[name=total]').val(parseFloat(cantidad) * parseFloat(unitario));
    } else {
        $(this).parents("tr").find('input[name=total]').val(0);
    }
});
// Add row on add button click
$('#listaMateriasPrimas tbody').on("click", ".add", function(){
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
        var cantidad = 0;
        var unitario = 0;

        input.each(function(){
            if ($(this)[0].name == 'cantidad'){
                cantidad = parseFloat($(this).val());
            }
            else if ($(this)[0].name == 'unitario'){
                unitario = parseFloat($(this).val());
            }
            $(this).parent("td").html($(this).val());
        });
        $(this).addClass("hidden");

        var id_trans = $('[name=id_transformacion]').val();
        var data = 'id_producto='+sel_producto_materia.id_producto+
                '&id_transformacion='+id_trans+
                '&part_number='+sel_producto_materia.part_number+
                '&descripcion='+sel_producto_materia.descripcion+
                '&cantidad='+cantidad+
                '&valor_unitario='+unitario+
                '&valor_total='+(cantidad * unitario);
        guardar_materia(data);
    }		
});

function guardar_materia(data){
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_materia',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_materias(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_materias(id_transformacion){
    $('#listaMateriasPrimas tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_materias/'+id_transformacion,
        dataType: 'JSON',
        success: function(response){
            var html = '';
            var suma_materias = 0;
            var est = $('[name=id_estado]').val();

            response.forEach(element => {
                suma_materias += parseFloat(element.valor_total);
                html += `<tr id="${element.id_materia}">
                    <td>${element.codigo !== null ? element.codigo : ''}</td>
                    <td>${element.part_number !== null ? element.part_number : (element.part_number_cc!==null?element.part_number_cc:'')}</td>
                    <td>${element.descripcion !== null ? element.descripcion : (element.descripcion_cc!==null?element.descripcion_cc:'')}</td>
                    <td>${element.cantidad}</td>
                    <td>${element.abreviatura !== null ? element.abreviatura : ''}</td>
                    <td>${element.valor_unitario}</td>
                    <td>${element.valor_total}</td>
                    <td style="background:Thistle;">${element.part_number_transformado!==null?element.part_number_transformado:''}</td>
                    <td style="background:Thistle;">${element.descripcion_transformado!==null?element.descripcion_transformado:''}</td>
                    <td style="background:Thistle;">${element.cantidad_transformado!==null?element.cantidad_transformado:''}</td>
                    <td style="background:Thistle;">${element.comentario_transformado!==null?element.comentario_transformado:''}</td>
                    <td style="padding:0px;">
                        ${(est == 24) ? `<i class="fas fa-trash icon-tabla red boton delete" 
                        data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>` : ''}
                    </td>
                </tr>`;
            });
            $('#listaMateriasPrimas tbody').html(html);
            $('[name=total_materias]').text(formatDecimalDigitos(suma_materias,2));
            actualizaTotales();
            // total_materia();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function editar_materia(id_materia){
//     $("#mat-"+id_materia+" td").find("input[name=mat_cantidad]").removeAttr('disabled');
//     $("#mat-"+id_materia+" td").find("input[name=mat_valor_unitario]").removeAttr('disabled');

//     $("#mat-"+id_materia+" td").find("i.blue").removeClass('visible');
//     $("#mat-"+id_materia+" td").find("i.blue").addClass('oculto');
//     $("#mat-"+id_materia+" td").find("i.green").removeClass('oculto');
//     $("#mat-"+id_materia+" td").find("i.green").addClass('visible');
// }
// function update_materia(id_materia){
//     var cant = $("#mat-"+id_materia+" td").find("input[name=mat_cantidad]").val();
//     var unit = $("#mat-"+id_materia+" td").find("input[name=mat_valor_unitario]").val();
//     var tota = $("#mat-"+id_materia+" td").find("input[name=mat_valor_total]").val();

//     var data = 'id_materia='+id_materia+
//             '&cantidad='+cant+
//             '&valor_unitario='+unit+
//             '&valor_total='+tota;
//     console.log(data);

//     $.ajax({
//         type: 'POST',
//         url: 'update_materia',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 // alert('Item actualizado con éxito');
//                 $("#mat-"+id_materia+" td").find("input").attr('disabled',true);
//                 $("#mat-"+id_materia+" td").find("i.blue").removeClass('oculto');
//                 $("#mat-"+id_materia+" td").find("i.blue").addClass('visible');
//                 $("#mat-"+id_materia+" td").find("i.green").removeClass('visible');
//                 $("#mat-"+id_materia+" td").find("i.green").addClass('oculto');
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// Delete row on delete button click
$('#listaMateriasPrimas tbody').on("click", ".delete", function(){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    
    if (anula){
        var idx = $(this).parents("tr")[0].id;
        $(this).parents("tr").remove();
        console.log(idx);
        if (idx !== ''){
            anular_materia(idx);
        }
    }
});
function anular_materia(id){
    $.ajax({
        type: 'GET',
        url: 'anular_materia/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Item anulado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_materias(id_trans);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function calcula_materia(id_materia){
//     var cant = $('#mat-'+id_materia+' input[name=mat_cantidad]').val();
//     var unit = $('#mat-'+id_materia+' input[name=mat_valor_unitario]').val();
//     console.log('cant'+cant+' unit'+unit);
//     if (cant !== '' && unit !== '') {
//         $('#mat-'+id_materia+' input[name=mat_valor_total]').val(cant * unit);
//     } else {
//         $('#mat-'+id_materia+' input[name=mat_valor_total]').val(0);
//     }
//     total_materia();
// }
// function total_materia(){
//     var total = 0;
//     $("input[name=mat_valor_total]").each(function(){
//         console.log($(this).val());
//         total += parseFloat($(this).val());
//     });
//     console.log('total='+total);
//     $('[name=total_materias]').val(total);
// }
