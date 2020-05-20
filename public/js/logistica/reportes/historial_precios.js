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
    document.querySelector('form[id="form-historial_precios"] input[name="id_producto"]').value = id_producto;
    document.querySelector('form[id="form-historial_precios"] input[name="producto"]').value = descripcion;
    $('#modal-catalogo-productos').modal('hide');
}

function getDataFormulario(){
    let id_producto= document.querySelector('form[id="form-historial_precios"] input[name="id_producto"]').value;
    let id_empresa= document.querySelector('form[id="form-historial_precios"] select[name="empresa"]').value;
    let año= document.querySelector('form[id="form-historial_precios"] input[name="año"]').value;

    return data={
        id_producto,
        id_empresa,
        año
    };

}

function getDataHistorialPrecios(){ 
    let data = getDataFormulario();

        return new Promise(function(resolve, reject) {
            const baseUrl = '/logistica/historial_precios';
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

function reporteHistorialPrecios(event){
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
        reporteHistorialPreciosExcel(formData);
        
    }else if(option =='PREVISUALIZAR'){

        getDataHistorialPrecios().then(function(data) { //17 ESTADO ENVIADO
            // Run this when your request was successful
            // console.log(data);
            // console.log(option);
            if(data.length >0){
                reporteHistorialPreciosVisualizar(data);
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })

    }
}

function reporteHistorialPreciosVisualizar(data){
    var vardataTables = funcDatatables();
    $('#listaHistorialPrecios').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'data': data,
        'columns': [
            {'data': 'id'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.id_item;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.unidad_medida
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.precio_unitario
                }
            },
            {'render':
                function (data, type, row, meta){
                    $proveedor = row.razon_social_proveedor+' '+row.tipo_documento+': '+row.nro_documento_proveedor; 
                    return $proveedor;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.documento
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.fecha_registro
                }
            }

        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},

                    ],
        'order': [
            [4, 'desc']
        ]
    });
    
    let tablelistaitem = document.getElementById('listaHistorialPrecios_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function reporteHistorialPreciosExcel(data){
    $.ajax({
        type: 'GET',
        url: '/logistica/historial_precios_excel/',
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