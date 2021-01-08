var 
// rutaRequerimientosPendientes, 
// rutaOrdenesEnProceso,
rutaDetalleRequerimientoOrden,
rutaGuardarOrdenPorRequerimiento
// rutaRevertirOrdenPorRequerimiento,
// rutaActualizarEstadoOrdenPorRequerimiento,
// rutaActualizarEstadoDetalleOrdenPorRequerimiento,
// rutaActualizarEstadoDetalleRequerimiento,
// rutaSedeByEmpresa,
// rutaDocumentosVinculadosOrden,
// rutaListaItemsCuadroCostosPorRequerimiento
;

function inicializarModalOrdenRequerimiento(
    // _rutaRequerimientosPendientes,
    // _rutaOrdenesEnProceso,
    _rutaDetalleRequerimientoOrden,
    _rutaGuardarOrdenPorRequerimiento
    // _rutaRevertirOrdenPorRequerimiento,
    // _rutaActualizarEstadoOrdenPorRequerimiento,
    // _rutaActualizarEstadoDetalleOrdenPorRequerimiento,
    // _rutaActualizarEstadoDetalleRequerimiento,
    // _rutaSedeByEmpresa,
    // _rutaDocumentosVinculadosOrden,
    // _rutaListaItemsCuadroCostosPorRequerimiento
    ) {
    
    // rutaRequerimientosPendientes = _rutaRequerimientosPendientes;
    // rutaOrdenesEnProceso = _rutaOrdenesEnProceso;
    rutaDetalleRequerimientoOrden = _rutaDetalleRequerimientoOrden;
    rutaGuardarOrdenPorRequerimiento = _rutaGuardarOrdenPorRequerimiento;
    // rutaRevertirOrdenPorRequerimiento = _rutaRevertirOrdenPorRequerimiento;
    // rutaActualizarEstadoOrdenPorRequerimiento = _rutaActualizarEstadoOrdenPorRequerimiento;
    // rutaActualizarEstadoDetalleOrdenPorRequerimiento = _rutaActualizarEstadoDetalleOrdenPorRequerimiento;
    // rutaActualizarEstadoDetalleRequerimiento = _rutaActualizarEstadoDetalleRequerimiento;
    // rutaSedeByEmpresa = _rutaSedeByEmpresa;
    // rutaDocumentosVinculadosOrden = _rutaDocumentosVinculadosOrden;
    // rutaListaItemsCuadroCostosPorRequerimiento = _rutaListaItemsCuadroCostosPorRequerimiento;

}
 

function limpiarTabla(idElement){
    // console.log("limpiando tabla....");
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--)
    {
        table.deleteRow(i);
    }
    return null;
}

function obtenerRequerimiento(reqTrueList){
    limpiarTabla('listaDetalleOrden');

    // console.log(reqTrueList);
    detalleRequerimientoSelected=[];
        $.ajax({
            type: 'POST',
            url: rutaDetalleRequerimientoOrden,
            data:{'requerimientoList':reqTrueList},
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                // detalleRequerimientoSelected=response.det_req;
                response.det_req.forEach(element => {
                    if(element.cantidad !=0){
                        detalleRequerimientoSelected.push(element);
                    }
                });
                // console.log(detalleRequerimientoSelected);
                if(detalleRequerimientoSelected.length ==0){
                    alert("No puede generar una orden sin antes agregar item(s) base");
                    $('#modal-orden-requerimiento').modal('hide');

                }else{
                    listar_detalle_orden_requerimiento(detalleRequerimientoSelected);
                }
                // console.log(response.det_req); 
                // document.querySelector("div[id='modal-orden-requerimiento'] span[id='codigo_requeriento_seleccionado']").textContent= ' - Requerimiento: '+ response.requerimiento.codigo;
                // document.querySelector("div[id='modal-orden-requerimiento'] input[name='id_requerimiento']").value= response.requerimiento[0].id_requerimiento;
                // document.querySelector("div[id='modal-orden-requerimiento'] select[name='sede']").value= response.requerimiento.id_sede;
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
}


function updateInObjCantidad(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].cantidad = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].cantidad = valor;
                
            }
        });
    }

}

