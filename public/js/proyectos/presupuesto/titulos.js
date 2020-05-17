$(function(){
    $("#form-propuesta_par_obs").on("submit", function(e){
        console.log('submit');
        e.preventDefault();
        var data = $(this).serialize();
        console.log(data);
        guardar_detalle_partida(data);
    });
});

function agregar_primer_titulo(){
    var id_pres = $('[name=id_presup]').val();
    console.log('id_presup: '+id_pres);
    if (id_pres !== ''){
        var titulo = prompt("Ingrese un nombre al título", "Ingrese un título...");
        if (titulo != null) {
            console.log('titulo:'+titulo);
            var i = 1;
            var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
            filas.forEach(function(e){
                var colum = e.querySelectorAll('td');
                var padre = colum[10].innerText;
                console.log('padre: '+padre);
                if (padre == ''){
                    i++;
                }
            });
            var data = 'id_presup='+id_pres+'&codigo='+leftZero(2,i)+
            '&descripcion='+titulo+'&cod_padre=';
            guardar_titulo(data, id_pres);
        }
    } else {
        alert('Debe seleccionar un ingresar un Presupuesto');
    }
}

function agregar_titulo(cod_padre){
    console.log('cod_padre'+cod_padre);
    var titulo = prompt("Ingrese un nombre al título", "Ingrese un título..");
    if (titulo != null) {
        var i = 1;
        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[10].innerText;
            var unid = colum[6].innerText;
            console.log('padre: '+padre+' unid: '+unid);
            if (padre == cod_padre && unid == ''){
                i++;
            }
        });
        console.log('i'+i);
        var id_pres = $('[name=id_presup]').val();
        console.log('id_pres:'+id_pres);
        var codigo = cod_padre+'.'+leftZero(2,i);
        var data =  'id_presup='+id_pres+'&codigo='+codigo+
                    '&descripcion='+titulo+'&cod_padre='+cod_padre;
        console.log('data: '+data);
        guardar_titulo(data, id_pres);
    } else {
        alert("No ha ingresado ningun valor.");
    }
}

