var adjuntosRequerimiento=[];
var onlyAdjuntosRequerimiento=[];
// var rutaListaAdjuntosRequerimiento;

// function inicializar( _rutaListaAdjuntosRequerimiento) {
//     rutaListaAdjuntosRequerimiento = _rutaListaAdjuntosRequerimiento;
// }

function archivosAdjuntosRequerimientoModal(){
    $('#modal-mostrar-archivos-adjuntos-requerimiento').modal({
        show: true
    });
        llenarTablaAdjuntosRequerimiento();
}

function llenarTablaAdjuntosRequerimiento(id_req=null){
    if(id_req >0){
        llenarTablaAdjuntosRequerimientoOtrosArchivos(id_req);
        llenarTablaAdjuntosRequerimientOrdenes(id_req);
        llenarTablaAdjuntosRequerimientComprobanteBancario(id_req);
        llenarTablaAdjuntosRequerimientComprobanteContable(id_req);
    }else{
        let idRequerimiento= document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;
        if(idRequerimiento>0){
            id_req =idRequerimiento;    
            llenarTablaAdjuntosRequerimientoOtrosArchivos(id_req);
            llenarTablaAdjuntosRequerimientOrdenes(id_req);
            llenarTablaAdjuntosRequerimientComprobanteBancario(id_req);
            llenarTablaAdjuntosRequerimientComprobanteContable(id_req);
        }

    }
}

function llenarTablaAdjuntosRequerimientoOtrosArchivos(id_req){
    var vardataTables = funcDatatables();
    $('#tablaAdjuntoOtrosArchivos').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        lengthChange: false,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        order:[[1,"desc"]],
        ajax: 'mostrar-archivos-adjuntos-requerimiento/'+id_req+'/'+1,
        columns: [

            {'render':
                function (data, type, row, meta){
                    return row.archivo;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.fecha_registro;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let btns = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                    '<a'+
                    '    class="btn btn-primary btn-sm "'+
                    '    name="btnAdjuntarArchivos"'+
                    '    href="/files/logistica/requerimiento/'+row.archivo+'"'+
                    '    target="_blank"'+
                    '    title="Descargar Archivo"'+
                    '>'+
                    '    <i class="fas fa-file-download"></i>'+
                    '</a>'+
                    '<button'+
                    '    class="btn btn-danger btn-sm "'+
                    '    name="btnEliminarAdjuntoRequerimiento"'+
                    '    onclick="eliminarArchivoAdjuntoRequerimiento(event,'+meta.row+','+row.id_adjunto+')"'+
                    '    title="Eliminar Archivo"'+
                    '>'+
                    '    <i class="fas fa-trash"></i>'+
                    '</button>'+
                    '</div>'
                    return btns;
                }
            },
        ],"columnDefs": [
            { "width": "60%", "targets": 0 },
            { "width": "30%", "targets": 1 },
            { "width": "10%", "targets": 2 }
          ]
    })
}
function llenarTablaAdjuntosRequerimientOrdenes(id_req){
    var vardataTables = funcDatatables();
    $('#tablaAdjuntoOrdenes').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        lengthChange: false,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        order:[[1,"desc"]],
        ajax: 'mostrar-archivos-adjuntos-requerimiento/'+id_req+'/'+2,
        columns: [

            {'render':
                function (data, type, row, meta){
                    return row.archivo;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.fecha_registro;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let btns = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                    '<a'+
                    '    class="btn btn-primary btn-sm "'+
                    '    name="btnAdjuntarArchivos"'+
                    '    href="/files/logistica/requerimiento/'+row.archivo+'"'+
                    '    target="_blank"'+
                    '    title="Descargar Archivo"'+
                    '>'+
                    '    <i class="fas fa-file-download"></i>'+
                    '</a>'+
                    '<button'+
                    '    class="btn btn-danger btn-sm "'+
                    '    name="btnEliminarAdjuntoRequerimiento"'+
                    '    onclick="eliminarArchivoAdjuntoRequerimiento(event,'+meta.row+','+row.id_adjunto+')"'+
                    '    title="Eliminar Archivo"'+
                    '>'+
                    '    <i class="fas fa-trash"></i>'+
                    '</button>'+
                    '</div>'
                    return btns;
                }
            },
        ],"columnDefs": [
            { "width": "60%", "targets": 0 },
            { "width": "30%", "targets": 1 },
            { "width": "10%", "targets": 2 }
          ]
    })
}
function llenarTablaAdjuntosRequerimientComprobanteBancario(id_req){
    var vardataTables = funcDatatables();
    $('#tablaAdjuntoComprobanteBancario').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        lengthChange: false,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        order:[[1,"desc"]],
        ajax: 'mostrar-archivos-adjuntos-requerimiento/'+id_req+'/'+3,
        columns: [

            {'render':
                function (data, type, row, meta){
                    return row.archivo;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.fecha_registro;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let btns = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                    '<a'+
                    '    class="btn btn-primary btn-sm "'+
                    '    name="btnAdjuntarArchivos"'+
                    '    href="/files/logistica/requerimiento/'+row.archivo+'"'+
                    '    target="_blank"'+
                    '    title="Descargar Archivo"'+
                    '>'+
                    '    <i class="fas fa-file-download"></i>'+
                    '</a>'+
                    '<button'+
                    '    class="btn btn-danger btn-sm "'+
                    '    name="btnEliminarAdjuntoRequerimiento"'+
                    '    onclick="eliminarArchivoAdjuntoRequerimiento(event,'+meta.row+','+row.id_adjunto+')"'+
                    '    title="Eliminar Archivo"'+
                    '>'+
                    '    <i class="fas fa-trash"></i>'+
                    '</button>'+
                    '</div>'
                    return btns;
                }
            },
        ],"columnDefs": [
            { "width": "60%", "targets": 0 },
            { "width": "30%", "targets": 1 },
            { "width": "10%", "targets": 2 }
          ]
    })
}
function llenarTablaAdjuntosRequerimientComprobanteContable(id_req){
    var vardataTables = funcDatatables();
    $('#tablaAdjuntoComprobanteContable').dataTable({
        bDestroy: true,
        info:     false,
        iDisplayLength:2,
        lengthChange: false,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        order:[[1,"desc"]],
        ajax: 'mostrar-archivos-adjuntos-requerimiento/'+id_req+'/'+4,
        columns: [

            {'render':
                function (data, type, row, meta){
                    return row.archivo;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.fecha_registro;
                }
            },
            {'render':
                function (data, type, row, meta){
                    let btns = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                    '<a'+
                    '    class="btn btn-primary btn-sm "'+
                    '    name="btnAdjuntarArchivos"'+
                    '    href="/files/logistica/requerimiento/'+row.archivo+'"'+
                    '    target="_blank"'+
                    '    title="Descargar Archivo"'+
                    '>'+
                    '    <i class="fas fa-file-download"></i>'+
                    '</a>'+
                    '<button'+
                    '    class="btn btn-danger btn-sm "'+
                    '    name="btnEliminarAdjuntoRequerimiento"'+
                    '    onclick="eliminarArchivoAdjuntoRequerimiento(event,'+meta.row+','+row.id_adjunto+')"'+
                    '    title="Eliminar Archivo"'+
                    '>'+
                    '    <i class="fas fa-trash"></i>'+
                    '</button>'+
                    '</div>'
                    return btns;
                }
            },
        ],"columnDefs": [
            { "width": "60%", "targets": 0 },
            { "width": "30%", "targets": 1 },
            { "width": "10%", "targets": 2 }
          ]
    })
}


