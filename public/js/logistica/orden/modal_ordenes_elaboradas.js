var detalleOrdenList=[];
function ordenesElaboradasModal(){
    $('#modal-ordenes-elaboradas').modal({
        show: true,
        backdrop: 'true'
    });
    listarOrdenesElaboradas();
    
}

function listarOrdenesElaboradas(){
        var vardataTables = funcDatatables();
        var tabla = $('#listaOrdenesElaboradas').DataTable({
            'processing':true,
            'destroy':true,
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'ajax': 'listar-ordenes-elaboradas',
            // "dataSrc":'',
            'scrollX': false,
            'columns': [
                {'data': 'id_orden_compra'},
                {'data': 'fecha'},
                {'data': 'codigo'},
                {'data': 'nro_documento'},
                {'data': 'razon_social'},
                {'data': 'moneda_simbolo'},
                {'data': 'condicion'},
                {'data': 'plazo_entrega'},
                {'data': 'codigo_sede_empresa'},
                {'data': 'direccion_destino'},
                {'data': 'ubigeo_destino'},
                {'data': 'estado_doc'}
                
            ],
            'columnDefs': [{ className: "text-right", 'aTargets': [0], 'sClass': 'invisible'}]
        });
    
}

$('#listaOrdenesElaboradas tbody').on('click', 'tr', function(){
    if ($(this).hasClass('eventClick')){
        $(this).removeClass('eventClick');
    } else {
        $('#listaOrdenesElaboradas').dataTable().$('tr.eventClick').removeClass('eventClick');
        $(this).addClass('eventClick');
    }
    var idTr = $(this)[0].firstChild.innerHTML;
    $('.modal-footer #id_orden').text(idTr);
    
});

function selectOrden(){
    let idOrdenSelected= document.querySelector("div[id='modal-ordenes-elaboradas'] div[class='modal-footer'] label[id='id_orden']").textContent;
    mostrarOrden(idOrdenSelected);
    changeStateInput('crear-orden-requerimiento', true);

    $('#modal-ordenes-elaboradas').modal('hide');
}

function mostrarOrden(id){
    $.ajax({
        type: 'GET',
        url: 'mostrar-orden/'+id,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            loadHeadOrden(response.head);
            loadDetailOrden(response.detalle);
            detalleOrdenList= response.detalle;
            
            
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function fechaHoy(){
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value = now.toISOString().slice(0, -1);
};

function loadHeadOrden(data){

    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_orden']").value=data.id_orden_compra?data.id_orden_compra:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_documento']").value=data.id_tp_documento?data.id_tp_documento:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_moneda']").value=data.id_moneda?data.id_moneda:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent=data.codigo_orden?data.codigo_orden:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='codigo_orden']").value=data.codigo_softlink?data.codigo_softlink:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value=data.fecha?data.fecha.replace(" ","T"):'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_sede']").value=data.id_sede?data.id_sede:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] img[id='logo_empresa']").setAttribute("src",data.logo_empresa);
    
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value=data.id_proveedor?data.id_proveedor:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value=data.id_contribuyente?data.id_contribuyente:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='razon_social']").value=data.razon_social?data.razon_social:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value=data.direccion_fiscal?data.direccion_fiscal:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value=data.ubigeo?data.ubigeo:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value=data.ubigeo_proveedor?data.ubigeo_proveedor:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value=data.id_contacto?data.id_contacto:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value=data.nombre_contacto?data.nombre_contacto:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value=data.telefono_contacto?data.telefono_contacto:'';
    
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_condicion']").value=data.id_condicion?data.id_condicion:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_dias']").value=data.plazo_dias?data.plazo_dias:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_entrega']").value=data.plazo_entrega?data.plazo_entrega:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='cdc_req']").value=data.codigo_cc?data.codigo_cc:data.codigo_requerimiento;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ejecutivo_responsable']").value=data.nombre_responsable_cc?data.nombre_responsable_cc:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_doc']").value=data.id_tp_doc?data.id_tp_doc:'';

    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_destino']").value=data.direccion_destino?data.direccion_destino:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value=data.ubigeo_destino_id?data.ubigeo_destino_id:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_destino']").value=data.ubigeo_destino?data.ubigeo_destino:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_trabajador']").value=data.personal_autorizado?data.personal_autorizado:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado']").value=data.nombre_personal_autorizado?data.nombre_personal_autorizado:'';
}

