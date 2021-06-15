//PRORRATEO
function open_doc_prorrateo(){
    $('#modal-doc_prorrateo').modal({
        show: true
    });
    limpiarCampos();
}

function limpiarCampos(){
    $('[name=id_tp_prorrateo]').val('');
    $('[name=pro_serie]').val('');
    $('[name=pro_numero]').val('');
    $('[name=doc_fecha_emision]').val(fecha_actual());
    $('[name=tipo_cambio]').val(0);
    $('[name=id_moneda]').val(0);
    $('[name=sub_total]').val(0);
    $('[name=importe]').val(0);
    $('[name=importe_aplicado]').val(0);
    $('[name=doc_razon_social]').val('');
    $('[name=doc_id_proveedor]').val('');
    $('[name=id_tp_documento]').val('').trigger('change.select2');
    $('[name=id_contrib]').val('');
}

let documentos = [];
let guias_detalle = [];

$("#form-doc_prorrateo").on("submit", function(){
    var data = JSON.stringify($(this).serializeArray());
    console.log(data);

    let nuevo = {
        'id_tp_prorrateo':$('[name=id_tp_prorrateo]').val(),
        'tp_prorrateo':$('select[name="id_tp_prorrateo"] option:selected').text(),
        'id_proveedor':$('[name=doc_id_proveedor]').val(),
        'razon_social':$('[name=doc_razon_social]').val(),
        'fecha_emision':$('[name=doc_fecha_emision]').val(),
        'id_tp_documento':$('[name=id_tp_documento]').val(),
        'serie':$('[name=pro_serie]').val(),
        'numero':$('[name=pro_numero]').val(),
        'id_moneda':$('[name=id_moneda]').val(),
        'total':$('[name=sub_total]').val(),
        'tipo_cambio':$('[name=tipo_cambio]').val(),
        'importe':$('[name=importe]').val(),
        'importe_aplicado':$('[name=importe_aplicado]').val(),
    }
    
    documentos.push(nuevo);
    $('#modal-doc_prorrateo').modal('hide');
    mostrar_documentos();

    return false;
});

function mostrar_documentos(){
    let tr = '';
    let i = 0;
    let total_aplicado = 0;

    documentos.forEach(element => {
        i++;
        total_aplicado += parseFloat(element.importe_aplicado);
        tr += `<tr>
            <td>${i}</td>
            <td>${element.tp_prorrateo}</td>
            <td>${element.serie+'-'+element.numero}</td>
            <td>${element.fecha_emision}</td>
            <td>${element.id_moneda==1 ? 'S/' : '$'}</td>
            <td>${element.total}</td>
            <td>${element.tipo_cambio}</td>
            <td>${element.importe}</td>
            <td>${element.importe_aplicado}</td>
            <td style="display:flex;">
                <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                title="Editar" onClick="editar_adicional('.$d->id_prorrateo.');"></i>
                <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                title="Anular" onClick="anular_adicional('.$d->id_prorrateo.','.$d->id_doc_com.');"></i>
            </td>
        </tr>`;
    });
    
    $('[name=total_comp]').val(total_aplicado);
    $('#listaProrrateos tbody').html(tr);
}

