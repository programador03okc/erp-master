$(function(){
    $('#formPage').on('submit', function(){
        var formData = new FormData($('#formPage')[0]);
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'cargar_csv',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response.status == 'ok'){
                    alert('Se cargo el archivo con exito');
                    $('#formPage')[0].reset();
                    $('#modal-csv').modal('hide');
                }else{
                    alert('Error al cargar el archivo');
                }
            }
        });
        return false;
    });

    resizeSide();
});

function ProcesarDiario(){
    var empre = $('#id_empresa').val();
    var sede = $('#id_sede').val();
    var tipo = $('#tipo_planilla').val();
    var fecha1 = $('#fecha1').val();
    var fecha2 = $('#fecha2').val();
    $.ajax({
        type: 'GET',
        url: 'cargar_asistencia/'+empre+'/'+sede+'/'+tipo+'/'+fecha1+'/'+fecha2,
        dataType: 'JSON',
        success: function(response){
            $('#reporte-visual').html(response);
            resizeSide();
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function generarAsist(){
    var fecha = $('#fecha2').val();
    var empresa = $('#id_empresa').val();
    var sede = $('#id_sede').val();
    var tipo = $('#tipo_planilla').val();
    
    var personal = new Array();
    $("input[name*='id_trabajador']").each(function() {personal.push($(this).val());});
    var dia1 = new Array();
    $("input[name*='dia01']").each(function() {dia1.push($(this).val());});
    var dia2 = new Array();
    $("input[name*='dia02']").each(function() {dia2.push($(this).val());});
    var dia3 = new Array();
    $("input[name*='dia03']").each(function() {dia3.push($(this).val());});
    var dia4 = new Array();
    $("input[name*='dia04']").each(function() {dia4.push($(this).val());});
    var dia5 = new Array();
    $("input[name*='dia05']").each(function() {dia5.push($(this).val());});
    var dia6 = new Array();
    $("input[name*='dia06']").each(function() {dia6.push($(this).val());});
    var dia7 = new Array();
    $("input[name*='dia07']").each(function() {dia7.push($(this).val());});
    var dia8 = new Array();
    $("input[name*='dia08']").each(function() {dia8.push($(this).val());});
    var dia9 = new Array();
    $("input[name*='dia09']").each(function() {dia9.push($(this).val());});
    var dia10 = new Array();
    $("input[name*='dia10']").each(function() {dia10.push($(this).val());});
    var dia11 = new Array();
    $("input[name*='dia11']").each(function() {dia11.push($(this).val());});
    var dia12 = new Array();
    $("input[name*='dia12']").each(function() {dia12.push($(this).val());});
    var dia13 = new Array();
    $("input[name*='dia13']").each(function() {dia13.push($(this).val());});
    var dia14 = new Array();
    $("input[name*='dia14']").each(function() {dia14.push($(this).val());});
    var dia15 = new Array();
    $("input[name*='dia15']").each(function() {dia15.push($(this).val());});
    var dia16 = new Array();
    $("input[name*='dia16']").each(function() {dia16.push($(this).val());});
    var dia17 = new Array();
    $("input[name*='dia17']").each(function() {dia17.push($(this).val());});
    var dia18 = new Array();
    $("input[name*='dia18']").each(function() {dia18.push($(this).val());});
    var dia19 = new Array();
    $("input[name*='dia19']").each(function() {dia19.push($(this).val());});
    var dia20 = new Array();
    $("input[name*='dia20']").each(function() {dia20.push($(this).val());});
    var dia21 = new Array();
    $("input[name*='dia21']").each(function() {dia21.push($(this).val());});
    var dia22 = new Array();
    $("input[name*='dia22']").each(function() {dia22.push($(this).val());});
    var dia23 = new Array();
    $("input[name*='dia23']").each(function() {dia23.push($(this).val());});
    var dia24 = new Array();
    $("input[name*='dia24']").each(function() {dia24.push($(this).val());});
    var dia25 = new Array();
    $("input[name*='dia25']").each(function() {dia25.push($(this).val());});
    var dia26 = new Array();
    $("input[name*='dia26']").each(function() {dia26.push($(this).val());});
    var dia27 = new Array();
    $("input[name*='dia27']").each(function() {dia27.push($(this).val());});
    var dia28 = new Array();
    $("input[name*='dia28']").each(function() {dia28.push($(this).val());});
    var dia29 = new Array();
    $("input[name*='dia29']").each(function() {dia29.push($(this).val());});
    var dia30 = new Array();
    $("input[name*='dia30']").each(function() {dia30.push($(this).val());});
    var dia31 = new Array();
    $("input[name*='dia31']").each(function() {dia31.push($(this).val());});
    var tardanza = new Array();
    $("input[name*='minutos']").each(function() {tardanza.push($(this).val());});
    var descuento = new Array();
    $("input[name*='descuentos']").each(function() {descuento.push($(this).val());});
    var inasistencia = new Array();
    $("input[name*='inasistencia']").each(function() {inasistencia.push($(this).val());});

    var data = 'empresa='+empresa+'&sede='+sede+'&tipo='+tipo+'&fecha='+fecha+'&personal='+personal+'&dia1='+dia1+'&dia2='+dia2+'&dia3='+dia3+'&dia4='+dia4+'&dia5='+dia5+'&dia6='+dia6+'&dia7='+dia7+'&dia8='+dia8+'&dia9='+dia9+'&dia10='+dia10+'&dia11='+dia11+'&dia12='+dia12+'&dia13='+dia13+'&dia14='+dia14+'&dia15='+dia15+'&dia16='+dia16+'&dia17='+dia17+'&dia18='+dia18+'&dia19='+dia19+'&dia20='+dia20+'&dia21='+dia21+'&dia22='+dia22+'&dia23='+dia23+'&dia24='+dia24+'&dia25='+dia25+'&dia26='+dia26+'&dia27='+dia27+'&dia28='+dia28+'&dia29='+dia29+'&dia30='+dia30+'&dia31='+dia31+'&tardanza='+tardanza+'&descuento='+descuento+'&inasistencia='+inasistencia;
    
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'grabar_asistencia_final',
        data: data,
        dataType: 'JSON',
        success: function(response){
            alert('Los datos fueron actulizados..');
            $('#reporte-visual').empty();
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cambiarEmpresa(value){
    baseUrl = 'mostrar_combos_emp/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var sedes = response.sedes;
            var htmls = '<option value="0" selected disabled>Elija una opción</option>';
            Object.keys(sedes).forEach(function (key){
                htmls += '<option value="'+sedes[key]['id_sede']+'">'+sedes[key]['descripcion']+'</option>';
            })
            $('#id_sede').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function cambiarEmpresaReport(value){
//     baseUrl = 'mostrar_combos_emp/'+value;
//     $.ajax({
//         type: 'GET',
//         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//         url: baseUrl,
//         dataType: 'JSON',
//         success: function(response){
//             var sedes = response.sedes;
//             var htmls = '<option value="0" selected disabled>Elija una opción</option>';
//             Object.keys(sedes).forEach(function (key){
//                 htmls += '<option value="'+sedes[key]['id_sede']+'">'+sedes[key]['descripcion']+'</option>';
//             })
//             $('#sede_rep').html(htmls);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }