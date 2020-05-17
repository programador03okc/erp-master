// var data_contribuyente='';

function llenar_tab_proveedor(data){
    document.getElementsByName('id_proveedor')[0].value = data.id_proveedor;
    document.getElementsByName('nro_documento')[0].value = data.nro_documento;
    document.getElementsByName('razon_social')[0].value = data.razon_social;
    document.getElementsByName('estado_ruc')[0].value= data.id_estado_ruc;
    document.getElementsByName('condicion_ruc')[0].value = data.id_condicion_ruc ;
    document.getElementsByName('tipo_empresa')[0].value= data.id_tipo_contribuyente ;
    document.getElementsByName('telefono')[0].value = data.telefono;
    document.getElementsByName('direccion')[0].value = data.direccion_fiscal;
    document.getElementsByName('ubigeo')[0].value= data.ubigeo;
    document.getElementsByName('paises')[0].value= data.descripcion_pais;
}




function actionNuevoContri(){
     // document.getElementsByName('nro_documento')[0].value = '';
    // document.getElementsByName('razon_social')[0].value = '';
    // document.getElementsByName('direccion')[0].value = '';
    // document.getElementsByName('tipo_empresa')[0].selectedIndex = 0;
    // document.getElementsByName('ubigeo')[0].value = '';

    // default select pais
    var temp = "170"; //PERU
    var mySelect = document.getElementsByName('paises')[0];

    for(var i, j = 0; i = mySelect.options[j]; j++) {
        if(i.value == temp) {
            mySelect.selectedIndex = j;
            break;
        }
    }
    // default select estado_ruc
    var temp = "1"; //ACTIVO
    var mySelect = document.getElementsByName('estado_ruc')[0];

    for(var i, j = 0; i = mySelect.options[j]; j++) {
        if(i.value == temp) {
            mySelect.selectedIndex = j;
            break;
        }
    }
    // default select estado_ruc
    var temp = "1"; //habido
    var mySelect = document.getElementsByName('condicion_ruc')[0];

    for(var i, j = 0; i = mySelect.options[j]; j++) {
        if(i.value == temp) {
            mySelect.selectedIndex = j;
            break;
        }
    }
}

function eliminarProveedor(event,id){        
    // event.preventDefault();
    // if(data_contacto_list[id].id_datos_contacto == '0'){
    // }else{
    //     data_contacto_list[id].estado=0;
    // }
    // llenar_tabla_contactos(data_contacto_list);

}

function save_form_proveedor(action){
    console.log(action);
    
    let id_prov = $('[name=id_proveedor]').val();

    let data =[{
        'id_proveedor' : document.getElementsByName('id_proveedor')[0].value,
        'nro_documento' : document.getElementsByName('nro_documento')[0].value,
        'razon_social' : document.getElementsByName('razon_social')[0].value,
        'id_doc_identidad' : 2,
        'id_estado_ruc' : document.getElementsByName('estado_ruc')[0].value,
        'id_condicion_ruc' : document.getElementsByName('condicion_ruc')[0].value,
        'id_tipo_contribuyente' : document.getElementsByName('tipo_empresa')[0].value,
        'telefono' : document.getElementsByName('telefono')[0].value,
        'direccion_fiscal' : document.getElementsByName('direccion')[0].value,
        'ubigeo' : document.getElementsByName('ubigeo')[0].value,
        'id_pais' : document.getElementsByName('paises')[0].value,
        'estado' : 1
    }];


        if(action=='register' && data.length >0){
            baseUrl = 'registrar_proveedor';
            data = data;
            method= 'POST';
            msj='Proveedor(s) agregado';
            executeRequest(id_prov,baseUrl,method,data,msj);
            
        }
        if(action=='edition' && data.length >0){
            baseUrl = 'update_proveedor';
            data = data;
            method= 'PUT';
            msj='Proveedor(s) actualizado';
            executeRequest(id_prov,baseUrl,method,data,msj);
        }
}