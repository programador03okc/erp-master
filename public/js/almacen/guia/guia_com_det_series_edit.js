
function open_guia_series_edit(id_guia_com_det){
    $('#modal-guia_com_barras').modal({
        show: true
    });

    $('#listaBarras tbody').html('');

    $('[name=id_guia_com_det]').val(id_guia_com_det);
    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_producto]').val('');
    $('[name=serie_prod]').val('');
    $('.cabecera').hide();

    mostrar_series(id_guia_com_det);
}

function mostrar_series(id_guia_com_det){
    console.log('id_guia_com_det: '+id_guia_com_det)
    $.ajax({
        type: 'GET',
        url: 'mostrar_series/'+id_guia_com_det,
        dataType: 'JSON',
        success: function(response){
            var tr = '';
            var i = 1;
            if (response.length > 0){
                response.forEach(element => {
                    tr +=`<tr id="${element.id_prod_serie}">
                            <td hidden>0</td>
                            <td class="numero">${i}</td>
                            <td colSpan="2"><input type="text" class="form-control" name="series" value="${element.serie}"/></td>
                        </tr>`;
                    i++;
                });
                $('#listaBarras tbody').html(tr);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}