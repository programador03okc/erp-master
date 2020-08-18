$(function(){
    mostrar_tabla();
});

let dataLabel = [];
let dataCantidades = [];
let backgroundColor = [ //Color del segmento
    
    'rgba(255, 99, 132, 1)',
    'rgba(54, 162, 235, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 159, 64, 1)',
    'rgba(0, 255, 127, 1)',//spring green
    'rgba(255, 127, 80, 1)',//coral
    // "#8BC34A",
    // "#03A9F4",
    // "#FFCE56"
];

let orden = [1,5,19,17,9,20,21];

function mostrar_tabla(){
    var filtro = $('[name=filtro]').val();
    dataLabel = [];
    dataCantidades = [];
    $.ajax({
        type: 'GET',
        url: 'getEstadosRequerimientos/'+filtro,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            var html = '';
            // console.log(orden);
            var i = 1;
            for (let index = 0; index < orden.length; index++) {
                const nro = orden[index];
                // console.log('nro:'+nro);
                var estado = response.find(element => element.estado == nro);
                // response.forEach(function (element) {
                if (estado !== undefined){
                    dataLabel.push(estado.estado_doc);
                    dataCantidades.push(estado.cantidad);
                    html += '<tr>'+
                    '<td><i class="fas fa-bookmark" style="color:'+backgroundColor[i-1]+';" data-toggle="tooltip" data-placement="bottom" '+
                    'title="'+estado.estado_doc+'"></i></td>'+
                    '<td>'+estado.estado_doc+'</td>'+
                    '<td class="right">'+estado.cantidad+'</td>'+
                    '<td class="center"><button type="button" class="ver btn btn-info boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Requerimientos" data-id="'+estado.estado+'" data-estado="'+estado.estado_doc+'" >'+
                    '<i class="fas fa-list-ul"></i></button></td>'+
                    '</tr>';
                    i++;
                }
                // });
            }
            $('#listaEstadosRequerimientos tbody').html(html);
            // $('#Proyectos tfoot').html(html_foot);
            mostrar_grafico();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

$('#listaEstadosRequerimientos tbody').on("click","button.ver", function(){
    var id = $(this).data('id');
    var estado_doc = $(this).data('estado');
    $('#modal-verRequerimientoEstado').modal({
        show: true
    });
    $('#nombreEstado').text('Requerimientos con Estado '+estado_doc);
    listarRequerimientosEstado(id);
});

function listarRequerimientosEstado(estado){
    var filtro = $('[name=filtro]').val();
    $.ajax({
        type: 'GET',
        url: 'listarEstadosRequerimientos/'+estado+'/'+filtro,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            
            response.forEach(element => {
                html+='<tr id="'+element.id_requerimiento+'">'+
                '<td>'+element.codigo+'</td>'+
                '<td>'+element.concepto+'</td>'+
                '<td>'+element.nombre_corto+'</td>'+
                '</tr>';
            });
            
            $('#listaRequerimientosEstado tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_grafico(){
    var ctx = document.getElementById('chartRequerimientos').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar', //Gráfica barras
        data: {
            labels: dataLabel, //Etiquetas
            datasets: [
                {
                    label: 'Cantidad',
                    data: dataCantidades, //Cantidad
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }
            ]
        },
        options: {
            legend: {
                display: false          // Hides annoying dataset label
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }],
            }
        }
    });
}