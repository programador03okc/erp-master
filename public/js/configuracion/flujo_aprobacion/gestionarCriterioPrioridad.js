var lastActionBtnPrioridad='';

function listarTablaCriterioPrioridad(id=null){
    let urlBase='/mostrar-criterio-prioridad';
    if(id >0){
        urlBase+= '/'+id;
    }else{
        urlBase+= '/'+null;
    }

    var vardataTables = funcDatatables();
    $('#listarCriterioPrioridad').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': urlBase,
        'columns': [
            {'data': 'id_prioridad'},
            {'data': 'descripcion'},
            {'render':
            function ( data, type, row ) {
                let html='';
                if(row.estado == 1){
                    html+='Activado';
                }
                else{
                    html+='Anulado';
                }
                return (html);
                
            }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('#listarCriterioPrioridad tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        }else{
            $('#listarCriterioPrioridad').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        statusBtnCriterios('DESHABILITAR');

        var idTr = $(this)[0].firstChild.innerHTML;
        // console.log(idTr);

        get_one_criterio_prioridad(idTr);
        
        
    });

}

function get_one_criterio_prioridad(id){
    $.ajax({
        type: 'GET',
        url: '/mostrar-criterio-prioridad/'+id,
        success: function(response) {   
            fill_form_criterio_prioridad(response);
            
            
        },
    })
}

function fill_form_criterio_prioridad(res){
    let data = res.data;        
    document.getElementsByName('id_criterio_prioridad')[0].value = data[0].id_prioridad?data[0].id_prioridad:'';
    document.getElementsByName('descripcion_prioridad')[0].value = data[0].descripcion?data[0].descripcion:'';
    document.getElementsByName('estado_prioridad')[0].value = data[0].estado?data[0].estado:'';
    
}

function get_data_form_monto_prioridad(){
    let data = {
        'id_criterio_prioridad':document.getElementsByName('id_criterio_prioridad')[0].value,
        'descripcion_prioridad':document.getElementsByName('descripcion_prioridad')[0].value,
        'estado':document.getElementsByName('estado_prioridad')[0].value
    };

    return data;
}

function guardarCriterioPrioridad(){
    let data = get_data_form_monto_prioridad();
    if(lastActionBtnPrioridad == 'EDITAR'){
        $.ajax({
            type: 'PUT',
            url: '/actualizar-criterio_prioridad',
            datatype: "JSON",
            data: data,
            success: function(response){
                
                if(response == 'ACTUALIZADO'){
                    alert('Datos Actualizado!');   
                    listarTablaFlujo(null);  
                    listarTablaCriterioPrioridad(null);  
                }else if(response == 'NO_ACTUALIZADO'){
                    alert('NO se puedo actualizar');
                }else{
                    alert('ERROR al intentar actualizar');
                }
            }
        });
    }
    
    if(lastActionBtnPrioridad == 'NUEVO'){
        $.ajax({
            type: 'POST',
            url: '/guardar-criterio_prioridad',
            datatype: "JSON",
            data: data,
            success: function(response){
                
                if(response == 'GUARDADO'){
                    alert('Datos Guardados!');   
                    listarTablaFlujo(null);  
                    listarTablaCriterioPrioridad(null);  
                }else if(response == 'NO_GUARDADO'){
                    alert('NO se puedo guardados');
                }else{
                    alert('ERROR al intentar guardados');
                }
            }
        });
    }

    statusBtnCriterios('DESHABILITAR');
}
function nuevoCriterioPrioridad(){
    lastActionBtnPrioridad='NUEVO';
    statusBtnCriterios('HABILITAR');
    document.getElementsByName('id_criterio_prioridad')[0].value = '';
    document.getElementsByName('descripcion_prioridad')[0].value = '';
    document.getElementsByName('estado_prioridad')[0].value ='';

}
function editarCriterioPrioridad(){
    lastActionBtnPrioridad='EDITAR';

    statusBtnCriterios('HABILITAR');

}