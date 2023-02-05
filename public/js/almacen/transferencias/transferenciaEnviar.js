// let origen = null;
let id_trans_seleccionadas = [];

function openGenerarGuia(data) {
    id_trans_seleccionadas = [];

    $("#modal-transferenciaGuia").modal({
        show: true
    });

    $("[name=id_almacen_origen]").val(data.id_almacen_origen);
    $("[name=id_almacen_destino]").val(data.id_almacen_destino);
    $("[name=almacen_origen_descripcion]").val(data.alm_origen_descripcion);
    $("[name=almacen_destino_descripcion]").val(data.alm_destino_descripcion);
    $("[name=punto_partida]").val(data.alm_origen_direccion);
    $("[name=punto_llegada]").val(data.alm_destino_direccion);
    $("[name=trans_serie]").val("");
    $("[name=trans_numero]").val("");
    $("[name=id_guia_com]").val("");
    $("[name=fecha_emision]").val(fecha_actual());
    $("[name=fecha_almacen]").val(fecha_actual());
    $("[name=id_sede]").val(data.id_sede_origen);
    $("[name=id_mov_alm]").val("");
    $("[name=id_requerimiento]").val(data.id_requerimiento);
    $("[name=id_transferencia]").val(data.id_transferencia);
    $("#submit_transferencia").removeAttr("disabled");

    $("[name=tra_serie]").val("");
    $("[name=tra_numero]").val("");
    $("[name=id_transportista]").val('');
    $("[name=transportista]").val('');
    $("[name=placa]").val('');
    $("[name=responsable_destino_trans]").val(usuario_session);

    if (data.id_empresa_origen !== data.id_empresa_destino) {
        $("[name=operacion]").val("VENTA NACIONAL");
    } else {
        $("[name=operacion]").val("SALIDA POR TRANSFERENCIA");
    }
    listarDetalleTransferencia(data.id_transferencia);
    id_trans_seleccionadas.push(data.id_transferencia);
    // var tp_doc_almacen = 2;//guia venta
    // next_serie_numero(data.sede_orden,tp_doc_almacen);
}

function openGuiaTransferenciaCreate() {
    var alm_origen = null;
    var alm_destino = null;
    var alm_origen_des = null;
    var alm_destino_des = null;
    var sede_origen = null;
    var sede_destino = null;
    var dif_ori = 0;
    var dif_des = 0;
    id_trans_seleccionadas = [];
    // origen = 'transferencia_por_requerimiento';

    trans_seleccionadas.forEach(element => {
        id_trans_seleccionadas.push(element.id_transferencia);

        if (alm_origen == null) {
            alm_origen = element.id_almacen_origen;
            alm_origen_des = element.alm_origen_descripcion;
            sede_origen = element.id_sede_origen;
        } else if (element.id_almacen_origen !== alm_origen) {
            dif_ori++;
        }
        if (alm_destino == null) {
            alm_destino = element.id_almacen_destino;
            alm_destino_des = element.alm_destino_descripcion;
            sede_destino = element.id_sede_destino;
        } else if (element.id_almacen_destino !== alm_destino) {
            dif_des++;
        }
    });

    var text = "";
    if (dif_ori > 0)
        text += "Debe seleccionar transferencias del mismo Almacén Origen\n";
    if (dif_des > 0)
        text += "Debe seleccionar transferencias del mismo Almacén Destino";

    if (dif_des + dif_ori > 0) {
        alert(text);
    } else {
        $("#modal-transferenciaGuia").modal({
            show: true
        });
        $("[name=id_almacen_origen]").val(alm_origen);
        $("[name=id_almacen_destino]").val(alm_destino);
        $("[name=almacen_origen_descripcion]").val(alm_origen_des);
        $("[name=almacen_destino_descripcion]").val(alm_destino_des);
        $("[name=trans_serie]").val("");
        $("[name=trans_numero]").val("");
        $("[name=id_guia_com]").val("");
        $("[name=fecha_emision]").val(fecha_actual());
        $("[name=fecha_almacen]").val(fecha_actual());
        $("[name=id_sede]").val(sede_origen);
        $("[name=id_mov_alm]").val("");
        $("[name=id_requerimiento]").val("");
        $("[name=id_transferencia]").val("");
        $("#submit_transferencia").removeAttr("disabled");

        $("[name=tra_serie]").val("");
        $("[name=tra_numero]").val("");
        $("[name=id_transportista]").val('');
        $("[name=transportista]").val('');
        $("[name=placa]").val('');

        var data = { 'id_trans_seleccionadas': id_trans_seleccionadas };
        listarDetalleTransferenciaSeleccionadas(data);
    }
}

let listaDetalle = [];

