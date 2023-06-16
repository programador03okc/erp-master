let json_series = [];
let cant_items = null;

function agrega_series(id_oc_det) {
    const $modal = $('#modal-guia_com_barras');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    $('#listaBarras tbody').html('');
    json_series = [];

    var json = oc_det_seleccionadas.find(element => element.id_oc_det == id_oc_det);
    console.log(json);

    if (json !== null) {
        if (json.series.length > 0) {
            json_series = json.series;
            cargar_series();
        }
    }

    cant_items = $("#" + id_oc_det + "cantidad").val();

    $('[name=id_guia_com_det]').val('');
    $('[name=id_oc_det]').val(id_oc_det);
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_detalle_devolucion]').val('');
    $('[name=id_trans_detalle]').val('');
    $('[name=id_producto_sobrante]').val('');
    $('[name=id_producto_transformado]').val('');
    $('[name=serie_prod]').val('');
    $('.cabecera').show();
}

function agrega_series_transformacion(id) {
    const $modal = $('#modal-guia_com_barras');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    $('#listaBarras tbody').html('');
    json_series = [];

    var json = series_transformacion.find(element => element.id == id);
    console.log(json);

    if (json !== null) {
        if (json.series.length > 0) {
            json_series = json.series;
            cargar_series();
        }
    }
    cant_items = (json !== null ? json.cantidad : 0);

    $('[name=id_guia_com_det]').val('');
    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val(id);
    $('[name=id_detalle_devolucion]').val('');
    $('[name=id_trans_detalle]').val('');
    $('[name=id_producto_sobrante]').val('');
    $('[name=id_producto_transformado]').val('');
    $('[name=serie_prod]').val('');
    $('.cabecera').show();
}

function agrega_series_devolucion(id) {
    const $modal = $('#modal-guia_com_barras');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    $('#listaBarras tbody').html('');
    json_series = [];

    var json = detalle_devolucion.find(element => element.id_detalle == id);
    console.log(json);

    if (json !== null) {
        if (json.series.length > 0) {
            json_series = json.series;
            cargar_series();
        }
    }
    cant_items = (json !== null ? json.cantidad : 0);

    $('[name=id_guia_com_det]').val('');
    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_detalle_devolucion]').val(id);
    $('[name=id_trans_detalle]').val('');
    $('[name=id_producto_sobrante]').val('');
    $('[name=id_producto_transformado]').val('');
    $('[name=serie_prod]').val('');
    $('.cabecera').show();
}

function agrega_series_producto(id) {
    console.log('agrega_series_producto' + id);
    $('#modal-guia_com_barras').modal({
        show: true
    });
    $('#listaBarras tbody').html('');
    json_series = [];

    var json = oc_det_seleccionadas.find(element => element.id_producto == id);
    console.log(json);

    if (json !== null) {

        if (json.series.length > 0) {
            json_series = json.series;
            cargar_series();
        }
    }

    cant_items = $("#p" + id + "cantidad").val();

    $('[name=id_guia_com_det]').val('');
    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_detalle_devolucion]').val('');
    $('[name=id_trans_detalle]').val('');
    $('[name=id_producto_sobrante]').val(id);
    $('[name=id_producto_transformado]').val('');
    $('[name=serie_prod]').val('');
    $('.cabecera').show();
}

function agrega_series_guia(id_guia_com_det, cantidad, id_producto, id_almacen) {
    $('#modal-guia_com_barras').modal({
        show: true
    });
    $('#listaBarras tbody').html('');
    json_series = [];

    var json = guia_detalle.find(element => element.id_guia_com_det == id_guia_com_det);
    console.log(json);

    if (json !== null) {
        if (json.series.length > 0) {
            json.series.forEach(element => {
                json_series.push(element.serie);
            });
            cargar_series();
        }
    }

    cant_items = cantidad;

    $('[name=id_guia_com_det]').val(id_guia_com_det);
    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_detalle_devolucion]').val('');
    $('[name=id_trans_detalle]').val('');
    $('[name=id_producto]').val(id_producto);
    $('[name=id_producto_sobrante]').val('');
    $('[name=id_producto_transformado]').val('');
    $('[name=id_almacen_detalle]').val(id_almacen);
    $('[name=serie_prod]').val('');
    $('[name=edit]').val('false');
    $('.cabecera').show();
}

