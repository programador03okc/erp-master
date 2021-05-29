
function nuevo_req(){
    data_item=[];
    data=[];
    adjuntos=[];
    adjuntosRequerimiento=[];
    onlyAdjuntosRequerimiento=[];
    $('#form-requerimiento')[0].reset();
    limpiarSelectFuenteDet();
    // autoSelectTipoRequerimientoPorUsuarioEnSesion();
    autoSelectTipoRequerimientoPorDefecto();
    document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det']").setAttribute('hidden',true);

    $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="12"> No hay datos registrados</td></tr>');
    $('#body_adjuntos_requerimiento').html('<tr id="default_tr"><td></td><td colspan="3"> No hay datos registrados</td></tr>');
    $('#body_lista_trazabilidad_requerimiento').html('<tr id="default_tr"><td></td><td colspan="5"> No hay datos registrados</td></tr>');
    $('#estado_doc').text('');
    $('[name=id_usuario_req]').val('');
    $('[name=id_estado_doc]').val('');
    $('[name=id_requerimiento]').val('');
    // vista_extendida();
    var btnImprimirRequerimiento = document.getElementsByName("btn-imprimir-requerimento-pdf");
    disabledControl(btnImprimirRequerimiento,true);
    var btnAdjuntosRequerimiento = document.getElementsByName("btn-adjuntos-requerimiento");
    disabledControl(btnAdjuntosRequerimiento,true);
    var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
    disabledControl(btnTrazabilidadRequerimiento,true);

}

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

// function get_requerimiento_por_codigo(){
//     var codigo = $('[name=codigo]').val();
//     mostrar_requerimiento(codigo);
// }


function changeMonedaSelect(e){
    if( e.target.value == 1){
        document.querySelector("div[id='montoMoneda']").textContent='S/.';
        document.querySelector("form[id='form-requerimiento'] table span[name='simbolo_moneda']").textContent= 'S/.';

    }else if( e.target.value ==2){
        document.querySelector("div[id='montoMoneda']").textContent='$';
        document.querySelector("form[id='form-requerimiento'] table span[name='simbolo_moneda']").textContent= '$';
    }else{
        document.querySelector("div[id='montoMoneda']").textContent='';
        document.querySelector("form[id='form-requerimiento'] table span[name='simbolo_moneda']").textContent= '';
    }

}




function limpiarFormRequerimiento(){
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
            document.querySelector("form[id='form-requerimiento'] span[name='codigo']").textContent = response.data;
        }
    });
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
        if(element.codigo == 'LIMA' || element.codigo == 'Lima'){ // default sede lima
            option.setAttribute('selected','selected');

        }
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
function getDataSelectSedeSinUbigeo(id_empresa = null){
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
                }
            }
        });
    }
    return false;
}

