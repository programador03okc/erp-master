
var lastActionBtnMonto='';

function listarTablaCriterioMonto(id=null){
    let urlBase='/mostrar-criterio-monto';
    if(id >0){
        urlBase+= '/'+id;
    }else{
        urlBase+= '/'+null;
    }

    var vardataTables = funcDatatables();
    $('#listarCriterioMonto').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': urlBase,
        'columns': [
            {'data': 'id_criterio_monto'},
            {'data': 'descripcion'},
            {'data': 'descripcion_operador1'},
            {'data': 'monto1'},
            {'data': 'descripcion_operador2'},
            {'data': 'monto2'},
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

    $('#listarCriterioMonto tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        }else{
            $('#listarCriterioMonto').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        statusBtnCriterios('DESHABILITAR');

        var idTr = $(this)[0].firstChild.innerHTML;
        // console.log(idTr);

        get_one_criterio_monto(idTr);
        
        
    });

}

function get_one_criterio_monto(id){
    $.ajax({
        type: 'GET',
        url: '/mostrar-criterio-monto/'+id,
        success: function(response) {   
            fill_form_criterio_monto(response);
            
            
        },
    })
}

function fill_form_criterio_monto(res){
    let data = res.data;        
    document.getElementsByName('id_criterio_monto')[0].value = data[0].id_criterio_monto?data[0].id_criterio_monto:'';
    document.getElementsByName('descripcion_monto')[0].value = data[0].descripcion?data[0].descripcion:'';
    document.getElementsByName('operador1')[0].value = data[0].id_operador1?data[0].id_operador1:0;
    document.getElementsByName('monto1')[0].value = data[0].monto1?data[0].monto1:'-';
    document.getElementsByName('operador2')[0].value = data[0].id_operador2?data[0].id_operador2:0;
    document.getElementsByName('monto2')[0].value = data[0].monto2?data[0].monto2:'';
    document.getElementsByName('estado_criterio')[0].value = data[0].estado?data[0].estado:'';
    
}

function get_operador(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-operador',
        success: function(response) {               
            fill_select_operador(response);
            
        },
    })
}

function fill_select_operador(data){    
    let selectOperador1=document.getElementsByName('operador1')[0];
    let selectOperador2=document.getElementsByName('operador2')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_operador+'>'+element.descripcion+'</option>';
    });
    selectOperador1.innerHTML = html;
    selectOperador2.innerHTML = html;
}

function statusBtnCriterios(option){
    let inputsCriterios = document.getElementsByClassName('activation');
    if(option =='HABILITAR'){
    
        for(var i = 0; i < inputsCriterios.length; i++)
        {
            inputsCriterios.item(i).removeAttribute('disabled');
        }
    }else if(option == 'DESHABILITAR'){
        for(var i = 0; i < inputsCriterios.length; i++)
        {
            inputsCriterios.item(i).setAttribute('disabled',true);
        }

    }
    
}

function get_data_form_monto_criterio(){
    let data = {
        'id_criterio_monto':document.getElementsByName('id_criterio_monto')[0].value,
        'descripcion_monto':document.getElementsByName('descripcion_monto')[0].value,
        'operador1':document.getElementsByName('operador1')[0].value,
        'monto1':document.getElementsByName('monto1')[0].value,
        'operador2':document.getElementsByName('operador2')[0].value,
        'monto2':document.getElementsByName('monto2')[0].value,
        'estado':document.getElementsByName('estado_criterio')[0].value
    };

    return data;
}

function nuevoCriterioMonto(){
    lastActionBtnMonto='NUEVO';
    statusBtnCriterios('HABILITAR');
    document.getElementsByName('id_criterio_monto')[0].value = '';
    document.getElementsByName('descripcion_monto')[0].value = '';
    document.getElementsByName('operador1')[0].value = 0 ;
    document.getElementsByName('monto1')[0].value = '';
    document.getElementsByName('operador2')[0].value = 0 ;
    document.getElementsByName('monto2')[0].value = '';
    document.getElementsByName('estado_criterio')[0].value = 0;


}

function editarCriterioMonto(){
    lastActionBtnMonto='EDITAR';

    statusBtnCriterios('HABILITAR');

}

function guardarCriterioMonto(){
    
    let data = get_data_form_monto_criterio();
    if(lastActionBtnMonto == 'EDITAR'){
        $.ajax({
            type: 'PUT',
            url: '/actualizar-criterio_monto',
            datatype: "JSON",
            data: data,
            success: function(response){
                
                if(response == 'ACTUALIZADO'){
                    alert('Datos Actualizado!');   
                    listarTablaFlujo(null);  
                    listarTablaCriterioMonto(null);  
                }else if(response == 'NO_ACTUALIZADO'){
                    alert('NO se puedo actualizar');
                }else{
                    alert('ERROR al intentar actualizar');
                }
            }
        });
    }
    
    if(lastActionBtnMonto == 'NUEVO'){
        $.ajax({
            type: 'POST',
            url: '/guardar-criterio_monto',
            datatype: "JSON",
            data: data,
            success: function(response){
                
                if(response == 'GUARDADO'){
                    alert('Datos Guardados!');   
                    listarTablaFlujo(null);  
                    listarTablaCriterioMonto(null);  
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