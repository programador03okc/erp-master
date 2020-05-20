$(function(){
    var fecha = new Date();
    var anio = fecha.getFullYear();
    var mes = fecha.getMonth() + 1;
    $('#anio_mes').text(anio+' - '+(mes < 10 ? '0' + mes : mes));
    mostrar_tabla();
});

let dataLabel = [];
let dataImportes = [];
let backgroundColor = [ //Color del segmento
    "#8BC34A",
    "#03A9F4",
    "#FFCE56"
];

function mostrar_tabla(){
    $.ajax({
        type: 'GET',
        url: 'getProyectosActivos',
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            var porc_actual = 0;
            var porc_acum = 0;
            var saldo = 0;

            var proy = 0;
            var total_contrato = 0;
            var total_actual = 0;
            var total_valorizado = 0;
            var total_saldo = 0;

            response['data'].forEach(function (element) {
                porc_actual = parseFloat(element.actual_ejecutado) * 100 / parseFloat(element.total_programado);
                porc_acum = parseFloat(element.acumulado_ejecutado) * 100 / parseFloat(element.total_programado);
                saldo = parseFloat(element.total_programado) - parseFloat(element.acumulado_ejecutado);
                dataLabel.push(element.codigo);
                dataImportes.push(formatDecimal(element.total_programado));

                proy++;
                total_contrato += parseFloat(element.total_programado);
                total_actual += parseFloat(element.actual_ejecutado);
                total_valorizado += parseFloat(element.acumulado_ejecutado);
                total_saldo += saldo;

                html += '<tr><td>'+i+'</td>'+
                '<td><i class="fas fa-bookmark" style="color:'+backgroundColor[i-1]+';" data-toggle="tooltip" data-placement="bottom" '+
                'title="'+element.descripcion+'"></i></td>'+
                '<td>'+element.codigo+'</td>'+
                '<td class="right">'+formatNumber.decimal(element.cant_mes,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal(element.total_programado,'',-2)+'</td>'+
                '<td class="right" style="background: #d8fcfc;">'+formatNumber.decimal(porc_actual,'',-2)+'%</td>'+
                '<td class="right" style="background: #d8fcfc;">'+formatNumber.decimal(porc_acum,'',-2)+'%</td>'+
                '<td class="right" style="background: #ffffb0;">'+formatNumber.decimal(element.actual_ejecutado,'',-2)+'</td>'+
                '<td class="right" style="background: #ffffb0;">'+formatNumber.decimal(element.acumulado_ejecutado,'',-2)+'</td>'+
                '<td class="right" style="background: #ffffb0;">'+formatNumber.decimal(saldo,'',-2)+'</td>'+
                '</tr>';
                i++;
            });
            var html_foot = '<tr>'+
            '<th class="right" colSpan="4">Totales</th>'+
            '<th class="right">'+formatNumber.decimal(total_contrato,'',-2)+'</th>'+
            '<th class="right" colSpan="2"></th>'+
            '<th class="right">'+formatNumber.decimal(total_actual,'',-2)+'</th>'+
            '<th class="right">'+formatNumber.decimal(total_valorizado,'',-2)+'</th>'+
            '<th class="right">'+formatNumber.decimal(total_saldo,'',-2)+'</th>'+
            '</tr>';

            $('#opciones_generadas').text(response['nro_opciones']);
            $('#proyectos_generados').text(proy);
            $('#total_valorizado').text(formatNumber.decimal(total_valorizado,'',-2));
            $('#total_saldo').text(formatNumber.decimal(total_saldo,'',-2));
            
            $('#Proyectos tbody').html(html);
            $('#Proyectos tfoot').html(html_foot);
            mostrar_grafico();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function mostrar_grafico(){
    var circularChart = new Chart(document.getElementById('chartProyectos'), {
        type: 'pie', //Gráfica circular
        data: {
            labels: dataLabel, //Etiquetas
            datasets: [
                {
                    data: dataImportes, //Cantidad de la ¿rebanada?
                    backgroundColor: [ //Color del segmento
                        "#8BC34A",
                        "#03A9F4",
                        "#FFCE56"
                    ],
                    hoverBackgroundColor: [ //Color al hacer hover al segmento
                        "#7CB342",
                        "#039BE5",
                        "#FFA000"
                    ]
                }]
        }
    });
}