

function listarUltimasCompras(id_item,id_detalle_requerimiento) {
    var vardataTables = funcDatatables();
    $('#ultimasCompras').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'destroy' : true,

        'ajax': '/logistica/ultimas_compras/'+id_item+'/0',
        'columns': [
            {'data': 'id'},
            {'data': 'id_item'},
            {'data': 'descripcion'},
            {'data': 'precio_unitario'},
            {'data': 'proveedor'},
            {'data': 'documento'},
            {'data': 'fecha_registro'},
            
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'desc']
        ]
    });    
}

function verUltimasCompras(event){
    let id_item = $('[name=id_item]').val();
    // console.log('id_item');
    // console.log(id_item);
    // console.log(id_item.length);
    
    if(id_item != null && id_item.length > 0){
    $('#modal-ultimas_compras').modal({
        show: true,
        backdrop: 'true'
    });
 
        listarUltimasCompras(id_item,0);
    }else{
        alert("Primero debe seleccione un ítem. ");
    }
    
}

function validaModalDetalle(){
    var unidad_medida_item = document.querySelector("div[id='modal-detalle-requerimiento'] select[name='unidad_medida_item']").value;
    var cantidad_item = document.querySelector("div[id='modal-detalle-requerimiento'] input[name='cantidad_item']").value;
    var msj = '';
    // console.log(unidad_medida_item);
    // console.log(cantidad_item);
    if(document.querySelector("div[id='modal-detalle-requerimiento'] input[name='id_producto']").value > 0 ){
        if (unidad_medida_item == ''){
            msj+='\n Es necesario que seleccione una Unidad de Medida';
        }
    }
    if (cantidad_item == ''){
        msj+='\n Es necesario una Cantidad';    
    }
    return msj;
}

function statusBtnOpenProyectoModal(value){
    switch (value) {
        case 'DESHABILITAR':
            document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]')?document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]').setAttribute('disabled', true):null;
            document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]')?document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]').setAttribute('title', 'No puede Cambiar de Proyecto, Existe uno o más items vinculados con el proyecto'):null;
            
            break;
            case 'HABILITAR':
                document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]')?document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]').removeAttribute('disabled'):null;
                document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]')?document.querySelector('form[id="form-requerimiento"] select[name="id_proyecto"]').setAttribute('title', 'Seleccionar Proyecto'):null;
            
            break;
    
        default:
            break;
    }
    
}

// function calcMontoLimiteDePartida(){
//     let passMount= false;
//    if( ListOfPartidaSelected.filter(function(partida){ return partida.id_partida === idPartidaSelected }).length  == 0){
//        ListOfPartidaSelected.push(partidaSelected);
//    }
//    // console.log('itemSelected');
//    // console.log(itemSelected);
//    ListOfItems.push(itemSelected);
   
//    // console.log('ListOfItems');
//    // console.log(ListOfItems);
//    let counts =[];
//    let partidaList =[];
//    let htmlStatusPartida='';
//    // calc limite de monto de items por partida
//    if(ListOfItems.length >0){
   
       
//        // first, convert data into a Map with reduce
//            counts = ListOfItems.reduce((prev, curr) => {
//            let count = prev.get(curr.id_partida) || 0;
//            let sum_prec_ref = (parseFloat(curr.precio_unitario) * parseFloat(curr.cantidad)) + parseInt(count);
//            prev.set(curr.id_partida, sum_prec_ref );
//            return prev;
//        }, new Map());

//        // console.log([...counts]);

//        partidaList = ListOfItems.reduce((prev, curr) => {
//            prev.set(curr.id_partida, curr.codigo_partida );
//            return prev;
//        }, new Map());

//        // console.log([...partidaList]);




//        // then, map your counts object back to an array
//        let reducedObjArr = [...counts].map(([id_partida, suma_total]) => {
//            return {id_partida, suma_total}
//        })

//        // console.log(reducedObjArr);

//        // agregando descripcion (nombre de partida) 


//        reducedObjArr.map((item,i)=>{
//                ListOfPartidaSelected.filter(function(partida){ 
//                    return partida.id_partida == item.id_partida 
//                });
//        });

//        // console.log(reducedObjArr);

//        reducedObjArr.map((item)=>{
//            if(item.id_partida == [...partidaList][0][0] ){
//                item.codigo = [...partidaList][0][1];
//            }
//        });

//        // calcular si excede
//        ListOfPartidaSelected.forEach(function(element) {
//            let st =reducedObjArr.filter(vendor => (vendor.id_partida == element.id_partida));
//            // console.log(st);

//        if(st.length > 0){
//            if(st[0].suma_total > element.importe_total){
//                alert("Ha sido superado el importe total de partida "+element.descripcion+" [ importe acumulado: "+st[0].suma_total+"]" )
//                let lastIndex =ListOfItems.length-1;
//                // console.log(lastIndex);
//                ListOfItems.splice(lastIndex,1);
//                // console.log(ListOfItems);
               
//                passMount= true;
//            }else{
//                passMount= false;
//            }
//        }else{
//            passMount= false;

//        }         

//        });

//    }
//    return passMount;
// }


