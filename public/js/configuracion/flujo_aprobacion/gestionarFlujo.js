$(function() {

    vista_extendida();
    listarTablaFlujo();
    listarTablaOperacion();

    $.ajax({
        type: 'GET',
        url: '/session-rol-aprob',
        success: function(response) {
            userSession = response
            userSession.roles.forEach(element => {
                if (
                    element.nombre_area == 'logistica' ||
                    element.nombre_area == 'LOGISTICA'
                ) {
                    disabledBtn = false
                }
            })
        },
    })
})

function vista_extendida() {
    let body = document.getElementsByTagName('body')[0]
    body.classList.add('sidebar-collapse')
}

function limpiarTabla(idElement){
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--) {
        table.deleteRow(i);
    }
    return null;
}

function listarTablaFlujo(id=null) {
    let urlBase='/mostrar-flujos';
    if(id >0){
        urlBase+= '/'+id+'/'+null;
    }else{
        urlBase+= '/'+null+'/'+null;
    }

    var vardataTables = funcDatatables();
    $('#listarFlujos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': urlBase,
        'columns': [
            {'data': 'id_flujo'},
            {'data': 'nombre'},
            {'data': 'tp_documento_descripcion'},
            {'data': 'razon_social_empresa'},
            {'data': 'codigo_sede'},
            {'data': 'grupo_descripcion'},
            {'data': 'area_descripcion'},
            {'data': 'rol_concepto_descripcion'},
            {'data': 'orden'},
            {'render':
            function ( data, type, row ) {
                let html='';
                if(row.cantidad_criterio_monto > 0){
                    html+='<i class="far fa-money-bill-alt fa-2x" title="Monto"></i> ';
                }
                if(row.cantidad_criterio_prioridad > 0){
                    html+='<i class="fas fa-thermometer-full fa-2x" title="Prioridad"></i> ';
                }
                return (html);
                
            }
            },
            {'render':
            function ( data, type, row ) {
                let html ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group">';
                        html +='<button type="button" class="btn btn-sm btn-log btn-primary" name="btnEditarFlujo" title="Editar Flujo" onClick="editarFlujo('+row.id_flujo+');"><i class="fas fa-edit fa-xs"></i></button>';
                        html +='<button type="button" class="btn btn-sm btn-log btn-warning" name="btnGestionarCriterio" title="Gestionar Criterio" onClick="gestionarCriterio('+row.id_flujo+');"><i class="fas fa-bezier-curve fa-xs"></i></button>';
                        html +='<button type="button" class="btn btn-sm btn-log btn-danger" name="btnAnularFlujo" title="Anular Flujo" onClick="anularFlujo('+row.id_flujo+');"><i class="fas fa-trash fa-xs"></i></button>';
                    html +='</div>';
                return (html);
                
            }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

}


function cambiarGrupo(id_grupo_flujo){
    listarTablaFlujo(id_grupo_flujo)
}

function limpiar_inputs_flujo(){
    document.getElementsByName('nombre_flujo')[0].value = '';
    document.getElementsByName('id_flujo')[0].value = '' ;
    document.getElementsByName('grupo_flujo')[0].value = '';
    document.getElementsByName('orden')[0].value = '';
    document.getElementsByName('flujo_estado')[0].value = '';
    document.getElementsByName('operacion')[0].value = '';
}

function editarFlujo(id_flujo) {    

    limpiar_inputs_flujo();

    $('#modal-gestionar_flujo').modal({
        show: true,
    })

    get_grupo_flujo();
    get_rol();
    get_operacion();
    document.getElementsByName('btnActualizarFlujo')[0].setAttribute('disabled',true);

    

    $.ajax({
        type: 'GET',
        url: '/mostrar-flujos/null/'+id_flujo,
        success: function(response) {  
            document.getElementsByName('id_flujo')[0].value=response.data[0].id_flujo?response.data[0].id_flujo:'';
            document.getElementsByName('grupo_flujo')[0].value=response.data[0].id_grupo_flujo?response.data[0].id_grupo_flujo:'';
            document.getElementsByName('nombre_flujo')[0].value=response.data[0].nombre?response.data[0].nombre:'';
            document.getElementsByName('rol')[0].value=response.data[0].id_rol?response.data[0].id_rol:'';
            document.getElementsByName('orden')[0].value=response.data[0].orden?response.data[0].orden:'';
            document.getElementsByName('flujo_estado')[0].value=response.data[0].flujo_estado?response.data[0].flujo_estado:0;
            document.getElementsByName('operacion')[0].value=response.data[0].id_operacion?response.data[0].id_operacion:0;
            
            get_operacion_selected(response.data[0].id_operacion?response.data[0].id_operacion:0);

            document.getElementsByName('btnActualizarFlujo')[0].removeAttribute('disabled');
        },
    })


}

function anularFlujo(id_flujo){
    if(id_flujo > 0){
        var ask = confirm('¿Desea anular este flujo?');
        if (ask == true){
            $.ajax({
                type: 'PUT',
                url: '/anular-flujo/'+id_flujo,
                success: function(response) {   
                    if(response == 'ACTUALIZADO'){
                        alert('Flujo anulado!');   
                        listarTablaFlujo(null);  
                    }else if(response == 'NO_ACTUALIZADO'){
                        alert('NO se puedo anular');
                    }else{
                        alert('ERROR al intentar anular');
                    }                
                },
            })
        }else{
            return false;
        }
    }else{
        alert('No existe id_flujo');
    }

}

function get_grupo_flujo(){
    $.ajax({
        type: 'GET',
        url: '/mostrar_grupo_flujo',
        success: function(response) {   
            fill_select_grupo_flujo(response);
            
        },
    })
}

function get_rol(){
    $.ajax({
        type: 'GET',
        url: '/mostrar_roles_concepto',
        success: function(response) {   
            fill_select_rol(response);
            
        },
    })
}
function get_tipo_documento(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-tipo-documento',
        success: function(response) {               
            fill_select_tipo_documento(response);
            
        },
    })
}
function get_empresa(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-empresa',
        success: function(response) {               
            fill_select_empresa(response);
            
        },
    })
}
function get_sede(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-sede',
        success: function(response) {               
            fill_select_sede(response);
            
        },
    })
}
function get_grupo(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-grupo',
        success: function(response) {               
            fill_select_grupo(response);
            
        },
    })
}
function get_area(){
    $.ajax({
        type: 'GET',
        url: '/mostrar-area',
        success: function(response) {               
            fill_select_area(response);
            
        },
    })
}

