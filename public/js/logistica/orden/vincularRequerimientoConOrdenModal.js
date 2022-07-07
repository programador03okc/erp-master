var tablaListaRequerimientosParaVincular;
var listaImteSeleccionadosParaVincular = [];

$(function () {
    $("#listaSalidasVenta tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaSalidasVenta").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }

        var data = $('#listaSalidasVenta').DataTable().row($(this)).data();
        console.log(data);

        // $("[name=id_mov_alm]").val(data.id_mov_alm);
        // $("[name=id_guia_ven]").val(data.id_guia_ven);
        $("[name=id_requerimiento]").val(data.id_requerimiento ?? 0);
        $("[name=id_contribuyente]").val(data.id_contribuyente ?? null);
        $("[name=id_empresa]").val(data.id_empresa);
        $("[name=id_entidad]").val(data.id_entidad);
        $("[name=id_contacto]").val(data.id_contacto);
        $("[name=codigo_oportunidad]").val(data.codigo_oportunidad);

        $("[name=cliente_razon_social]").val(data.razon_social);
        $("[name=nro_orden]").val(data.nro_orden);
        $(".codigo_oportunidad").text(data.codigo_oportunidad);
        $(".fecha_registro").text(formatDate(fecha_actual()));

        $("[name=nombre_contacto]").val(data.nombre);
        $("[name=cargo_contacto]").val(data.cargo);
        $("[name=telefono_contacto]").val(data.telefono);
        $("[name=direccion_contacto]").val(data.direccion);
        $(".horario_contacto").text(data.horario);
        $(".email_contacto").text(data.email);

        $("#modal-salidasVenta").modal("hide");
    });



    $('#modal-listaItemsRequerimientoParaVincular #listaItemsRequerimientoParaVincular tbody').on("change", "input.handleCheckControlCheckParaAgregarItemParaVincular", (e) => {
        controlCheckParaAgregarItemSeleccionadoParaVincular(e);
    });

    $('#modal-listaItemsRequerimientoParaVincular').on("change", "input.handleCheckSeleccionarTodoItemParaVincular", (e) => {
        agregarTodoItemSeleccionadoParaVincular(e);
    });

    $('#modal-listaItemsRequerimientoParaVincular').on("click", "span.handleClickModalVerOrdenDeRequerimiento", (e) => { // tab para lista pendiente tab lista atendidos
        modalVerOrdenDeRequerimiento(e.currentTarget);
    });

});

function limpiarTabla(idElement) {
    let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
    if (nodeTbody != null) {
        while (nodeTbody.children.length > 0) {
            nodeTbody.removeChild(nodeTbody.lastChild);
        }

    }
}

