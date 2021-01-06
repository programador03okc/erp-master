function llenarTablaCuadroCostosComercial(data){
    var vardataTables = funcDatatables();
    $('#listaCuadroCostos').dataTable({
        "order": [[ 10, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id'},
            {'data': 'fecha_entrega'},
            {'data': 'codigo_oportunidad'},
            {'data': 'oportunidad'},
            {'data': 'probabilidad'},
            {'data': 'fecha_limite'},
            {'data': 'moneda'},
            {'data': 'importe'},
            {'data': 'tipo'},
            {'data': 'nombre_contacto'},
            {'data': 'created_at'}
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaCuadroCostos_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;


    $('#listaCuadroCostos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaCuadroCostos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        let codigo = $(this)[0].children[2].textContent;
        let descipcion = $(this)[0].children[3].textContent;
        // console.log(codigo);
        
        document.querySelector('div[id="modal-cuadro_costos_comercial"] label[id="codigo"]').textContent = codigo;
        document.querySelector('div[id="modal-cuadro_costos_comercial"] label[id="descripcion"]').textContent = descipcion;
    });

}

function get_cuadro_costos_comercial(){
    
    baseUrl = '/logistica/get_cuadro_costos_comercial';
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                llenarTablaCuadroCostosComercial(response);
            }else{
                alert('no hay data');
            }
 
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function selectCodigoCC(){
    let codigoCC = document.querySelector('div[id="modal-cuadro_costos_comercial"] label[id="codigo"]').textContent;
    let descripcionCC = document.querySelector('div[id="modal-cuadro_costos_comercial"] label[id="descripcion"]').textContent;
    document.querySelector('form[id="form-requerimiento"] input[name="codigo_occ"]').value = codigoCC;
    document.querySelector('form[id="form-requerimiento"] input[name="occ"]').value = descripcionCC;

    $('#modal-cuadro_costos_comercial').modal('hide');

}

function mostrar_cuadro_costos_modal(){
    // let id_opt_com =getActualOptComercial()['id'];
    
    // console.log(tpOptCom);

    switch (tpOptCom.id) {
        case '1': //orden c cliente
            alert('no esta definida esta opcion');

            break;
    
        case '2': // cuadro de costos
            $('#modal-cuadro_costos_comercial').modal({
                show: true,
                backdrop: 'true'
            });

            get_cuadro_costos_comercial();
        
            break;
    
        case '3': // gastos operativos
            alert('no esta definida esta opcion');

            break;
    
        default:
            alert('no esta definida esta opcion');
            break;
    }
}

function get_requerimiento_por_codigo(){
    var codigo = $('[name=codigo]').val();
    mostrar_requerimiento(codigo);
}


function changeMonedaSelect(e){
    if( e.target.value == 1){
        document.querySelector("div[id='montoMoneda']").textContent='S/.';

    }else if( e.target.value ==2){
        document.querySelector("div[id='montoMoneda']").textContent='$';
    }else{
        document.querySelector("div[id='montoMoneda']").textContent='';
    }

}

function limpiarSelectTipoCliente(){
    let selectElement = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']");
    if(selectElement !=null){
        while (selectElement.options.length > 0) {                
            selectElement.remove(0);
        }    
    }
}

function createOptionTipoCliente(tipoRequerimiento){  
    let selectTipoCliente = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']");
    if(selectTipoCliente !=null){
        let array = [];
        switch (tipoRequerimiento) {
        case 'COMPRA':
        case '1':
            limpiarSelectTipoCliente();
            array =[
                {descripcion:'Persona Natural', valor: 1},
                {descripcion:'Persona Juridica', valor: 2}
                // {descripcion:'Uso Almacen', valor: 3},
                // {descripcion:'Uso Administración', valor: 4}
            ]
            array.forEach(element => {
                let option = document.createElement("option");
                option.text = element.descripcion;
                option.value = element.valor;
                selectTipoCliente.add(option);
            });
            break;
            case 'VENTA':
            case '2':
                limpiarSelectTipoCliente();
                array =[
                    {descripcion:'Persona Natural', valor: 1},
                    {descripcion:'Persona Juridica', valor: 2}
                ]
                array.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.descripcion;
                    option.value = element.valor;
                    selectTipoCliente.add(option);
                });
                break;
            case 'USO_ALMACEN':
            case '3':
                limpiarSelectTipoCliente();
                array =[
                    {descripcion:'Uso Almacen', valor: 3},
                    {descripcion:'Uso Administración', valor: 4}
                ]
                array.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.descripcion;
                    option.value = element.valor;
                    selectTipoCliente.add(option);
                });
                break;
        
            default:
    
                break;
        }
        return false;
    }
   
}
function stateFormRequerimiento(estilo){
    // console.log(estilo);
    switch (estilo) {
        case 1:
            hiddeElement('ocultar','form-requerimiento',[
                // 'input-group-proyecto',
                'input-group-rol-usuario',
                'input-group-comercial',
                'input-group-almacen',
                'input-group-cuenta'
            ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-sede',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-cliente',
                'input-group-direccion-entrega',
                'input-group-ubigeo-entrega',
                'input-group-monto'
    
            ]); 
            break;
    
        case 2:
            hiddeElement('ocultar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-rol-usuario',
                'input-group-sede',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-cliente',
                'input-group-direccion-entrega',
                'input-group-cuenta',
                'input-group-ubigeo-entrega',
                'input-group-proyecto',
                'input-group-comercial',
                'input-group-monto'
            ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-almacen',
                'input-group-tipo-cliente'
        
            ]);

            document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén que solicita';
            document.querySelector("form[id='form-requerimiento'] select[name='rol_usuario']").value='';

        break;
    
        case 3:
            document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén';
            document.querySelector("form[id='form-requerimiento'] select[name='rol_usuario']").value='';

            hiddeElement('ocultar','form-requerimiento',[
                'input-group-rol-usuario',
                'input-group-proyecto',
                'input-group-comercial',
                'input-group-almacen'
            ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-sede',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-empresa',
                'input-group-tipo-cliente',
                'input-group-cliente',
                'input-group-direccion-entrega',
                'input-group-monto'
    
            ]);
            break;
            case 4: //compra - proyectos
            hiddeElement('ocultar','form-requerimiento',[
                'input-group-cliente',
                'input-group-rol-usuario',
                'input-group-comercial',
                'input-group-almacen',
                'input-group-cuenta',
                'input-group-fecha_entrega',
                'input-group-monto',
                'input-group-cliente',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-direccion-entrega',
                'input-group-cuenta',
                'input-group-nombre-contacto',
                'input-group-cargo-contacto',
                'input-group-email-contacto',
                'input-group-telefono-contacto',
                'input-group-direccion-contacto',
                'input-group-horario-contacto'
            ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-sede',
                'input-group-proyecto'    
            ]); 
            break;
            case 5: //compra - comercial, otros
            hiddeElement('ocultar','form-requerimiento',[
                'input-group-cliente',
                'input-group-rol-usuario',
                'input-group-comercial',
                'input-group-almacen',
                'input-group-proyecto'
            ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-sede',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-cliente',
                'input-group-direccion-entrega',
                'input-group-ubigeo-entrega',
                'input-group-monto',
                'input-group-cliente',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-direccion-entrega',
                'input-group-cuenta',
                'input-group-nombre-contacto',
                'input-group-cargo-contacto',
                'input-group-email-contacto',
                'input-group-telefono-contacto',
                'input-group-direccion-contacto',
                'input-group-horario-contacto',
    
            ]); 
            break;
        default:
            break;
    }
    

}
function limpiarFormRequerimiento(){
    document.querySelector("div[id='input-group-fecha']").setAttribute('class','col-md-2');
    document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='dni_persona']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='cliente_ruc']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='nombre_persona']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='cliente_razon_social']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='telefono_cliente']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='email_cliente']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='direccion_entrega']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='id_cuenta']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='nro_cuenta']").value='';
    document.querySelector("form[id='form-requerimiento'] input[name='cci']").value='';


    // document.querySelector("form[id='form-requerimiento'] select[name='id_almacen']").value='';
    // document.querySelector("form[id='form-requerimiento'] input[name='ubigeo']").value='';
    // document.querySelector("form[id='form-requerimiento'] select[name='sede']").value='';
    // document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']").value = '';      
    // document.querySelector("form[id='form-requerimiento'] input[name='name_ubigeo']").value='';

}

