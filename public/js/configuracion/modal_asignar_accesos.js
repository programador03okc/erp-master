var acccesoUsuario=[];

function accesoUsuario(id){
    $('#modal-asignar-accesos').modal({
        show: true,
        backdrop: 'static'
    });

    listarRolesDeUsuarioEnSesion(id);
}

function limpiarSelectRolesUsuario(){
    let selectRolesUsuario = document.querySelector("div[id='modal-asignar-accesos'] select[name='roles_usuario']");
    if(selectRolesUsuario !=null){
        while (selectRolesUsuario.options.length > 0) {                
            selectRolesUsuario.remove(0);
        }    
    }
}

function listarRolesDeUsuarioEnSesion(id){
    $.ajax({
        type: 'GET',
        url: 'lista-roles-usuario/'+id,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                limpiarSelectRolesUsuario();
                let selectRolesUsuario = document.querySelector("div[id='modal-asignar-accesos'] select[name='roles_usuario']");
                response.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.descripcion;
                    option.value = element.id_rol;
                    selectRolesUsuario.add(option);
                });
                buildArbolSistema();

            }

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function buildArbolSistema(){
    let roleUsuario = document.querySelector("div[id='modal-asignar-accesos'] select[name='roles_usuario']").value;
    // console.log(roleUsuario);
    $.ajax({
        type: 'GET',
        url: 'arbol-acceso/'+roleUsuario,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            let li='';
            let tabModulo='';
            let htmlSubModulo='';
            let subModuloArray=[];
            response.forEach((element,index) => {
                if(element.sub_modulo != undefined){
                    element.sub_modulo.forEach(sm => {
                        htmlSubModulo = '<div class="col-md-12" name="contenedor-submodulo">'+
                            '<div class="checkbox">'+
                            '<label style="font-weight:bold;">'+
                                '<input type="checkbox" name="checkSubModulo" onClick="checkSubModulo(this);" data-id-padre="'+sm.id_padre+'" data-id-sub-modulo="'+sm.id_sub_modulo+'">'+
                                sm.descripcion+
                            '</label>';
                            let sub_modulo_hijo='';
                            if(sm.sub_modulo_hijo != undefined){
                                sm.sub_modulo_hijo.forEach(smh => {
                                    sub_modulo_hijo+='<div name="contenedor-submodulohijo">';

                                    sub_modulo_hijo+=
                                        '<label style="display:block; margin-left:21px">'+
                                        '<input type="checkbox"  name="checkSubModuloHijo" onClick="checkSubModuloHijo(this);" data-id-padre="'+smh.id_padre+'" data-id-modulo="'+smh.id_modulo+'" >'+
                                        smh.modulo+
                                        '</label>';

                                    if(smh.aplicacion != undefined){
                                        smh.aplicacion.forEach(a => {
                                            sub_modulo_hijo+='<div name="contenedor-aplicacion">';

                                            sub_modulo_hijo+=
                                            '<label style="display:block; margin-left:41px">'+
                                            '<input type="checkbox" name="checkAplicacion" onClick="checkAplicacion(this);" data-id-sub-modulo="'+a.id_sub_modulo+'" data-id-aplicacion="'+a.id_aplicacion+'" >'+
                                            a.descripcion+
                                            '</label>';

                                            if(a.accion != undefined){
                                                a.accion.forEach(a => {
                                                    sub_modulo_hijo+='<div name="contenedor-accion">';

                                                    if(a.permiso == true){
                                                        sub_modulo_hijo+=
                                                        '<label style="display:block; margin-left:81px;">'+
                                                        '<input type="checkbox" name="checkAccion"  onClick="checkAccion(this);" data-id-aplicacion="'+a.id_aplicacion+'" data-id-accion="'+a.id_accion+'" checked>'+
                                                        '<span style="color:#337ab7;">'+a.descripcion+'</span>'+    
                                                        '</label>';
                                                    }else{
                                                        sub_modulo_hijo+=
                                                        '<label style="display:block; margin-left:81px;">'+
                                                        '<input type="checkbox" name="checkAccion" onClick="checkAccion(this);" data-id-aplicacion="'+a.id_aplicacion+'" data-id-accion="'+a.id_accion+'">'+
                                                        '<span style="color:#337ab7;">'+a.descripcion+'</span>'+    
                                                        '</label>';
                                                    }
                                                    sub_modulo_hijo+='</div>';

                                                });
                                            }
                                            sub_modulo_hijo+='</div>';

                                        });
                                    }
                                    sub_modulo_hijo+='</div>';
                                });
                            }
                        htmlSubModulo+= sub_modulo_hijo
                        htmlSubModulo += '</div>'+
                        '</div>'
                        
                            subModuloArray.push( {
                                'id_padre':sm.id_padre,
                                'html':htmlSubModulo
                                });
                        
                    });
                }
                if(index ==0){
                    li +=`<li role="presentation" class="active"><a href="#${element.id_modulo}" aria-controls="${element.modulo}" role="tab" data-toggle="tab">${element.modulo}</a></li>`;
                    tabModulo=`
                        <div role="tabpanel" class="tab-pane active" id="${element.id_modulo}">
                        <div class="panel panel-default">
                            <div class="panel-body" style="overflow: scroll; height: 35vh;">
                                <div class="row" name="panel_body">
                                
                                </div>
                            </div>
                        </div>
                </div>`;
                }else{
                    li +=`<li role="presentation" class=""><a href="#${element.id_modulo}" onClick="vista_extendida();" aria-controls="${element.modulo}" role="tab" data-toggle="tab">${element.modulo}</a></li>`;
                    tabModulo+=`
                        <div role="tabpanel" class="tab-pane" id="${element.id_modulo}">
                        <div class="panel panel-default">
                            <div class="panel-body" style="overflow: scroll; height: 35vh;">
                                <div class="row" name="panel_body">
                                </div>
                            </div>
                        </div>
                    </div>`;
                    }
            });
            
            document.querySelector("ul[id='tab_modulos']").innerHTML=li;
            document.querySelector("div[id='tabpanel_modulos']").innerHTML=tabModulo;
            // console.log(subModuloArray);
            let tabpanel_modulos_children_length= document.querySelector("div[id='tabpanel_modulos']").children.length;
            let tabpanel_modulos_children= document.querySelector("div[id='tabpanel_modulos']").children;
            for (let index = 0; index < tabpanel_modulos_children_length; index++) {
                const id_tab_panel = tabpanel_modulos_children[index].getAttribute('id');
                subModuloArray.forEach(sma => {
                    if(sma.id_padre == id_tab_panel){
                        tabpanel_modulos_children[index].querySelector("div[name='panel_body']").innerHTML+=sma.html;
                    }
                });
            }

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


function checkSubModulo(obj){
    let value = obj.checked;
    let idPadre = obj.dataset.idPadre;
    let idSubModulo = obj.dataset.idSubModulo;
    console.log('checkSubModulo',idPadre,idSubModulo);
    let parent = obj.parentNode.parentNode;
    parent.querySelectorAll("div[name='contenedor-submodulohijo']").forEach(element => {
        if(value==true){
            element.querySelector("input[name='checkSubModuloHijo']").setAttribute('checked',true);
            parent.querySelectorAll("div[name='contenedor-submodulohijo'] div[name='contenedor-aplicacion']").forEach(element => {
                element.querySelector("input[name='checkAplicacion']").setAttribute('checked',true);
                parent.querySelectorAll("div[name='contenedor-aplicacion'] div[name='contenedor-accion']").forEach(element => {
                    element.querySelector("input[name='checkAccion']").setAttribute('checked',true);
                    updateObjAccesoUsuario(element.querySelector("input[name='checkAccion']").dataset.idAccion, value);
                    
                });
            });

        }else{
            element.querySelector("input[name='checkSubModuloHijo']").removeAttribute('checked',true);
            parent.querySelectorAll("div[name='contenedor-submodulohijo'] div[name='contenedor-aplicacion']").forEach(element => {
                element.querySelector("input[name='checkAplicacion']").removeAttribute('checked');
                parent.querySelectorAll("div[name='contenedor-aplicacion'] div[name='contenedor-accion']").forEach(element => {
                    element.querySelector("input[name='checkAccion']").removeAttribute('checked');
                    updateObjAccesoUsuario(element.querySelector("input[name='checkAccion']").dataset.idAccion, value);
                    
                });
                });
        }
    
    });

}

function checkSubModuloHijo(obj){
let value = obj.checked;
let idPadre = obj.dataset.idPadre;
let idModulo = obj.dataset.idModulo;
console.log('checkSubModuloHijo',idPadre,idModulo);
let parent= obj.parentNode.parentNode;
parent.querySelectorAll("div[name='contenedor-aplicacion']").forEach(element => {
    if(value==true){
        element.querySelector("input[name='checkAplicacion']").setAttribute('checked',true);
        parent.querySelectorAll("div[name='contenedor-aplicacion'] div[name='contenedor-accion']").forEach(element => {
            element.querySelector("input[name='checkAccion']").setAttribute('checked',true);
            updateObjAccesoUsuario(element.querySelector("input[name='checkAccion']").dataset.idAccion, value);
            
        });
    }else{
        element.querySelector("input[name='checkAplicacion']").removeAttribute('checked');
        parent.querySelectorAll("div[name='contenedor-aplicacion'] div[name='contenedor-accion']").forEach(element => {
            element.querySelector("input[name='checkAccion']").removeAttribute('checked');
            updateObjAccesoUsuario(element.querySelector("input[name='checkAccion']").dataset.idAccion, value);
            
        });
    }
});

}
function checkAplicacion(obj){
let value = obj.checked;
let idAplicacion = obj.dataset.idAplicacion;
let idSubModulo = obj.dataset.idSubModulo;
console.log(idAplicacion,idSubModulo);
let parent= obj.parentNode.parentNode;
parent.querySelectorAll("div[name='contenedor-accion']").forEach(element => {
    if(value==true){
        element.querySelector("input[name='checkAccion']").setAttribute('checked',true);
        updateObjAccesoUsuario(element.querySelector("input[name='checkAccion']").dataset.idAccion, value);
    }else{
        element.querySelector("input[name='checkAccion']").removeAttribute('checked');
        updateObjAccesoUsuario(element.querySelector("input[name='checkAccion']").dataset.idAccion, value);

    }
});
}
function checkAccion(obj){
let value = obj.checked;
let idAplicacion = obj.dataset.idAplicacion;
let idAccion = obj.dataset.idAccion;
console.log(idAplicacion,idAccion);
updateObjAccesoUsuario(idAccion,value);
}

function actualizarAccesoUsuario(){
    let data={
        'id_rol':document.querySelector("div[id='modal-asignar-accesos'] select[name='roles_usuario']").value, 
        'accesos':acccesoUsuario
    };
    // console.log(data);

    $.ajax({
        type: 'PUT',
        url: 'actualizar-accesos-usuario',
        data: data,
        datatype: "JSON",
        success: function(response){
            // console.log(response);
            if (response.status ==200){
                alert('Acceso actualizado con éxito');
            }else{
                alert('Hubo un problema al actualizar');
            }
            
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}