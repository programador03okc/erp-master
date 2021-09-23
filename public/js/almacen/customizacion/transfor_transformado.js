let sel_producto_transformado = null;
//Transformados
function agregar_producto_transformado(sel) {
    sel_producto_transformado = sel;

    console.log(sel);
    var row = `<tr>
        <td>${sel.codigo}</td>
        <td>${sel.part_number !== null ? sel.part_number : ''}</td>
        <td>${sel.descripcion}</td>
        <td><input type="number" class="form-control calcula" name="cantidad" id="cantidad"></td>
        <td>${sel.unid_med}</td>
        <td><input type="number" class="form-control calcula" name="unitario" id="unitario"></td>
        <td><input type="number" class="form-control" name="total" readOnly id="total"></td>
        <td>
        <i class="fas fa-check icon-tabla blue boton add" 
            data-toggle="tooltip" data-placement="bottom" title="Agregar" ></i>
        <i class="fas fa-trash icon-tabla red boton delete" 
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    $("#listaProductoTransformado").append(row);
}
// Calcula total
$('#listaProductoTransformado tbody').on("change", ".calcula", function () {
    var cantidad = $(this).parents("tr").find('input[name=cantidad]').val();
    var unitario = $(this).parents("tr").find('input[name=unitario]').val();
    console.log('cantidad' + cantidad + ' unitario' + unitario);
    if (cantidad !== '' && unitario !== '') {
        $(this).parents("tr").find('input[name=total]').val(parseFloat(cantidad) * parseFloat(unitario));
    } else {
        $(this).parents("tr").find('input[name=total]').val(0);
    }
});
// Add row on add button click
$('#listaProductoTransformado tbody').on("click", ".add", function () {
    var empty = false;
    var input = $(this).parents("tr").find('input');
    input.each(function () {
        if (!$(this).val()) {
            $(this).addClass("error");
            empty = true;
        } else {
            $(this).removeClass("error");
        }
    });
    $(this).parents("tr").find(".error").first().focus();
    if (!empty) {
        var cantidad = 0;
        var unitario = 0;

        input.each(function () {
            if ($(this)[0].name == 'cantidad') {
                cantidad = parseFloat($(this).val());
            }
            else if ($(this)[0].name == 'unitario') {
                unitario = parseFloat($(this).val());
            }
            $(this).parent("td").html($(this).val());
        });
        $(this).addClass("hidden");

        var id_trans = $('[name=id_transformacion]').val();
        var data = 'id_producto=' + sel_producto_transformado.id_producto +
            '&id_transformacion=' + id_trans +
            '&part_number=' + sel_producto_transformado.part_number +
            '&descripcion=' + sel_producto_transformado.descripcion +
            '&cantidad=' + cantidad +
            '&valor_unitario=' + unitario +
            '&valor_total=' + (cantidad * unitario);
        guardar_transformado(data);
    }
});

function guardar_transformado(data) {
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_transformado',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                alert('Item guardado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_transformados(id_trans);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_transformados(id_transformacion) {
    $('#listaProductoTransformado tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_transformados/' + id_transformacion,
        dataType: 'JSON',
        success: function (response) {
            var html = '';
            var est = $('[name=id_estado]').val();

            if (response.length > 0) {

                response.forEach(element => {
                    html += `<tr id="${element.id_transformado}">
                        <td class="text-center">${element.codigo}</td>
                        <td class="text-center">${element.part_number !== null ? element.part_number : ''}</td>
                        <td>${element.descripcion}</td>
                        <td class="text-right">${element.cantidad}</td>
                        <td>${element.abreviatura}</td>
                        <td class="text-right">${element.valor_unitario}</td>
                        <td class="text-right">${element.valor_total}</td>
                    </tr>`;
                });
                $('#listaProductoTransformado tbody').html(html);
            }
            // total_transformado();
            //     <td style="padding:0px;">
            //     ${(est == 24) ? `<i class="fas fa-trash icon-tabla red boton delete" 
            //     data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>` : ''}
            // </td>
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function editar_transformado(id_transformado){
//     $("#tra-"+id_transformado+" td").find("input[name=tra_cantidad]").removeAttr('disabled');
//     $("#tra-"+id_transformado+" td").find("input[name=tra_valor_unitario]").removeAttr('disabled');

//     $("#tra-"+id_transformado+" td").find("i.blue").removeClass('visible');
//     $("#tra-"+id_transformado+" td").find("i.blue").addClass('oculto');
//     $("#tra-"+id_transformado+" td").find("i.green").removeClass('oculto');
//     $("#tra-"+id_transformado+" td").find("i.green").addClass('visible');
// }
// function update_transformado(id_transformado){
//     var cant = $("#tra-"+id_transformado+" td").find("input[name=tra_cantidad]").val();
//     var unit = $("#tra-"+id_transformado+" td").find("input[name=tra_valor_unitario]").val();
//     var tota = $("#tra-"+id_transformado+" td").find("input[name=tra_valor_total]").val();

//     var data = 'id_transformado='+id_transformado+
//             '&cantidad='+cant+
//             '&valor_unitario='+unit+
//             '&valor_total='+tota;
//     console.log(data);

//     $.ajax({
//         type: 'POST',
//         url: 'update_transformado',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 // alert('Item actualizado con éxito');
//                 $("#tra-"+id_transformado+" td").find("input").attr('disabled',true);
//                 $("#tra-"+id_transformado+" td").find("i.blue").removeClass('oculto');
//                 $("#tra-"+id_transformado+" td").find("i.blue").addClass('visible');
//                 $("#tra-"+id_transformado+" td").find("i.green").removeClass('visible');
//                 $("#tra-"+id_transformado+" td").find("i.green").addClass('oculto');
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// Delete row on delete button click
$('#listaProductoTransformado tbody').on("click", ".delete", function () {
    var anula = confirm("¿Esta seguro que desea anular éste item?");

    if (anula) {
        var idx = $(this).parents("tr")[0].id;
        $(this).parents("tr").remove();
        console.log(idx);
        if (idx !== '') {
            anular_transformado(idx);
        }
    }
});
function anular_transformado(id) {
    $.ajax({
        type: 'GET',
        url: 'anular_transformado/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var id_trans = $('[name=id_transformacion]').val();
            listar_transformados(id_trans);
            // if (response > 0){
            //     alert('Item anulado con éxito');
            // }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function calcula_transformado(id_transformado){
//     var cant = $('#tra-'+id_transformado+' input[name=tra_cantidad]').val();
//     var unit = $('#tra-'+id_transformado+' input[name=tra_valor_unitario]').val();
//     console.log('cant'+cant+' unit'+unit);
//     if (cant !== '' && unit !== '') {
//         $('#tra-'+id_transformado+' input[name=tra_valor_total]').val(cant * unit);
//     } else {
//         $('#tra-'+id_transformado+' input[name=tra_valor_total]').val(0);
//     }
//     total_transformado();
// }
// function total_transformado(){
//     var total = 0;
//     $("input[name=tra_valor_total]").each(function(){
//         console.log($(this).val());
//         total += parseFloat($(this).val());
//     });
//     console.log('total='+total);
//     $('[name=total_transformado]').val(total);
// }
