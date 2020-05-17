function procesar() {
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();

    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'procesar_planilla/' + empre + '/' + plani + '/' + mes,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function generar(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        if (plani == 1) {
            var periodo = $('#periodo option:selected').text();
            window.open('generar_planilla_pdf/'+empre+'/'+plani+'/'+mes+'/'+periodo);
        }else{
            alert('Solo Régimen Común puede generar Boleta de Pagos');
        }
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function reportePlanilla(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        var periodo = $('#periodo option:selected').text();
        window.open('reporte_planilla_xls/'+empre+'/'+plani+'/'+mes+'/'+periodo);
    }else{
        alert('Debe seleccionar todos los campos');
    }

}

function generarBoletaUnica(){
    $('#modal-plani-ind').modal({show: true, backdrop: 'static'});
    $('#modal-plani-ind').on('shown.bs.modal', function(){
        $('[name=name_empleado]').focus();
    });
}

function processBoleta(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();
    var empleado = $('[name=id_trabajador]').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0 && empleado > 0){
        var periodo = $('#periodo option:selected').text();
        window.open('reporte_planilla_trabajador_xls/'+empre+'/'+plani+'/'+mes+'/'+periodo+'/'+empleado);
    }else{
        alert('Debe seleccionar todos los campos');
    }
}