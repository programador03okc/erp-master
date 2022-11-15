$(document).ready(function () {

});
$(document).on('click','.agregar-item',function () {
    var html = '',
        html_option='',
        cantidad_tr= $('[data-table="requerimientos"]')[0].children.length,
        moneda = $('[name="moneda"]').val(),
        simbolo='S/.',
        igv=0,
        random = Math.random();
    $.each(unidades_medida, function (index, element) {
        html_option+='<option value="'+element.id_unidad_medida+'">'+element.descripcion+'</option>'
    });
    if (moneda=='1') {
        simbolo='S/.';
    }
    if (moneda=='2') {
        simbolo='$';
    }

    html = ''+
    '<tr>'+
        // '<td>'+cantidad_tr+'</td>'+
        '<td>'+
            '<input type="text" class="form-control" value="" name="item['+random+'][part_number]" required>'+
            // '<input type="text" class="form-control" value="" name="index['+random+'][part_namber][]" value="1" required>'+
        '</td>'+
        '<td>'+
            '<div class="form-group">'+
                '<textarea class="form-control input-sm" name="item['+random+'][descripcion]" placeholder="Descripción" required></textarea>'+
                // '<input type="text" class="form-control" value="" name="index['+random+'][descripcion][]" value="2" required>'+
            '</div>'+
        '</td>'+
        '<td><select name="item['+random+'][unidad]" class="form-control">'+html_option+'</select></td>'+
        '<td>'+
            '<input type="number" class="form-control cantidad-change" data-input="change" value="" name="item['+random+'][cantidad]" min="1" step="0.01" required>'+
        '</td>'+
        '<td>'+
            '<input type="number" class="form-control precio-unitario-change" data-input="change" value="" name="item['+random+'][precioUnitario]" min="0" step="0.01" required>'+
        '</td>'+
        '<td>'+
            '<span class="simbolo-text">'+simbolo+'</span>'+
            '<span class="subtotal-text">0</span>'+
            '<input type="hidden" value="" name="item['+random+'][subtotal]" step="0.01" data-input="sub-total" required>'+
        '</td>'+
        '<td>'+
            '<div class="form-group">'+
                '<textarea class="form-control input-sm" name="item['+random+'][motivo]" placeholder="Motivo de requerimiento de item (opcional)" required></textarea>'+
            '</div>'+
        '</td>'+
        '<td>'+
            // '<button type="button" class="btn btn-warning btn-xs"><i class="fas fa-paperclip"></i></button>'+
            '<button type="button" class="btn btn-danger btn-xs eliminar-item"><i class="fas fa-trash"></i></button>'+
        '</td>'+
    '</tr>';
    $('[data-table="requerimientos"]').append(html);
});
$(document).on('click','.eliminar-item',function () {
    $(this).closest('tr').remove();
});
$(document).on('change','select[name="moneda"]',function () {
    var simbolo = $(this).val();
    if (simbolo=='1') {
        $('span.simbolo-text').text('S/.');
        $('span[name="simboloMoneda"]').text('S/.');
    }
    if (simbolo=='2') {
        $('span.simbolo-text').text('$');
        $('span[name="simboloMoneda"]').text('$');
    }
});
$(document).on('change','[data-input="change"]',function () {
    var cantidad = parseFloat($(this).closest('tr').find('.cantidad-change')[0].value),
        precio_unitario = parseFloat($(this).closest('tr').find('.precio-unitario-change')[0].value),
        total = 0,
        monto_subtotal=0,
        igv=0,
        ivg_total=0;

    if (parseFloat(cantidad) > 0 && parseFloat(precio_unitario)>0) {
        total = parseFloat(cantidad)*parseFloat(precio_unitario);
    }
    $(this).closest('tr').find('span.subtotal-text').text(total.toFixed(2));
    $(this).closest('tr').find('input[data-input="sub-total"]').val(total.toFixed(2));// .val(total.toFixed(2));

    if (parseFloat(cantidad) > 0 && parseFloat(precio_unitario)>0) {
        $.each($('[data-input="sub-total"]'), function (index, element) {
            if (parseFloat(element.value)>=0) {
                monto_subtotal = parseFloat(monto_subtotal) + parseFloat(element.value);
            }
        });

        $(this).closest('table').find('tfoot').find('tr').find('td').find('label[name="monto_subtotal"]').text(monto_subtotal.toFixed(2));
        $(this).closest('table').find('tfoot').find('tr').find('td').find('input[name="monto_subtotal"]').val(monto_subtotal.toFixed(2));

        if ($('[name="incluye_igv"]').prop("checked")) {
            igv = monto_subtotal*0.18;
            ivg_total = igv + monto_subtotal
            $(this).closest('table').find('tfoot').find('tr').find('td').find('label[name="monto_igv"]').text(igv.toFixed(2));
            $(this).closest('table').find('tfoot').find('tr').find('td').find('input[name="monto_igv"]').val(igv.toFixed(2));
            $(this).closest('table').find('tfoot').find('tr').find('td').find('label[name="monto_total"]').text(ivg_total.toFixed(2));
            $(this).closest('table').find('tfoot').find('tr').find('td').find('input[name="monto_total"]').val(ivg_total.toFixed(2));
        }else{
            $(this).closest('table').find('tfoot').find('tr').find('td').find('label[name="monto_igv"]').text(0);
            $(this).closest('table').find('tfoot').find('tr').find('td').find('input[name="monto_igv"]').val(0);
            $(this).closest('table').find('tfoot').find('tr').find('td').find('label[name="monto_total"]').text(monto_subtotal.toFixed(2));
            $(this).closest('table').find('tfoot').find('tr').find('td').find('input[name="monto_total"]').text(monto_subtotal.toFixed(2));
        }
    }
});
$(document).on('change','[name="incluye_igv"]',function () {
    if ($(this).prop("checked")) {
        $('[data-input="change"]').change();
    }else{
        $('[data-input="change"]').change();
    }
});
$(document).on('submit','[data-form="guardar"]',function (e) {
    e.preventDefault();
    var data =  new FormData($(this)[0]),
        route = $(this).attr('action');
    Swal.fire({
        title: 'Guardar',
        text: "¿Está seguro de guardar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: 'POST',
                url: route,
                data: data,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

          },
      }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.status===200) {
                console.log(result.value);
            }
        }
    })
});
