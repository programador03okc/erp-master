var data_contacto_list=[];

function limpiarInputContactos(){
    document.getElementsByName('nombre')[0].value="";
    document.getElementsByName('telefono_contacto')[0].value="";
    document.getElementsByName('email')[0].value="";
    document.getElementsByName('cargo')[0].value="";
}


function llenar_establecimiento_contacto(id_proveedor){
    data_item = [];
    baseUrl = '/contacto_establecimiento/'+id_proveedor;
    let html ='';
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
                    if(response.length > 0){
                        response.forEach(element => {
                            html +='<option value="'+element.id_establecimiento+'">'+element.direccion+' ['+element.descripcion_tipo_establcimiento +']</option>';
                        });
                    }


                let div = document.getElementsByName('establecimiento_contacto')[0];
                div.innerHTML = html;

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function llenar_tabla_contactos(data){
    data_contacto_list=data;
    limpiarTabla('ListaContactos');
    htmls ='<tr></tr>';
    $('#ListaContactos tbody').html(htmls);
    var table = document.getElementById("ListaContactos");
    for(var a=0;a < data.length;a++){
        if(data[a].estado != 0){
            var row = table.insertRow(a+1);
            var tdIdcuentaContri =  row.insertCell(0);
                tdIdcuentaContri.setAttribute('class','hidden');
                tdIdcuentaContri.innerHTML = data[a].id_datos_contacto;
            row.insertCell(1).innerHTML = a+1;
            row.insertCell(2).innerHTML = data[a].nombre;
            row.insertCell(3).innerHTML = data[a].telefono;
            row.insertCell(4).innerHTML = data[a].email;
            row.insertCell(5).innerHTML = data[a].cargo;
            row.insertCell(6).innerHTML = data[a].establecimiento_direccion;
            row.insertCell(7).innerHTML = 
                                        '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                                            '<button class="btn btn-secondary btn-sm  activation" name="btnEditarContacto"'+
                                            'data-toggle="tooltip" onclick="editarContacto(event,'+a+');"'+ 
                                            'data-original-title="Editar"><i class="fas fa-edit"></i>'+
                                            '</button>'+
                                            '<button class="btn btn-danger btn-sm  activation"'+
                                                'name="btnEliminarContacto"'+
                                                'data-toggle="tooltip"'+
                                                'title=""'+
                                                'onclick="eliminarContacto(event,'+a+');"'+
                                                'data-original-title="Eliminar"'+
                                            '>'+
                                                '<i class="fas fa-trash-alt"></i>'+
                                            '</button>'+
                                        '</div>';

        }
    }
    return null;
}

function AgregarContacto(event){
    event.preventDefault();
    // limpiarInputContactos();
    document.getElementById('modal-gestionar-contacto-title').innerText ='Agregar Contacto';
    $('#modal-gestionar-contacto').modal({
        show: true,
        backdrop: 'static',
    });
    

    const div = document.getElementById('btnAction_contactos');
    while(div.firstChild) {
        div.removeChild(div.firstChild);
    }
    var button = document.createElement('button');
    button.innerHTML = 'Agregar';
    button.setAttribute('class','btn btn-sm btn-success')
    button.addEventListener('click', function(){
        var id_prov = $('[name=id_proveedor]').val();
        // let bancos = document.getElementsByName('banco')[0];
        // let id_banco = bancos.value;
        // let nombre_banco = bancos.options[bancos.selectedIndex].text;
 
        let nombre = document.getElementsByName('nombre')[0].value;
        let telefono = document.getElementsByName('telefono_contacto')[0].value;
        let email = document.getElementsByName('email')[0].value;
        let cargo = document.getElementsByName('cargo')[0].value;
 
        let data_contacto = {
            'id_datos_contacto':0,
            'id_proveedor':id_prov?id_prov:0,
            'nombre':nombre,
            'telefono':telefono,
            'email':email,
            'cargo':cargo,
            'estado':2
        };
        
        data_contacto_list.push(data_contacto);
        llenar_tabla_contactos(data_contacto_list);
        
        setTextInfoAnimation('Agregado!');

    });
    div.appendChild(button)
}

function editarContacto(event, id){   
    event.preventDefault();
    
    document.getElementById('modal-gestionar-contacto-title').innerText ='Editar Contacto';
    $('#modal-gestionar-contacto').modal({
        show: true,
        backdrop: 'static',
    })
    document.getElementsByName('nombre')[0].value=data_contacto_list[id].nombre;
    document.getElementsByName('telefono_contacto')[0].value=data_contacto_list[id].telefono;
    document.getElementsByName('email')[0].value=data_contacto_list[id].email;
    document.getElementsByName('cargo')[0].value=data_contacto_list[id].cargo;
    document.getElementsByName('establecimiento_contacto')[0].value=data_contacto_list[id].id_establecimiento;

    const div = document.getElementById('btnAction_contactos');
    while(div.firstChild) {
        div.removeChild(div.firstChild);
    }
    var button = document.createElement('button');
    button.innerHTML = 'Actualizar';
    button.setAttribute('class','btn btn-sm btn-primary')
    button.addEventListener('click', function(){
        var id_prov = $('[name=id_proveedor]').val();
        // let bancos = document.getElementsByName('banco')[0];
        // let id_banco = bancos.value;
        // let nombre_banco = bancos.options[bancos.selectedIndex].text;
        
        
        let nombre = document.getElementsByName('nombre')[0].value;
        let telefono = document.getElementsByName('telefono_contacto')[0].value;
        let email = document.getElementsByName('email')[0].value;
        let cargo = document.getElementsByName('cargo')[0].value;

        let establecimiento = document.getElementsByName('establecimiento_contacto')[0];
        let id_establecimiento = establecimiento.value;
        let establecimiento_direccion = establecimiento.options[establecimiento.selectedIndex].text;

        data_contacto_list[id].id_proveedor=id_prov?id_prov:0;
        data_contacto_list[id].nombre=nombre;
        data_contacto_list[id].telefono=telefono;
        data_contacto_list[id].email=email;
        data_contacto_list[id].cargo=cargo;
        data_contacto_list[id].id_establecimiento=id_establecimiento;
        data_contacto_list[id].establecimiento_direccion=establecimiento_direccion;

        if(data_contacto_list[id].estado !=2){
            data_contacto_list[id].estado=3; //editado
        }
        setTextInfoAnimation('Editado!');
        llenar_tabla_contactos(data_contacto_list);

    });
    div.appendChild(button)

}

function eliminarContacto(event,id){        
    event.preventDefault();
    if(data_contacto_list[id].id_datos_contacto == '0'){
    }else{
        data_contacto_list[id].estado=0;
    }
    llenar_tabla_contactos(data_contacto_list);

}



function save_form_contactos(){
    let id_prov = $('[name=id_proveedor]').val();
    let nuevos_list = data_contacto_list.filter(word => (word.id_datos_contacto < 1 )  ); // nuevos
        let editados_eliminados_list = data_contacto_list.filter(e => (e.id_datos_contacto > 0 && e.estado != 1 )  ); // editados o eliminados
        
        for (var i in editados_eliminados_list) {
            if (editados_eliminados_list[i].estado == 3) {
                editados_eliminados_list[i].estado = 1;
            }
        }

        if(nuevos_list.length >0){
            baseUrl = 'registrar_contacto';
            data = nuevos_list;
            method= 'POST';
            msj='Contacto(s) agregadas';
            executeRequest(id_prov,baseUrl,method,data,msj);
            
        }
        if(editados_eliminados_list.length >0){
            baseUrl = 'update_contacto';
            data = editados_eliminados_list;
            method= 'PUT';
            msj='Contacto(s) actualizado';
            executeRequest(id_prov,baseUrl,method,data,msj);
        }
}