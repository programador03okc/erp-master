function catalogoProductosModal(){   
    $('#modal-catalogo-productos').modal({
        show: true,
        backdrop: 'static'
    });
    listarProductos();
}

function listarProductos(){
    var vardataTables = funcDatatables();
    $('#listaProductos').dataTable({
        'scrollY':  '50vh',
        'scrollCollapse': true,
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'ajax': '/logistica/reportes/listar_productos',
        'columns': [
            {'data': 'id_item'},
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida_descripcion'},
            {'data': 'stock'}
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},
            { 'aTargets': [1], 'sClass': 'invisible'}
                    ],
        'order': [
            [3, 'asc']
        ]
    });

    let tablelistaitem = document.getElementById('listaProductos_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;

}

$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaProductos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaProductos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idItem = $(this)[0].children[0].innerHTML;
        var idProd = $(this)[0].children[1].innerHTML;
        var codigo = $(this)[0].children[2].innerHTML;
        var descri = $(this)[0].children[3].innerHTML;
        $('.modal-footer #id_item').text(idItem);
        $('.modal-footer #codigo').text(codigo);
        $('.modal-footer #descripcion').text(descri);
        $('.modal-footer #id_producto').text(idProd);
    });
});


function selectProducto(){
    let id_item = document.querySelector('div[id="modal-catalogo-productos"] label[id="id_item"]').textContent;
    let id_producto = document.querySelector('div[id="modal-catalogo-productos"] label[id="id_producto"]').textContent;
    let descripcion = document.querySelector('div[id="modal-catalogo-productos"] label[id="descripcion"]').textContent;
    document.querySelector('form[id="form-frecuencia_compra"] input[name="id_producto"]').value = id_producto;
    document.querySelector('form[id="form-frecuencia_compra"] input[name="producto"]').value = descripcion;
    $('#modal-catalogo-productos').modal('hide');
}

function getDataFormulario(){
    let id_producto= document.querySelector('form[id="form-frecuencia_compra"] input[name="id_producto"]').value;
    let id_empresa= document.querySelector('form[id="form-frecuencia_compra"] select[name="empresa"]').value;
    let año= document.querySelector('form[id="form-frecuencia_compra"] input[name="año"]').value;
    
 

    return data={
        id_producto,
        id_empresa,
        año
    };

}



function getDataFrecuenciaCompra(){ 
    let data = getDataFormulario();

        return new Promise(function(resolve, reject) {
            const baseUrl = '/logistica/frecuencia_compras';
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


function reporteFrecuenciaCompra(event){
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
        reporteFrecuenciaCompraExcel(formData);
        
    }else if(option =='PREVISUALIZAR'){

        getDataFrecuenciaCompra().then(function(data) { //17 ESTADO ENVIADO
            // Run this when your request was successful
            // console.log(data);
            // console.log(option);
            if(data.length >0){
                reporteFrecuenciaCompraVisualizar(data);
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })

    }
}


function reporteFrecuenciaCompraVisualizar(data){
    var vardataTables = funcDatatables();
    $('#listaFrecuenciaComra').dataTable({
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
                    return row.razon_social+' RUC:'+row.nro_documento;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.primera_compra
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.ultima_compra
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.rango
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.nro_compras
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.frecuencia
                }
            }

        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},

                    ],
        'order': [
            [7, 'desc']
        ]
    });
    
    let tablelistaitem = document.getElementById('listaFrecuenciaComra_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}
function reporteFrecuenciaCompraExcel(data){
    $.ajax({
        type: 'GET',
        url: '/logistica/frecuencia_compras_excel/',
        dataType: 'JSON',
        data:{data},
        success: function(response){
            data = response;

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