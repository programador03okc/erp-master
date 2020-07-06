var mySession =[];

$(function(){
    // $.ajax({
    //     type: 'GET',
    //     url: 'verSession',
    //     // data: data,
    //     success: function(response){
    //         // console.log(response.trabajador.roles); 
    //         let roles = response.trabajador.roles;
    //         let idRolConceptoListSession= [];
    //         roles.forEach(element => {
    //             idRolConceptoListSession.push(element.id_rol_concepto);
    //         });
            
    //         mySession={
    //             'roles':idRolConceptoListSession
    //         };
            
    //     }
    // });

    // $('[name=id_almacen]').val(1).trigger('change.select2');
    listarOrdenes();
    $("#form-registrar_pago").on("submit", function(e){
        e.preventDefault();
        // var data = $(this).serialize();
        guardar_pago_orden();
     });
    $("#form-aprobacion_orden").on("submit", function(e){
        e.preventDefault();
        // var data = $(this).serialize();
        guardar_aprobación();
    });
});
function listarOrdenes(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaOrdenes').DataTable({
        'processing':true,
        'destroy':true,
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_todas_ordenes',
        "dataSrc":'',
        'scrollX': true,
        'columnDefs': [{ className: "text-right", 'aTargets': [0], 'sClass': 'invisible'}],
        "drawCallback": function (settings) { 
            // Here the response
            var response = settings.json;
            // console.log(response);
            
            if(response == undefined || response.data.length ==0){
                    alert("No hay ordenes registradas para mostrar");
            }
        },
    });

    // ver("#listaOrdenes tbody", tabla);
    aprobar_orden("#listaOrdenes tbody", tabla);
    pagar("#listaOrdenes tbody", tabla);
    eliminar("#listaOrdenes tbody", tabla);
    imprimir_orden("#listaOrdenes tbody", tabla);
    tracking_orden("#listaOrdenes tbody", tabla);
    vista_extendida();
}

function tracking_orden(tbody, tabla){
    $(tbody).on("click","button.tracking_orden", function(){
        if (this.dataset.idOrdenCompra > 0){
            open_tracking_orden(this.dataset.idOrdenCompra);

        }

    });

}

function imprimir_orden(tbody,tabla){
    $(tbody).on("click","button.imprimir_orden", function(){
        if (this.dataset.idOrdenCompra > 0){
            window.open('generar_orden_pdf/'+this.dataset.idOrdenCompra);
        }

    });

}

function aprobar_orden(tbody, tabla){
    $(tbody).on("click","button.aprobar_orden", function(){
        // var data = tabla.row($(this).parents("tr")).data();
        // console.log(data.id_rol_concepto_aprob);
        if (this.dataset.idOrdenCompra > 0){
            // console.log(this.dataset.idOrdenCompra);
            
            // if(mySession.roles.includes(data.id_rol_concepto_aprob)){
                open_aprobar_orden(this.dataset.idOrdenCompra);
            // }else{
                // alert("No tiene autorizado realiar esta acción");
            // }
        }
    });
}

function pagar(tbody, tabla){
    // console.log("pagar");
    $(tbody).on("click","button.pagar", function(){
        // var data = tabla.row($(this).parents("tr")).data();
        // if (data !== undefined){
            open_registrar_pago(this);
        // }
    });
}
function eliminar(tbody, tabla){
    // console.log("eliminar");
    $(tbody).on("click","button.eliminar", function(){
        // var data = tabla.row($(this).parents("tr")).data();
        // console.log(data);
        // if (data !== undefined && data.id_pago !== null){
            eliminar_pago(this);
        // } else {
        //     alert('No existe un pago.');
        // }
    });
}
function abrir_orden(id_orden_compra){
    // console.log(id_orden_compra);
    localStorage.setItem("idOrden",id_orden_compra);
    location.assign("../../generar_orden");
}
function abrir_cuadro(id_grupo_cotizacion){
    localStorage.setItem("idGrupo",id_grupo_cotizacion);
    localStorage.setItem("TipoCodigo",3);
    location.assign("../logistica/cotizacion/cuadro-comparativo");
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}

function cancelarModalAprobarOrden(){
    $('#modal-aprobar_orden').modal('hide');
}

//aprobar orden
function open_aprobar_orden(id_orden){
    // console.log(data);

    if(id_orden > 0){
 
            $('#modal-aprobar_orden').modal({
                show: true
            });
                //  $('#codigo_orden').text(id_orden);
                $('[name=id_orden_compra]').val(id_orden);
                // $('[name=codigo_orden]').val(data.codigo);
 
    }else{
        alert('no existe ID');
    }
}

