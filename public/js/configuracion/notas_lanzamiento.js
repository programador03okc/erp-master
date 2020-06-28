$(function() {

    vista_extendida();
    mostrar_nota_lanzamiento_select();


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

function mostrar_nota_lanzamiento_select(){
    $.ajax({
        type: 'GET',
        url: '/mostrar_notas_lanzamiento_select',
        success: function(response) {   
            fill_select_nota_lanzamiento(response);
        },
    })
}

function fill_select_nota_lanzamiento(data){
     let selectNotaLanzamiento=document.getElementsByName('nota_lanzamiento')[0];
    let html ='';
    data.forEach(element => {        
        html+= '<option data-selected='+JSON.stringify(element)+'  value='+element.id_nota_lanzamiento+'>'+element.version+'</option>';
    });
   selectNotaLanzamiento.innerHTML = html;

    let id_nota=selectNotaLanzamiento.value;
    if(id_nota >0){
        mostrar_detalle_notas_lanzamiento(id_nota);

        fill_detail_nota_lanzamiento();

    }
}

function fill_detail_nota_lanzamiento(){
    let selectNotaLanzamiento=document.getElementsByName('nota_lanzamiento')[0];
    var selected = selectNotaLanzamiento.options[selectNotaLanzamiento.selectedIndex];
    var dataSelected = JSON.parse(selected.getAttribute('data-selected'));
    llenar_tabla_nota_lanzamiento(dataSelected);
}

function llenar_tabla_nota_lanzamiento(payload){  
        let data = [];
        data.push(payload);
        limpiarTabla('listarNotasLanzamiento');
        htmls ='<tr></tr>';
        $('#listarNotasLanzamiento tbody').html(htmls);
        var table = document.getElementById("listarNotasLanzamiento");
        // console.log(data.length);
        
        if(data.length > 0){
            for(var a=0;a < data.length;a++){
                let option ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group">';
                            option +='<button type="button" class="btn btn-sm btn-log btn-primary" name="btnEditarNotaLanzamiento" title="Gestionar Nota de Lanzamiento" onClick="editarNotaLanzamiento('+data[a].id_nota_lanzamiento+');"><i class="fas fa-edit fa-xs"></i></button>';
                            option +='<button type="button" class="btn btn-sm btn-log btn-danger" name="btnEditarNotaLanzamiento" title="Eliminar Nota de Lanzamiento" onClick="eliminarNotaLanzamiento('+data[a].id_nota_lanzamiento+');"><i class="fas fa-trash fa-xs"></i></button>';
                        option +='</div>';
                var row = table.insertRow(a+1);
                row.insertCell(0).innerHTML = data[a].id_nota_lanzamiento?data[a].id_nota_lanzamiento:'-';
                row.insertCell(1).innerHTML = data[a].version?data[a].version:'-';
                row.insertCell(2).innerHTML = data[a].version_actual;
                row.insertCell(3).innerHTML = data[a].fecha_nota_lanzamiento?data[a].fecha_nota_lanzamiento:'-';
                row.insertCell(4).innerHTML = option;
            }
        }
    }


function cambiarNotaLanzamiento(id_nota){
    mostrar_detalle_notas_lanzamiento(id_nota);    
    fill_detail_nota_lanzamiento();
}

function mostrar_detalle_notas_lanzamiento(id){
    var vardataTables = funcDatatables();

    let urlBase='/listar_detalle_notas_lanzamiento/'+id;

    $('#listarDetalleNotasLanzamiento').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': urlBase,
        'columns': [
            {'data': 'id_detalle_nota_lanzamiento'},
            {'data': 'titulo'},
            {'data': 'descripcion'},
            {'data': 'fecha_detalle_nota_lanzamiento'},
            {'render':
            function ( data, type, row ) {
                let html ='<div class="btn-group btn-group-sm" role="group" aria-label="Second group">';
                        html +='<button type="button" class="btn btn-sm btn-log btn-primary" name="btnEditarDetalleNotaLanzamiento" title="Editar Detalle Nota de Lanzamiento" onClick="editarDetalleNotaLanzamiento('+row.id_detalle_nota_lanzamiento+');"><i class="fas fa-edit fa-xs"></i></button>';
                        html +='<button type="button" class="btn btn-sm btn-log btn-danger" name="btnEliminarDetalleNotaLanzamiento" title="Eliminar Detalle Nota de Lanzamiento" onClick="eliminarDetalleNotaLanzamiento('+row.id_detalle_nota_lanzamiento+');"><i class="fas fa-trash fa-xs"></i></button>';
                    html +='</div>';
                return (html);
                
            }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    
}


function agregarNotaLanzamiento(){
    $('#modal-modal_nota_lanzamiento').modal({
        show: true
    });
    document.getElementsByName('btnAgregarNota')[0].classList.remove('invisible');
    document.getElementsByName('btnActualizarNota')[0].classList.add('invisible');
    document.getElementById('title-nota_lanzamiento').innerText = 'Agregar Nota de Lanzamiento';
    document.getElementsByName('id_nota_lanzamiento')[0].value= '';
    document.getElementsByName('version')[0].value='';
    document.getElementsByName('version_actual')[0].value='';
    document.getElementsByName('fecha_nota_lanzamiento')[0].value= '';
    
}
function agregarDetalleNotaLanzamiento(){
    $('#modal-modal_detalle_nota_lanzamiento').modal({
        show: true
    });
    document.getElementsByName('btnAgregarDetalleNota')[0].classList.remove('invisible');
    document.getElementsByName('btnActualizarDetalleNota')[0].classList.add('invisible');
    document.getElementById('title-detalle_nota_lanzamiento').innerText = 'Agregar Detalle de Nota de Lanzamiento';

    document.getElementsByName('id_detalle_nota_lanzamiento')[0].value= '';
    document.getElementsByName('titulo')[0].value='';
    document.getElementsByName('descripcion')[0].value='';
    document.getElementsByName('fecha_detalle_nota_lanzamiento')[0].value= '';
    
}

function guardarNotaLanzamiento(){
    let data =  getDataFormularioNotaLanzamiento();

    $.ajax({
        type: 'POST',
        url: '/guardar_nota_lanzamiento',
        datatype: "JSON",
        data: data,
        success: function(response){
            
            if(response == 'GUARDADO'){
                alert('Datos guardados!');   
                mostrar_nota_lanzamiento_select();  
            }else if(response == 'NO_GUARDADO'){
                alert('NO se puedo guardar');
            }else{
                alert('ERROR al intentar guardar');

            }
        }
    });
    
}

function editarNotaLanzamiento(id){
    document.getElementsByName('btnAgregarNota')[0].classList.add('invisible');
    document.getElementsByName('btnActualizarNota')[0].classList.remove('invisible');
    document.getElementById('title-nota_lanzamiento').innerText = 'Editar Nota de Lanzamiento';

    
    $('#modal-modal_nota_lanzamiento').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        url: '/mostrar_nota_lanzamiento/'+id,
        success: function(response) {                        
            document.getElementsByName('id_nota_lanzamiento')[0].value=response.id_nota_lanzamiento?response.id_nota_lanzamiento:'';
            document.getElementsByName('version')[0].value=response.version?response.version:'';
            document.getElementsByName('version_actual')[0].value=response.version_actual?response.version_actual:'';
            document.getElementsByName('fecha_nota_lanzamiento')[0].value= response.fecha_nota_lanzamiento?response.fecha_nota_lanzamiento:'';
        },
    })
}

function editarDetalleNotaLanzamiento(id){
    
    document.getElementsByName('btnAgregarDetalleNota')[0].classList.add('invisible');
    document.getElementsByName('btnActualizarDetalleNota')[0].classList.remove('invisible');
    document.getElementById('title-detalle_nota_lanzamiento').innerText = 'Editar Detalle de Nota de Lanzamiento';

    
    $('#modal-modal_detalle_nota_lanzamiento').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        url: '/mostrar_detalle_nota_lanzamiento/'+id,
        success: function(response) {                                    
            document.getElementsByName('id_detalle_nota_lanzamiento')[0].value=response.id_detalle_nota_lanzamiento?response.id_detalle_nota_lanzamiento:'';
            document.getElementsByName('titulo')[0].value=response.titulo?response.titulo:'';
            document.getElementsByName('descripcion')[0].value=response.descripcion?response.descripcion:'';
            document.getElementsByName('fecha_detalle_nota_lanzamiento')[0].value= response.fecha_detalle_nota_lanzamiento?response.fecha_detalle_nota_lanzamiento:'';
        },
    })
}