function construirListarRequerimientosPendientesParaVincularConOrden() {
    var vardataTables = funcDatatables();

    tablaListaRequerimientosParaVincular = $('#listaRequerimientosParaVincular').DataTable({
        'dom': 'Bfrtip',
        'language': vardataTables[0],
        'order': [[9, 'desc']],
        'serverSide': true,
        'processing': false,
        'destroy': true,
        'ajax': {
            'url': 'listarRequerimientoLogisticosView',
            'type': 'POST',
            beforeSend: data => {

                $("#listaRequerimientosParaVincular").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }

        },
        'columns': [
            { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
            { 'data': 'concepto', 'name': 'concepto', 'className': 'text-left' },
            { 'data': 'fecha_registro', 'name': 'fecha_registro', 'className': 'text-center' },
            { 'data': 'tipo_requerimiento', 'name': 'tipo_requerimiento', 'className': 'text-center' },
            { 'data': 'descripcion_moneda', 'name': 'descripcion_moneda', 'className': 'text-center' },
            { 'data': 'razon_social_cliente', 'name': 'razon_social_cliente', 'className': 'text-left' },
            { 'data': 'empresa_sede', 'name': 'empresa_sede', 'className': 'text-center' },
            { 'data': 'solicitado_por', 'name': 'solicitado_por', 'className': 'text-center' },
            {
                'data': 'estado', 'name': 'estado', 'className': 'text-center', 'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + ' estadoRequerimiento" title="' + row['estado'] + '">' + row['estado'] + '</span>';
                }
            },
            { 'data': 'id_requerimiento_logistico', 'name': 'id_requerimiento_logistico', "searchable": false }

        ],
        'initComplete': function () {
            //Boton de busqueda
            const $filter = $('#listaRequerimientosParaVincular_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                tablaListaRequerimientosParaVincular.search($input.val()).draw();
            })
            //Fin boton de busqueda
        },
        "drawCallback": function (settings) {
            //Botón de búsqueda
            $('#listaRequerimientosParaVincular_filter input').prop('disabled', false);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
            $('#listaRequerimientosParaVincular_filter input').trigger('focus');
            //fin botón búsqueda
            if (tablaListaRequerimientosParaVincular.rows().data().length == 0) {
                Lobibox.notify('info', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `No se encontro data disponible para mostrar`
                });
            }
            //Botón de búsqueda
            $('#listaRequerimientosParaVincular_filter input').prop('disabled', false);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
            $('#listaRequerimientosParaVincular_filter input').trigger('focus');
            //fin botón búsqueda
            $("#listaRequerimientosParaVincular").LoadingOverlay("hide", true);
        },
        'columnDefs': [
            { 'aTargets': [0], 'className': "text-left", 'sWidth': '7%' },
            { 'aTargets': [1], 'className': "text-left", 'sWidth': '30%' },
            { 'aTargets': [2], 'className': "text-center", 'sWidth': '4%' },
            { 'aTargets': [3], 'className': "text-center", 'sWidth': '4%' },
            { 'aTargets': [4], 'className': "text-center", 'sWidth': '5%' },
            { 'aTargets': [5], 'className': "text-left", 'sWidth': '8%' },
            { 'aTargets': [6], 'className': "text-center", 'sWidth': '4%' },
            { 'aTargets': [7], 'className': "text-center", 'sWidth': '4%' },
            { 'aTargets': [8], 'className': "text-center", 'sWidth': '4%' },
            {
                'render':
                    function (data, type, row) {
                        let containerOpenBrackets = `<div class="btn-group" role="group" style="display: flex;flex-direction: row;flex-wrap: nowrap;">`;
                        let btnVerDetalle = `<button type="button" class="ver-detalle btn btn-default boton" onclick="desplegarDetalleRequerimientoModalVincularRequerimiento(this);" data-id-requerimiento="${row.id_requerimiento_logistico}"  data-toggle="tooltip" data-placement="bottom" title="Ver detalle requerimiento"> <i class="fas fa-chevron-down fa-sm"></i> </button>`;
                        let btnSeleccionar = `<button type="button" class="ver-detalle btn btn-${row.count_pendientes > 0 ? 'default' : 'success'} boton" onclick="openVincularItemsDeRequerimientoModal(this);" data-toggle="tooltip" data-placement="bottom" title="${(row.id_estado == 38 || row.id_estado == 39 ? 'Este requerimiento tiene un estado por regularizar / en pausa' : 'Seleccionar')}" data-id-requerimiento="${row.id_requerimiento_logistico}" data-codigo-requerimiento="${row.codigo}" ${(row.id_estado == 38 || row.id_estado == 39 ? 'disabled' : '')}> Seleccionar </button>`;
                        let containerCloseBrackets = `</div>`;
                        let infoPorMapear = `<small class="text-${row.count_pendientes > 0 ? 'danger' : 'success'}">${row.count_pendientes > 0 ? ('Mapeos pendientes: ' + row.count_pendientes) : ''}</small>
                    `;
                        return (containerOpenBrackets + btnVerDetalle + btnSeleccionar + containerCloseBrackets + infoPorMapear);
                    }, targets: 9, className: "text-center", sWidth: '10%'
            }
        ]

    });
}

function openVincularRequerimientoConOrden() {
    $("#modal-vincularRequerimientoConOrden").modal({
        show: true
    });
    construirListarRequerimientosPendientesParaVincularConOrden();
    
}


