$(function () {
    $("#form-agregar-cuenta-bancaria-proveedor").on("submit", function (e) {
        e.preventDefault();
        document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[class~='boton']").setAttribute("disabled",true);
        guardarCuentaBancariaProveedor();
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

function guardarCuentaBancariaProveedor() {
    let idProveedor = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='id_proveedor']").value;
    let banco = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] select[name='banco']").value;
    let idMoneda = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] select[name='moneda']").value;
    let tipoCuenta = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] select[name='tipo_cuenta_banco']").value;
    let nroCuenta = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='nro_cuenta']").value;
    let nroCuentaInter = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='nro_cuenta_interbancaria']").value;
    let swift = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='swift']").value;
    let mensajeValidación = '';

    if (nroCuenta == '' || nroCuenta == null) {
        mensajeValidación += "Debe escribir un número de cuenta";
    }

    if (mensajeValidación.length > 0) {
        Lobibox.notify('warning', {
            title: false,
            size: 'normal',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: mensajeValidación
        });
        document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[class~='boton']").removeAttribute("disabled");

    } else {
        $.ajax({
            type: 'POST',
            url: 'guardar-cuenta-bancaria-proveedor',
            data: {
                'id_proveedor': idProveedor,
                'id_banco': banco,
                'id_moneda': idMoneda,
                'id_tipo_cuenta': tipoCuenta,
                'nro_cuenta': nroCuenta,
                'nro_cuenta_interbancaria': nroCuentaInter,
                'swift': swift
            },
            cache: false,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.status == '200') {
                    $('#modal-agregar-cuenta-bancaria-proveedor').modal('hide');
                    Lobibox.notify('success', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Cuenta bancaria registrado con éxito'
                    });
                    // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value = response.id_cuenta_contribuyente;
                    // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nro_cuenta_principal_proveedor']").value = nroCuenta;

                    // $('#listaCuentasBancariasProveedor').DataTable().ajax.reload(null, false);
                    listarCuentasBancariasContribuyente(idProveedor);
                    document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[class~='boton']").removeAttribute("disabled");

                } else {
                    Swal.fire(
                        '',
                        'Hubo un error al intentar guardar la cuenta bancaria del proveedor, por favor intente nuevamente',
                        'error'
                    );
                    document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[class~='boton']").removeAttribute("disabled");

                }



            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            Swal.fire(
                '',
                'Hubo un error al intentar guardar la cuenta bancaria del proveedor. ' + errorThrown,
                'error'
            );
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);

            document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[class~='boton']").removeAttribute("disabled");

        });
    }



}

function limpiarFormularioCuentaBancaria(){
    document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor']") ? document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] input[name='nro_cuenta']").value='' : false;
    document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor']") ? document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] input[name='nro_cuenta_interbancaria']").value='' : false;
    document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor']") ? document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] input[name='swift']").value='' : false;
    document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[class~='boton']").removeAttribute("disabled");
}

function agregar_cuenta_proveedor() {

    if($('.page-main').attr('type')=='lista_requerimiento_pago'){
        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] strong[id='nombre_contexto']").textContent= "Destinatarios";
    }else{
        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] strong[id='nombre_contexto']").textContent= "Proveedores";
    }

    let razonSocialProveedor = document.querySelector("input[name='razon_social']").value;
    let id = document.querySelector("input[name='id_proveedor']").value;

    if (id > 0) {
        $('#modal-agregar-cuenta-bancaria-proveedor').modal({
            show: true
        });
        limpiarFormularioCuentaBancaria();

        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] span[id='razon_social_proveedor']").textContent = razonSocialProveedor;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] input[name='id_proveedor']").value = id;

    } else {
        Swal.fire(
            '',
            'Debe seleccionar un proveedor',
            'warning'
        );
    }

}


