
//modal adjunta archivos
function archivosAdjuntosModal(event,index){
    event.preventDefault();

// console.log(data_item);

    if(data_item.length >0){
        id_detalle_requerimiento = data_item[index].id_detalle_requerimiento;
        obs = data_item[index].obs;
        $('[name=id_requerimiento]').val(data_item[index].id_requerimiento);
            // console.log('id_detalle_requerimiento',id_detalle_requerimiento);
            // console.log(data_item[index]);
        if(data_item[index].id_detalle_requerimiento >0){ // es un requerimiento traido de la base de datos\

            $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
                show: true,
                backdrop: 'true'
            });
            get_data_archivos_adjuntos(data_item[index].id_detalle_requerimiento);
            
        }else{ //no existe id_detalle_requerimiento => es un nuevo requerimiento
            alert("es nuevo requerimiento.... debe guardar el requerimiento primero");
            
            
        }
    }
    
}


function get_data_archivos_adjuntos(index){
    adjuntos=[];
    limpiarTabla('listaArchivos');
    baseUrl = 'mostrar-archivos-adjuntos/'+index;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                for (x=0; x<response.length; x++){
                    id_detalle_requerimiento= response[x].id_detalle_requerimiento;
                        adjuntos.push({ 
                            'id_adjunto':response[x].id_adjunto,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
            llenar_tabla_archivos_adjuntos(adjuntos);
            
            }else{
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData =  row.insertCell(0);
                tdSinData.setAttribute('colspan','5');
                tdSinData.setAttribute('class','text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}

function llenar_tabla_archivos_adjuntos(adjuntos){
    limpiarTabla('listaArchivos');
    htmls ='<tr></tr>';
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    for(var a=0;a < adjuntos.length;a++){
        var row = table.insertRow(a+1);
        var tdIdArchivo =  row.insertCell(0);
            tdIdArchivo.setAttribute('class','hidden');
            tdIdArchivo.innerHTML = adjuntos[a].id_adjunto?adjuntos[a].id_adjunto:'0';
        var tdIdDetalleReq =  row.insertCell(1);
            tdIdDetalleReq.setAttribute('class','hidden');
            tdIdDetalleReq.innerHTML = adjuntos[a].id_detalle_requerimiento?adjuntos[a].id_detalle_requerimiento:'0';
        row.insertCell(2).innerHTML = a+1;
        row.insertCell(3).innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '<button'+
        '    class="btn btn-danger btn-sm "'+
        '    name="btnEliminarArchivoAdjunto"'+
        '    onclick="eliminarArchivoAdjunto('+a+','+adjuntos[a].id_adjunto+')"'+
        '    title="Eliminar Archivo"'+
        '>'+
        '    <i class="fas fa-trash"></i>'+
        '</button>'+
        '</div>';

    }
    return null;
}

function eliminarArchivoAdjunto(indice,id_adjunto){

    // document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] input[name='nombre_archivo']").value;
    if(id_adjunto >0){
        var ask = confirm('¿Desea eliminar este archivo ?');
        if (ask == true){
            $.ajax({
                type: 'PUT',
                url: 'eliminar-archivo-adjunto-detalle-requerimiento/'+id_adjunto,
                dataType: 'JSON',
                success: function(response){
                    if(response.status == 'ok'){
                        alert("Archivo Eliminado");
                        get_data_archivos_adjuntos(id_detalle_requerimiento);
        
                    }else{
                        alert("No se pudo eliminar el archivo")
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            return false;
        }
    }else{
        only_adjuntos.splice(indice,1 );
        adjuntos.splice(indice,1);
        imprimir_tabla_adjuntos();

    }    

}

let only_adjuntos=[];
function agregarAdjunto(event){ //agregando nuevo archivo adjunto
    let id_req = document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;

    //  console.log(event.target.value);
     let fileList = event.target.files;
     let file = fileList[0];

     let extension = file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase(); // assuming that this file has any extension
    //  console.log(extension);
    if (extension === 'dwg' 
        || extension === 'dwt' 
        || extension === 'cdr' 
        || extension === 'back' 
        || extension === 'backup' 
        || extension === 'psd' 
        || extension === 'sql' 
        || extension === 'exe' 
        || extension === 'html' 
        || extension === 'js' 
        || extension === 'php' 
        || extension === 'ai' 
        || extension === 'mp4' 
        || extension === 'mp3' 
        || extension === 'avi' 
        || extension === 'mkv' 
        || extension === 'flv' 
        || extension === 'mov' 
        || extension === 'wmv' 
        ) {
            alert('Extensión de archivo incorrecta (NO se permite .'+extension+').  La entrada del archivo se borra.');
            event.target.value = '';
        }
        else {


            let archivo ={
                id_adjunto: 0,
                id_requerimiento: id_req,
                id_detalle_requerimiento: id_detalle_requerimiento,
                archivo:file.name,
                fecha_registro: new Date().toJSON().slice(0, 10),
                estado: 1
                // file:event.target.files[0]
            }
            let only_file = event.target.files[0]
            adjuntos.push(archivo);
            only_adjuntos.push(only_file);
            // console.log("agregar adjunto");
            // console.log(adjuntos);
            // console.log(only_adjuntos);
            imprimir_tabla_adjuntos();
            
    }
}

function imprimir_tabla_adjuntos(){
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    var indicadorTd='';
    for(var a=0;a < adjuntos.length;a++){
        var row = table.insertRow(-1);

        if(adjuntos[a].id_adjunto ==0){
            indicadorTd="green"; // si es nuevo
        }
        var tdIdArchivo =  row.insertCell(0);
        tdIdArchivo.setAttribute('class','hidden');
        tdIdArchivo.innerHTML = adjuntos[a].id_adjunto?adjuntos[a].id_adjunto:'0';
        var tdIdDetalleReq =  row.insertCell(1);
        tdIdDetalleReq.setAttribute('class','hidden');
        tdIdDetalleReq.innerHTML = 0;
        var tdNumItem = row.insertCell(2);
        tdNumItem.innerHTML = a+1;
        var tdNameFile = row.insertCell(3);
        tdNameFile.innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        tdNameFile.setAttribute('class',indicadorTd);
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '<button'+
        '    class="btn btn-danger btn-sm "'+
        '    name="btnEliminarArchivoAdjunto"'+
        '    onclick="eliminarArchivoAdjunto('+a+','+adjuntos[a].id_adjunto+')"'+
        '    title="Eliminar Archivo"'+
        '>'+
        '    <i class="fas fa-trash"></i>'+
        '</button>'+
        '</div>';
    }
}

function guardarAdjuntos(){
    
    // console.log(obs);
    let id_req = $('[name=id_requerimiento]').val();
    if(id_req < 0){
        alert("error 790: GuardarAdjunto");
    }
    
    // console.log(adjuntos);
    // console.log(only_adjuntos);
    let id_requerimiento = id_req;
    let id_detalle_requerimiento = adjuntos[0].id_detalle_requerimiento;

    const onlyNewAdjuntos = adjuntos.filter(id => id.id_adjunto == 0); // solo enviar los registros nuevos

        var myformData = new FormData();        
        // myformData.append('archivo_adjunto', JSON.stringify(adjuntos));
        for(let i=0;i<only_adjuntos.length;i++){
            myformData.append('only_adjuntos[]', only_adjuntos[i]);
            
        }
        
        myformData.append('detalle_adjuntos', JSON.stringify(onlyNewAdjuntos));
        myformData.append('id_requerimiento', id_requerimiento);
        myformData.append('id_detalle_requerimiento', id_detalle_requerimiento);
    
        baseUrl = 'guardar-archivos-adjuntos-detalle-requerimiento';
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            // dataType: 'JSON',
            url: baseUrl,
            success: function(response){
                // console.log(response);     
                if (response > 0){
                    alert("Archivo(s) Guardado(s)");
                    only_adjuntos=[];
                    get_data_archivos_adjuntos(id_detalle_requerimiento);
                    let ask = confirm('¿Desea seguir agregando más archivos ?');
                    if (ask == true){
                        return false;
                    }else{
                        $('#modal-adjuntar-archivos-detalle-requerimiento').modal('hide');
                    }
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
}