function get_operacion(){
    $.ajax({
        type: 'GET',
        url: '/mostrar_operacion',
        success: function(response) {   
            fill_select_operacion(response);            
        },
    })
}


function get_operacion_selected(id){
    if(id>0){

        $.ajax({
            type: 'GET',
            url: '/mostrar_operacion/'+id,
            success: function(response) {   
                fill_table_operacion(response);
                
                
            },
        })
    }
}

function fill_table_operacion(data){    
    limpiarTabla('listaOperacion');
    htmls ='<tr></tr>';
    $('#listaOperacion tbody').html(htmls);
    var table = document.getElementById("listaOperacion");
    if(data.length > 0){
        for(var a=0;a < data.length;a++){
            var row = table.insertRow(a+1);
            row.insertCell(0).innerHTML = data[a].id_operacion?data[a].id_operacion:0;
            row.insertCell(1).innerHTML = data[a].razon_social_empresa?data[a].razon_social_empresa:'-';
            row.insertCell(2).innerHTML = data[a].codigo_sede?data[a].codigo_sede:'-';
            row.insertCell(3).innerHTML = data[a].grupo_descripcion?data[a].grupo_descripcion:'';
            row.insertCell(4).innerHTML = data[a].area_descripcion?data[a].area_descripcion:'-';
            row.insertCell(5).innerHTML = data[a].tipo_documento?data[a].tipo_documento:'-';
            row.insertCell(6).innerHTML = data[a].estado==1?'Activo':'Anulado';
            row.insertCell(7).innerHTML = 
            '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
            '<button type="button" class="btn btn-sm btn-log btn-primary" name="btnEditarOperacion" title="Editar operación" onclick="editarOperacion('+data[a].id_operacion+');"><i class="fas fa-edit fa-xs"></i></button>'+
            '</div>';
        }
    }
}

function OnchangeOperacion(event){
    get_operacion_selected(event.target.value);

}


function fill_select_grupo_flujo(data){
    let selectGrupoFlujo=document.getElementsByName('grupo_flujo')[0];
     let html ='';
    html+= '<option value="0" >Elija una opción</option>';      
    data.forEach(element => {    
        html+= '<option value='+element.id_grupo_flujo+'>'+element.descripcion+'</option>';
    });
    selectGrupoFlujo.innerHTML = html;
}
function fill_select_rol(data){
    let selectRol=document.getElementsByName('rol')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_rol_concepto+'>'+element.descripcion+'</option>';
    });
    selectRol.innerHTML = html;
}
function fill_select_tipo_documento(data){
    let selectTipoDoc=document.getElementsByName('tipo_documento_')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_tp_documento+'>'+element.descripcion+'</option>';
    });
    selectTipoDoc.innerHTML = html;
}
function fill_select_empresa(data){
    let selectEmpresa=document.getElementsByName('empresa_')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_empresa+'>'+element.razon_social+'</option>';
    });
    selectEmpresa.innerHTML = html;
}
function fill_select_sede(data){
    let selectSede=document.getElementsByName('sede_')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_sede+'>'+element.descripcion+'</option>';
    });
    selectSede.innerHTML = html;
}
function fill_select_grupo(data){
    let selectGrupo=document.getElementsByName('grupo_')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_grupo+'>'+element.descripcion+'</option>';
    });
    selectGrupo.innerHTML = html;
}
function fill_select_area(data){
    let selectArea=document.getElementsByName('area_')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';  
    data.forEach(element => {
        html+= '<option value='+element.id_area+'>'+element.descripcion+'</option>';
    });
    selectArea.innerHTML = html;
}
function fill_select_operacion(data){
    let selectOperacion=document.getElementsByName('operacion')[0];
    let html ='';
    html+= '<option value="0" >Elija una opción</option>';
    data.forEach(element => {        
        html+= '<option value='+element.id_operacion+'>'+element.descripcion+'</option>';
    });
    selectOperacion.innerHTML = html;

}

function getDataFormularioGestionarFlujo(){
    let data_flujo = {
        'id_flujo':document.getElementsByName('id_flujo')[0].value,
        'nombre_flujo':document.getElementsByName('nombre_flujo')[0].value,
        'grupo_flujo':document.getElementsByName('grupo_flujo')[0].value,
        'orden': document.getElementsByName('orden')[0].value,
        'rol': document.getElementsByName('rol')[0].value,
        'operacion': document.getElementsByName('operacion')[0].value,
        'estado': document.getElementsByName('flujo_estado')[0].value
        
    };
    return data_flujo;
}


function getDataFormularioGestionarOperacion_(){
    let data_operacion = {
        'id_operacion': document.getElementsByName('id_operacion_')[0].value,
        'operacion_descripcion': document.getElementsByName('operacion_descripcion_')[0].value,
        'tipo_documento': document.getElementsByName('tipo_documento_')[0].value,
        'empresa': document.getElementsByName('empresa_')[0].value,
        'sede': document.getElementsByName('sede_')[0].value,
        'area': document.getElementsByName('area_')[0].value,
        'grupo': document.getElementsByName('grupo_')[0].value,
        'estado': document.getElementsByName('operacion_estado_')[0].value
    };
    
    return data_operacion;
}

function actualizarFlujo(event){
    event.preventDefault();
    let data =  getDataFormularioGestionarFlujo();
    $.ajax({
        type: 'PUT',
        url: '/actualizar_flujo',
        datatype: "JSON",
        data: data,
        success: function(response){
            
            if(response == 'ACTUALIZADO'){
                alert('Datos Actualizado!');   
                listarTablaFlujo(null);  
            }else if(response == 'NO_ACTUALIZADO'){
                alert('NO se puedo actualizar');
            }else{
                alert('ERROR al intentar actualizar');
            }
        }
    });
}

function actualizarOperacion_(event){
    event.preventDefault();
    let data =  getDataFormularioGestionarOperacion_();
    
    $.ajax({
        type: 'PUT',
        url: '/actualizar_operacion',
        datatype: "JSON",
        data: data,
        success: function(response){
            
            if(response == 'ACTUALIZADO'){
                alert('Datos Actualizado!');   
                listarTablaOperacion(null);  
            }else if(response == 'NO_ACTUALIZADO'){
                alert('NO se puedo actualizar');
            }else{
                alert('ERROR al intentar actualizar');
            }
        }
    });
}