function updateInObjPrecioReferencial(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].precio_referencial = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            console.log(element.id);
            console.log(rowNumber);
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].precio_referencial = valor;
                
            }
        });
    }

}

function updateInObjCantidadAComprar(rowNumber, idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].cantidad_a_comprar = valor;
                }
            }
        });
    }

    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].cantidad_a_comprar = valor;
                
            }
        });
    }
    // console.log(detalleRequerimientoSelected);
}

function updateInObjStockComprometido(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].stock_comprometido = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].stock_comprometido = valor;
                
            }
        });
    }
}

function updateInObjSubtotal(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].subtotal = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].subtotal = valor;
                
            }
        });
    }
}

function updateInputPrecio(event){
    // console.log(detalleRequerimientoSelected);
    let nuevoValor =event.target.value;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
    let rowNumber = event.target.dataset.row;
    updateInObjPrecioReferencial(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);

    calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumber);

    // console.log(detalleRequerimientoSelected);
}

function calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumberSelected){
    let sizeInputTotal = document.querySelectorAll("div[name='subtotal']").length;
    for (let index = 0; index < sizeInputTotal; index++) {
        let rowNumber = document.querySelectorAll("div[name='subtotal']")[index].dataset.row;
        let idReq = document.querySelectorAll("div[name='subtotal']")[index].dataset.id_requerimiento;
        if(rowNumber == rowNumberSelected){
            let precio = document.querySelectorAll("input[name='precio']")[index].value?document.querySelectorAll("input[name='precio']")[index].value:0;
            let cantidad =( document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value)>0?document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value:document.querySelectorAll("input[name='cantidad']")[index].value;
            let subtotal = (parseFloat(precio) * parseFloat(cantidad)).toFixed(2);
            document.querySelectorAll("div[name='subtotal']")[index].textContent=subtotal;
            updateInObjSubtotal(rowNumberSelected,idReq,idDetalleRequerimientoSelected,subtotal);
        }
    }
    let total =0;
    let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;
    for (let index = 0; index < sizeInputTotal; index++) {
        let num = document.querySelectorAll("div[name='subtotal']")[index].textContent?document.querySelectorAll("div[name='subtotal']")[index].textContent:0;
        total += parseFloat(num);
    }
    // console.log(total);
    document.querySelector("var[name='total']").textContent= (simbolo_moneda_selected) + (total);


    
}

function updateInputCantidad(event){
    // console.log(detalleRequerimientoSelected);
    let nuevoValor =event.target.value;
    let rowNumber= event.target.dataset.row;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
 
    updateInObjCantidad(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);
    calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumber);

    // console.log(detalleRequerimientoSelected);
}
function updateInputCantidadAComprar(event){
    let nuevoValor =event.target.value;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
    let rowNumberSelected = event.target.dataset.row;
    let sizeInputCantidad = document.querySelectorAll("input[name='cantidad']").length;
    let cantidad =0;
    for (let index = 0; index < sizeInputCantidad; index++) {
        let row = document.querySelectorAll("input[name='cantidad']")[index].dataset.row;
        if(row == rowNumberSelected){
            cantidad = document.querySelectorAll("input[name='cantidad']")[index].value;
            if(parseFloat(nuevoValor) <= parseFloat(cantidad)){                
                // actualizar datadetreq cantidad
                updateInObjCantidadAComprar(rowNumberSelected,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);
                calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumberSelected);

                // console.log(detalleRequerimientoSelected);
                // 
            }
            
            if(parseFloat(nuevoValor) > parseFloat(cantidad)){
                console.log(nuevoValor);
                console.log(cantidad);
                alert("La cantidad a comprar no puede ser mayor a la cantidad `solicitada");
                document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value= cantidad;

            }
        }
    }
}

