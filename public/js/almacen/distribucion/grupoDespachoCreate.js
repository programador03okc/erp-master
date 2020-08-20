function crear_grupo_orden_despacho() {
    $('#modal-grupo_despacho_create').modal({
        show: true
    });
    var html = '';
    var i = 1;
    var sede = null;
    var diferentes = 0;

    od_seleccionadas.forEach(element => {
        if (sede == null){
            sede = element.id_sede;
        } 
        else if (element.id_sede !== sede){
            diferentes++;
        }
        html+='<tr id="'+element.id_od+'">'+
        '<td>'+i+'</td>'+
        '<td>'+element.codigo+'</td>'+
        '<td>'+(element.razon_social !== null ? element.razon_social : element.nombre_persona)+'</td>'+
        '<td>'+element.codigo_req+'</td>'+
        '<td>'+element.concepto+'</td>'+
        '<td>'+element.ubigeo_descripcion+'</td>'+
        '<td>'+element.direccion_destino+'</td>'+
        '<td>'+element.fecha_despacho+'</td>'+
        '<td>'+element.fecha_entrega+'</td>'+
        '</tr>';
        i++;
    });

    if (diferentes > 0){
        alert('No puede seleccionar Ordenes de Despacho de sedes distintas!');
        $('#modal-grupo_despacho_create').modal('hide');
    } else {
        $('[name=id_sede_grupo]').val(sede);
        $('#detalleODs tbody').html(html);
        $("#btnGrupoDespacho").removeAttr("disabled");
        $("#proveedor").hide();
        $("#trabajador").show();
        console.log(od_seleccionadas);
    }
}

// $("[name=mov_propia]").on( 'change', function() {
//     if( $(this).is(':checked') ) {
//         $("#proveedor").hide();
//         $("#trabajador").show();
//         $("[name=mov_propia_valor]").val('no');
//     } else {
//         $("#proveedor").show();
//         $("#trabajador").hide();
//         $("[name=mov_propia_valor]").val('si');
//     }
// });
$("[name=mov_entrega]").change( function(e) {
    console.log($(this).val());
    if( $(this).val() == 'Movilidad Propia' ) {
        $("#proveedor").hide();
        $("#trabajador").show();
    } 
    else if( $(this).val() == 'Movilidad de Tercero' ) {
        $("#proveedor").show();
        $("#trabajador").hide();
    }
    else {
        $("#proveedor").hide();
        $("#trabajador").hide();
    }
});

function guardar_grupo_despacho(){
    var resp = $('[name=responsable_grupo]').val();
    var fdes = $('[name=fecha_despacho_grupo]').val();
    var sede = $('[name=id_sede_grupo]').val();
    var move = $('[name=mov_entrega]').val();
    var prov = $('[name=gd_id_proveedor]').val();
    var obs = $('[name=observaciones]').val();

    var data =  'responsable='+resp+
                '&fecha_despacho='+fdes+
                '&id_sede='+sede+
                '&mov_entrega='+move+
                '&id_proveedor='+prov+
                '&observaciones='+obs+
                '&ordenes_despacho='+JSON.stringify(od_seleccionadas);

    $("#btnGrupoDespacho").attr('disabled','true');
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_grupo_despacho',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('El Despacho se generó correctamente.');
                $('#modal-grupo_despacho_create').modal('hide');
                var id = encode5t(response);
                window.open('imprimir_despacho/'+id);
                $('#ordenesDespacho').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

