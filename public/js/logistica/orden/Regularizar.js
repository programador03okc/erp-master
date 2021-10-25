$('#listaRequerimientosPendientes tbody').on("click", "i.handleClickAbrirModalPorRegularizar", (e) => {
    abrirModalPorRegularizar(e.currentTarget);
});
$('#modal-por-regularizar tbody').on("click", "button.handleClickAbrirModalVerOpcionesParaRegunlarizarItem", (e) => {
    abrirModalVerOpcionesParaRegunlarizarItem(e.currentTarget);
});
$('#modal-opciones-para-regularizar-item tbody').on("click", "button.handleClickRemplazarProductoEnOrden", (e) => {
    remplazarProductoEnOrden(e.currentTarget);
});
$('#modal-opciones-para-regularizar-item tbody').on("click", "button.handleClickLiberarProducto", (e) => {
    liberarProducto(e.currentTarget);
});

function limpiarTabla(idElement){
    let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
    if(nodeTbody!=null){
        while (nodeTbody.children.length > 0) {
            nodeTbody.removeChild(nodeTbody.lastChild);
        }

    }
}

function abrirModalPorRegularizar(obj){
    $('#modal-por-regularizar').modal({
        show: true,
        backdrop: 'static'
    });

    construirTablaItemsPorRegularizar(obj.dataset.idRequerimiento);
}

function construirTablaItemsPorRegularizar(idRequerimiento){
    if(idRequerimiento > 0){
        limpiarTabla('listaItemsPorRegularizar')
        obtenerDataItemsPorRegularlizar(idRequerimiento).then((res) => {
            listarItemsPorRegularizar(res);
        }).catch((err) => {
            console.log(err)
        })

    }
}

function obtenerDataItemsPorRegularlizar(id){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url:`items-por-regularizar/${id}`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function(err) {
            reject(err)
            }
            });
        });
}
function listarItemsPorRegularizar(data){
    if (data.length > 0) {
        (data).forEach(element => {
            // cantidadTotalStockComprometido+= element.stock_comprometido;
            document.querySelector("tbody[id='bodylistaItemsPorRegularizar']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td>${(element.codigo != null && element.codigo != '') ? element.codigo : ''} ${element.producto !=null ?'':'<a href="#" data-id-requerimiento="'+element.id_requerimiento+'" data-codigo-requerimiento="'+element.codigo_requerimiento+'"   class="handleClickMapear" >(SIN MAPEAR)</a>'}</td>
            <td>${element.part_number}</td>
            <td>${element.descripcion}</td>
            <td>${element.cantidad}</td>
            <td>${element.precio_unitario}</td>
            <td>${element.ordenes_compra !=null && element.ordenes_compra.length>0?'SI':'NO'}</td>
            <td>${element.reservas != null && element.reservas.length>0?'SI':'NO'}</td>
            <td>${element.guias_ingreso !=null && element.guias_ingreso.length>0?'SI':'NO'}</td>
            <td>${element.id_producto >0?'<button type="button" class="btn btn-info btn-xs handleClickAbrirModalVerOpcionesParaRegunlarizarItem" name="btnVerOpcionesParaRegularizarItem" title="Ver opciones para regularizar" data-id-detalle-requerimiento="'+element.id_detalle_requerimiento+'" data-part-number="'+element.part_number+'" data-descripcion="'+element.descripcion+'" data-cantidad="'+element.cantidad+'" data-unidad-medida="'+element.unidad_medida.abreviatura+'" data-precio-unitario="'+element.precio_unitario+'"><i class="fas fa-magic fa-sm"></i></button>':'(MAPEO REQUERIDO)'}</td>
            </tr>`);
        });
        // document.querySelector("table[id='listaHistorialReserva'] label[name='totalReservado']").textContent=cantidadTotalStockComprometido;
    } else {
        document.querySelector("tbody[id='bodylistaItemsPorRegularizar']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
        <td colspan="8" style="text-align:center;">(Sin data)</td>

        </tr>`);
    }
}


function abrirModalVerOpcionesParaRegunlarizarItem(obj){
    $('#modal-opciones-para-regularizar-item').modal({
        show: true,
        backdrop: 'static'
    });

    document.querySelector("div[id='modal-opciones-para-regularizar-item'] label[id='partNumber']").textContent =obj.dataset.partNumber;
    document.querySelector("div[id='modal-opciones-para-regularizar-item'] label[id='descripcion']").textContent =obj.dataset.descripcion;
    document.querySelector("div[id='modal-opciones-para-regularizar-item'] label[id='cantidad']").textContent =obj.dataset.cantidad;
    document.querySelector("div[id='modal-opciones-para-regularizar-item'] label[id='unidadMedida']").textContent =obj.dataset.unidadMedida;
    document.querySelector("div[id='modal-opciones-para-regularizar-item'] label[id='precioUnitario']").textContent =obj.dataset.precioUnitario;

    construirTablaOpcionesParaRegularizarItem(obj.dataset.idDetalleRequerimiento);
}

function construirTablaOpcionesParaRegularizarItem(idDetalleRequerimiento){

    if(idDetalleRequerimiento > 0){
        limpiarTabla('listaOrdenesDeItem')
        obtenerDataListaOrdenesConItemPorRegularizar(idDetalleRequerimiento).then((res) => {
            listarOrdenesVinculadasAItemPorRegularizar(res);
        }).catch((err) => {
            console.log(err)
        })

    }
}

function obtenerDataListaOrdenesConItemPorRegularizar(idDetalleRequerimiento){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url:`ordenes-con-item-por-regularizar/${idDetalleRequerimiento}`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function(err) {
            reject(err)
            }
            });
        });
}