function listar_detalle_orden_requerimiento(data){
    // console.log(data);

    var vardataTables = funcDatatables();
    $('#listaDetalleOrden').DataTable({
        bDestroy: true,
        order: [[0, 'asc']],
        info:     false,
        scrollY: '30vh',
        scrollCollapse: true,
        paging:   false,
        searching: false,
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        data:data,
        columns: [
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="checkbox" checked data-id-detalle-requerimiento="' + row.id_detalle_requerimiento + '"  disabled />';
                    }else{
                        return '<input type="checkbox" checked data-id-detalle-requerimiento="' + row.id_detalle_requerimiento + '"  />';

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
                    return row.descripcion_adicional;
                }, 'name':'descripcion_adicional'
            },
            {'render':
                function (data, type, row, meta){
                    return row.unidad_medida;
                }, 'name':'unidad_medida'
            },            
            {'render':
                function (data, type, row, meta){
                    if(row.estado ==7){
                        return '<input type="text" class="form-control" name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+row.cantidad+'" onChange="updateInputCantidad(event);" style="width: 70px;" disabled/>';
                    }else{
                        return '<input type="text" class="form-control" name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+row.cantidad+'" onChange="updateInputCantidad(event);" style="width: 70px;"/>';
                    }
                }, 'name':'cantidad'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado ==7){
                        return '<input type="text" class="form-control" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_referencial?row.precio_referencial:"")+'" onChange="updateInputPrecio(event);" style="width:70px;" disabled/>';
                    }else{
                        return '<input type="text" class="form-control" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_referencial?row.precio_referencial:"")+'" onChange="updateInputPrecio(event);" style="width:70px;"/>';
                    }
                } , 'name':'precio'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="updateInputStockComprometido(event);" onfocusin ="updateInputStockComprometido(event);" style="width: 70px;" disabled />';
                    }else{
                        return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="updateInputStockComprometido(event);" onfocusin ="updateInputStockComprometido(event);" style="width: 70px;"/>';
                    }
                }, 'name':'stock_comprometido'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="text" class="form-control" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="updateInputCantidadAComprar(event);" value="'+row.cantidad_a_comprar+'" style="width:70px;" disabled />';
                    }else{
                        return '<input type="text" class="form-control" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="updateInputCantidadAComprar(event);" value="'+row.cantidad_a_comprar+'" style="width:70px;"/>';
                    }
                } , 'name':'cantidad_a_comprar'
            },
            {'render':
                function (data, type, row, meta){
                    return '<div name="subtotal" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"></div>';
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
                            <button type="button" class="btn btn-danger btn-sm" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" data-row="${(meta.row+1)}" data-id_requerimiento="${(row.id_requerimiento?row.id_requerimiento:0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)}"  onclick="openModalEliminarItemOrden(this);">
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
        columnDefs: [
            { width: '10px', targets: 0 },
            { width: '20px', targets: 1 },
            { width: '20px', targets: 2 },
            { width: '40px', targets: 3 },
            { width: '50px', targets: 4 },
            { width: '20px', targets: 5 },
            { width: '20px', targets: 6 },
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

function openModalEliminarItemOrden(obj){
    let codigoItemSelected=obj.parentNode.parentNode.parentNode.childNodes[2].textContent;
    let descripcionItemSelected=obj.parentNode.parentNode.parentNode.childNodes[3].textContent;
    let rowNumber = obj.dataset.row;
    let idRequerimientoSelected = obj.dataset.id_requerimiento;
    let idDetalleRequerimiento = obj.dataset.id_detalle_requerimiento

    if(idDetalleRequerimiento > 0){
        $('#modal-confirmar-eliminar-item').modal({
            show: true,
            backdrop: 'true',
            keyboard: true
    
        });
        document.querySelector("span[id='codigo_descripcion_item']").textContent= `${codigoItemSelected} ${descripcionItemSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='codigo_item']").textContent= `${codigoItemSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='descripcion_item']").textContent= `${descripcionItemSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='row']").textContent= `${rowNumber}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='id_requerimiento']").textContent= `${idRequerimientoSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='id_detalle_requerimiento']").textContent= `${idDetalleRequerimiento}`;
    
    }else{
        eliminarItemSinMotivo(obj);
    }
    console.log(detalleRequerimientoSelected);


}


function eliminarItemSinMotivo(obj){
    // let codigoItemSelected=obj.parentNode.parentNode.parentNode.childNodes[2].textContent;
    // let descripcionItemSelected=obj.parentNode.parentNode.parentNode.childNodes[3].textContent;
    let rowNumber = obj.dataset.row;
    // let idRequerimientoSelected = obj.dataset.id_requerimiento;
    let idDetalleRequerimiento = obj.dataset.id_detalle_requerimiento
    eliminarItemDeObj(rowNumber,idDetalleRequerimiento);
    afectarEstadoEliminadoFilaTablaListaDetalleOrden(rowNumber,'Error de Ingreso');
}

function eliminarItemDeObj(rowSelected,idDetalleRequerimientoSelected){

    if(idDetalleRequerimientoSelected >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_detalle_requerimiento == idDetalleRequerimientoSelected){
            detalleRequerimientoSelected.splice( index, 1 );
            }
        });
    }else if(rowSelected >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowSelected){
                detalleRequerimientoSelected.splice( index, 1 );
            }
        });
    }
    console.log(detalleRequerimientoSelected);

}