function getNexCodigoRequerimiento(tipo_requerimiento){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: rutaNextCodigoRequerimiento+'/' + tipo_requerimiento,
        dataType: 'JSON',
        success: function(response){ 
            document.querySelector("form[id='form-requerimiento'] input[name='codigo']").value = response.data;
        }
    });
}

function changeOptTipoReqSelect(e){
    if(e.target.value == 1){
        createOptionTipoCliente('COMPRA');
        stateFormRequerimiento(1);
        limpiarFormRequerimiento();
        document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén que solicita';
    }else if(e.target.value == 2){ //venta directa
        createOptionTipoCliente('VENTA');
        stateFormRequerimiento(3)
        // listar_almacenes();
    }else if(e.target.value == 3){
        createOptionTipoCliente('USO_ALMACEN');
        stateFormRequerimiento(2);
    }
    getNexCodigoRequerimiento(e.target.value);
}



function changeOptEmpresaSelect(e){
    let id_empresa = e.target.value;
    getDataSelectSede(id_empresa);
}

function llenarSelectSede(array){

    let selectElement = document.querySelector("div[id='input-group-sede'] select[name='sede']");
    
    if(selectElement.options.length>0){
        var i, L = selectElement.options.length - 1;
        for(i = L; i >= 0; i--) {
            selectElement.remove(i);
        }
    }

    array.forEach(element => {
        let option = document.createElement("option");
        option.text = element.descripcion;
        option.value = element.id_sede;
        option.setAttribute('data-ubigeo',element.id_ubigeo);
        option.setAttribute('data-name-ubigeo',element.ubigeo_descripcion);
        selectElement.add(option);
    });

    // console.log(selectElement.value);
    // let id_empresa = document.querySelector("div[id='requerimiento'] select[id='id_empresa_select_req']");
    // let id_sede= selectElement.value;

}

