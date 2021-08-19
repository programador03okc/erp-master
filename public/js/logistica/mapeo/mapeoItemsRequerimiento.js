let detalle = [];

function listarItemsRequerimientoMapeo(id_requerimiento) {
    detalle = [];

    $.ajax({
        type: 'GET',
        url: 'itemsRequerimiento/' + id_requerimiento,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);
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
                    });
                }

            });
            mostrar_detalle();
        }
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
        var pn = element.part_number;
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
        html += `<tr>
            <td>${i}</td>
            <td>${(element.codigo !== null && element.codigo !== '') ? element.codigo :
                ((element.id_categoria !== null && element.id_producto == null) ? '(Por crear)' : '')}</td>
            <td>`+ link_pn + (element.tiene_transformacion ? ' <span class="badge badge-secondary">Transformado</span> ' : '') + `</td>
            <td>`+ link_des + `</td>
            <td>${element.cantidad !== null ? element.cantidad : ''}</td>
            <td>${element.abreviatura !== null ? element.abreviatura : ''}</td>
            <td>
                <button type="button" style="padding-left:8px;padding-right:7px;" 
                    class="asignar btn btn-info boton" data-toggle="tooltip" 
                    data-placement="bottom" data-partnumber="${element.part_number}" 
                    data-desc="${encodeURIComponent(element.descripcion)}" data-id="${element.id_detalle_requerimiento}"
                    title="Asignar producto" >
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </td>
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

function openAsignarProducto(partnumber, desc, id, type) {

    $('#part_number').text(partnumber);
    $('#descripcion').text(decodeURIComponent(desc));
    $('[name=id_detalle_requerimiento]').val(id);
    $('[name=part_number]').val(partnumber);
    $('[name=descripcion]').val(decodeURIComponent(desc));
    $('[name=id_tipo_producto]').val('');
    $('[name=id_categoria]').val('');
    $('[name=id_subcategoria]').val('');
    $('[name=id_clasif]').val(5);
    $('[name=id_unidad_medida]').val(1);

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
            let contidadMapeado = 0;
            let cantidadTotalItem = detalle.length;
            detalle.forEach(element => {
                if (element.id_producto != null) {
                    contidadMapeado++;
                }
                // if (element.id_categoria!==null){
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
                });
                // }
            });

            // let data = 'detalle='+JSON.stringify(lista);

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
                            msg: `Productos mapeados con éxito`
                        });
                        $('#modal-mapeoItemsRequerimiento').modal('hide');

                        if (objBtnMapeo != undefined) {
                            let cantidadPorMapear = parseInt(cantidadTotalItem) - parseInt(contidadMapeado);
                            // console.log(objBtnMapeo.closest("div"));
                            // console.log(cantidadTotalItem);
                            // console.log(contidadMapeado);
                            let divBtnGroup = objBtnMapeo.closest("div");
                            let idRequerimiento = document.querySelector("form[id='form-mapeoItemsRequerimiento'] input[name='id_requerimiento']").value;

                            if( divBtnGroup.querySelector("button[name='btnOpenModalAtenderConAlmacen']") == null){
                                let btnOpenModalAtenderConAlmacen = document.createElement("button");
                                btnOpenModalAtenderConAlmacen.type = "button";
                                btnOpenModalAtenderConAlmacen.name = "btnOpenModalAtenderConAlmacen";
                                btnOpenModalAtenderConAlmacen.className = "btn btn-primary btn-xs handleClickAtenderConAlmacen";
                                btnOpenModalAtenderConAlmacen.title = "Reserva en almacén";
                                btnOpenModalAtenderConAlmacen.dataset.idRequerimiento = idRequerimiento;
                                btnOpenModalAtenderConAlmacen.innerHTML = "<i class='fas fa-dolly fa-sm'></i>";
                                divBtnGroup.appendChild(btnOpenModalAtenderConAlmacen);
                            }
                            if( divBtnGroup.querySelector("button[name='btnCrearOrdenCompraPorRequerimiento']")== null){
                                let btnCrearOrdenCompraPorRequerimiento = document.createElement("button");
                                btnCrearOrdenCompraPorRequerimiento.type = "button";
                                btnCrearOrdenCompraPorRequerimiento.name = "btnCrearOrdenCompraPorRequerimiento";
                                btnCrearOrdenCompraPorRequerimiento.className = "btn btn-warning btn-xs handleClickCrearOrdenCompraPorRequerimiento";
                                btnCrearOrdenCompraPorRequerimiento.title = "Crear Orden de Compra";
                                btnCrearOrdenCompraPorRequerimiento.dataset.idRequerimiento = idRequerimiento;
                                btnCrearOrdenCompraPorRequerimiento.innerHTML = "<i class='fas fa-file-invoice'></i>";
                                divBtnGroup.appendChild(btnCrearOrdenCompraPorRequerimiento);
                                
                            }

                            // actualizar cantidad de items por mapear 
                            objBtnMapeo.querySelector("span[class='badge']").textContent = cantidadPorMapear;
                            objBtnMapeo.closest("tr").querySelector("input[type='checkbox']").dataset.mapeosPendientes = cantidadPorMapear;

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