function agrega_series_sobrante(id, cantidad) {
    console.log('agrega_series_sobrante' + id);
    $('#modal-guia_com_barras').modal({
        show: true
    });
    $('#listaBarras tbody').html('');
    json_series = [];

    var json = items_sobrante.find(element => element.id_producto == id);
    console.log(json);

    if (json !== null) {

        if (json.series.length > 0) {
            var nuevaSeries = [];
            json.series.forEach(serie => {
                nuevaSeries.push(serie.serie);
            });
            json_series = nuevaSeries;
            cargar_series();
        }
    }

    cant_items = cantidad;

    $('[name=id_guia_com_det]').val('');
    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_detalle_devolucion]').val('');
    $('[name=id_trans_detalle]').val('');
    $('[name=id_producto_sobrante]').val(id);
    $('[name=id_producto_transformado]').val('');
    $('[name=serie_prod]').val('');
    $('.cabecera').show();
}

function agrega_series_transformado(id, cantidad) {
    $('#frm-example')[0].reset();

    console.log('agrega_series_transformado' + id);
    $('#modal-guia_com_barras').modal({
        show: true
    });
    $('#listaBarras tbody').html('');
    json_series = [];

    var json = items_transformado.find(element => element.id_producto == id);
    console.log(json);

    if (json !== null) {

        if (json.series.length > 0) {
            var nuevaSeries = [];
            json.series.forEach(serie => {
                nuevaSeries.push(serie.serie);
            });
            json_series = nuevaSeries;
            cargar_series();
        }
    }

    cant_items = cantidad;

    $('[name=id_guia_com_det]').val('');
    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_detalle_devolucion]').val('');
    $('[name=id_trans_detalle]').val('');
    $('[name=id_producto_sobrante]').val('');
    $('[name=id_producto_transformado]').val(id);
    $('[name=serie_prod]').val('');
    $('.cabecera').show();
}

function cargar_series() {
    var tr = '';
    var i = 1;

    json_series.forEach(serie => {
        tr += `<tr id="reg-${serie}">
            <td hidden>0</td>
            <td class="numero">${i}</td>
            <td><input type="text" class="oculto" name="series" value="${serie}"/>${serie}</td>
            <td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('${serie}');"></i></td>
        </tr>`;
        i++;
    });
    $('#listaBarras tbody').html(tr);
    $('[name=serie_prod]').focus();
}

function handleKeyPress(event) {
    var exeptuados = ['/', '"', "'", '*', '+', '#', '$', '%', '&', '(', ')', '=', '?', '¿', '¡', '!', '.', '¨', '^', '´', '`', '_', ',', ';', '>', '<', '|', '°', '¬'];

    if (event.which == 13) {
        agregar_serie();
    }
    else if (exeptuados.includes(event.key)) {
        var valor = $('[name=serie_prod]').val();
        valor = valor.substring(0, valor.length - 1);

        $('[name=serie_prod]').val(valor);
        // event.returnValue = false;
        Swal.fire('Valor No Permitido: ' + valor, "", "warning");

    }
}

function agregar_serie() {
    var serie = $('[name=serie_prod]').val().trim();

    if (serie !== '') {
        var agrega = false;
        if (json_series.length > 0) {
            const found = json_series.find(element => element == serie);

            if (found == undefined) {
                agrega = true;
            }
        } else {
            agrega = true;
        }

        if (agrega) {
            var cant = $('#listaBarras tbody tr').length + 1;
            var td = '<tr id="reg-' + serie + '"><td hidden>0</td><td class="numero">' + cant + '</td><td><input type="text" class="oculto" name="series" value="' + serie + '"/>' + serie + '</td><td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie(' + "'" + serie + "'" + ');"></i></td></tr>';
            console.log('cant:' + cant + ' items:' + cant_items);

            if (cant <= cant_items) {
                $('#listaBarras tbody').append(td);
                $('[name=serie_prod]').val('');
                // var id_oc_det = $('[name=id_oc_det]').val();
                json_series.push(serie);
            } else {
                Swal.fire('Ha superado la cantidad del producto!\nYa no puede agregar mas series.', "", "warning");
            }
        } else {
            $('[name=serie_prod]').val('');
        }
    } else {
        Swal.fire('El campo serie esta vacío!', "", "warning");
    }
}

function eliminar_serie(serie) {
    Swal.fire({
        title: "¿Esta seguro que desea eliminar la serie " + serie,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Eliminar"
    }).then(result => {
        if (result.isConfirmed) {
            var index = json_series.findIndex(function (item, i) {
                return item == serie;
            });
            console.log(json_series);
            json_series.splice(index, 1);
            cargar_series();
        }
    });
}

