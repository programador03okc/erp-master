function agrega_partida_ci(cod_compo, titulo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        $('#modal-partidaCICreate').modal({
            show: true
        });
        $("#btnGuardarPartidaCI").removeAttr("disabled");
        var i = 1;
        var filas = document.querySelectorAll('#listaCI tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[12].innerText;
            var cant = colum[4].innerText;
            console.log('padre: '+padre);
            console.log('cant: '+cant);
            if (padre == cod_compo && cant !== ''){
                i++;
            }
        });
        var id_ci = $('[name=id_presupuesto]').val();
        console.log('cod_compo: '+cod_compo);
        $('[name=cod_compo_ci]').val(cod_compo);
        $('[name=id_ci]').val(id_ci);
        $('[name=codigo_ci]').val(cod_compo+'.'+leftZero(2,i));
        $('[name=id_ci_detalle]').val('');
        $('[name=id_cu_ci]').val('');
        $('[name=cod_acu_ci]').val('');
        $('[name=des_acu_ci]').val('');
        $('[name=unid_medida_ci]').val(0);
        $('[name=cantidad_ci]').val('');
        $('[name=precio_unitario_ci]').val('');
        $('[name=participacion]').val('');
        $('[name=tiempo]').val('');
        $('[name=veces]').val('');
        $('[name=precio_total_ci]').val('');
        $('#titulo').text(titulo);
    }
}

function calculaPrecioTotalCI(){
    var cant = $('[name=cantidad_ci]').val();
    var unit = $('[name=precio_unitario_ci]').val();
    var par = $('[name=participacion]').val();
    var tiem = $('[name=tiempo]').val();
    var vec = $('[name=veces]').val();
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

    $('[name=precio_total_ci]').val(precio_tot);
}

function guardar_partida_ci(){
    var id_pres = $('[name=id_presupuesto]').val();
    var id = $('[name=id_ci_detalle]').val();
    var id_ci = $('[name=id_ci]').val();
    var id_cu = $('[name=id_cu_ci]').val();
    var cod = $('[name=codigo_ci]').val();
    var des = $('[name=des_acu_ci]').val();
    var unid = $('[name=unid_medida_ci]').val();
    var cant = $('[name=cantidad_ci]').val();
    var unit = $('[name=precio_unitario_ci]').val();
    var par = $('[name=participacion]').val();
    var tiem = $('[name=tiempo]').val();
    var vec = $('[name=veces]').val();
    var tot = $('[name=precio_total_ci]').val();
    var comp = $('[name=cod_compo_ci]').val();

    var data = 'id_ci_detalle='+id+
            '&id_ci='+id_ci+
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
        baseUrl = 'update_partida_ci';
    } else {
        baseUrl = 'guardar_partida_ci';
    }
    console.log(baseUrl);
    $("#btnGuardarPartidaCI").attr('disabled','true');

    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Partida registrada con éxito');
                listar_ci(id_pres);
                totales(id_pres);
                $('#modal-partidaCICreate').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function editar_partida_ci(id_ci_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        $('#modal-partidaCICreate').modal({
            show: true
        });
        $("#btnGuardarPartidaCI").removeAttr("disabled");
        var id_ci = $('[name=id_presupuesto]').val();
        $('[name=id_ci]').val(id_ci);
    
        var filas = document.querySelectorAll('#parci-'+id_ci_detalle);
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            cu      = (colum[1].id).split('-');
            cod_cu  = (colum[2].id).split('-');
            unid    = (colum[3].id).split('-');
    
            $('[name=id_ci_detalle]').val(id_ci_detalle);
            $('[name=id_cu_ci]').val(cu[1]);
            $('[name=codigo_ci]').val(colum[1].innerText);
            $('[name=cod_acu_ci]').val(cod_cu[1]);
            $('[name=des_acu_ci]').val(colum[2].innerText);
            $('[name=unid_medida_ci]').val(unid[1]);
            $('[name=cantidad_ci]').val(colum[4].innerText);
            $('[name=precio_unitario_ci]').val(parseFloat(colum[5].innerText.replace(',','')));
            $('[name=participacion]').val(parseFloat(colum[6].innerText.replace(',','')));
            $('[name=tiempo]').val(parseFloat(colum[7].innerText.replace(',','')));
            $('[name=veces]').val(parseFloat(colum[8].innerText.replace(',','')));
            $('[name=precio_total_ci]').val(parseFloat(colum[9].innerText.replace(',','')));
            $('[name=cod_compo_ci]').val(colum[12].innerText);
        
        });
    
        $("#parci-"+id_ci_detalle+" td").find("input[name=descripcion]");
    }
}

function anular_partida_ci(id_ci_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var anula = confirm("¿Esta seguro que desea anular ésta partida?");
        var cod = '';
        var id_pres = $('[name=id_presupuesto]').val();
        console.log('id_ci_detalle:'+id_ci_detalle);

        var padre = $("#parci-"+id_ci_detalle+" td")[12].innerHTML;
        console.log('padre'+padre);
        // var filas = document.querySelectorAll('#par-'+id_ci_detalle);
        // $("#par-"+id_ci_detalle+" td").forEach(function(e){
        //     var colum = e.querySelectorAll('td');
        //     console.log(colum);
        //     cod = colum[12].innerText;
        //     console.log('padre:'+cod);
        // });

        var token = $('#token').val();
        if (anula){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': token},
                url: 'anular_partida_ci',
                data: 'id_ci_detalle='+id_ci_detalle+'&cod_compo='+cod+'&id_pres='+id_pres,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Partida anulada con éxito');
                        listar_ci(id_pres);
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

function change_descripcion_ci(event){
    $('[name=cod_acu_ci]').val('');
    $('[name=id_cu_ci]').val('');
}

function subir_partida_ci(id_ci_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        $.ajax({
            type: 'GET',
            url: 'subir_partida_ci/'+id_ci_detalle,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    listar_ci(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function bajar_partida_ci(id_ci_detalle){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        $.ajax({
            type: 'GET',
            url: 'bajar_partida_ci/'+id_ci_detalle,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    listar_ci(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}