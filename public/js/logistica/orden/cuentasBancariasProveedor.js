$(function(){
    $("#form-agregar-cuenta-bancaria-proveedor").on("submit", function(e){
        e.preventDefault();
        guardarCuentaBancariaProveedor();
    });
});

function guardarCuentaBancariaProveedor(){
    let idContribuyente =document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='id_contribuyente']").value;
    let banco = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] select[name='banco']").value;
    let idMoneda = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] select[name='moneda']").value;
    let tipoCuenta = document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] select[name='tipo_cuenta_banco']").value;
    let nroCuenta =document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='nro_cuenta']").value;
    let nroCuentaInter=document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='nro_cuenta_interbancaria']").value;
    let swift=document.querySelector("form[id='form-agregar-cuenta-bancaria-proveedor'] input[name='swift']").value;
    let mensajeValidación='';

    if(nroCuenta =='' || nroCuenta ==null ){
        mensajeValidación+="Debe escribir un número de cuenta";
    }

    if(mensajeValidación.length >0){
        Lobibox.notify('warning', {
            title:false,
            size: 'normal',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: mensajeValidación
        });
    }else{
        $.ajax({
            type: 'POST',
            url: 'guardar-cuenta-bancaria-proveedor',
            data: {
                'id_contribuyente':idContribuyente,
                'id_banco':banco,
                'id_moneda':idMoneda,
                'id_tipo_cuenta':tipoCuenta,
                'nro_cuenta':nroCuenta,
                'nro_cuenta_interbancaria':nroCuentaInter,
                'swift': swift
            },
            cache: false,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if(response.status =='200'){
                    $('#modal-agregar-cuenta-bancaria-proveedor').modal('hide');
                    Lobibox.notify('success', {
                        title:false,
                        size: 'normal',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Cuenta bancaria registrado con éxito'
                    });
                    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value = response.id_cuentra_contribuyente;
                    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nro_cuenta_principal_proveedor']").value = nroCuenta;



                }else{
                    Lobibox.notify('error', {
                        title:false,
                        size: 'normal',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Hubo un error al intentar guardar el contacto, por favor intente nuevamente, si el problema persiste notifique a soporte.'
                    });
                }

                    
                
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            Lobibox.notify('error', {
                title:false,
                size: 'normal',
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Hubo un error al intentar guardar el contacto. '+ errorThrown
            });
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
    }



}

function agregar_cuenta_proveedor(){
    let razonSocialProveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='razon_social']").value;
    let idContribuyente = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contrib']").value;
    if(idContribuyente >0){
        $('#modal-agregar-cuenta-bancaria-proveedor').modal({
            show: true
        });
        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] span[id='razon_social_proveedor']").textContent= razonSocialProveedor;
        document.querySelector("div[id='modal-agregar-cuenta-bancaria-proveedor'] input[name='id_contribuyente']").value= idContribuyente;

    }else{
        alert("Debe seleccionar un proveedor");
    }

}


function cuentasBancariasModal(){
    let razonSocialProveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='razon_social']").value;
    let idContribuyente = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contrib']").value;
    if(idContribuyente >0){
        $('#modal-cuentas-bancarias-proveedor').modal({
            show: true
        });
        document.querySelector("div[id='modal-cuentas-bancarias-proveedor'] span[id='razon_social_proveedor']").textContent= razonSocialProveedor;
        listarCuentasBancariasContribuyente(idContribuyente);

    }else{
        alert("Debe seleccionar un proveedor");
    }
}


function listarCuentasBancariasContribuyente(idContribuyente){

    getCuentasBancariasContribuyente(idContribuyente).then(function (res) {
        ConstruirTablalistaCuentasBancariasProveedor(res);
    }).catch(function (err) {
        console.log(err)
    })




}

function getCuentasBancariasContribuyente(idContribuyente){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url:`listar-cuentas-bancarias-contribuyente/${idContribuyente}`,
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


function ConstruirTablalistaCuentasBancariasProveedor(data){
    $('#listaCuentasBancariasProveedor').DataTable({
        'processing':true,
        'destroy':true,
        'language' : vardataTables[0],
        'dom': 'Bfrtip',
        'order': [1, 'desc'],
        'data': data,
        'columns': [

            { render: function (data, type, row) {   
                return `${row.banco.contribuyente.razon_social?row.banco.contribuyente.razon_social:''}`;
                }
            },
            { render: function (data, type, row) {   
                return `${row.tipo_cuenta.descripcion?row.tipo_cuenta.descripcion:''}`;
                }
            },
            { render: function (data, type, row) {   
                return `${row.nro_cuenta?row.nro_cuenta:''}`;
                }
            },
            { render: function (data, type, row) {   
                return `${row.nro_cuenta_interbancaria?row.nro_cuenta_interbancaria:''}`;
                }
            }, 
            { render: function (data, type, row) {   
                return `${row.moneda.descripcion?row.moneda.descripcion:''}`;
                }
            }, 
            { render: function (data, type, row) {   
                return `${row.swift?row.swift:''}`;
                }
            }, 
            { render: function (data, type, row) {                     
                    return `
                        <button type="button" class="btn btn-primary btn-xs" name="btnSeleccionarCuenta" title="Seleccionar cuenta"  data-id-cuenta="${row.id_cuentra_contribuyente}" data-nro-cuenta="${row.nro_cuenta}" onclick="seleccionarCuentaContribuyente(this);">Seleccionar</button>`;
                }   
            }   
        ],
 
        'columnDefs': [
            { 'aTargets': [0], 'sWidth': '30%'},
            { 'aTargets': [1], 'sWidth': '10%'},
            { 'aTargets': [2], 'sWidth': '10%'},
            { 'aTargets': [3], 'sWidth': '10%'},
            { 'aTargets': [4], 'sWidth': '10%'},
            { 'aTargets': [5], 'sWidth': '8%'}
    ],
    });
}

function seleccionarCuentaContribuyente(obj){
    $('#modal-cuentas-bancarias-proveedor').modal('hide');
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nro_cuenta_principal_proveedor']").value= obj.dataset.nroCuenta;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value= obj.dataset.idCuenta;

}