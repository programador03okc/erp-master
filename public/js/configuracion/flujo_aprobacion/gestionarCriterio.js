var lastActionBtnAsignarCriterio='';
var metodoAsignarCriterio ='';

function gestionarCriterio(id_flujo){
    $('#modal-gestionar_criterio').modal({
        show: true
    });
    
    listarTablaCriterioMonto();
    get_operador();
    listarTablaCriterioPrioridad();

    // criteriosAsignados();

    get_grupo_criterio();

    get_criterio_monto();
    get_criterio_prioridad();

    // console.log(id_flujo);

    get_grupo_detalle_criterio(id_flujo);
    document.getElementsByName('id_flujo')[0].value=id_flujo;
    
}

function get_grupo_criterio(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-grupo_criterio/null',
        success: function(response) {               
            fill_select_grupo_criterio(response.data);
            
        },
    })
}


function get_grupo_detalle_criterio(id_flujo){
    $.ajax({
        type: 'GET',
        url: '/mostrar-grupo-criterio-by-id_flujo/'+id_flujo,
        success: function(response) {  
            fill_select_grupo_criterio(response)

            document.getElementsByName('grupo_criterio')[0].removeAttribute('disabled');
            document.getElementsByName('estado_grupo_criterio')[0].removeAttribute('disabled');
        },
    })
}

function fill_select_grupo_criterio(data){
    let selectGrupoFlujo=document.getElementsByName('grupo_criterio')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_grupo_criterios+' data-estado="'+element.estado+'" >'+element.descripcion+'</option>';
    });
    selectGrupoFlujo.innerHTML = html;
}

function get_criterio_monto(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-criterio-monto/null',
        success: function(response) {               
            fill_select_criterio_monto(response);
            
        },
    })
}
function get_criterio_prioridad(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-criterio-prioridad/null',
        success: function(response) {               
            fill_select_criterio_prioridad(response);
            
        },
    })
}

function fill_select_criterio_monto(res){
    let data = res.data;

    let selectCriterioMonto=document.getElementsByName('select_criterio_monto')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_criterio_monto+' data-estado="'+element.estado+'">'+element.descripcion+'</option>';
    });
    selectCriterioMonto.innerHTML = html;
}
function fill_select_criterio_prioridad(res){
    let data = res.data;
    
    let selectCriterioPrioridad=document.getElementsByName('select_criterio_prioridad')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_prioridad+' data-estado="'+element.estado+'">'+element.descripcion+'</option>';
    });
    selectCriterioPrioridad.innerHTML = html;
}

function cambiarGrupoCriterio(event){
    var selected = event.target.options[event.target.options.selectedIndex];    
    var dataSelected = selected.getAttribute('data-estado');
    document.getElementsByName('estado_grupo_criterio')[0].value= dataSelected;
    let id_grupo_criterio = event.target.value;
    let id_flujo = document.getElementsByName('id_flujo')[0].value;

    getOpcionCriterio(id_flujo,id_grupo_criterio);

}



function getOpcionCriterio(id_flujo,id_grupo_criterio){
    $.ajax({
        type: 'GET',
        url: '/mostrar-criterio/'+id_flujo+'/'+id_grupo_criterio,
        success: function(response) {
            if(response.length == 1){
                metodoAsignarCriterio = 'PUT';
                document.getElementsByName('select_criterio_monto')[0].value = response[0].id_criterio_monto;
                document.getElementsByName('select_criterio_prioridad')[0].value = response[0].id_criterio_prioridad;
                document.getElementsByName('id_detalle_grupo_criterios')[0].value = response[0].id_detalle_grupo_criterios;
                document.getElementsByName('select_estado_detalle_grupo_criterio')[0].value = response[0].estado;
                
            }else if(response.length > 1){
                metodoAsignarCriterio = '';
                alert('error, existe mas de un registro para un grupo de criterio y flujo');
            }else if(response.length == 0){
                metodoAsignarCriterio = 'POST';
                document.getElementsByName('id_detalle_grupo_criterios')[0].value = '';


            }
            
        },
    })
}

function get_data_form_asignar_criterio(){
    let data = {
        'id_flujo':document.getElementsByName('id_flujo')[0].value,
        'id_criterio_monto':document.getElementsByName('select_criterio_monto')[0].value,
        'id_criterio_prioridad':document.getElementsByName('select_criterio_prioridad')[0].value,
        'id_grupo_criterio':document.getElementsByName('grupo_criterio')[0].value,
        'estado_grupo_criterio':document.getElementsByName('estado_grupo_criterio')[0].value,
        'id_detalle_grupo_criterios':document.getElementsByName('id_detalle_grupo_criterios')[0].value,
        'estado':document.getElementsByName('select_estado_detalle_grupo_criterio')[0].value
    };

    return data;
}

function guardarAsignarCriterio(){
    let data = get_data_form_asignar_criterio();
    
        $.ajax({
            type: metodoAsignarCriterio,
            url: '/asignar_criterio',
            datatype: "JSON",
            data: data,
            success: function(response){
                
                if(response == 'ACTUALIZADO' || response == 'GUARDADO'){

                    alert(response);   
                    listarTablaFlujo(null);  

                }else if(response == 'NO_ACTUALIZADO' || response =='NO_GUARDADO'){
                    alert(response);
                }else{
                    alert('ERROR');
                }
            }
        });

    statusBtnCriterios('DESHABILITAR');
}

function nuevoAsignarCriterio(){
    metodoAsignarCriterio = 'POST';
    get_grupo_criterio();
    lastActionBtnAsignarCriterio='NUEVO';
    statusBtnCriterios('HABILITAR');
    document.getElementsByName('select_criterio_monto')[0].value = 0;
    document.getElementsByName('select_criterio_prioridad')[0].value = 0;
    document.getElementsByName('select_estado_detalle_grupo_criterio')[0].value = 0;
    document.getElementsByName('id_detalle_grupo_criterios')[0].value = '';
}

function editarAsignarCriterio(){
    let id_detalle_grupo_criterios = document.getElementsByName('id_detalle_grupo_criterios')[0].value;
    if(id_detalle_grupo_criterios > 0){
        metodoAsignarCriterio = 'PUT';
    }else{
        metodoAsignarCriterio = 'POST';

    }
    
    lastActionBtnAsignarCriterio='EDITAR';
    statusBtnCriterios('HABILITAR');

}

