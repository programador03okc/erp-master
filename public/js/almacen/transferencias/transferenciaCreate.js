function ver_transferencia(id_guia){
    $("#submit_guia_transferencia").removeAttr("disabled");
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
                        html_serie += '<br>'+ser.serie;
                    } else {
                        html_serie += ', '+ser.serie;
                    }
                });

                html+=`<tr>
                <td>${i}</td>
                <td>${element.codigo_orden!==null?element.codigo_orden:(element.codigo_transfor!==null ? element.codigo_transfor : '')}</td>
                <td>${element.codigo_req!==null?element.codigo_req:''}</td>
                <td><strong>${element.sede_req!==null?element.sede_req:''}</strong></td>
                <td>${element.codigo}</td>
                <td>${element.part_number!==null?element.part_number:''}</td>
                <td>${element.descripcion} <strong>${html_serie}</strong></td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
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
    $("#submit_guia_transferencia").attr('disabled','true');
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
            $('#modal-guia_com_ver').modal('hide');
            let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
            
            if (formName =='transferencias'){
                listarTransferenciasPorEnviar();
            }
            else if (formName =='ordenesPendientes'){
                $('#listaIngresosAlmacen').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-ver_requerimiento").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    var id = $('[name=id_requerimiento]').val();
    generar_transferencia_requerimiento(id);
});

function generar_transferencia_requerimiento(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'generarTransferenciaRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert(response);
            listarTransferenciasPorEnviar();
            $('#modal-ver_requerimiento').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function ver_requerimiento(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#modal-ver_requerimiento').modal({
                show: true
            });
            $('[name=id_requerimiento]').val(response['requerimiento'].id_requerimiento);
            $('[name=codigo_req]').text(response['requerimiento'].codigo);
            $('[name=concepto]').text(response['requerimiento'].concepto);
            $('[name=fecha_requerimiento]').text(response['requerimiento'].fecha_requerimiento);
            $('[name=sede_requerimiento]').text(response['requerimiento'].sede_requerimiento);
            $('[name=estado_requerimiento]').text(response['requerimiento'].estado_doc);
            
            var html='';
            var html_serie='';
            var i=1;

            response['detalle'].forEach(element => {
                
                html_serie = '';
                element.series.forEach(ser => {
                    if (html_serie == ''){
                        html_serie += '<br>'+ser.serie;
                    } else {
                        html_serie += ', '+ser.serie;
                    }
                });

                html+=`<tr>
                <td>${i}</td>
                <td>${element.codigo_orden!==null?element.codigo_orden:''}</td>
                <td>${element.sede}</td>
                <td>${element.codigo}</td>
                <td>${element.part_number!==null?element.part_number:''}</td>
                <td>${element.descripcion} <strong>${html_serie}</strong></td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                </tr>`;
                i++;
            });
            $('#detalleRequerimiento tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}