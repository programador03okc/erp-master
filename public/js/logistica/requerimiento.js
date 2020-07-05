var rutaListaRequerimientoModal, 
rutaMostrarRequerimiento,
rutaGuardarRequerimiento,
rutaActualizarRequerimiento,
rutaAnularRequerimiento,
rutaSedeByEmpresa,
rutaCopiarRequerimiento,
rutaTelefonosCliente,
rutaDireccionesCliente;

function inicializar( _rutaLista,
    _rutaMostrarRequerimiento,
    _rutaGuardarRequerimiento,
    _rutaActualizarRequerimiento,
    _rutaAnularRequerimiento,
    _rutaSedeByEmpresa,
    _rutaCopiarRequerimiento,
    _rutaTelefonosCliente,
    _rutaDireccionesCliente
    ) {
    rutaListaRequerimientoModal = _rutaLista;
    rutaMostrarRequerimiento = _rutaMostrarRequerimiento;
    rutaGuardarRequerimiento = _rutaGuardarRequerimiento;
    rutaActualizarRequerimiento = _rutaActualizarRequerimiento;
    rutaAnularRequerimiento = _rutaAnularRequerimiento;
    rutaSedeByEmpresa = _rutaSedeByEmpresa;
    rutaCopiarRequerimiento = _rutaCopiarRequerimiento;
    rutaTelefonosCliente = _rutaTelefonosCliente;
    rutaDireccionesCliente = _rutaDireccionesCliente;
}

let data = [];
let data_item=[];
var adjuntos=[];
var id_detalle_requerimiento=0;
var obs=false;
var gobal_observacion_requerimiento=[];


var ListOfPartidaSelected = [];
var ListOfItems = [];
var partidaSelected ={};
var idPartidaSelected=0;
var codigoPartidaSelected='';
var itemSelected ={};
var UsoDePartida =[];
var userSession =[];

let tpOptCom  ={};