function getDataFormularioNotaLanzamiento(){
    let data = {
        'id_nota_lanzamiento':document.getElementsByName('id_nota_lanzamiento')[0].value,
        'version': document.getElementsByName('version')[0].value,
        'version_actual': document.getElementsByName('version_actual')[0].value,
        'fecha_nota_lanzamiento': document.getElementsByName('fecha_nota_lanzamiento')[0].value
    };
    return data;
}

function getDataFormularioDetalleNotaLanzamiento(){
    let data = {
        'id_detalle_nota_lanzamiento':document.getElementsByName('id_detalle_nota_lanzamiento')[0].value,
        'titulo': document.getElementsByName('titulo')[0].value,
        'descripcion': document.getElementsByName('descripcion')[0].value,
        'fecha_detalle_nota_lanzamiento': document.getElementsByName('fecha_detalle_nota_lanzamiento')[0].value
    };
    return data;
}

function actualizarNotaLanzamiento(){
    let data =  getDataFormularioNotaLanzamiento();
    

    $.ajax({
        type: 'PUT',
        url: '/actualizar_nota_lanzamiento',
        datatype: "JSON",
        data: data,
        success: function(response){
            
            if(response == 'ACTUALIZADO'){
                alert('Datos Actualizado!');   
                mostrar_nota_lanzamiento_select();  
            }else if(response == 'NO_ACTUALIZADO'){
                alert('NO se puedo actualizar');
            }else{
                alert('ERROR al intentar actualizar');

            }
        }
    });
}
function actualizarDetalleNotaLanzamiento(){
    let data =  getDataFormularioDetalleNotaLanzamiento();
    

    $.ajax({
        type: 'PUT',
        url: '/actualizar_detalle_nota_lanzamiento',
        datatype: "JSON",
        data: data,
        success: function(response){
            
            if(response == 'ACTUALIZADO'){
                alert('Datos Actualizado!');   
                mostrar_nota_lanzamiento_select();  
            }else if(response == 'NO_ACTUALIZADO'){
                alert('NO se puedo actualizar');
            }else{
                alert('ERROR al intentar actualizar');

            }
        }
    });
}


function eliminarNotaLanzamiento(id){
     $.ajax({
        type: 'PUT',
        url: '/eliminar_nota_lanzamiento/'+id,
        success: function(response) {   
            if(response == 'ELIMINADO'){
                alert('Registro eliminado!');   
                mostrar_nota_lanzamiento_select();  
            }else if(response == 'NO_ELIMINADO'){
                alert('NO se puedo eliminar');
            }else{
                alert('ERROR al intentar eliminar');

            }        
        },
    }) 
} 

function eliminarDetalleNotaLanzamiento(id){
     $.ajax({
        type: 'PUT',
        url: '/eliminar_detalle_nota_lanzamiento/'+id,
        success: function(response) {   
            if(response == 'ELIMINADO'){
                alert('Registro eliminado!');   
                mostrar_nota_lanzamiento_select();  
            }else if(response == 'NO_ELIMINADO'){
                alert('NO se puedo eliminar');
            }else{
                alert('ERROR al intentar eliminar');

            }        
        },
    }) 
} 

