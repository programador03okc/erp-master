

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
    document.querySelector('form[id="form-compras_por_producto"] input[name="id_producto"]').value = id_producto;
    document.querySelector('form[id="form-compras_por_producto"] input[name="producto"]').value = descripcion;
    $('#modal-catalogo-productos').modal('hide');
}

function getDataFormulario(){
    let id_producto= document.querySelector('form[id="form-compras_por_producto"] input[name="id_producto"]').value;
    let id_empresa= document.querySelector('form[id="form-compras_por_producto"] select[name="empresa"]').value;
    let tipo_periodo= document.querySelector('form[id="form-compras_por_producto"] select[name="tipo_periodo"]').value;
    let año= document.querySelector('form[id="form-compras_por_producto"] input[name="año"]').value;

    return data={
        id_producto,
        id_empresa,
        tipo_periodo,
        año
    };

}

function getDataComprasPorProducto(){

    let data = getDataFormulario();
    
    return new Promise(function(resolve, reject) {
        const baseUrl = '/logistica/compras_por_producto';
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


function reporteComprasPorProducto(event){
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

        getDataComprasPorProducto().then(function(data) { //17 ESTADO ENVIADO
            // Run this when your request was successful
            // console.log(data);
            // console.log(option);
            if(data.length >0){
                reporteComprasPorProductoVisualizar(data);
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })

    }

}

function reporteComprasPorProductoVisualizar(data){
    var vardataTables = funcDatatables();
    $('#listaComprasPorProducto').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'data': data,
        'columns': [
            {'data': 'id_producto'},
            {'render':
            function (data, type, row, meta){
                return meta.row +1;
            }
            },
            {'render':
            function (data, type, row, meta){
                return row.descripcion_producto;
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
            [3, 'asc']
        ]
    });
    
    let tablelistaitem = document.getElementById('listaComprasPorProducto_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}


function reporteComprasPorProveedorExcel(data){
    $.ajax({
        type: 'GET',
        url: '/logistica/compras_por_producto_excel/',
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