function listar_guia_detalle(id_guia){
    console.log('id_guia'+id_guia);
    $('#listaDetalleProrrateo tbody').html('');
    
    $.ajax({
        type: 'GET',
        url: 'listar_guia_detalle/'+id_guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            response.forEach(element => {
                guias_detalle.push(element);
            });
            mostrar_guias_detalle();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_guias_detalle(){
    
    var html = '';
    let importe_doc = $('[name=total_comp]').val();
    // let tipo_cambio = $('[name=tipo_cambio]').val();
    let suma_total = 0;

    guias_detalle.forEach(element => {
        suma_total+= parseFloat(element.valorizacion);
    });
    let valor = importe_doc / (suma_total>0 ? suma_total : 1);
    let adicional = 0;
    let total = 0;

    let total_compra = 0;
    let total_adicional = 0;
    let total_prorrateado = 0;

    guias_detalle.forEach(element => {
        adicional = element.valorizacion * valor;
        total = parseFloat(element.valorizacion) + adicional;
        total_compra += parseFloat(element.valorizacion);
        total_adicional += parseFloat(adicional);
        total_prorrateado += parseFloat(total);
        
        html += `
        <tr id="${element.id_guia_com_det}">
            <td><input type="checkbox" checked/></td>
            <td>${element.serie+'-'+element.numero}</td>
            <td>${element.codigo}</td>
            <td>${element.part_number!==null ? element.part_number : ''}</td>
            <td>${element.descripcion}</td>
            <td>${element.cantidad}</td>
            <td>${element.abreviatura}</td>
            <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="subtotal" 
            onChange="calcula_importe('${element.id_guia_com_det}');" value="${formatDecimalDigitos(element.valorizacion,3)}" /></td>
            <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="adicional" 
            onChange="calcula_importe('${element.id_guia_com_det}');" value="${formatDecimalDigitos(adicional,3)}" /></td>
            <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="total" 
            value="${formatDecimalDigitos(total,3)}" /></td>
            <td style="display:flex;">
            </td>
        </tr>`;
    });
    
    $('#listaDetalleProrrateo tbody').html(html);

    $('[name=total_suma]').val(formatDecimalDigitos(total_compra,3));
    $('[name=total_adicional]').val(formatDecimalDigitos(total_adicional,3));
    $('[name=total_costo]').val(formatDecimalDigitos(total_prorrateado,3));

}

function listar_docs_prorrateo(id_guia){
    $.ajax({
        type: 'GET',
        url: 'listar_docs_prorrateo/'+id_guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#listaProrrateos tbody').html(response['html']);
            $('[name=total_comp]').val(response['total_comp']);
            $('[name=total_items]').val(response['total_items']);

            if (response['moneda'] !== null){
                console.log(response['moneda']);
                console.log(response['moneda'].descripcion+' '+response['moneda'].simbolo);
                $('#moneda').text(response['moneda'].descripcion+' '+response['moneda'].simbolo);
            }
            console.log('total_comp:'+response['total_comp']);
            listar_detalle_prorrateo(id_guia, response['total_comp']);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_detalle_prorrateo(guia, total_comp){
    $('#listaDetalleProrrateo tbody').html('');
    console.log('id_guia'+guia);
    console.log('total_comp'+total_comp);
    console.log();
    var baseUrl = 'listar_guia_detalle_prorrateo/'+guia+'/'+total_comp;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response['sumas']);
            $('#listaDetalleProrrateo tbody').html(response['html']);
            $('[name=total_suma]').val(response['sumas'][0].suma_total);
            $('[name=total_adicional]').val(response['sumas'][0].suma_adicional);
            $('[name=total_costo]').val(response['sumas'][0].suma_costo);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function editar_adicional(id){
    $("#"+id+" td").find("input[name=subtotal]").attr('disabled',false);
    $("#"+id+" td").find("input[name=tipocambio]").attr('disabled',false);
    // $("#"+id+" td").find("input[name=importe]").attr('disabled',false);
    $("#"+id+" td").find("i.blue").removeClass('visible');
    $("#"+id+" td").find("i.blue").addClass('oculto');
    $("#"+id+" td").find("i.green").removeClass('oculto');
    $("#"+id+" td").find("i.green").addClass('visible');
}

function calcula_importe(id){
    var subtotal = $('#det-'+id+' input[name=subtotal]').val();
    var tpcambio = $('#det-'+id+' input[name=tipocambio]').val();
    if (subtotal !== '' && tpcambio !== ''){
        $('#det-'+id+' input[name=importedet]').val(formatDecimal(subtotal * tpcambio));
    } else {
        $('#det-'+id+' input[name=importedet]').val(0);
    }
}

function calculaImporte(){
    var moneda = $('[name=id_moneda]').val();
    var sub_total = $('[name=sub_total]').val();
    if (moneda == 2){
        var tcambio = $('[name=tipo_cambio]').val();
        if (tcambio == null || tcambio == '' || tcambio == '0'){
            getTipoCambio();
            tcambio = $('[name=tipo_cambio]').val();
        }
        var imp = formatDecimal(sub_total * tcambio);
        $('[name=importe]').val(imp);
        $('[name=importe_aplicado]').val(imp);
    } else {
        $('[name=importe]').val(sub_total);
        $('[name=importe_aplicado]').val(sub_total);
    }
}

function anular_adicional(id,id_doc){
    var anula = confirm("¿Esta seguro que desea anular éste adicional?");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'eliminar_doc_prorrateo/'+id+'/'+id_doc,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Adicional anulado con éxito');
                    // $("#det-"+id).remove();
                    var id = $('[name=id_guia]').val();
                    console.log('id:'+id);
                    listar_docs_prorrateo(id);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function update_adicional(id,id_doc){
    var subtotal = $("#det-"+id+" td").find("input[name=subtotal]").val();
    var tipocambio = $("#det-"+id+" td").find("input[name=tipocambio]").val();
    var importe = $("#det-"+id+" td").find("input[name=importedet]").val();
    var data =  'id_prorrateo='+id+
                '&id_doc='+id_doc+
                '&sub_total='+subtotal+
                '&tipo_cambio='+tipocambio+
                '&importe='+importe;
    console.log(data);

    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'update_doc_prorrateo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Adicional actualizado con éxito');
                $("#det-"+id+" td").find("input[name=subtotal]").attr('disabled',true);
                $("#det-"+id+" td").find("input[name=tipocambio]").attr('disabled',true);
                // $("#det-"+id+" td").find("input[name=importe]").attr('disabled',false);
                $("#det-"+id+" td").find("i.blue").removeClass('oculto');
                $("#det-"+id+" td").find("i.blue").addClass('visible');
                $("#det-"+id+" td").find("i.green").removeClass('visible');
                $("#det-"+id+" td").find("i.green").addClass('oculto');            
                                
                var id = $('[name=id_guia]').val();
                console.log('despues id_guia:'+id);
                listar_docs_prorrateo(id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function getTipoCambio(){
    var fecha = $('[name=doc_fecha_emision]').val();
    if (fecha !== null && fecha !== ''){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'tipo_cambio_compra/'+fecha,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=tipo_cambio]').val(response);                
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function copiar_unitario(){
    $('[name=prorrateo]').prop('checked',true);
    var p = $('[name=prorrateo]').val();
    console.log(p);
    var id_guia = $('[name=id_guia]').val();
    var id = [];
    var uni = [];
    var r = 0;
    
    $('#listaDetalleProrrateo tbody tr').each(function(e){
        var pro = $(this)[0].id.split("-");
        var tds = parseFloat($(this).find("td input[name=unit]").val());
        console.log('unitario:'+tds);
        console.log('id_guia_com_det:'+pro[1]);
        id[r] = pro[1];
        uni[r] = tds;
        r++;
    });
    var data =  'id_guia='+id_guia+
                '&id_guia_com_det='+id+
                '&unitario='+uni;
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'update_guia_detalle_adic',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                listar_detalle(id_guia);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function agregar_tipo(){
    var nombre = prompt('Ingrese el Nombre del nuevo tipo','');
    console.log(nombre);
    if (nombre !== null){
        var rspta = confirm("¿Está seguro que desea agregar éste tipo: "+nombre+"?");
        if (rspta){
            $.ajax({
                type: 'GET',
                url: 'guardar_tipo_prorrateo/'+nombre,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    $('[name=id_tp_prorrateo]').html('');
                    var html = '<option value="0" disabled>Elija una opción</option>'+response;
                    $('[name=id_tp_prorrateo]').html(html);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    }
}

//PRORRATEO ITEMS
function prorrateo_items(id_prorrateo,importe){
    $('#modal-guia_com_det').modal({
        show: true
    });
    listar_guia_detalle(id_prorrateo);
    $('[name=id_prorrateo]').val(id_prorrateo);
    $('[name=importe_prorrateo]').val(importe);
}
// function listar_guia_detalle(id_prorrateo){
//     var id = $('[name=id_guia]').val();
//     console.log('id'+id);
//     $.ajax({
//         type: 'GET',
//         url: 'mostrar_guia_detalle/'+id+'/'+id_prorrateo,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             $('#listaGuiaDetalle tbody').html(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
function guardar_prorrateo_detalle(){
    console.log('guardar_prorrateo_detalle');
    var id_guia_com_det = [];
    var total_det = [];
    var id_prorrateo = $('[name=id_prorrateo]').val();
    var imp_comp = $('[name=importe_prorrateo]').val();
    var r = 0;
    var suma_total = 0;

    $("input[type=checkbox]:checked").each(function(){
        id_guia_com_det[r] = $(this)[0].parentElement.parentElement.id;
        var columnas = $(this)[0].parentElement.parentElement.querySelectorAll("td");
        var imp = parseFloat(columnas[6].innerHTML);
        total_det[r] = imp;
        console.log(imp);
        suma_total += imp;
        ++r;
    });
    var data =  'id_guia_com_det='+id_guia_com_det+
                '&total_det='+total_det+
                '&id_prorrateo='+id_prorrateo+
                '&importe_comp='+imp_comp+
                '&suma_total='+suma_total;
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_prorrateo_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#modal-guia_com_det').modal('hide');
            $('[name=total_items]').val(imp_comp);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero(numero){
    if (numero == 'pro_numero'){
        var num = $('[name=pro_numero]').val();
        $('[name=pro_numero]').val(leftZero(7,num));
    }
}