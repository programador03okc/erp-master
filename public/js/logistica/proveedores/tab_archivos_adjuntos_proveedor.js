var data_archivo_adjunto_list=[];
var data_only_archivo_adjunto_list=[];

// function limpiarInputArchivoAdjuntoProveedor(){
//     document.getElementsByName('archivo-adjunto-proveedor')[0].value="";
// }

function llenar_tabla_adjuntos(data){
    data_archivo_adjunto_list=data;
    limpiarTabla('ListaArchivoAdjuntosProveedor');
    htmls ='<tr></tr>';
    $('#ListaArchivoAdjuntosProveedor tbody').html(htmls);
    var table = document.getElementById("ListaArchivoAdjuntosProveedor");
    for(var a=0;a < data.length;a++){
        if(data[a].estado != 0){   
            var row = table.insertRow(a+1);
            var tdId =  row.insertCell(0);
                tdId.setAttribute('class','hidden');
                tdId.innerHTML = data[a].id_archivo;
            row.insertCell(1).innerHTML = a+1;
            row.insertCell(2).innerHTML = data[a].archivo;
            row.insertCell(3).innerHTML = 
                                        '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                                            '<button class="btn btn-secondary btn-sm "'+
                                            'name="btnDescargarAdjuntoProveedor" data-toggle="tooltip"'+
                                            'onclick="descargarAdjuntoProveedor(event,'+a+');" data-original-title="Editar"><i class="fas fa-file-download"></i>'+
                                            '</button>'+
                                            '<button class="btn btn-danger btn-sm  activation"'+
                                                'name="btnEliminarAdjunto"'+
                                                'data-toggle="tooltip"'+
                                                'title=""'+
                                                'onclick="eliminarArchivoProveedor(event,'+a+');"'+
                                                'data-original-title="Eliminar"'+
                                            '>'+
                                                '<i class="fas fa-trash-alt"></i>'+
                                            '</button>'+
                                        '</div>';

        }
    }
    return null;
}

function agregarAdjuntoProveedor(event){
    event.preventDefault();
    
    $('#modal-gestionar-archivo-adjunto-proveedor').modal({
        show: true,
        backdrop: 'static',
    });
    document.getElementById('modal-gestionar-archivo-adjunto-proveedor-title').innerText ='Agregar Archivo';
    // limpiarInputArchivoAdjuntoProveedor();
    
    const div = document.getElementById('btnAction_archivo_adjunto');
    while(div.firstChild) {
        div.removeChild(div.firstChild);
    }
    var button = document.createElement('button');
    button.innerHTML = 'Agregar';
    button.setAttribute('class','btn btn-sm btn-success')
    button.addEventListener('click', function(){
        var id_prov = $('[name=id_proveedor]').val();
        let data_archivo_adjunto = {
            'id_proveedor':id_prov,
            'id_archivo':0,
            'archivo':event.target.files[0].name,
            'fecha_registro':new Date().toJSON().slice(0, 10),
            'estado':2
        };
        let only_file = event.target.files[0]

        data_archivo_adjunto_list.push(data_archivo_adjunto);
        data_only_archivo_adjunto_list.push(only_file);

        // console.log(data_archivo_adjunto_list);
        // console.log(data_only_archivo_adjunto_list);
        llenar_tabla_adjuntos(data_archivo_adjunto_list);
        setTextInfoAnimation('Agregado!');

    });
    div.appendChild(button)
}

function descargarAdjuntoProveedor(event,position){
    event.preventDefault();
    let name_file= data_archivo_adjunto_list[position].archivo;
    let url='/files/logistica/proveedores/'+name_file;
    window.open(url);
}

function eliminarArchivoProveedor(event,position){
    event.preventDefault();
    if(data_archivo_adjunto_list[position].id_archivo == '0'){
    }else{
        data_archivo_adjunto_list[position].estado=0;
    }
    llenar_tabla_adjuntos(data_archivo_adjunto_list);

}

function save_form_archivos_proveedor(){
    
    let myformDataF = new FormData();        
    let id_prov = $('[name=id_proveedor]').val();
    let nuevos_list = data_archivo_adjunto_list.filter(word => (word.id_archivo < 1 )  ); // nuevos
    let editados_eliminados_list = data_archivo_adjunto_list.filter(e => (e.id_archivo > 0 && e.estado != 1 )  ); // editados o eliminados
    
    for (var i in editados_eliminados_list) {
        if (editados_eliminados_list[i].estado == 3) {
            editados_eliminados_list[i].estado = 1;
        }
    }
        
        if(data_only_archivo_adjunto_list.length > 0 && nuevos_list.length >0){
            
            baseUrl = 'registrar_adjunto_proveedor';
            for(let i=0;i<data_only_archivo_adjunto_list.length;i++){
                myformDataF.append('only_adjuntos[]', data_only_archivo_adjunto_list[i]);
            }
            myformDataF.append('detalle_adjuntos', JSON.stringify(nuevos_list));
            myformDataF.append('id_proveedor', id_prov);
            method= 'POST';
            msj='Archivo(s) registrado';
            
            executeRequestFormData(id_prov,baseUrl,method,myformDataF,msj);

        }
        // if(nuevos_list.length >0){
        //     baseUrl = 'registrar_adjunto_proveedor';
        //     data = nuevos_list;
        //     method= 'POST';
        //     msj='Archivo(s) agregadas';
        //     executeRequest(id_prov,baseUrl,method,data,msj);
            
        // }
        if(editados_eliminados_list.length >0){
            baseUrl = 'update_adjunto_proveedor';
            data = editados_eliminados_list;
            method= 'PUT';
            msj='Adjunto(s) actualizado';
            let res =executeRequest(id_prov,baseUrl,method,data,msj);
            console.log(res);
            
        }
}