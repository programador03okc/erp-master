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
                if(response['requerimiento'][0].id_tipo_requerimiento == 1){ // compra
                    if(response['requerimiento'][0].tipo_cliente == 1 || response['requerimiento'][0].tipo_cliente == 2){ //persona natural o persona juridica
                        // stateFormRequerimiento(1);
                        grupos.forEach(element => {
                            if(element.id_grupo ==3){ // proyectos
                                stateFormRequerimiento(4)
                             }else{
                                stateFormRequerimiento(5) // otro
                
                            }
                        });
                    }
                    if(response['requerimiento'][0].tipo_cliente == 3  ){ // uso almacen
                        stateFormRequerimiento(2);
                    }
                }else if(response['requerimiento'][0].id_tipo_requerimiento ==2){ //venta directa
                    stateFormRequerimiento(3);
                }else if(response['requerimiento'][0].id_tipo_requerimiento ==3){ // pedido almacén
                    stateFormRequerimiento(2);
                }

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
                // $('[name=id_area]').val(response['requerimiento'][0].id_area);
                $('[name=id_grupo]').val(response['requerimiento'][0].id_grupo);
                // $('[name=nombre_area]').val(response['requerimiento'][0].area_descripcion);
                $('[name=moneda]').val(response['requerimiento'][0].id_moneda);
                $('[name=periodo]').val(response['requerimiento'][0].id_periodo);
                 $('[name=id_proyecto]').val(response['requerimiento'][0].id_proyecto);
                $('[name=codigo_proyecto]').val(response['requerimiento'][0].codigo_proyecto);
                // $('[name=nombre_proyecto]').val(response['requerimiento'][0].descripcion_op_com);
                $('[name=observacion]').val(response['requerimiento'][0].observacion);
                
                $('[name=sede]').val(response['requerimiento'][0].id_sede);
                $('[name=tipo_cliente]').val(response['requerimiento'][0].tipo_cliente);


                $('[name=ubigeo]').val(response['requerimiento'][0].id_ubigeo_entrega);
                $('[name=name_ubigeo]').val(response['requerimiento'][0].name_ubigeo);
                $('[name=id_almacen]').val(response['requerimiento'][0].id_almacen);
                $('[name=monto]').val(response['requerimiento'][0].monto);
                $('[name=fecha_entrega]').val(response['requerimiento'][0].fecha_entrega);
                

                $('#estado_doc').text(response['requerimiento'][0].estado_doc);
                $('#estado_doc').removeClass();
                $('#estado_doc').addClass("label label-"+response['requerimiento'][0].bootstrap_color);
                
                if(response['requerimiento'][0].area_descripcion == 'PROYECTOS' || response['requerimiento'][0].area_descripcion == 'DPTO. FORMULACIÓN' || response['requerimiento'][0].area_descripcion == 'DPTO. EJECUCIÓN'){
                    // document.getElementById('section-proyectos').setAttribute('class', 'col');
                    document.querySelector("form[id='form-requerimiento'] div[id='input-group-proyecto']").removeAttribute('hidden');

                }
                $('[name=cantidad_aprobaciones]').val(response['aprobaciones']);

                changeTipoCliente(event,response['requerimiento'][0].tipo_cliente); //cambiar input para tipo cliente
                $('[name=id_persona]').val(response['requerimiento'][0].id_persona);
                $('[name=dni_persona]').val(response['requerimiento'][0].dni_persona);
                $('[name=nombre_persona]').val(response['requerimiento'][0].nombre_persona);
                $('[name=id_cliente]').val(response['requerimiento'][0].id_cliente);
                $('[name=cliente_ruc]').val(response['requerimiento'][0].cliente_ruc);
                $('[name=cliente_razon_social]').val(response['requerimiento'][0].cliente_razon_social);
                $('[name=direccion_entrega]').val(response['requerimiento'][0].direccion_entrega);
                $('[name=telefono_cliente]').val(response['requerimiento'][0].telefono);
                $('[name=email_cliente]').val(response['requerimiento'][0].email);
                $('[name=id_cuenta]').val(response['requerimiento'][0].id_cuenta);
                $('[name=tipo_cuenta]').val(response['requerimiento'][0].id_tipo_cuenta);
                $('[name=banco]').val(response['requerimiento'][0].id_banco);
                $('[name=nro_cuenta]').val(response['requerimiento'][0].nro_cuenta);
                $('[name=cci]').val(response['requerimiento'][0].nro_cuenta_interbancaria);
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
                        'part_number':detalle_requerimiento[x].part_number,
                        'cod_item':detalle_requerimiento[x].codigo_item,
                        'categoria':detalle_requerimiento[x].categoria,
                        'subcategoria':detalle_requerimiento[x].subcategoria,
                        'id_almacen_reserva':detalle_requerimiento[x].id_almacen_reserva,
                        'almacen_descripcion':detalle_requerimiento[x].almacen_reserva,
                        'des_item':detalle_requerimiento[x].descripcion?detalle_requerimiento[x].descripcion:detalle_requerimiento[x].descripcion_adicional, 
                        'id_unidad_medida':detalle_requerimiento[x].id_unidad_medida,
                        'unidad':detalle_requerimiento[x].unidad_medida,
                        'cantidad':detalle_requerimiento[x].cantidad,
                        'precio_referencial':detalle_requerimiento[x].precio_referencial,
                        'id_tipo_moneda':detalle_requerimiento[x].id_tipo_moneda,
                        'tipo_moneda':detalle_requerimiento[x].tipo_moneda,
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
                    

                    var btnMigrarRequerimiento = document.getElementsByName("btn-migrar-requerimiento");
                    
                    if (response['requerimiento'][0].occ_softlink == '' ||
                        response['requerimiento'][0].occ_softlink == null){
                        disabledControl(btnMigrarRequerimiento,false);
                    } else {
                        disabledControl(btnMigrarRequerimiento,true);
                    }
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
                obsReq.innerHTML = '<fieldset class="group-table"> <h5><strong>Observaciones por resolver:</strong></h5></br>'+htmlObservacionReq+'</fieldset>';

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


function openSustento(id_obs ,id_req){ 
    $('[name=motivo_sustento]').val('');
    $('[name=id_requerimiento_sustento]').val(id_req);
    $('[name=id_observacion_sustento]').val(id_obs);
    $('#modal-sustento').modal({show: true, backdrop: 'true'});
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