function aceptarCambiosItem(){ // del modal-detalle-requerimiento
  // var id_det = $('[name=id_detalle_requerimiento]').val();
    // var id_req = $('[name=id_requerimiento]').val();
    // let item = get_data_detalle_requerimiento();
    let unidad_medida_item = document.querySelector("div[id='modal-detalle-requerimiento'] select[name='unidad_medida_item']").value;
    let cantidad_item = document.querySelector("div[id='modal-detalle-requerimiento'] input[name='cantidad_item']").value;
    let precio_unitario = document.querySelector("div[id='modal-detalle-requerimiento'] input[name='precio_ref_item']").value;

    if(indice >= 0){
        data_item[indice].id_unidad_medida = unidad_medida_item;
        data_item[indice].unidad = document.querySelector("div[id='modal-detalle-requerimiento'] select[name='unidad_medida_item']").options[document.querySelector("div[id='modal-detalle-requerimiento'] select[name='unidad_medida_item']").selectedIndex].textContent;
        data_item[indice].cantidad = cantidad_item;
        data_item[indice].precio_unitario = precio_unitario;
        data_item[indice].subtotal = (Math.round((cantidad_item * precio_unitario) * 100) / 100).toFixed(2);

        llenar_tabla_detalle_requerimiento(data_item);
        $('#modal-detalle-requerimiento').modal('hide');


    }else{
        alert("El indice no es numérico");
    }
}

function eliminarItemDetalleRequerimiento(event,index){
    event.preventDefault();
    
    if(index  !== undefined){ // editando item
        let item = data_item[index]; 
        // console.log(data_item[index].id_item);
        actualizarMontoLimiteDePartida(data_item[index].id_item,'ELIMINAR');
        item.estado=7;
        // console.log(data_item.length);
        
        let tamDataItem = data_item.length;
        let numEstadoCero =0;
        data_item.forEach(element => {
            if(element.estado == 7){
                numEstadoCero++;
            }
        });
        if(numEstadoCero == tamDataItem){
            statusBtnOpenProyectoModal('HABILITAR');
        }

        alert("Se cambio el estado del Item, guarde el Requerimiento para salvar los cambios");
        llenar_tabla_detalle_requerimiento(data_item);

    }



}

