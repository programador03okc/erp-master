var vardataTables = funcDatatables();

var trPorRegularizarSeleccionar;
var $listaItemsPorRegularizar;
var iTableCounter = 1;
var oInnerTable;
$('#listaRequerimientosPendientes tbody').on("click", "i.handleClickAbrirModalPorRegularizar", (e) => {
    abrirModalPorRegularizar(e.currentTarget);
});

$('#modal-por-regularizar').on("click", "button.handleClickRemplazarProductoComprometidoEnTodaOrden", (e) => {
    remplazarProductoComprometidoEnTodaOrden(e.currentTarget);
});
$('#modal-por-regularizar').on("click", "button.handleClickLiberarProductoComprometidoEnTodaOrden", (e) => {
    liberarProductoComprometidoEnTodaOrden(e.currentTarget);
});
$('#modal-por-regularizar').on("click", "button.handleClickAnularItemComprometidoEnTodaOrdenYReservas", (e) => {
    anularItemComprometidoEnTodaOrdenYReserva(e.currentTarget);
});


// $('#modal-por-regularizar').on("click", "button.handleClickLevantarRegularizacion", (e) => {
//     levantarRegularizacion(e.currentTarget);
// });

$('#modal-por-regularizar').on("click", "button.handleClickDesplegarVerDetalleOrdenReserva", (e) => {
    // var data = $('#listaRequerimientosPendientes').DataTable().row($(this).parents("tr")).data();
    this.desplegarVerDetalleOrdenReserva(e.currentTarget);
});


function limpiarTabla(idElement) {
    let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
    if (nodeTbody != null) {
        while (nodeTbody.children.length > 0) {
            nodeTbody.removeChild(nodeTbody.lastChild);
        }

    }
}

function abrirModalPorRegularizar(obj) {
    $('#modal-por-regularizar').modal({
        show: true,
        backdrop: 'static'
    });
    document.querySelector("div[id='modal-por-regularizar'] input[name='idRequerimiento']").value = obj.dataset.idRequerimiento;
    trPorRegularizarSeleccionar = obj.closest('tr');
    construirTablaPorRegularizar(obj.dataset.idRequerimiento);
}

function construirTablaPorRegularizar(idRequerimiento) {
    if (idRequerimiento > 0) {
        limpiarTabla('listaItemsPorRegularizar')
        obtenerDataPorRegularlizar(idRequerimiento).then((res) => {
            document.querySelector("div[id='modal-por-regularizar'] span[id='codigo_requerimiento']").textContent = res.codigo_requerimiento ?? '';
            document.querySelector("div[id='modal-por-regularizar'] span[id='codigo_cuadro_presupuesto']").textContent = res.codigo_cuadro_presupuesto ?? '';
            listarItemsPorRegularizar(idRequerimiento);
        }).catch((err) => {
            console.log(err)
        })

    }
}

function obtenerDataPorRegularlizar(id) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `por-regularizar-cabecera/${id}`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function (err) {
                reject(err)
            }
        });
    });
}



