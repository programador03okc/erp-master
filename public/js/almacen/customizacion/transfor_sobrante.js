//Sobrantes
function agregar_producto_sobrante() {
    $(this).attr("disabled", "disabled");

    var row = `<tr>
        <td></td>
        <td><input type="text" class="form-control" name="part_number" ></td>
        <td><input type="text" class="form-control" name="descripcion" ></td>
        <td><input type="number" class="form-control calcula" name="cantidad" ></td>
        <td></td>
        <td><input type="number" class="form-control calcula" name="unitario" ></td>
        <td><input type="number" class="form-control" name="total" readOnly></td>
        <td>
        <i class="fas fa-check icon-tabla blue boton add" 
            data-toggle="tooltip" data-placement="bottom" title="Agregar" ></i>
        <i class="fas fa-trash icon-tabla red boton delete" 
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    $("#listaSobrantes").append(row);
}
// Calcula total
$('#listaSobrantes tbody').on("change", ".calcula", function () {
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
$('#listaSobrantes tbody').on("click", ".add", function () {
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
        var part_number = '';
        var descripcion = '';

        input.each(function () {
            if ($(this)[0].name == 'cantidad') {
                cantidad = parseFloat($(this).val());
            }
            else if ($(this)[0].name == 'unitario') {
                unitario = parseFloat($(this).val());
            }
            else if ($(this)[0].name == 'part_number') {
                part_number = $(this).val();
            }
            else if ($(this)[0].name == 'descripcion') {
                descripcion = $(this).val();
            }
            $(this).parent("td").html($(this).val());
        });
        $(this).addClass("hidden");

        var id_trans = $('[name=id_transformacion]').val();
        var data = 'id_transformacion=' + id_trans +
            '&part_number=' + part_number +
            '&descripcion=' + descripcion +
            '&cantidad=' + cantidad +
            '&valor_unitario=' + unitario +
            '&valor_total=' + (cantidad * unitario);
        guardar_sobrante(data);
    }
});

function guardar_sobrante(data) {
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_sobrante',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                alert('Item guardado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_sobrantes(id_trans);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_sobrantes(id_transformacion) {
    $('#listaSobrantes tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listar_sobrantes/' + id_transformacion,
        dataType: 'JSON',
        success: function (response) {
            var html = '';
            var suma_sobrante = 0;
            var est = $('[name=id_estado]').val();

            if (response.length > 0) {

                response.forEach(element => {
                    suma_sobrante += parseFloat(element.valor_total);
                    html += `<tr id="${element.id_sobrante}">
                        <td>${element.codigo !== null ? element.codigo : ''}</td>
                        <td>${element.part_number_prod !== null ? element.part_number_prod : (element.part_number !== null ? element.part_number : '')}</td>
                        <td>${element.descripcion_prod !== null ? element.descripcion_prod : (element.descripcion !== null ? element.descripcion : '')}</td>
                        <td>${element.cantidad}</td>
                        <td>${element.abreviatura !== null ? element.abreviatura : ''}</td>
                        <td>${element.valor_unitario}</td>
                        <td>${element.valor_total}</td>
                        <td style="padding:0px;">
                            ${(est == 24) ? `<i class="fas fa-trash icon-tabla red boton delete" 
                            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>`: ''}
                        </td>
                    </tr>`;
                });
                $('#listaSobrantes tbody').html(html);
                $('[name=total_sobrantes]').text(formatDecimalDigitos(suma_sobrante, 2));
                // var costo_primo = parseFloat($('[name=costo_primo]').text());
                // var total_indirectos = parseFloat($('[name=total_indirectos]').text());
                // $('[name=costo_transformacion]').text(formatDecimalDigitos((costo_primo + total_indirectos - suma_sobrante),2));
                actualizaTotales();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function editar_sobrante(id_sobrante){
//     $("#sob-"+id_sobrante+" td").find("input[name=sob_cantidad]").removeAttr('disabled');
//     $("#sob-"+id_sobrante+" td").find("input[name=sob_valor_unitario]").removeAttr('disabled');

//     $("#sob-"+id_sobrante+" td").find("i.blue").removeClass('visible');
//     $("#sob-"+id_sobrante+" td").find("i.blue").addClass('oculto');
//     $("#sob-"+id_sobrante+" td").find("i.green").removeClass('oculto');
//     $("#sob-"+id_sobrante+" td").find("i.green").addClass('visible');
// }
// function update_sobrante(id_sobrante){
//     var cant = $("#sob-"+id_sobrante+" td").find("input[name=sob_cantidad]").val();
//     var unit = $("#sob-"+id_sobrante+" td").find("input[name=sob_valor_unitario]").val();
//     var tota = $("#sob-"+id_sobrante+" td").find("input[name=sob_valor_total]").val();

//     var data = 'id_sobrante='+id_sobrante+
//             '&cantidad='+cant+
//             '&valor_unitario='+unit+
//             '&valor_total='+tota;
//     console.log(data);

//     $.ajax({
//         type: 'POST',
//         url: 'update_sobrante',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 // alert('Item actualizado con éxito');
//                 $("#sob-"+id_sobrante+" td").find("input").attr('disabled',true);
//                 $("#sob-"+id_sobrante+" td").find("i.blue").removeClass('oculto');
//                 $("#sob-"+id_sobrante+" td").find("i.blue").addClass('visible');
//                 $("#sob-"+id_sobrante+" td").find("i.green").removeClass('visible');
//                 $("#sob-"+id_sobrante+" td").find("i.green").addClass('oculto');
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// Delete row on delete button click
$('#listaSobrantes tbody').on("click", ".delete", function () {
    var anula = confirm("¿Esta seguro que desea anular éste item?");

    if (anula) {
        var idx = $(this).parents("tr")[0].id;
        $(this).parents("tr").remove();
        console.log('idx' + idx);
        if (idx !== '') {
            anular_sobrante(idx);
        }
    }
});
function anular_sobrante(id) {
    $.ajax({
        type: 'GET',
        url: 'anular_sobrante/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                // alert('Item anulado con éxito');
                var id_trans = $('[name=id_transformacion]').val();
                listar_sobrantes(id_trans);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