function listarDetalleTransferenciaPrincipal(type, id = 0, data = null) {
    $.ajax({
        type: "POST",
        url: "listarDetalleTransferencia",
        data: {
            type: type,
            id: id,
            data: data,
        },
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            listaDetalle = response;
            mostrarDetalleTransferencia();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        //alert("Hubo un probe")
        //alert("Fail");
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
function listarDetalleTransferencia(id) {
    listarDetalleTransferenciaPrincipal(1, id, null);
}

function listarDetalleTransferenciaSeleccionadas(data) {
    listarDetalleTransferenciaPrincipal(2, 0, data);
}

function mostrarDetalleTransferencia() {
    var html = "";
    var html_series = "";
    var i = 1;
    var id_almacen = parseInt($('[name=id_almacen_origen]').val());

    listaDetalle.forEach(element => {
        html_series = "";

        element.series.forEach(ser => {
            if (ser.estado !== 7) {
                if (html_series == "") {
                    html_series += ser.serie;
                } else {
                    html_series += ", " + ser.serie;
                }
            }
        });
        html += `<tr>
        <td>${i}</td>
        <td>${element.codigo_trans}</td>
        <td>${element.codigo_req !== null ? element.codigo_req : ""
            }</td>
        <td>${element.codigo}</td>
        <td style="background-color: navajowhite;">${element.part_number !== null ? element.part_number : ""
            }</td>
        <td style="background-color: navajowhite;">${element.descripcion + '<br><strong>' + html_series + '</strong>'}</td>
        <td>${element.cantidad}</td>
        <td>${element.abreviatura}</td>
        <td>${element.control_series ? `<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
            title="Agregar Series" onClick="open_series_transferencia(${element.id_trans_detalle},${element.id_producto},${element.cantidad},${id_almacen});"></i>` : ''}
        </td>
        </tr>`;
        i++;
    });
    $("#detalleTransferencia tbody").html(html);
}
//  style="background-color: LightCyan;"
// function next_serie_numero(id_sede, id_tp_doc) {
//     if (id_sede !== null && id_tp_doc !== null) {
//         $.ajax({
//             type: "GET",
//             url: "next_serie_numero_guia/" + id_sede + "/" + id_tp_doc,
//             dataType: "JSON",
//             success: function(response) {
//                 console.log(response);
//                 if (response !== "") {
//                     $("[name=serie]").val(response.serie);
//                     $("[name=numero]").val(response.numero);
//                     $("[name=id_serie_numero]").val(response.id_serie_numero);
//                 } else {
//                     $("[name=serie]").val("");
//                     $("[name=numero]").val("");
//                     $("[name=id_serie_numero]").val("");
//                 }
//             }
//         }).fail(function(jqXHR, textStatus, errorThrown) {
//             console.log(jqXHR);
//             console.log(textStatus);
//             console.log(errorThrown);
//         });
//     }
// }

$("#form-transferenciaGuia").on("submit", function (e) {

    e.preventDefault();
    let data = $(this).serialize();
    console.log(data);
    let detalle = [];
    let prod_sin_series = 0;

    listaDetalle.forEach(element => {
        detalle.push({
            id_trans_detalle: element.id_trans_detalle,
            series: element.series
        });
        if (element.control_series && element.series.length == 0) {
            prod_sin_series++;
        }
    });

    if (prod_sin_series > 0) {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Falta agregar series a ' + prod_sin_series + ' productos.'
        });
    } else {
        data += "&trans_seleccionadas=" + JSON.stringify(id_trans_seleccionadas) +
            "&detalle=" + JSON.stringify(detalle);
        console.log(data);
        salidaTransferencia(data);
    }

});

function salidaTransferencia(data) {
    var msj = validaCampos();
    if (msj.length > 0) {
        Swal.fire("Es necesario que ingrese " + msj, "", "warning");
    } else {
        Swal.fire({
            title: "Esta seguro que desea guardar la guía de salida ?",
            // text: "No podrás revertir esto.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Si, Guardar"
        }).then(result => {
            if (result.isConfirmed) {
                $("#submit_transferencia").attr("disabled", "true");
                $.ajax({
                    type: "POST",
                    url: "guardarSalidaTransferencia",
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        console.log(response);
                        Lobibox.notify(response.tipo, {
                            title: false,
                            size: "mini",
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje
                        });
                        if (response.tipo == 'success') {
                            $("#modal-transferenciaGuia").modal("hide");
                            $("#listaTransferenciasPorEnviar").DataTable().ajax.reload(null, false);
                            // var id = encode5t(response);
                            // window.open('imprimir_salida/'+id);
                        }
                        $("#nro_por_enviar").text(response.nroPorEnviar);
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        });
    }
}

function validaCampos() {
    var serie = $("[name=trans_serie]").val();
    var numero = $("[name=trans_numero]").val();
    var alm_ori = $("[name=id_almacen_origen]").val();
    var alm_des = $("[name=id_almacen_destino]").val();
    var resp = $("[name=responsable_destino_trans]").val();
    var text = "";

    if (serie == "" || serie == "0000" || serie == "0") {
        text += text == "" ? " una Serie" : ", una Serie";
    }
    if (numero == "" || numero == "0000000" || numero == "0") {
        text += text == "" ? " un Número" : ", un Número";
    }
    if (alm_ori == "" || alm_ori == "0") {
        text += text == "" ? " un Almacén Origen" : ", un Almacén Origen";
    }
    if (alm_des == "" || alm_des == "0") {
        text += text == "" ? " un Almacén Destino" : ", un Almacén Destino";
    }
    if (resp == "" || resp == "0") {
        text +=
            text == "" ? " un Responsable Destino" : ", un Responsable Destino";
    }
    return text;
}

$(".handleChangeSerie").on("keyup", function (e) {
    if (e.target.value.length > 0) {
        e.target.closest("div").classList.remove("has-error");
        if (e.target.closest("div").querySelector("span")) {
            e.target.closest("div").querySelector("span").remove();
        }
    } else {
        e.target.closest("div").classList.add("has-error");
    }
});

function ceros_numero_trans(numero, origen) {
    if (origen == "transferencia") {
        if (numero == "numero") {
            var num = $("[name=trans_numero]").val();
            $("[name=trans_numero]").val(leftZero(7, num));
        } else if (numero == "serie") {
            var num = $("[name=trans_serie]").val();
            $("[name=trans_serie]").val(leftZero(4, num));
        }
    } else if (origen == "transporte") {
        if (numero == "numero") {
            var num = $("[name=tra_numero]").val();
            $("[name=tra_numero]").val(leftZero(7, num));
        } else if (numero == "serie") {
            var num = $("[name=tra_serie]").val();
            $("[name=tra_serie]").val(leftZero(4, num));
        }
    }
}