function listarItemsPorRegularizar(idRequerimiento) {

    let that = this;

    $listaItemsPorRegularizar = $('#listaItemsPorRegularizar').DataTable({
        'dom': vardataTables[1],
        'buttons': [],
        'language': vardataTables[0],
        'order': [[10, 'asc']],
        'bLengthChange': false,
        // 'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'por-regularizar-detalle/'+idRequerimiento,
            'type': 'GET',
            beforeSend: data => {

                $("#listaItemsPorRegularizar").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },

        },
        'columns': [
            { 'data': 'detalle_cc.part_no', "searchable": false },
            { 'data': 'detalle_cc.descripcion', "searchable": false },
            { 'data': 'detalle_cc.cantidad', "searchable": false },
            { 'data': 'detalle_cc.pvu_oc', "searchable": false },
            { 'data': 'producto.part_number','defaultContent':'', "searchable": false },
            { 'data': 'producto.descripcion','defaultContent':'(sin mapear)', "searchable": false },
            { 'data': 'cantidad', "searchable": false },
            { 'data': 'precio_unitario', "searchable": false },
            { 'data': 'detalle_orden', "searchable": false },
            { 'data': 'reserva', "searchable": false },
            { 'data': 'id_detalle_requerimiento', "searchable": false }


        ],
        'columnDefs': [

            {
                'render': function (data, type, row) {

                    return `<div style="text-align:left; width: 100%; height: 80px; margin: 0; padding: 0; overflow-y: scroll;">
                    ${row.detalle_cc!=null?row.detalle_cc.descripcion:'(Sin mapear)'}
                    </div>`;
                }, targets: 1
            },
            {
                'render': function (data, type, row) {

                    return `<div style="text-align:left; width: 100%; height: 80px; margin: 0; padding: 0; overflow-y: scroll;">
                    ${row.producto!=null ?row.producto.part_number :'(Sin mapear) '+row.part_number}
                    </div>`;
                }, targets: 4
            },
            {
                'render': function (data, type, row) {

                    return `<div style="text-align:left; width: 100%; height: 80px; margin: 0; padding: 0; overflow-y: scroll;">
                    ${row.producto!=null ?row.producto.descripcion :'(Sin mapear) '+row.descripcion}
                    </div>`;
                }, targets: 5
            },
            {
                'render': function (data, type, row) {
                    let ordenes = [];

                    row.detalle_orden.map((detOrden, i) => {
                        if (detOrden.estado != 7) {
                            ordenes.push('<a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/' + detOrden.orden.id_orden_compra + '" target="_blank" title="Abrir Orden" >' + detOrden.orden.codigo + '</a>');
                        }
                    });
                    return ordenes;
                }, targets: 8
            },
            {
                'render': function (data, type, row) {
                    let reservas = [];

                    (row.reserva).map((r, i) => {
                        if (r.estado != 7) {
                            reservas.push('<a href="imprimir_ingreso/' + r.id_reserva + '"  target="_blank" title="Abrir Ingreeso">' + r.codigo + '</a>');

                        }
                    });
                    return reservas;
                }, targets: 9
            },
            {
                'render': function (data, type, row) {

                    let reservaHabilitada = [];
                    let ordenes = [];
                    let ingresoAlmacenList = [];

                    row.detalle_orden.map((detOrden, i) => {
                        if (detOrden.estado != 7) {
                            ordenes.push('<a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/' + detOrden.orden.id_orden_compra + '" target="_blank" title="Abrir Orden" >' + detOrden.orden.codigo + '</a>');
                            if (detOrden.guia_compra_detalle != null && detOrden.guia_compra_detalle.length > 0) {
                                (detOrden.guia_compra_detalle).forEach(gcd => {
                                    if (gcd.movimiento_detalle != null && gcd.movimiento_detalle.length > 0) {
                                        (gcd.movimiento_detalle).forEach(md => {
                                            if (md.estado != 7 && md.movimiento.estado != 7) {
                                                ingresoAlmacenList.push({
                                                    'id': md.movimiento.id_mov_alm,
                                                    'codigo': md.movimiento.codigo,
                                                    'id_orden': detOrden.id_orden_compra,
                                                    'id_detalle_orden': detOrden.id_detalle_orden,
                                                    'id_detalle_requerimiento': detOrden.id_detalle_requerimiento
                                                });
                                            }
                                        });
                                    }

                                });
                            }
                        }
                    });
                    (row.reserva).map((r, i) => {
                        if (r.estado != 7) {
                            if (r.estado == 1) {
                                reservaHabilitada.push(
                                    {
                                        'id': r.id_reserva,
                                        'id_detalle_requerimiento': r.id_detalle_requerimiento
                                    }
                                )
                            }
                        }
                    });

                    let mensaje = [];
                    if (row.id_producto == 0) {
                        mensaje.push("Producto sin mapear");
                    }
                    if (ingresoAlmacenList.length > 0) {

                        mensaje.push("Orden con ingreso almacén");
                    }

                    // if(reservaHabilitada.length > 0 ){

                    //     mensaje.push("Con reserva procesada");
                    // }
                    let botoneraAccion= `<div class="btn-group" role="group">
                    <button type="button" class="btn btn-default btn-xs handleClickDesplegarVerDetalleOrdenReserva" name="btnVerDetalleOrdenReserva" title="Ver detalle ordenes / reserva" data-id-detalle-requerimiento="${row.id_detalle_requerimiento}"><i class="fas fa-chevron-down fa-sm"></i></button>
                    `;

                    if ((ordenes.length != 0) && (row.estado == 38)) {
                        botoneraAccion+= `
                        <button type="button" class="btn btn-warning btn-xs handleClickRemplazarProductoComprometidoEnTodaOrden" data-id-detalle-requerimiento="${row.id_detalle_requerimiento}"  name="btnRemplazarTodoProductoComprometidoEnTodaOrden" title="Remplazar item en todas las ordenes"><i class="fas fa-paint-roller fa-sm"></i></button>
                        <button type="button" class="btn btn-info btn-xs handleClickLiberarProductoComprometidoEnTodaOrden" data-id-detalle-requerimiento="${row.id_detalle_requerimiento}"  name="btnLiberarTodoProductoComprometidoEnTodaOrden" title="Liberar el item en todas las ordenes"><i class="fas fa-parachute-box fa-sm"></i></button>
                        <button type="button" class="btn btn-danger btn-xs handleClickAnularItemComprometidoEnTodaOrdenYReservas"  data-id-detalle-requerimiento="${row.id_detalle_requerimiento}"  name="btnAnularTodoItemComprometidoEnTodaOrdenYReservas" title="Anular todas las ordenes y reservas comprometidas"><i class="fas fa-ban fa-sm"></i></button>
                        `;
                    }
                    if ((ordenes.length == 0 && reservaHabilitada.length != 0) && (row.estado == 38)) {
                        botoneraAccion+= `<button type="button" class="btn btn-danger btn-xs handleClickAnularItemComprometidoEnTodaOrdenYReservas" data-id-detalle-requerimiento="${row.id_detalle_requerimiento}" name="btnAnularTodoItemComprometidoEnTodaOrdenYReservas" title="Anular todas las reservas comprometidas"><i class="fas fa-ban fa-sm"></i></button>`;
                    }
                    
                    botoneraAccion+=`</div>`

                    return botoneraAccion;
                }, targets: 10
            }

        ],
        'rowCallback': function (row, data, dataIndex) {

        },
        'initComplete': function () {

        },
        "drawCallback": function (settings) {
            $("#listaItemsPorRegularizar").LoadingOverlay("hide", true);

        },
        "createdRow": function (row, data, dataIndex) {
            if (data.estado == 38) {
                $(row.childNodes).css('background-color', '#f3e68d');
                // $(row.childNodes).css('font-weight', 'bold');
            }

        }

    });
}


