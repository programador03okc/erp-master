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

function OpenModal(){
    $('#modal-csv').modal({show: true});
}

function Diario(){
    $('#inputDiario').attr('hidden', false);
    $('#id_empresa').focus();
}

function ProcesarDiario(){
    var empre = $('#id_empresa').val();
    var sede = $('#id_sede').val();
    var tipo = $('#tipo_planilla').val();
    var fecha = $('#fecha').val();
    $.ajax({
        type: 'GET',
        url: 'cargar_data_diaria/'+empre+'/'+sede+'/'+tipo+'/'+fecha,
        dataType: 'JSON',
        success: function(response){
            var dia = response.dia;
            $('#tablaDiario').attr('hidden', false);
            $('#tablaDiario tbody').html(Object.values(Object.values(response.hora)));
            $('#tablaDiario caption').html(response.button);
            $('[name=dia_sem]').val(dia);
            calcularDiario();
            resizeSide();
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function calcularDiario(){
    var dia = $('[name=dia_sem]').val();
    var filas = document.querySelectorAll("#tablaDiario tbody tr");
    var total = 0;
 
    filas.forEach(function(e){
        var columnas = e.querySelectorAll("td");
        var her = columnas[1].firstChild.value; //entrada reg
        var hsr = columnas[2].firstChild.value; // salida reg
        var hsa = columnas[3].firstChild.value; // salida alm
        var hea = columnas[4].firstChild.value; //entrada alm
        var hes = columnas[5].firstChild.value; //entrada sab
        var hss = columnas[6].firstChild.value; // salida sab

        var er = columnas[7].firstChild.value;
        var sa = columnas[8].firstChild.value;
        var ea = columnas[9].firstChild.value;
        var sr = columnas[10].firstChild.value;


        var hrfinal = '00:00';
        var hafinal = '00:00';

        if (dia != 6){
            if (er > her){
                hrfinal = restarHoras(her, er);
            }else{
                hrfinal = '00:00';
            }

            var talm = restarHoras(sa, ea);
            if (sa == '00:00' && ea == '00:00') {
                hafinal = '00:00';
            }else if(talm > '01:00'){
                hafinal = restarHoras('01:00', talm);
            }else{
                hafinal = '00:00';
            }
        }else{
            if (er > hes){
                if(hes == '00:00' || hss == '00:00'){
                    hrfinal = '00:00';
                }else{
                    hrfinal = restarHoras(hes, er);
                }
            }else{
                hrfinal = '00:00';
            }
        }

        columnas[11].firstChild.value = hrfinal;
        columnas[12].firstChild.value = hafinal;
        var ttotal = sumarHoras(columnas[11].firstChild.value, columnas[12].firstChild.value);
        columnas[13].textContent = ttotal;
    });
}

function Recargar(){
    var personal = new Array();
        $("input[name*='rrhh_id_trabajador']").each(function() {personal.push($(this).val());});
    var tipo = new Array();
        $("input[name*='rrhh_id_tipo_planilla']").each(function() {tipo.push($(this).val());});
    var entrada = new Array();
        $("input[name*='rrhh_ent_reg']").each(function() {entrada.push($(this).val());});
    var almuerzo_sal = new Array();
        $("input[name*='rrhh_sal_alm']").each(function() {almuerzo_sal.push($(this).val());});
    var almuerzo_ent = new Array();
        $("input[name*='rrhh_ent_alm']").each(function() {almuerzo_ent.push($(this).val());});
    var salida = new Array();
        $("input[name*='rrhh_sal_reg']").each(function() {salida.push($(this).val());});
    var tardIng = new Array();
        $("input[name*='rrhh_tar_ing']").each(function() {tardIng.push($(this).val());});
    var tardAlm = new Array();
        $("input[name*='rrhh_tar_alm']").each(function() {tardAlm.push($(this).val());});

    var fecha = $('#fecha').val();
    var empresa = $('#id_empresa').val();
    var sede = $('#id_sede').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'grabar_asistencia',
        data: 'personal='+personal+'&tipo='+tipo+'&entrada='+entrada+'&almuerzo_sal='+almuerzo_sal+'&almuerzo_ent='+almuerzo_ent+'&salida='+salida+'&fecha='+fecha+'&ting='+tardIng+'&talm='+tardAlm+'&empresa='+empresa+'&sede='+sede,
        dataType: 'JSON',
        success: function(response){
            alert('Los datos fueron actulizados..');
            $('#tablaDiario tbody tr').remove();
            ProcesarDiario();
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function modalReporte(){
    $('[name=from]').val('');
    $('[name=to]').val('');
    $('#modal-reporte').modal({show: true});
}

function downloadExcel(){
    var empre = 1;
    var sede = 1;
    var from = $('[name=from]').val();
    var to = $('[name=to]').val();
    window.open('reporte_tardanzas/'+from+'/'+to+'/'+empre+'/'+sede);
}


function sumarHoras(hini, hfin){
    horas1 = hini.split(":");
    horas2 =  hfin.split(":");
    horatotale=new Array();
    for(a = 0; a < 3; a++){
        horas1[a] = (isNaN(parseInt(horas1[a]))) ? 0 : parseInt(horas1[a])
        horas2[a] = (isNaN(parseInt(horas2[a]))) ? 0 : parseInt(horas2[a])
        horatotale[a] = (horas1[a] + horas2[a]);
    }
    horatotal = new Date();
    horatotal.setHours(horatotale[0]);
    horatotal.setMinutes(horatotale[1]);
    return horatotal.getHours()+":"+horatotal.getMinutes();
}

function restarHoras(inicio, fin){
    inicioMinutos = parseInt(inicio.substr(3,2));
    inicioHoras = parseInt(inicio.substr(0,2));

    finMinutos = parseInt(fin.substr(3,2));
    finHoras = parseInt(fin.substr(0,2));

    transcurridoMinutos = finMinutos - inicioMinutos;
    transcurridoHoras = finHoras - inicioHoras;

    if (transcurridoMinutos < 0) {
        transcurridoHoras--;
        transcurridoMinutos = 60 + transcurridoMinutos;
    }

    horas = transcurridoHoras.toString();
    minutos = transcurridoMinutos.toString();

    if (horas.length < 2) {
        horas = "0"+horas;
    }

    if (minutos.length < 2) {
        minutos = "0"+minutos;
    }

    var final = horas+":"+minutos;
    return final;
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

function cambiarEmpresaReport(value){
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
            $('#sede_rep').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function verPermisos(id, fecha){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'mostrar_permiso_asistencia/' + id + '/' + fecha,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#permi-tareo').html(response);
            $('#modal-permi-asist').modal({show: true});
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}