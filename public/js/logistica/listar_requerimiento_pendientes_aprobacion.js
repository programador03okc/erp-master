var rutaListaPendienteAprobacion, rutaListaAprobarDocumento;


function inicializarRutasPendienteAprobacion(_rutaListaPendienteAprobacion,_rutaListaAprobarDocumento ) {
    
    rutaListaPendienteAprobacion = _rutaListaPendienteAprobacion;
    rutaListaAprobarDocumento = _rutaListaAprobarDocumento;
}


function listar_requerimientos_pendientes_aprobar(){
    $('#ListaReqPendienteAprobacion').DataTable({
        'processing': true,
        'serverSide': true,
        'bDestroy': true,
        // bInfo:     false,
        'paging':   true,
        'searching': true,
        'bLengthChange': false,

        'iDisplayLength':50,
        'ajax': {
            url:rutaListaPendienteAprobacion,
            type:'GET',
            data: {_token: "{{csrf_token()}}"}
        },
        'columns':[
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render': function (data, type, row){
                let prioridad ='';
                let thermometerNormal = '<center><i class="fas fa-thermometer-empty green fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad Normal" ></i></center>';
                let thermometerAlta = '<center> <i class="fas fa-thermometer-half orange fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad Alta"  ></i></center>';
                let thermometerCritica = '<center> <i class="fas fa-thermometer-full red fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad Crítico"  ></i></center>';
                    if(row.id_prioridad==1){
                        prioridad = thermometerNormal
                    }else if(row.id_prioridad ==2){
                        prioridad = thermometerAlta
                    }else if(row.id_prioridad ==3){
                        prioridad = thermometerCritica
                    }
                return prioridad; 
                }
            },  
            {'data':'codigo', 'name':'codigo'},
            {'data':'concepto', 'name':'concepto'},
            {'data':'fecha_requerimiento', 'name':'fecha_requerimiento'},
            {'data':'tipo_requerimiento', 'name':'tipo_requerimiento'},          
            {'data':'descripcion_tipo_cliente', 'name':'descripcion_tipo_cliente'},          
            {'data':'razon_social_empresa', 'name':'razon_social_empresa'},
            {'render': function (data, type, row){
                return row['descripcion_op_com']?row['descripcion_op_com']:row['descripcion_grupo']; 
                }
            },  
            {'data':'usuario', 'name':'usuario'},
            {'data':'estado_doc', 'name':'estado_doc'},
            {'data':'cantidad_aprobados_total_flujo', 'name':'cantidad_aprobados_total_flujo'},
            {'render': function (data, type, row){
                var list_id_rol_aprob =[];
                var hasAprobacion =0;
                if(row.aprobaciones.length>0){
                    row.aprobaciones.forEach(element => {
                        list_id_rol_aprob.push(element.id_rol)
                    });

                    roles.forEach(element => {
                        if(list_id_rol_aprob.includes(element.id_rol)==true){
                            hasAprobacion+=1;
                        }
                        
                    });
                }
                if(hasAprobacion==0){
                    disableBtnA= '';
                } else{
                    disableBtnA= 'disabled';
                }
                let containerOpenBrackets='<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                let containerCloseBrackets='</div></center>';
                let btnDetalleRapido='<button type="button" class="btn btn-xs btn-info" title="Ver detalle rápido" onClick="viewFlujo(' +row['id_requerimiento']+ ', ' +row['id_doc_aprob']+ ');"><i class="fas fa-eye fa-xs"></i></button>';
                let btnTracking='<button type="button" class="btn btn-xs bg-primary" title="Explorar Requerimiento" onClick="tracking_requerimiento(' +row['id_requerimiento']+ ');"><i class="fas fa-globe fa-xs"></i></button>';
                let btnAprobar='<button type="button" class="btn btn-xs btn-success" title="Aprobar Requerimiento" onClick="aprobarRequerimiento(' +row['id_doc_aprob']+ ');" '+disableBtnA+'><i class="fas fa-check fa-xs"></i></button>';
                let btnObservar='<button type="button" class="btn btn-xs btn-warning" title="Observar Requerimiento" onClick="observarRequerimiento(' +row['id_doc_aprob']+ ', ' +row['id_doc_aprob']+ ');" '+disableBtnA+'><i class="fas fa-exclamation-triangle fa-xs"></i></button>';
                let btnAnular='<button type="button" class="btn btn-xs bg-maroon" title="Anular Requerimiento" onClick="anularRequerimiento(' +row['id_doc_aprob']+ ');" '+disableBtnA+'><i class="fas fa-ban fa-xs"></i></button>';
                return containerOpenBrackets+btnDetalleRapido+btnTracking+btnAprobar+btnObservar+btnAnular+containerCloseBrackets;
                }
            },        ]
    });
    let tablelistaitem = document.getElementById(
        'ListaReqPendienteAprobacion_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}


function openModalAprob(id_doc_aprob){
    $('#modal-aprobacion-docs').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    document.querySelector("form[id='form-aprobacion'] input[name='id_doc_aprob']").value =id_doc_aprob;
}

function GrabarAprobacion(){
    let id_doc_aprob = document.querySelector("form[id='form-aprobacion'] input[name='id_doc_aprob']").value;
    let id_rol_usuario = document.querySelector("form[id='form-aprobacion'] select[name='rol_usuario']").value;
    let detalle_observacion = document.querySelector("form[id='form-aprobacion'] textarea[name='detalle_observacion']").value;

    $.ajax({
        type: 'POST',
        url: rutaListaAprobarDocumento,
        data:{'id_doc_aprob':id_doc_aprob,'detalle_observacion':detalle_observacion,'id_rol':id_rol_usuario},
        dataType: 'JSON',
        success: function(response){
            if(response.status ==200){
                $('#modal-aprobacion-docs').modal('hide');
                listar_requerimientos_pendientes_aprobar();
                alert("Requerimiento Aprobado");
            }else{
                alert("Hubo un problema, no se puedo aprobar el requerimiento");
                console.log(response);
            }

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}


function aprobarRequerimiento(id_doc_aprob){
    openModalAprob(id_doc_aprob);
}

function observarRequerimiento(id_doc_aprob){
    console.log(id_doc_aprob);

}
function anularRequerimiento(id_doc_aprob){
    console.log(id_doc_aprob);

}