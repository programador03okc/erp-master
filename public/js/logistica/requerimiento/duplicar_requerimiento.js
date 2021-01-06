
function copiarDocumento(){
    var id = $('#id_requerimiento').text();
    var concepto = $('[name=concepto]').val();
    
    
    changeStateButton('historial');
    
    if(concepto.length !=0 || concepto != ''){
        if(id >0 ){
            $('#modal-copiar-documento').modal({
                show: true,
                backdrop: 'true'
            });
        }else{
            alert("No se seleccionó un requerimiento del historial");
        }

    }else{        
        alert("No escribió ningún concepto");
    }
}
function pasteDataOfModalToForm(){
    let concepto = document.getElementById('textConcepto').value;
    let fecha = document.getElementById('textFechaRequerimiento').value;
    let prioridad = document.getElementById('textPrioridad').value;
    let moneda = document.getElementById('textMoneda').value;
    let periodo = document.getElementById('textPeriodo').value;
    let empresa = document.getElementById('textEmpresa').value;
    let grupo = document.getElementById('textGrupo').value;
    // let area = document.getElementById('textArea').value;
    // let nombre_area = document.getElementById('textNombreArea').value;
    let rol = document.getElementById('textRolUsuario').value;
// console.log(rol);


    let mcd_concepto = document.querySelectorAll('input[name="concepto"]');
        mcd_concepto.forEach(function(item) {
            item.value=concepto;
});
    let mcd_fecha = document.querySelectorAll('input[name="fecha_requerimiento"]');
        mcd_fecha.forEach(function(item) {
            item.value=fecha;
});
    let mcd_prioridad = document.querySelectorAll('select[name="prioridad"]');
        mcd_prioridad.forEach(function(item) {
            item.value=prioridad;
});
    let mcd_moneda = document.querySelectorAll('input[name="moneda"]');
        mcd_moneda.forEach(function(item) {
            item.value=moneda;
});
    let mcd_periodo = document.querySelectorAll('select[name="periodo"]');
    mcd_periodo.forEach(function(item) {
        item.value=periodo;
});
    let mcd_empresa = document.querySelectorAll('select[name="empresa"]');
        mcd_empresa.forEach(function(item) {
            item.value=empresa;
});
    let mcd_grupo = document.querySelectorAll('input[name="id_grupo"]');
        mcd_grupo.forEach(function(item) {
            item.value=grupo;
});
//     let mcd_area = document.querySelectorAll('input[name="id_area"]');
//         mcd_area.forEach(function(item) {
//             item.value=area;
// });
//     let mcd_nombre_area = document.querySelectorAll('input[name="nombre_area"]');
//         mcd_nombre_area.forEach(function(item) {
//             item.value=nombre_area;
// });
    let mcd_rol_usuario = document.querySelectorAll('select[name="rol_usuario"]');
        mcd_rol_usuario.forEach(function(item) {
            item.value=rol;
});
}

function copiarDatosRequerimiento(){
    
    pasteDataOfModalToForm();

    var id = $('#id_requerimiento').text();

    baseUrl = rutaCopiarRequerimiento+'/'+id;
    let actual_id_usuario = userSession.id_usuario;
    let requerimiento = get_data_requerimiento();
    let detalle_requerimiento = data_item;

    requerimiento.id_usuario = actual_id_usuario; //update -> usuario actual
    // requerimiento.id_area = actual_id_area; // update -> id area actual
    // requerimiento.id_rol = actual_id_rol; // update -> id rol actual
    let data = {requerimiento,detalle:detalle_requerimiento};
    data.requerimiento.id_estado_doc =1  // estado elaborado 
    data.requerimiento.estado = 1  // estado 
    // console.log(data);

    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.status == 'OK'){
                alert("Copiado!, Se genero un nuevo requerimiento con código: "+ response.codigo_requerimiento);
                $('#modal-copiar-documento').modal('hide');
                mostrar_requerimiento(response.id_requerimiento);
            }else if(response.status=='NO_COPIADO'){
                alert("No se puede copiar el requerimiento.");
            }else{
                alert("ERROR");
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}