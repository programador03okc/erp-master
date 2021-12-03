let detalle = [];
function limpiarTabla(idElement) {
    let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
    if (nodeTbody != null) {
        while (nodeTbody.children.length > 0) {
            nodeTbody.removeChild(nodeTbody.lastChild);
        }

    }
}

function listarItemsRequerimientoMapeo(id_requerimiento) {
    limpiarTabla('detalleItemsRequerimiento');
    detalle = [];

    $.ajax({
        type: 'GET',
        url: 'itemsRequerimiento/' + id_requerimiento,
        dataType: 'JSON',
        beforeSend: data => {
            $("#modal-mapeoItemsRequerimiento .modal-body").LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
        },
        success: function (response) {
            response.forEach(element => {
                if (element.id_tipo_item == 1) {
                    detalle.push({
                        'id_detalle_requerimiento': element.id_detalle_requerimiento,
                        'id_producto': element.id_producto,
                        'codigo': element.codigo,
                        'part_number': (element.id_producto !== null ? element.part_number_prod : element.part_number),
                        'descripcion': (element.id_producto !== null ? element.descripcion_prod : element.descripcion),
                        'cantidad': element.cantidad,
                        'tiene_transformacion': element.tiene_transformacion,
                        'abreviatura': (element.abreviatura !== null ? element.abreviatura : ''),
                        'id_categoria': null,
                        'id_clasif': null,
                        'id_subcategoria': null,
                        'estado': element.estado
                    });
                }

            });
            mostrar_detalle();
            $("#modal-mapeoItemsRequerimiento .modal-body").LoadingOverlay("hide", true);

        },
        "drawCallback": function (settings) {
            $("#modal-mapeoItemsRequerimiento .modal-body").LoadingOverlay("hide", true);
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function mostrar_detalle() {
    var html = '';
    var i = 1;

    detalle.forEach(element => {
        var pn = element.part_number ?? '';
        var dsc = encodeURIComponent(element.descripcion);
        var link_pn = '';
        var link_des = '';

        if (pn !== null) {
            link_pn = `
            <a href="javascript: void(0);" 
                onclick="openAsignarProducto('`+ pn + `', '` + dsc + `', ` + element.id_detalle_requerimiento + `, 1);">
            `+ pn + `
            </a>`;
        }
        if (dsc !== null) {
            link_des = `
            <a href="javascript: void(0);" 
                onclick="openAsignarProducto('`+ pn + `', '` + dsc + `', ` + element.id_detalle_requerimiento + `, 2);">
            `+ decodeURIComponent(dsc) + `
            </a>`;
        }
        html += `<tr ${element.estado == 7 ? 'class="bg-danger"' : ''}>
            <td>${i}</td>
            <td>${(element.codigo !== null && element.codigo !== '') ? element.codigo :
                ((element.id_categoria !== null && element.id_producto == null) ? '(Por crear)' : '')}</td>
            <td>`+ link_pn + (element.tiene_transformacion ? ' <span class="badge badge-secondary">Transformado</span> ' : '') + `</td>
            <td>`+ link_des + `</td>
            <td>${element.cantidad !== null ? element.cantidad : ''}</td>
            <td>${element.abreviatura !== null ? element.abreviatura : ''}</td>
            <td style="display:flex;">
                <button type="button" style="padding-left:8px;padding-right:7px;" 
                    class="asignar btn btn-xs btn-info boton" data-toggle="tooltip" 
                    data-placement="bottom" data-partnumber="${element.part_number}" 
                    data-desc="${encodeURIComponent(element.descripcion)}" data-id="${element.id_detalle_requerimiento}"
                    title="Asignar producto" >
                    <i class="fas fa-angle-double-right"></i>
                </button>`;
        if (element.estado == 7) {
            html += `
                <button type="button" style="padding-left:8px;padding-right:7px;" 
                    class="anular btn btn-xs btn-danger boton oculto" data-toggle="tooltip" 
                    data-placement="bottom" data-partnumber="${element.part_number}" 
                    data-desc="${encodeURIComponent(element.descripcion)}" data-id="${element.id_detalle_requerimiento}"
                    title="Anular" >
                    <i class="fas fa-times"></i>
                </button>
                <button type="button" title="Restablecer" data-id="${element.id_detalle_requerimiento}" class="restablecer btn-xs btn btn-primary"><i class="fas fa-undo"></i></button>
                `;

        } else {
            html += `
                <button type="button" style="padding-left:8px;padding-right:7px;" 
                    class="anular btn btn-xs btn-danger boton" data-toggle="tooltip" 
                    data-placement="bottom" data-partnumber="${element.part_number}" 
                    data-desc="${encodeURIComponent(element.descripcion)}" data-id="${element.id_detalle_requerimiento}"
                    title="Anular" >
                    <i class="fas fa-times"></i>
                </button>
                `;

        }

        html += `</td>
        </tr>`;
        i++;
    });

    $('#detalleItemsRequerimiento tbody').html(html);

}

$('#detalleItemsRequerimiento tbody').on("click", "button.asignar", function () {
    var partnumber = $(this).data('partnumber');
    var desc = $(this).data('desc');
    var id = $(this).data('id');
    openAsignarProducto(partnumber, desc, id, 0);
});

$('#detalleItemsRequerimiento tbody').on("click", "button.anular", function (e) {
    var partnumber = $(this).data('partnumber');
    var desc = $(this).data('desc');
    var id = $(this).data('id');
    anularProducto(partnumber, desc, id, e.currentTarget);
});
$('#detalleItemsRequerimiento tbody').on("click", "button.restablecer", function (e) {
    var id = $(this).data('id');
    restablecerItemAnulado(id, e.currentTarget);
});

function anularProducto(partnumber, desc, id, obj) {
    detalle.forEach((element, index) => {
        if (element.id_detalle_requerimiento == id) {
            detalle[index].estado = 7;
            Lobibox.notify('success', {
                title: false,
                size: 'mini',
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: `Item anulado. Haga click en guardar para grabar los cambios.`
            });
        }
    });
    obj.closest("tr").classList.add('bg-danger');
    obj.closest("td").querySelector("button[class~='anular']").classList.add("oculto")

    let tdBotoneraAccionMapeo = obj.closest("td");
    if (tdBotoneraAccionMapeo.querySelector("button[class~='restablecer']") == null) {
        let buttonRestablecerItem = document.createElement("button");
        buttonRestablecerItem.type = "button";
        buttonRestablecerItem.dataset.id = id;
        buttonRestablecerItem.title = "Restablecer";
        buttonRestablecerItem.className = "restablecer btn-xs btn btn-primary";
        buttonRestablecerItem.innerHTML = "<i class='fas fa-undo'></i>";
        // buttonRestablecerItem.addEventListener('click', function(){
        //     restablecerItemAnulado(id,obj);
        // });
        // buttonRestablecerItem.addEventListener('click', function () {

        //     detalle.forEach((element, index) => {
        //         if (element.id_detalle_requerimiento == id) {
        //             detalle[index].estado = 1;
        //             Lobibox.notify('info', {
        //                 title: false,
        //                 size: 'mini',
        //                 rounded: true,
        //                 sound: false,
        //                 delayIndicator: false,
        //                 msg: `Item restablecido`
        //             });
        //         }
        //     });

        //     obj.closest("td").querySelector("button[class~='anular']").classList.remove("oculto")
        //     obj.closest("td").querySelector("button[class~='restablecer']").classList.add("oculto")
        //     obj.closest("tr").classList.remove('bg-danger');

        // }, false);
        tdBotoneraAccionMapeo.appendChild(buttonRestablecerItem);
    } else {
        obj.closest("td").querySelector("button[class~='restablecer']").classList.remove("oculto")

    }

}

function restablecerItemAnulado(id, obj) {

    detalle.forEach((element, index) => {
        if (element.id_detalle_requerimiento == id) {
            detalle[index].estado = 1;
            Lobibox.notify('info', {
                title: false,
                size: 'mini',
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: `Item restablecido`
            });
        }
    });

    obj.closest("td").querySelector("button[class~='anular']").classList.remove("oculto")
    obj.closest("td").querySelector("button[class~='restablecer']").classList.add("oculto")
    obj.closest("tr").classList.remove('bg-danger');
}

function openAsignarProducto(partnumber, desc, id, type) {

    $('#part_number').text(partnumber);
    $('#descripcion_producto').text(decodeURIComponent(desc));
    $('[name=id_detalle_requerimiento]').val(id);
    $('[name=part_number]').val(partnumber);
    $('[name=descripcion]').val(decodeURIComponent(desc));
    $('[name=id_tipo_producto]').val(8);
    $('[name=id_categoria]').val('');
    $('[name=id_subcategoria]').val('');
    $('[name=id_clasif]').val(2);
    $('[name=id_unidad_medida]').val(1);
    $('[name=series]').iCheck('uncheck');

    listarProductosCatalogo();
    listarProductosSugeridos(partnumber, decodeURIComponent(desc), type);

    $('#modal-mapeoAsignarProducto').modal('show');
    $('[href="#seleccionar"]').tab('show');
    $('#submit_mapeoAsignarProducto').removeAttr('disabled');
}

$("#form-mapeoItemsRequerimiento").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: '¿Está seguro que desea guardar los productos mapeados?',
        text: "No podrás revertir esto.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Guardar'

    }).then((result) => {
        if (result.isConfirmed) {

            $("#submit_orden_despacho").attr('disabled', 'true');
            let lista = [];
            detalle.forEach(element => {

                lista.push({
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_producto': element.id_producto,
                    'part_number': (element.id_producto !== null ? '' : element.part_number),
                    'descripcion': (element.id_producto !== null ? '' : element.descripcion),
                    'codigo': element.codigo,
                    'cantidad': element.cantidad,
                    'abreviatura': element.abreviatura,
                    'id_categoria': element.id_categoria,
                    'id_clasif': element.id_clasif,
                    'id_subcategoria': element.id_subcategoria,
                    'id_unidad_medida': element.id_unidad_medida,
                    'series': element.series,
                    'estado': element.estado
                });
                // }
            });

            $.ajax({
                type: 'POST',
                url: 'guardar_mapeo_productos',
                data: {
                    detalle: lista
                },
                dataType: 'JSON',
                success: function (response) {
                    if (response.response == 'ok') {
                        // console.log(response);
                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje.toString()
                        });
                        $('#modal-mapeoItemsRequerimiento').modal('hide');

                        if (objBtnMapeo != undefined) {
                            let cantidadPorMapear = parseInt(response.cantidad_total_items) - parseInt(response.cantidad_items_mapeados);
                            // console.log(objBtnMapeo.closest("div"));
                            // console.log(cantidadTotalItemBase);
                            // console.log(contidadMapeado);
                            if (response.cantidad_items_mapeados > 0) {
                                let divBtnGroup = objBtnMapeo.closest("div");
                                let idRequerimiento = document.querySelector("form[id='form-mapeoItemsRequerimiento'] input[name='id_requerimiento']").value;

                                if (divBtnGroup.querySelector("button[name='btnOpenModalAtenderConAlmacen']") == null) {
                                    let btnOpenModalAtenderConAlmacen = document.createElement("button");
                                    btnOpenModalAtenderConAlmacen.type = "button";
                                    btnOpenModalAtenderConAlmacen.name = "btnOpenModalAtenderConAlmacen";
                                    btnOpenModalAtenderConAlmacen.className = "btn btn-primary btn-xs handleClickAtenderConAlmacen";
                                    btnOpenModalAtenderConAlmacen.title = "Reserva en almacén";
                                    btnOpenModalAtenderConAlmacen.dataset.idRequerimiento = idRequerimiento;
                                    btnOpenModalAtenderConAlmacen.innerHTML = "<i class='fas fa-dolly fa-sm'></i>";
                                    divBtnGroup.appendChild(btnOpenModalAtenderConAlmacen);
                                }
                                if (divBtnGroup.querySelector("button[name='btnCrearOrdenCompraPorRequerimiento']") == null) {
                                    let btnCrearOrdenCompraPorRequerimiento = document.createElement("button");
                                    btnCrearOrdenCompraPorRequerimiento.type = "button";
                                    btnCrearOrdenCompraPorRequerimiento.name = "btnCrearOrdenCompraPorRequerimiento";
                                    btnCrearOrdenCompraPorRequerimiento.className = "btn btn-warning btn-xs handleClickCrearOrdenCompraPorRequerimiento";
                                    btnCrearOrdenCompraPorRequerimiento.title = "Crear Orden de Compra";
                                    btnCrearOrdenCompraPorRequerimiento.dataset.idRequerimiento = idRequerimiento;
                                    btnCrearOrdenCompraPorRequerimiento.innerHTML = "<i class='fas fa-file-invoice'></i>";
                                    divBtnGroup.appendChild(btnCrearOrdenCompraPorRequerimiento);

                                }
                            }

                            // actualizar cantidad de items por mapear 
                            objBtnMapeo.querySelector("span[class='badge']").textContent = cantidadPorMapear;
                            objBtnMapeo.closest("tr").querySelector("input[type='checkbox']").dataset.mapeosPendientes = cantidadPorMapear;
                            objBtnMapeo.closest("tr").querySelector("input[type='checkbox']").dataset.mapeados = response.cantidad_items_mapeados;

                            if (response.estado_requerimiento != null && response.estado_requerimiento.hasOwnProperty('descripcion')) {
                                objBtnMapeo.closest("tr").querySelector("span[class~='estadoRequerimiento']").textContent = response.estado_requerimiento.descripcion;

                            }

                        }

                        if (document.querySelector("div[id='modal-por-regularizar']").classList.contains('in') == true) {
                            construirTablaItemsPorRegularizar(document.querySelector("div[id='modal-mapeoItemsRequerimiento'] input[name='id_requerimiento']").value); // Regularizar.js
                        }

                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar guardar el mapeo de producto(s), por favor vuelva a intentarlo',
                    'error'
                );
                console.log(textStatus);
                console.log(errorThrown);
            });


        }
    })
});