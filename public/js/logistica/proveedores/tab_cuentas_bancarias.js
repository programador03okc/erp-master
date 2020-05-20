var data_cuenta_bancaria_list=[];

function limpiarInputCuentaBancaria(){
    document.getElementsByName('banco')[0].value="";
    document.getElementsByName('tipo_cuenta')[0].value="";
    document.getElementsByName('nro_cuenta')[0].value="";
    document.getElementsByName('nro_cuenta_interbancaria')[0].value="";
}

function llenar_tabla_cuentas_bancarias(data){
    data_cuenta_bancaria_list=data;
    limpiarTabla('ListaCuentasBancarias');
    htmls ='<tr></tr>';
    $('#ListaCuentasBancarias tbody').html(htmls);
    var table = document.getElementById("ListaCuentasBancarias");
    for(var a=0;a < data.length;a++){
    // console.log(data[a].id_cuenta_contribuyente);
        if(data[a].estado != 0){

            var row = table.insertRow(a+1);
            var tdIdcuentaContri =  row.insertCell(0);
                tdIdcuentaContri.setAttribute('class','hidden');
                tdIdcuentaContri.innerHTML = data[a].id_cuenta_contribuyente;
            row.insertCell(1).innerHTML = a+1;
            row.insertCell(2).innerHTML = data[a].nombre_banco;
            row.insertCell(3).innerHTML = data[a].descripcion_tipo_cuenta;
            row.insertCell(4).innerHTML = data[a].nro_cuenta;
            row.insertCell(5).innerHTML = data[a].nro_cuenta_interbancaria;
            row.insertCell(6).innerHTML = 
                                        '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                                            '<button class="btn btn-secondary btn-sm  activation" name="btnEditarCuentaBancaria" data-toggle="tooltip" onclick="editarCuentaBancaria(event,'+a+');"'+ 
                                            'data-original-title="Editar"><i class="fas fa-edit"></i>'+
                                            '</button>'+
                                            '<button class="btn btn-danger btn-sm  activation"'+
                                                'name="btnEliminarCuentaBancaria"'+
                                                'data-toggle="tooltip"'+
                                                'title=""'+
                                                'onclick="eliminarCuentaBancaria(event,'+a+');"'+
                                                'data-original-title="Eliminar"'+
                                            '>'+
                                                '<i class="fas fa-trash-alt"></i>'+
                                            '</button>'+
                                        '</div>';

        }
    }
    return null;
}

function AgregarCuantaBancaria(event){
    event.preventDefault();
    // limpiarInputCuentaBancaria();
    document.getElementById('modal-gestionar-cuenta-bancaria-title').innerText ='Agregar Cuenta Bancaria';
    $('#modal-gestionar-cuenta-bancaria').modal({
        show: true,
        backdrop: 'static',
    });
    

    const div = document.getElementById('btnAction_cuentas');
    while(div.firstChild) {
        div.removeChild(div.firstChild);
    }
    var button = document.createElement('button');
    button.innerHTML = 'Agregar';
    button.setAttribute('class','btn btn-sm btn-success')
    button.addEventListener('click', function(){
        var id_prov = $('[name=id_proveedor]').val();
        let bancos = document.getElementsByName('banco')[0];
        let id_banco = bancos.value;
        let nombre_banco = bancos.options[bancos.selectedIndex].text;
        let tipo_cuenta_banco = document.getElementsByName('tipo_cuenta_banco')[0];
        let id_tipo_cuenta =tipo_cuenta_banco.value;
        let descripcion_tipo_cuenta = tipo_cuenta_banco.options[tipo_cuenta_banco.selectedIndex].text;
        let nro_cuenta = document.getElementsByName('nro_cuenta')[0].value;
        let nro_cuenta_interbancaria = document.getElementsByName('nro_cuenta_interbancaria')[0].value;

        let data_cuenta_banco = {
            'id_cuenta_contribuyente':0,
            'id_proveedor':id_prov?id_prov:0,
            'id_banco':id_banco,
            'nombre_banco':nombre_banco,
            'id_tipo_cuenta':id_tipo_cuenta,
            'descripcion_tipo_cuenta':descripcion_tipo_cuenta,
            'nro_cuenta':nro_cuenta,
            'nro_cuenta_interbancaria':nro_cuenta_interbancaria,
            'estado':2};
        
        data_cuenta_bancaria_list.push(data_cuenta_banco);
        llenar_tabla_cuentas_bancarias(data_cuenta_bancaria_list);
        
        setTextInfoAnimation('Agregado!');

    });
    div.appendChild(button)
}

