let detalle_requerimiento = [];
let detalle_ingresa = [];
let detalle_sale = [];
let tab_origen = null;

function open_despacho_create(data) {
    $('#modal-orden_despacho_interno_create').modal({
        show: true
    });
    console.log('open_despacho_create');
    console.log(data);
    $("#submit_orden_despacho").removeAttr("disabled");
    $('[name=tipo_entrega]').val('MISMA CIUDAD').trigger('change.select2');
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=tiene_transformacion]').val(data.tiene_transformacion ? 'si' : 'no');
    $('[name=id_almacen]').val((data.id_almacen !== null && data.id_almacen !== 0) ? data.id_almacen : '');
    $('[name=almacen_descripcion]').val(data.almacen_descripcion !== null ? data.almacen_descripcion : '');
    $('[name=id_sede]').val(data.sede_requerimiento !== null ? data.sede_requerimiento : '');
    $('[name=id_cc]').val(data.id_cc);
    $('[name=descripcion_sobrantes]').val('');

    // $("#detalleItemsReq").hide();
    // $("#despachoExterno").show();

    $('#detalleSale tbody').html('');

    detalle_requerimiento = [];
    detalle_ingresa = [];
    detalle_sale = [];

    detalleRequerimiento(data.id_requerimiento).then(function (response) {

        var html = '';
        var almacenes = [];
        var almacenes_des = [];
        var despachos_pendientes = 0;
        var almacenes_ext = [];
        var almacenes_ext_des = [];
        var despachos_ext_pendientes = 0;

        console.log(response);
        response.forEach(element => {
            // var ing = (element.suma_ingresos !== null ? parseFloat(element.suma_ingresos) : 0);//ingresos por compra
            // var tran = (element.suma_transferencias_recibidas !== null ? parseFloat(element.suma_transferencias_recibidas) : 0);//ingresos por transferencias recibidas
            var stock = (element.stock_comprometido !== null ? element.stock_comprometido : 0);
            var cant = /*ing +*/ stock - (element.suma_despachos_internos !== null ? parseFloat(element.suma_despachos_internos) : 0);

            if (!element.tiene_transformacion) {

                if (cant > 0) {
                    despachos_pendientes++;
                    var partes = (element.cc_pn == null && element.cc_des == null && element.cc_com == null) ? true : false;

                    html += '<tr id="' + element.id_detalle_requerimiento + '">' +
                        '<td>' + (partes
                            ? '<span class="label label-info">Partes y piezas</span>'
                            : '<span class="label label-success">Item Base</span>') + '</td>' +
                        '<td>' + (element.producto_codigo !== null ? element.producto_codigo : '') + '</td>' +
                        '<td>' + (element.part_number !== null ? element.part_number : '') + '</td>' +
                        '<td>' + (element.producto_descripcion !== null ? element.producto_descripcion : element.descripcion_adicional) + '</td>' +
                        // '<td>'+(element.almacen_descripcion !== null ? element.almacen_descripcion : '')+'</td>'+
                        '<td>' + element.cantidad + '</td>' +
                        '<td>' + (element.abreviatura !== null ? element.abreviatura : '') + '</td>' +
                        '<td>' + (stock) + '</td>' +
                        '<td>' + (element.suma_despachos_internos !== null ? element.suma_despachos_internos : '0') + '</td>' +
                        '<td><input type="number" id="' + element.id_detalle_requerimiento + 'cantidad" value="' + cant + '" max="' + cant + '" min="0" style="width: 80px;"/></td>' +
                        '<td><span class="label label-' + element.bootstrap_color + '">' + element.estado_doc + '</span></td>' +
                        '<td>' + (!partes ? '<i class="fas fa-exchange-alt boton btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ver Instrucciones segun Mgc" onClick="verInstrucciones(' + element.id_detalle_requerimiento + ');"></i>' : '') +
                        '</td></tr>';
                } else {
                    if (element.estado !== 28 && element.estado !== 10) {//En Almacen Total o Culminado
                        despachos_pendientes++;
                    }
                }
                // (element.series ? '<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Ver Series" onClick="verSeries(' + element.id_detalle_requerimiento + ');"></i>' : '') +
                detalle_ingresa.push({
                    'id_reserva': element.id_reserva,
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_producto': element.id_producto,
                    'cantidad': element.cantidad,
                    'suma_ingresos': element.suma_ingresos,
                    'suma_despachos': element.suma_despachos_internos,
                    'stock_comprometido': element.stock_comprometido,
                    'valorizacion': element.valorizacion,
                });

                if (element.id_almacen_reserva !== null) {
                    if (!almacenes.includes(element.id_almacen_reserva)) {
                        almacenes.push(element.id_almacen_reserva);
                        almacenes_des.push(element.almacen_reserva_descripcion);
                    }
                }
                else if (element.id_almacen_guia_com !== null) {
                    if (!almacenes.includes(element.id_almacen_guia_com)) {
                        almacenes.push(element.id_almacen_guia_com);
                        almacenes_des.push(element.almacen_guia_com_descripcion);
                    }
                }
            }
            else {
                if ((element.estado == 28 || element.estado == 19 || element.estado == 10) && element.id_almacen_reserva !== null) {
                    if (!almacenes_ext.includes(element.id_almacen_reserva)) {
                        almacenes_ext.push(element.id_almacen_reserva);
                        almacenes_ext_des.push(element.almacen_reserva_descripcion);
                    }
                    despachos_ext_pendientes++;
                }
                detalle_sale.push({
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_producto': element.id_producto,
                    'part_number': element.part_number,
                    'codigo': element.producto_codigo,
                    'descripcion': element.producto_descripcion,
                    'id_unidad_medida': element.id_unidad_medida,
                    'abreviatura': element.abreviatura,
                    'cantidad': element.cantidad,
                });
            }
        });

        $('#detalleRequerimientoOD tbody').html(html);
        mostrarSale();
        console.log(almacenes_des);
        console.log('despachos_pendientes: ' + despachos_pendientes);

        console.log(almacenes_ext);
        console.log(almacenes_ext_des);
        console.log('despachos_ext_pendientes: ' + despachos_ext_pendientes);
        console.log(data);

        if (data.tiene_transformacion) {
            // data.count_despachos_internos == 0
            if (despachos_pendientes > 0) {
                if (almacenes.length == 1) {
                    $('[name=id_almacen]').val(almacenes[0]);
                    $('[name=almacen_descripcion]').val(almacenes_des[0]);
                    // $('[name=aplica_cambios]').prop('checked', true);
                    // on();
                }
                else if (almacenes.length == 0) {
                    Swal.fire({
                        title: "Es necesario que los productos estén en almacén",
                        icon: "warning"
                    });
                    $('#modal-orden_despacho_interno_create').modal('hide');
                }
                else {
                    console.log(almacenes_des);
                    Swal.fire({
                        title: 'Los productos no pueden estar en más de un Almacén: \n' + almacenes_des + '. Es necesario realizar una transferencia.',
                        icon: "warning"
                    });
                    $('#modal-orden_despacho_interno_create').modal('hide');
                }
            } else {
                if (despachos_ext_pendientes > 0) {
                    if (almacenes_ext.length == 1) {
                        var id_alm = $('[name=id_almacen]').val();

                        if (parseInt(almacenes_ext[0]) !== parseInt(id_alm)) {//revisar
                            Swal.fire({
                                title: 'El almacén es diferente. Debe realizar una transferencia. ' + almacenes_ext_des[0],
                                icon: "warning"
                            });
                            $('#modal-orden_despacho_interno_create').modal('hide');
                        }
                    }
                    else if (almacenes_ext.length == 0) {
                        Swal.fire({
                            title: 'Es necesario que los productos transformados esten en almacén.',
                            icon: "warning"
                        });
                        $('#modal-orden_despacho_interno_create').modal('hide');
                    }
                    else {
                        console.log(almacenes_ext_des);
                        Swal.fire({
                            title: 'Los productos transformados no pueden estar en más de un Almacén: \n' + almacenes_ext_des + ' es necesario realizar una transferencia.',
                            icon: "warning"
                        });
                        $('#modal-orden_despacho_interno_create').modal('hide');
                    }
                }
            }
            // } else {
            //     if (despachos_pendientes > 0) {
            //         if (almacenes.length == 1) {
            //             var id_alm = $('[name=id_almacen]').val();

            //             if (parseInt(almacenes[0]) !== parseInt(id_alm)) {
            //                 alert('El almacén es diferente. Debe realizar una transferencia. ' + almacenes_des[0]);
            //                 $('#modal-orden_despacho_interno_create').modal('hide');
            //             } else {
            //                 $('[name=aplica_cambios]').prop('checked', false);
            //                 off();
            //             }
            //         }
            //         else if (almacenes.length == 0) {
            //             alert('Es necesario que los productos esten en almacen.');
            //             $('#modal-orden_despacho_interno_create').modal('hide');
            //         }
            //         else {
            //             console.log(almacenes_des);
            //             alert('Los productos no pueden estar en más de un Almacén: \n' + almacenes_des);
            //             $('#modal-orden_despacho_interno_create').modal('hide');
            //         }
            //     }
        }

    }).catch(function (err) {
        console.log(err)
    });

}