function loadDetailOrden(data){
    var hasAttrDisabled ='';
    if(document.querySelector("button[id='btnEditar']").hasAttribute('disabled')== false){
        hasAttrDisabled ='disabled';
    }else{
        hasAttrDisabled = '';
    }

    var vardataTables = funcDatatables();
    $('#listaDetalleOrden').DataTable({
        bDestroy: true,
        order: [[0, 'asc']],
        info:     false,
        scrollCollapse: true,
        paging:   false,
        searching: false,
        language: vardataTables[0],
        processing: true,
        responsive: true,
        bDestroy: true,
        data:data,
        columns: [
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="checkbox" checked data-id-detalle-requerimiento="' + row.id_detalle_requerimiento + '"  disabled />';
                    }else{
                        return '<input type="checkbox" checked data-id-detalle-requerimiento="' + row.id_detalle_requerimiento + '" '+hasAttrDisabled+' />';

                    }
                }, 'name':'checkbox'
            },
            {'render':
                function (data, type, row, meta){
                    return row.codigo_requerimiento;
                }, 'name':'codigo_requerimiento'
            },
            {'render':
                function (data, type, row, meta){
                    return row.codigo_item;
                }, 'name':'codigo_item'
            },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion_producto;
                }, 'name':'descripcion_producto'
            },
            {'render':
                function (data, type, row, meta){
                    return row.unidad_medida;
                }, 'name':'unidad_medida'
            },            
            {'render':
                function (data, type, row, meta){
                    return '<span name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+row.cantidad+'</span>';
                
                }, 'name':'cantidad'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado ==7){
                        return '<input type="text" class="form-control activation" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="updateDetalleOrdenListPrecio(event);" style="width:70px;" disabled/>';
                    }else{
                        return '<input type="text" class="form-control activation" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="updateDetalleOrdenListPrecio(event);" style="width:70px;" '+hasAttrDisabled+'/>';
                    }
                } , 'name':'precio'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="updateInputStockComprometido(event);" onfocusin ="updateInputStockComprometido(event);" style="width: 70px;" disabled />';
                    }else{
                        return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="updateInputStockComprometido(event);" onfocusin ="updateInputStockComprometido(event);" style="width: 70px;" '+hasAttrDisabled+'/>';
                    }
                }, 'name':'stock_comprometido'
            },
            {'render':
                function (data, type, row, meta){

                    if(row.estado == 7){
                        return '<input type="text" class="form-control activation" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="updateDetalleOrdenListCantidadAComprar(event);" value="'+(row.cantidad?row.cantidad:'')+'" style="width:70px;" disabled />';
                    }else{
                        // updateInObjCantidadAComprar((meta.row+1),(row.id_requerimiento),(row.id_detalle_requerimiento),(row.cantidad));

                        return '<input type="text" class="form-control activation" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="updateDetalleOrdenListCantidadAComprar(event);" value="'+(row.cantidad_a_comprar?row.cantidad_a_comprar:'')+'" style="width:70px;"'+hasAttrDisabled+'/>';
                    }
                } , 'name':'cantidad_a_comprar'
            },
            {'render':
                function (data, type, row, meta){
                    return '<div name="subtotal" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+(row.subtotal?((parseFloat(row.subtotal).toFixed(2))):'')+'</div>';
                } , 'name':'subtotal'
            },
            {'render':
                function (data, type, row, meta){
                    let action ='';

                    if(row.estado ==7){
                        action = `
                        <div class="btn-group btn-group-sm" role="group" style="cursor: default;">
                            <i class="fas fa-sticky-note fa-2x" style="color:orange" title="${(row.observacion?row.observacion:'Sin Observación')}" ></i>
                        </div>
                        `;
                    }else{
                        action = `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-danger btn-sm activation" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" data-row="${(meta.row+1)}" data-id_requerimiento="${(row.id_requerimiento?row.id_requerimiento:0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)}"  onclick="openModalEliminarItemOrden(this);" ${hasAttrDisabled}>
                            <i class="fas fa-trash fa-sm"></i>
                            </button>
                        </div>
                        `;
                    }
                    return action;
                }
            }
        ],
        rowCallback: function( row, data ) {
            
            if ( data.estado == '7' )
            { 
                $('td', row).css({'background-color': 'mistyrose', 'color': 'indianred'});
            }
        },
        "initComplete": function(settings, json) {
            calcTotalOrdenDetalleList();

        },
        columnDefs: [
            { width: '10px', targets: 0 },
            { width: '20px', targets: 1 },
            { width: '20px', targets: 2 },
            { width: '40px', targets: 3 },
            { width: '50px', targets: 4 },
            { width: '20px', targets: 5 ,sClass: 'invisible'},
            { width: '15px', targets: 6 },
            { width: '20px', targets: 7 , sClass: 'invisible'},
            { width: '20px', targets: 8 },
            { width: '20px', targets: 9 },
            { width: '30px', targets: 10, sClass:'text-center' }
        ],
    
        order: [[1, "asc"]]


    });

    let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function updateDetalleOrdenListPrecio(event){

    let nuevoValor =event.target.value;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
    let rowNumberSelected = event.target.dataset.row;
 
    if(parseFloat(nuevoValor) >0){                
        if(idRequerimientoSelected >0 && idDetalleRequerimientoSelected >0){
            detalleOrdenList.forEach((element,index) => {
                if(element.id_requerimiento == idRequerimientoSelected){
                    if(element.id_detalle_requerimiento == idDetalleRequerimientoSelected){
                        detalleOrdenList[index].precio_unitario = nuevoValor;
                    }
                }
            });
        }
        calcTotalDetalleOrden(idDetalleRequerimientoSelected,rowNumberSelected);
    }
}

