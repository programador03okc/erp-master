var data_establecimiento_list=[];

function limpiarInputEstablecimiento(){
    document.getElementsByName('tipo_establecimiento')[0].value="";
    document.getElementsByName('direccion_establecimiento')[0].value="";
}

function llenar_tabla_establecimientos(data){
    data_establecimiento_list = data;       
    limpiarTabla('ListaEstablecimientos');
    htmls ='<tr></tr>';
    $('#ListaEstablecimientos tbody').html(htmls);
    var table = document.getElementById("ListaEstablecimientos");
    for(var a=0;a < data.length;a++){   
        if(data[a].estado != 0){
            var row = table.insertRow(a+1);
            var tdId =  row.insertCell(0);
                tdId.setAttribute('class','hidden');
                tdId.innerHTML = data[a].id_establecimiento;
            row.insertCell(1).innerHTML = a+1;
            row.insertCell(2).innerHTML = data[a].tipo_establecimiento;
            row.insertCell(3).innerHTML = data[a].direccion;
            row.insertCell(4).innerHTML = 
                                        '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                                            '<button class="btn btn-secondary btn-sm  activation" name="btnEditarEstablecimiento" data-toggle="tooltip"'+
                                            'onclick="editarEstablecimiento(event,'+a+');"'+ 
                                            'data-original-title="Editar"><i class="fas fa-edit"></i>'+
                                            '</button>'+
                                            '<button class="btn btn-danger btn-sm  activation"'+
                                                'name="btnEliminarEstablecimiento"'+
                                                'data-toggle="tooltip"'+
                                                'title=""'+
                                                'onclick="eliminarEstablecimiento(event,'+a+');"'+
                                                'data-original-title="Eliminar"'+
                                            '>'+
                                                '<i class="fas fa-trash-alt"></i>'+
                                            '</button>'+
                                        '</div>';

        }
    }
    return null;
}

function AgregarEstablecimiento(event){
    event.preventDefault();
    limpiarInputEstablecimiento();
    document.getElementById('modal-gestionar-establecimiento-title').innerText ='Agregar Establecimiento';
    $('#modal-gestionar-establecimiento').modal({
        show: true,
        backdrop: 'static',
    });
    

    const div = document.getElementById('btnAction_establecimiento');
    while(div.firstChild) {
        div.removeChild(div.firstChild);
    }
    var button = document.createElement('button');
    button.innerHTML = 'Agregar';
    button.setAttribute('class','btn btn-sm btn-success')
    button.addEventListener('click', function(){
        // btnActionEstablecimiento('ADD')
        var id_prov = $('[name=id_proveedor]').val();
        let tipo = document.getElementsByName('tipo_establecimiento')[0];
        let id_tipo_establecimiento = document.getElementsByName('tipo_establecimiento')[0].value;
        let tipo_establecimiento = tipo.options[tipo.selectedIndex].text;
        let direccion = document.getElementsByName('direccion_establecimiento')[0].value;
        let data_establecimiento = {'id_proveedor':id_prov,'id_establecimiento':0,'id_tipo_establecimiento':id_tipo_establecimiento,'tipo_establecimiento':tipo_establecimiento,'direccion':direccion,'estado':2};
        data_establecimiento_list.push(data_establecimiento);
        llenar_tabla_establecimientos(data_establecimiento_list);
        
        setTextInfoAnimation('Agregado!');

    });
    div.appendChild(button)
}

function editarEstablecimiento(event,id){        
    event.preventDefault();
    document.getElementById('modal-gestionar-establecimiento-title').innerText ='Editar Establecimiento';
    $('#modal-gestionar-establecimiento').modal({
        show: true,
        backdrop: 'static',
    })
    // console.log(data_establecimiento_list);
    
    document.getElementsByName('tipo_establecimiento')[0].value=data_establecimiento_list[id].id_tipo_establecimiento;
    document.getElementsByName('direccion_establecimiento')[0].value=data_establecimiento_list[id].direccion;

    const div = document.getElementById('btnAction_establecimiento');
    while(div.firstChild) {
        div.removeChild(div.firstChild);
    }
    var button = document.createElement('button');
    button.innerHTML = 'Actualizar';
    button.setAttribute('class','btn btn-sm btn-primary')
    button.addEventListener('click', function(){
        // btnActionEstablecimiento('UPDATE')
        let tipo = document.getElementsByName('tipo_establecimiento')[0];
        let tipo_establecimiento = tipo.options[tipo.selectedIndex].text;
        let direccion = document.getElementsByName('direccion_establecimiento')[0].value;
        data_establecimiento_list[id].id_tipo_establecimiento=tipo.value;
        data_establecimiento_list[id].tipo_establecimiento=tipo_establecimiento;
        data_establecimiento_list[id].direccion=direccion;
        if(data_establecimiento_list[id].estado !=2){
            data_establecimiento_list[id].estado=3; //editado
        }
        setTextInfoAnimation('Editado!');
        // let data_establecimiento = {'id_establecimiento':0,'tipo':tipo,'direccion':direccion,'estado':1};
        llenar_tabla_establecimientos(data_establecimiento_list);

    });
    div.appendChild(button)

}

function eliminarEstablecimiento(event,id){        
    event.preventDefault();
    if(data_establecimiento_list[id].id_establecimiento == '0'){
        // data_establecimiento_list.splice(data_establecimiento_list.findIndex(e => (e.estado == 0 && data_establecimiento_list[id].id_establecimiento =='0')),1);
        // data_establecimiento_list.filter(word => (word.id_establecimiento >0 && word.estado == 0 ));

    }else{
        data_establecimiento_list[id].estado=0;
        delete_establecimiento_list.push(data_establecimiento_list[id]);
    }
    // console.log(data_establecimiento_list);
    
    llenar_tabla_establecimientos(data_establecimiento_list);
}

function save_form_establecimiento(){
    let id_prov = $('[name=id_proveedor]').val();
    let nuevos_estab_list = data_establecimiento_list.filter(word => (word.id_establecimiento < 1 )  ); // nuevos
        let editados_eliminados_estab_list = data_establecimiento_list.filter(e => (e.id_establecimiento > 0 && e.estado != 1 )  ); // editados o eliminados
        
        for (var i in editados_eliminados_estab_list) {
            if (editados_eliminados_estab_list[i].estado == 3) {
                editados_eliminados_estab_list[i].estado = 1;
            }
        }

        if(nuevos_estab_list.length >0){
            baseUrl = 'registrar_establecimiento';
            data = nuevos_estab_list;
            method= 'POST';
            msj='Establecimiento(s) agregados';
            executeRequest(id_prov,baseUrl,method,data,msj);
            
        }
        if(editados_eliminados_estab_list.length >0){
            baseUrl = 'update_establecimiento';
            data = editados_eliminados_estab_list;
            method= 'PUT';
            msj='Establecimiento(s) actualizados';
            executeRequest(id_prov,baseUrl,method,data,msj);
        }
}