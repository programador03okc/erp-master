var activeForm = '';
$(function(){
     // tabs
    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').hide();
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');
        var activeTab = $(this).attr('type');
        activeForm = "form-"+activeTab.substring(1);
        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);
        changeStateButton('inicio');
        $(activeTab).show();
        resizeSide();

        var id = $('[name=id_proveedor]').val();
    if (activeForm == 'form-contribuyente' && id != ''){
        changeStateButton('guardar');
        // changeStateInput(activeForm, true);
    }else if (activeForm == 'form-establecimientos' && id != ''){
        changeStateButton('guardar');
        // changeStateInput(activeForm, true);
    }else if (activeForm == 'form-cuentas_bancarias' && id != ''){
        changeStateButton('guardar');
        // changeStateInput(activeForm, true);
    }else if (activeForm == 'form-contactos'  && id != ''){
        changeStateButton('guardar');
        // changeStateInput(activeForm, true);
    }
    else if (activeForm == 'form-adjuntos'  && id != ''){
        changeStateButton('guardar');
        // changeStateInput(activeForm, true);
    }
    });
});


function mayus(e) {
    e.value = e.value.toUpperCase();
}


function ModalListaProveedores(){
    $('#modal-lista-proveedores').modal({
        show: true,
        backdrop: 'static',
    })
}

function nuevo(form){
    if(form == 'form-contribuyente'){
        actionNuevoContri();
    }    
   
}

function actualizarForm(myId){
    
    $('[name=id_proveedor]').val(myId); 
    var scrollingElement = (document.scrollingElement || document.body);
    scrollingElement.scrollTop = 120;
    baseUrl = '/mostrar_proveedor/'+myId;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(activeForm);
            if(response.proveedor.length >0){
                llenar_tab_proveedor(response.proveedor[0]);
                changeStateInput('form-contribuyente', true);
            }else{
                alert("no existe datos de proveedor");
            } 
            if(response.establecimientos.length >0){
                llenar_tabla_establecimientos(response.establecimientos);
                changeStateInput('form-establecimientos', true);
            }else{
                // alert("no existe datos de establecimientos");
            }
            if(response.cuentas_bancarias.length >0){
                llenar_tabla_cuentas_bancarias(response.cuentas_bancarias);
                changeStateInput('form-cuentas_bancarias', true);
            }else{
                // alert("no existe datos de cuentas bancarias");
            } 
            if(response.contactos.length >0){
                llenar_tabla_contactos(response.contactos);
                changeStateInput('form-contactos', true);
                var id_prov = $('[name=id_proveedor]').val();
                llenar_establecimiento_contacto(id_prov); //llenar select
            }else{
                // alert("no existe datos de contactos");
            } 
            if(response.archivos.length >0){
                llenar_tabla_adjuntos(response.archivos);
                changeStateInput('form-adjuntos', true);
            }else{
                // alert("no existe datos de archivos");
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}



    function save_form(data, action, frm_active){
        var id_prov = $('[name=id_proveedor]').val();
        data = data + '&id_proveedor=' + id_prov;
        let = baseUrl='';
        let = msj='';
        let = method = action =='register'?'POST':action=='edition'?'PUT':'undefined';
         // console.log(nuevos_estab_list);
        // console.log(editados_eliminados_estab_list);
       
        if(frm_active == 'form-contribuyente'){           
            save_form_proveedor(action);
        } else if(frm_active == 'form-establecimientos'  && id_prov != ''){
            save_form_establecimiento();
        } else if(frm_active == 'form-cuentas_bancarias' && id_prov != ''){
            save_form_cuentas();
        }else if(frm_active == 'form-contactos' && id_prov != ''){
            save_form_contactos();
        }else if(frm_active == 'form-adjuntos' && id_prov != ''){
            save_form_archivos_proveedor();
        }
        
    }

    function executeRequest(id_prov,baseUrl,method,data,msj){
        if(data.length >0){
            $.ajax({
                type: method,
                url: baseUrl,
                data: {data},
                dataType:"json", 
                success: function(response){
                    // console.log(response);
                    if(response >0){
                        alert(msj);
                        actualizarForm(id_prov?id_prov:response);
                        changeStateButton('guardar');
                        $('#form-establecimientos').attr('type', 'register');                        
                        changeStateInput('form-establecimientos', true);
                    }else{
                        alert("ERROR, NO SE PUEDO GUARDAR");
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert("no hay data");
        }
    }
    function executeRequestFormData(id_prov,baseUrl,method,data,msj){
        // console.log(id_prov ,data);
        if(id_prov > 0){
            $.ajax({
                type: method,
                url: baseUrl,
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',
                success: function(response){
                    // console.log(response);
                    if(response.status_file ==true && response.status_register ==true ){
                        alert(msj);
                    }else{
                        alert("ERROR, NO SE PUEDO SUBIR EL ARCHIVO");
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert("no hay data");
        }
    }

