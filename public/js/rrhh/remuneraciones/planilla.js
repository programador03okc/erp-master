// function procesar(){
//     var empre = $('#id_empresa').val();
//     var plani = $('#id_tipo_planilla').val();
//     var mes = $('#mes').val();

//     $.ajax({
//         type: 'GET',
//         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//         url: 'procesar_planilla/' + empre + '/' + plani + '/' + mes,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function generar(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        if (plani == 1){
            var periodo = $('#periodo option:selected').text();
            window.open('generar_planilla_pdf/'+empre+'/'+plani+'/'+mes+'/'+periodo);
        }else{
            alert('Solo Régimen Común puede generar Boleta de Pagos');
        }
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function generarSPCC() {
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        if (plani == 1){
            var periodo = $('#periodo option:selected').text();
            window.open('generar_planilla_spcc_pdf/'+empre+'/'+plani+'/'+mes+'/'+periodo);
        }else{
            alert('Solo Régimen Común puede generar Boleta de Pagos');
        }
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function enviarBoleta(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        if (plani == 1){
            var periodo = $('#periodo option:selected').text();
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: 'generar_pdf_trabajdor/' + empre + '/' + plani + '/' + mes + '/' + periodo,
                dataType: 'JSON',
                success: function(response){
                    $('#modal-correos').modal('toggle');
                    $('#ul-si').removeClass('oculto');
                    $('#ul-no').removeClass('oculto');

                    if ((response.si).length > 0){
                        $('#ul-si').html(response.si);
                    }else{
                        $('#ul-si').html('No hay envíos exitosos');
                    }
                    if ((response.no).length > 0){
                        $('#ul-no').html(response.no);
                    }else{
                        $('#ul-no').html('No hay envíos fallidos');
                    }
                    console.log(response);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert('Solo Régimen Común puede generar Boleta de Pagos');
        }
    }else{
        alert('Debe seleccionar los 4 primeros campos');
    }
}

function reportePlanillaGrupal(){
    var grupo = $('#nameGrupo').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();
    
    if (grupo != null){
        if (plani > 0){
            if (mes > 0){
                if (perio > 0){
                    var periodo = $('#periodo option:selected').text();
                    window.open('reporte_planilla_grupal_xls/'+plani+'/'+mes+'/'+periodo+'/'+grupo);
                }else{
                    alert('Debe seleccionar el periodo');
                    $('#periodo').focus();
                }
            }else{
                alert('Debe seleccionar el mes');
                $('#mes').focus();
            }
        }else{
            alert('Debe seleccionar el tipo de planilla');
            $('#id_tipo_planilla').focus();
        }
    }else{
        alert('Debe seleccionar la gerencia');
        $('#nameGrupo').focus();
    }
}

function reportePlanilla(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        var periodo = $('#periodo option:selected').text();
        window.open('reporte_planilla_xls/'+empre+'/'+plani+'/'+mes+'/'+periodo+'/1/0');
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function reportePlanillaSPCC(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        var periodo = $('#periodo option:selected').text();
        window.open('reporte_planilla_spcc_xls/'+empre+'/'+plani+'/'+mes+'/'+periodo);
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
        window.open('reporte_planilla_trabajador_pdf/'+empre+'/'+plani+'/'+mes+'/'+periodo+'/'+empleado);
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function reporteGastos(){
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (plani > 0){
        if (mes > 0){
            if (perio > 0){
                var periodo = $('#periodo option:selected').text();
                window.open('reporte_gastos/'+plani+'/'+mes+'/'+periodo);
            }else{
                alert('Debe seleccionar el periodo');
                $('#periodo').focus();
            }
        }else{
            alert('Debe seleccionar el mes');
            $('#mes').focus();
        }
    }else{
        alert('Debe seleccionar el tipo de planilla');
        $('#id_tipo_planilla').focus();
    }
}