function agregarItem(){

    var msj = validaModalDetalle();
    if (msj.length > 0){
        alert(msj);
    } else{

    var table = document.getElementById("ListaDetalleRequerimiento");
    var len = table.querySelectorAll('tr').length;
    for (var i=0; i < len; i++){
    // console.log(table.querySelectorAll('tr')[i].getAttribute('id'));
    
        if ( table.querySelectorAll('tr')[i].getAttribute('id') == "default_tr"){
        // table.querySelectorAll('tr')[i].setAttribute('class', 'yourID')
            table.deleteRow(i);
        }
    }
    let item = get_data_detalle_requerimiento();
    // console.log(item);
    // verficar codigo de item exista para poder ser agregado ////////
    if(item.cod_item ==="" || item.cod_item ===null || item.cod_item ===undefined ){
        alert("Campo vacío - Debe selecione un item o escriba uno");
        return null;
    }
    
    // if(parseInt(item.id_partida) <= 0 ||  (Number.isNaN(item.id_partida) ==true) ){
    //     alert("Debe seleccionar una partida");
    //     return null;
    // }

    // let = passMount=calcMontoLimiteDePartida();
    // console.log(passMount);
    
    // if(passMount == false){

    //     /////////////////////////////////////////
        let tam_data_item = data_item.length;
        data_item.push(item);
        // console.log(data_item);
        
        let update_tam_data_item= data_item.length;
        if(update_tam_data_item > tam_data_item  ){
            setTextInfoAnimation("Agregado!"); //public/js/publico/animation.js
            statusBtnOpenProyectoModal('DESHABILITAR');

        }else{
            setTextInfoAnimation("Error!");//public/js/publico/animation.js
        }
    // }else{
    //     setTextInfoAnimation("Excede el monto de la partida");

    // }


        llenar_tabla_detalle_requerimiento(data_item);



    limpiarFormularioDetalleRequerimiento();

    let btnVerUltimasCompras = document.getElementsByName('btnVerUltimasCompras')[0];
    btnVerUltimasCompras.setAttribute('disabled',true);
    }
    $('#modal-detalle-requerimiento').modal('hide');

 }


 function llenar_tabla_detalle_requerimiento(data_item){

    limpiarTabla('ListaDetalleRequerimiento');
    htmls ='<tr></tr>';
    $('#ListaDetalleRequerimiento tbody').html(htmls);
    var table = document.getElementById("ListaDetalleRequerimiento"); 
    
    let widthGroupBtnAction='auto';

    let cantidadIdPartidas=0;
    let cantidadIdCentroCostos=0;

    for (var a = 0; a < data_item.length; a++) {
        if(data_item[a].id_partida >0){
            cantidadIdPartidas++;
        }
        if(data_item[a].id_centro_costo >0){
            cantidadIdCentroCostos++;
        }
    }
    var tipo_requerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;


    for(var a=0;a < data_item.length;a++){
        if(data_item[a].estado !=7){
            
            var row = table.insertRow(-1);
            let descripcion_unidad = '';
    
            if(data_item[a].id_producto > 0){
                descripcion_unidad = data_item[a].unidad;
            }else if(data_item[a].id_servicio > 0){
                descripcion_unidad = "Servicio";
            }else if(data_item[a].id_equipo >0){
                descripcion_unidad = "Equipo";
            }else{
                descripcion_unidad = data_item[a].unidad;
            }
            row.insertCell(0).innerHTML = data_item[a].id_item?data_item[a].id_item:'0';
            row.insertCell(1).innerHTML = data_item[a].cod_item?data_item[a].cod_item:'-';
            row.insertCell(2).innerHTML = data_item[a].part_number?data_item[a].part_number:'-';
            row.insertCell(3).innerHTML = data_item[a].des_item?data_item[a].des_item:'-';
            row.insertCell(4).innerHTML = descripcion_unidad;
            row.insertCell(5).innerHTML = data_item[a].cantidad?data_item[a].cantidad:'0';
            row.insertCell(6).innerHTML = data_item[a].precio_unitario?data_item[a].precio_unitario:'0';
            row.insertCell(7).innerHTML = data_item[a].tipo_moneda?data_item[a].tipo_moneda:'';
            row.insertCell(8).innerHTML = data_item[a].subtotal ? data_item[a].subtotal : '';

            var id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
            var id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
            var tdBtnAction = '';

            if((id_grupo == 3 && data_item[a].id_partida > 0 ) || (cantidadIdPartidas >0)){
                document.querySelector("table[id='ListaDetalleRequerimiento']").tHead.children[0].cells[9].setAttribute('class','');            
                row.insertCell(9).innerHTML =  data_item[a].cod_partida ? data_item[a].cod_partida : '';

                if(data_item[a].id_centro_costo >0 || (cantidadIdPartidas >0 && cantidadIdCentroCostos >0)){
                    document.querySelector("table[id='ListaDetalleRequerimiento']").tHead.children[0].cells[10].setAttribute('class','');            
                    row.insertCell(10).innerHTML =  data_item[a].codigo_centro_costo ? data_item[a].codigo_centro_costo : '';
                    tdBtnAction = row.insertCell(11);
        
                }else{
                    tdBtnAction = row.insertCell(10);
                }


            }else if(data_item[a].id_centro_costo >0 || (cantidadIdCentroCostos >0)){
                document.querySelector("table[id='ListaDetalleRequerimiento']").tHead.children[0].cells[10].setAttribute('class','');             
                row.insertCell(9).innerHTML =  data_item[a].codigo_centro_costo ? data_item[a].codigo_centro_costo : '';

                if(data_item[a].id_almacen_reserva >0 || data_item[a].proveedor_id >0){
                    document.querySelector("table[id='ListaDetalleRequerimiento']").tHead.children[0].cells[11].setAttribute('class','');          
                    row.insertCell(10).innerHTML =  data_item[a].almacen_reserva ? data_item[a].almacen_reserva : data_item[a].proveedor_razon_social;
                    tdBtnAction = row.insertCell(11);

                }else{

                    tdBtnAction = row.insertCell(10);
                }
            }else if(data_item[a].id_almacen_reserva >0 || data_item[a].proveedor_id >0){
                document.querySelector("table[id='ListaDetalleRequerimiento']").tHead.children[0].cells[11].setAttribute('class','');            
                row.insertCell(9).innerHTML =  data_item[a].almacen_reserva ? data_item[a].almacen_reserva : data_item[a].proveedor_razon_social;

                tdBtnAction = row.insertCell(10);
            }else{
                tdBtnAction = row.insertCell(9);

            }
            // tdBtnAction.className = classHiden;
            var btnAction = '';
            var hasAttrDisabled ='';
                if(document.querySelector("button[id='btnEditar']").hasAttribute('disabled')== true){
                    hasAttrDisabled ='disabled';
                }else{
                    hasAttrDisabled = '';
                }
   

            tdBtnAction.setAttribute('width', 'auto');
            var id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;

            btnAction = `<div class="btn-group btn-group-sm" role="group" aria-label="Second group"><center>`;
           
            if (tipo_requerimiento ==3 ) {
                btnAction += `<button type="button" class="btn btn-warning btn-xs"  name="btnMostarPartidas" data-toggle="tooltip" title="Partidas" onClick=" partidasModal(${data_item[a].id_item});" ${hasAttrDisabled}><i class="fas fa-money-check"></i></button>`;
            } 
        
            btnAction += `<button type="button" class="btn btn-primary btn-xs activation" name="btnCentroCostos" data-toggle="tooltip" title="Centro de Costos" style="background: #3c763d;" onClick="centroCostosModal(event, ${a});" ${hasAttrDisabled}><i class="fas fa-donate"></i></button>`;
            if(tipo_requerimiento ==3){ // tipo = Bienes y Servicios
                // btnAction += `<button type="button" class="btn btn-primary btn-xs" name="btnBuscarEnAlmacen" data-toggle="tooltip" title="Buscar Stock en Almacenes" style="background:#b498d0;" onClick="buscarStockEnAlmacenesModal(${data_item[a].id_item});" ${hasAttrDisabled}><i class="fas fa-warehouse"></i></button>`;
                btnAction += `<button type="button" class="btn btn-xs activation" name="btnAlmacenReservaModal" data-toggle="tooltip" title="Almacén Reserva" onClick="modalAlmacenReserva(this, ${a});" ${hasAttrDisabled} style="background:#b498d0; color: #f5f5f5;"><i class="fas fa-warehouse"></i></button>`;

            }
            if(tipo_requerimiento ==2){ // tipo = CMS
                btnAction += `<button type="button" class="btn btn-xs activation" name="btnAlmacenReservaModal" data-toggle="tooltip" title="Almacén Reserva" onClick="modalAlmacenReserva(this, ${a});" ${hasAttrDisabled} style="background:#b498d0; color: #f5f5f5;"><i class="ffas fa-warehouse"></i></button>`;
                btnAction += `<button type="button" class="btn btn-primary btn-xs activation" name="btnModalSeleccionarCrearProveedor data-toggle="tooltip" title="Proveedor" onClick="modalSeleccionarCrearProveedor(event, ${a});" ${hasAttrDisabled}><i class="fas fa-user-tie"></i></button>`;

            }
            if(tipo_requerimiento !=2){

                btnAction += `<button type="button" class="btn btn-default btn-xs activation" name="btnAdjuntarArchivos" data-toggle="tooltip" title="Adjuntos" onClick="archivosAdjuntosModal(event, ${a});" ${hasAttrDisabled} ><i class="fas fa-paperclip"></i></button>`;
            }
            btnAction += `<button type="button" class="btn btn-info btn-xs" name="btnEditarItem" data-toggle="tooltip" title="Editar" onclick="detalleRequerimientoModal(event,${a});" ${hasAttrDisabled} ><i class="fas fa-edit"></i></button>`;
            btnAction += `<button type="button" class="btn btn-danger btn-xs activation"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" onclick="eliminarItemDeListado(this,${data_item[a].id_item});" ${hasAttrDisabled} ><i class="fas fa-trash-alt"></i></button>`;
            btnAction += `</center></div>`;
            tdBtnAction.innerHTML = btnAction;

        }
    }

}


