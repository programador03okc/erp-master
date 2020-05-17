let unitario_temporal = 0;

function listar_cd(id_pres){
    $.ajax({
        type: 'GET',
        url: 'listar_cd/'+id_pres,
        dataType: 'JSON',
        success: function(response){
            var html = '';
            var estado = (($('#estado').text() == '1') ? '' : 'readOnly');
            console.log('estado:'+estado);
            response['data'].forEach(element => {
                html+='<tr class="blue info" style="font-size: 14px;">'+
                '<th></th>'+
                '<th>'+element.descripcion+'</th>'+
                '<th></th>'+
                '<th></th>'+
                '<th></th>'+
                '<th class="right">'+formatNumber.decimal(element.suma,'',-4)+'</th>'+
                '<th></th>'+
                '</tr>';

                element.insumos.forEach(ins => {
                    var color = (ins.count_precio > 1 ? 'primary' : 'info');
                    html+='<tr id="'+ins.id_insumo+'" style="font-size: 13px;">'+
                    '<td>'+ins.codigo+'</td>'+
                    '<td>'+ins.descripcion+'</td>'+
                    '<td>'+ins.abreviatura+'</td>';
                    if (ins.id_categoria == 1){//categoria aproximados
                        html+='<td></td><td></td>';
                    } else {
                        html+='<td class="right">'+formatNumber.decimal(ins.cantidad,'',-4)+'</td>'+
                        '<td class="right"><input type="number" class="form-control right" style="width:160px;" name="unitario" onFocus="copiar_unitario('+ins.id_insumo+');" onBlur="actualizar_unitario('+ins.id_insumo+');" value="'+ins.precio_unitario+'" '+estado+'/></td>';
                    }
                    html+='<td class="right">'+formatNumber.decimal(ins.importe_parcial,'',-6)+'</td>'+
                    '<td>'+
                    '<i class="fas fa-list-alt btn btn-'+color+' visible boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver partidas enlazadas" onClick="ver_partida_insumo('+id_pres+','+ins.id_insumo+','+"'"+ins.codigo+' - '+ins.descripcion+"'"+');"></i>'+
                    // '<button type="button" class="ver btn btn-'+color+' boton" data-toggle="tooltip" '+
                    // 'data-placement="bottom" title="Ver Precios"><i class="fas fa-list-alt"></i></button>'+
                    '</td>'+
                    '</tr>';
                });
            });
            var mnd = $('[name=simbolo]').val();

            html+='<tr class="red danger" style="font-size: 14px;">'+
                    '<th colSpan="5" class="right">TOTAL  '+mnd+'</th>'+
                    '<th class="right" >'+formatNumber.decimal(response['total'],'',-2)+'</th>'+
                    '<th></th>'+
                    '</tr>';
            $('#listaCD tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function refresh_cd(){
    var id = $('[name=id_presupuesto]').val();
    listar_cd(id);
    totales(id);
}

function copiar_unitario(id_insumo){
    unitario_temporal = $("#"+id_insumo+" td").find("input[name=unitario]").val();
    console.log('unitario_temporal : '+unitario_temporal);
}

function actualizar_unitario(id_insumo){
    var id = $('[name=id_presupuesto]').val();
    var unit = $("#"+id_insumo+" td").find("input[name=unitario]").val();

    if (unitario_temporal !== unit){
        var cant = $("#"+id_insumo+" td")[3].innerText;
        var tot = cant * unit;
        $("#"+id_insumo+" td")[5].innerText = formatNumber.decimal(tot,'',-6);

        $.ajax({
            type: 'POST',
            url: 'update_unitario_partida_cd',
            data: 'id_presupuesto='+id+'&id_insumo='+id_insumo+'&unitario='+unit,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response['data'] > 0){
                    $('[name=total_costo_directo]').val(formatDecimal(response['totales'].total_costo_directo));
                    $('[name=porcentaje_ci]').val(formatDecimal(response['totales'].porcentaje_ci));
                    $('[name=total_ci]').val(formatDecimal(response['totales'].total_ci));
                    $('[name=porcentage_gg]').val(formatDecimal(response['totales'].porcentage_gg));
                    $('[name=total_gg]').val(formatDecimal(response['totales'].total_gg));
                    $('[name=sub_total]').val(formatDecimal(response['totales'].sub_total));
                    $('[name=porcentaje_utilidad]').val(formatDecimal(response['totales'].porcentaje_utilidad));
                    $('[name=total_utilidad]').val(formatDecimal(response['totales'].total_utilidad));
                    $('[name=porcentaje_igv]').val(formatDecimal(response['totales'].porcentaje_igv));
                    $('[name=total_igv]').val(formatDecimal(response['totales'].total_igv));
                    $('[name=total_presupuestado]').val(formatDecimal(response['totales'].total_presupuestado));
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

//Partida CD

function agregar_componente_cd(){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        if (id_pres !== ''){
            var titulo = prompt("Ingrese un nombre al título", "Ingrese un título...");
            if (titulo != null) {
                var i = 1;
                var filas = document.querySelectorAll('#listaAcusCD tbody tr');
                filas.forEach(function(e){
                    var colum = e.querySelectorAll('td');
                    var padre = colum[9].innerText;
                    console.log('padre: '+padre);
                    if (padre == ''){
                        i++;
                    }
                });
                var data = 'id_pres='+id_pres+'&codigo='+leftZero(2,i)+'&descripcion='+titulo+'&cod_compo=';
                console.log('data guardar:'+data);
                guardar_componente_cd(data, id_pres);
            }
        } else {
            alert('Debe seleccionar un ingresar un Presupuesto');
        }
    }
}

function agregar_compo_cd(cod_compo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var titulo = prompt("Ingrese un nombre al título", "Ingrese un título..");
        if (titulo != null) {
            var i = 1;
            var filas = document.querySelectorAll('#listaAcusCD tbody tr');
            filas.forEach(function(e){
                var colum = e.querySelectorAll('td');
                var padre = colum[9].innerText;
                var unid = colum[3].innerText;
                if (padre == cod_compo && unid == ''){
                    i++;
                }
            });
            var id_pres = $('[name=id_presupuesto]').val();
            var codigo = cod_compo+'.'+leftZero(2,i);
            var data =  'id_pres='+id_pres+'&codigo='+codigo+
                        '&descripcion='+titulo+'&cod_compo='+cod_compo;
            guardar_componente_cd(data, id_pres);
        } else {
            alert("No ha ingresado ningun valor.");
        }
    }
}

function guardar_componente_cd(data, id_pres){
    var rspta = confirm("¿Esta seguro que desea guardar el titulo?");
    if (rspta){
        $.ajax({
            type: 'POST',
            url: 'guardar_componente_cd',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log('response guardar cd: '+response);
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

function editar_compo_cd(id_cd_compo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        $("#com-"+id_cd_compo+" td").find("input[name=descripcion]").attr('disabled',false);
        $("#com-"+id_cd_compo+" td").find("i.blue").removeClass('visible');
        $("#com-"+id_cd_compo+" td").find("i.blue").addClass('oculto');
        $("#com-"+id_cd_compo+" td").find("i.green").removeClass('oculto');
        $("#com-"+id_cd_compo+" td").find("i.green").addClass('visible');
    }
}

function update_compo_cd(id_cd_compo){
    var des = $("#com-"+id_cd_compo+" td").find("input[name=descripcion]").val();
    var data =  'id_cd_compo='+id_cd_compo+
                '&descripcion='+des;
    var id_pres = $('[name=id_presupuesto]').val();
    $.ajax({
        type: 'POST',
        url: 'update_componente_cd',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Título actualizado con éxito');
                $("#com-"+id_cd_compo+" td").find("input[name=descripcion]").attr('disabled',true);
                $("#com-"+id_cd_compo+" td").find("i.blue").removeClass('oculto');
                $("#com-"+id_cd_compo+" td").find("i.blue").addClass('visible');
                $("#com-"+id_cd_compo+" td").find("i.green").removeClass('visible');
                $("#com-"+id_cd_compo+" td").find("i.green").addClass('oculto');
                listarAcusCD(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_compo_cd(id_cd_compo,codigo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var anula = confirm("¿Esta seguro que desea anular éste titulo?");
        if (anula){
            var cod_padre = '';
            var hijos_com = [];
            var hijos_par = [];
            var i = 0;

            var filas = document.querySelectorAll('#listaAcusCD tbody tr');
            filas.forEach(function(e){
                var ids = (e.id).split('-');
                var colum = e.querySelectorAll('td');
                cod_padre = colum[9].innerText;
                
                if (cod_padre === codigo){
                    if (ids[0] === "com"){
                        hijos_com[i] = ids[1];
                    } 
                    else if (ids[0] === "par"){
                        hijos_par[i] = ids[1];
                    }
                    i++;
                }
            });
            var rspta = true;
            if (hijos_com.length > 0 || hijos_par.length > 0){
                rspta = confirm("Este titulo tiene dependientes. \n¿Está seguro que desea anularlo con sus dependientes?");
            }
            if (rspta) {
                var id_pres = $('[name=id_presupuesto]').val();
                var data =  'id_cd_compo='+id_cd_compo+
                            '&cod_compo='+cod_padre+
                            '&id_pres='+id_pres+
                            '&hijos_com='+hijos_com+
                            '&hijos_par='+hijos_par;
                console.log(data);

                $.ajax({
                    type: 'POST',
                    url: 'anular_compo_cd',
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('Titulo anulado con éxito');
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
}