function guardar_series_compra() {
    var id_guia_com_det = $('[name=id_guia_com_det]').val();
    var id_oc_det = $('[name=id_oc_det]').val();
    var id_sobrante = $('[name=id_producto_sobrante]').val();
    var id_transformado = $('[name=id_producto_transformado]').val();
    var id_detalle_transformacion = $('[name=id_detalle_transformacion]').val();
    var id_detalle_devolucion = $('[name=id_detalle_devolucion]').val();
    var edit = $('[name=edit]').val();

    if (id_oc_det !== '') {
        var json = oc_det_seleccionadas.find(element => element.id_oc_det == id_oc_det);

        if (json !== null) {
            json.series = json_series;
        }
        console.log(json);
        console.log(oc_det_seleccionadas);
        mostrar_ordenes_seleccionadas();
        $('#modal-guia_com_barras').modal('hide');
    }
    else if (id_detalle_transformacion !== '') {
        var json = series_transformacion.find(element => element.id == id_detalle_transformacion);

        if (json !== null) {
            json.series = json_series;
        }
        console.log(json);
        console.log(series_transformacion);
        mostrar_detalle_transformacion();
        $('#modal-guia_com_barras').modal('hide');
    }
    else if (id_sobrante !== '') {
        var json = items_sobrante.find(element => element.id_producto == id_sobrante);

        if (json !== null) {
            var nuevaSeries = [];
            json_series.forEach(serie => {
                nuevaSeries.push({
                    'id_prod_serie': 0,
                    'serie': serie
                });
            });
            json.series = nuevaSeries;
        }
        console.log(json);
        console.log(items_sobrante);
        mostrarProductoSobrante();
        $('#modal-guia_com_barras').modal('hide');
    }
    else if (id_transformado !== '') {
        var json = items_transformado.find(element => element.id_producto == id_transformado);

        if (json !== null) {
            var nuevaSeries = [];
            json_series.forEach(serie => {
                nuevaSeries.push({
                    'id_prod_serie': 0,
                    'serie': serie
                });
            });
            json.series = nuevaSeries;
        }
        console.log(json);
        console.log(items_transformado);
        mostrarProductoTransformado();
        $('#modal-guia_com_barras').modal('hide');
    }
    else if (id_detalle_devolucion !== '') {
        var json = detalle_devolucion.find(element => element.id_detalle == id_detalle_devolucion);

        if (json !== null) {
            json.series = json_series;
        }
        console.log(json);
        console.log(detalle_devolucion);
        mostrar_detalle_devolucion();
        $('#modal-guia_com_barras').modal('hide');
    }
    else if (id_guia_com_det !== '' && edit == 'true') {
        let series = [];
        let repetidos = 0;
        let vacios = 0;

        $('#listaBarras tbody tr').each(function (index) {
            let serie = $(this).find('[name=series]').val();
            let id = $(this)[0].id;
            var json = series.find(element => element.serie.trim() == serie.trim());
            $(this).find('[name=series]').val(serie.trim());
            console.log(json);

            if (json !== undefined) {
                repetidos++;
                $(this).find('[name=series]').addClass('resaltar');
            } else {
                if (serie.trim() == '') {
                    vacios++;
                    $(this).find('[name=series]').addClass('resaltar');
                } else {
                    series.push({
                        'id_guia_com_det': id_guia_com_det,
                        'id_prod_serie': id,
                        'serie': serie.trim()
                    });
                    $(this).find('[name=series]').removeClass('resaltar');
                }
            }
        });

        if (repetidos > 0 || vacios > 0) {
            Swal.fire(`${(repetidos > 0 ? 'Hay ' + repetidos + ' repetido(s).' : '') + (vacios > 0 ? ' Hay ' + vacios + ' vacio(s)' : '')} Revise las series ingresadas!`, "", "warning");
        } else {
            var data = 'series=' + JSON.stringify(series);
            console.log(data);

            $.ajax({
                type: 'POST',
                url: 'actualizar_series',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    var id = $('[name=id_guia_com_detalle]').val();
                    listar_detalle_movimiento(id);
                    Lobibox.notify("success", {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: "Se actualizaron las series con éxito."
                    });
                    $('#modal-guia_com_barras').modal('hide');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    }
    else if (id_guia_com_det !== '' && edit == 'false') {

        var id_producto = $('[name=id_producto]').val();
        var id_almacen = $('[name=id_almacen_detalle]').val();

        var data = 'id_guia_com_det=' + id_guia_com_det +
            '&id_producto=' + id_producto +
            '&id_almacen=' + id_almacen +
            '&series=' + JSON.stringify(json_series);

        console.log(data);
        $.ajax({
            type: 'POST',
            url: 'guardar_series',
            data: data,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                var id = $('[name=id_guia_com_detalle]').val();
                listar_detalle_movimiento(id);
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "Se guardaron las series con éxito."
                });
                $('#modal-guia_com_barras').modal('hide');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

$(document).ready(function () {
    document.getElementById('importar').addEventListener("change", function (e) {
        var files = e.target.files, file;
        if (!files || files.length == 0) return;
        file = files[0];
        var fileReader = new FileReader();
        fileReader.onload = function (e) {
            var filename = file.name;
            // pre-process data
            var binary = "";
            var bytes = new Uint8Array(e.target.result);
            var length = bytes.byteLength;
            for (var i = 0; i < length; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            // call 'xlsx' to read the file
            var oFile = XLSX.read(binary, { type: 'binary', cellDates: true, cellStyles: true });
            var result = {};
            oFile.SheetNames.forEach(function (sheetName) {
                var roa = XLS.utils.sheet_to_row_object_array(oFile.Sheets[sheetName]);
                if (roa.length > 0) {
                    result[sheetName] = roa;
                }
            });
            var td = '';
            var i = 0;
            var items = cant_items;
            var cant = $('#listaBarras tbody tr').length;
            var msj = false;
            var imp = cant + (result.Hoja1 !== undefined ? result.Hoja1.length : result.Worksheet.length);
            console.log('items' + items + ' imp' + imp + ' length' + (result.Hoja1 !== undefined ? result.Hoja1.length : result.Worksheet.length));
            console.log((result.Hoja1 !== undefined ? result.Hoja1 : result.Worksheet));
            var rspta = true;

            if (imp > items) {
                Swal.fire({
                    title: "Las series importadas superan la cantidad. Solo se agregaran hasta que complete la cantidad de " + cant_items + ". ¿Desea continuar?",
                    // text: "No podrás revertir esto.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#00a65a", //"#3085d6",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "Cancelar",
                    confirmButtonText: "Sí, Continuar"
                }).then(result => {
                    rspta = result.isConfirmed;
                });
            }
            if (rspta) {
                var hoja = (result.Hoja1 !== undefined ? result.Hoja1 : result.Worksheet);
                console.log(hoja);
                for (i = 0; i < hoja.length; i++) {
                    console.log(hoja[i].serie);
                    var serie = hoja[i].serie;
                    var agrega = false;

                    if (json_series.length > 0) {
                        const found = json_series.find(element => element == serie);
                        console.log('found' + found);

                        if (found == undefined) {
                            agrega = true;
                        }
                    } else {
                        agrega = true;
                    }

                    if (agrega) {
                        cant++;
                        td = '<tr id="reg-' + serie + '"><td hidden>0</td><td class="numero">' + cant + '</td><td><input type="text" class="oculto" name="series" value="' + serie + '"/>' + serie + '</td><td><i class="btn btn-danger fas fa-trash fa-lg " onClick="eliminar_serie(' + serie + ');"></i></td></tr>';
                        if (cant <= items) {
                            $('#listaBarras tbody').append(td);
                            json_series.push(serie);
                        } else {
                            msj = true;
                        }
                    }
                }
                if (msj) {
                    Lobibox.notify("success", {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        // width: 500,
                        msg: 'No se cargaron todas las series porque superan a la cantidad del producto.'
                    });
                }
            }
        };
        fileReader.readAsArrayBuffer(file);
    });
});

function autogenerar() {
    Swal.fire({
        title: "Ingrese un prefijo para autogenerar las series:",
        input: "text",
        // type: "input",
        icon: "info",
        // text: "xx",
        // closeOnConfirm: false,
        showCancelButton: true,
        confirmButtonText: "Autogenerar",
        cancelButtonText: "Cancelar",
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return fetch(`//api.github.com/users/${login}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText)
                    }
                    return response.json()
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
        },
        inputValidator: nombre => {
            // Si el valor es válido, debes regresar undefined. Si no, una cadena
            if (!nombre) {
                return "Por favor ingrese un prefijo";
            } else {
                return undefined;
            }
        },
        // preConfirm: function () {
        //     return new Promise(function (resolve) {
        //         resolve([
        //             $('#swal-input1').val(),
        //             $('#swal-input2').val()
        //         ])
        //     })
        // },
        // onOpen: function () {
        //     $('#swal-input1').focus()
        // }
    }).then(resultado => {
        if (resultado.value) {
            let nombre = resultado.value;
            console.log("Hola, " + nombre);
        }
    });
}