function remplazarProductoComprometidoEnTodaOrden(obj) {
    if (obj.dataset.idDetalleRequerimiento > 0) {
        Swal.fire({
            title: 'Esta seguro que desea remplazar el producto en todas las ordenes?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, remplazar'

        }).then((result) => {
            if (result.isConfirmed) {
                // let regularizacionConOrdenResueltoParaDetalleRequerimiento=JSON.parse(sessionStorage.getItem('regularizacionConOrdenResueltoParaDetalleRequerimiento'))
                // regularizacionConOrdenResueltoParaDetalleRequerimiento.push(obj.dataset.idDetalleRequerimiento)
                // sessionStorage.setItem('regularizacionConOrdenResueltoParaDetalleRequerimiento', JSON.stringify(regularizacionConOrdenResueltoParaDetalleRequerimiento));
                // sessionStorage.removeItem('regularizacionConOrdenResueltoParaDetalleRequerimiento');

                realizarRemplazarProductoComprometidoEnTodaOrden(obj.dataset.idDetalleRequerimiento).then((res) => {
                    // console.log(res);
                    if (res.status == 200) {
                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });
                        // construirTablaPorRegularizar(document.querySelector("div[id='modal-por-regularizar'] input[name='idRequerimiento']").value);
                        $("#listaItemsPorRegularizar").DataTable().ajax.reload(null, false);

                        if (res.cambiaEstadoRequerimiento == true) {
                            // trPorRegularizarSeleccionar.querySelector("i[class~='fa-exclamation-triangle']").remove()
                            $('#modal-por-regularizar').modal('hide');

                            let timerInterval
                            Swal.fire({
                                title: 'Regularización finalizada',
                                html: '<h5>Se actualizará el listado en <br> <b></b> milisegundos. </h5>',
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                    const b = Swal.getHtmlContainer().querySelector('b')
                                    timerInterval = setInterval(() => {
                                        b.textContent = Swal.getTimerLeft()
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    $("#listaRequerimientosPendientes").DataTable().ajax.reload(null, false);
                                }
                            })

                        }

                        // obj.closest('tr').remove();
                    } else {
                        Lobibox.notify('warning', {
                            title: false,
                            size: 'mini',
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


    } else {
        alert("El ID enviado no es correcto, contacte con el administrador");
    }
}
function realizarRemplazarProductoComprometidoEnTodaOrden(idDetalleRequerimiento) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'POST',
            url: `realizar-remplazo-de-producto-comprometido-en-toda-orden`,
            dataType: 'JSON',
            data: { 'idDetalleRequerimiento': idDetalleRequerimiento },
            success(response) {
                resolve(response);
            },
            fail: (jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);

                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar remplazar todo los producto comprometidos, por favor vuelva a intentarlo',
                    'error'
                );
            },
            error: function (err) {
                console.log(err);
                reject(err)
            }
        });
    });
}

