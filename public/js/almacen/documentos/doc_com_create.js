function open_doc_create(id_guia){
    console.log('open_doc_create');
    // $('#modal-doc_guia').modal('hide');
    $('#modal-doc_create').modal({
        show: true
    });
    var id_tp_doc = 2;
    $('[name=id_tp_doc]').val(id_tp_doc).trigger('change.select2');
    $('[name=fecha_emision_doc]').val(fecha_actual());
    $('[name=serie_doc]').val("");
    $('[name=numero_doc]').val("");
    $('[name=moneda]').val(1);
    $('[name=simbolo]').val("S/");
    
    totales.simbolo = "S/";
    obtenerGuía(id_guia);
}

let listaItems = [];
let totales = {};

function obtenerGuía(id){
    $.ajax({
        type: 'GET',
        url: 'obtenerGuia/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);

            if (response['guia'] !== null){
                $('[name=id_proveedor]').val(response['guia'].id_proveedor);
                $('[name=proveedor_razon_social]').val(response['guia'].razon_social);
                $('[name=id_guia]').val(response['guia'].id_guia);
                $('[name=serie_guia]').val(response['guia'].serie);
                $('[name=numero_guia]').val(response['guia'].numero);
            }
            
            if (response['detalle'].length > 0){
                listaItems = response['detalle'];
                totales = {'porcentaje_igv' : parseFloat(response['igv'])};
                mostrarListaItems();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarListaItems(){
    var html = ''
    var i = 1;
    var sub_total = 0;

    listaItems.forEach(element => {
        // total_item = parseFloat(element.cantidad * element.precio);
        element.porcentaje_dscto = (element.porcentaje_dscto !== undefined ? element.porcentaje_dscto : 0);
        element.total_dscto = (element.total_dscto !== undefined ? element.total_dscto : 0);
        element.sub_total = (parseFloat(element.cantidad) * parseFloat(element.precio));
        element.total = (element.sub_total - element.total_dscto);
        sub_total += element.total;

        html+=`<tr>
        <td>${i}</td>
        <td>${element.cod_orden}</td>
        <td>${element.codigo}</td>
        <td>${element.part_number!==null?element.part_number:''}</td>
        <td>${element.descripcion}</td>
        <td>${element.cantidad}</td>
        <td>${element.abreviatura}</td>
        <td>
            <input type="number" class="form-control right unitario" value="${element.precio}" 
            data-id="${element.id_guia_com_det}" min="0" step="0.001"/>
        </td>
        <td class="right">${formatNumber.decimal(element.sub_total,'',-2)}</td>
        <td>
            <input type="number" class="form-control right porcentaje_dscto" value="${element.porcentaje_dscto}" 
            data-id="${element.id_guia_com_det}" min="0" step="0.001"/>
        </td>
        <td>
            <input type="number" class="form-control right total_dscto" value="${element.total_dscto}" 
            data-id="${element.id_guia_com_det}" min="0" step="0.001"/>
        </td>
        <td class="right">${formatNumber.decimal(element.total,'',-2)}</td>
        </tr>`;
        i++;
    });

    $('#detalleItems tbody').html(html);

    totales.sub_total = sub_total;
    totales.igv = (totales.porcentaje_igv * sub_total /100);
    totales.total = sub_total + totales.igv;
    totales.simbolo = $('select[name="moneda"] option:selected').data('sim');

    var html_foot=`<tr>
        <th colSpan="11" class="right">Sub Total <label name="sim">${totales.simbolo}</label></th>
        <th class="right">${formatNumber.decimal(totales.sub_total,'',-2)}</th>
    </tr>
    <tr>
        <th colSpan="11" class="right">IGV ${totales.porcentaje_igv}% <label name="sim">${totales.simbolo}</label></th>
        <th class="right">${formatNumber.decimal(totales.igv,'',-2)}</th>
    </tr>
    <tr>
        <th colSpan="11" class="right"> Total <label name="sim">${totales.simbolo}</label></th>
        <th class="right">${formatNumber.decimal(totales.total,'',-2)}</th>
    </tr>
    `;
    $('#detalleItems tfoot').html(html_foot);
    $('[name=importe]').val(formatNumber.decimal(totales.total,'',-2));
}

$('#detalleItems tbody').on("change", ".unitario", function(){
    
    let id_guia_det = $(this).data('id');
    let unitario = parseFloat($(this).val());
    console.log('unitario: '+unitario);
    // let item = listaItems.find(element => element.id_guia_com_det == id_guia_det);
    listaItems.forEach(element => {
        if (element.id_guia_com_det == id_guia_det){
            element.precio = unitario;
            element.sub_total = (unitario * parseFloat(element.cantidad));
            element.total = (element.sub_total - element.total_dscto);
            console.log(element);
        }
    });
    mostrarListaItems();
});

$('#detalleItems tbody').on("change", ".porcentaje_dscto", function(){
    
    let id_guia_det = $(this).data('id');
    let porcentaje_dscto = parseFloat($(this).val());
    let unitario = 0;
    console.log('porcentaje_dscto: '+porcentaje_dscto);
    // let item = listaItems.find(element => element.id_guia_com_det == id_guia_det);
    listaItems.forEach(element => {
        if (element.id_guia_com_det == id_guia_det){

            element.porcentaje_dscto = porcentaje_dscto;
            element.total_dscto = (porcentaje_dscto * element.sub_total / 100);
            element.total = (element.sub_total - element.total_dscto);
            console.log(element);
        }
    });
    mostrarListaItems();
});

$('#detalleItems tbody').on("change", ".total_dscto", function(){
    
    let id_guia_det = $(this).data('id');
    let total_dscto = parseFloat($(this).val());
    console.log('total_dscto: '+total_dscto);
    // let item = listaItems.find(element => element.id_guia_com_det == id_guia_det);
    listaItems.forEach(element => {
        if (element.id_guia_com_det == id_guia_det){
            element.porcentaje_dscto = 0;
            element.total_dscto = total_dscto;
            element.total = (element.sub_total - total_dscto);
            console.log(element);
        }
    });
    mostrarListaItems();
});

$("#form-doc_create").on("submit", function(e){
    e.preventDefault();
    var id_doc_com = $('[name=id_doc_com]').val();
    var serial = $(this).serialize();
    
    var data =  serial+
                '&sub_total='+totales.sub_total+
                '&porcentaje_igv='+totales.porcentaje_igv+
                '&igv='+totales.igv+
                '&total='+totales.total+
                '&detalle_items='+JSON.stringify(listaItems);
    console.log(data);
    guardar_doc_create(data);
    
});

function guardar_doc_create(data){
    $.ajax({
        type: 'POST',
        url: 'guardar_doc_compra',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log('response'+response);
            if (response > 0){
                alert('Comprobante registrado con éxito');
                // localStorage.setItem("id_doc",response);
                $('#modal-doc_create').modal('hide');
                // location.assign("doc_compra");
                listarOrdenesEntregadas();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function ceros_numero_doc(){
    var num = $('[name=numero_doc]').val();
    $('[name=numero_doc]').val(leftZero(6,num));
}

function changeMoneda(){
    var simbolo = $('select[name="moneda"] option:selected').data('sim');
    if (simbolo.length > 0){
        console.log(simbolo);
        $('[name=simbolo]').val(simbolo);
        $('[name=sim]').text(simbolo);
    } else {
        $('[name=simbolo]').val("");
        $('[name=sim]').text("");
    }
}