var proveedoresContactoList =[];

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
    document.querySelector('form[id="form-proveedores_producto_determinado"] input[name="id_producto"]').value = id_producto;
    document.querySelector('form[id="form-proveedores_producto_determinado"] input[name="producto"]').value = descripcion;
    $('#modal-catalogo-productos').modal('hide');
}

function getDataFormulario(){
    let id_producto= document.querySelector('form[id="form-proveedores_producto_determinado"] input[name="id_producto"]').value;
    let id_empresa= document.querySelector('form[id="form-proveedores_producto_determinado"] select[name="empresa"]').value;
    let tipo_periodo= document.querySelector('form[id="form-proveedores_producto_determinado"] select[name="tipo_periodo"]').value;
    let año= document.querySelector('form[id="form-proveedores_producto_determinado"] input[name="año"]').value;

    return data={
        id_producto,
        id_empresa,
        tipo_periodo,
        año
    };

}

function getDataProveedoresProductoDeterminado(){

    let data = getDataFormulario();
    
    return new Promise(function(resolve, reject) {
        const baseUrl = '/logistica/proveedores_producto_determinado';
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


function reporteProveedoresProductoDeterminado(event){
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
        reportereporteProveedoresProductoDeterminadoExcel(formData);
        
    }else if(option =='PREVISUALIZAR'){

        getDataProveedoresProductoDeterminado().then(function(data) { //17 ESTADO ENVIADO
            // Run this when your request was successful
            // console.log(data);
            // console.log(option);
            if(data.length >0){
                reporteProveedoresProductoDeterminadoVisualizar(data);
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })

    }
 
}


function reporteProveedoresProductoDeterminadoVisualizar(data){
    document.querySelector('table[id="listaProveedoresProductoDeterminado"]').parentElement.setAttribute('style','overflow-x:auto')

    var vardataTables = funcDatatables();
    $('#listaProveedoresProductoDeterminado').dataTable({
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
                    return row.razon_social;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let texto = '';
                    let nro_documento = row.nro_documento;
                    let tipo_documento = row.tipo_documento;
                    texto = tipo_documento +': '+ nro_documento;

                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){               
                    let texto = row.direccion_fiscal?row.direccion_fiscal:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){               
                    let texto = row.telefono?row.telefono:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){     
                    let texto = row.pais?row.pais:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let btn= '<button class="btn btn-info btn-sm" onclick="verContactos(event,'+row.id_proveedor+');"   data-original-title="Contacto"><i class="far fa-eye"></i></button>';
                    return btn;
                }
            }
 

        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},

                    ],
        'order': [
            [1, 'asc']
        ]
    });
    
    let tablelistaitem = document.getElementById('listaProveedoresProductoDeterminado_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}


function verContactos(event, id_proveedor){
    event.preventDefault();
    $('#modal-lista-contacto').modal({
        show: true,
        backdrop: 'static'
    });

    proveedoresContactoList.forEach(element => {
        if(element.id_proveedor == id_proveedor){
            llenarTablaListaContacto(element.contacto)
        }
    });
    console.log(proveedoresContactoList);
    
}

function llenarTablaListaContacto(data){

    var vardataTables = funcDatatables();
    $('#listaContacto').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'data': data,
        'columns': [
            {'data': 'id_datos_contacto'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.nombre?row.nombre:"-";
                }
            },

            {'render':
                function (data, type, row, meta){               
                    let texto = row.dni?row.dni:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){               
                    let texto = row.cargo?row.cargo:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){     
                    let texto = row.email?row.email:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){     
                    let texto = row.telefono?row.telefono:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){     
                    let texto = row.tipo_establecimiento?row.tipo_establecimiento:"-";
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){     
                    let texto = row.direccion_establecimiento?row.direccion_establecimiento:"-";
                    return texto;
                }
            }
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},

                    ],
        'order': [
            [1, 'asc']
        ]
    });
    
    let tablelistaitem = document.getElementById('listaContacto_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}


function reportereporteProveedoresProductoDeterminadoExcel(data){
    $.ajax({
        type: 'GET',
        url: '/logistica/proveedores_producto_determinado_excel/',
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