function seleccionarAmacen(data){
    // let firstSede = data[0].id_sede;
    let id_empresa_selected =  document.querySelector("select[id='empresa']").value;
    let selectAlmacen = document.querySelector("div[id='input-group-almacen'] select[name='id_almacen']");
    if(selectAlmacen.options.length>0){
        var i, L = selectAlmacen.options.length - 1;
        for(i = L; i > 0; i--) {
            if(selectAlmacen.options[i].dataset.idEmpresa == id_empresa_selected){
                 if( [4,10,11,12,13,14].includes(parseInt(selectAlmacen.options[i].dataset.idSede)) == true){ ///default almacen lima
                    selectAlmacen.options[i].setAttribute('selected',true);
                }
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
        // grupos.forEach(element => {
        //     if(element.id_grupo ==3){ // proyectos
        //         stateFormRequerimiento(4)
        //     }else{
        //         stateFormRequerimiento(5)
        //     }
        // });
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
        // stateFormRequerimiento(1);

    }else if(option == 3 ){ // uso almacen
        limpiarFormRequerimiento()
        // stateFormRequerimiento(2);
        listar_almacenes();
    
    }else if(option == 4 ){ // uso administracinón
        limpiarFormRequerimiento()
        // stateFormRequerimiento(1);
        
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
 
function makeId(){
    let ID = "";
    let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ( var i = 0; i < 12; i++ ) {
      ID += characters.charAt(Math.floor(Math.random() * 36));
    }
    return ID;
} 

function agregarServicio(){
    var tipo_requerimiento = $('[name=tipo_requerimiento]').val();

    if(tipo_requerimiento >0){
        let item = {
            'id':makeId(),
            'id_detalle_requerimiento': null,
            'id_item': null,
            'codigo': null,
            'part_number': null,
            'des_item': '',
            'cantidad': 1,
            'id_producto': null,
            'id_servicio': 0,
            'id_equipo': null,
            'id_tipo_item': 2,
            'id_unidad_medida': 38,
            'categoria': null,
            'subcategoria': null,
            'precio_unitario':null,
            'subtotal':null,
            'id_tipo_moneda':1,
            'lugar_entrega':null,
            'id_partida':null,
            'cod_partida':null,
            'des_partida':null,
            'id_almacen_reserva':null,
            'almacen_descripcion':null,
            'id_cc_am_filas':null,
            'id_cc_venta_filas': null,
            'tiene_transformacion':false,
            'id_centro_costo':null,
            'codigo_centro_costo':null,
            'estado':1
        };
        data_item.push(item);
        componerTdItemDetalleRequerimiento();
                
    }else{
        alert("Debe seleccionar un tipo de requerimiento");
    }

    
}

function updateInputDescripcionItem(event){
    let nuevoValor = event.target.value;
    let indiceSelected = event.target.dataset.indice;
    data_item.forEach((element, index) => {
        if (index == indiceSelected) {
            data_item[index].des_item = nuevoValor;

        }
    });
}
function updateInputCantidadItem(event){
    let nuevoValor = event.target.value;
    let indiceSelected = event.target.dataset.indice;
    data_item.forEach((element, index) => {
        if (index == indiceSelected) {
            data_item[index].cantidad = nuevoValor;

        }
    });
    updateMontoTotalRequerimiento();

}

function updateInputPrecioUnitarioItem(obj,event){
    let nuevoValor = event.target.value;
    let indiceSelected = event.target.dataset.indice;
    data_item.forEach((element, index) => {
        if (index == indiceSelected) {
            data_item[index].precio_unitario = nuevoValor;

            updateSubtotalItem(obj,indiceSelected);
        }
    });

    updateMontoTotalRequerimiento();
    calcMontoLimiteDePartida();
}

function updateSubtotalItem(obj,indiceSelected){
    let cantidad =obj.parentNode.parentNode.children[5].querySelector("input").value;
    let precio_unitario =obj.parentNode.parentNode.children[6].querySelector("input").value;
    let subtotal = parseFloat(parseInt(cantidad) * parseFloat(precio_unitario?precio_unitario:0))
    let montoSubtotal=(Math.round(subtotal * 100) / 100).toFixed(2);
    
    let IdMoneda =document.querySelector("form[id='form-requerimiento'] select[name='moneda']").value;
    let simboloMoneda ='';
    if( IdMoneda== 1){
        simboloMoneda='S/';
    }else if(IdMoneda == 2){
        simboloMoneda='$';

    }
    
    data_item.forEach((element, index) => {
        if (index == indiceSelected) {
            data_item[index].subtotal = montoSubtotal;
            obj.parentNode.parentNode.children[7].textContent = simboloMoneda+montoSubtotal;
        }
    });
}

function updateMontoTotalRequerimiento(){
    let sumSubTotal=0;
    if(data_item.length > 0){
        data_item.forEach(element => {
            sumSubTotal+= parseFloat(parseInt(element.cantidad) * parseFloat(element.precio_unitario?element.precio_unitario:0));
        });
    }

    let montoTotal=(Math.round(sumSubTotal * 100) / 100).toFixed(2);
    let IdMoneda =document.querySelector("form[id='form-requerimiento'] select[name='moneda']").value;
    let simboloMoneda ='';
    if( IdMoneda== 1){
        simboloMoneda='S/';
    }else if(IdMoneda == 2){
        simboloMoneda='$';

    }

   document.querySelector("form[id='form-requerimiento'] input[name='monto']").value= montoTotal;
   document.querySelector("form[id='form-requerimiento'] table span[name='simbolo_moneda']").textContent= simboloMoneda;
   document.querySelector("form[id='form-requerimiento'] table label[name='total']").textContent= Math.round(montoTotal).toFixed(2);

}

function makeSelectedToSelect(indice, type, data, id, hasDisabled) {
    let html = '';
    switch (type) {
        // case 'categoria':
        //     html = `<select class="form-control" name="categoria" ${hasDisabled} data-indice="${indice}">`;
        //     data.forEach(item => {
        //         if (item.id_categoria == id) {
        //             html += `<option value="${item.id_categoria}" selected>${item.descripcion}</option>`;
        //         } else {
        //             html += `<option value="${item.id_categoria}">${item.descripcion}</option>`;
        //         }
        //     });
        //     html += '</select>';
        //     break;
        // case 'subcategoria':
        //     html = `<select class="form-control" name="subcategoria" ${hasDisabled} data-indice="${indice}" >`;
        //     data.forEach(item => {
        //         if (item.id_subcategoria == id) {
        //             html += `<option value="${item.id_subcategoria}" selected>${item.descripcion}</option>`;
        //         } else {
        //             html += `<option value="${item.id_subcategoria}">${item.descripcion}</option>`;
        //         }
        //     });
        //     html += '</select>';
        //     break;
        // case 'clasificacion':
        //     html = `<select class="form-control" name="clasificacion" ${hasDisabled} data-indice="${indice}"  >`;
        //     data.forEach(item => {
        //         if (item.id_clasificacion == id) {
        //             html += `<option value="${item.id_clasificacion}" selected>${item.descripcion}</option>`;
        //         } else {
        //             html += `<option value="${item.id_clasificacion}">${item.descripcion}</option>`;

        //         }
        //     });
        //     html += '</select>';
        //     break;
        case 'unidad_medida':
            html = `<select class="form-control" name="unidad_medida" ${hasDisabled} data-indice="${indice}" onChange="updateInputUnidadMedidaItem(event);">`;
            data.forEach(item => {
                if (item.id_unidad_medida == id) {
                    html += `<option value="${item.id_unidad_medida}" selected>${item.descripcion}</option>`;
                } else {
                    html += `<option value="${item.id_unidad_medida}">${item.descripcion}</option>`;

                }
            });
            html += '</select>';
            break;
        case 'moneda':
            html = `<select class="form-control" name="moneda" ${hasDisabled} data-indice="${indice}" onChange="updateInputMonedaItem(event);">`;
            data.forEach(item => {
                if (item.id_moneda == id) {
                    html += `<option value="${item.id_moneda}" selected>${item.descripcion}</option>`;
                } else {
                    html += `<option value="${item.id_moneda}">${item.descripcion}</option>`;

                }
            });
            html += '</select>';
            break;

        default:
            break;
    }

    return html;
}

function updateInputUnidadMedidaItem(event){
    let idValor = event.target.value;
    let textValor = event.target.options[event.target.selectedIndex].textContent;
    let indiceSelected = event.target.dataset.indice;

    data_item.forEach((element, index) => {
        if (index == indiceSelected) {
            data_item[index].id_unidad_medida = parseInt(idValor);
            data_item[index].unidad_medida = textValor;

        }
    });
}
function updateInputMonedaItem(event){
    let idValor = event.target.value;
    let textValor = event.target.options[event.target.selectedIndex].textContent;
    let indiceSelected = event.target.dataset.indice;

    data_item.forEach((element, index) => {
        if (index == indiceSelected) {
            data_item[index].id_tipo_moneda = parseInt(idValor);
            data_item[index].moneda = textValor;

        }
    });
}


function llenarTablaListaDetalleRequerimiento(data,selectMoneda,selectUnidadMedida){
    // console.log(data);
    htmls = '<tr></tr>';   
    $('#ListaDetalleRequerimiento tbody').html(htmls);
    var table = document.getElementById("ListaDetalleRequerimiento");

    // console.log(data);
    let cantidadIdPartidas=0;
    let cantidadIdCentroCostos=0;
    for (var a = 0; a < data.length; a++) {
        if(data[a].id_partida >0){
            cantidadIdPartidas++;
        }
        if(data[a].id_centro_costo >0){
            cantidadIdCentroCostos++;
        }
    }
    var tipo_requerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;

    for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(-1);

            if (data[a].id_producto == '' || data[a].id_producto == null) {
                    var id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

                    row.insertCell(0).innerHTML = data[a].id_item ? data[a].id_item : '';
                    row.insertCell(1).innerHTML = data[a].codigo ? data[a].codigo : '';
                    row.insertCell(2).innerHTML =  data[a].part_number ? data[a].part_number : '';
                    row.insertCell(3).innerHTML = ` <textarea  class="form-control" name="descripcion" data-indice="${a}" onkeyup ="updateInputDescripcionItem(event);">${data[a].des_item ? data[a].des_item : ''}</textarea>`;
                    row.insertCell(4).innerHTML = makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, 38, '');
                    row.insertCell(5).innerHTML = `<input type="number" min="0" class="form-control" name="cantidad" data-indice="${a}" onkeyup ="updateInputCantidadItem(event);" value="${data[a].cantidad}">`;
                    row.insertCell(6).innerHTML = `<input type="number" min="0" class="form-control" name="precio_unitario" data-indice="${a}" onkeyup ="updateInputPrecioUnitarioItem(this,event);" value="${data[a].precio_unitario?data[a].precio_unitario:''}">`;
                    // row.insertCell(7).innerHTML = makeSelectedToSelect(a, 'moneda', selectMoneda, 1, '');
                    row.insertCell(7).innerHTML = data[a].subtotal ? data[a].subtotal : '';
                    row.insertCell(8).innerHTML =  data[a].cod_partida ? data[a].cod_partida : '';
                    row.insertCell(9).innerHTML =  data[a].codigo_centro_costo ? data[a].codigo_centro_costo : '';
                    row.insertCell(10).innerHTML =  data[a].motivo ? data[a].motivo : '';
                    row.insertCell(11).innerHTML =  data[a].almacen_reserva ? data[a].almacen_reserva : (data[a].proveedor_razon_social?data[a].proveedor_razon_social:'');
                    
                    var tdBtnAction=null;
                    tdBtnAction = row.insertCell(12);
                    
    
                    var btnAction = '';
                    // tdBtnAction.className = classHiden;
                    var hasAttrDisabled = '';
                    tdBtnAction.setAttribute('width', 'auto');
                    var id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
        
                    btnAction = `<div class="btn-group btn-group-xs" role="group" aria-label="Second group" style=" display: grid; grid-template-columns: 1fr 1fr minmax(auto,1fr); ">`;
                    if (tipo_requerimiento ==3 ) {
                            btnAction += `<button type="button" class="btn btn-warning btn-xs"  name="btnMostarPartidas" data-toggle="tooltip" title="Partidas" onClick=" partidasModal(${a});" ${hasAttrDisabled}><i class="fas fa-money-check"></i></button>`;
                        
                    } 
                    btnAction += `<button type="button" class="btn btn-primary btn-xs" name="btnCentroCostos" data-toggle="tooltip" title="Centro de Costos" style="background: #3c763d;" onClick="centroCostosModal(event, ${a});" ${hasAttrDisabled}><i class="fas fa-donate"></i></button>`;
                    
                    if(tipo_requerimiento !=2){
                        btnAction += `<button type="button" class="btn btn-default btn-xs" name="btnAdjuntarArchivos" data-toggle="tooltip" title="Adjuntos" onClick="archivosAdjuntosModal(event, ${a});" ${hasAttrDisabled}><i class="fas fa-paperclip"></i></button>`;
                    }
                    btnAction += `<button type="button" class="btn btn-danger btn-xs" name="btnMotivo" data-toggle="tooltip" title="Motivo" style="background: #963277;" onClick="motivoModal(event, ${a});" ${hasAttrDisabled}><i class="fas fa-bullseye"></i></button>`;
                    btnAction += `<button type="button" class="btn btn-danger btn-xs"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" onclick="eliminarItemDeListado(this,'${data[a].id}');" ${hasAttrDisabled} ><i class="fas fa-trash-alt"></i></button>`;
                    btnAction += `</div>`;
                    tdBtnAction.innerHTML = btnAction;
              

            } else {
                var id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
      

                row.insertCell(0).innerHTML = data[a].id_item ? data[a].id_item : '';
                row.insertCell(1).innerHTML = data[a].codigo ? data[a].codigo : '';
                row.insertCell(2).innerHTML =  data[a].part_number ? data[a].part_number : '';
                row.insertCell(3).innerHTML = `<span name="descripcion">${data[a].des_item ? data[a].des_item : ''}</span> `;
                row.insertCell(4).innerHTML = makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, data[a].id_unidad_medida, '');
                row.insertCell(5).innerHTML = `<input type="number" min="0" class="form-control" name="cantidad" data-indice="${a}" onkeyup ="updateInputCantidadItem(event);" value="${data[a].cantidad}">`;
                row.insertCell(6).innerHTML = `<input type="number" min="0" class="form-control" name="precio_unitario" data-indice="${a}" onkeyup ="updateInputPrecioUnitarioItem(this,event);" value="${data[a].precio_unitario?data[a].precio_unitario:''}">`;
                // row.insertCell(7).innerHTML = makeSelectedToSelect(a, 'moneda', selectMoneda, data[a].id_unidad_medida, '');
                row.insertCell(7).innerHTML = data[a].subtotal ? data[a].subtotal : '';
                row.insertCell(8).innerHTML =  data[a].cod_partida ? data[a].cod_partida : ''; 
                row.insertCell(9).innerHTML =  data[a].codigo_centro_costo ? data[a].codigo_centro_costo : '';
                row.insertCell(10).innerHTML =  data[a].motivo ? data[a].motivo : '';
                row.insertCell(11).innerHTML =  data[a].almacen_reserva ? data[a].almacen_reserva : (data[a].proveedor_razon_social?data[a].proveedor_razon_social:'');

                var tdBtnAction=null;
                tdBtnAction = row.insertCell(12);


                var btnAction = '';
                // tdBtnAction.className = classHiden;
                var hasAttrDisabled = '';
                tdBtnAction.setAttribute('width', 'auto');
                var id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
    
                btnAction = `<div class="btn-group btn-group-xs" role="group" aria-label="Second group" style=" display: grid; grid-template-columns: 1fr 1fr minmax(auto,1fr); ">`;
                if (tipo_requerimiento ==3 ) {
                        btnAction += `<button type="button" class="btn btn-warning btn-xs"  name="btnMostarPartidas" data-toggle="tooltip" title="Partidas" onClick=" partidasModal(${a});" ${hasAttrDisabled}><i class="fas fa-money-check"></i></button>`;
                } 
                
        
                // btnAction += `<button type="button" class="btn btn-primary btn-xs" name="btnRemplazarItem" data-toggle="tooltip" title="Remplazar" onClick="buscarRemplazarItemParaCompra(this, ${a});" ${hasAttrDisabled}><i class="fas fa-search"></i></button>`;
                btnAction += `<button type="button" class="btn btn-primary btn-xs" name="btnCentroCostos" data-toggle="tooltip" title="Centro de Costos" style="background: #3c763d;" onClick="centroCostosModal(event, ${a});" ${hasAttrDisabled}><i class="fas fa-donate"></i></button>`;
                if(tipo_requerimiento ==3){ // tipo = bienes y servicios
                    // btnAction += `<button type="button" class="btn btn-primary btn-xs" name="btnBuscarEnAlmacen" data-toggle="tooltip" title="Buscar Stock en Almacenes" style="background:#b498d0;" onClick="buscarStockEnAlmacenesModal(${data[a].id_item});" ${hasAttrDisabled}><i class="fas fa-warehouse"></i></button>`;
                    btnAction += `<button type="button" class="btn btn-xs" name="btnAlmacenReservaModal" data-toggle="tooltip" title="Almacén Reserva" onClick="modalAlmacenReserva(this, ${a});" ${hasAttrDisabled} style="background:#b498d0; color: #f5f5f5;"><i class="fas fa-warehouse"></i></button>`;
                }
                if(tipo_requerimiento ==2){ // tipo = CMS
                    btnAction += `<button type="button" class="btn btn-xs" name="btnAlmacenReservaModal" data-toggle="tooltip" title="Almacén Reserva" onClick="modalAlmacenReserva(this, ${a});" ${hasAttrDisabled} style="background:#b498d0; color: #f5f5f5;"><i class="fas fa-warehouse"></i></button>`;
                    btnAction += `<button type="button" class="btn btn-primary btn-xs" name="btnModalSeleccionarCrearProveedor data-toggle="tooltip" title="Proveedor" onClick="modalSeleccionarCrearProveedor(event, ${a});" ${hasAttrDisabled}><i class="fas fa-user-tie"></i></button>`;

                }
                if(tipo_requerimiento !=2){
                    btnAction += `<button type="button" class="btn btn-default btn-xs" name="btnAdjuntarArchivos" data-toggle="tooltip" title="Adjuntos" onClick="archivosAdjuntosModal(event, ${a});" ${hasAttrDisabled}><i class="fas fa-paperclip"></i></button>`;
                }
                btnAction += `<button type="button" class="btn btn-danger btn-xs" name="btnMotivo" data-toggle="tooltip" title="Motivo" style="background: #963277;" onClick="motivoModal(event, ${a});" ${hasAttrDisabled}><i class="fas fa-bullseye"></i></button>`;

                btnAction += `<button type="button" class="btn btn-danger btn-xs"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" onclick="eliminarItemDeListado(this,'${data[a].id}');" ${hasAttrDisabled} ><i class="fas fa-trash-alt"></i></button>`;

                btnAction += `</div>`;
                tdBtnAction.innerHTML = btnAction;
            }
    }
}

function centroCostosModal(event,indice){
    $('#modal-centro-costos').modal({
        show: true
    });
    // let indiceSelected = event.target.dataset.indice;
    document.querySelector("div[id='modal-centro-costos'] label[id='indice_item']").textContent=indice;

    listarCentroCostos();
}

function selectCC(id_cuadro_costos,codigo){

let indiceSeleccionado = document.querySelector("div[id='modal-centro-costos'] label[id='indice_item']").textContent;

if(data_item.length >0){
    data_item.forEach((element, index) => {
        if (index == indiceSeleccionado) {
            data_item[index].id_centro_costo = parseInt(id_cuadro_costos);
            data_item[index].codigo_centro_costo = codigo;

        }
    });
    $('#modal-centro-costos').modal('hide');
    componerTdItemDetalleRequerimiento();


}else{
    alert("hubo un problema, no se puedo encontrar el listado de item para asignarle una partida");
}
}

function listarCentroCostos(){
    $.ajax({
        type: 'GET',
        url: 'mostrar-centro-costos',
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            var html = '';
            response.forEach((padre,index) => {
                if(padre.id_padre == null){
                    html+=`
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading${index}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}" >
                                    ${padre.descripcion} 
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" style="position:absolute; right:20px; margin-top:-5px;" data-toggle="collapse">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse${index}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading${index}" >   
                            <div class="box-body" style="display: block;">`;
                            response.forEach(hijo => {
                                if(padre.id_centro_costo == hijo.id_padre){
                                    if((hijo.id_padre > 0) && (hijo.estado ==1)){
                                        if(hijo.nivel == 2){
                                            html+= `<div class="okc-cc okc-niv-2" onClick="selectCC(${hijo.id_centro_costo} , '${hijo.codigo}');"> ${hijo.codigo} - ${hijo.descripcion} </div>`;
                                        }
                                    }
                                    response.forEach(hijo3 => {
                                        if(hijo.id_centro_costo == hijo3.id_padre){
                                            if((hijo3.id_padre > 0) && (hijo3.estado ==1)){
                                                if(hijo3.nivel == 3){
                                                    html+= `<div class="okc-cc okc-niv-3" onClick="selectCC(${hijo3.id_centro_costo} , '${hijo3.codigo}');"> ${hijo3.codigo} - ${hijo3.descripcion} </div>`;
                                                }
                                            }
                                        }
                                    });
                                }


                            });

                            html+= `</div></div></div>`;
                }
        });
        document.querySelector("div[name='centro-costos-panel']").innerHTML=html;
        

    }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// modal partidas
function partidasModal(indice){  
    // console.log(indice);
    var id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
    var id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
    var usuarioProyectos = false;
        grupos.forEach(element => {
            if(element.id_grupo ==3){ // proyectos
                usuarioProyectos=true
            }
        });
    if (id_grupo > 0){
  
            $('#modal-partidas').modal({
                show: true,
                backdrop: 'true'
            });
            document.querySelector("div[id='modal-partidas'] label[id='indice']").textContent =  indice;
            listarPartidas(id_grupo,id_proyecto>0?id_proyecto:null);

        
        
    }else{
        alert("Ocurrio un problema, no se puedo seleccionar el grupo al que pertence el usuario.");
    }
    
}
function listarPartidas(id_grupo,id_proyecto){
    
    if(id_proyecto == 0 || id_proyecto == '' || id_proyecto == null){
        id_proyecto = '';
    }
    // console.log('listar_partidas/'+id_grupo+'/'+id_proyecto);
    $.ajax({
        type: 'GET',
        url: 'listar-partidas/'+id_grupo+'/'+id_proyecto,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            
            $('#listaPartidas').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function apertura(id_presup){
    if ($("#pres-"+id_presup+" ").attr('class') == 'oculto'){
        $("#pres-"+id_presup+" ").removeClass('oculto');
        $("#pres-"+id_presup+" ").addClass('visible');
    } else {
        $("#pres-"+id_presup+" ").removeClass('visible');
        $("#pres-"+id_presup+" ").addClass('oculto');
    }
}



function eliminarItemDeListado(obj,id){
    let row = obj.parentNode.parentNode.parentNode.parentNode;
    let idCcAmFilas =data_item.find(item => item.id_producto == id)?data_item.find(item => item.id_producto == id).id_cc_am_filas:null;
    let tieneTransformacion = document.querySelector("form[id='form-requerimiento'] input[name='tiene_transformacion']").value;

    row.remove(row);
    

    data_item = data_item.filter((item, i) => item.id != id);
    updateMontoTotalRequerimiento();
    componerTdItemDetalleRequerimiento();
    if(idCcAmFilas !=null){
        if(tieneTransformacion == false ){
            agregarItemDeTablaDetalleCuadroCostos(idCcAmFilas);
        }else{
            agregarItemDeTablaDetalleCuadroCostosItemTransformado(idCcAmFilas);
    
        }

    }
}

function agregarItemDeTablaDetalleCuadroCostos(idCcAmFilas){
    detalleItemsCC.forEach(element => {
        if(element.id_cc_am_filas == idCcAmFilas){
            tempDetalleItemsCC.push(element);
            llenarDetalleCuadroCostos(tempDetalleItemsCC);
        }
    });
}
function agregarItemDeTablaDetalleCuadroCostosItemTransformado(idCcAmFilas){
    itemsConTransformacionList.forEach(element => {
        if(element.id_cc_am_filas == idCcAmFilas){
            tempItemsConTransformacionList.push(element);
            llenarItemsTransformados(tempItemsConTransformacionList);
        }
    });
}

function componerTdItemDetalleRequerimiento(){
    var data = data_item;
    // var selectCategoria=[];
    // var selectSubCategoria=[];
    // var selectClasCategoria=[];
    var selectMoneda=[];
    var selectUnidadMedida=[];
    if (dataSelect.length > 0) {
            // selectCategoria = dataSelect[0].categoria;
            // selectSubCategoria = dataSelect[0].subcategoria; 
            // selectClasCategoria = dataSelect[0].clasificacion; 
            selectMoneda = dataSelect[0].moneda;
            selectUnidadMedida = dataSelect[0].unidad_medida;

            llenarTablaListaDetalleRequerimiento(data,selectMoneda,selectUnidadMedida);

    } else {
        getDataAllSelect().then(function (response) {
            if (response.length > 0) {
                // console.log(response);
                    dataSelect = response;
                    // selectCategoria = response[0].categoria;
                    // selectSubCategoria = response[0].subcategoria; 
                    // selectClasCategoria = response[0].clasificacion; 
                    selectMoneda = response[0].moneda;
                    selectUnidadMedida = response[0].unidad_medida;
                    llenarTablaListaDetalleRequerimiento(data,selectMoneda,selectUnidadMedida);

            } else {
                alert('No se pudo obtener data de select de item');
            }

        }).catch(function (err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })
    }
    // validarObjItemsParaCompra();
}

function selectPartida(id_partida){
    var codigo = $("#par-"+id_partida+" ").find("td[name=codigo]")[0].innerHTML;
    var descripcion = $("#par-"+id_partida+" ").find("td[name=descripcion]")[0].innerHTML;
    var importe_total = $("#par-"+id_partida+" ").find("td[name=importe_total]")[0].innerHTML;
 

    $('#modal-partidas').modal('hide');
    $('[name=id_partida]').val(id_partida);
    $('[name=cod_partida]').val(codigo);
    $('[name=des_partida]').val(descripcion);

    idPartidaSelected = id_partida;
    codigoPartidaSelected = codigo;
    partidaSelected = {
        'id_partida': id_partida,
        'codigo': codigo,
        'descripcion': descripcion,
        'importe_total': importe_total
    };

    let indice_modal_partida = document.querySelector("div[id='modal-partidas'] label[id='indice']").textContent;
    if(indice_modal_partida >=0){
        if(data_item.length >0){
            data_item.forEach((element, index) => {
                if (index == indice_modal_partida) {
                    // itemSelected= data_item[index];
                    data_item[index].id_partida = parseInt(id_partida);
                    data_item[index].cod_partida = codigoPartidaSelected;
                    data_item[index].des_partida = descripcion;
        
                }
            });
            calcMontoLimiteDePartida();
            registrarPartida();
        }else{
            alert("hubo un problema, no se puedo encontrar el listado de item para asignarle una partida");
        }
    }else{
        alert("hubo un problema, no se pudo cargar el id_item para vincularlo a una partida");

    }

    componerTdItemDetalleRequerimiento();


    // itemSelected = {
    //     'id_item': document.getElementsByName('id_item')[0].value,
    //     'codigo_item': document.getElementsByName('codigo_item')[0].value,
    //     'descripcion':document.getElementsByName('descripcion_item')[0].value,
    //     'unidad':document.getElementsByName('unidad_medida_item')[0].value,
    //     'cantidad':document.getElementsByName('cantidad_item')[0].value,
    //     'precio_unitario':document.getElementsByName('precio_ref_item')[0].value,
    //     'id_partida':id_partida,
    //     'codigo_partida':codigoPartidaSelected
    // }

    // document.querySelectorAll('[id^="pres"]')[0].setAttribute('class','oculto' );

}

function registrarPartida(){

    if( ListOfPartidaSelected.filter(function(partida){ return partida.id_partida === idPartidaSelected }).length  == 0){
        partidaSelected.monto_acumulado=0;
        ListOfPartidaSelected.push(partidaSelected);
    }
}

function calcMontoLimiteDePartida(){

    let partidasItems = {};
    let output = [];

    data_item.forEach(item => {

        //Si la id_partida no existe en partidasItems entonces
        //la creamos e inicializamos el arreglo de items. 
        if( !partidasItems.hasOwnProperty(item.id_partida)){
            partidasItems[item.id_partida] = {
                items: []
            }
        }
          //Agregamos los datos de items. 
            partidasItems[item.id_partida].items.push({
                'id_producto': item.id_producto,
                'descripcion': item.des_item,
                // id_partida: item.id_partida,
                'subtotal': parseFloat(item.subtotal)
            })
    });

    // console.log(Object.keys(partidasItems));
    if(Object.keys(partidasItems).length > 0){
        Object.values(partidasItems).forEach((element,indice) => {
            // console.log(Object.keys(partidasItems)[indice]);
            // console.log(element);
            if(!partidasItems[Object.keys(partidasItems)[indice]].hasOwnProperty('suma_total')){
                // console.log(element.items);
                let sumaSubtotal=(element.items).reduce((accum, obj) => accum + (obj.subtotal>0?obj.subtotal:0), 0);
                let sumaSubtotalPorPartida = parseFloat((Math.round(sumaSubtotal * 100) / 100).toFixed(2));
                partidasItems[Object.keys(partidasItems)[indice]]['suma_total']=sumaSubtotalPorPartida;

                    ListOfPartidaSelected.forEach(partida => {
                        if((partida.id_partida == Object.keys(partidasItems)[indice]) && (sumaSubtotalPorPartida > parseFloat(partida.importe_total))){
                            output.push(`Se a excedido +${sumaSubtotalPorPartida- parseFloat(partida.importe_total)} el importe asiganado para la partida "${partida.codigo} - ${partida.descripcion}", la partida tiene un monto máximo de ${partida.importe_total}`);
                        }
                    });
            }
        });
    }


    // console.log(partidasItems);
    if(output.length >0){
        alert(output);
    }

    return false;
}


// fuente 
function agregarFuenteModal(){
    $('#modal-agregar-fuente').modal({
        show: true,
        backdrop: 'true'
    });

    llenarTablaListaFuentes();
}

function llenarTablaListaFuentes(){
    var vardataTables = funcDatatables();

    $('#listaFuente').dataTable({
        "order": [[ 0, "asc" ]],
        'dom': vardataTables[1],
        'buttons': [
        ],
        'language' : vardataTables[0],
        'serverSide' : false,
        'bInfo': false,
        "bLengthChange" : false,
        'paging': true,
        'searching': false,
        'bDestroy' : true,
        'ajax': 'mostrar-fuente',
        'columns': [
            {'data': 'id_fuente'},
            {'render':
                function (data, type, row, meta){
                    return meta.row+1;
                }
            },
            {'data': 'descripcion'},
            {'render':
            function (data, type, row, meta){
                return `<div class="btn-group btn-group-xs  " role="group" aria-label="Second group">
                                <button type="button" class="btn btn-info btn-xs" name="btnEditarFuente" data-toggle="tooltip" title="Editar Fuente" onclick="editarFuente(this, ${row.id_fuente});"><i class="fas fa-edit"></i></button>
                                <button type="button" class="btn btn-warning btn-xs" name="btnAgregarDetalleFuente" data-toggle="tooltip" title="Agregar Detalle Fuente" onclick="agregarDetalleFuenteModal(event, ${row.id_fuente});"><i class="fas fa-cookie-bite"></i></button>
                                <button type="button" class="btn btn-danger btn-xs" name="btnAnularFuente" data-toggle="tooltip" title="Anular Fuente" onclick="anularFuente(${row.id_fuente});"><i class="fas fa-trash-alt"></i></button>
                        </div>`;
            }
        }
        ],
        'columnDefs': [
        { 'aTargets': [0], 'sClass': 'invisible'}
 
    ],
    });
}

function limpiarSelectFuenteDet(){
    let selectElement = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det'] select[name='fuente_det_id']");

    if(selectElement !=null){
        while (selectElement.options.length > 0) {                
            selectElement.remove(0);
        }    
    }
}

function selectFuente(event,fuente_id=null){
    
    if(fuente_id ==null){
        fuente_id = event.target.value;
    }
    
    $.ajax({
        type: 'GET',
        url: 'mostrar-fuente-detalle/'+fuente_id,
        dataType: 'JSON',
        success: function(response){
            if(response.length >0){
                //mostrar select fuente_det 
                document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det']").removeAttribute('hidden');
                let selectElement = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det'] select[name='fuente_det_id']");
                // limpiar select
                limpiarSelectFuenteDet();
                // llenar select
                response.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.descripcion;
                    option.value = element.id_fuente_det;
                    selectElement.add(option);
                });
                

            }else{
                //mantener oculto fuente_det
                limpiarSelectFuenteDet();
                document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det']").setAttribute('hidden',true);

            }

  
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}





function agregarFuente(){
    let nombre_fuente = document.querySelector("div[id='modal-agregar-fuente'] input[name='nombre_fuente']").value;
    if(nombre_fuente.length >0){
        $.ajax({
            type: 'POST',
            url: 'guardar-fuente',
            data: {'descripcion':nombre_fuente},
            dataType: 'JSON',
            beforeSend: function(){
            },
            success: function(response){
                // console.log(response);
                if (response.status == '200') {
                    agregarFuenteEnSelect(response.id_fuente,nombre_fuente);
                    $('#listaFuente').DataTable().ajax.reload();
                }else {
                    alert('hubo un error, No se puedo guardar');
                }
            }
        });
    }else{
        alert("Debe ingresar una descripción");
    }

}

function agregarFuenteEnSelect(id,descripcion){
    let selectElement = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente'] select[name='fuente_id']");
    let option = document.createElement("option");
    option.text = descripcion;
    option.value = id;
    selectElement.add(option);
    
}

function anularFuente(id_fuente){
    if(id_fuente >0){
        $.ajax({
            type: 'POST',
            url: 'anular-fuente',
            data: {'id_fuente':id_fuente},
            dataType: 'JSON',
            beforeSend: function(){
            },
            success: function(response){
                // console.log(response);
                if (response.status == '200') {
                    removerFuenteEnSelect(id_fuente);
                    $('#listaFuente').DataTable().ajax.reload();
                    alert("Se anuló la fuente");
                }else {
                    alert('hubo un error, No se puedo guardar');
                }
            }
        });
    }
}

function removerFuenteEnSelect(id_fuente){
    let selectElement = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente'] select[name='fuente_id']");
    for (var i=0; i<selectElement.length; i++) {
        if (selectElement.options[i].value == id_fuente)
        selectElement.remove(i);
    }
}

function editarFuente(obj,id_fuente){
    let tr =obj.parentNode.parentNode.parentNode;
    let tdDescripcion = tr.childNodes[2];
    let TextDescripcion = tr.childNodes[2].textContent;
    tr.childNodes[2].textContent=''
    var input = document.createElement("input");
    input.type = "text";
    input.className = "form-control input-sm"; 
    input.value = TextDescripcion; 
    tdDescripcion.appendChild(input);

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "btn btn-success btn-sm"; 
    btn.innerHTML = '<i class="fas fa-save fa-lg"></i>'; 
    btn.onclick =function() {
        actualizarFuente(id_fuente,this);
    } ; 
    tdDescripcion.appendChild(btn);
}

function actualizarFuente(id_fuente, obj){
    let nuevaDescripcion =obj.parentNode.querySelector("input").value;

    if(id_fuente >0){
        $.ajax({
            type: 'POST',
            url: 'actualizar-fuente',
            data: {'id_fuente':id_fuente,'descripcion':nuevaDescripcion},
            dataType: 'JSON',
            beforeSend: function(){
            },
            success: function(response){
                // console.log(response);
                if (response.status == '200') {
                    // $('#listaFuente').DataTable().ajax.reload();
                    alert("Se actualizo la fuente");
                    obj.parentNode.parentNode.childNodes[2].textContent= nuevaDescripcion;
                }else {
                    alert('hubo un error, No se puedo actualizar');
                }
            }
        });
    }
}

// detalle fuente

function agregarDetalleFuenteModal(){
    $('#modal-agregar-detalle-fuente').modal({
        show: true,
        backdrop: 'true'
    });

    llenarTablaListaDetalleFuentes();
} 

function llenarTablaListaDetalleFuentes(){
    let fuente_id = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente'] select[name='fuente_id']").value;

    $.ajax({
        type: 'GET',
        url: 'mostrar-fuente-detalle/'+fuente_id,
        dataType: 'JSON',
        success: function(response){
            if(response.length >0){
            
                construirTablaDetalleFuente(response);
                

            }else{
                //mantener oculto fuente_det
                limpiarSelectFuenteDet();
                document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det']").setAttribute('hidden',true);

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}


function construirTablaDetalleFuente(data){
    var vardataTables = funcDatatables();
    $('#listaDetalleFuente').dataTable({
        "order": [[ 0, "asc" ]],
        'dom': vardataTables[1],
        'buttons': [
        ],
        'language' : vardataTables[0],
        'serverSide' : false,
        'bInfo': false,
        "bLengthChange" : false,
        'paging': true,
        'searching': false,
        'bDestroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_fuente_det'},
            {'render':
                function (data, type, row, meta){
                    return meta.row+1;
                }
            },
            {'data': 'descripcion'},
            {'render':
            function (data, type, row, meta){
                return `<div class="btn-group btn-group-xs  " role="group" aria-label="Second group">
                                <button type="button" class="btn btn-info btn-xs" name="btnEditarDetalleFuente" data-toggle="tooltip" title="Editar Detalle Fuente" onclick="editarDetalleFuente(this, ${row.id_fuente_det});"><i class="fas fa-edit"></i></button>
                                <button type="button" class="btn btn-danger btn-xs" name="btnAnularDetalleFuente" data-toggle="tooltip" title="Anular Detalle Fuente" onclick="anularDetalleFuente(${row.id_fuente_det});"><i class="fas fa-trash-alt"></i></button>
                        </div>`;
            }
        }
        ],
        'columnDefs': [
        { 'aTargets': [0], 'sClass': 'invisible'}
 
    ],
    });
}


function agregarDetalleFuente(){
    let id_fuente = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente'] select[name='fuente_id']").value;
    let nombre_detalle_fuente = document.querySelector("div[id='modal-agregar-detalle-fuente'] input[name='nombre_detalle_fuente']").value;
    if(nombre_detalle_fuente.length >0){
        $.ajax({
            type: 'POST',
            url: 'guardar-detalle-fuente',
            data: {'id_fuente':id_fuente,'descripcion':nombre_detalle_fuente},
            dataType: 'JSON',
            beforeSend: function(){
            },
            success: function(response){
                if (response.status == '200') {
                    agregarDetalleFuenteEnSelect(response.id_fuente_det,nombre_detalle_fuente);
                    llenarTablaListaDetalleFuentes();
                }else {
                    alert('hubo un error, No se puedo guardar');
                }
            }
        });
    }else{
        alert("Debe ingresar una descripción");
    }

}

function agregarDetalleFuenteEnSelect(id_fuente_det,descripcion){
    let selectElement = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det'] select[name='fuente_det_id']");
    let option = document.createElement("option");
    option.text = descripcion;
    option.value = id_fuente_det;
    selectElement.add(option);
}



function editarDetalleFuente(obj,id_fuente_det){
    let tr =obj.parentNode.parentNode.parentNode;
    let tdDescripcion = tr.childNodes[2];
    let TextDescripcion = tr.childNodes[2].textContent;
    tr.childNodes[2].textContent=''
    var input = document.createElement("input");
    input.type = "text";
    input.className = "form-control input-sm"; 
    input.value = TextDescripcion; 
    tdDescripcion.appendChild(input);

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "btn btn-success btn-sm"; 
    btn.innerHTML = '<i class="fas fa-save fa-lg"></i>'; 
    btn.onclick =function() {
        actualizarDetalleFuente(id_fuente_det,this);
    } ; 
    tdDescripcion.appendChild(btn);
}

function actualizarDetalleFuente(id_fuente_det,obj){
    let nuevaDescripcion =obj.parentNode.querySelector("input").value;

    if(id_fuente_det >0){
        $.ajax({
            type: 'POST',
            url: 'actualizar-detalle-fuente',
            data: {'id_fuente_det':id_fuente_det,'descripcion':nuevaDescripcion},
            dataType: 'JSON',
            beforeSend: function(){
            },
            success: function(response){
                if (response.status == '200') {
                    alert("Se actualizo el detalle de fuente");
                    obj.parentNode.parentNode.childNodes[2].textContent= nuevaDescripcion;
                }else {
                    alert('hubo un error, No se puedo actualizar');
                }
            }
        });
    }
}


function anularDetalleFuente(id_fuente_det){
    if(id_fuente_det >0){
        $.ajax({
            type: 'POST',
            url: 'anular-detalle-fuente',
            data: {'id_fuente_det':id_fuente_det},
            dataType: 'JSON',
            beforeSend: function(){
            },
            success: function(response){
                // console.log(response);
                if (response.status == '200') {
                    removerDetalleFuenteEnSelect(id_fuente_det);
                    llenarTablaListaDetalleFuentes();
                    alert("Se anuló el detalle de fuente");
                }else {
                    alert('hubo un error, No se puedo guardar');
                }
            }
        });
    }
}

function removerDetalleFuenteEnSelect(id_fuente_det){
    let selectElement = document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det'] select[name='fuente_det_id']");
    for (var i=0; i<selectElement.length; i++) {
        if (selectElement.options[i].value == id_fuente_det)
        selectElement.remove(i);
    }
}