function open_tracking_orden(id_orden){
 
    if(id_orden > 0){
 
            $('#modal-tracking_orden').modal({
                show: true
            });
            $('[name=id_orden_compra]').val(id_orden);

            get_data_orden_tracking(id_orden);

  
    }else{
        alert('no existe ID');
    }
}

function get_data_orden_tracking(id_orden){
    baseUrl = 'explorar-orden/'+id_orden;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            // llenar_tabla_historial_aprobaciones(response.historial_aprobacion);     
            // llenar_tabla_flujo_aprobacion(response.flujo_aprobacion);  
            llenar_header(response.header);   
            llenar_registro_pago(response.registro_pago);   
            llenar_entrada_almacen(response.entrada_almacen);   
            llenar_despacho(response.despacho);   
 
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function llenar_header(data){
    document.querySelector('form[id="form-tracking_orden"] strong').textContent= data.orden.codigo;
}

function llenar_registro_pago(data){
    let cantidadRegistroPago  = data.length;
    document.querySelector('form[id="form-tracking_orden"] span[id="cantidad_cotizaciones"]').textContent= cantidadRegistroPago;
    llenar_tabla_registro_pago(data);
}

function llenar_tabla_registro_pago(data){
    var vardataTables = funcDatatables();
    $('#listaRegistroPago').dataTable({
        "order": [[ 1, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_pago'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'data': 'detalle_pago'},
            {'data': 'archivo_adjunto'},
            {'data': 'nombre_responsable'},
            {'data': 'fecha_registro'},
            {'render':
            function (data, type, row){
                let estado = '';
                if(row.estado ==1){
                    estado ='Elaborado';
                    
                }else if(row.estado ==7){
                    estado ='Anulado';
                }
 
                return (estado);
                }
            }
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaRegistroPago_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function llenar_entrada_almacen(data){
    let cantidadITemEntrante  = data.length;

    document.querySelector('form[id="form-tracking_orden"] span[id="cantidad_item_entrantes"]').textContent= cantidadITemEntrante;
    llenar_tabla_entrada_almacen(data);
}

function llenar_tabla_entrada_almacen(data){
    var vardataTables = funcDatatables();
    $('#listaEntradaAlmacen').dataTable({
        "order": [[ 1, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_guia'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'data': 'descripcion_tp_doc_almacen'},
            {'data': 'serie_numero'},
            {'data': 'descripcion_producto'},
            {'data': 'abreviatura'},
            {'data': 'cantidad'},
            {'data': 'unitario'},
            {'data': 'total'},
            {'data': 'descripcion_almacen'},
            {'data': 'codigo'},
            {'data': 'fecha_emision'},
            {'data': 'fecha_almacen'}
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaEntradaAlmacen_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function llenar_despacho(data){
    console.log(data);
    
    let cantidadDespacho  = data.length;

    document.querySelector('form[id="form-tracking_orden"] span[id="cantidad_despachados"]').textContent= cantidadDespacho;
    llenar_tabla_despacho(data);
}

function llenar_tabla_despacho(data){
    var vardataTables = funcDatatables();
    $('#listaDespachos').dataTable({
        "order": [[ 1, "desc" ]],
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'data': data,
        'columns': [
            {'data': 'id_guia_ven'},
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'data': 'descripcion_tp_doc_almacen'},
            {'data': 'serie_numero'},
            {'data': 'descripcion_producto'},
            {'data': 'abreviatura'},
            {'data': 'cantidad'},
            {'data': 'descripcion_almacen'},
            {'data': 'codigo'},
            {'data': 'fecha_emision'},
            {'data': 'fecha_almacen'}
         ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    let tablelistaitem = document.getElementById('listaDespachos_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}
 

function llenar_tabla_historial_aprobaciones(data){        
    limpiarTabla('listaHistorialAprobacion');
    htmls ='<tr></tr>';
    $('#listaHistorialAprobacion tbody').html(htmls);
    var table = document.getElementById("listaHistorialAprobacion");
    if(data.length > 0){
        for(var a=0;a < data.length;a++){
            var row = table.insertRow(a+1);
            row.insertCell(0).innerHTML = data[a].estado?data[a].estado.toUpperCase():'-';
            row.insertCell(1).innerHTML = data[a].nombre_usuario?data[a].nombre_usuario:'-';
            row.insertCell(2).innerHTML = data[a].obs?data[a].obs:'-';
            row.insertCell(3).innerHTML = data[a].fecha?data[a].fecha:'-';
        }
    }
}

function llenar_tabla_flujo_aprobacion(data){
    // console.log(data);
    limpiarTabla('listaFlujoAprobacion');
    htmls ='<tr></tr>';
    $('#listaFlujoAprobacion tbody').html(htmls);
    var table = document.getElementById("listaFlujoAprobacion");
    if(data.length > 0){
        for(var a=0;a < data.length;a++){
            var row = table.insertRow(a+1);
            row.insertCell(0).innerHTML = data[a].orden?data[a].orden:'-';
            row.insertCell(1).innerHTML = data[a].nombre_fase?data[a].nombre_fase:'-';
            row.insertCell(2).innerHTML = data[a].nombre_responsable?data[a].nombre_responsable:'-';
        }
    }
}

function guardar_aprobación(){
     
    // var formData = new FormData($('#form-aprobacion_orden')[0]);
    // console.log(formData);
    let id_orden = $('[name=id_orden_compra]').val();
     let payload= {
        'id_orden':id_orden
         };
 

    if ( Number.isInteger(id_orden) ==false && id_orden <=0){
        alert('No existe un ID orden valido');
    } else {
        $.ajax({
            type: 'PUT',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_aprobacion_orden',
            data: payload,
            // cache: false,
            // contentType: false,
            // processData: false,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response.status == 'success'){
                    $('#modal-aprobar_orden').modal('hide');
                    alert('Operación realizada con éxito!');
                    $('[name=id_pago]').val('');
                    $('[name=id_orden_compra]').val('');
                    $('[name=detalle_pago]').val('');
                    $('[name=archivo_adjunto]').val('');
                    listarOrdenes();
                }else{
                    alert('Houston tenemos un problema!, no se puedo aprobar la orden');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
//Registrar Pago
function open_registrar_pago(e){
    // console.log(e.dataset.idOrdenCompra);
    // console.log(e.dataset.idPago);
    let id_pago = e.dataset.idPago;
    if (id_pago > 0 ){
        alert('El pago ya fue registrado.');
    } else {
        $('#modal-registrar_pago').modal({
            show: true
        });
        if (e.dataset.idOrdenCompra > 0){
            // $('#codigo_orden').text(e.dataset.idOrdenCompra);
            $('[name=id_orden_compra]').val(e.dataset.idOrdenCompra);
            // $('[name=codigo_orden]').val(data.codigo);
            $('[name=id_pago]').val(e.dataset.idPago);
        }else{
            alert("error no existe id_orden");
        }
    }
   
}
function guardar_pago_orden(){
    var formData = new FormData($('#form-registrar_pago')[0]);
    // console.log(formData);
    var id_pago = $('[name=id_pago]').val();
    // console.log(data);
    // console.log(id_pago);
    if (id_pago !== ''){
        alert('El pago ya fue registrado.');
    } else {
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_pago_orden',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response > 0){
                    $('#modal-registrar_pago').modal('hide');
                    alert('Pago registrado con éxito');
                    $('[name=id_pago]').val('');
                    $('[name=id_orden_compra]').val('');
                    $('[name=detalle_pago]').val('');
                    $('[name=archivo_adjunto]').val('');
                    listarOrdenes();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function eliminar_pago(e){
    let id_pago= e.dataset.idPago;
    if(id_pago >0){
        $.ajax({
            type: 'GET',
            url: 'eliminar_pago/'+id_pago,
            dataType: 'JSON',
            success: function(response){
                if (response > 0){
                    alert('Pago quitado con éxito');
                    listarOrdenes();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }else{
        alert("no existe id_pago");
    }

}

function viewGroupInfo(e){
    data= JSON.parse(e.target.dataset.groupInfo);
    // console.log(data);
    $('#modal-info-grupo').modal({
        show: true
    });

    document.getElementById('info-numero_orden').innerHTML= data[0].codigo_orden;
    document.getElementById('info-numero_requerimiento').innerHTML= data.map((value)=>{return value.codigo_requerimiento});
    document.getElementById('info-nombre_grupo').innerHTML= data.map((value)=>{return value.nombre_grupo});
    document.getElementById('info-nombre_area').innerHTML= data.map((value)=>{ return value.nombre_area});
}