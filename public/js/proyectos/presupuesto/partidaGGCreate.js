function agrega_partida_gg(cod_compo, titulo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        console.log('agrega_partida');
        $('#modal-partidaGGCreate').modal({
            show: true
        });
        $("#btnGuardarPartidaGG").removeAttr("disabled");
        var i = 1;
        var filas = document.querySelectorAll('#listaGG tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[12].innerText;
            var cant = colum[4].innerText;

            if (padre == cod_compo && cant !== ''){
                i++;
            }
        });
        var id_gg = $('[name=id_presupuesto]').val();
        console.log(id_gg);
        $('[name=cod_compo_gg]').val(cod_compo);
        $('[name=id_gg]').val(id_gg);
        $('[name=codigo_gg]').val(cod_compo+'.'+leftZero(2,i));
        $('[name=id_gg_detalle]').val('');
        $('[name=id_cu_gg]').val('');
        $('[name=cod_acu_gg]').val('');
        $('[name=des_acu_gg]').val('');
        $('[name=unid_medida_gg]').val(0);
        $('[name=cantidad_gg]').val('');
        $('[name=precio_unitario_gg]').val('');
        $('[name=participacion_gg]').val('');
        $('[name=tiempo_gg]').val('');
        $('[name=veces_gg]').val('');
        $('[name=precio_total_gg]').val('');
        $('#titulo_gg').text(titulo);
        // $("input:text:visible:first").focus();
        console.log('autofocus');
        // document.getElementById("des_acu_gg").focus();
        setTimeout(function () {
            var el = document.getElementsByName("des_acu_gg")[0];
            console.log(el);
            $('#des_acu_gg').attr('autofocus', 'true');
            // el.focus(); 
        }, 200);
    }
    // document.getElementById('des_acu_gg').focus();
    // $('#des_acu_gg').attr('autofocus', 'true');
}

function calculaPrecioTotalGG(){
    var cant = $('[name=cantidad_gg]').val();
    var unit = $('[name=precio_unitario_gg]').val();
    var par = $('[name=participacion_gg]').val();
    var tiem = $('[name=tiempo_gg]').val();
    var vec = $('[name=veces_gg]').val();
    var precio_tot = 0;

    console.log('cant'+cant+' unit'+unit);
    if (cant !== null && unit !== null){
        precio_tot = (cant * unit).toFixed(2);
    }
    if (par > 0) { precio_tot *= par; }
    if (tiem > 0) { precio_tot *= tiem; }
    if (vec > 0) { precio_tot *= vec; }

    console.log('par'+par+' tiem'+tiem+' vec'+vec);
    console.log('precio_tot'+precio_tot);

    $('[name=precio_total_gg]').val(precio_tot);
}

function guardar_partida_gg(){
    var id_pres = $('[name=id_presupuesto]').val();
    var id = $('[name=id_gg_detalle]').val();
    var id_gg = $('[name=id_gg]').val();
    var id_cu = $('[name=id_cu_gg]').val();
    var cod = $('[name=codigo_gg]').val();
    var des = $('[name=des_acu_gg]').val();
    var unid = $('[name=unid_medida_gg]').val();
    var cant = $('[name=cantidad_gg]').val();
    var unit = $('[name=precio_unitario_gg]').val();
    var par = $('[name=participacion_gg]').val();
    var tiem = $('[name=tiempo_gg]').val();
    var vec = $('[name=veces_gg]').val();
    var tot = $('[name=precio_total_gg]').val();
    var comp = $('[name=cod_compo_gg]').val();

    var data = 'id_gg_detalle='+id+
            '&id_gg='+id_gg+
            '&id_cu='+id_cu+
            '&codigo='+cod+
            '&descripcion='+des+
            '&unid_medida='+unid+
            '&cantidad='+cant+
            '&unitario='+unit+
            '&total='+tot+
            '&participacion='+par+
            '&tiempo='+tiem+
            '&veces='+vec+
            '&comp='+comp;
    
    var token = $('#token').val();
    console.log(data);

    var baseUrl;
    if (id !== ''){
        baseUrl = 'update_partida_gg';
    } else {
        baseUrl = 'guardar_partida_gg';
    }
    console.log(baseUrl);
    $("#btnGuardarPartidaGG").attr('disabled','true');

    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Partida registrada con éxito');
                listar_gg(id_pres);
                totales(id_pres);
                $('#modal-partidaGGCreate').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function editar_partida_gg(id_gg_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        $('#modal-partidaGGCreate').modal({
            show: true
        });
        $("#btnGuardarPartidaGG").removeAttr("disabled");
        var id_gg = $('[name=id_presupuesto]').val();
        $('[name=id_gg]').val(id_gg);

        var filas = document.querySelectorAll('#pargg-'+id_gg_detalle);
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            
            cu      = (colum[1].id).split('-');
            cod_cu  = (colum[2].id).split('-');
            unid    = (colum[3].id).split('-');
            
            $('[name=id_gg_detalle]').val(id_gg_detalle);
            $('[name=id_cu_gg]').val(cu[1]);
            $('[name=codigo_gg]').val(colum[1].innerText);
            $('[name=cod_acu_gg]').val(cod_cu[1]);
            $('[name=des_acu_gg]').val(colum[2].innerText);
            $('[name=unid_medida_gg]').val(unid[1]);
            $('[name=cantidad_gg]').val(colum[4].innerText);
            $('[name=precio_unitario_gg]').val(parseFloat(colum[5].innerText.replace(',','')));
            $('[name=participacion_gg]').val(parseFloat(colum[6].innerText.replace(',','')));
            $('[name=tiempo_gg]').val(parseFloat(colum[7].innerText.replace(',','')));
            $('[name=veces_gg]').val(parseFloat(colum[8].innerText.replace(',','')));
            $('[name=precio_total_gg]').val(parseFloat(colum[9].innerText.replace(',','')));
            $('[name=cod_compo_gg]').val(colum[12].innerText);
        
        });

        $("#pargg-"+id_gg_detalle+" td").find("input[name=descripcion]");
    }
}

function anular_partida_gg(id_gg_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var anula = confirm("¿Esta seguro que desea anular ésta partida?");
        var cod = '';
        var id_pres = $('[name=id_presupuesto]').val();

        var filas = document.querySelectorAll('#pargg-'+id_gg_detalle);
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            cod = colum[12].innerText;
        });

        var token = $('#token').val();
        if (anula){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': token},
                url: 'anular_partida_gg',
                data: 'id_gg_detalle='+id_gg_detalle+'&cod_compo='+cod+'&id_pres='+id_pres,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Partida anulada con éxito');
                        listar_gg(id_pres);
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

function change_descripcion_gg(event){
    $('[name=cod_acu_gg]').val('');
    $('[name=id_cu_gg]').val('');
    console.log('change_descripcion_gg');
    $('#des_acu_gg').attr('autofocus', 'true');
}

function subir_partida_gg(id_gg_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        $.ajax({
            type: 'GET',
            url: 'subir_partida_gg/'+id_gg_detalle,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    listar_gg(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function bajar_partida_gg(id_gg_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        $.ajax({
            type: 'GET',
            url: 'bajar_partida_gg/'+id_gg_detalle,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    listar_gg(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}