function afectarEstadoEliminadoFilaTablaListaDetalleOrden(rowSelected,motivo){
    let sizeTableListaDetalleOrden = document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children.length;
    for (let index = 0; index < sizeTableListaDetalleOrden; index++) {
        let row = document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].children[0].dataset.row;
        if(row ==rowSelected){
            $('#modal-confirmar-eliminar-item').modal('hide');
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[0].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[5].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[6].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[7].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[8].children[0].disabled = true;

            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].children[0].remove();
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].innerHTML=`
            <div class="btn-group btn-group-sm" role="group" style="cursor: default;">
                <i class="fas fa-sticky-note fa-2x" style="color:orange" title="${(motivo?motivo:'Sin Observación')}" ></i>
            </div>
            `;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].setAttribute("style","background:mistyrose; color:indianred;");
        }
        
    }
}



function validaOrdenRequerimiento(){
    var codigo_orden = $('[name=codigo_orden]').val();
    var id_proveedor = $('[name=id_proveedor]').val();
    var msj = '';
    if (codigo_orden == ''){
        msj+='\n Es necesario que ingrese un código de orden Softlink';
    }
    if (id_proveedor == ''){
        msj+='\n Es necesario que seleccione un Proveedor';
    }
    return  msj;
}


function hasNullCantidadAComprar(){
    let sizeInputCantidadAComprar = document.querySelectorAll("input[name='cantidad_a_comprar']").length;
    let cantidadVacios=0;
    let hasNull=false;
    for (let index = 0; index < sizeInputCantidadAComprar; index++) {
        let inputCantidadAComprar = document.querySelectorAll("input[name='cantidad_a_comprar']")[index];
        if(inputCantidadAComprar.value == '0' || inputCantidadAComprar.value == '' || inputCantidadAComprar == null){
            cantidadVacios+=1;
        }
        // console.log(inputCantidadAComprar);
    }

    if(cantidadVacios >0){
        hasNull=true;
    }

    return hasNull;
}

function countRequirementsInObj(){
    // console.log(listCheckReq);
    let idRequerimientoList=[];
    let size=0;
    listCheckReq.forEach(element => {
        if(element.stateCheck ==true){
            idRequerimientoList.push(element.id_req);
        } 
    });
    let idRequerimientoListUnique = Array.from(new Set(idRequerimientoList));
    // console.log(idRequerimientoList);
    // console.log(idRequerimientoListUnique);
    size = idRequerimientoListUnique.length;
    return size;
}

function hasCheckedGuardarEnRequerimiento(){
    let hasCheck = document.querySelector("input[name='guardarEnRequerimiento']").checked;
    return hasCheck;
}