function listarOrdenesVinculadasAItemPorRegularizar(data){
    let btnRemplazarProductoEnOrden='';
    let btnLiberarProducto='';
    if (data.length > 0) {
        (data).forEach(element => {
            btnRemplazarProductoEnOrden= `<button type="button" class="btn btn-warning btn-xs handleClickRemplazarProductoEnOrden" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-id-orden="${element.id_orden_compra}" name="btnRemplazarProductoEnOrden" title="Remplazar producto en orden"><i class="fas fa-paint-roller fa-sm"></i></button>`;
            btnLiberarProducto= `<button type="button" class="btn btn-success btn-xs handleClickLiberarProducto" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-id-orden="${element.id_orden_compra}" name="btnLiberarProducto" title="Liberar producto"><i class="fas fa-dove fa-sm"></i></button>`;

            document.querySelector("tbody[id='bodylistaOrdenesDeItem']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td><a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${element.id_orden_compra}" target="_blank" title="Abrir Orden"> ${element.orden.codigo}</a></td>
            <td>${element.producto.codigo}</td>
            <td>${element.producto.part_number}</td>
            <td>${element.producto.descripcion}</td>
            <td>${element.cantidad}</td>
            <td>${element.unidad_medida.abreviatura}</td>
            <td>${element.precio}</td>
            <td>${element.id_producto >0?('<div style="display:flex;">'+btnRemplazarProductoEnOrden+btnLiberarProducto+'</div>'):'(MAPEO REQUERIDO)'}</td>
            </tr>`);
        });
    } else {
        document.querySelector("tbody[id='bodylistaOrdenesDeItem']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
        <td colspan="8" style="text-align:center;">(Sin data)</td>

        </tr>`);
    }

}


function remplazarProductoEnOrden(obj){
    // obj.dataset.idOrden
    if(obj.dataset.idOrden > 0){
        Swal.fire({
            title: 'Esta seguro que desea remplazar el producto del requerimiento en el producto de la orden?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, remplazar'

        }).then((result) => {
            if (result.isConfirmed) {
                realizarRemplazoDeProductoEnOrden(obj.dataset.idOrden,obj.dataset.idDetalleRequerimiento).then((res) => {
                    // console.log(res);
                    if(res.status ==200){
                        Lobibox.notify('success', {
                            title:false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });
                        obj.closest('tr').remove();
                        // console.log(obj.closest('tr'));
                    }else{
                        Lobibox.notify('warning', {
                            title:false,
                            size: 'large',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });
                    }
                }).catch((err) => {
                    console.log(err)
                })
            }
        })


    }else{
        alert("el id de la orden no es un id correcto");
    }
}

function realizarRemplazoDeProductoEnOrden(idOrden,idDetalleRequerimiento){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            url:`realizar-remplazo-de-producto-en-orden`,
            dataType: 'JSON',
            data: {'idOrden':idOrden, 'idDetalleRequerimiento':idDetalleRequerimiento},
            success(response) {
                resolve(response);
            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);

                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar remplazar el producto, por favor vuelva a intentarlo',
                    'error'
                );
            },
            error: function(err) {
                console.log(err);
            reject(err)
            }
            });
        });
}


function liberarProducto(obj){
    // obj.dataset.idOrden
    if(obj.dataset.idOrden > 0){
        Swal.fire({
            title: 'Esta seguro que desea liberar el producto de la orden?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, remplazar'

        }).then((result) => {
            if (result.isConfirmed) {
                realizarLiberacionDeProductoEnOrden(obj.dataset.idOrden,obj.dataset.idDetalleRequerimiento).then((res) => {
                    // console.log(res);
                    if(res.status ==200){
                        Lobibox.notify('success', {
                            title:false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });
                        obj.closest('tr').remove();
                        // console.log(obj.closest('tr'));
                    }else{
                        Lobibox.notify('warning', {
                            title:false,
                            size: 'large',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });
                    }
                }).catch((err) => {
                    console.log(err)
                })
            }
        })


    }else{
        alert("el id de la orden no es un id correcto");
    }
}


function realizarLiberacionDeProductoEnOrden(idOrden,idDetalleRequerimiento){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            url:`realizar-liberacion-de-producto-en-orden`,
            dataType: 'JSON',
            data: {'idOrden':idOrden, 'idDetalleRequerimiento':idDetalleRequerimiento},
            success(response) {
                resolve(response);
            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);

                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar liberar el producto, por favor vuelva a intentarlo',
                    'error'
                );
            },
            error: function(err) {
                console.log(err);
            reject(err)
            }
            });
        });
}