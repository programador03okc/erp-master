var sistema_contrato = null;

function agrega_partida_cd(cod_compo, titulo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        $('#modal-partidaCDCreate').modal({
            show: true
        });
        limpiarCamposPartida();
        $('[name=cod_compo]').val(cod_compo);

        var i = 1;
        var sis = null;

        document.querySelectorAll('#listaAcusCD tbody tr').forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[9].innerText;
            var unid = colum[3].innerText;
            if (padre == cod_compo && unid !== ''){
                i++;
            }
            console.log('sistema');
            
        });

        document.querySelector("#listaAcusCD").querySelectorAll("select").forEach(function(item) {
            sis = item.value;
        });

        console.log(cod_compo+'.'+leftZero(2,i));
        $('[name=codigo_cd]').val(cod_compo+'.'+leftZero(2,i));
        $('#titulo').text(titulo);

        if (sis !== null){
            $('[name=id_sistema_cu]').val(sis);
        }
    }
}

function listarAcusCD(id_pres){
    console.log('id_pres: '+id_pres);
    $.ajax({
        type: 'GET',
        url: 'listar_acus_cd/'+id_pres,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var mnd = $('[name=simbolo]').val();
            $('#simbolo_cd').text(mnd);
            $('#total_acus_cd').text(formatNumber.decimal(response['total'],'',-2));
            $('#listaAcusCD tbody').html(response['html']);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function calculaPrecioTotalPartida(){
    var cant = $('[name=cantidad_par]').val();
    var unit = $('[name=precio_unitario]').val();
    let precio_tot = 0;
    console.log('cant:'+cant+' unit:'+unit);

    if (cant !== null && unit !== null){
        precio_tot = (cant * unit).toFixed(6);
    }
    $('[name=precio_total_partida]').val(precio_tot);
    console.log('precio total'+precio_tot);
}

function calcula_total_acus_cd(){
    var total_acus = 0;
    $('#listaAcusCD tbody tr').each(function(e){
        var a = $(this).find("td input[name=importe_parcial]").val();
        console.log('parcial: '+a);
        if (a !== undefined){
            var total = parseFloat(a);
            console.log('total:'+total);
            total_acus += total;
        }
    });
    $('#total_acus_cd').text(formatNumber.decimal(total_acus,'',-2));
}

function limpiarCamposPartida(){
    $('[name=cod_compo]').val('');
    // $('[name=id_cd]').val(id_cd);
    $('[name=id_partida]').val('');
    $('[name=id_cu_partida_cd]').val('');
    // $('[name=id_cu_cd]').val('');
    $('[name=cod_cu]').val('');
    $('[name=des_cu]').val('');
    $('[name=id_unid_medida]').val('');
    $('[name=unid_medida]').val('');
    $('[name=cantidad_par]').val('');
    $('[name=precio_unitario]').val('');
    $('[name=precio_total_partida]').val('');
    $('[name=id_sistema_cu]').val('');
}

function guardar_partida_cd(){
    var id_pres = $('[name=id_presupuesto]').val();
    var id = $('[name=id_partida]').val();
    var id_cu = $('[name=id_cu_partida_cd]').val();
    var codigo_cd = $('[name=codigo_cd]').val();
    var cod = $('[name=cod_cu]').val();
    var des = $('[name=des_cu]').val();
    var unid = $('[name=id_unid_medida]').val();
    var cant = $('[name=cantidad_par]').val();
    var unit = $('[name=precio_unitario]').val();
    var tot = $('[name=precio_total_partida]').val();
    var sis = $('[name=id_sistema_cu]').val();
    var comp = $('[name=cod_compo]').val();

    var data = 'id_partida='+id+
            '&id_cd='+id_pres+
            '&id_cu_partida='+id_cu+
            '&codigo='+codigo_cd+
            '&descripcion='+des+
            '&unid_medida='+unid+
            '&cantidad='+cant+
            '&unitario='+unit+
            '&total='+tot+
            '&sis='+sis+
            '&comp='+comp;
    
    console.log(data);
    if (tot !== ''){
        if (sis !== '0'){
            $.ajax({
                type: 'POST',
                url: 'guardar_partida_cd',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        $('#modal-partidaCDCreate').modal('hide');
                        listarAcusCD(id_pres);
                        limpiarCamposPartida();
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        } else {
            alert('Debe seleccionar un sistema de contrato');
        }
    } else {
        alert('Es necesario que ingrese todos los campos!');
    }
}

function editar_partida_cd(id_partida){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        $("#par-"+id_partida+" td").find("input[name=cantidad]").removeAttr('disabled');
        $("#par-"+id_partida+" td").find("select").attr('disabled',false);

        $("#par-"+id_partida+" td").find("i.blue").removeClass('visible');
        $("#par-"+id_partida+" td").find("i.blue").addClass('oculto');
        $("#par-"+id_partida+" td").find("i.fa-save").removeClass('oculto');
        $("#par-"+id_partida+" td").find("i.fa-save").addClass('visible');
    }
}

function calcula_total_cd(id_partida){
    var cant = $('#par-'+id_partida+' input[name=cantidad]').val();
    var unit = $('#par-'+id_partida+' input[name=importe_unitario]').val();
    console.log('cant'+cant+' unit'+unit);
    if (cant !== '' && unit !== '') {
        $('#par-'+id_partida+' input[name=importe_parcial]').val(cant * unit);
    } else {
        $('#par-'+id_partida+' input[name=importe_parcial]').val(0);
    }
}

function update_partida_cd(id_partida){
    var sist  = $("#par-"+id_partida+" td").find("select").val();
    var cant  = $("#par-"+id_partida+" td").find("input[name=cantidad]").val();
    var unit  = $("#par-"+id_partida+" td").find("input[name=importe_unitario]").val();
    var total = $("#par-"+id_partida+" td").find("input[name=importe_parcial]").val();
    var padre = $("#par-"+id_partida+" td")[9].textContent;
    var id_pres = $('[name=id_presupuesto]').val();

    var data = 'id_partida='+id_partida+
            '&id_cd='+id_pres+
            '&id_sistema='+sist+
            '&cantidad='+cant+
            '&importe_unitario='+unit+
            '&importe_parcial='+total+
            '&comp='+padre;

    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'update_partida_cd',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $("#par-"+id_partida+" td").find("select").attr('disabled',true);
                $("#par-"+id_partida+" td").find("input").attr('disabled',true);
                $("#par-"+id_partida+" td").find("i.blue").removeClass('oculto');
                $("#par-"+id_partida+" td").find("i.blue").addClass('visible');
                $("#par-"+id_partida+" td").find("i.fa-save").removeClass('visible');
                $("#par-"+id_partida+" td").find("i.fa-save").addClass('oculto');
                listarAcusCD(id_pres);
                calcula_total_acus_cd();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_partida_cd(id_partida){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var anula = confirm("¿Esta seguro que desea anular ésta partida?");
        var cod = '';
        var id_pres = $('[name=id_presupuesto]').val();

        var filas = document.querySelectorAll('#par-'+id_partida);
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            cod = colum[8].innerText;
        });

        if (anula){
            $.ajax({
                type: 'POST',
                url: 'anular_partida_cd',
                data: 'id_partida='+id_partida+'&cod_compo='+cod+'&id_pres='+id_pres,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        listarAcusCD(id_pres);
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

function edit_acu(id_cu_partida, id_partida){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        if (id_cu_partida !== ''){
            $.ajax({
                type: 'GET',
                url: 'mostrar_acu/'+id_cu_partida,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response['nro_pres'] > 0){
                        alert('No puede editar éste A.C.U porque el Presupuesto ya esta Emitido!');
                    } else {
                        $('#modal-acu_partida_create').modal({
                            show: true
                        });
                        $('[name=id_cu_partida_cd]').val(response['acu'][0].id_cu_partida);
                        $('[name=id_cu]').val(response['acu'][0].id_cu);
                        $('[name=cod_acu]').val(response['acu'][0].codigo);
                        $('[name=des_acu]').val(response['acu'][0].descripcion);
                        $('[name=rendimiento]').val(response['acu'][0].rendimiento);
                        $('[name=unid_medida_cu]').val(response['acu'][0].unid_medida);
                        $('[name=total_acu]').val(formatNumber.decimal(response['acu'][0].total,'',-4));
                        // $('[name=id_categoria]').val(response['acu'][0].id_categoria);
                        // $('[name=observacion]').val(response['acu'][0].observacion);
                        unid_abrev();
                        insumos = [];
                        id_partida_temporal = id_partida;
                        listar_acu_detalle(response['acu'][0].id_cu_partida);
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        } else {
            alert('Debe seleccionar un A.C.U.');
        }
    }
}

function acuParticaCreateModal(){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id = $('[name=id_presupuesto]').val();
        if (id !== '' && id !== null){
            open_acu_partida_create(undefined);
        } else {
            alert('Debe seleccionar un Presupuesto');
        }
    }
}

function subir_partida_cd(id_partida){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        $.ajax({
            type: 'GET',
            url: 'subir_partida_cd/'+id_partida,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    listarAcusCD(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function bajar_partida_cd(id_partida){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        $.ajax({
            type: 'GET',
            url: 'bajar_partida_cd/'+id_partida,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    listarAcusCD(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}