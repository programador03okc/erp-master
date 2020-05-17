let lista = [];

$(function(){
    vista_extendida();
});

function nueva_valorizacion(){
    propuestaModal('valorizacion');
    $("[name=modo]").val('new');
}

function mostrar_nueva_valorizacion(id_presup){
    $.ajax({
        type: 'GET',
        url: 'nueva_valorizacion/'+id_presup,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#numero').text('Val Nro.'+response['periodo'].numero);
            $('#total').text(formatNumber.decimal(response['periodo'].total,response['presup'].simbolo,-2));
            $('[name=fecha_inicio]').val(response['periodo'].fecha_inicio);
            $('[name=fecha_fin]').val(response['periodo'].fecha_fin);
            $('[name=id_residente]').val(response['presup'].id_residente);
            $('[name=nombre_residente]').val(response['presup'].nombre_residente);
            $('[name=numero]').val(response['periodo'].numero);
            $('[name=id_periodo]').val(response['periodo'].id_periodo);
            lista = response['lista'];
            mostrar_partidas_valorizacion('');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_valorizacion(id_valorizacion){
    $.ajax({
        type: 'GET',
        url: 'mostrar_valorizacion/'+id_valorizacion,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#numero').text('Val Nro.'+response['presup'].numero);
            $('#codigo').text(response['presup'].codigo);
            $('#total').text(formatNumber.decimal(response['total'], response['presup'].simbolo, -2));
            $('[name=fecha_valorizacion]').val(response['presup'].fecha_valorizacion);
            $('[name=fecha_inicio]').val(response['presup'].fecha_inicio);
            $('[name=fecha_fin]').val(response['presup'].fecha_fin);
            $('[name=id_residente]').val(response['presup'].id_residente);
            $('[name=nombre_residente]').val(response['presup'].nombre_residente);
            $('[name=nombre_opcion]').val(response['presup'].descripcion);
            $('[name=numero]').val(response['presup'].numero);
            // $('[name=id_periodo]').val(response['periodo'].id_periodo);
            lista = response['lista'];
            mostrar_partidas_valorizacion('');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

let total_actual = 0;

function mostrar_partidas_valorizacion(input){
    var html = '';
    var disabled = '';
    var type = $("#form-valorizacion").attr('type');
    var modo = $("[name=modo]").val();

    if (modo !== "new"){
        disabled = (type == "edition" ? "" : 'disabled="true"');
    }

    var acum_total = 0;
    var saldo = 0;
    var total_programado = 0;
    var total_anterior = 0;
    var total_acumulado = 0;
    var total_saldo = 0;
    total_actual = 0;
    var unitario = 0;
    var html_actual = '';
    var av_anterior = 0;

    lista.forEach(element => {
        if (element.importe_total == undefined){
            html += '<tr>'+
            '<td><strong>'+element.codigo+'</strong></td>'+
            '<td><strong>'+element.descripcion+'</strong></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td class="right"><strong>'+formatNumber.decimal(element.total,'',-2)+'</strong></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '</tr>';
            console.log(element.descripcion);

            element.partidas.forEach(partida => {
                av_anterior = (partida.avance_anterior !== null ? partida.avance_anterior : 0);
                acum_total = parseFloat(av_anterior) + parseFloat(partida.avance_actual);
                saldo = parseFloat(partida.metrado) - acum_total;
                unitario = parseFloat(partida.importe_unitario);
                console.log('unitario: '+unitario);
                total_programado += parseFloat(partida.importe_total);
                total_anterior += parseFloat(av_anterior * unitario);
                total_actual += parseFloat(partida.avance_actual * unitario);
                total_acumulado += acum_total * unitario;
                total_saldo += saldo * unitario;

                if (input == ''){
                    html_actual = '<td style="width:100px;"><input type="number" class="form-control activation right" '+
                    'style="width:100px;margin-right:0px;" '+disabled+' name="avance_actual" value="'+
                    formatDecimal(partida.avance_actual)+'" onBlur="changeAvanceActual('+partida.id_partida+');"/></td>';
                } else {
                    html_actual = '<td class="right">'+formatNumber.decimal(partida.avance_actual,'',-2)+'</td>';
                }

                html+='<tr id="'+partida.id_partida+'">'+
                '<td>'+partida.codigo+'</td>'+
                '<td>'+partida.descripcion+'</td>'+
                '<td>'+partida.abreviatura+'</td>'+
                '<td class="right">'+formatNumber.decimal(partida.metrado,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal(unitario,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal(partida.importe_total,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal(av_anterior,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal((av_anterior * unitario),'',-2)+'</td>'+
                html_actual+
                '<td class="right">'+formatNumber.decimal((partida.avance_actual * unitario),'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal(acum_total,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal((acum_total * unitario),'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal(saldo,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal((saldo * unitario),'',-2)+'</td>'+
                '</tr>';
            });
        }
        else {
            av_anterior = (element.avance_anterior !== null ? element.avance_anterior : 0);
            acum_total = parseFloat(av_anterior) + parseFloat(element.avance_actual);
            saldo = parseFloat(element.metrado) - acum_total;
            unitario = parseFloat(element.importe_unitario);

            total_programado += parseFloat(element.importe_total);
            total_anterior += parseFloat(av_anterior * unitario);
            total_actual += parseFloat(element.avance_actual * unitario);
            total_acumulado += acum_total * unitario;
            total_saldo += saldo * unitario;

            if (input == ''){
                html_actual = '<td style="width:100px;"><input type="number" class="form-control activation right" '+
                'style="width:100px;margin-right:0px;" '+disabled+' name="avance_actual" value="'+
                formatDecimal(element.avance_actual)+'" onBlur="changeAvanceActual('+element.id_partida+');"/></td>';
            } else {
                html_actual = '<td class="right">'+formatNumber.decimal(element.avance_actual,'',-2)+'</td>';
            }

            html+='<tr id="'+element.id_partida+'">'+
            '<td>'+element.codigo+'</td>'+
            '<td>'+element.descripcion+'</td>'+
            '<td>'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
            '<td class="right">'+formatNumber.decimal(element.metrado,'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal(unitario,'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal(element.importe_total,'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal(av_anterior,'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal((av_anterior * unitario),'',-2)+'</td>'+
            html_actual+
            '<td class="right">'+formatNumber.decimal((element.avance_actual * unitario),'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal(acum_total,'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal((acum_total * unitario),'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal(saldo,'',-2)+'</td>'+
            '<td class="right">'+formatNumber.decimal((saldo * unitario),'',-2)+'</td>'+
            '</tr>';
        }
    });

    $('#listaPartidas tbody').html(html);

    var html_foot = '<tr><td style="background: #e8e6e6;"></td>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<th style="background: #e8e6e6;" class="right">'+formatNumber.decimal(total_programado,'',-2)+'</th>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<th style="background: #e8e6e6;" class="right">'+formatNumber.decimal(total_anterior,'',-2)+'</th>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<th style="background: #e8e6e6;" class="right">'+formatNumber.decimal(total_actual,'',-2)+'</th>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<th style="background: #e8e6e6;" class="right">'+formatNumber.decimal(total_acumulado,'',-2)+'</th>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<th style="background: #e8e6e6;" class="right">'+formatNumber.decimal(total_saldo,'',-2)+'</th>'+
    '</tr>';

    $('#listaPartidas tfoot').html(html_foot);
}

function changeAvanceActual(id_partida){
    // var part;
    var actual = $('#'+id_partida+' td').find("input[name=avance_actual]").val();
    console.log('actual: '+actual);
    lista.forEach(function(element) {
        if (element.partidas !== undefined){
            element.partidas.forEach(part => {
                if (part.id_partida == id_partida){
                    part.avance_actual = parseFloat(actual);
                }
            });
            // part = element.partidas.find(partida => partida.id_partida == id_partida);
        } 
        else if (element.importe_total !== undefined && element.id_partida == id_partida){
            element.avance_actual = parseFloat(actual);
        }
    });
    console.log(lista);
    // if (part !== undefined){
    //     part.avance_actual = parseFloat(actual);
        mostrar_partidas_valorizacion('');
    // }
}

function exportTableToExcel(tableID, filename = ''){
    mostrar_partidas_valorizacion('sinInput');
    var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }

    var table = tableID;
    var name = filename;

    if (!table.nodeType) table = document.getElementById(table)
        var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
        window.location.href = uri + base64(format(template, ctx));
        window.location.download = filename;
        // window.location.click();
}

function listarPartidas(){
    var partidas = [];
    lista.forEach(function(element) {
        if (element.partidas == undefined && element.avance_actual !== undefined){
            partidas.push(element);
        } else {
            element.partidas.forEach(partida => {
                partidas.push(partida);
            });
        }
    });
    return partidas;
}

function save_valorizacion(){
    var id_valori = $('[name=id_valorizacion]').val();
    var id_pres = $('[name=id_presup]').val();
    var id_per = $('[name=id_periodo]').val();
    var num = $('[name=numero]').val();
    var res = $('[name=id_residente]').val();
    var fval = $('[name=fecha_valorizacion]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();
    var id_valori_par = [];
    var id_partida = [];
    var avance = [];
    
    var i = 0;
    var partidas = listarPartidas();

    partidas.forEach(function(element) {
        id_valori_par[i] = (element.id_valori_par !== undefined ? element.id_valori_par : 0);
        id_partida[i] = element.id_partida;
        avance[i] = element.avance_actual;
        ++i;
    });

    var data = 'id_valorizacion='+id_valori+
               '&id_presup='+id_pres+
               '&id_periodo='+id_per+
               '&numero='+num+
               '&id_residente='+res+
               '&fecha_valorizacion='+fval+
               '&fecha_inicio='+fini+
               '&fecha_fin='+ffin+
               '&total='+total_actual+
               '&id_valori_par='+id_valori_par+
               '&id_partida='+id_partida+
               '&avance_actual='+avance;
    console.log(data);
    var url = '';
    if (id_valori !== ''){
        url = 'update_valorizacion';
    } else {
        url = 'guardar_valorizacion';
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                if (id_valori == ""){
                    alert('Valorización registrada con éxito');
                } else {
                    alert('Valorización actualizada con éxito');
                }
                changeStateButton('guardar');
                $('#form-valorizacion').attr('type', 'register');
                changeStateInput('form-valorizacion', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_valorizacion(id_valorizacion){
    $.ajax({
        type: 'GET',
        url: 'anular_valorizacion/'+id_valorizacion,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Se anuló correctamente.');
            };
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}