function liberarProductoComprometidoEnTodaOrden(obj) {
    if (obj.dataset.idDetalleRequerimiento > 0) {
        Swal.fire({
            title: 'Esta seguro que desea liberar el producto en todas las ordenes?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, liberar'

        }).then((result) => {
            if (result.isConfirmed) {
                realizarLiberarTodoProductoComprometidoEnTodaOrden(obj.dataset.idDetalleRequerimiento).then((res) => {
                    // console.log(res);
                    if (res.status == 200) {
                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });
                        // construirTablaPorRegularizar(document.querySelector("div[id='modal-por-regularizar'] input[name='idRequerimiento']").value);
                        $("#listaItemsPorRegularizar").DataTable().ajax.reload(null, false);


                        if (res.cambiaEstadoRequerimiento == true) {
                            // trPorRegularizarSeleccionar.querySelector("i[class~='fa-exclamation-triangle']").remove()
                            $('#modal-por-regularizar').modal('hide');
                            let timerInterval
                            Swal.fire({
                                title: 'Regularización finalizada',
                                html: '<h5>Se actualizará el listado en <br> <b></b> milisegundos. </h5>',
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                    const b = Swal.getHtmlContainer().querySelector('b')
                                    timerInterval = setInterval(() => {
                                        b.textContent = Swal.getTimerLeft()
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    $("#listaRequerimientosPendientes").DataTable().ajax.reload(null, false);
                                }
                            })
                        }
                    } else {
                        Lobibox.notify('warning', {
                            title: false,
                            size: 'mini',
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


    } else {
        alert("El ID enviado no es correcto, contacte con el administrador");
    }
}
function realizarLiberarTodoProductoComprometidoEnTodaOrden(idDetalleRequerimiento) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'POST',
            url: `realizar-liberacion-de-producto-comprometido-en-toda-orden`,
            dataType: 'JSON',
            data: { 'idDetalleRequerimiento': idDetalleRequerimiento },
            success(response) {
                resolve(response);
            },
            fail: (jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);

                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar liberar el item en todas la ordenes, por favor vuelva a intentarlo',
                    'error'
                );
            },
            error: function (err) {
                console.log(err);
                reject(err)
            }
        });
    });
}

function anularItemComprometidoEnTodaOrdenYReserva(obj) {
    if (obj.dataset.idDetalleRequerimiento > 0) {
        Swal.fire({
            title: 'Esta seguro que desea anular el producto en todas las ordenes y reservas?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, anular'

        }).then((result) => {
            if (result.isConfirmed) {
                realizarAnularProductoComprometidoEnTodaOrdenYReservas(obj.dataset.idDetalleRequerimiento).then((res) => {
                    // console.log(res);
                    if (res.status == 200) {
                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });
                        // construirTablaPorRegularizar(document.querySelector("div[id='modal-por-regularizar'] input[name='idRequerimiento']").value);
                        $("#listaItemsPorRegularizar").DataTable().ajax.reload(null, false);


                        if (res.cambiaEstadoRequerimiento == true) {
                            // trPorRegularizarSeleccionar.querySelector("i[class~='fa-exclamation-triangle']").remove()
                            $('#modal-por-regularizar').modal('hide');

                            let timerInterval
                            Swal.fire({
                                title: 'Regularización finalizada',
                                html: '<h5>Se actualizará el listado en <br> <b></b> milisegundos. </h5>',
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                    const b = Swal.getHtmlContainer().querySelector('b')
                                    timerInterval = setInterval(() => {
                                        b.textContent = Swal.getTimerLeft()
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    $("#listaRequerimientosPendientes").DataTable().ajax.reload(null, false);
                                }
                            })
                        }
                        // obj.closest('tr').remove();
                    } else {
                        Lobibox.notify('warning', {
                            title: false,
                            size: 'mini',
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


    } else {
        alert("El ID enviado no es correcto, contacte con el administrador");
    }
}
function realizarAnularProductoComprometidoEnTodaOrdenYReservas(idDetalleRequerimiento) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'POST',
            url: `realizar-anular-item-en-toda-orden-y-reservas`,
            dataType: 'JSON',
            data: { 'idDetalleRequerimiento': idDetalleRequerimiento },
            success(response) {
                resolve(response);
            },
            fail: (jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);

                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar anular el item en todas la ordenes/reservas, por favor vuelva a intentarlo',
                    'error'
                );
            },
            error: function (err) {
                console.log(err);
                reject(err)
            }
        });
    });
}

