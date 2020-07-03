var grupoCotizacion=[];
var items_valorizacion = [];
let indice_actual=0;

$(function(){

    // var idGrupo = localStorage.getItem('idGrupo');
    // var TipoCodigo = localStorage.getItem('TipoCodigo');
    // if (idGrupo != null &&  TipoCodigo != null){    
    //     grupoCotizaciones(idGrupo,TipoCodigo);
    //     localStorage.removeItem('idGrupo');
    //     localStorage.removeItem('TipoCodigo');
    // }

    // listarCotizacionesConEnvio();
    listarCotizacionesConEnvioFiltroSinValorizar();
    listarCotizacionesConEnvioValorizadas();
    defaultStateTab();
});

function listarCotizacionesConEnvioFiltroSinValorizar(){
    let id_empresa = document.querySelector('form[id="form-gestionar_valorizacion"] select[id="id_empresa_select_coti"]').value;
    listarCotizacionesConEnvio(id_empresa);
}

function getDataGrupoCotizaciones(codigo_cuadro,codigo_cotizacion,id_grupo,estado_envio,id_empresa,valorizacion_completa_incompleta,id_cotizacion_alone){

    return new Promise(function(resolve, reject) {
        const baseUrl = '/logistica/valorizacion/grupo_cotizaciones/0/0/'+id_grupo+'/'+estado_envio+'/'+id_empresa+'/'+valorizacion_completa_incompleta+'/'+id_cotizacion_alone;
    // let url = ''
    // switch (tipoCodigo) {
    //     case '1': // codigo cuadro comparartivo
    //         url = baseUrl.concat(`/0/${  codigo}/0`)
    //         break
    //     case '2': // codigo cotización
    //         url = baseUrl.concat(`/${  codigo  }/0/0`)
    //         break
    //     case '3': // id grupo cotizacion ( id cuadro comparativo)
    //         url = baseUrl.concat(`/0/0/${  codigo  }`)
    //         break
    //     default:
    //         break
    // }
    $.ajax({
        type: 'GET',
        url:baseUrl,
        dataType: 'JSON',
        success(response) {
             grupoCotizacion = response;
            resolve(response) // Resolve promise and go to then() 
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}
function getDataGrupoCotizacionesRelacionadas(id_cotizacion){

    return new Promise(function(resolve, reject) {
        const baseUrl = '/logistica/valorizacion/grupo_cotizaciones_relacionadas/'+id_cotizacion;
    $.ajax({
        type: 'GET',
        url:baseUrl,
        dataType: 'JSON',
        success(response) {
            if(response.status =='success'){
                grupoCotizacion = response.data;
                resolve(response) // Resolve promise and go to then() 
            }else{
                alert(response.message);
            }
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}

// function llenarTablaCotizacionesConEnvio(data){
//     limpiarTabla('listaGrupoCotizacionesEnviadas')
//     const table = document.querySelector('table[id="listaGrupoCotizacionesEnviadas"] tbody')    
//     data.map((currentValue, index, array) => {
//         const row = table.insertRow(index)
//         const tdIdCotizacion = row.insertCell(0)
//         tdIdCotizacion.innerHTML = currentValue.id_cotizacion
//         tdIdCotizacion.setAttribute('class', 'hidden')

//         row.insertCell(1).innerHTML = index + 1
//         row.insertCell(2).innerHTML = currentValue.codigo_cotizacion
//         row.insertCell(3).innerHTML = currentValue.codigo_grupo
//         row.insertCell(4).innerHTML = currentValue.empresa.razon_social
//         row.insertCell(5).innerHTML = currentValue.requerimientos
//         .map(function(k) {
//             return k.codigo_requerimiento
//         })
//         .join(', ')
//         const tdProveedor = row.insertCell(6)
//         tdProveedor.innerHTML = currentValue.proveedor.razon_social
//         tdProveedor.setAttribute('title',`${currentValue.proveedor.nombre_doc_identidad }: ${ currentValue.proveedor.nro_documento}`)
//         row.insertCell(7).innerHTML = currentValue.fecha_registro
//         row.insertCell(8).innerHTML = currentValue.estado_cotizacion_descripcion
//         row.insertCell(9).innerHTML = currentValue.estado_envio_descripcion
//         const tdBtnAction = row.insertCell(10)
//         tdBtnAction.setAttribute('width', 'auto')
//         tdBtnAction.innerHTML =
//             '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
//             '<button class="btn btn-warning btn-sm" name="btnValorizarCotizacion" title="Valorizar Cotización" onClick="valorizarCotizacion(event,'+index+ 
//             ');" disabled ><i class="fas fa-file-invoice-dollar"></i></button>' +
//             '</div>';
//     })
// }
function handleChangeFilterCotiByEmpresa(event){
    getDataGrupoCotizaciones(0,0,0,17,event.target.value,'VALORIZACION_INCOMPLETA',0).then(function(data) {
        // console.log(data);
        
        llenarTablaCotizacionesConEnvio(data);
        // eventOnClickTableListaCotizaciones();
    }).catch(function(err) {
        console.log(err)
    })    
}

function llenarTablaCotizacionesConEnvioValorizadas(data){
    var vardataTables = funcDatatables();
    $('#listaCotizacionesEnviadasValorizadas').dataTable({
        "order": [[ 3, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_cotizacion'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'data': 'codigo_cotizacion'},
            {'data': 'codigo_grupo'},
            {'data': 'empresa.razon_social'},
             {'render':
                function (data, type, row){
                    var req = '';
                    for (i=0;i<row['requerimientos'].length;i++){
                        if (req !== ''){
                            req += ', '+row['requerimientos'][i].codigo_requerimiento;
                        } else {
                            req += row['requerimientos'][0].codigo_requerimiento;
                        }
                    }
                    return (req);
                }
            },
            {'data': 'proveedor.razon_social'},
            {'data': 'fecha_registro'},
            {'render':
            function (data, type, row,meta){
                var html = '<center>'+row.estado_cotizacion_descripcion+', '+row.estado_envio_descripcion+'</center>';    
                return (html);
            }
            },            
            {'render':
            function (data, type, row,meta){
                var html = '<center>'+row.cantidad_items+'/'+row.cantidad_items_valorizado+'</center>';    
                return (html);
            }
            },
            {'render':
            function (data, type, row,meta){
                var html ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group" style="width:120px;" >' +
                '<button class="btn btn-success btn-sm" name="btnDescargarSolicitudCotizacion" title=" Descargar Solicitud de Cotización" onClick="DescargarSolicitudCotizacion(event,'+ row.id_cotizacion +');"  ><i class="fas fa-file-download"></i></button>' +
                '<button class="btn btn-primary btn-sm" name="btnIrACuadroComparativo" title="Ir a Cuadro Comparativo" onClick="goToCuadroComparativo(event,'+ row.id_grupo_cotizacion +');"  ><i class="far fa-closed-captioning"></i></button>' +
                '</div>';
 
                return (html);
            }
            }
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaCotizacionesEnviadasValorizadas_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function DescargarSolicitudCotizacion(event,id_cotizacion) {
    event.preventDefault();
    if (id_cotizacion == 0) {
        alert('NO existe un ID de cotización')
        
    } else {

        $.ajax({
            type: 'GET',
            url: '/descargar_solicitud_cotizacion_excel/'+id_cotizacion,
            dataType: 'JSON',
            success: function(response){
                data = response;
                // console.log(response.status);
                // console.log(response.ruta);
                // console.log(response.message);
                if(response.status >0){
                    window.open(response.ruta);

                }else{
                    alert(response.message);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

    }
}

function goToCuadroComparativo(event,id_grupo_cotizacion){
    event.preventDefault();
    // console.log(id_grupo_cotizacion);
    localStorage.setItem('idGrupoCotizacion',id_grupo_cotizacion);
    window.location.href ='./cuadro-comparativo';

}

function llenarTablaCotizacionesConEnvio(data){
    var vardataTables = funcDatatables();
    $('#listaGrupoCotizacionesEnviadas').dataTable({
        "order": [[ 3, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_cotizacion'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'data': 'codigo_cotizacion'},
            {'data': 'codigo_grupo'},
            {'data': 'empresa.razon_social'},
             {'render':
                function (data, type, row){
                    var req = '';
                    for (i=0;i<row['requerimientos'].length;i++){
                        if (req !== ''){
                            req += ', '+row['requerimientos'][i].codigo_requerimiento;
                        } else {
                            req += row['requerimientos'][0].codigo_requerimiento;
                        }
                    }
                    return (req);
                }
            },
            {'data': 'proveedor.razon_social'},
            {'data': 'fecha_registro'},
            {'render':
            function (data, type, row,meta){
                var html = '<center>'+row.estado_cotizacion_descripcion+', '+row.estado_envio_descripcion+'</center>';    
                return (html);
            }
            },            
            {'render':
            function (data, type, row,meta){
                var html = '<center>'+row.cantidad_items+'/'+row.cantidad_items_valorizado+'</center>';    
                return (html);
            }
            },
            {'render':
            function (data, type, row,meta){
                var html ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                '<button class="btn btn-primary btn-sm" name="btnCotizacionRelacionada" title=" Ir a Paso 2" onClick="gotToSecondStep(event,'+ row.id_cotizacion +');"  ><i class="far fa-arrow-alt-circle-right"></i></button>' +
                '</div>';
 
                return (html);
            }
            }
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaGrupoCotizacionesEnviadas_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function llenarTablaCotizacionesRelacionadas(data){
    var vardataTables = funcDatatables();
    $('#listaGrupoCotizacionesRelacionadas').dataTable({
        "order": [[ 3, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_cotizacion'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'data': 'codigo_cotizacion'},
            {'data': 'codigo_grupo'},
            {'data': 'empresa.razon_social'},
             {'render':
                function (data, type, row){
                    var req = '';
                    for (i=0;i<row['requerimientos'].length;i++){
                        if (req !== ''){
                            req += ', '+row['requerimientos'][i].codigo_requerimiento;
                        } else {
                            req += row['requerimientos'][0].codigo_requerimiento;
                        }
                    }
                    return (req);
                }
            },
            {'data': 'proveedor.razon_social'},
            {'data': 'fecha_registro'},
            {'render':
            function (data, type, row,meta){
                var html = '<center>'+row.estado_cotizacion_descripcion+', '+row.estado_envio_descripcion+'</center>';    
                return (html);
            }
            },   

            {'render':
            function (data, type, row,meta){
                var html ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
                '<button class="btn btn-warning btn-sm" name="btnValorizarCotizacion" title="Valorizar Cotización" onClick="valorizarCotizacion(event,'+ meta.row +');"  ><i class="fas fa-file-invoice-dollar"></i></button>' +
                '</div>';
 
                return (html);
            }
        }
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaGrupoCotizacionesEnviadas_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function listarCotizacionesConEnvio(id_empresa = 0){
    getDataGrupoCotizaciones(0,0,0,17,id_empresa,'VALORIZACION_INCOMPLETA',0).then(function(data) { //17 ESTADO ENVIADO
        // Run this when your request was successful
        // console.log(data)
        if(data.length >0){
            llenarTablaCotizacionesConEnvio(data);
        }

        // eventOnClickTableListaCotizaciones();
    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })
    
}
function listarCotizacionesConEnvioValorizadas(){
    getDataGrupoCotizaciones(0,0,0,17,0,'GRUPO_VALORIZADO_COMPLETO',0).then(function(data) {
        // Run this when your request was successful
        // console.log(data)
        if(data.length >0){
            llenarTablaCotizacionesConEnvioValorizadas(data);
        }

    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })
    
}

// function eventOnClickTableListaCotizaciones(){
//     $('#listaGrupoCotizacionesEnviadas tbody').on('click', 'tr', function(){
//         if ($(this).hasClass('eventClick')){
//             $(this).removeClass('eventClick');
//         }
//         var idTr = $(this)[0].firstChild.innerHTML;
//         // console.log(idTr);
//         // $('.modal-footer #id_grupo').text(idTr); 
//     });
// }

function valorizarCotizacion(event,index){
    event.preventDefault();
    // console.log(grupoCotizacion);
    
    $('#modal-valorizarCotizacion').modal({
        show: true,
        backdrop: 'static',
    })
    let item = grupoCotizacion[index];
    let codigo_cotizacion = grupoCotizacion[index].codigo_cotizacion;
    document.querySelector('div[id="modal-valorizarCotizacion"] h3[class="modal-title"]').textContent= 'Valorizar Cotización '+codigo_cotizacion;

    let id_cotizacion = item.id_cotizacion;
    listaItemValorizar(id_cotizacion);
    // console.log(item);
    // console.log(id_cotizacion);
}

function listaItemValorizar(id_cotizacion){
    const baseUrl = '/logistica/cuadro_comparativos/valorizacion/lista_item/'+id_cotizacion;

    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success(response) {
            // console.log(response);
            llenarTablaItemsValorizacion(response.item_cotizacion);
            
            $('[name=id_condicion]').val(response.cotizacion.id_condicion);
            $('[name=plazo_dias]').val(response.cotizacion.plazo_dias);
            $('#id_cotizacion').text(id_cotizacion);
        },
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

function llenarTablaItemsValorizacion(items){
    // console.log(items);
    items_valorizacion = items;
    limpiarTabla('listarItemCotizacion')

    htmls = '<tr></tr>';
    $('#listarItemCotizacion tbody').html(htmls)
    const table = document.getElementById('listarItemCotizacion').getElementsByTagName( 'tbody' )[0];

    items.map((currentValue, index, array) => {
        const row = table.insertRow(index + 1)
        const tdIdValorizacionCotizacion = row.insertCell(0)
        tdIdValorizacionCotizacion.innerHTML = currentValue.id_valorizacion_cotizacion
        tdIdValorizacionCotizacion.setAttribute('class', 'hidden')
        row.insertCell(1).innerHTML = index + 1
        row.insertCell(2).innerHTML = currentValue.codigo
        row.insertCell(3).innerHTML = currentValue.descripcion
        row.insertCell(4).innerHTML = currentValue.unidad_medida
        row.insertCell(5).innerHTML = currentValue.cantidad
        row.insertCell(6).innerHTML = formatDecimal(currentValue.precio_referencial)
        row.insertCell(7).innerHTML = currentValue.abrev_unidad_medida_cotizado
        row.insertCell(8).innerHTML = currentValue.cantidad_cotizada?currentValue.cantidad_cotizada:'0'
        row.insertCell(9).innerHTML = currentValue.precio_cotizado?formatDecimal(currentValue.precio_cotizado):"-"
        row.insertCell(10).innerHTML = (currentValue.cantidad_cotizada * currentValue.precio_cotizado)
        row.insertCell(11).innerHTML = currentValue.flete?formatDecimal(currentValue.flete):'-'
        row.insertCell(12).innerHTML = currentValue.porcentaje_descuento?formatDecimal(currentValue.porcentaje_descuento):'-'
        row.insertCell(13).innerHTML = currentValue.monto_descuento?formatDecimal(currentValue.monto_descuento):'-'
        row.insertCell(14).innerHTML = currentValue.subtotal?formatDecimal(currentValue.subtotal):'0.00'
        const tdBtnAction = row.insertCell(15)
        tdBtnAction.setAttribute('width', 'auto')
        tdBtnAction.innerHTML =
            '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
            '<button class="btn btn-primary btn-sm" name="btnValorizarCotizacion" title="Valorizar Cotización" onClick="valorizacionEspecificacion(event,'+index+ 
            ');"><i class="fas fa-edit"></i></button>' +
            '</div>';
    })
}

function handlechangeCondicion(event){
    let condicion= document.getElementsByName('id_condicion')[0];
    let text_condicion = condicion.options[condicion.selectedIndex].text;    
    if(text_condicion.includes('CONTADO')){
        document.querySelector('form[id="form-valorizar_cotizacion"] input[name="plazo_dias"]').value = null;
        document.querySelector('form[id="form-valorizar_cotizacion"] input[name="plazo_dias"]').setAttribute('disabled',true);
    }else if(text_condicion.includes('CREDITO')){
        document.querySelector('form[id="form-valorizar_cotizacion"] input[name="plazo_dias"]').removeAttribute('disabled');
    }
}

function guardarCondicion(){
    var ask = confirm('¿Desea guardar este registro?');
    if (ask == true){
        let data ={   
            'id_cotizacion' :$('#id_cotizacion').text(),
            'id_condicion' :$('[name=id_condicion]').val(),
            'plazo_dias':$('[name=plazo_dias]').val()
        };
        // console.log(data);
        $.ajax({
            type: 'PUT',
            url: '/logistica/condicion_valorizacion',
            datatype: "JSON",
            data:data,
            success: function(response){
                if(response == 'ACTUALIZADO'){
                    alert('Condición Actualizado!');
                }else if(response == 'NO_ACTUALIZADO'){
                    alert('NO se puedo actualizar');
                }else{
                    alert('ERROR al intentar actualizar');

                }
            }
        });
 
        return false;
    }else{
        return false;
    }
}

function valorizacionEspecificacion(event,index) {
    event.preventDefault()
    // item_val = items_valorizacion[index];
    // console.log(items_valorizacion[index]);
    fillInputsModalValorizacionEspecificacion(items_valorizacion[index]);
    
    indice_actual = index;

    $('#modal-valorizacion-especificacion').modal({
        show: true,
        backdrop: 'static',
    })

    defaultValues();
}

function defaultValues(){
    var temp = "NO";
    var mySelect = document.getElementById('igv');
    for(var i, j = 0; i = mySelect.options[j]; j++) {
        if(i.value == temp) {
            mySelect.selectedIndex = j;
            break;
        }
    }
    document.getElementById("check_option_monto").checked = true;
    document.getElementById("porcentaje_descuento_valorizacion").setAttribute("readonly",true);
    
}

function fillInputsModalValorizacionEspecificacion(item){
    // console.log(item);
        document.querySelector('div[id="modal-valorizacion-especificacion"] div[class="modal-footer"] input[id="id_cotizacion"]').value = item.id_cotizacion;
        document.querySelector('div[id="modal-valorizacion-especificacion"] div[class="modal-footer"] input[id="id_valorizacion_cotizacion"]').value = item.id_valorizacion_cotizacion;
    
    $('#id_valorizacion_cotizacion').val(item.id_valorizacion_cotizacion);
    $('#id_detalle_requerimiento').val(item.id_detalle_requerimiento);
    $('#unidad_medida_valorizacion').val(item.id_unidad_medida_cotizado?item.id_unidad_medida_cotizado:item.id_unidad_medida);
    $('#cantidad_valorizacion').val(item.cantidad_cotizada?item.cantidad_cotizada:item.cantidad);
    $('#precio_valorizacion').val(item.precio_cotizado);
    $('#flete_valorizacion').val(item.flete);
    $('#porcentaje_descuento_valorizacion').val(item.porcentaje_descuento);
    $('#monto_descuento_valorizacion').val(item.monto_descuento);
    $('#subtotal_valorizacion').val(item.subtotal);
    $('#monto_neto').val(item.precio_sin_igv);

    $('#igv').val(item.incluye_igv);
    $('#monto_igv').val(item.igv);
    $('#garantia').val(item.garantia);
    $('#plazo_entrega').val(item.plazo_entrega);
    $('#lugar_entrega').val(item.lugar_despacho);
    $('#detalle_adicional').val(item.detalle);

    CalValuesModalValorizacion();
    get_data_archivos_adjuntos(item.id_detalle_requerimiento);
    get_data_archivos_adjuntos_proveedor(item.id_valorizacion_cotizacion);
 
}

function onChangeInputValorizacion() {
    CalValuesModalValorizacion();
};

function handleCheckChange(checkbox){    
    if(checkbox.checked == true && checkbox.id == 'check_option_porcentaje'){
        document.getElementById("monto_descuento_valorizacion").setAttribute("readonly", true);
        document.getElementById("porcentaje_descuento_valorizacion").removeAttribute("readonly");
        CalValuesModalValorizacion();

    }else if( checkbox.checked == true && checkbox.id == 'check_option_monto'){
        document.getElementById("porcentaje_descuento_valorizacion").setAttribute("readonly", true);
        document.getElementById("monto_descuento_valorizacion").removeAttribute("readonly");
        CalValuesModalValorizacion();

    }
}

function CalValuesModalValorizacion(){    
    var cantidad = $('#cantidad_valorizacion').val()? $('#cantidad_valorizacion').val():0;
    var precio = $('#precio_valorizacion').val()?$('#precio_valorizacion').val():0;
    var hasIGV = $('#igv').val();
    var flete = $('#flete_valorizacion').val()?$('#flete_valorizacion').val():0;
    var porcentaje_descuento = $('#porcentaje_descuento_valorizacion').val()?$('#porcentaje_descuento_valorizacion').val():0;
    var monto_descuento = $('#monto_descuento_valorizacion').val()?$('#monto_descuento_valorizacion').val():0;

    let check_porcentaje_descuento =document.getElementById("check_option_porcentaje");
    let check_monto_descuento = document.getElementById("check_option_monto");
    
    
    
    if(check_monto_descuento.checked == true){
        //console.log("tomar monto desc");
        porc_descuento= ((monto_descuento * 100) / precio);
        $('#porcentaje_descuento_valorizacion').val(porc_descuento.toFixed(2));  

        
    }
    if(check_porcentaje_descuento.checked == true){
        //console.log("tomar % desc");
        nuevo_monto_desc = parseFloat(porcentaje_descuento * precio)/100;
        monto_descuento = nuevo_monto_desc.toFixed(2);
        //console.log("nuevo monto desc ",monto_descuento);
        $('#monto_descuento_valorizacion').val(monto_descuento);  

    }
    
    var precio_con_igv = 0;
    var precio_sin_igv = 0;
    var igv = 0;
    var monto_neto_sin_igv = 0;
    var monto_neto_con_igv = 0;
    var precio_con_descuento = 0;

 
    if ( cantidad > 0 ){
        if(hasIGV=='NO'){
            // precio_con_descuento = (precio - parseFloat(monto_descuento));            
            igv = (parseFloat(precio) * 0.18);
            $('#monto_igv').val(igv.toFixed(2));  
            precio_sin_igv = parseFloat(precio);
            monto_neto_sin_igv = (precio_sin_igv * cantidad);
            monto_neto_con_igv = (parseFloat(precio_sin_igv + igv) * cantidad);            
            subtotal_s = monto_neto_sin_igv-parseFloat(monto_descuento);
            $('#monto_neto').val(subtotal_s.toFixed(2));  
            subtotal_c = (monto_neto_con_igv - parseFloat(monto_descuento));
            $('#subtotal_valorizacion').val(subtotal_c.toFixed(2)); 

            subtotal_con_flete= subtotal_c + parseFloat(flete);
            $('#subtotal_con_flete').val(subtotal_con_flete.toFixed(2)); 


        }else if(hasIGV =='SI'){
            precio_sin_igv = (precio / 1.18);
            igv = (precio - precio_sin_igv);
            $('#monto_igv').val(igv.toFixed(2));  
            monto_neto_sin_igv = parseFloat(precio_sin_igv * cantidad);
            monto_neto_con_igv = parseFloat(precio * cantidad);
            subtotal_s = monto_neto_sin_igv - parseFloat(monto_descuento);
            $('#monto_neto').val(subtotal_s.toFixed(2)); 
            subtotal_c = monto_neto_con_igv - parseFloat(monto_descuento);
            $('#subtotal_valorizacion').val(subtotal_c.toFixed(2)); 

            subtotal_con_flete= subtotal_c + parseFloat(flete);
            $('#subtotal_con_flete').val(subtotal_con_flete.toFixed(2)); 

        }
    }
}

function get_data_archivos_adjuntos(id){
 
    adjuntos=[];
    baseUrl = '/logistica/mostrar-archivos-adjuntos/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                for (x=0; x<response.length; x++){
                    id_detalle_requerimiento= response[x].id_detalle_requerimiento;
                        adjuntos.push({ 
                            'id_adjunto':response[x].id_adjunto,
                            'id_valorizacion_cotizacion':response[x].id_valorizacion_cotizacion,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
                    llenar_tabla_archivos_adjuntos(adjuntos);
            }else{
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData =  row.insertCell(0);
                tdSinData.setAttribute('colspan','5');
                tdSinData.setAttribute('class','text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}
function llenar_tabla_archivos_adjuntos(adjuntos){
    limpiarTabla('listaArchivos');
    htmls ='<tr></tr>';
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    for(var a=0;a < adjuntos.length;a++){

        var row = table.insertRow(a+1);
        var tdIdArchivo =  row.insertCell(0);
            tdIdArchivo.setAttribute('class','hidden');
            tdIdArchivo.innerHTML = adjuntos[a].id_adjunto?adjuntos[a].id_adjunto:'0';
        var tdIdDetalleReq =  row.insertCell(1);
            tdIdDetalleReq.setAttribute('class','hidden');
            tdIdDetalleReq.innerHTML = adjuntos[a].id_detalle_requerimiento?adjuntos[a].id_detalle_requerimiento:'0';
            row.insertCell(2).innerHTML = a+1;
            row.insertCell(3).innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
            row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    data-original-title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '</div>';

    }
    return null;
}

var adjuntos_proveedor = [];

function get_data_archivos_adjuntos_proveedor(id){
 
    adjuntos_proveedor=[];
    baseUrl = '/logistica/mostrar-archivos-adjuntos-proveedor/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                for (x=0; x<response.length; x++){
                    id_detalle_requerimiento= response[x].id_detalle_requerimiento;
                        adjuntos_proveedor.push({ 
                            'id_adjunto':response[x].id_adjunto,
                            'id_valorizacion_cotizacion':response[x].id_valorizacion_cotizacion,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
                    llenar_tabla_archivos_adjuntos_proveedor(adjuntos_proveedor);
            }else{
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData =  row.insertCell(0);
                tdSinData.setAttribute('colspan','5');
                tdSinData.setAttribute('class','text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}


function guardarAdjuntosProveedor(){
    // console.log(adjuntos_proveedor);
    // console.log(only_adjuntos_proveedor);
    let id_valorizacion_cotizacion = adjuntos_proveedor[0].id_valorizacion_cotizacion;
    let id_detalle_requerimiento = adjuntos_proveedor[0].id_detalle_requerimiento;
    const onlyNewAdjuntos = adjuntos_proveedor.filter(id => id.id_adjunto == 0); // solo enviar los registros nuevos
    var myformData = new FormData();        
    // myformData.append('archivo_adjunto', JSON.stringify(adjuntos_proveedor));
    for(let i=0;i<only_adjuntos_proveedor.length;i++){
        myformData.append('only_adjuntos_proveedor[]', only_adjuntos_proveedor[i]);
        
    }
    myformData.append('detalle_adjuntos', JSON.stringify(onlyNewAdjuntos));
    myformData.append('id_detalle_requerimiento', id_detalle_requerimiento);
    myformData.append('id_valorizacion_cotizacion', id_valorizacion_cotizacion);

    baseUrl = '/logistica/guardar-archivos-adjuntos-proveedor';
    $.ajax({
        type: 'POST',
        processData: false,
        contentType: false,
        cache: false,
        data: myformData,
        enctype: 'multipart/form-data',
        // dataType: 'JSON',
        url: baseUrl,
        success: function(response){
            // console.log(response);     
            if (response > 0){
                alert("Archivo(s) Guardado(s)");
                only_adjuntos_proveedor=[];
                get_data_archivos_adjuntos_proveedor(id_valorizacion_cotizacion);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });  
}

function guardarValorizarCotizacion(){
    let formValorizacionItem = document.querySelector('form[id="form-valorizacion-item"]');
    let formValorizacionEspecificacion = document.querySelector('form[id="form-valorizacion-especificacion"]');
    
    let valorizacion = {
        'id_valorizacion_cotizacion' : document.querySelector('div[id="modal-valorizacion-especificacion"] div[class="modal-footer"] input[id="id_valorizacion_cotizacion"]').value,
        'id_cotizacion': document.querySelector('div[id="modal-valorizacion-especificacion"] div[class="modal-footer"] input[id="id_cotizacion"]').value,
        'unidad_medida_valorizacion': formValorizacionItem.querySelector('select[name="unidad_medida_valorizacion"]').value,
        'cantidad_valorizacion': formValorizacionItem.querySelector('input[name="cantidad_valorizacion"]').value,
        'precio_valorizacion': formValorizacionItem.querySelector('input[name="precio_valorizacion"]').value,
        'monto_igv': formValorizacionItem.querySelector('input[name="monto_igv"]').value,
        'igv': formValorizacionItem.querySelector('select[name="igv"]').value,
        'porcentaje_descuento_valorizacion': formValorizacionItem.querySelector('input[name="porcentaje_descuento_valorizacion"]').value,
        'monto_descuento_valorizacion': formValorizacionItem.querySelector('input[name="monto_descuento_valorizacion"]').value,
        'monto_neto': formValorizacionItem.querySelector('input[name="monto_neto"]').value,
        'subtotal_valorizacion': formValorizacionItem.querySelector('input[name="subtotal_valorizacion"]').value,
        'flete_valorizacion': formValorizacionItem.querySelector('input[name="flete_valorizacion"]').value,
        'subtotal_con_flete': formValorizacionItem.querySelector('input[name="subtotal_con_flete"]').value,
        'id_valorizacion_cotizacion' : document.querySelector('div[id="modal-valorizacion-especificacion"] div[class="modal-footer"] input[id="id_valorizacion_cotizacion"]').value,
        'id_cotizacion': document.querySelector('div[id="modal-valorizacion-especificacion"] div[class="modal-footer"] input[id="id_cotizacion"]').value,
        'garantia': formValorizacionEspecificacion.querySelector('input[name="garantia"]').value,
        'plazo_entrega': formValorizacionEspecificacion.querySelector('input[name="plazo_entrega"]').value,
        'lugar_entrega': formValorizacionEspecificacion.querySelector('input[name="lugar_entrega"]').value,
        'detalle_adicional': formValorizacionEspecificacion.querySelector('textarea[name="detalle_adicional"]').value
    };
    // console.log({valorizacion});
        $.ajax({
            type: 'PUT',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: '/logistica/actualizar_valorizacion',
            data: valorizacion,
            beforeSend: function () {
                $(document.body).append('<span class="loading"><div></div></span>');
            },
            success: function (response) {
                $('.loading').remove();
                if (response.status == 'success') {
                    listaItemValorizar(document.querySelector('div[id="modal-valorizacion-especificacion"] div[class="modal-footer"] input[id="id_cotizacion"]').value);
                    alert(response.message);
                }else{
                    alert(response.message);
                }
            }
        });
        return false;
 
}

function limpiarTabla(idElement,type=0) {
    let table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
    switch (type) {
        case 'ALL':
            table = document.getElementById(idElement).getElementsByTagName( 'thead' )[0];
            for (let i = table.rows.length - 1; i >= 0; i--) {
                table.deleteRow(i)
            }
            table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
            for (let i = table.rows.length - 1; i >= 0; i--) {
                table.deleteRow(i)
            }
        break;
            
        default:
                table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
        break;
    }
    // console.log('limpiando tabla....')
    // const table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
    // console.log(table.rows.length);
    
    for (let i = table.rows.length - 1; i >= 0; i--) {
        table.deleteRow(i)
        
    }
    return null
}


function defaultStateTab() {
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[3].children[0].setAttribute('data-toggle', 'notab');
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[3].className ='disabled';
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[1].className ='active';
    document.getElementById('contenido_tab_proceso_valorizar').childNodes[3].className = 'tab-pane';
    document.getElementById('contenido_tab_proceso_valorizar').childNodes[1].className = 'active';
}

function gotToSecondStep(e,id) {
    e.preventDefault();
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[1].children[0].setAttribute('data-toggle', 'notab');
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[1].className ='disabled';
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[3].className ='active';
    document.getElementById('contenido_tab_proceso_valorizar').childNodes[1].className = 'tab-pane';
    document.getElementById('contenido_tab_proceso_valorizar').childNodes[3].className = 'active';
    // getAllDetalleReqOfList('listaItemsRequerimiento');
    getDataGrupoCotizaciones(0,0,0,17,0,'VALORIZACION_INCOMPLETA',id).then(function(response) {
        // console.log(response);
        llenarTablaCotizacionesRelacionadas(response);

    }).catch(function(err) {
        console.log(err)
    })    
}

function backToFirstStep(e) {
    e.preventDefault();
    listarCotizacionesConEnvioFiltroSinValorizar();
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[3].children[0].setAttribute('data-toggle', 'notab');
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[3].className ='disabled';
    document.getElementById('menu_tab_valorizar_cotizacion').childNodes[1].className ='active';
    document.getElementById('contenido_tab_proceso_valorizar').childNodes[3].className = 'tab-pane';
    document.getElementById('contenido_tab_proceso_valorizar').childNodes[1].className = 'active';
}

function refreshListaCotizacionesValorizadas(){
    listarCotizacionesConEnvioValorizadas();
    vista_extendida();
}

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}