$(function(){
    $.ajax({
        type: 'GET',
        url: '/session-rol-aprob',
        data: data,
        success: function(response){
            // console.log(response); 
            userSession=response;
            document.getElementsByName('id_usuario_session')[0].value= response.id_usuario;
        }
    });

     

    var idGral = localStorage.getItem('idGral');

    if (idGral != null){
        mostrar_requerimiento(idGral);
        localStorage.clear();
        changeStateButton('historial');
    }
    resizeSide();

    $('#form-obs-sustento').on('submit', function(){
        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar el sustento?');
        if (ask == true){
            $.ajax({
                type: 'POST',
                // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/guardar_sustento',
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    // console.log(response);
                    
                    $('.loading').remove();
                    if (response.status == 'ok') {
                        alert('Se agregó sustento al Requerimiento');
                        mostrar_requerimiento(response.data);
                        $('#modal-sustento').modal('hide');
                    }else {
                        alert('No se puedo Guardar sustento al requerimiento');
                        $('#modal-sustento').modal('hide');
                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });
    changeOptComercialSelect(); // label's title of option comercial 
});

function changeOptComercialSelect(){
    let optCom =getActualOptComercial();
    document.getElementById('title-option-comercial').textContent = 'Código '+optCom.texto;
    switch (optCom.id) {
        case '1':
            document.getElementsByName('codigo_occ')[0].setAttribute('maxlength', '14');
            document.getElementsByName('codigo_occ')[0].setAttribute('placeholder', 'OKC0000-0000000');
            
            break;
            case '2':
                document.getElementsByName('codigo_occ')[0].setAttribute('maxlength', '11');
                document.getElementsByName('codigo_occ')[0].setAttribute('placeholder', 'OKC00-00000');

            break;
        default:
            break;
    }

}

function getActualOptComercial(){
    let selection = document.getElementsByName('tpOptCom')[0].options.selectedIndex;
 
    tpOptCom.texto = document.getElementsByName('tpOptCom')[0].options[selection].textContent;
    tpOptCom.id  = document.getElementsByName('tpOptCom')[0].value;
    // console.log(tpOptCom);

    return tpOptCom;
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
                backdrop: 'static'
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

function selectCodigoCC(){
    let codigoCC = document.querySelector('div[id="modal-cuadro_costos_comercial"] label[id="codigo"]').textContent;
    let descripcionCC = document.querySelector('div[id="modal-cuadro_costos_comercial"] label[id="descripcion"]').textContent;
    document.querySelector('form[id="form-requerimiento"] input[name="codigo_occ"]').value = codigoCC;
    document.querySelector('form[id="form-requerimiento"] input[name="occ"]').value = descripcionCC;

    $('#modal-cuadro_costos_comercial').modal('hide');

}

function nuevo_req(){
    data_item=[];
    data=[];
    $('#form-requerimiento')[0].reset();
    $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="7"> No hay datos registrados</td></tr>');
    $('#estado_doc').text('');
    $('[name=id_usuario_req]').val('');
    $('[name=id_estado_doc]').val('');
    $('[name=id_requerimiento]').val('');


}



function disabledControl(element,value){   
    // console.log("disable control"); 
    var i;
    for (i = 0; i < element.length; i++) {
        if(value === false){
            element[i].removeAttribute("disabled");
            element[i].classList.remove("disabled");

        }else{
            element[i].setAttribute("disabled","true");
        }
    }
    return null;
}

function handleKeyDown(event){
    const key = event.key;
    if(key == 'Backspace' || key == 'Delete'){
        $('[name=id_item]').val(0);
        $('[name=codigo_item]').val('SIN CODIGO');
        $('[name=id_producto]').val(0);
        $('[name=id_servicio]').val(0);
        $('[name=id_equipo]').val(0);
    }

}

function handleKeyPress(event){    
    $('[name=id_item]').val(0);
    $('[name=codigo_item]').val('SIN CODIGO');
    $('[name=id_producto]').val(0);
    $('[name=id_servicio]').val(0);
    $('[name=id_equipo]').val(0);

}
function handlePaste(event){    
    $('[name=id_item]').val(0);
    $('[name=codigo_item]').val('SIN CODIGO');
    $('[name=id_producto]').val(0);
    $('[name=id_servicio]').val(0);
    $('[name=id_equipo]').val(0);

}

function modalRequerimiento(){
    $('#modal-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });
    listarRequerimiento('ONLY_ACTIVOS');
}

function listarRequerimiento(viewAnulados) {
    
    let url=rutaListaRequerimientoModal+'/'+viewAnulados;
    // if(viewAnulados == true){
        // console.log(url);
    //     url='/logistica/requerimientos_sin_estado';
    // }
    var vardataTables = funcDatatables();
    $('#listaRequerimiento').dataTable({
        bDestroy: true,
        order: [[1, 'desc']],
        info:     true,
        iDisplayLength:10,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        ajax:url,
        columns: [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'tipo_req_desc'},
            {'data': 'alm_req_concepto'},
            {'data': 'usuario'},
            {'data': 'fecha_requerimiento'},
            {'data': 'estado_doc'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [5, 'desc']
        ]


    });

    // var vardataTables = funcDatatables();
    // $('#listaRequerimiento').dataTable({
    //     'language' : vardataTables[0],
    //     'processing': true,
    //     "scrollX": true,
    //     "info":     false,
    //     "iDisplayLength":10,
    //     "paging":   true,
    //     "searching": true,
    //     "bDestroy": true,
    //     'ajax': url,
    //     'columns': [
    //         {'data': 'id_requerimiento'},
    //         {'data': 'codigo'},
    //         {'data': 'tipo_req_desc'},
    //         {'data': 'alm_req_concepto'},
    //         {'data': 'usuario'},
    //         {'data': 'fecha_requerimiento'},
    //         {'data': 'estado_doc'}
    //     ],
    //     'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    //     'order': [
    //         [5, 'desc']
    //     ]
    // });
}

$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaRequerimiento tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaRequerimiento').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_requerimiento').text(idTr);
        
    });


    $('#checkViewTodos').on('click',function(){
        if(document.getElementById('checkViewTodos').checked){
            listarRequerimiento('SHOW_ALL');
        }else{
            listarRequerimiento('ONLY_ACTIVOS');
        }
    });
});

function inicializarSelect(){
    listar_almacenes();
    listar_sedes();

}

function selectRequerimiento(){
    // console.log("selectRequerimiento");
    inicializarSelect();
    var id = $('#id_requerimiento').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
        clearForm(form);
        changeStateButton('historial');
        mostrar_requerimiento(id);
        verTrazabilidadRequerimiento(id);
        // console.log($(":file").filestyle('disabled'));
    $('#modal-requerimiento').modal('hide');
}

function get_requerimiento_por_codigo(){
    var codigo = $('[name=codigo]').val();
    mostrar_requerimiento(codigo);
}


function mostrar_requerimiento(IdorCode){
    // console.log("mostrar_requeriniento");

    document.getElementById('btnCopiar').removeAttribute("disabled");

    if (! /^[a-zA-Z0-9]+$/.test(IdorCode)) { // si tiene texto
        url = rutaMostrarRequerimiento+'/'+0+'/'+IdorCode;
    }else{
        url = rutaMostrarRequerimiento+'/'+IdorCode+'/'+0;
    }

    let items={};
    $(":file").filestyle('disabled', false);
    data_item = [];
    baseUrl = url;
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            data = response;
            // console.log(response);
            if(response['requerimiento'] !== undefined){
                
                $('[name=id_usuario_req]').val(response['requerimiento'][0].id_usuario);
                $('[name=rol_usuario]').val(response['requerimiento'][0].id_rol);
                $('[name=id_estado_doc]').val(response['requerimiento'][0].id_estado_doc);
                $('[name=id_requerimiento]').val(response['requerimiento'][0].id_requerimiento);
                $('[name=tipo_requerimiento]').val(response['requerimiento'][0].id_tipo_requerimiento);
                $('[name=codigo]').val(response['requerimiento'][0].codigo);
                $('[name=concepto]').val(response['requerimiento'][0].concepto);
                $('[name=fecha_requerimiento]').val(response['requerimiento'][0].fecha_requerimiento);
                $('[name=prioridad]').val(response['requerimiento'][0].id_prioridad);
                $('[name=empresa]').val(response['requerimiento'][0].id_empresa);
                $('[name=sede]').val(response['requerimiento'][0].id_sede);
                $('[name=id_area]').val(response['requerimiento'][0].id_area);
                $('[name=id_grupo]').val(response['requerimiento'][0].id_grupo);
                $('[name=nombre_area]').val(response['requerimiento'][0].area_descripcion);
                $('[name=moneda]').val(response['requerimiento'][0].id_moneda);
                $('[name=periodo]').val(response['requerimiento'][0].id_periodo);
                 $('[name=id_op_com]').val(response['requerimiento'][0].id_op_com);
                $('[name=codigo_opcion]').val(response['requerimiento'][0].codigo_op_com);
                $('[name=nombre_opcion]').val(response['requerimiento'][0].descripcion_op_com);
                $('[name=observacion]').val(response['requerimiento'][0].observacion);
                
                $('[name=sede]').val(response['requerimiento'][0].id_sede);
                $('[name=tipo_cliente]').val(response['requerimiento'][0].tipo_cliente);
                $('[name=id_persona]').val(response['requerimiento'][0].id_persona);
                $('[name=dni_persona]').val(response['requerimiento'][0].dni_persona);
                $('[name=nombre_persona]').val(response['requerimiento'][0].nombre_persona);
                $('[name=id_cliente]').val(response['requerimiento'][0].id_cliente);
                $('[name=cliente_ruc]').val(response['requerimiento'][0].cliente_ruc);
                $('[name=cliente_razon_social]').val(response['requerimiento'][0].cliente_razon_social);
                $('[name=direccion_entrega]').val(response['requerimiento'][0].direccion_entrega);
                $('[name=telefono_cliente]').val(response['requerimiento'][0].telefono);
                $('[name=ubigeo]').val(response['requerimiento'][0].id_ubigeo_entrega);
                $('[name=name_ubigeo]').val(response['requerimiento'][0].name_ubigeo);
                $('[name=id_almacen]').val(response['requerimiento'][0].id_almacen);
                $('[name=monto]').val(response['requerimiento'][0].monto);
                

                $('#estado_doc').text(response['requerimiento'][0].estado_doc);
                $('#estado_doc').removeClass();
                $('#estado_doc').addClass("label label-"+response['requerimiento'][0].bootstrap_color);
                
                if(response['requerimiento'][0].area_descripcion == 'PROYECTOS' || response['requerimiento'][0].area_descripcion == 'DPTO. FORMULACIÓN' || response['requerimiento'][0].area_descripcion == 'DPTO. EJECUCIÓN'){
                    // document.getElementById('section-proyectos').setAttribute('class', 'col');
                    document.querySelector("form[id='form-requerimiento'] div[id='input-group-proyecto']").removeAttribute('hidden');

                }
                $('[name=cantidad_aprobaciones]').val(response['aprobaciones']);
                /* detalle */
                var detalle_requerimiento = response['det_req'];
                if(detalle_requerimiento.length === 0){
                    alert("El Requerimiento No Tiene Item");
                }
                // console.log(detalle_requerimiento);                
                for (x=0; x<detalle_requerimiento.length; x++){
                    let adjunto=[];
                        items ={
                        'id_item':detalle_requerimiento[x].id_item,
                        'id_tipo_item':detalle_requerimiento[x].id_tipo_item,
                        'id_producto':detalle_requerimiento[x].id_producto,
                        'id_servicio':detalle_requerimiento[x].id_servicio,
                        'id_equipo':detalle_requerimiento[x].id_equipo,
                        'id_requerimiento':response['requerimiento'][0].id_requerimiento,
                        'id_detalle_requerimiento':detalle_requerimiento[x].id_detalle_requerimiento,
                        'cod_item':detalle_requerimiento[x].codigo_item,
                        'des_item':detalle_requerimiento[x].descripcion?detalle_requerimiento[x].descripcion:detalle_requerimiento[x].descripcion_adicional, 
                        'id_unidad_medida':detalle_requerimiento[x].id_unidad_medida,
                        'unidad':detalle_requerimiento[x].unidad_medida,
                        'cantidad':detalle_requerimiento[x].cantidad,
                        'precio_referencial':detalle_requerimiento[x].precio_referencial,
                        'fecha_entrega':detalle_requerimiento[x].fecha_entrega,
                        'lugar_entrega':detalle_requerimiento[x].lugar_entrega?detalle_requerimiento[x].lugar_entrega:"",
                        'id_partida':detalle_requerimiento[x].id_partida,
                        'cod_partida':detalle_requerimiento[x].codigo_partida,
                        'des_partida':detalle_requerimiento[x].descripcion_partida,
                        'obs':detalle_requerimiento[x].obs,
                        'estado':detalle_requerimiento[x].estado
                    };
                        for(j=0; j<detalle_requerimiento[x].adjunto.length; j++){
                        adjunto.push({ 'id_adjunto':detalle_requerimiento[x].adjunto[j].id_adjunto,
                            'archivo':detalle_requerimiento[x].adjunto[j].archivo,
                            'estado':detalle_requerimiento[x].adjunto[j].estado,
                            'id_detalle_requerimiento':detalle_requerimiento[x].adjunto[j].id_detalle_requerimiento,
                            'id_requerimiento':response['requerimiento'][0].id_requerimiento
                            });
                        }
                        items['adjunto']=adjunto;
                        data_item.push(items);
                    }
                    // fill_table_detalle_requerimiento(data_item);
                    // console.log(data_item);
                    
                    llenar_tabla_detalle_requerimiento(data_item);
                    llenarTablaAdjuntosRequerimiento(response['requerimiento'][0].id_requerimiento);
                    
                    // desbloquear el imprimir requerimiento
                    var btnImprimirRequerimientoPdf = document.getElementsByName("btn-imprimir-requerimento-pdf");
                    disabledControl(btnImprimirRequerimientoPdf,false);
                    
                // get observaciones  
                let htmlObservacionReq = '';
                    // console.log(response.observacion_requerimiento);
                    if(response.observacion_requerimiento.length > 0){
                        gobal_observacion_requerimiento = response.observacion_requerimiento;
                        response.observacion_requerimiento.forEach(element => {
                            htmlObservacionReq +='<div class="col-sm-12">'+
                        '<blockquote class="blockquoteObservation box-shadow" onclick="levantarObservacion('+element.id_observacion+');" data-toggle="tooltip" data-placement="top" title="Haga clic para agregar una Sustentación">'+
                        '<p>'+element.descripcion+'</p>'+
                        '<footer><cite title="Source Title">'+element.nombre_completo+'</cite></footer>'+
                        '</blockquote>'+
                    '</div>'; 
                        });
                    }

                let obsReq = document.getElementById('observaciones_requerimiento');
                obsReq.innerHTML = '</br>'+htmlObservacionReq;

            }else{
                alert("no se puedo obtener el requerimiento para mostrar");
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function levantarObservacion(id_observacion){
    // console.log(id_observacion);
    var id_req = $('[name=id_requerimiento]').val();
    if(id_req > 0){
        openSustento(id_observacion,id_req);
    }else{
        alert("Error, el id es <= 0");
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

// modal area grupos
function modal_area(){
    var id_emp = $('[name=empresa]').val();
    if(id_emp >0){

        $('#modal-empresa-area').modal({
            show: true,
            backdrop: 'static'
        });
        cargarEstOrg(id_emp);
    }else{
        alert("Debe seleccionar  la empresa");
        $('[name=id_empresa]').focus();
    }
    
}

// function areaSelectModal(sede, grupo, area, text){
    // console.log('sede:'+sede+' grupo:'+grupo+' area:'+area);
    // $('[name=id_grupo]').val(grupo);
    // $('[name=id_area]').val(area);
    // $('[name=nombre_area]').val(text);
    // $('#modal-empresa-area').modal('hide');
    
    // if(grupo === 5){
    //         document.getElementById('section-proyectos').setAttribute('class', 'row')
    // }
// }


// function getIdGrupo(){
//     var area = document.getElementById("area");
//     var id_grupo = area.options[area.selectedIndex].parentNode.dataset.dataIdGrupo;
//     // console.log(id_grupo);
//     if(id_grupo !== undefined){
//         document.getElementsByName("id_grupo")[0].value = id_grupo;
//     }else{
//         document.getElementsByName("id_grupo")[0].value = 0;
//     }
// }

function limpiarFormularioDetalleRequerimiento(){
    $('[name=estado]').val('');
    $('[name=id_item]').val('');
    $('[name=id_producto]').val('');
    $('[name=id_servicio]').val('');
    $('[name=id_equipo]').val('');
    $('[name=id_tipo_item]').val('');
    $('[name=id_detalle_requerimiento]').val('');
    $('[name=codigo_item]').val('');
    $('[name=descripcion_item]').val('');
    $('[name=unidad_medida_item]').val('');
    $('[name=cantidad_item]').val('');
    $('[name=precio_ref_item]').val('');
    $('[name=fecha_entrega_item]').val(new Date().toJSON().slice(0, 10));
    $('[name=lugar_entrega_item]').val('');
    $('[name=id_partida]').val('');
    $('[name=cod_partida]').val('');
    $('[name=des_partida]').val('');
}

function agregarItem(){
    var table = document.getElementById("ListaDetalleRequerimiento");
    var len = table.querySelectorAll('tr').length;
    for (var i=0; i < len; i++){
    // console.log(table.querySelectorAll('tr')[i].getAttribute('id'));
    
        if ( table.querySelectorAll('tr')[i].getAttribute('id') == "default_tr"){
        // table.querySelectorAll('tr')[i].setAttribute('class', 'yourID')
            table.deleteRow(i);
        }
    }
    let item = get_data_detalle_requerimiento();
    // console.log(item);
    // verficar codigo de item exista para poder ser agregado ////////
    if(item.cod_item ==="" || item.cod_item ===null || item.cod_item ===undefined ){
        alert("Campo vacío - Debe selecione un item o escriba uno");
        return null;
    }
    
    // if(parseInt(item.id_partida) <= 0 ||  (Number.isNaN(item.id_partida) ==true) ){
    //     alert("Debe seleccionar una partida");
    //     return null;
    // }

    // let = passMount=calcMontoLimiteDePartida();
    // console.log(passMount);
    
    // if(passMount == false){

    //     /////////////////////////////////////////
        let tam_data_item = data_item.length;
        data_item.push(item);
        console.log(data_item);
        
        let update_tam_data_item= data_item.length;
        if(update_tam_data_item > tam_data_item  ){
            setTextInfoAnimation("Agregado!");
            statusBtnOpenProyectoModal('DESHABILITAR');

        }else{
            setTextInfoAnimation("Error!");
        }
    // }else{
    //     setTextInfoAnimation("Excede el monto de la partida");

    // }


        llenar_tabla_detalle_requerimiento(data_item);



    limpiarFormularioDetalleRequerimiento();

    let btnVerUltimasCompras = document.getElementsByName('btnVerUltimasCompras')[0];
    btnVerUltimasCompras.setAttribute('disabled',true);
 }

function statusBtnOpenProyectoModal(value){
    switch (value) {
        case 'DESHABILITAR':
            document.querySelector('form[id="form-requerimiento"] button[id="btnOpenModalProyecto"]').setAttribute('disabled', true);
            document.querySelector('form[id="form-requerimiento"] button[id="btnOpenModalProyecto"]').setAttribute('title', 'No puede Cambiar de Proyecto, Existe uno o más items vinculados con el proyecto');
            
            break;
            case 'HABILITAR':
                document.querySelector('form[id="form-requerimiento"] button[id="btnOpenModalProyecto"]').removeAttribute('disabled');
                document.querySelector('form[id="form-requerimiento"] button[id="btnOpenModalProyecto"]').setAttribute('title', 'Seleccionar Proyecto');
            
            break;
    
        default:
            break;
    }
    
}

function calcMontoLimiteDePartida(){
     let passMount= false;
    if( ListOfPartidaSelected.filter(function(partida){ return partida.id_partida === idPartidaSelected }).length  == 0){
        ListOfPartidaSelected.push(partidaSelected);
    }
    // console.log('itemSelected');
    // console.log(itemSelected);
    ListOfItems.push(itemSelected);
    
    // console.log('ListOfItems');
    // console.log(ListOfItems);
    let counts =[];
    let partidaList =[];
    let htmlStatusPartida='';
    // calc limite de monto de items por partida
    if(ListOfItems.length >0){
    
        
        // first, convert data into a Map with reduce
            counts = ListOfItems.reduce((prev, curr) => {
            let count = prev.get(curr.id_partida) || 0;
            let sum_prec_ref = (parseFloat(curr.precio_referencial) * parseFloat(curr.cantidad)) + parseInt(count);
            prev.set(curr.id_partida, sum_prec_ref );
            return prev;
        }, new Map());

        console.log([...counts]);

        partidaList = ListOfItems.reduce((prev, curr) => {
            prev.set(curr.id_partida, curr.codigo_partida );
            return prev;
        }, new Map());

        console.log([...partidaList]);




        // then, map your counts object back to an array
        let reducedObjArr = [...counts].map(([id_partida, suma_total]) => {
            return {id_partida, suma_total}
        })

        // console.log(reducedObjArr);

        // agregando descripcion (nombre de partida) 
 

        reducedObjArr.map((item,i)=>{
                ListOfPartidaSelected.filter(function(partida){ 
                    return partida.id_partida == item.id_partida 
                });
        });

        console.log(reducedObjArr);

        reducedObjArr.map((item)=>{
            if(item.id_partida == [...partidaList][0][0] ){
                item.codigo = [...partidaList][0][1];
            }
        });

        // calcular si excede
        ListOfPartidaSelected.forEach(function(element) {
            let st =reducedObjArr.filter(vendor => (vendor.id_partida == element.id_partida));
            // console.log(st);

        if(st.length > 0){
            if(st[0].suma_total > element.importe_total){
                alert("Ha sido superado el importe total de partida "+element.descripcion+" [ importe acumulado: "+st[0].suma_total+"]" )
                let lastIndex =ListOfItems.length-1;
                // console.log(lastIndex);
                ListOfItems.splice(lastIndex,1);
                // console.log(ListOfItems);
                
                passMount= true;
            }else{
                passMount= false;
            }
        }else{
            passMount= false;

        }         

        });

    }
    return passMount;
}

function llenar_tabla_detalle_requerimiento(data_item){
    // console.log(data_item);
    
    limpiarTabla('ListaDetalleRequerimiento');
    // limpiando
    htmls ='<tr></tr>';
    $('#ListaDetalleRequerimiento tbody').html(htmls);
    // mejorarndo recorrer data_item para llenar tabla detalle requerimiento...
    var table = document.getElementById("ListaDetalleRequerimiento");
    // console.log("data ",data_item);
    // console.log("length ",data_item.length);
    
    let widthGroupBtnAction='auto';
    let classHiden='';
    let classDisabled='disabled';
    let classBtnAdjuntos ='';
    var id_estado_doc = document.getElementsByName('id_estado_doc')[0].value;
    var id_usuario_session = document.getElementsByName('id_usuario_session')[0].value;
    var id_usuario_req = document.getElementsByName('id_usuario_req')[0].value;
    
    if((id_estado_doc == 3 || id_estado_doc == 1 ) && id_usuario_session==id_usuario_req ){
        classHiden='';
    }else{
        classHiden='hidden';
        document.getElementById('btnEditar').setAttribute("disabled","true");
        document.getElementById('btnAnular').setAttribute("disabled","true");
    }
    if(id_estado_doc.length <=0 || id_usuario_req.length <=0 ){
        classHiden='';
        classDisabled='';


    }

    for(var a=0;a < data_item.length;a++){
        if(data_item[a].estado >0){
            
            var row = table.insertRow(-1);
            let descripcion_unidad = '';
    
            if(data_item[a].id_producto > 0){
                descripcion_unidad = data_item[a].unidad;
            }else if(data_item[a].id_servicio > 0){
                descripcion_unidad = "Servicio";
            }else if(data_item[a].id_equipo >0){
                descripcion_unidad = "Equipo";
            }else{
                descripcion_unidad = data_item[a].unidad;
            }
            row.insertCell(0).innerHTML = data_item[a].id_item?data_item[a].id_item:'0';
            row.insertCell(1).innerHTML = data_item[a].cod_item?data_item[a].cod_item:'0';
            row.insertCell(2).innerHTML = data_item[a].des_item?data_item[a].des_item:'-';
            row.insertCell(3).innerHTML = descripcion_unidad;
            row.insertCell(4).innerHTML = data_item[a].cantidad?data_item[a].cantidad:'0';
            row.insertCell(5).innerHTML = data_item[a].precio_referencial?data_item[a].precio_referencial:'0';
            row.insertCell(6).innerHTML = data_item[a].fecha_entrega?data_item[a].fecha_entrega:null;
            row.insertCell(7).innerHTML = data_item[a].lugar_entrega?data_item[a].lugar_entrega:'-';

            var tdBtnAction = row.insertCell(8);
            // tdBtnAction.className = classHiden;
            tdBtnAction.setAttribute('width',widthGroupBtnAction);
            tdBtnAction.innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
            '<button class="btn btn-secondary btn-sm '+classHiden+' '+classDisabled+'"  name="btnEditarItem" data-toggle="tooltip" title="Editar" onClick="detalleRequerimientoModal(event, '+a+');" ><i class="fas fa-edit"></i></button>'+
            '<button class="btn btn-danger btn-sm '+classHiden+' '+classDisabled+'"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" onclick="eliminarItemDetalleRequerimiento(event, '+a+');" ><i class="fas fa-trash-alt"></i></button>'+
            '<button class="btn btn-primary btn-sm '+ classBtnAdjuntos+'" name="btnAdjuntarArchivos" data-toggle="tooltip" title="Adjuntos" onClick="archivosAdjuntosModal(event, '+a+');"><i class="fas fa-paperclip"></i></button>'+
            '</div>';

        }
    }

}


function get_data_requerimiento(){
    let tipo_req = document.querySelector("select[name='tipo_requerimiento']").value;
    let requerimiento = {};
    // console.log(tipo_req);
    tipo_requerimiento = tipo_req;
    id_requerimiento = document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;
    codigo = document.querySelector("form[id='form-requerimiento'] input[name='codigo']").value;
    concepto = document.querySelector("form[id='form-requerimiento'] input[name='concepto']").value;
    fecha_requerimiento = document.querySelector("form[id='form-requerimiento'] input[name='fecha_requerimiento']").value;
    id_prioridad = document.querySelector("form[id='form-requerimiento'] select[name='prioridad']").value;
    id_empresa = document.querySelector("form[id='form-requerimiento'] select[name='empresa']").value;
    id_sede = document.querySelector("form[id='form-requerimiento'] select[name='sede']").value;
    id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
    id_area = document.querySelector("form[id='form-requerimiento'] input[name='id_area']").value;
    nombre_area = document.querySelector("form[id='form-requerimiento'] input[name='nombre_area']").value;
    id_moneda = document.querySelector("form[id='form-requerimiento'] select[name='moneda']").value;
    id_periodo = document.querySelector("form[id='form-requerimiento'] select[name='periodo']").value;
    id_op_com = document.querySelector("form[id='form-requerimiento'] input[name='id_op_com']").value;
    id_rol = document.querySelector("form[id='form-requerimiento'] select[name='rol_usuario']").value;
    codigo_occ = document.querySelector("form[id='form-requerimiento'] input[name='codigo_occ']").value;
    id_sede = document.querySelector("form[id='form-requerimiento'] select[name='sede']").value;
    tipo_cliente = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']").value;
    id_cliente = document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value;
    id_persona = document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value;
    direccion_entrega = document.querySelector("form[id='form-requerimiento'] input[name='direccion_entrega']").value;
    telefono = document.querySelector("form[id='form-requerimiento'] input[name='telefono_cliente']").value;
    ubigeo = document.querySelector("form[id='form-requerimiento'] input[name='ubigeo']").value;
    id_almacen = document.querySelector("form[id='form-requerimiento'] select[name='id_almacen']").value;
    almacen_id_sede =document.querySelector("select[name='id_almacen']").options[document.querySelector("select[name='id_almacen']").selectedIndex].dataset.idSede;
    almacen_id_empresa =document.querySelector("select[name='id_almacen']").options[document.querySelector("select[name='id_almacen']").selectedIndex].dataset.idEmpresa;
    observacion = document.querySelector("form[id='form-requerimiento'] textarea[name='observacion']").value;
    monto = document.querySelector("form[id='form-requerimiento'] input[name='monto']").value;

    requerimiento = {
        id_requerimiento,
        tipo_requerimiento,
        codigo,
        concepto,
        fecha_requerimiento,
        id_prioridad,
        id_empresa,
        id_sede,
        id_grupo,
        id_area,
        nombre_area,
        id_moneda,
        id_periodo,
        id_op_com,
        id_rol,
        codigo_occ,
        tipo_cliente,
        id_cliente,
        id_persona,
        direccion_entrega,
        telefono,
        ubigeo,
        id_almacen,
        almacen_id_sede,
        almacen_id_empresa,
        observacion,
        monto
        
    };
return requerimiento;
}

function get_data_detalle_requerimiento(){

 
    var id_item = $('[name=id_item]').val();
    var id_tipo_item = $('[name=id_tipo_item]').val();
    var id_producto = $('[name=id_producto]').val();
    var id_servicio = $('[name=id_servicio]').val();
    var id_equipo = $('[name=id_equipo]').val();
    var id_detalle_requerimiento = $('[name=id_detalle_requerimiento]').val();
    var cod_item = $('[name=codigo_item]').val();
    var des_item = $('[name=descripcion_item]').val();
    // var id_unidad_medida = $('[name=unidad_medida_item]').val() !=="" ?$('[name=unidad_medida_item]').val():0;
    var id_unidad_medida = $('[name=unidad_medida_item]').val();
    // var und = document.getElementsByName("unidad_medida_item")[0];
    // var und_text = und.options[und.selectedIndex].text;   
    var und_text = $('[name=unidad_medida_item]').find('option:selected').text();
    var cantidad = $('[name=cantidad_item]').val();
    var precio_referencial = $('[name=precio_ref_item]').val();
    var fecha_entrega = $('[name=fecha_entrega_item]').val();
    var lugar_entrega = $('[name=lugar_entrega_item]').val();
    var id_partida = $('[name=id_partida]').val();
    var cod_partida = $('[name=cod_partida]').val();
    var des_partida = $('[name=des_partida]').val();
    if($('[name=estado]').val() === ""){
        var estado = 1;
    }else{
        var estado = $('[name=estado]').val();
        
    }

    let item = {
        'id_item':parseInt(id_item),
        'id_tipo_item':parseInt(id_tipo_item),
        'id_producto':parseInt(id_producto),
        'id_servicio':parseInt(id_servicio),
        'id_equipo':parseInt(id_equipo),
        'id_detalle_requerimiento':parseInt(id_detalle_requerimiento),
        'cod_item':cod_item,
        'des_item':des_item,
        'id_unidad_medida':parseInt(id_unidad_medida),
        'unidad':und_text,
        'cantidad':parseFloat(cantidad),
        'precio_referencial':parseFloat(precio_referencial),
        'fecha_entrega':fecha_entrega,
        'lugar_entrega':lugar_entrega,
        'id_partida':parseInt(id_partida),
        'cod_partida':cod_partida,
        'des_partida':des_partida,
        'estado':parseInt(estado)
        };
        return item;
}

function aceptarCambiosItem(){ // del modal-detalle-requerimiento
    var id_det = $('[name=id_detalle_requerimiento]').val();
    var id_req = $('[name=id_requerimiento]').val();
    let item = get_data_detalle_requerimiento();
    if(indice >= 0){
        update_data_item(indice, item);
        $('#modal-detalle-requerimiento').modal('hide');

    }else{
        alert("El indice no es numérico");
    }
}

function update_data_item(indice,item){
    data_item[indice]=item;
    llenar_tabla_detalle_requerimiento(data_item);
}

function eliminarItemDetalleRequerimiento(event,index){
    event.preventDefault();
    
    if(index  !== undefined){ // editando item
        let item = data_item[index]; 
        // console.log(data_item[index].id_item);
        actualizarMontoLimiteDePartida(data_item[index].id_item,'ELIMINAR');
        item.estado=0;
        // console.log(data_item.length);
        
        let tamDataItem = data_item.length;
        let numEstadoCero =0;
        data_item.forEach(element => {
            if(element.estado == 0){
                numEstadoCero++;
            }
        });
        if(numEstadoCero == tamDataItem){
            statusBtnOpenProyectoModal('HABILITAR');
        }

        alert("Se cambio el estado del Item, guarde el Requerimiento para salvar los cambios");
        llenar_tabla_detalle_requerimiento(data_item);

    }



}
// modal detalle 
var indice='';
function detalleRequerimientoModal(event,index){
    // console.log(data_item);

    $('#form-detalle-requerimiento')[0].reset();
    event.preventDefault();
    var btnAceptarCambio = document.getElementsByName("btn-aceptar-cambio");
    var btnAgregarCambio = document.getElementsByName("btn-agregar-item");
    if(index  !== undefined){ // editando item
        let item = data_item[index]; 
 
      
        indice = index;       
        fill_input_detalle_requerimiento(item);
        controlUnidadMedida();
        disabledControl(btnAgregarCambio,true);
        disabledControl(btnAceptarCambio,false);
    }else{
        disabledControl(btnAgregarCambio,false);
        disabledControl(btnAceptarCambio,true);
    }
    var tipo = $('[name=tipo_requerimiento]').val();
    
    if (tipo == 2){        
        var sede = $('[name=sede]').val();
        var almacen = $('select[name=id_almacen]').val();
        console.log(almacen);
        
        if (sede !== null && sede !== '' &&  sede !== undefined ){
            if (almacen !== null && almacen !== '' && almacen !== undefined ){
                $('#modal-detalle-requerimiento').modal({
                    show: true,
                    backdrop: 'static'
                });
                $('[name=id_almacen]').show();
    
                cargar_almacenes(sede);
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='fecha_entrega_item']").value='';
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='lugar_entrega_item']").value='';
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='des_partida']").value='';
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='id_partida']").value='';
                document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-fecha_entrega']").setAttribute('hidden',true);
                document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-lugar_entrega']").setAttribute('hidden',true);
                document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-partida']").setAttribute('hidden',true);
    
            }else{
                alert('Debe seleccionar un almacen.');
            }
        } else {
            alert('Debe seleccionar una sede.');
        }
    }
    else if (tipo == 1){
        $('#modal-detalle-requerimiento').modal({
            show: true,
            backdrop: 'static'
        });
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='fecha_entrega_item']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='lugar_entrega_item']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='des_partida']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='id_partida']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-fecha_entrega']").removeAttribute('hidden');
        document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-lugar_entrega']").removeAttribute('hidden');
        document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-partida']").removeAttribute('hidden');
    }else if(tipo ==3){
        $('#modal-detalle-requerimiento').modal({
            show: true,
            backdrop: 'static'
        });
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='fecha_entrega_item']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='lugar_entrega_item']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='des_partida']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='id_partida']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-fecha_entrega']").setAttribute('hidden',true);
        document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-lugar_entrega']").setAttribute('hidden',true);
        document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-partida']").setAttribute('hidden',true);
    }
    actualizarMontoLimiteDePartida();
}

