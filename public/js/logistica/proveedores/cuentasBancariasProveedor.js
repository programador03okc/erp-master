$(function () {
    $("#form-agregar-cuenta-bancaria-proveedor").on("submit", function (e) {
        e.preventDefault();
        guardarCuentaBancariaProveedor();
    });
});

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

                } else {
                    Swal.fire(
                        '',
                        'Hubo un error al intentar guardar la cuenta bancaria del proveedor, por favor intente nuevamente',
                        'error'
                    );
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
        });
    }



}

function agregar_cuenta_proveedor() {
    let razonSocialProveedor = document.querySelector("input[name='razon_social']").value;
    let idProveedor = document.querySelector("input[name='id_proveedor']").value;

    if (idProveedor > 0) {
        $('#modal-agregar-cuenta-bancaria-proveedor').modal({
            show: true
        });
        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] span[id='razon_social_proveedor']").textContent = razonSocialProveedor;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] input[name='id_proveedor']").value = idProveedor;

    } else {
        Swal.fire(
            '',
            'Debe seleccionar un proveedor',
            'warning'
        );
    }

}


function cuentasBancariasModal() {
    let razonSocialProveedor = document.querySelector("input[name='razon_social']").value;
    let idProveedor = document.querySelector("input[name='id_proveedor']").value;
    if (idProveedor > 0) {
        $('#modal-cuentas-bancarias-proveedor').modal({
            show: true
        });
        document.querySelector("div[id='modal-cuentas-bancarias-proveedor'] span[id='razon_social_proveedor']").textContent = razonSocialProveedor;
        listarCuentasBancariasContribuyente(idProveedor);

    } else {
        Swal.fire(
            '',
            'Debe seleccionar un proveedor',
            'warning'
        );
    }
}


function listarCuentasBancariasContribuyente(idProveedor) {

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
        console.log(err)
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

        'columnDefs': [
            { 'aTargets': [0], 'sWidth': '30%' },
            { 'aTargets': [1], 'sWidth': '10%' },
            { 'aTargets': [2], 'sWidth': '10%' },
            { 'aTargets': [3], 'sWidth': '10%' },
            { 'aTargets': [4], 'sWidth': '10%' },
            { 'aTargets': [5], 'sWidth': '8%' }
        ],
    });
}

function seleccionarCuentaContribuyente(obj) {
    $('#modal-cuentas-bancarias-proveedor').modal('hide');
    document.querySelector("input[name='nro_cuenta_principal_proveedor']").value = obj.dataset.nroCuenta;
    document.querySelector("input[name='id_cuenta_principal_proveedor']").value = obj.dataset.idCuenta;

}