function adjuntoRequerimientoModal(event){
    event.preventDefault();
    let id_req = document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;
    if(parseInt(id_req) >0){
            $('#modal-adjuntar-archivos-requerimiento').modal({
                show: true,
                backdrop: 'static'
            });
            // get_data_archivos_adjuntos(data_item[index].id_detalle_requerimiento);
            document.querySelector("div[id='group-adjunto-requerimiento'] span[class='buttonText']").textContent ="Seleccionar archivo"

        }else{ //no existe id_detalle_requerimiento => es un nuevo requerimiento
            alert("Primero debe guardar el requerimiento");
        }
}

function agregarAdjuntoRequerimiento(event){ //agregando nuevo archivo adjunto
   
    //  console.log(event.target.value);
    let id_req = document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;
    let categoria_adjunto_id = document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] select[name='categoria_adjunto']").value;
    if(parseInt(id_req)>0){
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
                    categoria_adjunto_id: categoria_adjunto_id?parseInt(categoria_adjunto_id):null,
                    archivo:file.name,
                    fecha_registro: new Date().toJSON().slice(0, 10),
                    estado: 1
                }
                let only_file = event.target.files[0]
                adjuntosRequerimiento.push(archivo);
                onlyAdjuntosRequerimiento.push(only_file);
                // console.log(adjuntosRequerimiento);
                // console.log(onlyAdjuntosRequerimiento);
                
        }
    }else{
        alert("es nuevo requerimiento.... debe guardar el requerimiento primero");

    }
    
}

function guardarAdjuntosRequerimiento(){

    let id_req =document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;
    if(id_req < 0){
        alert("hubo un problema, no se pudo guardar el adjunto")
        console.log("error 404: no se encontro el ID del requerimiento, guardarAdjuntosRequerimiento()");
    }
    
    // console.log(adjuntos);
    // console.log(only_adjuntos);
    let id_requerimiento = adjuntosRequerimiento[0].id_requerimiento;

        // const onlyNewAdjuntos = adjuntosRequerimiento.filter(id => id.id_adjunto == 0); // solo enviar los registros nuevos

        var myformData = new FormData();        
        // myformData.append('archivo_adjunto', JSON.stringify(adjuntosRequerimiento));
        for(let i=0;i<onlyAdjuntosRequerimiento.length;i++){
            myformData.append('only_adjuntos[]', onlyAdjuntosRequerimiento[i]);
            
        }
        
        myformData.append('detalle_adjuntos', JSON.stringify(adjuntosRequerimiento));
        myformData.append('id_requerimiento', id_requerimiento);
    
        baseUrl = 'guardar-archivos-adjuntos-requerimiento';
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
                    onlyAdjuntosRequerimiento=[];
                    // $('#listaArchivosAdjuntosRequerimiento').DataTable().ajax.reload();
                    llenarTablaAdjuntosRequerimiento();
                    adjuntosRequerimiento=[];
                    let ask = confirm('¿Desea seguir agregando más archivos ?');
                    if (ask == true){
                        return false;
                    }else{
                        adjuntosRequerimiento=[];

                        $('#modal-adjuntar-archivos-requerimiento').modal('hide');
                    }
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
}

function eliminarArchivoAdjuntoRequerimiento(event,indice,id_adjunto){
    event.preventDefault();

    if(id_adjunto >0){
        var ask = confirm('¿Desea eliminar este archivo ?');
        if (ask == true){
            $.ajax({
                type: 'PUT',
                url: 'eliminar-archivo-adjunto-requerimiento/'+id_adjunto,
                dataType: 'JSON',
                success: function(response){
                    if(response.status == 'ok'){
                        alert("Archivo Eliminado");
                        llenarTablaAdjuntosRequerimiento();
                        // $('#listaArchivosAdjuntosRequerimiento').DataTable().ajax.reload();
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