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
            console.log(response);
            let idGrupoList=[];
            auth_user.grupos.forEach(element => {
                idGrupoList.push(element.id_grupo);
            });

            data = response;
            
            let permisoBtn = false; 
            if(response.requerimiento[0].id_usuario == auth_user.id_usuario && response.requerimiento[0].estado == 3){ // usuario creador de rquerimiento y que estado de req = observado
                permisoBtn = true;
            }
            auth_user.roles.forEach((r)=>{ // solo rol programador
                if(r.id_rol ==6){ permisoBtn = true; }
            });

            // if(auth_user.id_usuario == 64 || idGrupoList.includes(response['requerimiento'][0].id_grupo)){ 
            if((auth_user.id_usuario == 64 || auth_user.id_usuario == response['requerimiento'][0].id_usuario) && ([1,3].includes(response['requerimiento'][0].estado))){ // id usuario = Ricardo
                permisoBtn = true;
            }
                if(permisoBtn) {// si el req tiene observaciones y el usuario no es el propietario
                    document.querySelector("button[id='btnEditar']").removeAttribute('disabled');
                    document.querySelector("button[id='btnAnular']").removeAttribute('disabled');
                    document.querySelectorAll("button[id='btnGuardar']")[1].removeAttribute('disabled');
                }else{
                    document.querySelector("button[id='btnEditar']").setAttribute('disabled',true);
                    document.querySelector("button[id='btnAnular']").setAttribute('disabled',true);
                    document.querySelectorAll("button[id='btnGuardar']")[1].setAttribute('disabled',true);
                }
            if(response['requerimiento'] !== undefined){
                if(response['requerimiento'][0].id_tipo_requerimiento == 1){ 
                    mostrarTipoForm('MGCP');

                    // if(response['requerimiento'][0].tipo_cliente == 1 || response['requerimiento'][0].tipo_cliente == 2){ //persona natural o persona juridica

                    // }
                    if(response['requerimiento'][0].tipo_cliente == 3  ){ 
                        mostrarTipoForm('BIENES_SERVICIOS');

                    }
                }else if(response['requerimiento'][0].id_tipo_requerimiento ==2){ 
                    mostrarTipoForm('CMS');

                }else if(response['requerimiento'][0].id_tipo_requerimiento ==3){ 
                    grupos.forEach(element => {
                        if(element.id_grupo ==3){ // proyectos
                            mostrarTipoForm('BIENES_SERVICIOS_PROYECTOS');

                        }else{
                            mostrarTipoForm('BIENES_SERVICIOS');
            
                        }
                    });

                }

                $('[name=id_usuario_req]').val(response['requerimiento'][0].id_usuario);
                $('[name=rol_usuario]').val(response['requerimiento'][0].id_rol);
                $('[name=id_estado_doc]').val(response['requerimiento'][0].id_estado_doc);
                $('[name=id_requerimiento]').val(response['requerimiento'][0].id_requerimiento);
                $('[name=tipo_requerimiento]').val(response['requerimiento'][0].id_tipo_requerimiento);
                $('[name=codigo]').text(response['requerimiento'][0].codigo);
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

                $('[name=fuente_id]').val(response['requerimiento'][0].fuente_id);
                $('[name=fuente_det_id]').val(response['requerimiento'][0].fuente_det_id);
                
                if(response['requerimiento'][0].fuente_det_id>0){ // mostrar fuente_det
                    document.querySelector("form[id='form-requerimiento'] div[id='input-group-fuente_det']").removeAttribute('hidden');
                    selectFuente(null,response['requerimiento'][0].fuente_id);
                }

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
                $('[name=estado]').val(response['requerimiento'][0].estado);
                $("[name=para_stock_almacen]").prop("checked", response['requerimiento'][0].para_stock_almacen);
                $('[name=rol_aprobante_id]').val(response['requerimiento'][0].rol_aprobante_id);
                $('[name=id_trabajador]').val(response['requerimiento'][0].trabajador_id);
                $('[name=nombre_trabajador]').val(response['requerimiento'][0].nombre_trabajador);

                let simboloMoneda='';
                if(response['requerimiento'][0].id_moneda==1){
                    simboloMoneda= 'S/.';
                }else if(response['requerimiento'][0].id_moneda ==2 ){
                    simboloMoneda= '$';

                }
                document.querySelector("form[id='form-requerimiento'] table label[name='total']").textContent= simboloMoneda+Math.round(response['requerimiento'][0].monto).toFixed(2);

                /* detalle */
                var detalle_requerimiento = response['det_req'];
                if(detalle_requerimiento.length === 0){
                    alert("El Requerimiento No Tiene Item");
                }
                // console.log(detalle_requerimiento);                
                for (x=0; x<detalle_requerimiento.length; x++){
                    let adjunto=[];
                        items ={
                        'id_item':detalle_requerimiento[x].id_item?detalle_requerimiento[x].id_item:null,
                        'id_tipo_item':detalle_requerimiento[x].id_tipo_item,
                        'id_producto':detalle_requerimiento[x].id_producto?detalle_requerimiento[x].id_producto:null,
                        'id_servicio':detalle_requerimiento[x].id_servicio?detalle_requerimiento[x].id_servicio:null,
                        'id_equipo':detalle_requerimiento[x].id_equipo,
                        'id_requerimiento':response['requerimiento'][0].id_requerimiento,
                        'id_detalle_requerimiento':detalle_requerimiento[x].id_detalle_requerimiento,
                        'part_number':detalle_requerimiento[x].part_number,
                        'cod_item':detalle_requerimiento[x].codigo_item,
                        'codigo_producto':detalle_requerimiento[x].codigo_producto,
                        'categoria':detalle_requerimiento[x].categoria,
                        'subcategoria':detalle_requerimiento[x].subcategoria,
                        'id_almacen_reserva':detalle_requerimiento[x].id_almacen_reserva,
                        'almacen_reserva':detalle_requerimiento[x].almacen_reserva,
                        'des_item':detalle_requerimiento[x].descripcion?detalle_requerimiento[x].descripcion:detalle_requerimiento[x].descripcion_adicional, 
                        'id_unidad_medida':detalle_requerimiento[x].id_unidad_medida,
                        'unidad':detalle_requerimiento[x].unidad_medida,
                        'cantidad':detalle_requerimiento[x].cantidad,
                        'stock_comprometido':detalle_requerimiento[x].stock_comprometido,
                        'precio_unitario':detalle_requerimiento[x].precio_unitario,
                        'subtotal':detalle_requerimiento[x].subtotal,
                        'id_tipo_moneda':detalle_requerimiento[x].id_tipo_moneda,
                        'tipo_moneda':detalle_requerimiento[x].tipo_moneda,
                        'fecha_entrega':detalle_requerimiento[x].fecha_entrega,
                        'lugar_entrega':detalle_requerimiento[x].lugar_entrega?detalle_requerimiento[x].lugar_entrega:"",
                        'id_partida':detalle_requerimiento[x].id_partida,
                        'cod_partida':detalle_requerimiento[x].codigo_partida,
                        'des_partida':detalle_requerimiento[x].descripcion_partida,
                        'id_centro_costo':detalle_requerimiento[x].id_centro_costo,
                        'codigo_centro_costo':detalle_requerimiento[x].codigo_centro_costo,
                        'id_partida':detalle_requerimiento[x].id_partida,
                        'obs':detalle_requerimiento[x].obs,
                        'tiene_transformacion':detalle_requerimiento[x].tiene_transformacion,
                        'proveedor_id':detalle_requerimiento[x].proveedor_id,
                        'motivo':detalle_requerimiento[x].motivo,
                        'proveedor_razon_social':detalle_requerimiento[x].proveedor_razon_social,
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
                    // llenarTablaAdjuntosRequerimiento(response['requerimiento'][0].id_requerimiento);
                    
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
                        '<blockquote class="blockquoteObservation box-shadow">'+
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