function actualizarMontoLimiteDePartida(id_item,option){
switch (option) {
    case 'ELIMINAR':
        const newListOfItems = ListOfItems.filter(word => word.id_item != id_item);
        ListOfItems = newListOfItems;
        // console.log(ListOfItems);
        let counts =[];
        let htmlStatusPartida='';
        // calc limite de monto de items por partida
        if(ListOfItems.length >0){
        
            
            // first, convert data into a Map with reduce
                counts = ListOfItems.reduce((prev, curr) => {
                let count = prev.get(curr.id_partida) || 0;
                prev.set(curr.id_partida, (parseFloat(curr.precio_referencial) *parseFloat(curr.cantidad)) + count);
                return prev;
            }, new Map());
    
            // console.log([...counts]);
    
            // then, map your counts object back to an array
            let reducedObjArr = [...counts].map(([id_partida, suma_total]) => {
                return {id_partida, suma_total}
            })
            // console.log('reducedObjArr');
            // console.log(reducedObjArr);
            // agregando descripcion (nombre de partida) 
            
             reducedObjArr.map((item,i)=>{
                // console.log(item);
                // console.log(ListOfPartidaSelected.filter(function(partida){ return partida.id_partida == item.id_partida }).length  > 0);
                     ListOfPartidaSelected.filter(function(partida){ 
                        return partida.id_partida == item.id_partida 
                    });
 
            });

 
            
            ListOfPartidaSelected.forEach(function(element) {
                let st =reducedObjArr.filter(vendor => (vendor.id_partida == element.id_partida));
                // console.log(st);
                
                if(st[0] !==undefined){
                    if(st[0].suma_total > element.importe_total){
                        alert("Ha sido superado el importe total de partida "+element.descripcion+" [importe limite: "+element.importe_total+", importe acumulado: "+st[0].suma_total+"]" )
                    }
                }
            });
        }
        break;
    default:
        break;
}
}