function getDataSelectSede(id_empresa = null){
    if(id_empresa >0){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: rutaSedeByEmpresa+'/' + id_empresa,
            dataType: 'JSON',
            success: function(response){ 
                // console.log(response);  
                if(response.length ==0){
                    console.error("usuario no registrado en 'configuracion'.'sis_usua_sede' o el estado del registro es diferente de 1");
                    alert('No se pudo acceder al listado de Sedes, el usuario debe pertenecer a una Sede y la sede debe estar habilitada');
                }else{
                    llenarSelectSede(response);
                    seleccionarAmacen(response)
                    llenarUbigeo();
                }
            }
        });
    }
    return false;
}

function seleccionarAmacen(data){
    let firstSede = data[0].id_sede;
    let selectAlmacen = document.querySelector("div[id='input-group-almacen'] select[name='id_almacen']");
    if(selectAlmacen.options.length>0){
        var i, L = selectAlmacen.options.length - 1;
        for(i = L; i >= 0; i--) {
            if(selectAlmacen.options[i].dataset.idSede == firstSede){
                selectAlmacen.options[i].setAttribute('selected',true);
            }
        }
    }
}

function llenarUbigeo(){
    var ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
    var name_ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
    document.querySelector("input[name='ubigeo']").value=ubigeo;
    document.querySelector("input[name='name_ubigeo']").value=name_ubigeo;
    
    var sede = $('[name=sede]').val();
    // cargar_almacenes(sede);
}