function editarCuentaBancaria(event, id){   
    event.preventDefault();
    
    document.getElementById('modal-gestionar-cuenta-bancaria-title').innerText ='Editar Cuenta Bancaria';
    $('#modal-gestionar-cuenta-bancaria').modal({
        show: true,
        backdrop: 'static',
    })
    document.getElementsByName('banco')[0].value=data_cuenta_bancaria_list[id].id_banco;
    document.getElementsByName('tipo_cuenta_banco')[0].value=data_cuenta_bancaria_list[id].id_tipo_cuenta;
    document.getElementsByName('nro_cuenta')[0].value=data_cuenta_bancaria_list[id].nro_cuenta;
    document.getElementsByName('nro_cuenta_interbancaria')[0].value=data_cuenta_bancaria_list[id].nro_cuenta_interbancaria;

    const div = document.getElementById('btnAction_cuentas');
    while(div.firstChild) {
        div.removeChild(div.firstChild);
    }
    var button = document.createElement('button');
    button.innerHTML = 'Actualizar';
    button.setAttribute('class','btn btn-sm btn-primary')
    button.addEventListener('click', function(){
        var id_prov = $('[name=id_proveedor]').val();
        let bancos = document.getElementsByName('banco')[0];
        let id_banco = bancos.value;
        let nombre_banco = bancos.options[bancos.selectedIndex].text;
        let tipo_cuenta_banco = document.getElementsByName('tipo_cuenta_banco')[0];
        let id_tipo_cuenta =tipo_cuenta_banco.value;
        let descripcion_tipo_cuenta = tipo_cuenta_banco.options[tipo_cuenta_banco.selectedIndex].text;
        let nro_cuenta = document.getElementsByName('nro_cuenta')[0].value;
        let nro_cuenta_interbancaria = document.getElementsByName('nro_cuenta_interbancaria')[0].value;

        data_cuenta_bancaria_list[id].id_proveedor=id_prov?id_prov:0;
        data_cuenta_bancaria_list[id].id_banco=id_banco;
        data_cuenta_bancaria_list[id].nombre_banco=nombre_banco;
        data_cuenta_bancaria_list[id].tipo_cuenta_banco=id_tipo_cuenta;
        data_cuenta_bancaria_list[id].descripcion_tipo_cuenta=descripcion_tipo_cuenta;
        data_cuenta_bancaria_list[id].nro_cuenta=nro_cuenta;
        data_cuenta_bancaria_list[id].nro_cuenta_interbancaria=nro_cuenta_interbancaria;

        if(data_cuenta_bancaria_list[id].estado !=2){
            data_cuenta_bancaria_list[id].estado=3; //editado
        }
        setTextInfoAnimation('Editado!');
        llenar_tabla_cuentas_bancarias(data_cuenta_bancaria_list);

    });
    div.appendChild(button)

}

function eliminarCuentaBancaria(event,id){        
    event.preventDefault();
    if(data_cuenta_bancaria_list[id].id_cuenta_contribuyente == '0'){
    }else{
        data_cuenta_bancaria_list[id].estado=0;
    }
    llenar_tabla_cuentas_bancarias(data_cuenta_bancaria_list);

}



function save_form_cuentas(){
    let id_prov = $('[name=id_proveedor]').val();
    let nuevos_cuenta_list = data_cuenta_bancaria_list.filter(word => (word.id_cuenta_contribuyente < 1 )  ); // nuevos
        let editados_eliminados_cuentas_list = data_cuenta_bancaria_list.filter(e => (e.id_cuenta_contribuyente > 0 && e.estado != 1 )  ); // editados o eliminados
        
        for (var i in editados_eliminados_cuentas_list) {
            if (editados_eliminados_cuentas_list[i].estado == 3) {
                editados_eliminados_cuentas_list[i].estado = 1;
            }
        }

        if(nuevos_cuenta_list.length >0){
            baseUrl = 'registrar_cuenta_bancaria';
            data = nuevos_cuenta_list;
            method= 'POST';
            msj='Cuenta(s) agregadas';
            executeRequest(id_prov,baseUrl,method,data,msj);
            
        }
        if(editados_eliminados_cuentas_list.length >0){
            baseUrl = 'update_cuenta_bancaria';
            data = editados_eliminados_cuentas_list;
            method= 'PUT';
            msj='Cuenta(s) actualizada';
            executeRequest(id_prov,baseUrl,method,data,msj);
        }
}