function get_header_orden_requerimiento(){
    let id_tipo_doc = document.querySelector("div[id='modal-orden-requerimiento'] select[name='id_tipo_doc']").value;
    let id_condicion = document.querySelector("div[id='modal-orden-requerimiento'] select[name='id_condicion']").value;
    let plazo_dias = document.querySelector("div[id='modal-orden-requerimiento'] input[name='plazo_dias']").value;
    let plazo_entrega = document.querySelector("div[id='modal-orden-requerimiento'] input[name='plazo_entrega']").value;
    let id_moneda = document.querySelector("div[id='modal-orden-requerimiento'] select[name='id_moneda']").value;
    let id_tp_documento = document.querySelector("div[id='modal-orden-requerimiento'] select[name='id_tp_documento']").value;
    let codigo_orden = document.querySelector("div[id='modal-orden-requerimiento'] input[name='codigo_orden']").value;
    let sede = document.querySelector("div[id='modal-orden-requerimiento'] select[name='sede']").value;
    let id_proveedor = document.querySelector("div[id='modal-orden-requerimiento'] input[name='id_proveedor']").value;
    let id_contrib = document.querySelector("div[id='modal-orden-requerimiento'] input[name='id_contrib']").value;

    let data = {
        'id_tipo_doc':id_tipo_doc, 
        'id_condicion':id_condicion, 
        'plazo_dias':plazo_dias, 
        'plazo_entrega':plazo_entrega, 
        'id_moneda':id_moneda, 
        'id_tp_documento':id_tp_documento, 
        'codigo_orden':codigo_orden, 
        'sede':sede, 
        'id_proveedor':id_proveedor, 
        'id_contrib':id_contrib,
        'detalle':[]
    }
    
    return data;
}

function llenarTablaListaRequerimientosVinculados(data){
    var vardataTables1 = funcDatatables();
    $('#listaRequerimientosVinculados').DataTable({
        'paging':false,
        'info': false,
        'language' : vardataTables1[0],
        'processing': true,
        'searching':false,
        "bDestroy": true,
        "data" :  data,
        'columns': [
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.codigo;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.concepto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.tipo_requerimiento;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.nombre_persona?row.nombre_persona:row.cliente_razon_social;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.razon_social_empresa;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.codigo_sede_empresa;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.usuario;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.fecha_requerimiento;
                }
            }
 
        ],
        'order': [
            [0, 'asc']
        ]

    });

    let tablelistaitem = document.getElementById('listaRequerimientosVinculados_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function makeLinktoReq(obj){

    let id_requerimiento = obj.value;
    let id_nuevo_item = obj.parentNode.parentNode.children[0].textContent;
    let linksToReqObj={
        'id_requerimiento':id_requerimiento,
        'id_nuevo_item':id_nuevo_item
    }
    linksToReqObjArray.push(linksToReqObj);
    // console.log(detalleRequerimientoSelected);
    // console.log(linksToReqObjArray);
}

function buildSelectOfRequirements(data){
    let html=`<select class="form-control" name="requerimientos" onChange="makeLinktoReq(this);">
                <option value="0">selecciona un requerimiento</option>`;
    data.forEach(element => {
        html +=`
            <option value="${element.id_requerimiento}">${element.codigo}</option>`;
    });
    html+='</select>';
    selectRequirementsToLink = html;
}