function changeTipoCliente(e,id =null){
    let option = e?e.target.value:null;
    if(id >0){
        option = id;
    }

    if (option == 1){ // persona natural
 
        limpiarFormRequerimiento()
        grupos.forEach(element => {
            if(element.id_grupo ==3){ // proyectos
                stateFormRequerimiento(4)
            }else{
                stateFormRequerimiento(5)
            }
        });
        document.querySelector("form[id='form-requerimiento'] input[name='cliente_ruc']").style.display ='none';
        document.querySelector("form[id='form-requerimiento'] input[name='cliente_razon_social']").style.display = 'none';
        document.querySelector("form[id='form-requerimiento'] input[name='nombre_persona']").style.display ='block';
        document.querySelector("form[id='form-requerimiento'] input[name='dni_persona']").style.display ='block';

    }
    else if (option == 2){ // persona juridica

        document.querySelector("form[id='form-requerimiento'] input[name='cliente_ruc']").style.display ='block';
        document.querySelector("form[id='form-requerimiento'] input[name='cliente_razon_social']").style.display ='block';
        document.querySelector("form[id='form-requerimiento'] input[name='nombre_persona']").style.display ='none';
        document.querySelector("form[id='form-requerimiento'] input[name='dni_persona']").style.display ='none';
        limpiarFormRequerimiento()
        stateFormRequerimiento(1);

    }else if(option == 3 ){ // uso almacen
        limpiarFormRequerimiento()
        stateFormRequerimiento(2);
        listar_almacenes();
    
    }else if(option == 4 ){ // uso administracinón
        limpiarFormRequerimiento()
        stateFormRequerimiento(1);
        
    }
}


function openCliente(){
    var tipoCliente = $('[name=tipo_cliente]').val();
    if (tipoCliente == 1){
        modalPersona();
    } else {
        clienteModal();
    }
}



function changeOptUbigeo(e){
    var ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
    var name_ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
    var sede = $('[name=sede]').val();

    document.querySelector("input[name='ubigeo']").value=ubigeo;
    document.querySelector("input[name='name_ubigeo']").value=name_ubigeo;
    cargar_almacenes(sede);
}

function cargar_almacenes(sede){
    if (sede !== ''){
        $.ajax({
            type: 'GET',
            url: 'cargar_almacenes/'+sede,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                var option = '';
                for (var i=0; i<response.length; i++){
                    if (response.length == 1){
                        option+='<option data-id-sede="'+response[i].id_sede+'" data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_almacen+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';

                    } else {
                        option+='<option data-id-sede="'+response[i].id_sede+'" data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_almacen+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';

                    }
                }
                $('[name=id_almacen]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}


function telefonosClienteModal(){
    let id_cliente = document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value):0;
    let id_persona = document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value):0;
    
    if(id_cliente>0){
        openModalTelefonosCliente();
        llenarListaTelefonoCliente(null,id_cliente);
    }
    if(id_persona>0){
        openModalTelefonosCliente();
        llenarListaTelefonoCliente(id_persona,null);
    }

}
function emailClienteModal(){
    let id_cliente = document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value):0;
    let id_persona = document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value):0;
    
    if(id_cliente>0){
        openModalEmailCliente();
        llenarListaEmailCliente(null,id_cliente);
    }
    if(id_persona>0){
        openModalEmailCliente();
        llenarListaEmailCliente(id_persona,null);
    }

}

function direccionesClienteModal(){
    let id_cliente = document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value):0;
    let id_persona = document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value):0;

    if(id_cliente>0){
        openModalDireccionesCliente();
        llenarListaDireccionesCliente(null,id_cliente);
    }
    if(id_persona>0){
        openModalDireccionesCliente();
        llenarListaDireccionesCliente(id_persona,null);
    }

}
function cuentaClienteModal(){
    let id_cliente = document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value):0;
    // let id_persona = document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value):0;

    if(id_cliente>0){
        openModalCuentasCliente();
        llenarListaCuentasCliente(null,id_cliente);
    }
    // if(id_persona>0){
    //     openModalCuentasCliente();
    //     llenarListaCuentasCliente(id_persona,null);
    // }

}

