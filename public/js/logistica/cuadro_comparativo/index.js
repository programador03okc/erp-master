let buenaPro={};
let itemSelected={};
let buenasPro=[];
let btnDisabledList=[];

$(function(){
    var idGrupoCotizacion = localStorage.getItem('idGrupoCotizacion');
    if (idGrupoCotizacion != null){
        // alert(idGrupoCotizacion)
        document.querySelector('form[id="form-cuadro_comparativo"] input[name="id_grupo_cotizacion"]').value = idGrupoCotizacion;
        drawTableCuadroComparativo(idGrupoCotizacion);
        localStorage.removeItem('idGrupoCotizacion');

    }    
    listaCuadroComparativo();

});

function getDataGrupoCotizaciones(codigo_cuadro,codigo_cotizacion,id_grupo,estado_envio,id_empresa,valorizacion_completa_incompleta,id_cotizacion_alone){

    return new Promise(function(resolve, reject) {
        const baseUrl = '/logistica/valorizacion/grupo_cotizaciones/0/0/'+id_grupo+'/'+estado_envio+'/'+id_empresa+'/'+valorizacion_completa_incompleta+'/'+id_cotizacion_alone;
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

function llenarTablaCotizacionesConEnvioValorizadas(data){
    var vardataTables = funcDatatables();
    $('#listaCuadroComparativos').dataTable({
        "order": [[ 3, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_grupo_cotizacion'},
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
                var html ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group" style="width:120px;" >' +
                '<button class="btn btn-primary btn-sm" name="btnMostrarCuadroComparativo" title="Mostrar Cuadro Comparativo" onClick="mostrarCuadroComparativo(event, '+ row.id_grupo_cotizacion +');"  ><i class="fas fa-table"></i></button>' +
                '<button class="btn btn-success btn-sm" name="btnDescargarCuadroComparativo" title="Descargar Cuadro Comparativo" onClick="descargarCuadroComparativo(event,'+ row.id_grupo_cotizacion +');"  ><i class="fas fa-file-excel"></i></button>' +
                '</div>';
 
                return (html);
            }
            }
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaCuadroComparativos_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function verificarValorizacion(cuadro_comparativo){
    let countValNull=0;
    cuadro_comparativo.cuadro_comparativo.map((detalle_req, indice, array) => {
        detalle_req.proveedores.map((proveedor, index, array) => {
            // console.log(proveedor.valorizacion);
            
            if(Object.keys(proveedor.valorizacion).length > 0){
                let precioCotizado = parseFloat(proveedor.valorizacion.precio_cotizado);
                let cantidadCotizada = parseInt(proveedor.valorizacion.cantidad_cotizada);
                if(!(precioCotizado && cantidadCotizada > 0)){
                    countValNull+=1;
                }
            }
        });
    });    
    if(countValNull > 0){
        return 'incompleted'
    }else{
        return 'completed'
    }
}


function dibujarCuadroCompartivo(cuadro_comparativo){
    // console.log('dibujarCuadroCompartivo');
    // console.log(cuadro_comparativo);
    
    
    var head = document.getElementById("head-cuadro");
    // head.innerHTML = '<h2 class="text-center"><strong>CC-1906-0005</strong></h2>';
    let titulo = '<div><h2 class="text-center"><strong>'+cuadro_comparativo.head.codigo_grupo+'</strong></h2>';
    let tabla_head =    '<table class="table table-condensed table-bordered">'+
                            ' <tr>'+
                            '     <th>Empresa</th>'+
                            '     <td>'+cuadro_comparativo.head.empresa_razon_social+' ['+cuadro_comparativo.head.empresa_nombre_doc_identidad+': '+cuadro_comparativo.head.empresa_nro_documento+']</td>'+
                            ' </tr>'+
                        '<tr>'+
                            '<th>Fecha Inicio</th>'+
                            '<td>'+cuadro_comparativo.head.fecha_inicio+'</td>'+
                        '</tr>'+
                        '</table></div>';
    head.innerHTML = titulo.concat(tabla_head); 
    
  

    let cantidad_proveedores = cuadro_comparativo.proveedores.length;
    
    // $('#cuadro_comparativo tbody').html(htmls)
    const table_head = document.getElementById('cuadro_comparativo').getElementsByTagName( 'thead' )[0];
    const table_body = document.getElementById('cuadro_comparativo').getElementsByTagName( 'tbody' )[0];
    
    const rowH = table_head.insertRow(0)
    const tdContador = rowH.insertCell(0)
    tdContador.innerHTML = "#";
    tdContador.setAttribute('rowspan', '3')
    const tdCodigo = rowH.insertCell(1)
    tdCodigo.innerHTML = "CODIGO";
    tdCodigo.setAttribute('rowspan', '3')
    const tdDescripcion = rowH.insertCell(2)
    tdDescripcion.innerHTML = "DESCRIPCIÓN";
    tdDescripcion.setAttribute('rowspan', '3')
    const tdUnidad = rowH.insertCell(3)
    tdUnidad.innerHTML = "UNIDAD";
    tdUnidad.setAttribute('rowspan', '3')
    const tdCantidad = rowH.insertCell(4)
    tdCantidad.innerHTML = "CANTIDAD";
    tdCantidad.setAttribute('rowspan', '3')
    const tdPrecioRef = rowH.insertCell(5)
    tdPrecioRef.innerHTML = "PRECIO REF.";
    tdPrecioRef.setAttribute('rowspan', '3')
    const tdProv = rowH.insertCell(6)
    tdProv.innerHTML = "PRORVEEDORES";
    tdProv.setAttribute('colspan', cantidad_proveedores*5)
    tdProv.setAttribute('class', 'text-center')
    const tdAccion = rowH.insertCell(7)
    tdAccion.innerHTML = "ACCIÓN";
    tdAccion.setAttribute('rowspan', 3)
    tdAccion.setAttribute('class', 'text-center vertical-align-m')

    const rowH2 = table_head.insertRow(1)
    const rowH3 = table_head.insertRow(2)

    cuadro_comparativo.proveedores.map((proveedor, index, array) => { 
        const tdNameProv = rowH2.insertCell(0)
        tdNameProv.innerHTML = proveedor.razon_social;
        tdNameProv.setAttribute('colspan', '5')
        tdNameProv.setAttribute('class', 'text-center')

        rowH3.insertCell(0).innerHTML = "UNID."
        rowH3.insertCell(1).innerHTML = "CANT."
        rowH3.insertCell(2).innerHTML = "PRECIO."
        rowH3.insertCell(3).innerHTML = "IGV"
        rowH3.insertCell(4).innerHTML = "TOTAL"
     });


    cuadro_comparativo.cuadro_comparativo.map((detalle_req, indice, array) => {
        const row = table_body.insertRow(indice)
        const tdidDetalleReq = row.insertCell(0)
            tdidDetalleReq.innerHTML = detalle_req.id_detalle_requerimiento
            tdidDetalleReq.setAttribute('class', 'hidden')
        row.insertCell(1).innerHTML = indice + 1
        row.insertCell(2).innerHTML = detalle_req.codigo
        row.insertCell(3).innerHTML = detalle_req.descripcion
        row.insertCell(4).innerHTML = detalle_req.unidad_medida
        row.insertCell(5).innerHTML = detalle_req.cantidad
        row.insertCell(6).innerHTML = 'S/.'+detalle_req.precio_referencial
        detalle_req.proveedores.map((proveedor, index, array) => {
            // console.log(Object.keys(proveedor.valorizacion).length);

            if(Object.keys(proveedor.valorizacion).length > 0){
                let precioCotizado = parseFloat(proveedor.valorizacion.precio_cotizado);
                
                let cantidadCotizada = parseInt(proveedor.valorizacion.cantidad_cotizada);
                // let subTotal = parseInt(proveedor.valorizacion.subtotal);
                let totalValorizado = parseInt(precioCotizado * cantidadCotizada).toFixed(2);


                row.insertCell(7).innerHTML = proveedor.valorizacion.unidad_medida_cotizada;
                row.insertCell(8).innerHTML = cantidadCotizada;
                
                if(proveedor.valorizacion.incluye_igv==='NO'){

                    let precio_incluido_igv = parseFloat(precioCotizado * 1.18).toFixed(2);
                    let igv= (precio_incluido_igv - precioCotizado).toFixed(2);
                    let total_con_Igv = parseFloat(precio_incluido_igv * cantidadCotizada).toFixed(2);

                    // row.insertCell(9).innerHTML =  '<button class="badge" onclick="darBuenaPro(event,'+indice+','+proveedor.valorizacion.id_valorizacion_cotizacion+','+detalle_req.id_detalle_requerimiento+','+proveedor.valorizacion.id_proveedor+','+proveedor.valorizacion.id_empresa+');"> '+precio_incluido_igv+'</button>';
                    row.insertCell(9).innerHTML =  precio_incluido_igv;
                    row.insertCell(10).innerHTML = igv?igv: '-';
                    row.insertCell(11).innerHTML = total_con_Igv?total_con_Igv:'-';
                }else{

                    // row.insertCell(9).innerHTML = '<button class="badge" onclick="darBuenaPro(event,'+indice+','+proveedor.valorizacion.id_valorizacion_cotizacion+','+detalle_req.id_detalle_requerimiento+','+proveedor.valorizacion.id_proveedor+','+proveedor.valorizacion.id_empresa+');"> '+precio_cotizado +'</button>';
                    row.insertCell(9).innerHTML =  proveedor.valorizacion.precio_cotizado?proveedor.valorizacion.precio_cotizado:'-';

                    row.insertCell(10).innerHTML = proveedor.valorizacion.incluye_igv?proveedor.valorizacion.incluye_igv: '-'
                    row.insertCell(11).innerHTML = typeof(totalValorizado) === 'number' &&  totalValorizado > 0 ? totalValorizado:'-'

                }
                
            }else{    
                row.insertCell(7).innerHTML = '-'
                row.insertCell(8).innerHTML = '-'
                row.insertCell(9).innerHTML = '-'
                row.insertCell(10).innerHTML = '-'
                row.insertCell(11).innerHTML = '-'


            }

        })
        row.insertCell(-1).innerHTML = '<button class="btn btn-primary btn-sm" type="button" name="btnCompararVariables" onclick="compararVariables(event,'+indice+');">Comparar</button>';


    })

    var scrollingElement = (document.scrollingElement || document.body);
    scrollingElement.scrollTop = scrollingElement.scrollHeight;

    llenarBuenaPro(cuadro_comparativo);

} 





function getPosition(num ,array1){
	// var array1 = [5, 12, 8, 13, 44];
	function isLessNumber(element) {
        return element == num;
    }
    return array1.findIndex(isLessNumber,num);
    // console.log(array1.findIndex(isLessNumber,num));
}

function arrayDupplicateCounter(array1,valor){
    let contArr = 0;
     array1.forEach(function(element) {
        if(valor == element){
        contArr+=1;
        }
    });
    return contArr;
}
function paintTdBestProv(positionbestProv,i){
    let tdBgColor='';
    if(positionbestProv == i){
        tdBgColor ='bg-info';
    }
    return tdBgColor;
}
// comprar variables proveedor 
function compararVariables(event,indice){
    $('#modal-comparar_variables_proveedor').modal({
        show: true,
        backdrop: 'static'
    });
    // console.log(cuadro_comparativo.head);
    // console.log(cuadro_comparativo.proveedores);
    // console.log(cuadro_comparativo.cuadro_comparativo[indice]);
    
    let head = cuadro_comparativo.head;
    let cuadro = cuadro_comparativo.cuadro_comparativo[indice];
    let cantidad_proveedores = cuadro_comparativo.proveedores.length; 

    let buena_pro = cuadro_comparativo.buena_pro; 
    let buena_pro_id_proveedor_list = [];
    buena_pro.forEach(function(element) {
        buena_pro_id_proveedor_list.push(element.id_proveedor);
      });

    //   console.log(cuadro);
      
    let priceList=[];
    let shipmentList=[];
    cuadro.proveedores.map((item,i)=>{      
        priceList.push(parseFloat(item.valorizacion.precio_cotizado));
        shipmentList.push(parseFloat(item.valorizacion.plazo_entrega));
    });
    
    let lessPrice= (Math.min(...priceList));// precio menor
    let lessTimeShipment =(Math.min(...shipmentList)); //tiempo entrega menor
    // console.log(lessPrice);
    // console.log(lessTimeShipment);
    
    let positionPrice = getPosition(lessPrice,priceList); // posicioón de precio menor en array
    let positionTimeShipment = getPosition(lessTimeShipment,shipmentList); // posicion de plazo entrega menor en array

    let sizePriceList= priceList.length; // tamaño de array priceList
    let sizeShipmentList= shipmentList.length; // tamaño de array shipmentList

    let countRepetedPrice = arrayDupplicateCounter(priceList,lessPrice); //cantidad de repeticiones del mejor precio
    let countRepetedShipment = arrayDupplicateCounter(shipmentList,lessTimeShipment) // cantida de repetaciones el  plazo entrega
    
    let positionbestProv= '';
    if(sizePriceList == countRepetedPrice){ //prioriza plazo entrega
        positionbestProv = positionTimeShipment;
    }else{ //prioriza precio
        positionbestProv = positionPrice;
    }

    // console.log(countRepetedPrice);
    // console.log(countRepetedShipment);
    // console.log(positionbestProv);
    

    
    
    limpiarTabla('compararVariablesProveedor');
    $('#id_detalle_requerimiento').val(cuadro.id_detalle_requerimiento);

    const table_buena_pro_body = document.getElementById('compararVariablesProveedor').getElementsByTagName( 'tbody' )[0];

    let row = table_buena_pro_body.insertRow(0)
    const tdid = row.insertCell(0)
    tdid.innerHTML = cuadro.id_detalle_requerimiento
    tdid.setAttribute('class', 'hidden')
    const tdempresa = row.insertCell(1)
    tdempresa.innerHTML = 'Empresa'
    tdempresa.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataEmpresa = row.insertCell(2)
    tdDataEmpresa.innerHTML = head.empresa_razon_social+' '+head.empresa_nombre_doc_identidad + ' '+ head.empresa_nro_documento 
    tdDataEmpresa.setAttribute('class', 'text-center wd-45rem')
    tdDataEmpresa.setAttribute('colspan', cantidad_proveedores+1)

    row = table_buena_pro_body.insertRow(1)
    const tdItem = row.insertCell(0)
    tdItem.innerHTML = 'Item'
    tdItem.setAttribute('class', 'text-left negrita wd-15rem ')
    const tdDataItem = row.insertCell(1)
    tdDataItem.innerHTML = '['+ cuadro.codigo +']'+ ' ' +cuadro.descripcion
    tdDataItem.setAttribute('class', 'text-center negrita wd-45rem')
    tdDataItem.setAttribute('colspan', cantidad_proveedores+1)
    

    row = table_buena_pro_body.insertRow(2)
    const td = row.insertCell(0)
    td.innerHTML = ''
    td.setAttribute('class', 'text-center text-info negrita wd-15rem')
    const tdNumReq = row.insertCell(1)
    tdNumReq.innerHTML = cuadro.codigo_requerimiento
    tdNumReq.setAttribute('class', 'text-center text-info negrita wd-15rem')
    var tdProve ='';
    cuadro.proveedores.map((item,i)=>{    
       let tdBgColor = paintTdBestProv(positionbestProv,i);
        tdProve = row.insertCell(2+i)
        tdProve.innerHTML = item.razon_social + ' <br>'+ item.nombre_doc_identidad + ' ' + item.nro_documento
        tdProve.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });

    row = table_buena_pro_body.insertRow(3)
    const tdUnidad = row.insertCell(0)
    tdUnidad.innerHTML = 'Unidad'
    tdUnidad.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataUnidad01 = row.insertCell(1)
    tdDataUnidad01.innerHTML = cuadro.unidad_medida
    tdDataUnidad01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataUnidad02 ='';
    cuadro.proveedores.map((item,i)=>{    
        let tdBgColor = paintTdBestProv(positionbestProv,i);
        tdDataUnidad02 = row.insertCell(2+i)
        tdDataUnidad02.innerHTML = item.valorizacion.unidad_medida_cotizada
        tdDataUnidad02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });

    row = table_buena_pro_body.insertRow(4)
    const tdCantidad = row.insertCell(0)
    tdCantidad.innerHTML = 'Cantidad'
    tdCantidad.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataCantidad01 = row.insertCell(1)
    tdDataCantidad01.innerHTML = cuadro.cantidad
    tdDataCantidad01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataCantidad02 ='';
    cuadro.proveedores.map((item,i)=>{     
        let tdBgColor = paintTdBestProv(positionbestProv,i);
        tdDataCantidad02 = row.insertCell(2+i)
        tdDataCantidad02.innerHTML = '<input type="text" id="textCantidad'+i+'" value="'+item.valorizacion.cantidad_cotizada+'" class="form-control input-sm">'
        tdDataCantidad02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });


    row = table_buena_pro_body.insertRow(5)
    const tdPrecio = row.insertCell(0)
    tdPrecio.innerHTML = 'Precio'
    tdPrecio.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataPrecio01 = row.insertCell(1)
    tdDataPrecio01.innerHTML = cuadro.precio_referencial
    tdDataPrecio01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataPrecio02 ='';
    cuadro.proveedores.map((item,i)=>{     
        let tdBgColor = paintTdBestProv(positionbestProv,i);
        tdDataPrecio02 = row.insertCell(2+i)
        tdDataPrecio02.innerHTML = '<input type="text" id="textPrecio'+i+'" value="'+item.valorizacion.precio_cotizado+'" class="form-control input-sm">'
        tdDataPrecio02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });

    row = table_buena_pro_body.insertRow(6)
    const tdDescuento = row.insertCell(0)
    tdDescuento.innerHTML = 'Descuento'
    tdDescuento.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDescuento01 = row.insertCell(1)
    tdDescuento01.innerHTML = '-'
    tdDescuento01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataDescuento02 ='';
    cuadro.proveedores.map((item,i)=>{   
        let tdBgColor = paintTdBestProv(positionbestProv,i); 
        tdDataDescuento02 = row.insertCell(2+i)
        tdDataDescuento02.innerHTML = '('+item.valorizacion.porcentaje_descuento+'%) '+item.valorizacion.monto_descuento
        tdDataDescuento02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });

    row = table_buena_pro_body.insertRow(7)
    const tdSubtotal = row.insertCell(0)
    tdSubtotal.innerHTML = 'Sub-Total'
    tdSubtotal.setAttribute('class', 'text-left negrita wd-15rem')
    const tdSubtotal01 = row.insertCell(1)
    tdSubtotal01.innerHTML = parseFloat(cuadro.precio_referencial * cuadro.cantidad).toFixed(2);
    tdSubtotal01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataSubtotal02 ='';
    cuadro.proveedores.map((item,i)=>{    
        let tdBgColor = paintTdBestProv(positionbestProv,i);  
        tdDataSubtotal02 = row.insertCell(2+i)
        tdDataSubtotal02.innerHTML = item.valorizacion.subtotal
        tdDataSubtotal02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });


    row = table_buena_pro_body.insertRow(8)
    const tdFlete = row.insertCell(0)
    tdFlete.innerHTML = 'Flete'
    tdFlete.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataFlete01 = row.insertCell(1)
    tdDataFlete01.innerHTML = '-'
    tdDataFlete01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataFlete02 ='';
    cuadro.proveedores.map((item,i)=>{     
        let tdBgColor = paintTdBestProv(positionbestProv,i);  
        tdDataFlete02 = row.insertCell(2+i)
        tdDataFlete02.innerHTML = item.valorizacion.flete
        tdDataFlete02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });

    row = table_buena_pro_body.insertRow(9)
    const tdSubtotalFlete = row.insertCell(0)
    tdSubtotalFlete.innerHTML = 'Sub-Total + Flete'
    tdSubtotalFlete.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataSubtotalFlete01 = row.insertCell(1)
    tdDataSubtotalFlete01.innerHTML = '-'
    tdDataSubtotalFlete01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataSubtotalFlete02 ='';
    cuadro.proveedores.map((item,i)=>{    
        let tdBgColor = paintTdBestProv(positionbestProv,i);  
        tdDataSubtotalFlete02 = row.insertCell(2+i)
        tdDataSubtotalFlete02.innerHTML = parseFloat(item.valorizacion.subtotal) + parseFloat(item.valorizacion.flete)
        tdDataSubtotalFlete02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });

    row = table_buena_pro_body.insertRow(10)
    const tdFechaEntrega = row.insertCell(0)
    tdFechaEntrega.innerHTML = 'Entrega'
    tdFechaEntrega.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataFechaEntrega01 = row.insertCell(1)
    tdDataFechaEntrega01.innerHTML = cuadro.fecha_entrega
    tdDataFechaEntrega01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataFechaEntrega02 ='';
    cuadro.proveedores.map((item,i)=>{    
        let tdBgColor = paintTdBestProv(positionbestProv,i);   
        tdDataFechaEntrega02 = row.insertCell(2+i)
        tdDataFechaEntrega02.innerHTML = item.valorizacion.plazo_entrega+' dìas'
        tdDataFechaEntrega02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });
    row = table_buena_pro_body.insertRow(11)
    const tdGarantia = row.insertCell(0)
    tdGarantia.innerHTML = 'Garantia'
    tdGarantia.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataGarantia01 = row.insertCell(1)
    tdDataGarantia01.innerHTML = '-'
    tdDataGarantia01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataGarantia02 ='';
    cuadro.proveedores.map((item,i)=>{     
        let tdBgColor = paintTdBestProv(positionbestProv,i);   
        tdDataGarantia02 = row.insertCell(2+i)
        tdDataGarantia02.innerHTML = item.valorizacion.garantia+' meses'
        tdDataGarantia02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });

    row = table_buena_pro_body.insertRow(12)
    const tdCondicion = row.insertCell(0)
    tdCondicion.innerHTML = 'Condición Pago'
    tdCondicion.setAttribute('class', 'text-left negrita wd-15rem')
    const tdDataCondicion01 = row.insertCell(1)
    tdDataCondicion01.innerHTML = '-'
    tdDataCondicion01.setAttribute('class', 'text-center text-info wd-15rem')
    var tdDataCondicion02 ='';
    cuadro.proveedores.map((item,i)=>{     
        let tdBgColor = paintTdBestProv(positionbestProv,i);  
        let condicion = '-' 
        if(item.condicion_pago == 'CREDITO'){
            condicion= item.condicion_pago +' '+item.plazo_dias;
        }else{
            condicion=item.condicion_pago;
        }
        tdDataCondicion02 = row.insertCell(2+i)
        tdDataCondicion02.innerHTML = condicion
        tdDataCondicion02.setAttribute('class', 'text-center text-muted '+tdBgColor+' negrita wd-15rem') 
    });
    
    row = table_buena_pro_body.insertRow(13)


    // const widthFisrtTd = document.querySelector("table[id='compararVariablesProveedor'] tbody").children[12].children[0].offsetWidth;


    const tdDataOption00 = row.insertCell(0)
    tdDataOption00.innerHTML = ''
    tdDataOption00.setAttribute('class', 'text-center wd-45rem')
    tdDataOption00.style.width= '15rem';

    const tdDataOption01 = row.insertCell(1)
    tdDataOption01.innerHTML = '<button type="button" class="btn btn-sm btn-default" onClick="verUltimasCompras(event);" ><i class="far fa-eye"></i> Últimas Compras</button>'
    // tdDataOption01.setAttribute('class', 'text-center wd-45rem')
    tdDataOption01.setAttribute('class', 'text-center')
    tdDataOption01.style.width= '15rem';

    tdDataOption01.setAttribute('colspan', 2)
    var tdDataOption02 ='';
    cuadro.proveedores.map((item,i)=>{    
        var TextSelectBuenaPro= 'Dar Buena Pro';
        var hasDisabled= '';
        var hasDisabled= '';
        var setClass = 'btn-default';
        
        let idbtn =indice.toString().concat(i);
        // console.log(idbtn);
        if(btnDisabledList.includes(idbtn)==true){
            hasDisabled = 'disabled';
            setClass ='btn-success';
            TextSelectBuenaPro= 'Con Buena Pro <i class="fas fa-check"></i>';

        }
        // console.log('buena_pro_id_proveedor_list');
        // console.log('item.id_proveedor');
        // console.log(item);
        // console.log(item.valorizacion);
        if(item.valorizacion.estado == 2 || item.valorizacion.estado == 5){ //  2 = tiene buena pro, 5 = atendido
            hasDisabled = 'disabled';
            setClass ='btn-success';
            TextSelectBuenaPro= 'Con Buena Pro <i class="fas fa-check"></i>';
        }
        
        tdDataOption02 = row.insertCell(2+i);
        tdDataOption02.innerHTML = '<button type="button" class="btn btn-sm '+setClass+'" name="btnSelectBuenaPro'+indice+i+'" onClick="selectBuenaPro('+indice+','+i+');" '+hasDisabled+'>'+TextSelectBuenaPro+'</button>'
        tdDataOption02.setAttribute('class', 'text-center text-muted negrita wd-15rem') 
    });


    

    // console.log(btnDisabledList);
    // if(btnDisabledList.length > 0){
    //     btnDisabledList.forEach(element => {
    //         document.querySelector('div[id="modal-comparar_variables_proveedor"] button[name="btnSelectBuenaPro'+element+'"]').setAttribute('disabled', true);
    //         document.querySelector('div[id="modal-comparar_variables_proveedor"] button[name="btnSelectBuenaPro'+element+'"]').setAttribute('class', 'btn btn-sm btn-success');
    //         document.querySelector('div[id="modal-comparar_variables_proveedor"] button[name="btnSelectBuenaPro'+element+'"]').innerHTML  = 'Con Buena Pro <i class="fas fa-check"></i>';
    //     });
    // }
    

} 



function llenarBuenaPro(cuadro_comparativo){
    buenaPro={};
    buenasPro=[];
    if(cuadro_comparativo.buena_pro.length >0){
        cuadro_comparativo.buena_pro.map((element,index)=>{
            buenaPro={
                'item_codigo': element.codigo_item,
                'item_descripcion': element.descripcion_item,
                'id_valorizacion_cotizacion': element.id_valorizacion_cotizacion,
                'id_proveedor': element.id_proveedor,
                'razon_social_proveedor': element.razon_social,
                'documento_proveedor': element.nombre_doc_identidad,
                'nro_documento_proveedor': element.nro_documento,
                'id_empresa': element.id_empresa,
                'razon_social_empresa': element.empresa_razon_social,
                'documento_empresa': element.empresa_nombre_doc_identidad,
                'nro_documento_empresa': element.empresa_nro_documento,
                'precio_valorizacion': element.precio_cotizado,
                'cantidad_valorizacion': element.cantidad_cotizada,
                'unidad_valorizacion': element.unidad_medida_cotizada,
                'justificacion':element.justificacion
            }
            buenasPro.push(buenaPro);
        });
        printListBuenaPro(buenasPro);

    }

}

function printListBuenaPro(buenaPro){
    // console.log(buenaPro);
    
    let panelBuenaPro = document.getElementById('panel-buena_pro');
    let btnAction = document.getElementById('btn-action-buena_pro');
    let html = '';
    if(buenaPro.length >0){
        html ='';
        buenaPro.forEach(function(buenaPro, index) {
        html += '<div class="panel panel-success">'+
                '<div class="panel-heading" role="tab" id="headingOne">'+
                '    <h4 class="panel-title">'+
                '        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'+index+'" aria-expanded="true" aria-controls="collapse'+index+'">'+
                '            <strong>Proveedor: </strong>'+buenaPro.razon_social_proveedor+' <strong>Item:</strong>['+buenaPro.item_codigo+'] '+buenaPro.item_descripcion+' <strong>Cantidad:</strong> '+buenaPro.cantidad_valorizacion+' <strong>Precio:</strong> '+buenaPro.precio_valorizacion+''+
                '        </a>'+
                '        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onClick="EliminarBuenaPro('+index+');"><span aria-hidden="true">&times;</span></button>'+
                '    </h4>'+
                '</div>'+
                '<div id="collapse'+index+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">'+
                '    <div class="panel-body  panel-default">'+
                '        <table class="mytable table table-condensed table-bordered table-okc-view">'+
                '            <thead>'+
                '                <th>Proveedor</th>'+
                '                <th>Unidad</th>'+
                '                <th>Cantidad</th>'+
                '                <th>Precio</th>'+
                '                <th>Justificacion</th>'+
                '                <th>Empresa</th>'+
                '            </thead>'+
                '            <tbody>'+
                '                <tr>'+
                '                    <td>'+buenaPro.razon_social_proveedor+' '+buenaPro.documento_proveedor+' '+buenaPro.nro_documento_proveedor+'</td>'+
                '                    <td>'+buenaPro.unidad_valorizacion+'</td>'+
                '                    <td>'+buenaPro.cantidad_valorizacion+'</td>'+
                '                    <td>'+buenaPro.precio_valorizacion+'</td>'+
                '                    <td>'+buenaPro.justificacion+'</td>'+
                '                    <td>'+buenaPro.razon_social_empresa+'</td>'+
                '                </tr>'+
                '            </tbody>'+
                '        </table>'+
                '    </div>'+
                '</div>'+
                '</div>';
        });

        let htmlBtnGuardarBuenaPro = '<div class="row">'+
                                        '   <div class="col-md-12 text-center">'+
                                        '       <button type="submit" class="btn btn-success btn-flat" onClick="guardarBuenaPro();" >Guardar Buena Pro</button>'+
                                        '   </div>'+
                                        '</div>';
        btnAction.innerHTML= htmlBtnGuardarBuenaPro;
                            
    }

    panelBuenaPro.innerHTML= html;
}

function EliminarBuenaPro(index){
    
    var ask = confirm('¿Desea eliminar esta buena pro?');
    if (ask == true){
        // console.log(buenasPro); 
        let id_valorizacion = buenasPro[index].id_valorizacion_cotizacion;
        if(id_valorizacion > 0){
            $.ajax({
                type: 'PUT',
                url: '/logistica/cuadro_comparativo/eliminar_buena_pro/'+id_valorizacion,
                dataType: 'JSON',
                success(response) {
                    // console.log(response);
                    
                    if(response >0){
                        buenasPro.splice(index)
                        alert("Se eliminó la buena Pro");
                        // console.log(buenasPro); 
                        printListBuenaPro(buenasPro);
                    }else{
                        alert("no se puedo elimnar")
                    }

                },
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR)
                console.log(textStatus)
                console.log(errorThrown)
            })
        }else{
            alert("No se puedo eliminar");
        }


        return false;
    }else{
        return false;
    }
}



function guardarBuenaPro(){
    var myformData = new FormData();        
    myformData.append('buenasPro', JSON.stringify(buenasPro));
    // console.log(buenasPro);
    if(buenasPro.length > 0){
        $.ajax({
            type: 'POST',
            url: '/logistica/cuadro_comparativo/guardar_buenas_pro',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            beforeSend: function(){
                $(document.body).append('<span class="loading"><div></div></span>');
            },
            success: function(response){
                // console.log(response);
                $('.loading').remove();
                if (response > 0){
                    alert("Se guardo Correctamente la Buenas Pro");
                }else{
                    alert("Error al guardar");
                }
            }
        });
        
    }else{
        alert("no hay Buena Pro para guardar");
    }
}




function descargarCuadroComparativo(event,id_grupo_cotizacion){
    event.preventDefault();
    window.open('/logistica/cuadro_comparativo/exportar_excel/'+id_grupo_cotizacion);
}

let cuadro_comparativo ={};
function mostrarCuadroComparativo(event,id_grupo_cotizacion=null){
    event.preventDefault();
    drawTableCuadroComparativo(id_grupo_cotizacion);
}

function drawTableCuadroComparativo(id_grupo_cotizacion){
    limpiarTabla('cuadro_comparativo','ALL');
    const btnExportarCuadroComarativo = document.getElementsByName('btnExportarCuadroComparativo');
    disabledControl(btnExportarCuadroComarativo, false);
    vista_extendida();

    var id_grupo = 0;
    if(id_grupo_cotizacion > 0){
        id_grupo = id_grupo_cotizacion;
    }else{
       id_grupo = document.querySelector('form[id="form-cuadro_comparativo"] input[name="id_grupo_cotizacion"]').value;
    }


    if(id_grupo > 0){// siempre debe existir un id_grupo cotizacion para generar el cuadro

        // var tipoCodigo = $('[name=tipoCodigo]').val();
        var baseUrl = '/logistica/cuadro_comparativo/mostrar_comparativo/'+id_grupo;
            $.ajax({
            type: 'GET',
            url: baseUrl,
            dataType: 'JSON',
            async:false,
            success: function(response){
                cuadro_comparativo = response;
                // console.log(response);
                
                if(verificarValorizacion(cuadro_comparativo) =='completed'){

                    dibujarCuadroCompartivo(cuadro_comparativo);
                }else{
                    alert("falta completar valorización");
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            if(qXHR.status === 500){
                alert("Error "+qXHR.status+" No se puede Generar el Cuadro Comparativo");
            }
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

    }else{
        alert("no existe id_grupo");
    }
}

function listaCuadroComparativo(){
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




function selectBuenaPro(indice_cuadro,indice_proveedor){
    let cuadro = cuadro_comparativo.cuadro_comparativo[indice_cuadro];
    let buena_pro_proveedor = cuadro_comparativo.cuadro_comparativo[indice_cuadro].proveedores[indice_proveedor];
    // console.log(cuadro)
    let textPrecioID='textPrecio'+indice_proveedor;
    let textCantidadID='textCantidad'+indice_proveedor;
   let textPrecio = document.querySelector("table[id='compararVariablesProveedor'] input[id="+textPrecioID+"]").value;
   let textCantidad= document.querySelector("table[id='compararVariablesProveedor'] input[id="+textCantidadID+"]").value;
//    console.log(textPrecio);
   
    // console.log(buena_pro_proveedor)

    itemSelected={
        'id_detalle_requerimiento': cuadro.id_detalle_requerimiento,
        'codigo_requerimiento': cuadro.codigo_requerimiento,
        'item_codigo': cuadro.codigo,
        'item_descripcion': cuadro.descripcion,
        'item_cantidad': cuadro.cantidad,
        'unidad_medida': cuadro.unidad_medida,
        'precio_referencial': cuadro.precio_referencial,
        'fecha_entrega': cuadro.fecha_entrega
    }

    buenaPro={
        'item_codigo': cuadro.codigo,
        'item_descripcion': cuadro.descripcion,
        'id_valorizacion_cotizacion': buena_pro_proveedor.valorizacion.id_valorizacion_cotizacion,
        'id_proveedor': buena_pro_proveedor.valorizacion.id_proveedor,
        'razon_social_proveedor':buena_pro_proveedor.razon_social,
        'documento_proveedor':buena_pro_proveedor.nombre_doc_identidad,
        'nro_documento_proveedor':buena_pro_proveedor.nro_documento,
        'id_empresa': buena_pro_proveedor.valorizacion.empresa.id_empresa,
        'razon_social_empresa':buena_pro_proveedor.valorizacion.empresa.empresa_razon_social,
        'documento_empresa':buena_pro_proveedor.valorizacion.empresa.empresa_nombre_doc_identidad,
        'nro_documento_empresa':buena_pro_proveedor.valorizacion.empresa.empresa_nro_documento,
        // 'cantidad_valorizacion':buena_pro_proveedor.valorizacion.cantidad_cotizada,
        'cantidad_valorizacion':textCantidad,
        'unidad_valorizacion':buena_pro_proveedor.valorizacion.unidad_medida_cotizada,
        // 'precio_valorizacion':buena_pro_proveedor.valorizacion.precio_cotizado,
        'precio_valorizacion':textPrecio,
        'plazo_entrega':buena_pro_proveedor.valorizacion.plazo_entrega,
        'monto_descuento':buena_pro_proveedor.valorizacion.monto_descuento,
        'lugar_despacho':buena_pro_proveedor.valorizacion.lugar_despacho,
        'incluye_igv':buena_pro_proveedor.valorizacion.incluye_igv,
        'justificacion':''
    }
    
    // console.log(itemSelected);
    // console.log(buenaPro);

    $('#modal-buena_pro').modal({
        show: true,
        backdrop: 'static'
    });

    $('#buena_pro_proveedor').text(buena_pro_proveedor.nombre_doc_identidad+' '+buena_pro_proveedor.nro_documento+' '+buena_pro_proveedor.razon_social);
    $('#buena_pro_item').text(cuadro.codigo+' - '+cuadro.descripcion);
    $('[name=idbtnSelectBuenaPro]').val((""+indice_cuadro+indice_proveedor));
 
}

function addBuenaPro(event){
    event.preventDefault();
    buenaPro.justificacion = document.getElementById('justificacionBuenaPro').value;    
    buenasPro.push(buenaPro);
    printListBuenaPro(buenasPro);

    // console.log(buenasPro);

    let idbtnBuenaPro = $('[name=idbtnSelectBuenaPro]').val();
    btnDisabledList.push(idbtnBuenaPro);
    
    disableButtonBuenaPro(idbtnBuenaPro)
    $('#modal-buena_pro').modal('hide');
    alert("Buena Pro agregada!");
}

function disableButtonBuenaPro(id){
    // console.log(id);
    
    let nameBtnBuenaPro = 'btnSelectBuenaPro'+id;    
    document.getElementsByName(nameBtnBuenaPro)[0].setAttribute('disabled' ,true);
    document.getElementsByName(nameBtnBuenaPro)[0].setAttribute('class' ,'btn btn-sm btn-success btn-flat');
    document.getElementsByName(nameBtnBuenaPro)[0].innerHTML='Con Buena Pro <i class="fas fa-check"></i>';
}


function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
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



function disabledControl(element, value) {
    // console.log("disable control");
    let i
    for (i = 0; i < element.length; i++) {
        if (value === false) {
            element[i].removeAttribute('disabled')
            element[i].classList.remove("disabled");

        } else {
            element[i].setAttribute('disabled', 'true')
            element[i].classList.add("disabled");

        }
    }
    return null
}