function fill_input_detalle_requerimiento(item){
    $('[name=id_tipo_item]').val(item.id_tipo_item);
    $('[name=id_item]').val(item.id_item);
    $('[name=id_producto]').val(item.id_producto);
    $('[name=id_servicio]').val(item.id_servicio);
    $('[name=id_equipo]').val(item.id_equipo);
    $('[name=id_detalle_requerimiento]').val(item.id_detalle_requerimiento);
    $('[name=codigo_item]').val(item.cod_item);
    $('[name=descripcion_item]').val(item.des_item);
    $('[name=unidad_medida_item]').val(item.id_unidad_medida);
    $('[name=cantidad_item]').val(item.cantidad);
    $('[name=precio_ref_item]').val(item.precio_referencial);
    $('[name=fecha_entrega_item]').val(item.fecha_entrega);
    $('[name=lugar_entrega_item]').val(item.lugar_entrega);
    $('[name=id_partida]').val(item.id_partida);
    $('[name=cod_partida]').val(item.cod_partida);
    $('[name=des_partida]').val(item.des_partida);
    $('[name=estado]').val(item.estado);
}

//modal adjunta archivos
function archivosAdjuntosModal(event,index){
    event.preventDefault();


    if(data_item.length >0){
        id_detalle_requerimiento = data_item[index].id_detalle_requerimiento;
        obs = data_item[index].obs;
        $('[name=id_requerimiento]').val(data_item[index].id_requerimiento);
            // console.log('id_detalle_requerimiento',id_detalle_requerimiento);
            // console.log(data_item[index]);
        if(data_item[index].id_detalle_requerimiento >0){ // es un requerimiento traido de la base de datos\

            $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
                show: true,
                backdrop: 'static'
            });
            get_data_archivos_adjuntos(data_item[index].id_detalle_requerimiento);
            
        }else{ //no existe id_detalle_requerimiento => es un nuevo requerimiento
            alert("es nuevo requerimiento.... debe guardar el requerimiento primero");
            
            
        }
    }
    
}