function agregarCuentaClienteModal(){
    let id_cliente = document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value):0;
    let razon_social = document.querySelector("form[id='form-requerimiento'] input[name='cliente_razon_social']").value?(document.querySelector("form[id='form-requerimiento'] input[name='cliente_razon_social']").value):"-";
    document.querySelector("div[id='modal-agregar-cuenta-cliente'] input[name='id_cliente']").value = id_cliente;
    document.querySelector("span[id='razon_social']").textContent = razon_social;
     // let id_persona = document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value?parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value):0;

    if(id_cliente>0){
        openModalAgregarCuentasCliente();
    }
 

}

function openModalAgregarCuentasCliente(){
    $('#modal-agregar-cuenta-cliente').modal({
        show: true
    });
}

function fillInputCuentaCliente(data){
    document.querySelector("form[id='form-requerimiento'] input[name='id_cuenta']").value = data.id_cuenta?data.id_cuenta:0;
    document.querySelector("form[id='form-requerimiento'] select[name='banco']").value = data.banco?data.banco:0;
    document.querySelector("form[id='form-requerimiento'] select[name='tipo_cuenta']").value = data.tipo_cuenta?data.tipo_cuenta:0;
    document.querySelector("form[id='form-requerimiento'] select[name='moneda']").value = data.moneda?data.moneda:0;
    document.querySelector("form[id='form-requerimiento'] input[name='nro_cuenta']").value = data.nro_cuenta?data.nro_cuenta:'';
    document.querySelector("form[id='form-requerimiento'] input[name='cci']").value = data.cci?data.cci:'';
}

function guardarCuentaCliente(){
    let id_cliente = document.querySelector("div[id='modal-agregar-cuenta-cliente'] input[name='id_cliente']").value;
    let banco = document.querySelector("div[id='modal-agregar-cuenta-cliente'] select[name='banco']").value;
    let tipo_cuenta = document.querySelector("div[id='modal-agregar-cuenta-cliente'] select[name='tipo_cuenta']").value;
    let moneda = document.querySelector("div[id='modal-agregar-cuenta-cliente'] select[name='moneda']").value;
    let nro_cuenta = document.querySelector("div[id='modal-agregar-cuenta-cliente'] input[name='nro_cuenta']").value;
    let cci = document.querySelector("div[id='modal-agregar-cuenta-cliente'] input[name='cci']").value;
    let payload={};

    if(id_cliente > 0){
        if(nro_cuenta.length >0 || cci.length >0){
            payload = {
                'id_cliente': id_cliente,
                'banco': banco,
                'tipo_cuenta': tipo_cuenta,
                'moneda': moneda,
                'nro_cuenta': nro_cuenta,
                'cci': cci
            };

            $.ajax({
                type: 'POST',
                url: rutaGuardarCuentacliente,
                data: payload,
                beforeSend: function(){
                },
                success: function(response){
                    console.log(response);
                    if (response.status == '200') {
                        alert('Se agregó la cuenta');
                        $('#modal-agregar-cuenta-cliente').modal('hide');
                        let new_id_cuenta= response.id_cuenta_contribuyente;
                        payload.id_cuenta=new_id_cuenta;
                        fillInputCuentaCliente(payload);

                    }else {
                        alert('hubo un error, No se puedo guardar');
                    }
                }
            });

        }else{
            alert("debe ingresar un número de cuenta");
        }
    }else{
        alert("hubo un error en obtener el ID cliente");
    }
}