// modal detalle 
var indice='';
function detalleRequerimientoModal(event=null,index=null){


    $('#form-detalle-requerimiento')[0].reset();
    if(event){
        event.preventDefault();
    }
    var btnAceptarCambio = document.getElementsByName("btn-aceptar-cambio");
    var btnAgregarCambio = document.getElementsByName("btn-agregar-item");
    if(index  != undefined){ // editando item
        let item = data_item[index]; 
        indice = index;       
        fill_input_detalle_requerimiento(item);
        controlUnidadMedida();
        disabledControl(btnAgregarCambio,true);
        disabledControl(btnAceptarCambio,false);
    }else{
        disabledControl(btnAgregarCambio,false);
        disabledControl(btnAceptarCambio,true);
    }
    var tipo = $('[name=tipo_requerimiento]').val();
    // console.log(tipo);
    if (tipo == 2){        
        // var sede = $('[name=sede]').val();
        // var almacen = $('select[name=id_almacen]').val();
        
        // if (sede !== null && sede !== '' &&  sede !== undefined ){
            // if (almacen !== null && almacen !== '' && almacen !== undefined ){
                $('#modal-detalle-requerimiento').modal({
                    show: true,
                    backdrop: 'true'
                });
                // $('[name=id_almacen]').show();
                document.querySelector("div[id='modal-detalle-requerimiento'] div[id='promocion_activa']").setAttribute('hidden',true);

                // cargar_almacenes(sede);
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='fecha_entrega_item']").value='';
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='lugar_entrega_item']").value='';
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='des_partida']").value='';
                document.querySelector("div[id='modal-detalle-requerimiento'] input[name='id_partida']").value='';
                // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-fecha_entrega']").setAttribute('hidden',true);
                // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-lugar_entrega']").setAttribute('hidden',true);
                // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-partida']").setAttribute('hidden',true);
    
            // }else{
            //     alert('Debe seleccionar un almacen.');
            // }
        // } else {
        //     alert('Debe seleccionar una sede.');
        // }
    }
    else if (tipo == 1){
        $('#modal-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        // document.querySelector("div[id='modal-detalle-requerimiento'] input[name='fecha_entrega_item']").value='';
        // document.querySelector("div[id='modal-detalle-requerimiento'] input[name='lugar_entrega_item']").value='';
        // document.querySelector("div[id='modal-detalle-requerimiento'] input[name='des_partida']").value='';
        // document.querySelector("div[id='modal-detalle-requerimiento'] input[name='id_partida']").value='';
        // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-fecha_entrega']").removeAttribute('hidden');
        // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-lugar_entrega']").removeAttribute('hidden');
        // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-partida']").removeAttribute('hidden');
    }else if(tipo ==3){
        $('#modal-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='fecha_entrega_item']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='lugar_entrega_item']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='des_partida']").value='';
        document.querySelector("div[id='modal-detalle-requerimiento'] input[name='id_partida']").value='';
        // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-fecha_entrega']").setAttribute('hidden',true);
        // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-lugar_entrega']").setAttribute('hidden',true);
        // document.querySelector("div[id='modal-detalle-requerimiento'] div[id='input-group-partida']").setAttribute('hidden',true);
    }
    actualizarMontoLimiteDePartida();

    controlInputModalDetalleRequerimiento();
}

function controlInputModalDetalleRequerimiento(){
    let id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
    let tipo_requerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
    let tipo_cliente = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']")?document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']").value:null;
    // console.log(id_grupo);
    // console.log(tipo_requerimiento);

    if(tipo_requerimiento == 1 ){
        // hiddeElement('mostrar','form-detalle-requerimiento',[
        //     'input-group-lugar_entrega',
        //     'input-group-fecha_entrega'
        //     ]);

            if(id_grupo == 3){ // proyectos
                hiddeElement('mostrar','form-detalle-requerimiento',[
                    'input-group-partida'
                    ]);
            }else{
                hiddeElement('ocultar','form-detalle-requerimiento',[
                    'input-group-lugar_entrega',
                    'input-group-fecha_entrega',
                    'input-group-partida'
                    ]);
            }
    }else{
        hiddeElement('ocultar','form-detalle-requerimiento',[
            'input-group-lugar_entrega',
            'input-group-fecha_entrega',
            'input-group-partida'
            ]);
    }
}

