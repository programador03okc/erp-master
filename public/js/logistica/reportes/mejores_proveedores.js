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
    document.querySelector('form[id="form-mejores_proveedores"] input[name="id_producto"]').value = id_producto;
    document.querySelector('form[id="form-mejores_proveedores"] input[name="producto"]').value = descripcion;
    $('#modal-catalogo-productos').modal('hide');
}

function getDataFormulario(){
    let id_producto= document.querySelector('form[id="form-mejores_proveedores"] input[name="id_producto"]').value;
    let id_empresa= document.querySelector('form[id="form-mejores_proveedores"] select[name="empresa"]').value;
    let tipo_periodo= document.querySelector('form[id="form-mejores_proveedores"] select[name="tipo_periodo"]').value;
    let año= document.querySelector('form[id="form-mejores_proveedores"] input[name="año"]').value;
    
    let condicion_precio= document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_precio"]').value;
    let precio= document.querySelector('form[id="form-mejores_proveedores"] input[name="precio"]').value;
    let condicion_garantia= document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_garantia"]').value;
    let garantia= document.querySelector('form[id="form-mejores_proveedores"] input[name="garantia"]').value;
    let condicion_tiempo_entrega= document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_tiempo_entrega"]').value;
    let tiempo_entrega= document.querySelector('form[id="form-mejores_proveedores"] input[name="tiempo_entrega"]').value;
    let optionMejorPrecio= document.querySelector('form[id="form-mejores_proveedores"] input[name="optionMejorPrecio"]').checked;

    return data={
        id_producto,
        id_empresa,
        tipo_periodo,
        año,
        condicion_precio,
        precio,
        condicion_garantia,
        garantia,
        condicion_tiempo_entrega,
        tiempo_entrega,
        optionMejorPrecio
        
    };

}

