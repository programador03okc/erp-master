function controlModal(){
    var id = $('[name="id_asignacion"]').val();
    if (id !== '') {
        $('#modal-control').modal({
            show:true
        });
        var chofer = $('[name=id_trabajador]').val();
        var id_equipo = $('[name=id_equipo]').val();
        
        $('[name=chofer]').val(chofer);
        $('[name=id_control]').val('');
        $('[name=fecha_recorrido]').val(fecha_actual());
        $('[name=hora_inicio]').val('');
        $('[name=hora_fin]').val('');
        $('[name=kilometraje_inicio]').val('');
        $('[name=kilometraje_fin]').val('');
        $('[name=descripcion_recorrido]').val('');
        $('[name=importe]').val('');
        $('[name=galones]').val('');
        $('[name=grifo]').val('');
        $('[name=precio_unitario]').val('');
        $('[name=observaciones]').val('');

        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'kilometraje_actual/'+id_equipo,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=kilometraje_inicio]').val(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }else{
        alert('Debe seleccionar una asignación');
    }
}
function editar_detalle(id_control){
    controlModal();
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_control/'+id_control,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_control]').val(response[0].id_control);
            $('[name=id_asignacion]').val(response[0].id_asignacion);
            $('[name=chofer]').val(response[0].chofer);
            $('[name=fecha_recorrido]').val(response[0].fecha_recorrido);
            $('[name=hora_inicio]').val(response[0].hora_inicio);
            $('[name=hora_fin]').val(response[0].hora_fin);
            $('[name=kilometraje_inicio]').val(response[0].kilometraje_inicio);
            $('[name=kilometraje_fin]').val(response[0].kilometraje_fin);
            $('[name=descripcion_recorrido]').val(response[0].descripcion_recorrido);
            $('[name=importe]').val(response[0].importe);
            $('[name=observaciones]').val(response[0].observaciones);
            $('[name=grifo]').val(response[0].grifo);
            $('[name=precio_unitario]').val(response[0].precio_unitario);
            $('[name=galones]').val(response[0].galones);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_detalle(id_control){
    var rspta = confirm('Esta seguro que desea anular éste detalle');
    if (rspta){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_control/'+id_control,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                alert('Detalle anulado con éxito');
                var id_asignacion = $('[name=id_asignacion]').val();
                console.log('id_asignacion'+id_asignacion);
                listar_controles(id_asignacion);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function calcula_galones(){
    var imp = $('[name=importe]').val();
    var pre = $('[name=precio_unitario]').val();
    if (imp !== '' && pre !== ''){
        var num = parseFloat(imp)/pre;
        $('[name=galones]').val(num.toFixed(2));
    }
}
function guardar_control(){
    var id = $('[name=id_control]').val();
    var asig = $('[name=id_asignacion]').val();
    var chofer = $('[name=chofer]').val();
    var fecha = $('[name=fecha_recorrido]').val();
    var hini = $('[name=hora_inicio]').val();
    var hfin = $('[name=hora_fin]').val();
    var kini = $('[name=kilometraje_inicio]').val();
    var kfin = $('[name=kilometraje_fin]').val();
    var des = $('[name=descripcion_recorrido]').val();
    var imp = $('[name=importe]').val();
    var gal = $('[name=galones]').val();
    var grifo = $('[name=grifo]').val();
    var uni = $('[name=precio_unitario]').val();
    var obs = $('[name=observaciones]').val();
    var id_equipo = $('[name=id_equipo]').val();

    if (kini !== ''){
        if (kfin !== ''){
            if (hini !== ''){
                if (hfin !== ''){
                    if (des !== ''){
                        var data = 'id_control='+id+
                            '&id_asignacion='+asig+
                            '&chofer='+chofer+
                            '&fecha_recorrido='+fecha+
                            '&hora_inicio='+hini+
                            '&hora_fin='+hfin+
                            '&kilometraje_inicio='+kini+
                            '&kilometraje_fin='+kfin+
                            '&descripcion_recorrido='+des+
                            '&importe='+imp+
                            '&galones='+gal+
                            '&grifo='+grifo+
                            '&precio_unitario='+uni+
                            '&observaciones='+obs+
                            '&id_equipo='+id_equipo;
            
                        var token = $('#token').val();
                        var baseUrl;
                        console.log(data);
                        if (id !== ''){
                            baseUrl = 'actualizar_control';
                        } else {
                            baseUrl = 'guardar_control';
                        }
                        $.ajax({
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': token},
                            url: baseUrl,
                            data: data,
                            dataType: 'JSON',
                            success: function(response){
                                console.log(response);
                                if (response > 0){
                                    alert('Control registrado con éxito');
                                    var id_asignacion = $('[name=id_asignacion]').val();
                                    console.log('id_asignacion'+id_asignacion);
                                    listar_controles(id_asignacion);
                                    $('#modal-control').modal('hide');
                                }
                            }
                        }).fail( function( jqXHR, textStatus, errorThrown ){
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                        });
                    } else {
                        alert('Debe ingresar la descripcion del recorrido');
                    }
                } else {
                    alert('Debe ingresar la hora fin');
                }
            } else {
                alert('Debe ingresar la hora inicio');
            }
        } else {
            alert('Debe ingresar el kilometraje fin');
        }
    } else {
        alert('Debe ingresar el kilometraje inicio');
    }
}
function listar_controles(id_asignacion){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_controles/'+id_asignacion,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#detalle tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function valida_kilometraje(value){
    console.log(value);
    var kilometraje_inicio = parseFloat($('[name=kilometraje_inicio]').val());
    if (value < kilometraje_inicio){
        alert('El Kilometraje fin debe ser mayor al kilometraje de inicio');
        $('[name=kilometraje_fin]').val('');
    }
}
function valida_hora(value){
    var hora_inicio = $('[name=hora_inicio]').val();
    if (value < hora_inicio){
        alert('El hora fin debe ser mayor al hora de inicio');
        $('[name=hora_fin]').val('');
    }
}
function open_ver(){
    var id_asignacion = $('[name=id_asignacion]').val();
    if (id_asignacion !== ''){
        var id = encode5t(id_asignacion);
        window.open('imprimir_solicitud/'+id);
    } else {
        alert('Debe seleccionar una Asignación de Equipo.');
    }
}
function downloadControlBitacora(){
    var id_asignacion = $('[name=id_asignacion]').val();
    console.log('id_asignacion:'+id_asignacion);
    if (id_asignacion !== ''){
        open_fechas();
    } else {
        alert('Debe seleccionar una Asignación de Equipo.');
    }
}
function open_fechas(){
    $('#modal-fechas').modal({
        show: true
    });
    $('[name=f_fecha_inicio]').val(fecha_actual());
    $('[name=f_fecha_fin]').val(fecha_actual());
}
function enviar_fechas(){
    // var id_solicitud = $('[name=id_solicitud]').val();
    var id_asignacion = $('[name=id_asignacion]').val();
    var fini = $('[name=f_fecha_inicio]').val();
    var ffin = $('[name=f_fecha_fin]').val();
    var id = encode5t(id_asignacion);
    window.open('download_control_bitacora/'+id+'/'+fini+'/'+ffin);
    $('#modal-fechas').modal('hide');
}