function desplegarDetalleRequerimientoModalVincularRequerimiento(obj) {
    let tr = obj.closest('tr');
    var row = tablaListaRequerimientosParaVincular.row(tr);
    var id = obj.dataset.idRequerimiento;
    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.classList.remove('shown');
    }
    else {
        // Open this row
        //    row.child( format(iTableCounter, id) ).show();
        buildFormatModalVincularRequerimiento(iTableCounter, id, row);
        tr.classList.add('shown');
        // try datatable stuff
        oInnerTable = $('#listaRequerimientosParaVincular_' + iTableCounter).dataTable({
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


function buildFormatModalVincularRequerimiento(table_id, id, row) {
    getDetalleRequerimientos(id).then((res) => {
        construirDetalleRequerimientoModalVincularRequerimiento(table_id, row, res);
    }).catch(function (err) {
        console.log(err)
        Swal.fire(
            '',
            err,
            'error'
        );
    })
}

function getDetalleRequerimientos(id) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `detalle-requerimiento/${id}`,
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

function construirDetalleRequerimientoModalVincularRequerimiento(table_id, row, response) {
    var html = '';
    if (response.length > 0) {
        response.forEach((element) => {
            if (element.tiene_transformacion == false) {
                let cantidad_atendido_almacen = 0;
                if (element.reserva.length > 0) {
                    (element.reserva).forEach(reserva => {
                        if (reserva.estado == 1) {
                            cantidad_atendido_almacen += parseFloat(reserva.stock_comprometido);
                        }
                    });
                }

                html += `<tr>
                    <td style="border: none; text-align:center;">${(element.producto_part_number != null ? element.producto_part_number : '')}</td>
                    <td style="border: none; text-align:center;">${(element.producto_codigo != null ? element.producto_codigo : '')}</td>
                    <td style="border: none; text-align:center;">${(element.producto_codigo_softlink != null ? element.producto_codigo_softlink : '')}</td>
                    <td style="border: none; text-align:left;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}</td>
                    <td style="border: none; text-align:center;">${element.abreviatura != null ? element.abreviatura : ''}</td>
                    <td style="border: none; text-align:center;">${element.cantidad > 0 ? element.cantidad : ''}</td>
                    <td style="border: none; text-align:center;">${element.precio_unitario > 0 ? element.precio_unitario : ''}</td>
                    <td style="border: none; text-align:center;">${parseFloat(element.subtotal) > 0 ? $.number(element.subtotal, 2) : $.number((element.cantidad * element.precio_unitario), 2)}</td>
                    <td style="border: none; text-align:center;">${element.motivo != null ? element.motivo : ''}</td>
                    <td style="border: none; text-align:center;">${cantidad_atendido_almacen != null ? cantidad_atendido_almacen : ''}</td>
                    <td style="border: none; text-align:center;">${element.estado_doc != null ? element.estado_doc : ''}</td>
                    </tr>`;
            }
        });
        var tabla = `<table class="table table-condensed table-bordered" 
            id="detalle_${table_id}">
            <thead style="color: black;background-color: #c7cacc;">
                <tr>
                    <th style="border: none; text-align:center;">Part number</th>
                    <th style="border: none; text-align:center;">Código Producto</th>
                    <th style="border: none; text-align:center;">Código Softlink</th>
                    <th style="border: none; text-align:center;">Descripcion</th>
                    <th style="border: none; text-align:center;">Unidad medida</th>
                    <th style="border: none; text-align:center;">cantidad</th>
                    <th style="border: none; text-align:center;">precio_unitario</th>
                    <th style="border: none; text-align:center;">subtotal</th>
                    <th style="border: none; text-align:center;">motivo</th>
                    <th style="border: none; text-align:center;">Stock comprometido</th>
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

// inicio func boton vincular requerimiento old
function openVincularItemsDeRequerimiento(obj) {
    obj.setAttribute("disabled", true);
    let idRequerimiento = obj.dataset.idRequerimiento;
    let i = 0;
    let cantidadItemSinMapear = 0;
    obtenerRequerimientoPorID(idRequerimiento).then((res) => {
        loadHeadRequerimiento([res], 2);
        obj.removeAttribute("disabled");

        (res.detalle).forEach((element) => {
            if (element.tiene_transformacion == false) {
                if (element.id_producto > 0 && (![5, 28, 7].includes(element.id_estado))) {
                    i++;

                    let cantidad_atendido_almacen = 0;
                    if (element.reserva.length > 0) {
                        (element.reserva).forEach(reserva => {
                            if (reserva.estado == 1) {
                                cantidad_atendido_almacen += parseFloat(reserva.stock_comprometido);
                            }
                        });
                    }
                    let cantidad_atendido_orden = 0;
                    if (element.ordenes_compra.length > 0) {
                        (element.ordenes_compra).forEach(orden => {
                            cantidad_atendido_orden += parseFloat(orden.cantidad);
                        });
                    }
                    let cantidad_a_comprar = parseFloat(element.cantidad > 0 ? element.cantidad : 0) - parseFloat(cantidad_atendido_almacen) - parseFloat(cantidad_atendido_orden);
                    // console.log(element);
                    agregarProducto([{
                        'id': this.makeId(),
                        'cantidad': element.cantidad ?? 0,
                        'cantidad_atendido_almacen': cantidad_atendido_almacen,
                        'cantidad_atendido_orden': cantidad_atendido_orden,
                        'cantidad_a_comprar': !(parseFloat(cantidad_a_comprar) >= 0) ? '' : cantidad_a_comprar,
                        'codigo_item': null,
                        'codigo_producto': element.producto.codigo != null ? element.producto.codigo : '',
                        'codigo_softlink': element.producto.cod_softlink != null ? element.producto.cod_softlink : '',
                        'codigo_requerimiento': element.codigo_requerimiento,
                        'descripcion': null,
                        'descripcion_producto': element.producto.descripcion != null ? element.producto.descripcion : '',
                        'estado': 0,
                        'garantia': null,
                        'id_detalle_orden': null,
                        'id_detalle_requerimiento': element.id_detalle_requerimiento,
                        'id_item': null,
                        'id_tipo_item': 1,
                        'id_producto': element.id_producto,
                        'id_requerimiento': element.id_requerimiento,
                        'id_unidad_medida': element.unidad_medida.id_unidad_medida,
                        'lugar_despacho': null,
                        'part_number': (!element.id_producto > 0 ? '(Sin mapear)' : ((element.producto.part_number != null ? element.producto.part_number : ''))),
                        'precio_unitario': element.precio_unitario ?? 0,
                        'id_moneda': 1,
                        'stock_comprometido': null,
                        'subtotal': $.number(parseFloat(element.precio_unitario * element.cantidad), 2),
                        'producto_regalo': false,
                        'tiene_transformacion': element.tiene_transformacion,
                        'unidad_medida': element.unidad_medida.abreviatura
                    }], 'DETALLE_REQUERIMIENTO');

                } else {
                    cantidadItemSinMapear++;
                }
            }
        });

        if (i > 0) {
            estadoVinculoRequerimiento({ 'mensaje': `Se agregó ${i} Item(s) a la orden`, 'estado': '200' })

        } else {
            if (cantidadItemSinMapear > 0) {
                estadoVinculoRequerimiento({ 'mensaje': `No se puede agregar item(s) a la orden, tiene ${cantidadItemSinMapear} items sin mapear`, 'estado': '204' })
            } else {
                estadoVinculoRequerimiento({ 'mensaje': `No se puede agregar item(s) a la orden`, 'estado': '204' })

            }

        }



    }).catch(function (err) {
        console.log(err)

    })

}

function estadoVinculoRequerimiento(resolve) {
    let tipoMensaje = '';
    if (resolve.estado == '200') {
        tipoMensaje = 'success'
        $('#modal-vincular-requerimiento-orden').modal('hide');
    } else {
        tipoMensaje = 'warning'

    }

    Lobibox.notify(tipoMensaje, {
        title: false,
        size: 'mini',
        rounded: true,
        sound: false,
        delayIndicator: false,
        msg: resolve.mensaje
    });
}

function obtenerRequerimientoPorID(id) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `requerimiento/${id}`,
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

function agregarProducto(data, tipo) {
    vista_extendida();
    // <td><select name="unidad[]" class="form-control ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : '')} input-sm unidadMedida" data-valor="${data[0].id_unidad_medida}"  >${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
    console.log(data);
    if(data.length>0){
        document.querySelector("tbody[id='body_detalle_orden']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;">
        <td class="text-center">${data[0].codigo_requerimiento ? data[0].codigo_requerimiento : ''} <input type="hidden"  name="idRegister[]" value="${data[0].id_detalle_orden ? data[0].id_detalle_orden : this.makeId()}"> <input type="hidden"  class="idEstado" name="idEstado[]"> <input type="hidden"  name="idDetalleRequerimiento[]" value="${data[0].id_detalle_requerimiento ? data[0].id_detalle_requerimiento : ''}"> <input type="hidden"  name="idTipoItem[]" value="1"> </td>
        <td class="text-center">${data[0].codigo_producto ? data[0].codigo_producto : ''} </td>
        <td class="text-center">${data[0].codigo_softlink ? data[0].codigo_softlink : ''} </td>
        <td class="text-center">${data[0].part_number ? data[0].part_number : ''} <input type="hidden"  name="idProducto[]" value="${(data[0].id_producto ? data[0].id_producto : data[0].id_producto)}"> </td>
        <td class="text-left">${(data[0].descripcion_producto ? data[0].descripcion_producto : (data[0].descripcion ? data[0].descripcion : ''))}  <input type="hidden"  name="descripcion[]" value="${(data[0].descripcion_producto ? data[0].descripcion_producto : data[0].descripcion)}">
            <textarea class="form-control activation" name="descripcionComplementaria[]" placeholder="Descripción complementaria" style="width:100%;height: 60px;overflow: scroll;"></textarea>
        </td>
        <td>
        <input type="hidden"  name="unidad[]" value="${data[0].id_unidad_medida}">
            <p name="unidad[]" class="form-control-static unidadMedida" data-valor="${data[0].id_unidad_medida}">${(data[0].unidad_medida ? data[0].unidad_medida : 'sin und.')}</p></td>
    
        <td>${(data[0].cantidad ? data[0].cantidad : '')}</td>
        <td>${(data[0].cantidad_atendido_almacen ? data[0].cantidad_atendido_almacen : '')}</td>
        <td>${(data[0].cantidad_atendido_orden ? data[0].cantidad_atendido_orden : '')}</td>
        <td>
            <input class="form-control cantidad_a_comprar input-sm text-right ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : 'activation')}  handleBurUpdateSubtotal"  data-id-tipo-item="1" type="number" min="0" name="cantidadAComprarRequerida[]"  placeholder="" value="${data[0].cantidad_a_comprar ? data[0].cantidad_a_comprar : ''}" >
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</div>
                <input class="form-control precio input-sm text-right ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : 'activation')}  handleBurUpdateSubtotal" data-id-tipo-item="1" data-producto-regalo="${(data[0].producto_regalo ? data[0].producto_regalo : false)}" type="number" min="0" name="precioUnitario[]"  placeholder="" value="${data[0].precio_unitario ? data[0].precio_unitario : 0}" >
            </div>
        </td>
        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : 'activation')} handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" >
            <i class="fas fa-trash fa-sm"></i>
            </button>
        </td>
     </tr>`);
    
        autoUpdateSubtotal();
        UpdateSelectUnidadMedida();
        if (data.length > 0 && tipo == 'OBSEQUIO') {
            Lobibox.notify('success', {
                title: false,
                size: 'mini',
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Producto para obsequio agregado'
            });
    
        }
    
        $('.modal').modal('hide');
    }

}

// fin func boton vincular requerimiento 


// inicia func modal para ver items de requerimiento y agregar seleccionado 
function openVincularItemsDeRequerimientoModal(obj) {
    // obj.setAttribute("disabled", true);
    resertModalListaItemsRequerimientoParaVincular();
    if (obj.dataset.idRequerimiento > 0) {
        document.querySelector("div[id='modal-listaItemsRequerimientoParaVincular'] span[id='codigoRequerimiento']").textContent = obj.dataset.codigoRequerimiento;
        $("#modal-listaItemsRequerimientoParaVincular").modal({
            show: true
        });
        obtenerListaItemDeRequerimiento(obj.dataset.idRequerimiento);
        document.querySelector("div[id='modal-listaItemsRequerimientoParaVincular'] input[id='idRequerimiento']").value=obj.dataset.idRequerimiento;

    } else {
        Swal.fire(
            '',
            'Hubo un problema al intentar obtener el ID del requerimiento, actualice la página y vuelva a intentarlo',
            'error'
        );
    }
}

function obtenerListaItemDeRequerimiento(idRequerimiento) {
    getDetalleRequerimientos(idRequerimiento).then((res) => {
        construirListaItemsRequerimientoParaVincular(res);
    }).catch(function (err) {
        console.log(err)
        Swal.fire(
            '',
            err,
            'error'
        );
    })
}

function construirListaItemsRequerimientoParaVincular(data) {
    console.log(data);
    limpiarTabla('listaItemsRequerimientoParaVincular');

    for (let i = 0; i < data.length; i++) {

        let stockComprometido = 0;
        (data[i].reserva).forEach(reserva => {
            if (reserva.estado != 7) {
                stockComprometido += parseFloat(reserva.stock_comprometido);
            }
        });

        let atencionOrden = 0;
        let objOrdenList = [];
        (data[i].ordenes_compra).forEach(orden => { // TODO: no incluir anulados
            if (orden.estado != 7) {
                atencionOrden += parseFloat(orden.cantidad);
                objOrdenList.push({ 'id_orden': orden.id_orden_compra, 'codigo': orden.codigo });
            }
        });

        if (data[i].id_tipo_item == 1) { // producto
            // if (data[i].id_producto > 0) {
            // <td><select name="unidad[]" class="form-control ${(data[i].estado_guia_com_det > 0 && data[i].estado_guia_com_det != 7 ? '' : '')} input-sm unidadMedida" data-valor="${data[i].id_unidad_medida}" >${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            document.querySelector("table[id='listaItemsRequerimientoParaVincular'] tbody").insertAdjacentHTML('beforeend', `<tr style="text-align:center;" class="${data[i].estado == 7 ? 'danger textRedStrikeHover' : ''};">
                <td class="text-center"><input type="checkbox" class="handleCheckControlCheckParaAgregarItemParaVincular" name="seleccionarItemParaVincular" data-id-detalle-requerimiento="${data[i].id_detalle_requerimiento}" ${(data[i].estado == 38 || data[i].estado == 39 ? 'disabled' : '')}></td>
                <td class="text-center">${data[i].part_number ? data[i].part_number : ''}</td>
                <td class="text-center">${data[i].codigo_producto ? data[i].codigo_producto : ''} </td>
                <td class="text-center">${data[i].codigo_softlink ? data[i].codigo_softlink : ''} </td>
                <td class="text-left">${(data[i].descripcion_producto ? data[i].descripcion_producto : (data[i].descripcion != null ? data[i].descripcion : ''))} </td>
                <td class="text-center">${data[i].abreviatura ? data[i].abreviatura : ''} </td>
                <td class="text-center">${data[i].cantidad ? data[i].cantidad : ''} </td>
                <td class="text-center">${data[i].moneda_simbolo ? data[i].moneda_simbolo : ''} ${data[i].precio_unitario ? $.number(data[i].precio_unitario, 2) : ''} </td>
                <td>${stockComprometido != null && stockComprometido > 0 ? stockComprometido : '0'}</td>
                <td>${atencionOrden != null && atencionOrden > 0 ? `<span class="label label-info handleClickModalVerOrdenDeRequerimiento" data-codigo-requerimiento="${data[i].codigo_requerimiento}" data-orden=${JSON.stringify(objOrdenList)} style="cursor:pointer;" >${atencionOrden}</span>` : '0'}</td>
                <td class="text-center">
                    <ul class="list-unstyled">
                        <li>${data[i].estado_doc ? '<strong>'+data[i].estado_doc+'</strong>' : ''}</li>
                        <li>${parseInt(data[i].id_producto) > 0 ? '' : '<small class="text-danger">Sin mapear</small>'}</li>
                    </ul>
                    
                    
                </td>
                </tr>`);

            // }
        } else { //servicio
            document.querySelector("table[id='listaItemsRequerimientoParaVincular'] tbody").insertAdjacentHTML('beforeend', `<tr style="text-align:center;" class="${data[i].estado == 7 ? 'danger textRedStrikeHover' : ''};">
            <td class="text-center"><input type="checkbox" class="handleCheckControlCheckParaAgregarItemParaVincular" data-id-detalle-requerimiento="${data[i].id_detalle_requerimiento}" name="seleccionarItemParaVincular" ${(data[i].estado == 38 || data[i].estado == 39 ? 'disabled' : '')}></td>
            <td class="text-center">(Servicio)</td>
            <td class="text-center">${data[i].codigo_producto ? data[i].codigo_producto : ''} </td>
            <td class="text-center">${data[i].codigo_softlink ? data[i].codigo_softlink : ''} </td>
            <td class="text-left">${(data[i].descripcion_producto ? data[i].descripcion_producto : (data[i].descripcion != null ? data[i].descripcion : ''))} </td>
            <td class="text-center">${data[i].abreviatura ? data[i].abreviatura : ''} </td>
            <td class="text-center">${data[i].cantidad ? data[i].cantidad : ''} </td>
            <td class="text-center">${data[i].moneda_simbolo ? data[i].moneda_simbolo : ''} ${data[i].precio_unitario ? $.number(data[i].precio_unitario, 2) : ''} </td>
            <td></td>
            <td>${atencionOrden != null && atencionOrden > 0 ? `<span class="label label-info handleClickModalVerOrdenDeRequerimiento" data-codigo-requerimiento="${data[i].codigo_requerimiento}" data-orden=${JSON.stringify(objOrdenList)} style="cursor:pointer;" >${atencionOrden}</span>` : '0'}</td>
            <td class="text-center">
                <ul class="list-unstyled">
                    <li>${data[i].estado_doc ? '<strong>'+data[i].estado_doc+'</strong>' : ''}</li>
                    <li>${parseInt(data[i].id_producto) > 0 && data[i].id_tipo_item ==1 ? '' : '<small class="text-danger">Sin mapear</small>'}</li>
                </ul>
            </td>
            </tr>`);
        }

    }
}
// fin func modal para ver items de requerimiento y agregar seleccionado 

function controlCheckParaAgregarItemSeleccionadoParaVincular(e) {
    if (e.currentTarget.checked == true) {
        agregarItemSeleccionadoParaVincular(e.currentTarget.dataset.idDetalleRequerimiento);
    } else {
        quitarImteSeleccionadoParaVincular(e.currentTarget.dataset.idDetalleRequerimiento);
    }
    controlEstadoCheckSeleccionarTodos();

}

function agregarItemSeleccionadoParaVincular(idDetalleRequerimiento) {
    let seDebeAgregarItem = 'true'
    if (listaImteSeleccionadosParaVincular.length > 0) {
        listaImteSeleccionadosParaVincular.forEach(element => {
            if (element == parseInt(idDetalleRequerimiento)) {
                seDebeAgregarItem = 'false'
                return;
            }
        });
        if (seDebeAgregarItem) {
            listaImteSeleccionadosParaVincular.push(parseInt(idDetalleRequerimiento));
        }
    } else {
        listaImteSeleccionadosParaVincular.push(parseInt(idDetalleRequerimiento));
    }

    controlEstadoBtnAgregarItem();

}

function quitarImteSeleccionadoParaVincular(idDetalleRequerimiento) {
    if (listaImteSeleccionadosParaVincular.length > 0) {
        listaImteSeleccionadosParaVincular.forEach((element, index) => {
            if (element == parseInt(idDetalleRequerimiento)) {
                listaImteSeleccionadosParaVincular.splice(index, 1);
            }
        });
    }
    controlEstadoBtnAgregarItem();

}

function agregarTodoItemSeleccionadoParaVincular(e) {
    let allCheckboxItemSeleccion = document.querySelectorAll("input[type='checkbox'][name='seleccionarItemParaVincular']")
    if (e.currentTarget.checked == true) {
        allCheckboxItemSeleccion.forEach(element => {
            element.checked = true;
            agregarItemSeleccionadoParaVincular(element.dataset.idDetalleRequerimiento);
        });
    } else {
        allCheckboxItemSeleccion.forEach(element => {
            element.checked = false;
            quitarImteSeleccionadoParaVincular(element.dataset.idDetalleRequerimiento);
        });
    }
    controlEstadoBtnAgregarItem();

}

function controlEstadoBtnAgregarItem(){
    if (listaImteSeleccionadosParaVincular.length > 0) {
        document.querySelector("button[id='btnAgregarItemADetalleOrden']").removeAttribute("disabled");
    }else{
        document.querySelector("button[id='btnAgregarItemADetalleOrden']").setAttribute("disabled",true);
    }
    controlEstadoCheckSeleccionarTodos();
}

function controlEstadoCheckSeleccionarTodos(){
    if (listaImteSeleccionadosParaVincular.length == 0) {
        document.querySelector("input[id='checkSeleccionarTodos']").checked=false;
    }else{
        let allCheckboxItemSeleccion = document.querySelectorAll("input[type='checkbox'][name='seleccionarItemParaVincular']")
        let totalCheckboxItemSeleccion = allCheckboxItemSeleccion.length;
        let cantidadCheckboxItemSeleccion=0;

        allCheckboxItemSeleccion.forEach(element => {
            if(element.checked){
                cantidadCheckboxItemSeleccion++;
            }
        });

        if(cantidadCheckboxItemSeleccion==totalCheckboxItemSeleccion){
            document.querySelector("input[id='checkSeleccionarTodos']").checked=true;
        }else{
            document.querySelector("input[id='checkSeleccionarTodos']").checked=false;

        }

    }
}

function resertModalListaItemsRequerimientoParaVincular(){
    listaImteSeleccionadosParaVincular=[];
    document.querySelector("div[id='modal-listaItemsRequerimientoParaVincular'] input[id='idRequerimiento']").value='';
    document.querySelector("input[id='checkSeleccionarTodos']").checked=false;
    document.querySelector("button[id='btnAgregarItemADetalleOrden']").setAttribute("disabled",true);
    let allCheckboxItemSeleccion = document.querySelectorAll("input[type='checkbox'][name='seleccionarItemParaVincular']")
    allCheckboxItemSeleccion.forEach(element => {
        element.checked==false;
    });
}

function agregarItemADetalleOrden(){
    let idRequerimiento =parseInt(document.querySelector("div[id='modal-listaItemsRequerimientoParaVincular'] input[id='idRequerimiento']").value);
    if(listaImteSeleccionadosParaVincular.length>0){
    if(idRequerimiento>0){
        let i = 0;
        let cantidadItemSinMapear = 0;
        obtenerRequerimientoPorID(idRequerimiento).then((res) => {
            loadHeadRequerimiento([res], 2);
    
            (res.detalle).forEach((element) => {
                if (listaImteSeleccionadosParaVincular.includes(element.id_detalle_requerimiento)) {
                    // if (element.id_producto > 0 && (![5, 28, 7].includes(element.id_estado))) {
                    if (element.id_producto > 0 && (![7].includes(element.id_estado))) {
                        i++;
    
                        let cantidad_atendido_almacen = 0;
                        if (element.reserva.length > 0) {
                            (element.reserva).forEach(reserva => {
                                if (reserva.estado == 1) {
                                    cantidad_atendido_almacen += parseFloat(reserva.stock_comprometido);
                                }
                            });
                        }
                        let cantidad_atendido_orden = 0;
                        if (element.ordenes_compra.length > 0) {
                            (element.ordenes_compra).forEach(orden => {
                                cantidad_atendido_orden += parseFloat(orden.cantidad);
                            });
                        }
                        let cantidad_a_comprar = parseFloat(element.cantidad > 0 ? element.cantidad : 0) - parseFloat(cantidad_atendido_almacen) - parseFloat(cantidad_atendido_orden);
                        // console.log(element);
                        agregarProducto([{
                            'id': makeId(),
                            'cantidad': element.cantidad ?? 0,
                            'cantidad_atendido_almacen': cantidad_atendido_almacen,
                            'cantidad_atendido_orden': cantidad_atendido_orden,
                            'cantidad_a_comprar': !(parseFloat(cantidad_a_comprar) >= 0) ? '' : cantidad_a_comprar,
                            'codigo_item': null,
                            'codigo_producto': element.producto.codigo != null ? element.producto.codigo : '',
                            'codigo_softlink': element.producto.cod_softlink != null ? element.producto.cod_softlink : '',
                            'codigo_requerimiento': element.codigo_requerimiento,
                            'descripcion': null,
                            'descripcion_producto': element.producto.descripcion != null ? element.producto.descripcion : '',
                            'estado': 0,
                            'garantia': null,
                            'id_detalle_orden': null,
                            'id_detalle_requerimiento': element.id_detalle_requerimiento,
                            'id_item': null,
                            'id_tipo_item': 1,
                            'id_producto': element.id_producto,
                            'id_requerimiento': element.id_requerimiento,
                            'id_unidad_medida': element.unidad_medida.id_unidad_medida,
                            'lugar_despacho': null,
                            'part_number': (!element.id_producto > 0 ? '(Sin mapear)' : ((element.producto.part_number != null ? element.producto.part_number : ''))),
                            'precio_unitario': element.precio_unitario ?? 0,
                            'id_moneda': 1,
                            'stock_comprometido': null,
                            'subtotal': $.number(parseFloat(element.precio_unitario * element.cantidad), 2),
                            'producto_regalo': false,
                            'tiene_transformacion': element.tiene_transformacion,
                            'unidad_medida': element.unidad_medida.abreviatura
                        }], 'DETALLE_REQUERIMIENTO');
    
                    } else {
                        cantidadItemSinMapear++;
                    }
                }
            });
    
            if (i > 0) {
                estadoVinculoRequerimiento({ 'mensaje': `Se agregó ${i} Item(s) a la orden`, 'estado': '200' })
    
            } else {
                if (cantidadItemSinMapear > 0) {
                    estadoVinculoRequerimiento({ 'mensaje': `No se puede agregar item(s) a la orden, tiene ${cantidadItemSinMapear} items sin mapear`, 'estado': '204' })
                } else {
                    estadoVinculoRequerimiento({ 'mensaje': `No se puede agregar item(s) a la orden`, 'estado': '204' })
    
                }
    
            }
    
    
    
        }).catch(function (err) {
            console.log(err)
    
        })
    }else{
        Swal.fire(
            '',
            'Lo sentimos, hubo un problema al no detectar el requerimiento seleccionado, cierre esta ventana emergente o actualice el navegador y vuelva a intentarlo',
            'error'
        );
    } 
    
    }else{
        Swal.fire(
            '',
            'Lo sentimos, hubo un problema al no detectar item seleccionados, cierre esta ventana emergente o actualice el navegador y vuelva a intentarlo',
            'error'
        );
    }
}

function modalVerOrdenDeRequerimiento(obj) {

    $('#modal-ver-orden-de-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });

    document.querySelector("div[id='modal-ver-orden-de-requerimiento'] span[id='codigo']").textContent = '';
    document.querySelector("div[id='modal-ver-orden-de-requerimiento'] div[id='contenedor-ordenes-de-requerimiento']").innerHTML = '';

    let linkOrden = [];
    if (JSON.parse(obj.dataset.orden).length > 0) {
        (JSON.parse(obj.dataset.orden)).forEach(element => {
            linkOrden.push(`<label class='lbl-codigo handleClickAbrirOrden' title='Ir a orden' data-id-orden='${element.id_orden}'>${element.codigo}</label>`);

        });
        document.querySelector("div[id='modal-ver-orden-de-requerimiento'] div[id='contenedor-ordenes-de-requerimiento']").innerHTML = linkOrden.toString();
    }

    document.querySelector("div[id='modal-ver-orden-de-requerimiento'] span[id='codigo']").textContent = obj.dataset.codigoRequerimiento != null ? obj.dataset.codigoRequerimiento : '';

}