function fillListaItemsRequerimientosVinculados(){
    // console.log(listCheckReq);
    let data=detalleRequerimientoSelected.filter(item => item.id > 0)
    console.log(data);
    var vardataTables1 = funcDatatables();
    $('#listaItemsRequerimientosVinculados').dataTable({
 
        'paging':true,
        'info': true,
        'iDisplayLength': 10,
        'language' : vardataTables1[0],
        'processing': true,
        'searching':false,
        "bDestroy": true,
        'data':data,
        'columns': [
            {'render':
                function (data, type, row, meta){
                    return row.id;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.codigo_item;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.part_number;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.unidad_medida;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.cantidad_a_comprar;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.precio_referencial;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return selectRequirementsToLink;
                }
            }
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'}
        ],
        'order': [
            [0, 'asc']
        ]

    });

    let tablelistaitem = document.getElementById('listaItemsRequerimientosVinculados_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;

}

function getDetalleYRequerimientoOrden(idReqList){

    return new Promise(function(resolve, reject) {
    $.ajax({
        type: 'POST',
        data:{'requerimientoList':idReqList},
        url:rutaDetalleRequerimientoOrden,
        dataType: 'JSON',
        success(response) {
            resolve(response) // Resolve promise and go to then() 
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}

function fillListaRequerimientosVinculados(){

    let ReqListOnlyTrue=listCheckReq.filter(field => field.stateCheck == true);
    let idReqList = ReqListOnlyTrue.map(function (element, index, array) {
        return element.id_req; 
    });
    // console.log(idReqList);
    getDetalleYRequerimientoOrden(idReqList).then(function(data) {
        // Run this when your request was successful
        console.log(data)
        if(data.requerimiento.length >0){
            llenarTablaListaRequerimientosVinculados(data.requerimiento);
            buildSelectOfRequirements(data.requerimiento);
            fillListaItemsRequerimientosVinculados();
        }

    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })

}

$("#form-modal-orden-requerimiento").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    var detalle_requerimiento = [];
    payload_orden = data;
    // console.log(detalleRequerimientoSelected);
    let hasCheck = hasCheckedGuardarEnRequerimiento();
    if(hasCheck == true){
        let coutReqInObj =countRequirementsInObj();
        if(coutReqInObj == 1){
            // console.log(listCheckReq);
            // console.log(detalleRequerimientoSelected);
            // vincultar item con req unico
            let id_req = listCheckReq[0].id_req;
            detalleRequerimientoSelected.forEach(drs => {
                if(drs.id>0){
                    drs.id_requerimiento= id_req;
                }
            });

            payload_orden =get_header_orden_requerimiento();
            payload_orden.detalle= detalleRequerimientoSelected;
            // payload_orden += '&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
            sendDataToSaveOrden(payload_orden);

        }else if(coutReqInObj >1){
            // console.log('open modal to select item/req');
            $('#modal-vincular-item-requerimiento').modal({
                show: true,
                backdrop: 'static'
            });
            fillListaRequerimientosVinculados();

            
        }else{ //no existen nuevos item argregados, guardar nromal (no habra que guardar en req)
            payload_orden =get_header_orden_requerimiento();
            payload_orden.detalle= detalleRequerimientoSelected;
            sendDataToSaveOrden(payload_orden);
    
        }
    }else{ // sin guardar en req
        payload_orden =get_header_orden_requerimiento();
        payload_orden.detalle= detalleRequerimientoSelected;
    sendDataToSaveOrden(payload_orden);
    }

    // let itemsChecked=[];
    // itemsChecked = agregarItemsWithChecked();
    // console.log('detalleRequerimientoSelected');
    // console.log(detalleRequerimientoSelected);
    // console.log('itemsChecked');
    // console.log(itemsChecked);
    // if(itemsChecked.length > 0){
    //     detalleRequerimientoSelected.forEach(elementDetReq => {
    //         itemsChecked.forEach(elementChecked => {
    //             if(elementDetReq.id_detalle_requerimiento == elementChecked.id_detalle_requerimiento){
    //                 detalle_requerimiento.push(elementDetReq);
    //             }
    //         })
    //     });
    // }else{
    //     detalle_requerimiento= detalleRequerimientoSelected;
    // }

    // var payload = data+'&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
  
    // console.log(detalleRequerimientoSelected);




});

function sendDataToSaveOrden(payload){
    // console.log(payload);
    let hasNull = hasNullCantidadAComprar();

    if(hasNull ==true){
        var ask = confirm('Tiene seleccionado producto(s) con cantidad a comprar igual a cero/vacio. ¿desea continuar?');
        if (ask == true){
        guardar_orden_requerimiento(payload);
    payload_orden =[];
    }else{
            return false;
        } 
    }else{
        guardar_orden_requerimiento(payload);
    }
}


function guardar_orden_requerimiento(data){
    console.log(data);
    
    var msj = validaOrdenRequerimiento();
    if (msj.length > 0){
        alert(msj);
    } else{
        $.ajax({
            type: 'POST',
            url: rutaGuardarOrdenPorRequerimiento,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Orden de registrada con éxito');
                    $('#modal-orden-requerimiento').modal('hide');
                    $('#listaRequerimientosPendientes').DataTable().ajax.reload();
                    // $('#listaComprasEnProceso').DataTable().ajax.reload();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
