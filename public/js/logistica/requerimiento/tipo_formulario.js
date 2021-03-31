function limpiarSelectTipoCliente(){
    let selectElement = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']");
    if(selectElement !=null){
        while (selectElement.options.length > 0) {                
            selectElement.remove(0);
        }    
    }
}

function createOptionTipoCliente(tipoRequerimiento){  
    let selectTipoCliente = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']");
    if(selectTipoCliente !=null){
        let array = [];
        switch (tipoRequerimiento) {
        case 'COMPRA':
        case '1':
            limpiarSelectTipoCliente();
            array =[
                {descripcion:'Persona Natural', valor: 1},
                {descripcion:'Persona Juridica', valor: 2}
                // {descripcion:'Uso Almacen', valor: 3},
                // {descripcion:'Uso Administración', valor: 4}
            ]
            array.forEach(element => {
                let option = document.createElement("option");
                option.text = element.descripcion;
                option.value = element.valor;
                selectTipoCliente.add(option);
            });
            break;
            case 'VENTA':
            case '2':
                limpiarSelectTipoCliente();
                array =[
                    {descripcion:'Persona Natural', valor: 1},
                    {descripcion:'Persona Juridica', valor: 2}
                ]
                array.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.descripcion;
                    option.value = element.valor;
                    selectTipoCliente.add(option);
                });
                break;
            case 'USO_ALMACEN':
            case '3':
                limpiarSelectTipoCliente();
                array =[
                    {descripcion:'Uso Almacen', valor: 3},
                    {descripcion:'Uso Administración', valor: 4}
                ]
                array.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.descripcion;
                    option.value = element.valor;
                    selectTipoCliente.add(option);
                });
                break;
        
            default:
    
                break;
        }
        return false;
    }
   
}

function changeOptTipoReqSelect(e){
    if(e.target.value == 1){
        createOptionTipoCliente('COMPRA');
        cambiarTipoFormulario('MGCP');
        limpiarFormRequerimiento();
        document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén que solicita';
    }else if(e.target.value == 2){ //venta directa
        createOptionTipoCliente('VENTA');
        cambiarTipoFormulario('CMS')
        // listar_almacenes();
    }else if(e.target.value == 3){
        createOptionTipoCliente('USO_ALMACEN');
        if(id_grupo_usuario_sesion_list.includes(3)){ //proyectos
            mostrarTipoForm('BIENES_SERVICIOS_PROYECTOS');
        }else{
            cambiarTipoFormulario('BIENES_SERVICIOS');

        }
    }
    getNexCodigoRequerimiento(e.target.value);
}


function cambiarTipoFormulario(tipo=null){
    if(tipo ==null){
        if(id_grupo_usuario_sesion_list.includes(1)){ //Administración
            mostrarTipoForm('BIENES_SERVICIOS');
            document.querySelector("div[type='requerimiento'] select[name='tipo_requerimiento']").value =3;
        }else if(id_grupo_usuario_sesion_list.includes(2)){ //Comercial
            mostrarTipoForm('MGCP');
            document.querySelector("div[type='requerimiento'] select[name='tipo_requerimiento']").value =1;
        }else if(id_grupo_usuario_sesion_list.includes(3)){ //proyectos
            mostrarTipoForm('BIENES_SERVICIOS_PROYECTOS');
            document.querySelector("div[type='requerimiento'] select[name='tipo_requerimiento']").value =3;
        }else if(id_grupo_usuario_sesion_list.includes(4)){ //Gerencia
            mostrarTipoForm('BIENES_SERVICIOS');
            document.querySelector("div[type='requerimiento'] select[name='tipo_requerimiento']").value =3;

        }else if(id_grupo_usuario_sesion_list.includes(5)){ //Control Interno
            mostrarTipoForm('BIENES_SERVICIOS');
            document.querySelector("div[type='requerimiento'] select[name='tipo_requerimiento']").value =3;

        }

    }else{
        mostrarTipoForm(tipo);
    }

}