function guardar_titulo(data, id_pres){
    var token = $('#token').val();
    var rspta = confirm("¿Esta seguro que desea guardar el titulo?");
    if (rspta){
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_titulo',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    // alert('Titulo registrado con éxito');
                    listar_partidas_propuesta(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function editar_titulo(id_titulo){
    $("#ti-"+id_titulo+" td").find("input[name=descripcion]").attr('disabled',false);
    $("#ti-"+id_titulo+" td").find("i.blue").removeClass('visible');
    $("#ti-"+id_titulo+" td").find("i.blue").addClass('oculto');
    $("#ti-"+id_titulo+" td").find("i.green").removeClass('oculto');
    $("#ti-"+id_titulo+" td").find("i.green").addClass('visible');
}

function update_titulo(id_titulo){
    var des = $("#ti-"+id_titulo+" td").find("input[name=descripcion]").val();
    var data =  'id_titulo='+id_titulo+
                '&descripcion='+des;
    var id_pres = $('[name=id_presup]').val();
    
    $("#ti-"+id_titulo+" td").find("input[name=descripcion]").attr('disabled',true);
    $("#ti-"+id_titulo+" td").find("i.blue").removeClass('oculto');
    $("#ti-"+id_titulo+" td").find("i.blue").addClass('visible');
    $("#ti-"+id_titulo+" td").find("i.green").removeClass('visible');
    $("#ti-"+id_titulo+" td").find("i.green").addClass('oculto');
    
    $.ajax({
        type: 'POST',
        url: 'update_titulo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Se actualizó correctamente.');
                // listar_partidas_propuesta(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_titulo(id_titulo,codigo){
    var anula = confirm("¿Esta seguro que desea anular éste titulo?");
    if (anula){
        var cod_padre = '';
        var hijos_com = [];
        var hijos_par = [];
        var i = 0;

        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        filas.forEach(function(e){
            var ids = (e.id).split('-');
            var colum = e.querySelectorAll('td');
            cod_padre = colum[10].innerText;
            console.log('cod_padre'+cod_padre);
            
            if (cod_padre === codigo){
                if (ids[0] === "ti"){
                    hijos_com[i] = ids[1];
                } 
                else if (ids[0] === "par"){
                    hijos_par[i] = ids[1];
                }
                i++;
            }
        });
        var rspta = true;
        if (hijos_com.length > 0 || hijos_par.length > 0){
            rspta = confirm("Este titulo tiene dependientes. \n¿Está seguro que desea anularlo con sus dependientes?");
        }
        if (rspta) {
            var id_pres = $('[name=id_presup]').val();
            var data =  'id_titulo='+id_titulo+
                        '&cod_padre='+cod_padre+
                        '&id_pres='+id_pres+
                        '&hijos_com='+hijos_com+
                        '&hijos_par='+hijos_par;
            console.log(data);
    
            $.ajax({
                type: 'POST',
                url: 'anular_titulo',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Titulo anulado con éxito');
                        listar_partidas_propuesta(id_pres);
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    }
}

/////Partidas
function agregar_partida(cod_padre){
    var i = 1;
    var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        var padre = colum[10].innerText;
        var imp = colum[6].innerText;
        if (padre == cod_padre && imp !== null){
            i++;
        }
    });
    var codigo = cod_padre+'.'+leftZero(2,i);
    guardar_partida(codigo, cod_padre);
}

function guardar_partida(codigo, cod_padre){
    var id_pres = $('[name=id_presup]').val();
    var data = 'id_presup='+id_pres+
            '&codigo='+codigo+
            '&id_pardet='+
            '&cod_padre='+cod_padre;
    
    console.log(data);
    var baseUrl = 'guardar_partida';
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Partida registrada con éxito');
                listar_partidas_propuesta(id_pres);
                $('#modal-partidaEstructura').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function editar_partida(id_partida){
    $("#par-"+id_partida+" td").find("input[name=descripcion]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("input[name=metrado]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("input[name=importe_unitario]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("input[name=importe_total]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("input[name=porcentaje_utilidad]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("input[name=importe_utilidad]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("select[name=unidad_medida]").attr('disabled',false);
    $("#par-"+id_partida+" td").find("i.blue").removeClass('visible');
    $("#par-"+id_partida+" td").find("i.blue").addClass('oculto');
    $("#par-"+id_partida+" td").find("i.green").removeClass('oculto');
    $("#par-"+id_partida+" td").find("i.green").addClass('visible');
}

function update_partida(id_partida){
    var desc = $("#par-"+id_partida+" td").find("input[name=descripcion]").val();
    var metr = $("#par-"+id_partida+" td").find("input[name=metrado]").val();
    var unit = $("#par-"+id_partida+" td").find("input[name=importe_unitario]").val();
    var total = $("#par-"+id_partida+" td").find("input[name=importe_total]").val();
    var porut = $("#par-"+id_partida+" td").find("input[name=porcentaje_utilidad]").val();
    var imput = $("#par-"+id_partida+" td").find("input[name=importe_utilidad]").val();
    var unid = $("#par-"+id_partida+" td").find("select[name=unidad_medida]").val();
    var id_pres = $('[name=id_presup]').val();

    var padre = $("#par-"+id_partida+" td")[10].innerText;
    console.log('padre: '+padre);

    var data =  'id_partida='+id_partida+
                '&id_presup='+id_pres+
                '&descripcion='+desc+
                '&metrado='+metr+
                '&importe_unitario='+unit+
                '&unidad_medida='+unid+
                '&importe_total='+total+
                '&porcentaje_utilidad='+porut+
                '&importe_utilidad='+imput+
                '&cod_padre='+padre;
    console.log('data: '+data);

    $("#par-"+id_partida+" td").find("input").attr('disabled',true);
    $("#par-"+id_partida+" td").find("select").attr('disabled',true);
    $("#par-"+id_partida+" td").find("i.blue").removeClass('oculto');
    $("#par-"+id_partida+" td").find("i.blue").addClass('visible');
    $("#par-"+id_partida+" td").find("i.green").removeClass('visible');
    $("#par-"+id_partida+" td").find("i.green").addClass('oculto');

    $.ajax({
        type: 'POST',
        url: 'update_partida_propuesta',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response['data'] > 0){
                // listar_partidas_propuesta(id_pres);
                if (response['totales'] !== null){
                    $('[name=sub_total]').val(formatDecimal(response['totales'].sub_total));
                    $('[name=porcen_utilidad]').val(formatDecimal(response['totales'].porcen_utilidad));
                    $('[name=impor_utilidad]').val(formatDecimal(response['totales'].importe_utilidad));
                    $('[name=total]').val(formatDecimal(parseFloat(response['totales'].sub_total) + parseFloat(response['totales'].importe_utilidad)));
                    $('[name=porcen_igv]').val(formatDecimal(response['totales'].porcen_igv));
                    $('[name=importe_igv]').val(formatDecimal(response['totales'].importe_igv));
                    $('[name=total_propuesta]').val(formatDecimal(response['totales'].total_propuesta));    
                }
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_partida(id_partida){
    var id_pres = $('[name=id_presup]').val();
    $.ajax({
        type: 'GET',
        url: 'anular_partida/'+id_partida,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                listar_partidas_propuesta(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function calcular_total(id_partida){
    var metr = $("#par-"+id_partida+" td").find("input[name=metrado]").val();
    var unit = $("#par-"+id_partida+" td").find("input[name=importe_unitario]").val();
    if (metr !== '' && unit !== ''){
        var tot = parseFloat(metr) * parseFloat(unit);
        $("#par-"+id_partida+" td").find("input[name=importe_total]").val(tot);
    }
}

function change_total(id_partida){
    $("#par-"+id_partida+" td").find("input[name=metrado]").val('0');
    $("#par-"+id_partida+" td").find("input[name=importe_unitario]").val('0');
}

function change_importe_utilidad_det(id_partida){
    $("#par-"+id_partida+" td").find("input[name=porcentaje_utilidad]").val(0);
    // change_utilidad_det(id_partida);
}

function change_utilidad_det(id_partida){
    var porc = $("#par-"+id_partida+" td").find("input[name=porcentaje_utilidad]").val();
    // var impo = $("#par-"+id_partida+" td").find("input[name=importe_utilidad]").val();
    var tota = $("#par-"+id_partida+" td").find("input[name=importe_total]").val();

    if (porc !== '' && porc > 0){
        var uti = tota * porc / 100;
        $("#par-"+id_partida+" td").find("input[name=importe_utilidad]").val(uti);
    }
}

function subir_partida(id_partida){
    var id_pres = $('[name=id_presup]').val();
    $.ajax({
        type: 'GET',
        url: 'subir_partida/'+id_partida,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                listar_partidas_propuesta(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function bajar_partida(id_partida){
    var id_pres = $('[name=id_presup]').val();
    $.ajax({
        type: 'GET',
        url: 'bajar_partida/'+id_partida,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                listar_partidas_propuesta(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

//Detalle de la Partida
function detalle_partida(id_partida){
    $('#modal-propuesta_par_obs').modal({
        show: true
    });
    $('[name=id_partida_obs]').val(id_partida);

    if (id_partida !== null && id_partida !== ''){
        $.ajax({
            type: 'GET',
            url: 'mostrar_detalle_partida/'+id_partida,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response !== 0){
                    $('[name=par_descripcion]').val(response.descripcion);
                    $('[name=update]').val(1);
                } else {
                    $('[name=update]').val(0);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function guardar_detalle_partida(data){
    var update = $('[name=update]').val();
    console.log('update'+update);
    var url = '';
    if (update == 1){
        url = 'update_detalle_partida';
    } else {
        url = 'guardar_detalle_partida';
    }
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Se guardó correctamente!');
                $('#modal-propuesta_par_obs').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}