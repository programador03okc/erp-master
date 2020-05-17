var lastActionBtnGrupoCriterio='';


function modalGrupoCriterio(){
    $('#modal-gestionar_grupo_criterio').modal({
        show: true
    });

    listarTablaGrupoCriterio();
}


function listarTablaGrupoCriterio(id=null){
    let urlBase='/mostrar-grupo_criterio';
    if(id >0){
        urlBase+= '/'+id;
    }else{
        urlBase+= '/'+null;
    }

    var vardataTables = funcDatatables();
    $('#listarGrupoCriterio').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': urlBase,
        'columns': [
            {'data': 'id_grupo_criterios'},
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

    $('#listarGrupoCriterio tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        }else{
            $('#listarGrupoCriterio').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        statusBtnCriterios('DESHABILITAR');

        var idTr = $(this)[0].firstChild.innerHTML;
        // console.log(idTr);

        get_one_grupo_criterio(idTr);
        
        
    });

}

function get_one_grupo_criterio(id){
    $.ajax({
        type: 'GET',
        url: '/mostrar-grupo_criterio/'+id,
        success: function(response) {   
            fill_form_grupo_criterios(response);
            
            
        },
    })

}

function fill_form_grupo_criterios(res){    
    let data = res.data;
    document.getElementsByName('id_grupo_criterio_')[0].value = data[0].id_grupo_criterios?data[0].id_grupo_criterios:'';
    document.getElementsByName('descripcion_grupo_criterio_')[0].value = data[0].descripcion?data[0].descripcion:'';
    document.getElementsByName('estado_grupo_criterio_')[0].value = data[0].estado?data[0].estado:0;

}

function get_data_form_grupo_criterio(){
    let data = {
        'id_grupo_criterio':document.getElementsByName('id_grupo_criterio_')[0].value,
        'descripcion_grupo_criterio':document.getElementsByName('descripcion_grupo_criterio_')[0].value,
        'estado':document.getElementsByName('estado_grupo_criterio_')[0].value
    };

    return data;
}

function guardarGrupoCriterio(){
    
    let data = get_data_form_grupo_criterio();
    if(lastActionBtnGrupoCriterio == 'EDITAR'){
        $.ajax({
            type: 'PUT',
            url: '/grupo_criterio',
            datatype: "JSON",
            data: data,
            success: function(response){
                
                if(response == 'ACTUALIZADO'){
                    alert('Datos Actualizado!');   
                    listarTablaFlujo(null);  
                    listarTablaGrupoCriterio(null);  
                }else if(response == 'NO_ACTUALIZADO'){
                    alert('NO se puedo actualizar');
                }else{
                    alert('ERROR al intentar actualizar');
                }
            }
        });
    }
    
    if(lastActionBtnGrupoCriterio == 'NUEVO'){
        $.ajax({
            type: 'POST',
            url: '/grupo_criterio',
            datatype: "JSON",
            data: data,
            success: function(response){
                
                if(response == 'GUARDADO'){
                    alert('Datos Guardados!');   
                    listarTablaFlujo(null);  
                    listarTablaGrupoCriterio(null);  
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

function nuevoGrupoCriterio(){
    lastActionBtnGrupoCriterio='NUEVO';
    statusBtnCriterios('HABILITAR');
    document.getElementsByName('id_criterio_prioridad')[0].value = '';
    document.getElementsByName('descripcion_prioridad')[0].value = '';
    document.getElementsByName('estado_prioridad')[0].value ='';
}

function editarGrupoCriterio(){
    lastActionBtnGrupoCriterio='EDITAR';

    statusBtnCriterios('HABILITAR');
}