function getDataProveedoresProductoDeterminado(){ 
    let data = getDataFormulario();

        return new Promise(function(resolve, reject) {
            const baseUrl = '/logistica/mejores_proveedores';
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


function reporteMejoresProveedores(event){
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
        reportereporteMejoresProveedoresExcel(formData);
        
    }else if(option =='PREVISUALIZAR'){

        getDataProveedoresProductoDeterminado().then(function(data) { //17 ESTADO ENVIADO
            // Run this when your request was successful
            // console.log(data);
            // console.log(option);
            if(data.length >0){
                reporteMejoresProveedoresVisualizar(data);
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })

    }


    
}


function reporteMejoresProveedoresVisualizar(data){
    document.querySelector('table[id="listaMejoresProveedores"]').parentElement.setAttribute('style','overflow-x:auto')

    var vardataTables = funcDatatables();
    $('#listaMejoresProveedores').dataTable({
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
            {'render':
                function (data, type, row, meta){
                    let texto = '';
                    let head = '';
                    if(row.proveedor.length > 0){
                        if(row.proveedor[0] != undefined){
                            let razon_social = row.proveedor[0].razon_social?row.proveedor[0].razon_social:"";
                            let nro_documento = row.proveedor[0].nro_documento?row.proveedor[0].nro_documento:"";
                            head =razon_social + '\n[RUC: '+ nro_documento+']';
                            document.querySelector("table[id='listaMejoresProveedores'] th[id='proveedor01']").textContent = head;

                            if(typeof (row.proveedor[0].compras) == 'object' && row.proveedor[0].compras.length > 0){
                                if(row.proveedor[0].compras[0] != undefined){
                                    texto = row.proveedor[0].compras[0].unidad_medida_cotizada;
                                }
        
                            }
                        }
                    }


                    
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let texto = '';
                    if(row.proveedor[0] != undefined){
                        if(typeof row.proveedor[0].compras == 'object' && row.proveedor[0].compras.length > 0){
                            if(row.proveedor[0].compras[0] != undefined){
                                texto = row.proveedor[0].compras[0].precio_cotizado;
                            }
                        }
                    }
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let texto = '';
                    if(row.proveedor[0] != undefined){
                        if(typeof row.proveedor[0].compras == 'object' && row.proveedor[0].compras.length > 0){
                            if(row.proveedor[0].compras[0] != undefined){
                                texto = row.proveedor[0].compras[0].garantia;
                            }
                        }
                    }
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let texto = '';
                    if(row.proveedor[0] != undefined){
                        if(typeof row.proveedor[0].compras == 'object' && row.proveedor[0].compras.length > 0){
                            if(row.proveedor[0].compras[0] != undefined){
                                texto = row.proveedor[0].compras[0].plazo_entrega;
                            }
                        }
                    }
                    return texto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let texto = '';
                    if(row.proveedor[0] != undefined){
                        if(typeof row.proveedor[0].compras == 'object' && row.proveedor[0].compras.length > 0){

                            if(row.proveedor[0].compras[0] != undefined){
                                texto = row.proveedor[0].compras[0].flete;
                            }
                        }
                    }
                    return texto;
                }
            },

        {'render':
            function (data, type, row, meta){
                let texto = '';
                let head = '';
                if(row.proveedor.length > 0){
                    if(row.proveedor[1] != undefined){

                        let razon_social = row.proveedor[1].razon_social?row.proveedor[1].razon_social:"-";
                        let nro_documento = row.proveedor[1].nro_documento?row.proveedor[1].nro_documento:"-";
                        head =razon_social + ' \n[RUC: '+ nro_documento+']';
                        document.querySelector("table[id='listaMejoresProveedores'] th[id='proveedor02']").textContent = head;
                        
                        if(typeof row.proveedor[1].compras == 'object' && row.proveedor[1].compras.length > 0){
                            if(row.proveedor[1].compras[0] != undefined){
                                texto = row.proveedor[1].compras[0].unidad_medida_cotizada;
                            }
                        }
                    }
                }
                
                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';                
                if(row.proveedor[1] != undefined){
                    if(typeof row.proveedor[1].compras == 'object' && row.proveedor[1].compras.length > 0){
                        if(row.proveedor[1].compras[0] != undefined){
                            texto = row.proveedor[1].compras[0].precio_cotizado;
                        }
                    }
                }
                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';
                if(row.proveedor[1] != undefined){
                    if(typeof row.proveedor[1].compras == 'object' && row.proveedor[1].compras.length > 0){
                        if(row.proveedor[1].compras[0] != undefined){
                            texto = row.proveedor[1].compras[0].garantia;
                        }
                    }
                }
                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';
                if(row.proveedor[1] != undefined){
                    if(typeof row.proveedor[1].compras == 'object' && row.proveedor[1].compras.length > 0){
                        if(row.proveedor[1].compras[0] != undefined){
                            texto = row.proveedor[1].compras[0].plazo_entrega;
                        }
                    }
                }
                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';
                if(row.proveedor[1] != undefined){
                    if(typeof row.proveedor[1].compras == 'object' && row.proveedor[1].compras.length > 0){
                        if(row.proveedor[1].compras[0] != undefined){
                            texto = row.proveedor[1].compras[0].flete;
                        }
                    }
                }
                return texto;
            }
        },

        {'render':
            function (data, type, row, meta){
                let texto = '';
                let head = '';
                if(row.proveedor.length > 0){
                    if(row.proveedor[2] != undefined ){
                        let razon_social = row.proveedor[2].razon_social?row.proveedor[2].razon_social:"";
                        let nro_documento = row.proveedor[2].nro_documento?row.proveedor[2].nro_documento:"";
                        head =razon_social + ' \n[RUC: '+ nro_documento+']';
                        document.querySelector("table[id='listaMejoresProveedores'] th[id='proveedor03']").textContent = head;

                            if(typeof row.proveedor[2].compras == 'object' && row.proveedor[2].compras.length > 0){
                                if(row.proveedor[2].compras[0] != undefined){
                                    texto = row.proveedor[2].compras[0].unidad_medida_cotizada?row.proveedor[2].compras[0].unidad_medida_cotizada:"";
                                }
                            }
                    }
                }

                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';
                if(row.proveedor[2] != undefined ){
                    if(typeof row.proveedor[2].compras == 'object' && row.proveedor[2].compras.length > 0){
                        if(row.proveedor[2].compras[0] != undefined){
                            texto = row.proveedor[2].compras[0].precio_cotizado?row.proveedor[2].compras[0].precio_cotizado:"";
                        }
                    }
                }
                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';
                if(row.proveedor[2] != undefined ){
                    if(typeof row.proveedor[2].compras == 'object' && row.proveedor[2].compras.length > 0){
                        if(row.proveedor[2].compras[0] != undefined){
                            texto = row.proveedor[2].compras[0].garantia?row.proveedor[2].compras[0].garantia:"";
                        }
                    }
                }
                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';
                if(row.proveedor[2] != undefined ){
                    if(typeof row.proveedor[2].compras == 'object' && row.proveedor[2].compras.length > 0){
                        if(row.proveedor[2].compras[0] != undefined){
                            texto = row.proveedor[2].compras[0].plazo_entrega?row.proveedor[2].compras[0].plazo_entrega:"";
                        }
                    }
                }
                return texto;
            }
        },
        {'render':
            function (data, type, row, meta){
                let texto = '';
                if(row.proveedor[2] != undefined ){
                    if(typeof row.proveedor[2].compras == 'object' && row.proveedor[2].compras.length > 0){
                        if(row.proveedor[2].compras[0] != undefined){
                            texto = row.proveedor[2].compras[0].flete?row.proveedor[2].compras[0].flete:"";
                        }
                    }
                }
                return texto;
            }
        },


    {'render':
        function (data, type, row, meta){
            let texto = '';
            let head = '';
            if(row.proveedor.length > 0){
                if(row.proveedor[3] != undefined ){
                let razon_social = row.proveedor[3].razon_social?row.proveedor[3].razon_social:"";
                let nro_documento = row.proveedor[3].nro_documento?row.proveedor[3].nro_documento:"";
                head =razon_social + ' \n[RUC: '+ nro_documento+']';
                document.querySelector("table[id='listaMejoresProveedores'] th[id='proveedor04']").textContent = head;

                    if(typeof row.proveedor[3].compras == 'object' && row.proveedor[3].compras.length > 0){
                        if(row.proveedor[3].compras[0] != undefined){
                            texto = row.proveedor[3].compras[0].unidad_medida_cotizada;
                        }
                    }
                }
            }

            return texto;
        }
    },
    {'render':
        function (data, type, row, meta){
            let texto = '';
            if(row.proveedor[3] != undefined ){
                if(typeof row.proveedor[3].compras == 'object' && row.proveedor[3].compras.length > 0){
                    if(row.proveedor[3].compras[0] != undefined){
                        texto = row.proveedor[3].compras[0].precio_cotizado;
                    }
                }
            }
            return texto;
        }
    },
    {'render':
        function (data, type, row, meta){
            let texto = '';
            if(row.proveedor[3] != undefined ){
                if(typeof row.proveedor[3].compras == 'object' && row.proveedor[3].compras.length > 0){
                    if(row.proveedor[3].compras[0] != undefined){
                    texto = row.proveedor[3].compras[0].garantia;
                    }
                }
            }
            return texto;
        }
    },
    {'render':
        function (data, type, row, meta){
            let texto = '';
            if(row.proveedor[3] != undefined ){
                if(typeof row.proveedor[3].compras == 'object' && row.proveedor[3].compras.length > 0){
                    if(row.proveedor[3].compras[0] != undefined){
                        texto = row.proveedor[3].compras[0].plazo_entrega;
                    }
                }
            }
            return texto;
        }
    },
    {'render':
        function (data, type, row, meta){
            let texto = '';
            if(row.proveedor[3] != undefined ){
                if(typeof row.proveedor[3].compras == 'object' && row.proveedor[3].compras.length > 0){
                    if(row.proveedor[3].compras[0] != undefined){
                        texto = row.proveedor[3].compras[0].flete;
                    }
                }
            }
            return texto;
        }
    },

    {'render':
    function (data, type, row, meta){
        let texto = '';
        let head = '';
        if(row.proveedor.length > 0){
            if(row.proveedor[4] != undefined ){
            let razon_social = row.proveedor[4].razon_social?row.proveedor[4].razon_social:"";
            let nro_documento = row.proveedor[4].nro_documento?row.proveedor[4].nro_documento:"";
            head =razon_social + ' \n[RUC: '+ nro_documento+']';
            document.querySelector("table[id='listaMejoresProveedores'] th[id='proveedor05']").textContent = head;

                if(row.proveedor[4].compras != undefined && row.proveedor[4].compras.length > 0){
                    if(row.proveedor[4].compras[0] != undefined){
                        texto = row.proveedor[4].compras[0].unidad_medida_cotizada;
                    }
                }

            }
        }

        return texto;
    }
    },
    {'render':
    function (data, type, row, meta){
        let texto = '';
        if(row.proveedor[4] != undefined ){
            if(typeof row.proveedor[4].compras == 'object' && row.proveedor[4].compras.length > 0){
                if(row.proveedor[4].compras[0] != undefined){
                    texto = row.proveedor[4].compras[0].precio_cotizado;
                }
            }
        }
        return texto;
    }
    },
    {'render':
    function (data, type, row, meta){
        let texto = '';
        if(row.proveedor[4] != undefined ){
            if(typeof row.proveedor[4].compras == 'object' && row.proveedor[4].compras.length > 0){
                if(row.proveedor[4].compras[0] != undefined){
                    texto = row.proveedor[4].compras[0].garantia;
                }
            }
        }
        return texto;
    }
    },
    {'render':
    function (data, type, row, meta){
        let texto = '';
        if(row.proveedor[4] != undefined ){
            if(typeof row.proveedor[4].compras == 'object' && row.proveedor[4].compras.length > 0){
                if(row.proveedor[4].compras[0] != undefined){
                    texto = row.proveedor[4].compras[0].plazo_entrega;
                }
            }
        }
        return texto;
    }
    },
    {'render':
    function (data, type, row, meta){
        let texto = '';
        if(row.proveedor[4] != undefined ){
            if(typeof row.proveedor[4].compras == 'object' && row.proveedor[4].compras.length > 0){
                if(row.proveedor[4].compras[0] != undefined){
                    texto = row.proveedor[4].compras[0].flete;
                }
            }
        }
        return texto;
    }
    }

        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},

                    ],
        'order': [
            [3, 'asc']
        ]
    });
    
    let tablelistaitem = document.getElementById('listaMejoresProveedores_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function reportereporteMejoresProveedoresExcel(data){
    // console.log(data);
     $.ajax({
        type: 'GET',
        url: '/logistica/mejores_proveedores_excel/',
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

function checkMejorPrecio(event){
    let status = event.target.checked;
    if(status == true){
        document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_precio"]').setAttribute('disabled',true);
        // document.querySelector('form[id="form-mejores_proveedores"] input[name="precio"]').value = "";
        document.querySelector('form[id="form-mejores_proveedores"] input[name="precio"]').setAttribute('disabled',true);
        document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_garantia"]').setAttribute('disabled',true);
        // document.querySelector('form[id="form-mejores_proveedores"] input[name="garantia"]').value = "";
        document.querySelector('form[id="form-mejores_proveedores"] input[name="garantia"]').setAttribute('disabled',true);
        document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_tiempo_entrega"]').setAttribute('disabled',true);
        // document.querySelector('form[id="form-mejores_proveedores"] input[name="tiempo_entrega"]').value= "";
        document.querySelector('form[id="form-mejores_proveedores"] input[name="tiempo_entrega"]').setAttribute('disabled',true);
    }else{
        document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_precio"]').removeAttribute('disabled');
        document.querySelector('form[id="form-mejores_proveedores"] input[name="precio"]').removeAttribute('disabled');
        document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_garantia"]').removeAttribute('disabled');
        document.querySelector('form[id="form-mejores_proveedores"] input[name="garantia"]').removeAttribute('disabled');
        document.querySelector('form[id="form-mejores_proveedores"] select[name="condicion_tiempo_entrega"]').removeAttribute('disabled');
        document.querySelector('form[id="form-mejores_proveedores"] input[name="tiempo_entrega"]').removeAttribute('disabled');
    }
}