function listarTablaOperacion(id=null) {
    let urlBase='/mostrar-operaciones';
    if(id >0){
        urlBase+= '/'+id;
    }else{
        urlBase+= '/'+null;
    }

    var vardataTables = funcDatatables();
    $('#listarOperaciones').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': urlBase,
        'columns': [
            {'data': 'id_operacion'},
            {'data': 'operacion_descripcion'},
            {'data': 'tp_documento_descripcion'},
            {'data': 'razon_social_empresa'},
            {'data': 'codigo_sede'},
            {'data': 'grupo_descripcion'},
            {'data': 'area_descripcion'},
            {'render':
            function ( data, type, row ) {
                let html ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group">';
                        html +='<button type="button" class="btn btn-sm btn-log btn-primary" name="btnEditarOperacion" title="Editar Operación" onClick="editarOperacion('+row.id_operacion+');"><i class="fas fa-edit fa-xs"></i></button>';
                        html +='<button type="button" class="btn btn-sm btn-log btn-danger" name="btnEliminarOperacion" title="Eliminar Operación" onClick="anularOperacion('+row.id_operacion+');"><i class="fas fa-trash fa-xs"></i></button>';
                    html +='</div>';
                return (html);
                
            }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });


}

function limpiar_inputs_operacion(){
    document.getElementsByName('id_operacion_')[0].value = '' ;
    document.getElementsByName('operacion_descripcion_')[0].value = '';
    document.getElementsByName('tipo_documento_')[0].value = '';
    document.getElementsByName('empresa_')[0].value = '';
    document.getElementsByName('sede_')[0].value = '';
    document.getElementsByName('grupo_')[0].value = '';
    document.getElementsByName('area_')[0].value = '';
    document.getElementsByName('operacion_estado_')[0].value = '';

}


function editarOperacion(id){
    limpiar_inputs_operacion();

    $('#modal-gestionar_operacion').modal({
        show: true,
    })

    get_tipo_documento();
    get_empresa();
    get_sede();
    get_grupo();
    get_area();
    
    document.getElementsByName('btnActualizarOperacion_')[0].setAttribute('disabled',true);

    if(id>0){

        $.ajax({
            type: 'GET',
            url: '/mostrar_operacion/'+id,
            success: function(response) {            
            document.getElementsByName('id_operacion_')[0].value=response[0].id_operacion?response[0].id_operacion:0;
            document.getElementsByName('operacion_descripcion_')[0].value=response[0].descripcion?response[0].descripcion:'';
            document.getElementsByName('tipo_documento_')[0].value=response[0].id_tp_documento?response[0].id_tp_documento:'';
            document.getElementsByName('empresa_')[0].value=response[0].id_empresa?response[0].id_empresa:'';
            document.getElementsByName('sede_')[0].value=response[0].id_sede?response[0].id_sede:'';
            document.getElementsByName('grupo_')[0].value=response[0].id_grupo?response[0].id_grupo:'';
            document.getElementsByName('area_')[0].value=response[0].id_area?response[0].id_area:'';
            document.getElementsByName('operacion_estado_')[0].value=response[0].estado?response[0].estado:0;
                
            document.getElementsByName('btnActualizarOperacion_')[0].removeAttribute('disabled');

            },
        })
    }

}

function anularOperacion(id_operacion){
    if(id_operacion > 0){
        var ask = confirm('¿Desea anular este operación?');
        if (ask == true){
            $.ajax({
                type: 'PUT',
                url: '/anular-operacion/'+id_operacion,
                success: function(response) {   
                    if(response == 'ACTUALIZADO'){
                        alert('Operación anulado!');   
                        listarTablaOperacion(null);  
                    }else if(response == 'NO_ACTUALIZADO'){
                        alert('NO se puedo anular');
                    }else{
                        alert('ERROR al intentar anular');
                    }                
                },
            })
        }else{
            return false;
        }
    }else{
        alert('No existe id_operacion');
    }

}