function updateDetalleOrdenListCantidadAComprar(event){
    // console.log(detalleOrdenList);

    let nuevoValor =event.target.value;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
    let rowNumberSelected = event.target.dataset.row;
 
    if(parseFloat(nuevoValor) >0){                
        if(idRequerimientoSelected >0 && idDetalleRequerimientoSelected >0){
            detalleOrdenList.forEach((element,index) => {
                if(element.id_requerimiento == idRequerimientoSelected){
                    if(element.id_detalle_requerimiento == idDetalleRequerimientoSelected){
                        detalleOrdenList[index].cantidad_a_comprar = nuevoValor;
                    }
                }
            });
        }
        calcTotalDetalleOrden(idDetalleRequerimientoSelected,rowNumberSelected);
    }
}


function calcTotalDetalleOrden(idDetalleRequerimientoSelected,rowNumberSelected){
    let sizeInputTotal = document.querySelectorAll("div[name='subtotal']").length;
    for (let index = 0; index < sizeInputTotal; index++) {
        let rowNumber = document.querySelectorAll("div[name='subtotal']")[index].dataset.row;
        let idReq = document.querySelectorAll("div[name='subtotal']")[index].dataset.id_requerimiento;
        if(rowNumber == rowNumberSelected){
            let precio = document.querySelectorAll("input[name='precio']")[index].value?document.querySelectorAll("input[name='precio']")[index].value:0;
            let cantidad =document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value;
            let subtotal = (parseFloat(precio) * parseFloat(cantidad)).toFixed(2);
            document.querySelectorAll("div[name='subtotal']")[index].textContent=subtotal;
            if(rowNumberSelected >0 && idDetalleRequerimientoSelected >0){
                detalleOrdenList.forEach((element,index) => {
                    if(element.id_requerimiento == idReq){
                        if(element.id_detalle_requerimiento == idDetalleRequerimientoSelected){
                            detalleOrdenList[index].subtotal = subtotal;
                        }
                    }
                });
            }
        }
    }
    
    calcTotalOrdenDetalleList();

}

function calcTotalOrdenDetalleList(){
    let sizeInputTotal = document.querySelectorAll("div[name='subtotal']").length;
    let total =0;
    let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;
    for (let index = 0; index < sizeInputTotal; index++) {
        let num = document.querySelectorAll("div[name='subtotal']")[index].textContent?document.querySelectorAll("div[name='subtotal']")[index].textContent:0;
        total += parseFloat(num);
    }
    document.querySelector("var[name='total']").textContent= (simbolo_moneda_selected) + (total);

}