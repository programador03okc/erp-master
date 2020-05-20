var lista = [];
var periodos = [];

$(function(){
    vista_extendida();
});

function nuevo_cronovaleje(){
    $('#codigo').text('');
    $('#listaPartidas tbody').html('');
    presejeModal('crononuevo');
}

function valida_campos(){
    var msj = '';
    var id_pres = $('[name=id_presupuesto]').val();
    var numero = $('[name=numero]').val();
    var unid_program = $('[name=unid_program]').val();

    if (id_pres == ''){
        msj += 'Debe seleccionar un presupuesto!\n';
    }
    if (numero == ''){
        msj += 'Debe ingresar una cantidad!\n';
    } else if (numero < 0){
        msj += 'La cantidad debe ser > 0!\n';
    }
    if (unid_program == '0' || unid_program == ''){
        msj += 'Debe seleccionar una unidad de programación!\n';
    }
    return msj;
}

function mostrar_crono_valorizado(){
    var id_presupuesto = $('[name=id_presupuesto]').val();
    var modo = $('[name=modo]').val();
    if (modo == 'new'){
        nuevoCronoValorizado(id_presupuesto);
    } else {
        mostrarCronoValorizado(id_presupuesto);
    }
}

function nuevoCronoValorizado(id_presupuesto){
    var numero = parseFloat($('[name=numero]').val());
    var unid_program = $('[name=unid_program]').val();
    var nro_dias = 0;
    console.log('id_presupuesto: '+id_presupuesto);
    console.log('unid_program: '+unid_program);

    switch (unid_program) {
        case '1':
            nro_dias = numero;//dias
            break;
        case '2':
            nro_dias = numero * 7;//semanas
            break;
        case '3':
            nro_dias = numero * 15;//quincenas
            break;
        case '4':
            nro_dias = numero * 30;//meses
            break;
        case '5':
            nro_dias = numero * 365;//años
            break;
        default:
            break;
    }
    console.log('nro_dias: '+nro_dias);
    $.ajax({
        type: 'GET',
        url: 'nuevo_crono_valorizado/'+id_presupuesto,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var duracion_total = restarFechas(response['fecha_inicio'],response['fecha_fin']);
            $('#duracion').text(duracion_total+' días');
            $('#importe').text(formatNumber.decimal(response['sub_total'],response['moneda'],-2));

            if (duracion_total >= nro_dias){
                var array_periodo = [];
                var length = 0;
                
                if (nro_dias !== 0){
                    length = parseInt(duracion_total / nro_dias);
                }
                var periodo;
                var suma_rango = 0;
                var i;
                var fini = response['fecha_inicio'];
                var ffin;
                
                for (i=1;i <= length;i++) {
                    suma_rango += nro_dias;
                    ffin = suma_fecha(nro_dias, fini);
                    periodo = {
                        nro: i,
                        nro_dias: nro_dias,
                        dias: suma_rango,
                        fecha_inicio: fini,
                        fecha_fin: ffin
                    }
                    array_periodo.push(periodo);
                    fini = ffin;
                }
                var dif = duracion_total - suma_rango;
                if (dif > 0){
                    suma_rango += dif;
                    ffin = suma_fecha(dif, fini);
                    periodo = {
                        nro: i,
                        nro_dias: dif,
                        dias: suma_rango,
                        fecha_inicio: fini,
                        fecha_fin: ffin
                    }
                    array_periodo.push(periodo);
                }
                console.log(array_periodo);
                periodos = array_periodo;
            }
            else {
                alert('Debe colocar un rango menor que la duración total (<='+duracion_total+' días)');
            }
            lista = response['lista'];

            lista.forEach(element => {
                if (element.partidas !== undefined){
                    console.log(element.descripcion);
                    
                    element.partidas.forEach(partida => {
                        var importe_dia = 0;
                        if (partida.dias > 0){
                            importe_dia = parseFloat(partida.importe_parcial) / parseFloat(partida.dias);
                        }
                        var fini = partida.fecha_inicio;
                        var montos_periodos = [];

                        periodos.forEach(per => {
                            if (fini >= per.fecha_inicio &&
                                fini < per.fecha_fin){
                                
                                if (partida.fecha_fin < per.fecha_fin){
                                    ffin = partida.fecha_fin;
                                } else {
                                    ffin = per.fecha_fin;
                                }
                                var dias = restarFechas(fini, ffin);
                                var valor = importe_dia * dias;
                                
                                // var importe = formatNumber.decimal(valor,'',-2);
                                var porcen = valor * 100 / parseFloat(partida.importe_parcial);

                                var nuevo = {
                                    periodo: per.nro,
                                    porcentaje: porcen,
                                    importe: valor
                                }
                                montos_periodos.push(nuevo);
                                if (partida.fecha_fin > per.fecha_fin){
                                    fini = per.fecha_fin;
                                }
                            } else {
                                var nuevo = {
                                    periodo: per.nro,
                                    porcentaje: 0,
                                    importe: 0
                                }
                                montos_periodos.push(nuevo);
                            }
                        });

                        partida.periodos = montos_periodos;
                    });
                }
                else {
                    var importe_dia = 0;
                    var imp = 0;
                    if (element.dias >= 0){
                        imp = (element.tipo == 'ci' ? response['total_ci'] : response['total_gg']);
                        element.codigo = element.tipo.toUpperCase();
                        element.descripcion = (element.tipo == 'ci' ? 'COSTOS INDIRECTOS' : 'GASTOS GENERALES');
                        element.importe_parcial = parseFloat(imp);
                        importe_dia = parseFloat(imp) / parseFloat(element.dias);
                    }
                    var fini = element.fecha_inicio;
                    var montos_periodos = [];

                    periodos.forEach(per => {
                        if (fini >= per.fecha_inicio &&
                            fini < per.fecha_fin){
                            
                            if (element.fecha_fin < per.fecha_fin){
                                ffin = element.fecha_fin;
                            } else {
                                ffin = per.fecha_fin;
                            }
                            var dias = restarFechas(fini, ffin);
                            var valor = importe_dia * dias;
                            // var importe = formatNumber.decimal(valor,'',-2);
                            
                            var porcen = valor * 100 / parseFloat(element.importe_parcial);

                            var nuevo = {
                                periodo: per.nro,
                                porcentaje: porcen,
                                importe: valor
                            }
                            montos_periodos.push(nuevo);
                            if (element.fecha_fin > per.fecha_fin){
                                fini = per.fecha_fin;
                            }
                        } else {
                            var nuevo = {
                                periodo: per.nro,
                                porcentaje: 0,
                                importe: 0
                            }
                            montos_periodos.push(nuevo);
                        }
                    });
                    element.periodos = montos_periodos;
                }
            });
            console.log(lista);
            mostrar_tabla();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarCronoValorizado(id_presupuesto){
    $.ajax({
        type: 'GET',
        url: 'mostrar_crono_valorizado/'+id_presupuesto,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            
            var duracion_total = restarFechas(response['fecha_inicio'],response['fecha_fin']);
            $('#duracion').text(duracion_total + ' días');
            $('#importe').text(formatNumber.decimal(response['total'],response['moneda'],-2));
            $('[name=numero]').val(response['cantidad']);
            $('[name=unid_program]').val(response['unid_program']);
            
            var nro_dias = 0;
            var numero = response['cantidad'];

            switch (response['unid_program']) {
                case 1:
                    nro_dias = numero;//dias
                    break;
                case 2:
                    nro_dias = numero * 7;//semanas
                    break;
                case 3:
                    nro_dias = numero * 15;//quincenas
                    break;
                case 4:
                    nro_dias = numero * 30;//meses
                    break;
                case 5:
                    nro_dias = numero * 365;//años
                    break;
                default:
                    break;
            }
            console.log('nro_dias: '+nro_dias);
    
            if (duracion_total >= nro_dias){
                var array_periodo = [];
                var length = 0;
                
                if (nro_dias !== 0){
                    length = parseInt(duracion_total / nro_dias);
                }
                var periodo;
                var suma_rango = 0;
                var i;
                var fini = response['fecha_inicio'];
                var ffin;
                
                for (i=1;i <= length;i++) {
                    suma_rango += nro_dias;
                    ffin = suma_fecha(nro_dias, fini);
                    periodo = {
                        nro: i,
                        nro_dias: nro_dias,
                        dias: suma_rango,
                        fecha_inicio: fini,
                        fecha_fin: ffin
                    }
                    array_periodo.push(periodo);
                    fini = ffin;
                }
                var dif = duracion_total - suma_rango;
                if (dif > 0){
                    suma_rango += dif;
                    ffin = suma_fecha(dif, fini);
                    periodo = {
                        nro: i,
                        nro_dias: dif,
                        dias: suma_rango,
                        fecha_inicio: fini,
                        fecha_fin: ffin
                    }
                    array_periodo.push(periodo);
                }
                console.log(array_periodo);
                periodos = array_periodo;
                
                lista = response['lista'];
                
                console.log(lista);
                mostrar_tabla();
            }
            else {
                alert('Debe colocar un rango menor que la duración total (<='+duracion_total+' días)');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_tabla(){
    $('#listaPartidas thead').html('');
    $('#listaPartidas tbody').html('');
    $('#listaPartidas tfoot').html('');

    var html = '';
    var disabled = '';
    var type = $("#form-cronoval").attr('type');
    var modo = $("[name=modo]").val();

    if (modo !== "new"){
        disabled = (type == "edition" ? "" : 'disabled="true"');
    }

    var html_head = '<tr><th rowSpan="2">Código</th>'+
    '<th rowSpan="2">Descripción</th>'+
    '<th rowSpan="2" width="50">Total Días</th>'+
    '<th rowSpan="2" width="70">Montos Parciales</th>';
    var html_titu = '';
    var html_head2 = '<tr>';
    
    periodos.forEach(per=>{
        html_head +='<th colSpan="2" style="text-align: center;">'+per.nro+'°Val.</th>';
        html_head2 += '<th style="text-align: center;">%</th><th style="text-align: center;">S/.</th>';
        html_titu += '<td style="background: #d8fcfc;"></td><td style="background: #ffffb0;"></td>';
    });
    html_head2 +='</tr>';
    html_head += '</tr>'+html_head2;

    lista.forEach(element => {
        if (element.partidas !== undefined){
            html += '<tr>'+
            '<td><strong>'+element.codigo+'</strong></td>'+
            '<td><strong>'+element.descripcion+'</strong></td>'+
            '<td></td>'+
            '<td></td>'+html_titu+'<td></td>'+
            '</tr>';
            console.log(element.descripcion);
            
            element.partidas.forEach(partida => {
                var html_periodos = '';
                var sel;

                partida.periodos.forEach(per => {                
                    html_periodos += '<td style="background: #d8fcfc;">'+formatNumber.decimal(per.porcentaje,'',0)+'</td><td class="right" style="background: #ffffb0;">'+formatNumber.decimal(per.importe,'',-2)+'</td>';
                    sel = periodos.find(p => p.nro == per.periodo);
                    if (sel.total !== undefined){
                        sel.total = parseFloat(sel.total) + parseFloat(per.importe);
                    } else {
                        sel.total = parseFloat(per.importe);
                    }
                });

                html+='<tr>'+
                '<td>'+partida.codigo+'</td>'+
                '<td>'+partida.descripcion+'</td>'+
                '<td>'+partida.dias+'</td>'+
                '<td class="right">'+formatNumber.decimal(partida.importe_parcial,'',-2)+'</td>'+
                html_periodos+'<td></td>'+
                '</tr>';
            });
        }
        else {
            var html_periodos = '';
            var sel;

            element.periodos.forEach(per => {                
                html_periodos += '<td style="background: #d8fcfc;">'+formatNumber.decimal(per.porcentaje,'',-1)+'</td><td class="right" style="background: #ffffb0;">'+formatNumber.decimal(per.importe,'',-2)+'</td>';
                sel = periodos.find(p => p.nro == per.periodo);
                if (sel.total !== undefined){
                    sel.total = parseFloat(sel.total) + parseFloat(per.importe);
                } else {
                    sel.total = parseFloat(per.importe);
                }
            });

            html+='<tr>'+
            '<td>'+element.codigo+'</td>'+
            '<td>'+element.descripcion+'</td>'+
            '<td>'+element.dias+'</td>'+
            '<td class="right">'+formatNumber.decimal(element.importe_parcial,'',-2)+'</td>'+
            html_periodos+
            '</tr>';
            
        }
    });
    
    var html_foot = '<tr><td style="background: #e8e6e6;"></td>'+
    '<td style="background: #e8e6e6;"></td>'+
    '<th class="right" style="background: #e8e6e6;">Totales</th>';
    var foot = '';
    var total_todo = 0;
    
    periodos.forEach(per => {
        foot += '<th style="background: #e8e6e6;"></th><th class="right" style="background: #e8e6e6;">'+formatNumber.decimal((per.total > 0 ? per.total : 0),'',-2)+'</th>';
        total_todo += parseFloat(per.total);
    });
    html_foot +='<th class="right" style="background: #e8e6e6;">'+formatNumber.decimal(total_todo,'',-2)+'</th>'+foot+'</tr>';
    
    $('#listaPartidas thead').html(html_head);
    $('#listaPartidas tbody').html(html);
    $('#listaPartidas tfoot').html(html_foot);

}

function exportar_cronoval(){
    var id_presup = $('[name=id_presupuesto]').val();
    console.log('download_presupuesto id_presup: '+id_presup);
    if (id_presup !== ''){
        window.open('download_cronoval/'+id_presup);
    }
}

function exportTableToExcel(tableID, filename = ''){
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
        window.location.click();    
}

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}

function listarPartidas(){
    var partidas = [];
    lista.forEach(function(element) {
        if (element.partidas == undefined && element.importe_parcial !== undefined){
            partidas.push(element);
        } else {
            element.partidas.forEach(partida => {
                partidas.push(partida);
            });
        }
    });
    return partidas;
}

function save_cronovaleje(){
    var id_pcronoval = [];
    var id_pcronog = [];
    var periodo = [];
    var porcentaje = [];
    var importe = [];
    var id_pres = $('[name=id_presupuesto]').val();
    var modo = $('[name=modo]').val();
    var num = $('[name=numero]').val();
    var unid = $('[name=unid_program]').val();
    var pnro = [];
    var pnro_dias = [];
    var pdias = [];
    var pfini = [];
    var pffin = [];
    var ptotal = [];
    
    var i = 0;
    var partidas = listarPartidas();

    partidas.forEach(function(element) {
        element.periodos.forEach(function(per) {
            id_pcronoval[i] = (per.id_pcronoval !== undefined ? per.id_pcronoval : 0);
            id_pcronog[i] = element.id_pcronog;
            periodo[i] = per.periodo;
            porcentaje[i] = per.porcentaje;
            importe[i] = per.importe;
            ++i;
        });
    });

    i = 0;
    periodos.forEach(function(per) {
        pnro[i] = per.nro;
        pnro_dias[i] = per.nro_dias;
        pdias[i] = per.dias;
        pfini[i] = per.fecha_inicio;
        pffin[i] = per.fecha_fin;
        ptotal[i] = per.total;
        i++;
    });

    var data = 'id_pcronog='+id_pcronog+
               '&id_pcronoval='+id_pcronoval+
               '&id_presupuesto='+id_pres+
               '&periodo='+periodo+
               '&porcentaje='+porcentaje+
               '&importe='+importe+
               '&modo='+modo+
               '&cantidad='+num+
               '&unid_program='+unid+
               '&pnro='+pnro+
               '&pnro_dias='+pnro_dias+
               '&pdias='+pdias+
               '&pfini='+pfini+
               '&pffin='+pffin+
               '&ptotal='+ptotal;
    console.log(data);
    
    var msj = valida_campos();

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        $.ajax({
            type: 'POST',
            url: 'guardar_cronoval_presupuesto',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    if (modo == "new"){
                        alert('Cronograma registrado con éxito');
                    } else {
                        alert('Cronograma actualizado con éxito');
                    }
                    $('[name=numero]').attr('disabled',true);
                    $('[name=unid_program]').attr('disabled',true);
                    $('[name=btn_actualizar]').attr('disabled',true);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_cronovaleje(id_presupuesto){
    $.ajax({
        type: 'GET',
        url: 'anular_cronoval/'+id_presupuesto,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert("Se anuló correctamente.");
                $('#codigo').text('');
                $('#duracion').text('');
                $('#importe').text('');            
                $('#listaPartidas thead').html('');
                $('#listaPartidas tbody').html('');
                $('#listaPartidas tfoot').html('');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
