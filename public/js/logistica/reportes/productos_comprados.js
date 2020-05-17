$(function(){





});

function get_id_proveedor(event){
    let option = document.getElementsByName('razon_social_proveedor')[0];
    let razon_social = option.value;
    var numero_ruc = event.target.options[event.target.selectedIndex].dataset.numeroRuc;
    document.getElementsByName('numero_ruc')[0].value = numero_ruc;

}

// function get_id_sede(event){
//     let option = document.getElementsByName('sede')[0];
//     let id_sede = option.value;
//     // console.log(id_sede);
    
//     let html_select ='';
//     $.ajax({
//         type: 'GET',
//         url: '/cargar_almacenes/'+id_sede,
//         success: function(response){
//             // console.log(response); 
//             html_select +='<option value="0">Elija una opción</option>';
//             response.forEach(element => {
//                 html_select+='<option value="'+element.id_almacen+'">'+element.descripcion+'</option>';
//             });
//             document.getElementsByName('almacen')[0].innerHTML = html_select;
//         }
//     });

// }

function getDataFormulario(){
    let id_proveedor= document.querySelector('form[id="form-productos_comprados"] select[name="razon_social_proveedor"]').value;
    let id_empresa= document.querySelector('form[id="form-productos_comprados"] select[name="empresa"]').value;
    let fecha_desde= document.querySelector('form[id="form-productos_comprados"] input[name="fecha_desde"]').value;
    let fecha_hasta= document.querySelector('form[id="form-productos_comprados"] input[name="fecha_hasta"]').value;

    return data={
        id_proveedor,
        id_empresa,
        fecha_desde,
        fecha_hasta
    };

}
function getDataProductosComprados(){

    let data = getDataFormulario();

    return new Promise(function(resolve, reject) {
        const baseUrl = '/logistica/productos_comprados';
    $.ajax({
        type: 'GET',
        url:baseUrl,
        dataType: 'JSON',
        data:data,
        success(response) {
            resolve(response) // Resolve promise and go to then() 
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}

function reporteProductosComprados(event){
    event.preventDefault();
    let optionsFormat =  document.querySelectorAll('input[name="inlineRadioOptions"]');
    let option = '';
    optionsFormat.forEach((element,index) => {
        if(element.checked == true){
            option = element.value;
        }
    });


    
    if(option =='EXCEL'){
        
        let formData = getDataFormulario();
        reporteProductosCompradosExcel(formData);
        
    }else if(option =='PREVISUALIZAR'){

        getDataProductosComprados().then(function(data) { //17 ESTADO ENVIADO
            // Run this when your request was successful
            // console.log(data);
            // console.log(option);
            if(data.length >0){
                reporteProductosCompradosVisualizar(data);
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })
    }

}

function reporteProductosCompradosExcel(data){
    $.ajax({
        type: 'GET',
        url: '/logistica/productos_comprados_excel/',
        dataType: 'JSON',
        data:{data},
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

function reporteProductosCompradosVisualizar(data){
    var vardataTables = funcDatatables();
    $('#listaProductosComprados').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'data': data,
        'columns': [
            {'data': 'id_item'},
            {'render':
            function (data, type, row, meta){
                return meta.row +1;
            }
            },
            {'data': 'proveedor'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cantidad_cotizada'},
            {'data': 'unidad_medida_cotizada'},
            {'data': 'precio_cotizado'},
            {'data': 'igv'},
            {'data': 'precio_sin_igv'},
            {'data': 'subtotal'},
            {'data': 'flete'},
            {'data': 'porcentaje_descuento'},
            {'data': 'monto_descuento'},
            {'data': 'garantia'},
            {'data': 'lugar_despacho'},
            {'data': 'plazo_entrega'},
            {'data': 'moneda'},
            {'data': 'codigo_orden'},
            {'data': 'fecha'}
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},

                    ],
        'order': [
            [2, 'asc']
        ]
    });
    let tablelistaitem = document.getElementById('listaProductosComprados_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

// function construirTablaReporteProductosComprados(data){

// }
















// function reporteComprasPorProveedorExcel(){
//     $.ajax({
//         type: 'GET',
//         url: '/reporte-compras_por_proveedor_excel',
//         success: function(response){
//             // console.log(response); 
//         }
//     });
// }

// function generarReporte(event){
//     event.preventDefault();
//     let optionsFormat =  document.querySelectorAll('input[name="inlineRadioOptions"]');

//     optionsFormat.forEach((element,index) => {
//         if(element.checked == true){
//             let option = element.value;
//             switch (option) {
//                 case 'EXCEL':
//                     console.log('EXCEL');
//                     reporteComprasPorProveedorExcel();
//                     break;
            
//                 default:
//                     console.log('DEFAULT');
//                     reporteComprasPorProveedorVisualizar();
//                     break;
//             }
//         }
//     });



//     // let data = getDataProductosComprados();



// }