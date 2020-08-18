var rutaListaPendienteAprobacion;


function inicializarRutasPendienteAprobacion(_rutaListaPendienteAprobacion ) {
    
    rutaListaPendienteAprobacion = _rutaListaPendienteAprobacion;
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
            {'render': function (data, type, row){
                let containerOpenBrackets='<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                let containerCloseBrackets='</div></center>';
                let btnAprobar='<button type="button" class="btn btn-sm btn-success" title="Aprobar Requerimiento" onClick="aprobarRequerimiento(' +row['id_requerimiento']+ ');"><i class="fas fa-check fa-xs"></i></button>';
                let btnObservar='<button type="button" class="btn btn-sm btn-warning" title="Observar Requerimiento" onClick="observarRequerimiento(' +row['id_requerimiento']+ ', ' +row['id_doc_aprob']+ ');"><i class="fas fa-exclamation-triangle fa-xs"></i></button>';
                let btnAnular='<button type="button" class="btn btn-sm btn-danger" title="Anular Requerimiento" onClick="anularRequerimiento(' +row['id_requerimiento']+ ');"><i class="fas fa-ban fa-xs"></i></button>';
                return containerOpenBrackets+btnAprobar+btnObservar+btnAnular+containerCloseBrackets;
                }
            },        ]
    });
    let tablelistaitem = document.getElementById(
        'ListaReqPendienteAprobacion_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function aprobarRequerimiento(id_requerimiento){
console.log(id_requerimiento);
}

function observarRequerimiento(id_requerimiento){
    console.log(id_requerimiento);

}
function anularRequerimiento(id_requerimiento){
    console.log(id_requerimiento);

}