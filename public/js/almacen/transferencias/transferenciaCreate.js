function ver_transferencia(id_guia){
    $.ajax({
        type: 'GET',
        url: 'verGuiaCompraTransferencia/'+id_guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#modal-guia_com_ver').modal({
                show: true
            });
            $('[name=id_guia_com]').val(response['guia'].id_guia);
            $('[name=serie_numero]').text(response['guia'].serie+'-'+response['guia'].numero);
            $('[name=fecha_emision]').text(response['guia'].fecha_emision);
            $('[name=fecha_almacen]').text(response['guia'].fecha_almacen);
            $('[name=almacen]').text(response['guia'].almacen_descripcion);
            $('[name=operacion]').text(response['guia'].operacion);
            $('[name=clasificacion]').text(response['guia'].clasificacion);
            
            var html='';
            var html_serie='';
            var i=1;

            response['detalle'].forEach(element => {
                
                html_serie = '';
                element.series.forEach(ser => {
                    if (html_serie == ''){
                        html_serie += ser.serie;
                    } else {
                        html_serie += '<br>'+ser.serie;
                    }
                });

                html+=`<tr>
                <td>${i}</td>
                <td>${element.codigo_orden!==null?element.codigo_orden:''}</td>
                <td>${element.codigo_req!==null?element.codigo_req:''}</td>
                <td><strong>${element.sede_req!==null?element.sede_req:''}</strong></td>
                <td>${element.codigo}</td>
                <td>${element.part_number!==null?element.part_number:''}</td>
                <td>${element.descripcion}</td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                <td>${html_serie}</td>
                </tr>`;
                i++;
            });
            $('#detalleGuiaCompra tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-guia_com_ver").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    generar_transferencia();
});

function generar_transferencia(){
    var id_guia = $('[name=id_guia_com]').val();
    $.ajax({
        type: 'GET',
        url: 'transferencia/'+id_guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert(response);
            listarTransferenciasPorEnviar();
            $('#modal-guia_com_ver').modal('hide')
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}