function actualizarMontoLimiteDePartida(id_item,option){
switch (option) {
    case 'ELIMINAR':
        const newListOfItems = ListOfItems.filter(word => word.id_item != id_item);
        ListOfItems = newListOfItems;
        // console.log(ListOfItems);
        let counts =[];
        let htmlStatusPartida='';
        // calc limite de monto de items por partida
        if(ListOfItems.length >0){
        
            
            // first, convert data into a Map with reduce
                counts = ListOfItems.reduce((prev, curr) => {
                let count = prev.get(curr.id_partida) || 0;
                prev.set(curr.id_partida, (parseFloat(curr.precio_unitario) *parseFloat(curr.cantidad)) + count);
                return prev;
            }, new Map());
    
            // console.log([...counts]);
    
            // then, map your counts object back to an array
            let reducedObjArr = [...counts].map(([id_partida, suma_total]) => {
                return {id_partida, suma_total}
            })
            // console.log('reducedObjArr');
            // console.log(reducedObjArr);
            // agregando descripcion (nombre de partida) 
            
             reducedObjArr.map((item,i)=>{
                // console.log(item);
                // console.log(ListOfPartidaSelected.filter(function(partida){ return partida.id_partida == item.id_partida }).length  > 0);
                     ListOfPartidaSelected.filter(function(partida){ 
                        return partida.id_partida == item.id_partida 
                    });
 
            });

 
            
            ListOfPartidaSelected.forEach(function(element) {
                let st =reducedObjArr.filter(vendor => (vendor.id_partida == element.id_partida));
                // console.log(st);
                
                if(st[0] !==undefined){
                    if(st[0].suma_total > element.importe_total){
                        alert("Ha sido superado el importe total de partida "+element.descripcion+" [importe limite: "+element.importe_total+", importe acumulado: "+st[0].suma_total+"]" )
                    }
                }
            });
        }
        break;
    default:
        break;
}
}

function fill_input_detalle_requerimiento(item){
    console.log(item);
    $('[name=id_tipo_item]').val(item.id_tipo_item);
    $('[name=id_item]').val(item.id_item);
    $('[name=id_producto]').val(item.id_producto);
    $('[name=id_servicio]').val(item.id_servicio);
    $('[name=id_equipo]').val(item.id_equipo);
    $('[name=id_detalle_requerimiento]').val(item.id_detalle_requerimiento);
    $('[name=codigo_item]').val(item.cod_item);
    $('[name=part_number]').val(item.part_number);
    $('[name=descripcion_item]').val(item.des_item);
    $('[name=unidad_medida_item]').val(item.id_unidad_medida);
    $('[name=cantidad_item]').val(item.cantidad);
    $('[name=precio_ref_item]').val(item.precio_unitario);
    $('[name=fecha_entrega_item]').val(item.fecha_entrega);
    $('[name=lugar_entrega_item]').val(item.lugar_entrega);
    $('[name=id_partida]').val(item.id_partida);
    $('[name=cod_partida]').val(item.cod_partida);
    $('[name=des_partida]').val(item.des_partida);
    $('[name=categoria]').val(item.categoria);
    $('[name=subcategoria]').val(item.subcategoria);
    $('[name=estado]').val(item.estado);
}


// modal catalogo items
function catalogoItemsModal(){  
    $('#modal-detalle-requerimiento').modal('hide');

    var tipo_requerimiento = $('[name=tipo_requerimiento]').val();
    if (tipo_requerimiento == 1 || tipo_requerimiento == 2|| tipo_requerimiento == 3){
        $('#modal-catalogo-items').modal({
            show: true,
            backdrop: 'true',
            keyboard: true

        });
        listarItems();


    }else{
        alert("Debe seleccionar un tipo de requerimiento");
    }

}