function openModalTelefonosCliente(){
    $('#modal-telefonos-cliente').modal({
        show: true
    });
}
function openModalEmailCliente(){
    $('#modal-email-cliente').modal({
        show: true
    });
}
function openModalDireccionesCliente(){
    $('#modal-direcciones-cliente').modal({
        show: true
    });
}
function openModalCuentasCliente(){
    $('#modal-cuentas-cliente').modal({
        show: true
    });
}


function llenarListaEmailCliente(id_persona=null,id_cliente=null){

    var vardataTables = funcDatatables();
    $('#listaEmailCliente').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        paging:   true,
        searching: true,
        language: vardataTables[0],
        processing: true,
        ajax: rutaEmailCliente+'/'+id_persona+'/'+id_cliente,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return row.email;
                }
            }
        ],
    })

    let tablelistaitem = document.getElementById(
        'listaEmailCliente_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true
}

function llenarListaTelefonoCliente(id_persona=null,id_cliente=null){

    var vardataTables = funcDatatables();
    $('#listaTelefonosCliente').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        paging:   true,
        searching: true,
        language: vardataTables[0],
        processing: true,
        ajax: rutaTelefonosCliente+'/'+id_persona+'/'+id_cliente,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return row.telefono;
                }
            }
        ],
    })

    let tablelistaitem = document.getElementById(
        'listaTelefonosCliente_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true
}

function llenarListaDireccionesCliente(id_persona=null,id_cliente=null){

    var vardataTables = funcDatatables();
    $('#listaDireccionesCliente').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        paging:   true,
        searching: true,
        language: vardataTables[0],
        processing: true,
        ajax: rutaDireccionesCliente+'/'+id_persona+'/'+id_cliente,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return row.direccion;
                }
            }
        ],
    })

    let tablelistaitem = document.getElementById(
        'listaDireccionesCliente_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true
}

function llenarListaCuentasCliente(id_persona=null,id_cliente=null){
    console.log(id_persona,id_cliente);
    var vardataTables = funcDatatables();
    $('#listaCuentasCliente').DataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        paging:   true,
        searching: true,
        language: vardataTables[0],
        processing: true,
        ajax: rutaCuentasCliente+'/'+id_persona+'/'+id_cliente,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return row.id_cuenta_contribuyente;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.banco;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.tipo_cuenta;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.nro_cuenta;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.nro_cuenta_interbancaria;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.moneda;
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],

    })

    let tablelistaitem = document.getElementById(
        'listaCuentasCliente_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true
}



function selectedProyecto(event){
    let codigo = event.target.options[ event.target.selectedIndex].getAttribute('data-codigo');
    let id_proyecto = event.target.value;
    document.querySelector("form[id='form-requerimiento'] input[name='codigo_proyecto']").value = codigo;

    if(id_proyecto >0){
        let btnMostarPartidas = document.querySelectorAll("button[name='btnMostarPartidas']");
        if(btnMostarPartidas.length >0){
            btnMostarPartidas.forEach(element => {
                element.removeAttribute('disabled');
                element.setAttribute('title','Partidas');
            });
        }
    }
}

//imprimir requerimiento pdf
function ImprimirRequerimientoPdf(){
    var id = document.getElementsByName("id_requerimiento")[0].value;
    window.open('/logistica/imprimir-requerimiento-pdf/'+id+'/0');
    
    // baseUrl = '/logistica/imprimir-requerimiento-pdf/'+id+'/0';
    // $.ajax({
    //     type: 'GET',
    //     url: baseUrl,
    //     // dataType: 'JSON',
    //     success: function(response){  
            
    //     }
    // }).fail( function( jqXHR, textStatus, errorThrown ){
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // });
}

function migrarRequerimiento(){
    var id = $('[name=id_requerimiento]').val();
    var data = 'id_requerimiento='+id;
    console.log('id_requerimiento: '+id);
    $.ajax({
        type: 'POST',
        url: 'migrar_venta_directa',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}