$(function(){
});

 

function getDataFormulario(){
    let id_proveedor= document.querySelector('form[id="form-compras_por_proveedor"] select[name="razon_social_proveedor"]').value;
    let id_empresa= document.querySelector('form[id="form-compras_por_proveedor"] select[name="empresa"]').value;
    let tipo_periodo= document.querySelector('form[id="form-compras_por_proveedor"]     [name="tipo_periodo"]').value;
    let año= document.querySelector('form[id="form-compras_por_proveedor"] input[name="año"]').value;

    return data={
        id_proveedor,
        id_empresa,
        tipo_periodo,
        año
    };

}
function getDataComprasPorProveedor(){

    let data = getDataFormulario();

    return new Promise(function(resolve, reject) {
        const baseUrl = '/logistica/compras_por_proveedor';
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

function reporteComprasPorProveedor(event){
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
        reporteComprasPorProveedorExcel(formData);
        
    }else if(option =='PREVISUALIZAR'){

        getDataComprasPorProveedor().then(function(data) { //17 ESTADO ENVIADO
            // Run this when your request was successful
            // console.log(data);
            // console.log(option);
            if(data.length >0){
                reporteComprasPorProveedorVisualizar(data);
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })
    }

}

function reporteComprasPorProveedorExcel(data){
    $.ajax({
        type: 'GET',
        url: '/logistica/compras_por_proveedor_excel/',
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
function reporteComprasPorProveedorVisualizar(data){
    var vardataTables = funcDatatables();
    $('#listaComprasPorProveedor').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'data': data,
        'columns': [
            {'data': 'id_proveedor'},
            {'render':
            function (data, type, row, meta){
                return meta.row +1;
            }
            },
            {'render':
            function (data, type, row, meta){
                return row.razon_social+' - '+row.nro_documento;
            }
            },
            {'data': 'cantidad_compras.01'},
            {'data': 'cantidad_compras.02'},
            {'data': 'cantidad_compras.03'},
            {'data': 'cantidad_compras.04'},
            {'data': 'cantidad_compras.05'},
            {'data': 'cantidad_compras.06'},
            {'data': 'cantidad_compras.07'},
            {'data': 'cantidad_compras.08'},
            {'data': 'cantidad_compras.09'},
            {'data': 'cantidad_compras.10'},
            {'data': 'cantidad_compras.11'},
            {'data': 'cantidad_compras.12'}
 
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},

                    ],
        'order': [
            [2, 'asc']
        ]
    });
    
    let tablelistaitem = document.getElementById('listaComprasPorProveedor_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}