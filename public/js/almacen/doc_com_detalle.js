// function editar_detalle(id){
//     $("#det-"+id+" td").find("input[name=cantidad]").attr('disabled',false);
//     $("#det-"+id+" td").find("input[name=precio_unitario]").attr('disabled',false);
//     $("#det-"+id+" td").find("input[name=porcen_dscto]").attr('disabled',false);
//     $("#det-"+id+" td").find("input[name=total_dscto]").attr('disabled',false);
//     $("#det-"+id+" td").find("i.blue").removeClass('visible');
//     $("#det-"+id+" td").find("i.blue").addClass('oculto');
//     $("#det-"+id+" td").find("i.green").removeClass('oculto');
//     $("#det-"+id+" td").find("i.green").addClass('visible');
// }
function editar_detalle(id){
    $('#modal-doc_compra_detalle').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_doc_detalle/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=codigo]').val(response.codigo);
            $('[name=descripcion]').val(response.descripcion);
            $('[name=guia]').val(response.guia);
            $('[name=cantidad_d]').val(response.cantidad);
            $('[name=abreviatura]').val(response.abreviatura);
            $('[name=precio_unitario_d]').val(response.precio_unitario);
            $('[name=porcen_dscto_d]').val(response.porcen_dscto);
            $('[name=porcen_igv]').val(18);
            $('[name=total_dscto_d]').val(response.total_dscto);
            $('[name=sub_total]').val(response.precio_total);
            actualiza_total();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function update_detalle(id){
    var cant = $("#det-"+id+" td").find("input[name=cantidad]").val();
    var unit = $("#det-"+id+" td").find("input[name=precio_unitario]").val();
    var pdes = $("#det-"+id+" td").find("input[name=porcen_dscto]").val();
    var tdes = $("#det-"+id+" td").find("input[name=total_dscto]").val();
    var total = $("#det-"+id+" td").find("input[name=precio_total]").val();

    var data =  'id_doc_det='+id+
                '&cantidad='+cant+
                '&precio_unitario='+unit+
                '&porcen_dscto='+pdes+
                '&total_dscto='+tdes+
                '&precio_total='+total;
    console.log(data);
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'update_doc_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item actualizado con éxito');
                $("#det-"+id+" td").find("input").attr('disabled',true);
                $("#det-"+id+" td").find("i.blue").removeClass('oculto');
                $("#det-"+id+" td").find("i.blue").addClass('visible');
                $("#det-"+id+" td").find("i.green").removeClass('visible');
                $("#det-"+id+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_detalle(id){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_doc_detalle/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Item anulado con éxito');
                    $("#det-"+id).remove();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function calcula_dscto(id){
    var cant = $("#det-"+id+" input[name=cantidad]").val();
    var unit = $("#det-"+id+" input[name=precio_unitario]").val();
    var pdes = $("#det-"+id+" input[name=porcen_dscto]").val();

    if (cant !== '' && unit !== '' && pdes !== ''){
        $('#det-'+id+' input[name=total_dscto]').val(cant * unit * (pdes / 100));
    } else {
        $('#det-'+id+' input[name=total_dscto]').val(0);
    }
    calcula_total(id);
}
function calcula_total(id){
    var cant = $("#det-"+id+" input[name=cantidad]").val();
    var unit = $("#det-"+id+" input[name=precio_unitario]").val();
    var tdes = $("#det-"+id+" input[name=total_dscto]").val();

    console.log('cant'+cant+' unit'+unit+' tdes'+tdes);

    if (cant !== '' && unit !== '') {
        if (tdes !== ''){
            $('#det-'+id+' input[name=precio_total]').val((cant * unit) - tdes);
        } else {
            $('#det-'+id+' input[name=precio_total]').val(cant * unit);
        }
    } else {
        $('#det-'+id+' input[name=precio_total]').val(0);
    }
}
function actualiza_total(){
    var cant = ($('[name=cantidad_d]').val() !== '' ? $('[name=cantidad_d]').val() : 0);
    var unit = ($('[name=precio_unitario_d]').val() !== '' ? $('[name=precio_unitario_d]').val() : 0);
    var dscto = ($('[name=total_dscto_d]').val() !== '' ? $('[name=total_dscto_d]').val() : 0);
    
    var sub_total = (cant * unit).toFixed(2);
    $('[name=sub_total]').val(sub_total);
    
    var porcen_igv = $('[name=porcen_igv]').val();
    var total_igv = (sub_total * porcen_igv / 100).toFixed(2);
    $('[name=total_igv]').val(total_igv);
    
    var total = (parseFloat(sub_total) + parseFloat(total_igv)).toFixed(2);
    $('[name=total]').val(total);
    
    var precio_total = (total - dscto).toFixed(2);
    $('[name=precio_total_d]').val(precio_total);
}
function actualiza_detraccion(){
    var detra = $('select[name="id_detraccion"] option:selected').text();
    var total = $('[name=total]').val();
    var dscto = $('[name=total_dscto_d]').val();
    var det = detra.split(" - ");
    var total_det = 0;
    
    if (det.length > 1){
        var por = det[1].split("%");
        $('[name=porcen_detra]').val(por[0]);
        total_det = (total * por[0] / 100).toFixed(2);
        $('[name=total_detraccion]').val(total_det);
    } else {
        total_det = 0;
        $('[name=total_detraccion]').val(0);
    }
    var precio_total = (total - total_det - dscto).toFixed(2);
    $('[name=precio_total_d]').val(precio_total);
}
function actualiza_dscto(){
    var total = ($('[name=total]').val() !== '' ? $('[name=total]').val() : 0);
    var detra = ($('[name=total_detraccion]').val() !== '' ? $('[name=total_detraccion]').val() : 0);
    var porc = ($('[name=porcen_dscto_d]').val() !== '' ? $('[name=porcen_dscto_d]').val() : 0);
    
    var dscto = (total * porc / 100).toFixed(2);
    $('[name=total_dscto_d]').val(dscto);
    
    var precio_total = (total - detra - dscto).toFixed(2);
    $('[name=precio_total_d]').val(precio_total);
}