function listar_almacenes(){
    $.ajax({
        type: 'GET',
        url: 'listar_almacenes',
        dataType: 'JSON',
        success: function(response){
            // console.log(response.data);
            var option = '';
            for (var i=0; i<response.data.length; i++){
                if (response.data.length == 1){
                    option+='<option data-id-sede="'+response.data[i].id_sede+'" data-id-empresa="'+response.data[i].id_empresa+'" value="'+response.data[i].id_almacen+'" selected>'+response.data[i].codigo+' - '+response.data[i].descripcion+'</option>';
                } else {
                    option+='<option data-id-sede="'+response.data[i].id_sede+'" data-id-empresa="'+response.data[i].id_empresa+'" value="'+response.data[i].id_almacen+'">'+response.data[i].codigo+' - '+response.data[i].descripcion+'</option>';
                }
            }
            $('[name=id_almacen]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_sedes(){
    $.ajax({
        type: 'GET',
        url: 'mostrar-sede',
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            var option = '';
            for (var i=0; i<response.length; i++){
                if (response.length == 1){
                    option+='<option data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_sede+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                } else {
                    option+='<option data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_sede+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                }
            }
            $('[name=sede]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


function listarItems() {
    // console.log('listaItems');
    var vardataTables = funcDatatables();
   var tablaListaItems =  $('#listaItems').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        // "dom": '<"toolbar">frtip',

        // 'scrollY': '30vh',
        // 'scrollCollapse': true,
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        // "scrollX": true,
        'ajax': '/logistica/mostrar_items',
        'columns': [
            {'data': 'id_item'},
            {'data': 'id_producto'},
            {'data': 'id_servicio'},
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'categoria'},
            {'data': 'subcategoria'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida_descripcion'},
            {'data': 'id_unidad_medida'},
            {'render':
                function (data, type, row){
                    if(row.id_unidad_medida == 1){
                        return ('<button class="btn btn-sm btn-info" onClick="verSaldoProducto('+row.id_producto+ ');">Stock</button>');
                    }else{ 
                        return '';
                    }

                }
            }
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},
            { 'aTargets': [1], 'sClass': 'invisible'},
            { 'aTargets': [2], 'sClass': 'invisible'},
            { 'aTargets': [3], 'sClass': 'invisible'},
            { 'aTargets': [10], 'sClass': 'invisible'}
                    ],
        'order': [
            [8, 'asc']
        ],
        "initComplete": function(settings, json) {
            // console.log(tempDetalleItemCCSelect);
            if(tempDetalleItemCCSelect.hasOwnProperty('descripcion')){
                if(tempDetalleItemCCSelect.descripcion.length >0){
                    $('#text-info-item-vinculado').attr('title',tempDetalleItemCCSelect.part_number);
                    $('#text-info-item-vinculado').removeAttr('hidden');
                    $('#example_filter input').val(tempDetalleItemCCSelect.part_number);
                    this.api().search(tempDetalleItemCCSelect.part_number).draw();
                    document.querySelector("input[type='search']").focus();
                    document.querySelector("input[type='search']").setSelectionRange(tempDetalleItemCCSelect.part_number.length,tempDetalleItemCCSelect.part_number.length );

                    if(this.api().page.info().recordsDisplay ==0){
                        $('#text-info-item-vinculado').attr('title',tempDetalleItemCCSelect.descripcion);
                        $('#text-info-item-vinculado').removeAttr('hidden');
                        $('#example_filter input').val(tempDetalleItemCCSelect.descripcion);
                        this.api().search(tempDetalleItemCCSelect.descripcion).draw();
                        document.querySelector("input[type='search']").focus();
                        document.querySelector("input[type='search']").setSelectionRange(tempDetalleItemCCSelect.descripcion.length,tempDetalleItemCCSelect.descripcion.length );

                    }

                }
            }
          } 
    });

 

    let tablelistaitem = document.getElementById(
        'listaItems_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    
    let listaItems_filter = document.getElementById(
        'listaItems_filter'
    )
    listaItems_filter.querySelector("input[type='search']").style.width='100%';
}

var getSaldosPorAlmacen = function() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url: 'listar-saldos-por-almacen',
            datatype: "JSON",
            data: data,
            success: function(response){
                resolve(response)  
            },
            error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
        });
    
    });
}

function verSaldoProducto(id_producto){
    $('#modal-saldos').modal({
        show: true,
        backdrop: 'true',
        keyboard: false
    });
    // listarSaldosProducto(id_producto);

    getSaldosPorAlmacen().then(function(data) {
        var table = document.getElementById("listaSaldos").tHead;
        table.parentNode.removeChild(table);
        document.getElementById("listaSaldos").createTHead();
        buildTableListaSaldosProducto(data,id_producto);
    });

}

function buildTableListaSaldosProducto(obj,id_producto){
    var table = document.getElementById("listaSaldos").tHead;
    var row = table.insertRow(0);
    row.insertCell(0).outerHTML  = '<th rowspan="2" hidden >Id</th>';
    row.insertCell(1).outerHTML  = '<th rowspan="2">Código</th>';
    row.insertCell(2).outerHTML  = '<th rowspan="2">Part Number</th>';
    row.insertCell(3).outerHTML  = '<th rowspan="2">Descripción</th>';
    row.insertCell(4).outerHTML  = '<th rowspan="2">Categoría</th>';
    row.insertCell(5).outerHTML  = '<th rowspan="2">SubCategoría</th>';
    let startTd =6;
    let firstElement = obj.data[0].stock_almacenes;
    
    for (let i = 0; i < firstElement.length; i++) {
        const almacen = firstElement[i].almacen_descripcion;
        row.insertCell(startTd).outerHTML  = '<th colspan="2">'+almacen+'</th>';
        startTd++;
    }
    row.insertCell(startTd).outerHTML  = '<th rowspan="2">Unid.medida</th>';
    row.insertCell(startTd+1).outerHTML  = '<th rowspan="2">id_item</th>';
    var row2 = table.insertRow(1);

    let cantidadAlmacenes = firstElement.length;
    let detallePorAlmacen = cantidadAlmacenes*2;
    for (let i = 0; i < detallePorAlmacen ; i++) {
        if(i%2 == 0 ){ //par
            row2.insertCell(i).outerHTML  = '<th>Stock</th>';
        }else{ //impar
            row2.insertCell(i).outerHTML  = '<th>Reserva</th>';
        }
    }


    // fillDataListaSaldos(obj);
    listarSaldosProducto(id_producto);
}

function listarSaldosProducto(id_producto){
    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'info':     false,
        'searching': false,
        'paging':   false,
        'bDestroy': true,
        'ajax': 'listar-saldos-por-almacen/'+id_producto,
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][0]['id_almacen'] == 1){
                            return row['stock_almacenes'][0]['stock'];
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][0]['id_almacen'] == 1){
                        return row['stock_almacenes'][0]['cantidad_reserva'];
                    }else{
                        return '-';
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][1]['id_almacen'] == 2){
                            return row['stock_almacenes'][1]['stock']
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][1]['id_almacen'] == 2){
                        return (row['stock_almacenes'][1]['cantidad_reserva']);
                    }else{
                        return '-';
                    }
                }
            },
            {'data': 'id_unidad_medida'},
            {'data': 'id_item'}
        ],
        'columnDefs': [{ 'aTargets': [0,10,11], 'sClass': 'invisible'}],
    });
}