function get_data_archivos_adjuntos(index){
    adjuntos=[];
    limpiarTabla('listaArchivos');
    baseUrl = '/logistica/mostrar-archivos-adjuntos/'+index;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                for (x=0; x<response.length; x++){
                    id_detalle_requerimiento= response[x].id_detalle_requerimiento;
                        adjuntos.push({ 
                            'id_adjunto':response[x].id_adjunto,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
            llenar_tabla_archivos_adjuntos(adjuntos);
            
            }else{
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData =  row.insertCell(0);
                tdSinData.setAttribute('colspan','5');
                tdSinData.setAttribute('class','text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}

function llenar_tabla_archivos_adjuntos(adjuntos){
    limpiarTabla('listaArchivos');
    htmls ='<tr></tr>';
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    for(var a=0;a < adjuntos.length;a++){
        var row = table.insertRow(a+1);
        var tdIdArchivo =  row.insertCell(0);
            tdIdArchivo.setAttribute('class','hidden');
            tdIdArchivo.innerHTML = adjuntos[a].id_adjunto?adjuntos[a].id_adjunto:'0';
        var tdIdDetalleReq =  row.insertCell(1);
            tdIdDetalleReq.setAttribute('class','hidden');
            tdIdDetalleReq.innerHTML = adjuntos[a].id_detalle_requerimiento?adjuntos[a].id_detalle_requerimiento:'0';
        row.insertCell(2).innerHTML = a+1;
        row.insertCell(3).innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '<button'+
        '    class="btn btn-danger btn-sm "'+
        '    name="btnEliminarArchivoAdjunto"'+
        '    onclick="eliminarArchivoAdjunto('+a+','+adjuntos[a].id_adjunto+')"'+
        '    title="Eliminar Archivo"'+
        '>'+
        '    <i class="fas fa-trash"></i>'+
        '</button>'+
        '</div>';

    }
    return null;
}

function eliminarArchivoAdjunto(indice,id_adjunto){

    // document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] input[name='nombre_archivo']").value;
    if(id_adjunto >0){
        var ask = confirm('¿Desea eliminar este archivo ?');
        if (ask == true){
            $.ajax({
                type: 'PUT',
                url: 'eliminar-archivo-adjunto-detaller-requerimiento/'+id_adjunto,
                dataType: 'JSON',
                success: function(response){
                    if(response.status == 'ok'){
                        alert("Archivo Eliminado");
                        get_data_archivos_adjuntos(id_detalle_requerimiento);
        
                    }else{
                        alert("No se pudo eliminar el archivo")
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            return false;
        }
    }else{
        only_adjuntos.splice(indice,1 );
        adjuntos.splice(indice,1);
        imprimir_tabla_adjuntos();

    }    

}

let only_adjuntos=[];
function agregarAdjunto(event){ //agregando nuevo archivo adjunto
    let id_req = document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;

    //  console.log(event.target.value);
     let fileList = event.target.files;
     let file = fileList[0];

     let extension = file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase(); // assuming that this file has any extension
    //  console.log(extension);
    if (extension === 'dwg' 
        || extension === 'dwt' 
        || extension === 'cdr' 
        || extension === 'back' 
        || extension === 'backup' 
        || extension === 'psd' 
        || extension === 'sql' 
        || extension === 'exe' 
        || extension === 'html' 
        || extension === 'js' 
        || extension === 'php' 
        || extension === 'ai' 
        || extension === 'mp4' 
        || extension === 'mp3' 
        || extension === 'avi' 
        || extension === 'mkv' 
        || extension === 'flv' 
        || extension === 'mov' 
        || extension === 'wmv' 
        ) {
            alert('Extensión de archivo incorrecta (NO se permite .'+extension+').  La entrada del archivo se borra.');
            event.target.value = '';
        }
        else {


            let archivo ={
                id_adjunto: 0,
                id_requerimiento: id_req,
                id_detalle_requerimiento: id_detalle_requerimiento,
                archivo:file.name,
                fecha_registro: new Date().toJSON().slice(0, 10),
                estado: 1
                // file:event.target.files[0]
            }
            let only_file = event.target.files[0]
            adjuntos.push(archivo);
            only_adjuntos.push(only_file);
            // console.log("agregar adjunto");
            // console.log(adjuntos);
            // console.log(only_adjuntos);
            imprimir_tabla_adjuntos();
            
    }
}

function imprimir_tabla_adjuntos(){
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    var indicadorTd='';
    for(var a=0;a < adjuntos.length;a++){
        var row = table.insertRow(-1);

        if(adjuntos[a].id_adjunto ==0){
            indicadorTd="green"; // si es nuevo
        }
        var tdIdArchivo =  row.insertCell(0);
        tdIdArchivo.setAttribute('class','hidden');
        tdIdArchivo.innerHTML = adjuntos[a].id_adjunto?adjuntos[a].id_adjunto:'0';
        var tdIdDetalleReq =  row.insertCell(1);
        tdIdDetalleReq.setAttribute('class','hidden');
        tdIdDetalleReq.innerHTML = 0;
        var tdNumItem = row.insertCell(2);
        tdNumItem.innerHTML = a+1;
        var tdNameFile = row.insertCell(3);
        tdNameFile.innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        tdNameFile.setAttribute('class',indicadorTd);
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '<button'+
        '    class="btn btn-danger btn-sm "'+
        '    name="btnEliminarArchivoAdjunto"'+
        '    onclick="eliminarArchivoAdjunto('+a+','+adjuntos[a].id_adjunto+')"'+
        '    title="Eliminar Archivo"'+
        '>'+
        '    <i class="fas fa-trash"></i>'+
        '</button>'+
        '</div>';
    }
}

function guardarAdjuntos(){
    
    // console.log(obs);
    let id_req = $('[name=id_requerimiento]').val();
    if(id_req < 0){
        alert("error 790: GuardarAdjunto");
    }
    
    // console.log(adjuntos);
    // console.log(only_adjuntos);
    let id_requerimiento = id_req;
    let id_detalle_requerimiento = adjuntos[0].id_detalle_requerimiento;

    const onlyNewAdjuntos = adjuntos.filter(id => id.id_adjunto == 0); // solo enviar los registros nuevos

        var myformData = new FormData();        
        // myformData.append('archivo_adjunto', JSON.stringify(adjuntos));
        for(let i=0;i<only_adjuntos.length;i++){
            myformData.append('only_adjuntos[]', only_adjuntos[i]);
            
        }
        
        myformData.append('detalle_adjuntos', JSON.stringify(onlyNewAdjuntos));
        myformData.append('id_requerimiento', id_requerimiento);
        myformData.append('id_detalle_requerimiento', id_detalle_requerimiento);
    
        baseUrl = 'guardar-archivos-adjuntos-detalle-requerimiento';
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            // dataType: 'JSON',
            url: baseUrl,
            success: function(response){
                // console.log(response);     
                if (response > 0){
                    alert("Archivo(s) Guardado(s)");
                    only_adjuntos=[];
                    get_data_archivos_adjuntos(id_detalle_requerimiento);
                    let ask = confirm('¿Desea seguir agregando más archivos ?');
                    if (ask == true){
                        return false;
                    }else{
                        $('#modal-adjuntar-archivos-detalle-requerimiento').modal('hide');
                    }
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
}

function limpiarTabla(idElement){
    // console.log("limpiando tabla....");
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--)
    {
        table.deleteRow(i);
    }
    return null;
}

// modal catalogo items
function catalogoItemsModal(){   
    var tipo = $('[name=tipo_requerimiento]').val();
    if (tipo == 1 || tipo ==3){
        $('#modal-catalogo-items').modal({
            show: true,
            backdrop: 'static'
        });
        listarItems();
    }
    else if(tipo == 2){
        var almacen = $('[name=id_almacen]').val();
        saldosModal(almacen);
    }
}

function listar_almacenes(){
    $.ajax({
        type: 'GET',
        url: 'listar_almacenes',
        dataType: 'JSON',
        success: function(response){
            // console.log(response.data);
            var option = '';
            for (var i=0; i<response.data.length; i++){
                if (response.data.length == 1){
                    option+='<option data-id-sede="'+response.data[i].id_sede+'" data-id-empresa="'+response.data[i].id_empresa+'" value="'+response.data[i].id_almacen+'" selected>'+response.data[i].codigo+' - '+response.data[i].descripcion+'</option>';
                } else {
                    option+='<option data-id-sede="'+response.data[i].id_sede+'" data-id-empresa="'+response.data[i].id_empresa+'" value="'+response.data[i].id_almacen+'">'+response.data[i].codigo+' - '+response.data[i].descripcion+'</option>';
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
function listar_sedes(){
    $.ajax({
        type: 'GET',
        url: 'mostrar-sede',
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            var option = '';
            for (var i=0; i<response.length; i++){
                if (response.length == 1){
                    option+='<option data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_sede+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                } else {
                    option+='<option data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_sede+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                }
            }
            $('[name=sede]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaItems tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaItems').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idItem = $(this)[0].children[0].innerHTML;
        var idProd = $(this)[0].children[1].innerHTML;
        var idServ = $(this)[0].children[2].innerHTML;
        var idEqui = $(this)[0].children[3].innerHTML;
        var codigo = $(this)[0].children[4].innerHTML;
        var descri = $(this)[0].children[5].innerHTML;
        $('.modal-footer #id_item').text(idItem);
        $('.modal-footer #codigo').text(codigo);
        $('.modal-footer #descripcion').text(descri);
        $('.modal-footer #id_producto').text(idProd);
        $('.modal-footer #id_servicio').text(idServ);
        $('.modal-footer #id_equipo').text(idEqui);
    });
});

function listarItems() {
    var vardataTables = funcDatatables();
    $('#listaItems').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'scrollY':        '50vh',
        'scrollCollapse': true,
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        "scrollX": true,
        'ajax': '/logistica/mostrar_items',
        'columns': [
            {'data': 'id_item'},
            {'data': 'id_producto'},
            {'data': 'id_servicio'},
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida_descripcion'}
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},
            { 'aTargets': [1], 'sClass': 'invisible'},
            { 'aTargets': [2], 'sClass': 'invisible'},
            { 'aTargets': [3], 'sClass': 'invisible'}
                    ],
        'order': [
            [2, 'asc']
        ]
    });

    let tablelistaitem = document.getElementById('listaItems_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;

}
function controlUnidadMedida(){
    var id_tipo_item = document.getElementsByName("id_tipo_item")[0].value;    
    var id_servicio = document.getElementsByName("id_servicio")[0].value;    
    var selectUnidadMedida = document.getElementsByName("unidad_medida_item");    
    // console.log(id_tipo_item);
    // console.log(id_servicio);
    if(id_tipo_item == 1){
        disabledControl(selectUnidadMedida,false);
    }
    if(id_tipo_item  == 2){
        disabledControl(selectUnidadMedida,true);

    }
    if(id_tipo_item == 3){
        disabledControl(selectUnidadMedida,true);
    }
}

function selectItem(){
    var id_item = $('.modal-footer #id_item').text();
    var id_producto = $('.modal-footer #id_producto').text();
    var id_servicio = $('.modal-footer #id_servicio').text();
    var id_equipo = $('.modal-footer #id_equipo').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    mostrar_item(id_item);
    var selectUnidadMedida = document.getElementsByName("unidad_medida_item");    
    // console.log(id_item);
    // console.log(id_producto);
    // console.log(id_servicio);
    // console.log(id_equipo);
    if(id_producto > 0){
        disabledControl(selectUnidadMedida,false);
        document.getElementsByName("id_tipo_item")[0].value = 1;
    }
    if(id_servicio > 0){
        disabledControl(selectUnidadMedida,true);
        document.getElementsByName("id_tipo_item")[0].value = 2;

    }
    if(id_equipo > 0){
        disabledControl(selectUnidadMedida,true);
        document.getElementsByName("id_tipo_item")[0].value = 3;
    }
    $('#modal-catalogo-items').modal('hide');
}


function mostrar_item(id){
    $(":file").filestyle('disabled', false);
    baseUrl = '/logistica/mostrar_item/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            let btnVerUltimasCompras = document.getElementsByName('btnVerUltimasCompras')[0];
            btnVerUltimasCompras.removeAttribute('disabled');
            btnVerUltimasCompras.setAttribute('class','btn btn-sm btn-default');

            $('[name=id_item]').val(response[0].id_item);
            $('[name=id_producto]').val(response[0].id_producto);
            $('[name=id_servicio]').val(response[0].id_servicio);
            $('[name=id_equipo]').val(response[0].id_equipo);
            $('[name=codigo_item]').val(response[0].codigo);
            $('[name=descripcion_item]').val(response[0].descripcion);
            $('[name=unidad_medida_item]').val(response[0].id_unidad_medida);

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


// modal partidas
function partidasModal(){  
    var grupo = $('[name=id_grupo]').val();
    // console.log(grupo);
    
    if (grupo !== ''){
        $('#modal-partidas').modal({
            show: true,
            backdrop: 'static'
        });
        listarPartidas(grupo);
    } else {
        alert('Es necesario que seleccione un Área!');
    }
}
function listarPartidas(id_grupo){
    let id_op_com= document.getElementsByName('id_op_com')[0].value;
    // console.log(id_op_com);
    
    if(id_op_com == 0 || id_op_com == '' || id_op_com == null){
        id_op_com = null;
    }
    $.ajax({
        type: 'GET',
        url: '/listar_partidas/'+id_grupo+'/'+id_op_com,
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

    itemSelected = {
        'id_item': document.getElementsByName('id_item')[0].value,
        'codigo_item': document.getElementsByName('codigo_item')[0].value,
        'descripcion':document.getElementsByName('descripcion_item')[0].value,
        'unidad':document.getElementsByName('unidad_medida_item')[0].value,
        'cantidad':document.getElementsByName('cantidad_item')[0].value,
        'precio_referencial':document.getElementsByName('precio_ref_item')[0].value,
        'id_partida':id_partida,
        'codigo_partida':codigoPartidaSelected
    }

 
    document.querySelectorAll('[id^="pres"]')[0].setAttribute('class','oculto' );

}

 





// function selectProyecto(){
//     var myId = $('.modal-footer #id_op_com').text();
//     // var page = $('.page-main').attr('type');
//     var form = $('.page-main form[type=register]').attr('id');
//         // mostrar_proyecto(myId); 
//         // console.log(myId); 
//     $('[name=id_op_com]').val(myId);

//     $('#modal-lista_opciones').modal('hide');
// }


// function mostrar_proyecto(id){
//     baseUrl = '/logistica/proyecto/'+id;
//     $.ajax({
//         type: 'GET',
//         url: baseUrl,
//         dataType: 'JSON',
//         success: function(response){            
//             $('[name=id_proyecto]').val(response['proyecto'][0].id_proyecto);
//             $('[name=codigo_proyecto]').val(response['proyecto'][0].codigo);
//             $('[name=descripcion_proyecto]').val(response['proyecto'][0].descripcion);
//             $('[name=cliente]').val(response['proyecto'][0].razon_social);
//             $('[name=presupuesto]').val(response['proyecto'][0].importe);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function save_requerimiento(action){
    let actual_id_usuario = userSession.id_usuario;
    let requerimiento = get_data_requerimiento();
    // console.log(requerimiento);
    
    let detalle_requerimiento = data_item;

    requerimiento.id_usuario = actual_id_usuario; //update -> usuario actual
    // requerimiento.id_area = actual_id_area; // update -> id area actual
    // requerimiento.id_rol = actual_id_rol; // update -> id rol actual
    // requerimiento.id_grupo = actual_id_grupo; // update -> id area actual
    let data = {requerimiento,detalle:detalle_requerimiento};
    // console.log(data);
    
    
    if (action == 'register'){
        // funcion guardar nuevo

        data.requerimiento.id_estado_doc =1  // estado elaborado 
        data.requerimiento.estado = 1  // estado 
        
        if(document.querySelector("select[name='tipo_requerimiento']").value == 3){ // Compra Stock Almacen
        let almacen_id_sede =document.querySelector("select[name='id_almacen']").options[document.querySelector("select[name='id_almacen']").selectedIndex].dataset.idSede;
        let almacen_id_empresa =document.querySelector("select[name='id_almacen']").options[document.querySelector("select[name='id_almacen']").selectedIndex].dataset.idEmpresa;
            // update id_sede, id_empresa
        data.requerimiento.id_empresa =almacen_id_empresa;
        data.requerimiento.id_sede = almacen_id_sede;
        
    
        }
        // console.log(data);
        
        baseUrl = rutaGuardarRequerimiento;
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response > 0){
                    let lastIdRequerimiento =  response;
                    mostrar_requerimiento(lastIdRequerimiento);
                    changeStateButton('guardar');
                    $('#form-requerimiento').attr('type', 'register');
                    changeStateInput('form-requerimiento', true);
                    alert("Requerimiento Guardado");
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
        
    }else if(action == 'edition'){
        // funcion editar
        baseUrl = rutaActualizarRequerimiento+'/'+data.requerimiento.id_requerimiento;
        $.ajax({
            type: 'PUT',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response > 0){
                    alert("Requerimiento Actualizado");
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });   
    }
}

function openSustento(id_obs ,id_req){ 
    $('[name=motivo_sustento]').val('');
    $('[name=id_requerimiento_sustento]').val(id_req);
    $('[name=id_observacion_sustento]').val(id_obs);
    $('#modal-sustento').modal({show: true, backdrop: 'static'});
}

function editRequerimiento(){
    // document.getElementsByName('concepto')[0].disabled=true;
    let id_estado_doc = document.getElementsByName('id_estado_doc')[0].value;
    // console.log(id_estado_doc);
    let cantidad_aprobaciones = $('[name=cantidad_aprobaciones]').val();

    if(id_estado_doc >1 && cantidad_aprobaciones >0){
        document.getElementsByName('codigo')[0].disabled=true;
        document.getElementsByName('descripcion_item')[0].disabled=true;
        // document.getElementById('basic-addon7').disabled=true;
        document.getElementsByName('unidad_medida_item')[0].disabled=true;
        document.getElementsByName('cantidad_item')[0].disabled=true;
        document.getElementsByName('precio_ref_item')[0].disabled=true;
    
    }
    // console.log("editando..")
    var btnEditarItem = document.getElementsByName("btnEditarItem");
    disabledControl(btnEditarItem,false);
    var btnAdjuntarArchivos = document.getElementsByName("btnAdjuntarArchivos");
    disabledControl(btnAdjuntarArchivos,false);
    var btnEliminarItem = document.getElementsByName("btnEliminarItem");
        disabledControl(btnEliminarItem,false);
    var btnEliminarAdjuntoRequerimiento = document.getElementsByName("btnEliminarAdjuntoRequerimiento");
        disabledControl(btnEliminarAdjuntoRequerimiento,false);
    return null;
}


function cancelarRequerimiento(){
    // console.log("cancelar");
    document.getElementById('btnCopiar').setAttribute("disabled",true);
    $('#estado_doc').text('');

    $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="7"> No hay datos registrados</td></tr>');
    $('[name=codigo]').val('');
    var btnEditarItem = document.getElementsByName("btnEditarItem");
        disabledControl(btnEditarItem,true);
    var btnAdjuntarArchivos = document.getElementsByName("btnAdjuntarArchivos");
        disabledControl(btnAdjuntarArchivos,false);
    var btnEliminarItem = document.getElementsByName("btnEliminarItem");
        disabledControl(btnEliminarItem,true);
}
function anular_requerimiento(id_req){
    if(id_req > 0){
        baseUrl = rutaAnularRequerimiento+'/'+id_req;
        $.ajax({
            type: 'PUT',
            url: baseUrl,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if(response.status ==1){
                    alert("Requerimiento Anulado.");
                    nuevo_req();
                }else if(response.status ==2){
                    alert("No se puede Anular, Unicamente el usuario que creo el requerimiento puede anular.");
                }else{
                    alert("No se pudo Anular el Requerimiento.");
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
   
}


function copiarDocumento(){
    var id = $('#id_requerimiento').text();
    var concepto = $('[name=concepto]').val();
    
    
    changeStateButton('historial');
    
    if(concepto.length !=0 || concepto != ''){
        if(id >0 ){
            $('#modal-copiar-documento').modal({
                show: true,
                backdrop: 'static'
            });
        }else{
            alert("No se seleccionó un requerimiento del historial");
        }

    }else{        
        alert("No escribió ningún concepto");
    }
}
function pasteDataOfModalToForm(){
    let concepto = document.getElementById('textConcepto').value;
    let fecha = document.getElementById('textFechaRequerimiento').value;
    let prioridad = document.getElementById('textPrioridad').value;
    let moneda = document.getElementById('textMoneda').value;
    let periodo = document.getElementById('textPeriodo').value;
    let empresa = document.getElementById('textEmpresa').value;
    let grupo = document.getElementById('textGrupo').value;
    let area = document.getElementById('textArea').value;
    let nombre_area = document.getElementById('textNombreArea').value;
    let rol = document.getElementById('textRolUsuario').value;
// console.log(rol);


    let mcd_concepto = document.querySelectorAll('input[name="concepto"]');
        mcd_concepto.forEach(function(item) {
            item.value=concepto;
});
    let mcd_fecha = document.querySelectorAll('input[name="fecha_requerimiento"]');
        mcd_fecha.forEach(function(item) {
            item.value=fecha;
});
    let mcd_prioridad = document.querySelectorAll('select[name="prioridad"]');
        mcd_prioridad.forEach(function(item) {
            item.value=prioridad;
});
    let mcd_moneda = document.querySelectorAll('input[name="moneda"]');
        mcd_moneda.forEach(function(item) {
            item.value=moneda;
});
    let mcd_periodo = document.querySelectorAll('select[name="periodo"]');
    mcd_periodo.forEach(function(item) {
        item.value=periodo;
});
    let mcd_empresa = document.querySelectorAll('select[name="empresa"]');
        mcd_empresa.forEach(function(item) {
            item.value=empresa;
});
    let mcd_grupo = document.querySelectorAll('input[name="id_grupo"]');
        mcd_grupo.forEach(function(item) {
            item.value=grupo;
});
    let mcd_area = document.querySelectorAll('input[name="id_area"]');
        mcd_area.forEach(function(item) {
            item.value=area;
});
    let mcd_nombre_area = document.querySelectorAll('input[name="nombre_area"]');
        mcd_nombre_area.forEach(function(item) {
            item.value=nombre_area;
});
    let mcd_rol_usuario = document.querySelectorAll('select[name="rol_usuario"]');
        mcd_rol_usuario.forEach(function(item) {
            item.value=rol;
});
}

function copiarDatosRequerimiento(){
    
    pasteDataOfModalToForm();

    var id = $('#id_requerimiento').text();

    baseUrl = rutaCopiarRequerimiento+'/'+id;
    let actual_id_usuario = userSession.id_usuario;
    let requerimiento = get_data_requerimiento();
    let detalle_requerimiento = data_item;

    requerimiento.id_usuario = actual_id_usuario; //update -> usuario actual
    // requerimiento.id_area = actual_id_area; // update -> id area actual
    // requerimiento.id_rol = actual_id_rol; // update -> id rol actual
    let data = {requerimiento,detalle:detalle_requerimiento};
    data.requerimiento.id_estado_doc =1  // estado elaborado 
    data.requerimiento.estado = 1  // estado 
    // console.log(data);

    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.status == 'OK'){
                alert("Copiado!, Se genero un nuevo requerimiento con código: "+ response.codigo_requerimiento);
                $('#modal-copiar-documento').modal('hide');
                mostrar_requerimiento(response.id_requerimiento);
            }else if(response.status=='NO_COPIADO'){
                alert("No se puede copiar el requerimiento.");
            }else{
                alert("ERROR");
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


function verUltimasCompras(event){
    let id_item = $('[name=id_item]').val();
    // console.log('id_item');
    // console.log(id_item);
    // console.log(id_item.length);
    
    if(id_item != null && id_item.length > 0){
    $('#modal-ultimas_compras').modal({
        show: true,
        backdrop: 'static'
    });
 
        listarUltimasCompras(id_item,0);
    }else{
        alert("Primero debe seleccione un ítem. ");
    }
    
}


function listarUltimasCompras(id_item,id_detalle_requerimiento) {
    var vardataTables = funcDatatables();
    $('#ultimasCompras').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'destroy' : true,

        'ajax': '/logistica/ultimas_compras/'+id_item+'/0',
        'columns': [
            {'data': 'id'},
            {'data': 'id_item'},
            {'data': 'descripcion'},
            {'data': 'precio_unitario'},
            {'data': 'proveedor'},
            {'data': 'documento'},
            {'data': 'fecha_registro'},
            
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'desc']
        ]
    });    
}

function cargarArchivo(){

    let data = new FormData();
    data.append('file', $('#file')[0].files[0]);

    $.ajax({
        url: 'cargar_archivo_correo',
        type: 'POST',

        // Form data
        // datos del formulario
        data:data,
        // necesario para subir archivos via ajax
        cache: false,
        contentType: false,
        processData: false,
        // mientras enviamos el archivo
        beforeSend() {
            $('.loading').removeClass('invisible');

        },
        // una vez finalizado correctamente
        success(data) {		
            // console.log(data);
        },
        // si ha ocurrido un error
        error(data) {
            $(`#${  divresul  }`).html(data)
        },
    })
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

function changeOptTipoReqSelect(e){
    
    if(e.target.value == 2){ //venta directa
        document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén';

        document.querySelector("form[id='form-requerimiento'] input[name='nombre_area']").value='';
        document.querySelector("form[id='form-requerimiento'] input[name='id_area']").value='';
        document.querySelector("form[id='form-requerimiento'] select[name='rol_usuario']").value='';
        hiddeElement('ocultar','form-requerimiento',[
            'input-group-area',
            'input-group-rol-usuario',
            'input-group-proyecto',
            'input-group-comercial'
        ]);
        hiddeElement('mostrar','form-requerimiento',[
            'input-group-sede',
            'input-group-tipo-cliente',
            'input-group-telefono-cliente',
            'input-group-empresa',
            'input-group-tipo-cliente',
            'input-group-cliente',
            'input-group-direccion-entrega',
            'input-group-monto'

        ]);

        listar_almacenes();

    }else if(e.target.value == 3){
        document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén que solicita';

        document.querySelector("form[id='form-requerimiento'] input[name='nombre_area']").value='';
        document.querySelector("form[id='form-requerimiento'] input[name='id_area']").value='';
        document.querySelector("form[id='form-requerimiento'] select[name='rol_usuario']").value='';
        hiddeElement('ocultar','form-requerimiento',[
            'input-group-moneda',
            'input-group-empresa',
            'input-group-area',
            'input-group-rol-usuario',
            'input-group-sede',
            'input-group-tipo-cliente',
            'input-group-telefono-cliente',
            'input-group-cliente',
            'input-group-direccion-entrega',
            'input-group-ubigeo-entrega',
            'input-group-proyecto',
            'input-group-comercial',
            'input-group-monto'
        ]);
        hiddeElement('mostrar','form-requerimiento',[
            'input-group-almacen'
        ]);

        listar_almacenes();

    }else if(e.target.value == 1){
        document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén que solicita';

        document.querySelector("div[id='input-group-fecha']").setAttribute('class','col-md-2');
        document.querySelector("form[id='form-requerimiento'] select[name='id_almacen']").value='';
        document.querySelector("form[id='form-requerimiento'] select[name='sede']").value='';
        document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']").value = '';      
        document.querySelector("form[id='form-requerimiento'] input[name='dni_persona']").value='';
        document.querySelector("form[id='form-requerimiento'] input[name='nombre_persona']").value='';
        document.querySelector("form[id='form-requerimiento'] input[name='direccion_entrega']").value='';
        document.querySelector("form[id='form-requerimiento'] input[name='ubigeo']").value='';
        document.querySelector("form[id='form-requerimiento'] input[name='name_ubigeo']").value='';

        hiddeElement('ocultar','form-requerimiento',[
            'input-group-proyecto',
            'input-group-comercial'
        ]);
        hiddeElement('mostrar','form-requerimiento',[
            'input-group-almacen',
            'input-group-area',
            'input-group-rol-usuario',
            'input-group-moneda',
            'input-group-empresa',
            'input-group-sede',
            'input-group-tipo-cliente',
            'input-group-telefono-cliente',
            'input-group-cliente',
            'input-group-direccion-entrega',
            'input-group-ubigeo-entrega',
            'input-group-monto'

        ]);

    }
}

function changeOptEmpresaSelect(e){
    let id_empresa = e.target.value;
    getDataSelectSede(id_empresa);
}


function getDataSelectSede(id_empresa = null){
    if(id_empresa >0){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: rutaSedeByEmpresa+'/' + id_empresa,
            dataType: 'JSON',
            success: function(response){
                llenarSelectSede(response);
                llenarUbigeo();
            }
        });
    }
    return false;
}

function llenarUbigeo(){
    var ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
    var name_ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
    document.querySelector("input[name='ubigeo']").value=ubigeo;
    document.querySelector("input[name='name_ubigeo']").value=name_ubigeo;
    
    var sede = $('[name=sede]').val();
    cargar_almacenes(sede);
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

// function changeOptTipoClienteSelect(e){
//     let tipo_cliente = parseInt(e.target.value);

//     if(tipo_cliente >0){
//         document.querySelector("button[name='btnCliente']").removeAttribute('disabled');
//     }else if(tipo_cliente ==0){
//         document.querySelector("button[name='btnCliente']").setAttribute('disabled',true);

//     }

//     if(tipo_cliente == 1){ //persona natural    
//         document.querySelector("button[name='btnCliente']").setAttribute('onClick','personaNaturalModal()');
//     }else if(tipo_cliente == 2){ // persona juridica
//         document.querySelector("button[name='btnCliente']").setAttribute('onClick','personaJuridicaModal()');
//     }
// }

// function personaNaturalModal(){
//     console.log('modal persona natural');
    
// }

// function personaJuridicaModal(){
//     console.log('modal persona juridica');
    
// }
function changeTipoCliente(e){
    if (e.target.value == 1){
        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').hide();
        $('[name=cliente_razon_social]').hide();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').show();
        $('[name=nombre_persona]').show();
    }
    else if (e.target.value == 2){
        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').show();
        $('[name=cliente_razon_social]').show();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').hide();
        $('[name=nombre_persona]').hide();
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

function openModalTelefonosCliente(){
    $('#modal-telefonos-cliente').modal({
        show: true
    });
}
function openModalDireccionesCliente(){
    $('#modal-direcciones-cliente').modal({
        show: true
    });
}

$(function(){
    $('#listaTelefonosCliente tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersonas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var tel = $(this)[0].firstChild.innerHTML;
        $('[name=telefono_cliente]').val(tel);    
        $('#modal-telefonos-cliente').modal('hide');
    });

    $('#listaDireccionesCliente tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersonas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var dir = $(this)[0].firstChild.innerHTML;
        $('[name=direccion_entrega]').val(dir);    
        $('#modal-direcciones-cliente').modal('hide');
    });
});

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

function verTrazabilidadRequerimiento(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verTrazabilidadRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            dibujarTablatrazabilidadRequerimiento(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function dibujarTablatrazabilidadRequerimiento(data){
    var vardataTables = funcDatatables();
    $('#listaTrazabilidadRequerimiento').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:10,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        data: data,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.accion;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.nombre_corto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.fecha_registro;
                }
            }
        ],
    })

    let tablelistaitem = document.getElementById(
        'listaTrazabilidadRequerimiento_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;

}