function mostrarTipoForm(tipo){
    // console.log(tipo);
    switch (tipo) {
        case 'MGCP': //Mgcp - comercial
            hiddeElement('ocultar','form-requerimiento',[
                'input-group-cliente',
                'input-group-rol-usuario',
                'input-group-comercial',
                'input-group-almacen',
                'input-group-cuenta',
                'input-group-proyecto'
            ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-sede',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-cliente',
                'input-group-fecha_entrega',
                'input-group-ubigeo-entrega',
                'input-group-monto',
                'input-group-cliente',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-direccion-entrega',
                'input-group-nombre-contacto',
                'input-group-cargo-contacto',
                'input-group-email-contacto',
                'input-group-telefono-contacto',
                'input-group-direccion-contacto',
                'input-group-horario-contacto',
            ]); 
            cambiarVisibilidadBtn("btn-add-servicio","ocultar")

        break;

        case 'CMS':
            hiddeElement('ocultar','form-requerimiento',[
                // 'input-group-proyecto',
                'input-group-rol-usuario',
                'input-group-comercial',
                'input-group-almacen',
                'input-group-cuenta'
                ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-sede',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-cliente',
                'input-group-direccion-entrega',
                'input-group-ubigeo-entrega',
                'input-group-monto'
    
            ]); 
            cambiarVisibilidadBtn("btn-add-servicio","mostrar")

        break;

        case 'BIENES_SERVICIOS':
            hiddeElement('ocultar','form-requerimiento',[
                'input-group-monto',
                'input-group-rol-usuario',
                'input-group-comercial',
                'input-group-almacen',
                'input-group-ubigeo-entrega',
                'input-group-cuenta',
                'input-group-cliente',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-direccion-entrega',
                'input-group-cuenta',
                'input-group-nombre-contacto',
                'input-group-cargo-contacto',
                'input-group-email-contacto',
                'input-group-telefono-contacto',
                'input-group-direccion-contacto',
                'input-group-horario-contacto'
                ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-sede'
                
            ]); 
            cambiarVisibilidadBtn("btn-add-servicio","mostrar")

        break;

        case 'BIENES_SERVICIOS_PROYECTOS': //bienes y servicios - proyectos
            hiddeElement('ocultar','form-requerimiento',[
                'input-group-cliente',
                'input-group-rol-usuario',
                'input-group-comercial',
                'input-group-almacen',
                'input-group-ubigeo-entrega',
                'input-group-cuenta',
                'input-group-monto',
                'input-group-cliente',
                'input-group-tipo-cliente',
                'input-group-telefono-cliente',
                'input-group-email-cliente',
                'input-group-direccion-entrega',
                'input-group-nombre-contacto',
                'input-group-cargo-contacto',
                'input-group-email-contacto',
                'input-group-telefono-contacto',
                'input-group-direccion-contacto',
                'input-group-horario-contacto',
                'input-group-nombre-contacto',
                'input-group-cargo-contacto',
                'input-group-email-contacto',
                'input-group-telefono-contacto',
                'input-group-direccion-contacto',
                'input-group-horario-contacto'
            ]);
            hiddeElement('mostrar','form-requerimiento',[
                'input-group-moneda',
                'input-group-empresa',
                'input-group-sede',
                'input-group-fecha_entrega',
                'input-group-proyecto'    
            ]); 
            cambiarVisibilidadBtn("btn-add-servicio","mostrar")

        break;

            // case 2:
        //     hiddeElement('ocultar','form-requerimiento',[
        //         'input-group-moneda',
        //         'input-group-empresa',
        //         'input-group-rol-usuario',
        //         'input-group-sede',
        //         'input-group-telefono-cliente',
        //         'input-group-email-cliente',
        //         'input-group-cliente',
        //         'input-group-direccion-entrega',
        //         'input-group-cuenta',
        //         'input-group-ubigeo-entrega',
        //         'input-group-proyecto',
        //         'input-group-comercial',
        //         'input-group-monto',
        //         'input-group-contacto'
        //     ]);
        //     hiddeElement('mostrar','form-requerimiento',[
        //         'input-group-almacen',
        //         'input-group-tipo-cliente'
        
        //     ]);

        //     document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén que solicita';
        //     document.querySelector("form[id='form-requerimiento'] select[name='rol_usuario']").value='';

        // break;
    
        // case 'VENTA':
        //     document.querySelector("div[id='input-group-almacen'] h5").textContent = 'Almacén';
        //     document.querySelector("form[id='form-requerimiento'] select[name='rol_usuario']").value='';

        //     hiddeElement('ocultar','form-requerimiento',[
        //         'input-group-rol-usuario',
        //         'input-group-proyecto',
        //         'input-group-comercial',
        //         'input-group-almacen'
                

        //     ]);
        //     hiddeElement('mostrar','form-requerimiento',[
        //         'input-group-sede',
        //         'input-group-tipo-cliente',
        //         'input-group-telefono-cliente',
        //         'input-group-email-cliente',
        //         'input-group-empresa',
        //         'input-group-tipo-cliente',
        //         'input-group-cliente',
        //         'input-group-direccion-entrega',
        //         'input-group-monto'
    
        //     ]);
        //     break;


            // case 6: //compra - administracion
            // hiddeElement('ocultar','form-requerimiento',[
            //     'input-group-cliente',
            //     'input-group-rol-usuario',
            //     'input-group-comercial',
            //     'input-group-almacen',
            //     'input-group-cuenta',
            //     'input-group-proyecto',
            //     'input-group-cliente',
            //     'input-group-tipo-cliente',
            //     'input-group-telefono-cliente',
            //     'input-group-email-cliente',
            //     'input-group-direccion-entrega',
            //     'input-group-nombre-contacto',
            //     'input-group-cargo-contacto',
            //     'input-group-email-contacto',
            //     'input-group-telefono-contacto',
            //     'input-group-direccion-contacto',
            //     'input-group-horario-contacto'
            // ]);
            // hiddeElement('mostrar','form-requerimiento',[
            //     'input-group-moneda',
            //     'input-group-empresa',
            //     'input-group-sede',
            //     'input-group-fecha_entrega',
            //     'input-group-ubigeo-entrega',
            //     'input-group-monto',

            // ]); 
            // break;
        default:
            break;
    }
}

function cambiarVisibilidadBtn(name,estado){
    let actualClass= document.querySelector("button[id='"+name+"']").className;
    let newclass='';
    if(estado == 'ocultar'){
        newclass = actualClass.concat(' invisible');
        document.querySelector("button[id='"+name+"']").setAttribute('class',newclass);
    }else if(estado =='mostrar'){

        while (actualClass.search("invisible") >= 0) {
            actualClass= actualClass.replace("invisible","");
        }
        newclass =actualClass;
        document.querySelector("button[id='"+name+"']").setAttribute('class',newclass);


    }
}