function controlUnidadMedida(){
    var id_tipo_item = document.getElementsByName("id_tipo_item")[0].value;    
    var id_servicio = document.getElementsByName("id_servicio")[0].value;    
    var selectUnidadMedida = document.getElementsByName("unidad_medida_item");    
    // console.log(id_tipo_item);
    // console.log(id_servicio);
    if(id_tipo_item == 1){
        disabledControl(selectUnidadMedida,false);
    }
    if(id_tipo_item  == 2){
        disabledControl(selectUnidadMedida,true);

    }
    if(id_tipo_item == 3){
        disabledControl(selectUnidadMedida,true);
    }
}

function selectItem(){
        // detalleRequerimientoModal(event);
        let id_producto = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent;
        let id_servicio = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_servicio']").textContent;
        let id_equipo = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_equipo']").textContent;
        let id_tipo_item=null;
        if(id_producto >0){
            id_tipo_item = 1;
        }else if(id_servicio >0){
            id_tipo_item = 2;
        }else if(id_equipo >0){
            id_tipo_item = 3;
        }
        let id_cc_am_filas = null;
        let id_cc_venta_filas=null;
        if( tempDetalleItemCCSelect.hasOwnProperty('id_cc_am_filas')){
            id_cc_am_filas = tempDetalleItemCCSelect.id_cc_am_filas;
        }else if(tempDetalleItemCCSelect.hasOwnProperty('id_cc_venta_filas')){
            id_cc_venta_filas = tempDetalleItemCCSelect.id_cc_venta_filas;
        }
        let data_item_selected = {
            'id_detalle_requerimiento': null,
            'id_item': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent,
            'codigo': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent,
            'part_number': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent,
            'des_item': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent,
            'cantidad': tempDetalleItemCCSelect.cantidad?tempDetalleItemCCSelect.cantidad:1,
            'id_producto': parseInt(id_producto),
            'id_servicio': parseInt(id_servicio),
            'id_equipo': parseInt(id_equipo),
            'id_tipo_item': parseInt(id_tipo_item),
            'id_unidad_medida': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent,
            'categoria': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='categoria']").textContent,
            'subcategoria': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='subcategoria']").textContent,
            'precio_unitario':null,
            'subtotal':null,
            'id_tipo_moneda':1,
            'lugar_entrega':null,
            'id_partida':null,
            'cod_partida':null,
            'des_partida':null,
            'id_centro_costo':null,
            'codigo_centro_costo':null,
            'id_almacen_reserva':null,
            'almacen_descripcion':null,
            'id_cc_am_filas':id_cc_am_filas,
            'id_cc_venta_filas': id_cc_venta_filas,
            'tiene_transformacion':document.querySelector("form[id='form-requerimiento'] input[name='tiene_transformacion']").value,
            'estado':1
        };
        agregarItemATablaListaDetalleRequerimiento(data_item_selected);

        // let btnVerUltimasCompras = document.getElementsByName('btnVerUltimasCompras')[0];
        // btnVerUltimasCompras.removeAttribute('disabled');
        // btnVerUltimasCompras.setAttribute('class','btn btn-sm btn-default');

        // obtenerPromociones(id_producto,id_almacen,descripcion_producto);

        $('#modal-catalogo-items').modal('hide');
}

function getDataAllSelect() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: rutaObtenerGrupoSelectItemParaCpmpra,
            dataType: 'JSON',
            success(response) {
                resolve(response) // Resolve promise and go to then() 
            },
            error: function (err) {
                reject(err) // Reject the promise and go to catch()
            }
        });
    });
}


