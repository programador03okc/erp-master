$(function(){
    vista_extendida();

    // var ctx = document.getElementById('myChart');
    // var myChart = new Chart(ctx, {
    //     type: 'line',
    //     data: {
    //         labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    //         datasets: [{
    //             label: 'Montos',
    //             data: [12, 19, 3, 5, 2, 3],
    //             // backgroundColor: [
    //             //     'rgba(255, 99, 132, 0.2)',
    //             //     'rgba(54, 162, 235, 0.2)',
    //             //     'rgba(255, 206, 86, 0.2)',
    //             //     'rgba(75, 192, 192, 0.2)',
    //             //     'rgba(153, 102, 255, 0.2)',
    //             //     'rgba(255, 159, 64, 0.2)'
    //             // ],
    //             // borderColor: [
    //             //     'rgba(255, 99, 132, 1)',
    //             //     'rgba(54, 162, 235, 1)',
    //             //     'rgba(255, 206, 86, 1)',
    //             //     'rgba(75, 192, 192, 1)',
    //             //     'rgba(153, 102, 255, 1)',
    //             //     'rgba(255, 159, 64, 1)'
    //             // ],
    //             borderWidth: 1,
    //             fill: false
    //         }]
    //     },
    //     options: {
    //         scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     beginAtZero: true
    //                 }
    //             }]
    //         }
    //     }
    // });

});

let dataProProgramado = [];
let dataProEjecutado = [];
let dataProProgramadoAcum = [];
let dataProEjecutadoAcum = [];

let dataPresProgramado = [];
let dataPresEjecutado = [];
let dataPresProgramadoAcum = [];
let dataPresEjecutadoAcum = [];

let dataValorGanadoAcum = [];
let porcen_fisico = [];

let dataProEjeX = [];
let dataPresEjeX = [];

let pro_suma_programado = 0;
let pro_suma_ejecutado = 0;
let pres_suma_programado = 0;
let pres_suma_ejecutado = 0;

let rosado = 'rgba(255, 99, 132, 0.2)';
let celeste = 'rgba(54, 162, 235, 0.2)';
let verde = 'rgba(7, 246, 153, 0.2)';