function cuentasBancariasModal() {
    let nombre_contexto= 'proveedor';
    if($('.page-main').attr('type')=='lista_requerimiento_pago'){
        document.querySelector("div[id='modal-cuentas-bancarias-proveedor'] h3[class='modal-title']").textContent= "Lista de cuentas bancarias del destinatarios";
        document.querySelector("div[id='modal-cuentas-bancarias-proveedor'] strong[id='nombre_contexto']").textContent= "Destinatario";
        nombre_contexto = 'destinatario';
    }else{
        document.querySelector("div[id='modal-cuentas-bancarias-proveedor'] h3[class='modal-title']").textContent= "Lista de cuentas bancarias del proveedor";
        document.querySelector("div[id='modal-cuentas-bancarias-proveedor'] strong[id='nombre_contexto']").textContent= "Proveedor";
    }

    let razonSocialProveedor = document.querySelector("input[name='razon_social']").value;
    let id = document.querySelector("input[name='id_proveedor']").value;
    if (id > 0) {
        $('#modal-cuentas-bancarias-proveedor').modal({
            show: true
        });
        document.querySelector("div[id='modal-cuentas-bancarias-proveedor'] span[id='razon_social_proveedor']").textContent = razonSocialProveedor;
        listarCuentasBancariasContribuyente(id);

    } else {
        Swal.fire(
            '',
            'Debe seleccionar un '+ nombre_contexto,
            'warning'
        );
    }
}


function listarCuentasBancariasContribuyente(idProveedor) {
    limpiarTabla('listaCuentasBancariasProveedor');

    getCuentasBancarias(idProveedor).then(function (res) {
        if (res[0].cuenta_contribuyente) {
            ConstruirTablalistaCuentasBancariasProveedor(res[0].cuenta_contribuyente);
        }
    }).catch(function (err) {
        Swal.fire(
            '',
            'Hubo un problema al intentar obtener la lista de cuentas bancarias, por favor vuelva a intentarlo',
            'error'
        );
        // console.log(err)
    })




}

function getCuentasBancarias(idProveedor) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-cuentas-bancarias-proveedor/${idProveedor}`,
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


function ConstruirTablalistaCuentasBancariasProveedor(data) {
    var vardataTables = funcDatatables();

    let botones = [];
    botones.push({
        text: 'Nueva cuenta',
        action: function () {
            agregar_cuenta_proveedor();
        }, className: 'btn-primary'
    });

    $('#listaCuentasBancariasProveedor').DataTable({
        'dom': vardataTables[1],
        'buttons': botones,
        'language': vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'order': [1, 'desc'],
        'data': data,
        'columns': [

            {
                render: function (data, type, row) {
                    return row.banco.contribuyente.razon_social ?? '';
                }
            },
            {
                render: function (data, type, row) {
                    return row.tipo_cuenta.descripcion ?? '';
                }, 'className': 'text-center'
            },
            {
                render: function (data, type, row) {
                    return row.nro_cuenta ?? '';
                }, 'className': 'text-center'
            },
            {
                render: function (data, type, row) {
                    return row.nro_cuenta_interbancaria ?? '';
                }, 'className': 'text-center'
            },
            {
                render: function (data, type, row) {
                    return row.moneda.descripcion ?? '';
                }, 'className': 'text-center'
            },
            {
                render: function (data, type, row) {
                    return row.swift ?? '';
                }, 'className': 'text-center'
            },
            {
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-primary btn-xs" name="btnSeleccionarCuenta" title="Seleccionar cuenta"  data-id-cuenta="${(row.id_cuenta_contribuyente ?? '')}" data-nro-cuenta="${(row.nro_cuenta ?? '')}" onclick="seleccionarCuentaContribuyente(this);">Seleccionar</button>`;
                }, 'className': 'text-center'
            }
        ],
    });
}

function seleccionarCuentaContribuyente(obj) {
    $('#modal-cuentas-bancarias-proveedor').modal('hide');
    document.querySelector("input[name='nro_cuenta_principal_proveedor']").value = obj.dataset.nroCuenta;
    document.querySelector("input[name='id_cuenta_principal_proveedor']").value = obj.dataset.idCuenta;

}