function agregarItemATablaListaDetalleRequerimiento(item){
     
    if(item.id_item > 0 && item.id_producto > 0){
        data_item.push(item);
        componerTdItemDetalleRequerimiento();
    }else{
        alert("lo siento, el item seleccionado no tiene un Id producto");
    }
}










function obtenerPromociones(id_producto,id_almacen,descripcion_producto){
    if(id_almacen > 0){
        $.ajax({
            type: 'GET',
            url: 'obtener-promociones/'+id_producto+'/'+id_almacen,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                objPromociones = response.data;
                if(response.data.length >0 ){
                    $('#modal-promocion-item').modal({
                        show: true,
                        backdrop: 'true'
                    });
                document.querySelector("div[id='modal-promocion-item'] strong[id='producto_descripcion']").innerText=descripcion_producto;
                
                var ul = document.querySelector("div[id='modal-promocion-item'] ul[id='productos_con_promocion']");
                while(ul.firstChild) {
                    ul.removeChild(ul.firstChild);
                }

                response.data.forEach(element => {
                    if(element.stock > 0){
                        var node = document.createElement("LI");
                        node.setAttribute('class','text-success');
                        var textnode = document.createTextNode(element.descripcion+' (Saldo disponible: '+element.stock+' Und.)');
                        node.appendChild(textnode);
                        ul.appendChild(node);
                    }else{
                        var node = document.createElement("LI");
                        node.setAttribute('class','text-danger');
                        var textnode = document.createTextNode(element.descripcion+' (No hay saldo en almacén)');
                        node.appendChild(textnode);
                        ul.appendChild(node);
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
}


function omitirPromocion(){
    $('#modal-promocion-item').modal('hide');
}

function agregarPromociones(){
    var ul = document.querySelector("div[id='modal-detalle-requerimiento'] ul[id='productos_con_promocion']");
    while(ul.firstChild) {
        ul.removeChild(ul.firstChild);
    }
    
    objPromociones.forEach(element => {

        if(element.stock >0){
            data_item.push({
                'id_promocion':element.id_promocion,
                'id_producto':element.id_producto,
                'id_producto_promocion':element.id_producto_promocion,
                'cod_item':element.codigo,
                'codigo_anexo':element.codigo_anexo,
                'part_number':element.part_number,
                'categoria':element.categoria,
                'subcategoria':element.subcategoria,
                'des_item':element.descripcion,
                'stock':element.stock,
                'id_unidad_medida':element.id_unidad_medida,
                'unidad':element.unidad_medida,
                'id_item':null,
                'id_tipo_item':null,
                'precio_unitario':null,
                'subtotal':null,
                'fecha_entrega':null,
                'id_partida':null,
                'id_centro_costo':null,
                'codigo_centro_costo':null,
                'cantidad':1,
                'id_almacen_reserva':null
    
            });
        }


        


        if(element.stock > 0){
            var node = document.createElement("LI");
            var textnode = document.createTextNode(element.descripcion);
            node.appendChild(textnode);
            ul.appendChild(node);
        }

    });

    document.querySelector("div[id='modal-detalle-requerimiento'] div[id='promocion_activa']").removeAttribute('hidden');
    // console.log(data_item);
    
    $('#modal-promocion-item').modal('hide');

}

function quitarPromocionAvtiva(){
    data_item=[];
    // console.log(data_item);
}



function limpiarFormularioDetalleRequerimiento(){
    $('[name=estado]').val('');
    $('[name=id_item]').val('');
    $('[name=part_number]').val('');
    $('[name=id_producto]').val('');
    $('[name=id_servicio]').val('');
    $('[name=id_equipo]').val('');
    $('[name=id_tipo_item]').val('');
    $('[name=id_detalle_requerimiento]').val('');
    $('[name=codigo_item]').val('');
    $('[name=descripcion_item]').val('');
    $('[name=unidad_medida_item]').val('');
    $('[name=cantidad_item]').val('');
    $('[name=precio_ref_item]').val('');
    $('[name=fecha_entrega_item]').val(new Date().toJSON().slice(0, 10));
    $('[name=lugar_entrega_item]').val('');
    $('[name=id_partida]').val('');
    $('[name=cod_partida]').val('');
    $('[name=des_partida]').val('');
}

function handleKeyDown(event){
    const key = event.key;
    if(key == 'Backspace' || key == 'Delete'){
        $('[name=id_item]').val(0);
        $('[name=codigo_item]').val('SIN CODIGO');
        $('[name=id_producto]').val(0);
        $('[name=id_servicio]').val(0);
        $('[name=id_equipo]').val(0);
    }

}

function handleKeyPress(event){    
    $('[name=id_item]').val(0);
    $('[name=codigo_item]').val('SIN CODIGO');
    $('[name=part_number]').val('');
    $('[name=id_producto]').val(0);
    $('[name=id_servicio]').val(0);
    $('[name=id_equipo]').val(0);

}
function handlePaste(event){    
    $('[name=id_item]').val(0);
    $('[name=codigo_item]').val('SIN CODIGO');
    $('[name=part_number]').val('');
    $('[name=id_producto]').val(0);
    $('[name=id_servicio]').val(0);
    $('[name=id_equipo]').val(0);

}