function mostrar_graficos(id_presup, id_presupuesto){
    dataProProgramado = [];
    dataProEjecutado = [];
    dataProProgramadoAcum = [];
    dataProEjecutadoAcum = [];
    
    dataPresProgramado = [];
    dataPresEjecutado = [];
    dataPresProgramadoAcum = [];
    dataPresEjecutadoAcum = [];
    
    dataValorGanadoAcum = [];
    porcen_fisico = [];
    
    dataProEjeX = [];
    dataPresEjeX = [];
    
    pro_suma_programado = 0;
    pro_suma_ejecutado = 0;
    pres_suma_programado = 0;
    pres_suma_ejecutado = 0;
    
    $.ajax({
        type: 'GET',
        url: 'getProgramadoValorizado/'+id_presup+'/'+id_presupuesto,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var acum_pro_p = 0;
            var acum_pro_e = 0;
            var acum_pres_p = 0;
            var acum_pres_e = 0;
            
            response['pro_programado'].forEach(function (element) {
                dataProProgramado.push(element.total);
                acum_pro_p += parseFloat(element.total);
                dataProProgramadoAcum.push(acum_pro_p);
                dataProEjeX.push(element.numero);
            });
            response['pro_valorizado'].forEach(function (element) {
                dataProEjecutado.push(element.total);
                acum_pro_e += parseFloat(element.total);
                dataProEjecutadoAcum.push(acum_pro_e);
            });
            pro_suma_programado = acum_pro_p;
            pro_suma_ejecutado = acum_pro_e;

            response['pres_programado'].forEach(function (element) {
                dataPresProgramado.push(element.total);
                acum_pres_p += parseFloat(element.total);
                dataPresProgramadoAcum.push(acum_pres_p);
                dataPresEjeX.push(element.numero);
            });
            response['pres_ejecutado'].forEach(function (element) {
                dataPresEjecutado.push(element.total);
                acum_pres_e += parseFloat(element.total);
                dataPresEjecutadoAcum.push(acum_pres_e);
            });
            pres_suma_programado = acum_pres_p;
            pres_suma_ejecutado = acum_pres_e;

            tablaProProgramadoEjecutado();
            tablaPresProgramadoEjecutado();
            tablaValorGanado();
            tablaIndicadores();

            graficarProProgramadoEjecutado();
            graficarPresProgramadoEjecutado();
            graficarValorGanado();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function tablaProProgramadoEjecutado(){
    $('#ProgramadoEjecutado thead').html('');
    $('#ProgramadoEjecutado tbody').html('');

    html_head = '<tr><th></th>';
    dataProEjeX.forEach(function (element) {
        html_head += '<th>Val N°'+element+'</th>';
    });
    html_head +='<th>Total</th></tr>';
    html_body = '<tr style="background-color:'+celeste+';"><td>Programado</td>';
    
    var sumaProgramado = 0;
    var porc = 0;
    var html_acum1 = '<tr><td>Acumulado</td>';

    dataProProgramado.forEach(function (element) {
        html_body += '<td class="right">'+formatNumber.decimal(element,'',-2)+'</td>';
        sumaProgramado += parseFloat(element);
        html_acum1 += '<td class="right">'+formatNumber.decimal(sumaProgramado,'',-2)+'</td>';
    });
    html_body += '<td class="right">'+formatNumber.decimal(sumaProgramado,'',-2)+'</td></tr> <tr style="background-color:'+celeste+';"><td>%</td>';
    html_acum1 +='<td></td> </tr><tr><td>%</td>';
    var suma_porc = 0;

    dataProProgramado.forEach(function (element) {
        porc = parseFloat(element) * 100/sumaProgramado;
        porcen_fisico.push(porc);
        suma_porc += porc;
        html_acum1 += '<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td>';
        html_body += '<td class="right">'+formatNumber.decimal(porc,'',-2)+'%</td>';
    });
    html_acum1 +='<td></td></tr>';
    html_body += '<td class="right">100%</td></tr>'+html_acum1+' <tr style="background-color:'+rosado+';"><td>Ejecutado</td>';

    var sumaEjecutado = 0;
    var html_acum2 = '<tr><td>Acumulado</td>';
    var nro_td = dataProEjeX.length - dataProEjecutado.length;
    var html_td = '';

    for (let i = 0; i < nro_td; i++) {
        html_td += '<td></td>';
    }

    dataProEjecutado.forEach(function (element) {
        html_body += '<td class="right">'+formatNumber.decimal(element,'',-2)+'</td>';
        sumaEjecutado += parseFloat(element);
        html_acum2 += '<td class="right">'+formatNumber.decimal(sumaEjecutado,'',-2)+'</td>';
    });
    html_body += html_td+'<td class="right">'+formatNumber.decimal(sumaEjecutado,'',-2)+'</td></tr> <tr style="background-color:'+rosado+';"><td>%</td>';
    html_acum2 +=html_td+'<td></td></tr><tr><td>%</td>';
    suma_porc = 0;

    dataProEjecutado.forEach(function (element) {
        porc = parseFloat(element) * 100/sumaProgramado;
        suma_porc += porc;
        html_acum2 += '<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td>';
        html_body += '<td class="right">'+formatNumber.decimal(porc,'',-2)+'%</td>';
    });
    html_acum2 +=html_td+'<td></td></tr>';
    html_body += html_td+'<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td></tr>'+html_acum2;

    $('#ProgramadoEjecutado thead').html(html_head);
    $('#ProgramadoEjecutado tbody').html(html_body);
}

function tablaPresProgramadoEjecutado(){
    $('#PresProgramadoEjecutado thead').html('');
    $('#PresProgramadoEjecutado tbody').html('');

    html_head = '<tr><th></th>';
    dataPresEjeX.forEach(function (element) {
        html_head += '<th>Val N°'+element+'</th>';
    });
    html_head +='<th>Total</th></tr>';
    html_body = '<tr style="background-color:'+celeste+';"><td>Programado</td>';
    
    var sumaProgramado = 0;
    var porc = 0;
    var html_acum1 = '<tr><td>Acumulado</td>';

    dataPresProgramado.forEach(function (element) {
        html_body += '<td class="right">'+formatNumber.decimal(element,'',-2)+'</td>';
        sumaProgramado += parseFloat(element);
        html_acum1 += '<td class="right">'+formatNumber.decimal(sumaProgramado,'',-2)+'</td>';
    });
    html_body += '<td class="right">'+formatNumber.decimal(sumaProgramado,'',-2)+'</td></tr> <tr style="background-color:'+celeste+';"><td>%</td>';
    html_acum1 +='<td></td> </tr><tr><td>%</td>';
    var suma_porc = 0;

    dataPresProgramado.forEach(function (element) {
        porc = parseFloat(element) * 100/sumaProgramado;
        suma_porc += porc;
        html_acum1 += '<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td>';
        html_body += '<td class="right">'+formatNumber.decimal(porc,'',-2)+'%</td>';
    });
    html_acum1 +='<td></td></tr>';
    html_body += '<td class="right">100%</td></tr>'+html_acum1+' <tr style="background-color:'+rosado+';"><td>Ejecutado</td>';

    var sumaEjecutado = 0;
    var html_acum2 = '<tr><td>Acumulado</td>';
    var nro_td = dataPresEjeX.length - dataPresEjecutado.length;
    var html_td = '';

    for (let i = 0; i < nro_td; i++) {
        html_td += '<td></td>';
    }

    dataPresEjecutado.forEach(function (element) {
        html_body += '<td class="right">'+formatNumber.decimal(element,'',-2)+'</td>';
        sumaEjecutado += parseFloat(element);
        html_acum2 += '<td class="right">'+formatNumber.decimal(sumaEjecutado,'',-2)+'</td>';
    });
    html_body += html_td+'<td class="right">'+formatNumber.decimal(sumaEjecutado,'',-2)+'</td></tr> <tr style="background-color:'+rosado+';"><td>%</td>';
    html_acum2 +=html_td+'<td></td></tr><tr><td>%</td>';
    suma_porc = 0;

    dataPresEjecutado.forEach(function (element) {
        porc = parseFloat(element) * 100/sumaProgramado;
        suma_porc += porc;
        html_acum2 += '<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td>';
        html_body += '<td class="right">'+formatNumber.decimal(porc,'',-2)+'%</td>';
    });
    html_acum2 +=html_td+'<td></td></tr>';
    html_body += html_td+'<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td></tr>'+html_acum2;

    $('#PresProgramadoEjecutado thead').html(html_head);
    $('#PresProgramadoEjecutado tbody').html(html_body);
}

function graficarProProgramadoEjecutado(){
    var speedCanvas = document.getElementById("chartPro");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var dataFirst = {
        label: "Programado",
        data: dataProProgramadoAcum,
        // lineTension: 0,
        fill: false,
        borderColor: 'rgba(54, 162, 235, 1)'
    };

    var dataSecond = {
        label: "Ejecutado",
        data: dataProEjecutadoAcum,
        // lineTension: 0,
        fill: false,
        borderColor: 'rgba(255, 99, 132, 1)'
    };

    var speedData = {
        labels: dataProEjeX,
        datasets: [dataFirst, dataSecond]
    };

    var chartOptions = {
        legend: {
            display: true,
            position: 'top',
            labels: {
            boxWidth: 80,
            fontColor: 'black'
            }
        },
        // title: {
        //     display: true,
        //     text: 'CURVA "S" DE EJECUCIÓN FÍSICA'
        // }
    };

    var lineChart = new Chart(speedCanvas, {
        type: 'line',
        data: speedData,
        options: chartOptions
    });

}

function graficarPresProgramadoEjecutado(){
    var speedCanvas = document.getElementById("chartPres");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var dataFirst = {
        label: "Programado",
        data: dataPresProgramadoAcum,
        // lineTension: 0,
        fill: false,
        borderColor: 'rgba(54, 162, 235, 1)'
    };

    var dataSecond = {
        label: "Ejecutado",
        data: dataPresEjecutadoAcum,
        // lineTension: 0,
        fill: false,
        borderColor: 'rgba(255, 99, 132, 1)'
    };

    var speedData = {
        labels: dataPresEjeX,
        datasets: [dataFirst, dataSecond]
    };

    var chartOptions = {
        legend: {
            display: true,
            position: 'top',
            labels: {
            boxWidth: 80,
            fontColor: 'black'
            }
        },
        // title: {
        //     display: true,
        //     text: 'CURVA "S" DE EJECUCIÓN FINANCIERA'
        // }
    };

    var lineChart = new Chart(speedCanvas, {
        type: 'line',
        data: speedData,
        options: chartOptions
    });

}

/**Gestion del Valor Ganado */

function tablaValorGanado(){
    $('#ValorGanado thead').html('');
    $('#ValorGanado tbody').html('');

    html_head = '<tr><th></th>';
    dataPresEjeX.forEach(function (element) {
        html_head += '<th>Val N°'+element+'</th>';
    });
    html_head +='<th>Total</th></tr>';
    html_body = '<tr style="background-color:'+celeste+';"><td>Valor Planificado</td>';
    
    var sumaProgramado = 0;
    var porc = 0;
    var html_acum1 = '<tr><td>Acumulado</td>';
    var html_porc = '<tr style="background-color:'+celeste+';"><td>%</td>';
    var html_porc_acum = '<tr><td>%</td>';
    var suma_porc = 0;

    dataPresProgramado.forEach(function (element) {
        html_body += '<td class="right">'+formatNumber.decimal(element,'',-2)+'</td>';
        sumaProgramado += parseFloat(element);
        html_acum1 += '<td class="right">'+formatNumber.decimal(sumaProgramado,'',-2)+'</td>';
        
        porc = parseFloat(element) * 100/pres_suma_programado;
        suma_porc += porc;
        html_porc_acum += '<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td>';
        html_porc += '<td class="right">'+formatNumber.decimal(porc,'',-2)+'%</td>';
    });
    html_acum1 +='<td></td></tr>';
    html_porc += '<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td></tr>';
    html_porc_acum += '<td></td></tr>';
    html_body += '<td class="right">'+formatNumber.decimal(sumaProgramado,'',-2)+'</td></tr>'+html_porc + html_acum1 + html_porc_acum;
    
    var nro_td = dataPresEjeX.length - dataPresEjecutado.length;
    var html_td = '';
    sumaEjecutado = 0;
    
    for (let i = 0; i < nro_td; i++) {
        html_td += '<td></td>';
    }
    html_body += '<tr style="background-color:'+rosado+';"><td>Costo Actual</td>';
    var html_acum2 = '<tr><td>Acumulado</td>';
    html_porc = '<tr style="background-color:'+rosado+';"><td>%</td>';
    html_porc_acum = '<tr><td>%</td>';
    suma_porc = 0;

    dataPresEjecutado.forEach(function (element) {
        html_body += '<td class="right">'+formatNumber.decimal(element,'',-2)+'</td>';
        sumaEjecutado += parseFloat(element);
        html_acum2 += '<td class="right">'+formatNumber.decimal(sumaEjecutado,'',-2)+'</td>';

        porc = parseFloat(element) * 100/pres_suma_ejecutado;
        suma_porc += porc;
        html_porc += '<td class="right">'+formatNumber.decimal(porc,'',-2)+'%</td>';
        html_porc_acum += '<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td>';
    });
    html_acum2 += html_td+'<td></td></tr>';
    html_porc += html_td+'<td class="right">'+formatNumber.decimal(suma_porc,'',-2)+'%</td></tr>';
    html_porc_acum += html_td+'<td></td></tr>';
    html_body += html_td+'<td class="right">'+formatNumber.decimal(sumaEjecutado,'',-2)+'</td></tr>' + html_porc + html_acum2 + html_porc_acum;

    var valor = 0;
    var suma_valor = 0;
    var porc = 0;
    var porc_acum = 0;
    html_body += '<tr style="background-color:'+verde+';"><td>Valor Ganado</td>';
    var html_acum3 = '<tr><td>Acumulado</td>';
    html_porc = '<tr style="background-color:'+verde+';"><td>%</td>';
    html_porc_acum = '<tr><td>%</td>';

    porcen_fisico.forEach(function (element) {
        valor = parseFloat(element) / 100 * pres_suma_programado;
        suma_valor += valor;
        dataValorGanadoAcum.push(suma_valor);
        html_body += '<td class="right">'+formatNumber.decimal(valor,'',-2)+'</td>';
        html_acum3 += '<td class="right">'+formatNumber.decimal(suma_valor,'',-2)+'</td>';
        
        porc = parseFloat(element) * 100 / pres_suma_programado;
        porc_acum = parseFloat(suma_valor) * 100 / pres_suma_programado;
        html_porc += '<td class="right">'+formatNumber.decimal(porc,'',-2)+'%</td>';
        html_porc_acum += '<td class="right">'+formatNumber.decimal(porc_acum,'',-2)+'%</td>';
    });

    html_acum3 += '<td></td></tr>';
    html_porc += '<td class="right">100%</td></tr>';
    html_porc_acum += '<td></td></tr>';
    html_body += '<td class="right">'+formatNumber.decimal(suma_valor,'',-2)+'</td></tr>'+html_porc + html_acum3 + html_porc_acum;

    $('#ValorGanado thead').html(html_head);
    $('#ValorGanado tbody').html(html_body);
}

function tablaIndicadores(){
    $('#Indicadores thead').html('');
    $('#Indicadores tbody').html('');

    html_head = '<tr><th>Indicador</th>';
    dataPresEjeX.forEach(function (element) {
        if (dataPresEjecutadoAcum[element-1] !== undefined && dataPresProgramadoAcum[element-1] !== undefined){
            html_head += '<th width="10%">Val N°'+element+'</th>';
        }
    });
    html_head +='</tr>';
    html_body = '<tr><td>Variación de costo (Cost Variance – CV)</td>';

    var vc = 0;
    var porc_vc = 0;
    var id = 0;
    var sv = 0;
    var porc_sv = 0;
    var idc = 0;
    var icp = 0;
    var eac1 = 0;
    var eac2 = 0;
    var eac3 = 0;
    var etc1 = 0;
    var etc2 = 0;
    var etc3 = 0;
    var cpi1 = 0;
    var cpi2 = 0;
    var cpi3 = 0;
    var html_porc_vc = '<tr><td>% Variación de costo (Cost Variance – CV)</td>';
    var html_id = '<tr><td>Índice de desempeño de costo (Cost Performance Index – SPI)</td>';
    var html_sv = '<tr><td>Variación de cronograma (Schedule Variance – SV)</td>';
    var html_porc_sv = '<tr><td>% Variación de cronograma (Schedule Variance – SV)</td>';
    var html_idc = '<tr><td>Índice de desempeño de cronograma (Schedule Performance Index – SPI)</td>';
    var html_icp = '<tr><td>Indice Costo-Programación (Schedule Cost Index - SCI)</td>';
    var html_eac1 = '<tr><td>Estimado a la Conclusión (Estimate at Completion - EAC)</td>';
    var html_eac2 = '<tr><td>Estimado a la Conclusión (Estimate at Completion - EAC)</td>';
    var html_eac3 = '<tr><td>Estimado a la Conclusión (Estimate at Completion - EAC)</td>';
    var html_etc1 = '<tr><td>Estimado hasta concluir (Estimate to Complete - ETC)</td>';
    var html_etc2 = '<tr><td>Estimado hasta concluir (Estimate to Complete - ETC)</td>';
    var html_etc3 = '<tr><td>Estimado hasta concluir (Estimate to Complete - ETC)</td>';
    var html_cpi1 = '<tr><td>Índice de Rendimiento del Costo a la Conclusión (Cost Performance Index at Conclusion, CPIAC)</td>';
    var html_cpi2 = '<tr><td>Índice de Rendimiento del Costo a la Conclusión (Cost Performance Index at Conclusion, CPIAC)</td>';
    var html_cpi3 = '<tr><td>Índice de Rendimiento del Costo a la Conclusión (Cost Performance Index at Conclusion, CPIAC)</td>';
    var color = '';

    for (let i = 0; i < dataPresEjeX.length; i++) {
        
        if (dataPresEjecutadoAcum[i] !== undefined && dataPresProgramadoAcum[i] !== undefined){

            vc = dataValorGanadoAcum[i] - (dataPresEjecutadoAcum[i] !== undefined ? dataPresEjecutadoAcum[i] : 0);
            color = (vc >= 0 ? verde : rosado);
            html_body += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(vc,'',-2)+'</td>';
    
            porc_vc = vc / dataValorGanadoAcum[i];
            color = (porc_vc >= 0 ? verde : rosado);
            html_porc_vc += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(porc_vc,'',-2)+'</td>';
            
            id = dataValorGanadoAcum[i] / (dataPresEjecutadoAcum[i] !== undefined ? dataPresEjecutadoAcum[i] : 1);
            color = (id >= 0 ? verde : rosado);
            html_id += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(id,'',-2)+'</td>';
    
            sv = dataValorGanadoAcum[i] - (dataPresProgramadoAcum[i] !== undefined ? dataPresProgramadoAcum[i] : 0);
            color = (sv >= 0 ? verde : rosado);
            html_sv += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(sv,'',-2)+'</td>';
    
            porc_sv = sv / (dataPresProgramadoAcum[i] !== undefined ? dataPresProgramadoAcum[i] : 1);
            color = (porc_sv >= 0 ? verde : rosado);
            html_porc_sv += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(porc_sv,'',-2)+'</td>';
    
            idc = dataValorGanadoAcum[i] / (dataPresProgramadoAcum[i] !== undefined ? dataPresProgramadoAcum[i] : 1);
            color = (idc >= 0 ? verde : rosado);
            html_idc += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(idc,'',-2)+'</td>';
    
            icp = id * idc;
            color = (icp >= 0 ? verde : rosado);
            html_icp += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(icp,'',-2)+'</td>';
    
            eac1 = pres_suma_programado - sv;
            color = (eac1 >= 0 ? verde : rosado);
            html_eac1 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(eac1,'',-2)+'</td>';
    
            eac2 = pres_suma_programado / id;
            color = (eac2 >= 0 ? verde : rosado);
            html_eac2 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(eac2,'',-2)+'</td>';
    
            eac3 = pres_suma_programado / icp;
            color = (eac3 >= 0 ? verde : rosado);
            html_eac3 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(eac3,'',-2)+'</td>';
    
            etc1 = eac1 - (dataPresProgramadoAcum[i] !== undefined ? dataPresProgramadoAcum[i] : 0);
            color = (etc1 >= 0 ? verde : rosado);
            html_etc1 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(etc1,'',-2)+'</td>';
    
            etc2 = eac2 - (dataPresProgramadoAcum[i] !== undefined ? dataPresProgramadoAcum[i] : 0);
            color = (etc2 >= 0 ? verde : rosado);
            html_etc2 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(etc2,'',-2)+'</td>';
    
            etc3 = eac3 - (dataPresProgramadoAcum[i] !== undefined ? dataPresProgramadoAcum[i] : 0);
            color = (etc3 >= 0 ? verde : rosado);
            html_etc3 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(etc3,'',-2)+'</td>';
    
            cpi1 = pres_suma_programado / eac1;
            color = (cpi1 >= 0 ? verde : rosado);
            html_cpi1 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(cpi1,'',-2)+'</td>';
    
            cpi2 = pres_suma_programado / eac2;
            color = (cpi2 >= 0 ? verde : rosado);
            html_cpi2 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(cpi2,'',-2)+'</td>';
    
            cpi3 = pres_suma_programado / eac3;
            color = (cpi3 >= 0 ? verde : rosado);
            html_cpi3 += '<td class="right" style="background-color:'+color+';">'+formatNumber.decimal(cpi3,'',-2)+'</td>';
        }
    }
    html_porc_vc += '</tr>';
    html_id += '</tr>';
    html_sv += '</tr>';
    html_porc_sv += '</tr>';
    html_idc += '</tr>';
    html_icp += '</tr>';
    html_eac1 += '</tr>';
    html_eac2 += '</tr>';
    html_eac3 += '</tr>';
    html_etc1 += '</tr>';
    html_etc2 += '</tr>';
    html_etc3 += '</tr>';
    html_cpi1 += '</tr>';
    html_cpi2 += '</tr>';
    html_cpi3 += '</tr>';
    html_body += '</tr>'+html_porc_vc + html_id + html_sv + html_porc_sv + html_idc + html_icp + html_eac1 + 
    html_eac2 + html_eac3 + html_etc1 + html_etc2 + html_etc3 + html_cpi1 + html_cpi2 + html_cpi3;

    $('#Indicadores thead').html(html_head);
    $('#Indicadores tbody').html(html_body);
}

function graficarValorGanado(){
    var speedCanvas = document.getElementById("chartValor");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var dataFirst = {
        label: "Planificado",
        data: dataPresProgramadoAcum,
        // lineTension: 0,
        fill: false,
        borderColor: 'rgba(54, 162, 235, 1)'
    };

    var dataSecond = {
        label: "Actual",
        data: dataPresEjecutadoAcum,
        // lineTension: 0,
        fill: false,
        borderColor: 'rgba(255, 99, 132, 1)'
    };

    var dataThird = {
        label: "Valor Ganado",
        data: dataValorGanadoAcum,
        // lineTension: 0,
        fill: false,
        borderColor: 'rgba(7, 246, 153, 1)'
    };

    var speedDataVG = {
        labels: dataPresEjeX,
        datasets: [dataFirst, dataSecond, dataThird]
    };

    var chartOptionsVG = {
        legend: {
            display: true,
            position: 'top',
            labels: {
            boxWidth: 80,
            fontColor: 'black'
            }
        },
        // title: {
        //     display: true,
        //     text: 'CURVA "S" DE EJECUCIÓN FINANCIERA'
        // }
    };

    var lineChart = new Chart(speedCanvas, {
        type: 'line',
        data: speedDataVG,
        options: chartOptionsVG
    });

}