function desplegarVerDetalleOrdenReserva(obj) {
    let tr = obj.closest('tr');
    var row = $listaItemsPorRegularizar.row(tr);
    var id = obj.dataset.idDetalleRequerimiento;
    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.classList.remove('shown');
    }
    else {
        // Open this row
        //    row.child( format(iTableCounter, id) ).show();
        this.buildFormatListaItemsPorRegularizar(obj, iTableCounter, id, row);
        tr.classList.add('shown');
        // try datatable stuff
        oInnerTable = $('#listaItemsPorRegularizar_' + iTableCounter).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: [
            ]
        });
        iTableCounter = iTableCounter + 1;
    }
}

function obtenerDetalleOrdenYReserva(idDetalleRequerimiento) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `items-orden-items-reserva-por-detalle-requerimiento/${idDetalleRequerimiento}`,
            dataType: 'JSON',
            success(response) {
                resolve(response.data);
            },
            error: function (err) {
                reject(err)
            }
        });
    });
}

function buildFormatListaItemsPorRegularizar(obj, table_id, id, row) {
    obj.setAttribute('disabled', true);

    obtenerDetalleOrdenYReserva(id).then((res) => {
        obj.removeAttribute('disabled');
        construirDetalleListaOrdenYReserva(table_id, row, res);
    }).catch((err) => {
        console.log(err)
    })
}

function construirDetalleListaOrdenYReserva(table_id, row, response) {
    var html = '';
    // console.log(response);
    if (response.length > 0) {

        response.forEach(function (element) {
            // let botoneraDetalleFila = '';
            // if (element.id_detalle_orden > 0) {
            //     botoneraDetalleFila += `<button type="button" class="btn btn-default btn-xs handleClickRemplazarProductoEnOrden" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-id-detalle-orden="${element.id_detalle_orden}" name="btnRemplazarProductoEnOrden" title="Remplazar producto en orden"><i class="fas fa-paint-roller fa-sm"></i></button>`;
            //     botoneraDetalleFila += `<button type="button" class="btn btn-default btn-xs handleClickLiberarProductoOrden" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-id-detalle-orden="${element.id_detalle_orden}" name="btnLiberarProductoOrden" title="Liberar producto de orden"><i class="fas fa-parachute-box fa-sm"></i></button>`;
            //     botoneraDetalleFila += `<button type="button" class="btn btn-default btn-xs handleClickAnularItemDeOrden"  data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-id-detalle-orden="${element.id_detalle_orden}" name="btnAnularItemOrden" title="Anular item de orden"><i class="fas fa-ban fa-sm"></i></button>`;
            // }
            // if (element.id_reserva > 0) {
            //     botoneraDetalleFila = `<button type="button" class="btn btn-default btn-xs handleClickAnularReserva" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-id-reserva="${element.id_reserva}" name="btnAnularReserva" title="Anular reserva"><i class="fas fa-ban fa-sm"></i></button>`;
            // }

            html += `<tr>
                    <td style="border: none; text-align:center;">${element.codigo_documento != null ? element.codigo_documento : ''}</td>
                    <td style="border: none; text-align:left;">${element.part_number != null ? element.part_number : ''}</td>
                    <td style="border: none; text-align:left;">${element.descripcion != null ? element.descripcion : ''}</td>
                    <td style="border: none; text-align:center;">${element.unidad_medida != null ? element.unidad_medida : ''}</td>
                    <td style="border: none; text-align:center;">${element.cantidad > 0 ? element.cantidad : ''}</td>
                    <td style="border: none; text-align:center;">${element.estado != null ? element.estado : ''}</td>
                    </tr>`;

        });
        var tabla = `<table class="table table-condensed table-bordered" 
            id="detalle_${table_id}">
            <thead style="color: black;background-color: #c7cacc;">
                <tr>
                    <th style="border: none; text-align:center;">Cod. documento</th>
                    <th style="border: none; text-align:center;">Part number</th>
                    <th style="border: none; text-align:center;">Descripcion</th>
                    <th style="border: none; text-align:center;">Unidad medida</th>
                    <th style="border: none; text-align:center;">Cantidad</th>
                    <th style="border: none; text-align:center;">Estado</th>
                </tr>
            </thead>
            <tbody style="background: #e7e8ea;">${html}</tbody>
            </table>`;
    } else {
        var tabla = `<table class="table table-sm" style="border: none;" 
            id="detalle_${table_id}">
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
            </table>`;
    }
    row.child(tabla).show();
}