function detalleRequerimiento(id_requerimiento) {

    return new Promise(function (resolve, reject) {

        $.ajax({
            type: 'GET',
            url: 'verDetalleRequerimientoDI/' + id_requerimiento,
            dataType: 'JSON',
            success(response) {
                console.log('promesa');
                resolve(response); // Resolve promise and go to then()
            },
            error: function (err) {
                reject(err) // Reject the promise and go to catch()
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });
}

function verSeries(id_detalle_requerimiento) {
    if (id_detalle_requerimiento !== null) {
        $.ajax({
            type: 'GET',
            url: 'verSeries/' + id_detalle_requerimiento,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                $('#modal-ver_series').modal({
                    show: true
                });
                var tr = '';
                var i = 1;
                response.forEach(element => {
                    tr += `<tr id="reg-${element.serie}">
                            <td class="numero">${i}</td>
                            <td><input type="text" class="oculto" name="series" value="${element.serie}"/>${element.serie}</td>
                            <td>${element.serie_guia_com}-${element.numero_guia_com}</td>
                            <td>${element.serie_guia_ven !== null ? (element.serie_guia_ven + '-' + element.numero_guia_ven) : ''}</td>
                        </tr>`;
                    i++;
                });
                $('#listaSeries tbody').html(tr);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

$("#form-orden_despacho").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var msj = validaOrdenDespacho();
    var json_detalle_ingresa = [];
    var json_detalle_sale = [];
    var validaCampos = '';

    if (msj.length > 0) {
        Swal.fire({
            title: msj,
            icon: "warning"
        });
    }
    else {
        var serial = $(this).serialize();
        // var doc = $('input[name=optionsRadios]:checked').val();

        // $("#detalleRequerimientoOD input[type=checkbox]:checked").each(function () {
        //     var id_detalle_requerimiento = $(this).val();
        //     var json = detalle_ingresa.find(element => element.id_detalle_requerimiento == id_detalle_requerimiento);


        //     json_detalle_ingresa.push({
        //         'cantidad': $(this).parent().parent().find('td input[id=' + id_detalle_requerimiento + 'cantidad]').val(),
        //         'id_detalle_requerimiento': json.id_detalle_requerimiento,
        //         'id_producto': json.id_producto,
        //         'valorizacion': json.valorizacion,
        //     });
        // });

        detalle_sale.forEach(element => {
            // var id_producto = $(this)[0].id;
            // var json = detalle_sale.find(element => element.id_producto == id_producto);
            // var cant = $(this).parent().parent().find('td input[type=number]').val();

            // if (cant == '' || cant == null) {
            //     validaCampos += 'El producto ' + json.descripcion + ' requiere que cantidad.\n';
            // }
            json_detalle_sale.push({
                'id_detalle_requerimiento': element.id_detalle_requerimiento,
                'cantidad': element.cantidad,
                'id_producto': element.id_producto
            });
        });

        console.log(json_detalle_ingresa);
        console.log(json_detalle_sale);

        if (validaCampos.length > 0) {
            Swal.fire({
                title: validaCampos,
                icon: "warning"
            });
        } else {
            // var ac = $('[name=aplica_cambios_valor]').val();
            // var m = '';
            // if (ac == 'si') {
            //     if (json_detalle_ingresa.length == 0) {
            //         m = 'No ha seleccionado items para despachar.';
            //     }
            //     if (json_detalle_sale.length == 0) {
            //         m += '\nNo ha ingresado items transformados.';
            //     }
            // } else {
            //     if (detalle_requerimiento.length == 0) {
            //         m = 'No hay items para despachar.';
            //     }
            // }
            // if (m == '') {

            Swal.fire({
                title: "¿Está seguro que desea guardar ésta Orden de Transformación?",
                text: "No podrás revertir esto.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00a65a", //"#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "Cancelar",
                confirmButtonText: "Sí. Guardar"
            }).then(result => {
                if (result.isConfirmed) {
                    var data = serial + '&detalle_ingresa=' + JSON.stringify(detalle_ingresa) +
                        '&detalle_sale=' + JSON.stringify(json_detalle_sale);
                    console.log(data);
                    guardar_orden_despacho(data);
                }
            });
            // } else {
            //     alert(m);
            // }
        }
    }
});

function guardar_orden_despacho(data) {
    $("#submit_orden_despacho").attr('disabled', 'true');

    $.ajax({
        type: 'POST',
        url: 'guardarOrdenDespachoInterno',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                // width: 500,
                msg: 'Orden de Despacho guardada con éxito.'
            });
            $('#modal-orden_despacho_interno_create').modal('hide');
            listarRequerimientosPendientes();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function changeCheckIngresa(checkbox, id_detalle_requerimiento) {
//     console.log(checkbox.checked + ' id_detalle_requerimiento' + id_detalle_requerimiento);
//     if (checkbox.checked) {
//         var nuevo = detalle_requerimiento.find(element => element.id_detalle_requerimiento == id_detalle_requerimiento);
//         detalle_ingresa.push(nuevo);
//     } else {
//         var index = detalle_ingresa.findIndex(function (item, i) {
//             return item.id_detalle_requerimiento == id_detalle_requerimiento;
//         });
//         detalle_ingresa.splice(index, 1);
//     }
//     console.log(detalle_ingresa);
// }

function validaOrdenDespacho() {
    var tpcli = $('[name=tipo_cliente]').val();
    var clie = $('[name=id_cliente]').val();
    var perso = $('[name=id_persona]').val();
    var ubig = $('[name=ubigeo]').val();
    var dir = $('[name=direccion_destino]').val();
    var telf = $('[name=telefono_cliente]').val();
    var msj = '';

    if (tpcli == 1) {
        if (perso == '') {
            msj += '\n Es necesario que ingrese los datos del Cliente';
        }
    } else if (tpcli == 2) {
        if (clie == '') {
            msj += '\n Es necesario que ingrese los datos del Cliente';
        }
    }
    if (ubig == '') {
        msj += '\n Es necesario que ingrese un Ubigeo Destino';
    }
    if (dir == '') {
        msj += '\n Es necesario que ingrese una Dirección Destino';
    }
    if (telf == '') {
        msj += '\n Es necesario que ingrese un Teléfono';
    }

    return msj;
}

function mostrarSale() {
    var html = '';
    var i = 1;
    detalle_sale.forEach(element => {
        html += `<tr id="${element.id_producto}">
        <td>${(element.codigo !== null ? element.codigo : '')}</td>
        <td>${(element.part_number !== null ? element.part_number : '')}</td>
        <td>${element.descripcion}</td>
        <td><input type="number" id="" value="${element.cantidad ? element.cantidad : '1'}" style="width: 80px;"/></td>
        <td>${(element.abreviatura !== null ? element.abreviatura : '')}</td>
        </tr>`;
        i++;
    });
    $('#detalleSale tbody').html(html);
}

// function ceros_numero(numero) {
//     if (numero == 'numero') {
//         var num = $('[name=numero]').val();
//         $('[name=numero]').val(leftZero(7, num));
//     }
//     else if (numero == 'serie') {
//         var num = $('[name=serie]').val();
//         $('[name=serie]').val(leftZero(4, num));
//     }
// }

// $('#detalleSale tbody').on("click", ".delete", function () {
//     var anula = confirm("¿Esta seguro que desea anular éste item?");

//     if (anula) {
//         var idx = $(this).parents("tr")[0].id;
//         var index = detalle_sale.findIndex(function (item, i) {
//             return parseInt(item.id_producto) == parseInt(idx);
//         });
//         console.log(index);
//         if (index !== -1) {
//             detalle_sale.splice(index, 1);
//         }
//         $(this).parents("tr").remove();
//         console.log('idx' + idx);
//     }
// });
// function eliminarProductoSale(){
//     var idx = $(this).parents("tr")[0].id;
//     $(this).parents("tr").remove();
// }