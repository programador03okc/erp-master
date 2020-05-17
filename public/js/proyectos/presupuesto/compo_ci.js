function listar_ci(id_pres){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_ci/'+id_pres,
        dataType: 'JSON',
        success: function(response){
            $('#listaCI tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function agregar_componente_ci(){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede agregar en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        if (id_pres !== ''){
            var titulo = prompt("Ingrese un nombre al título", "Ingrese un título...");
            if (titulo != null) {
                var i = 1;
                var filas = document.querySelectorAll('#listaCI tbody tr');
                filas.forEach(function(e){
                    var colum = e.querySelectorAll('td');
                    var padre = colum[11].innerText;
                    if (padre == ''){
                        i++;
                    }
                });
                var data = 'id_pres='+id_pres+'&codigo='+leftZero(2,i)+'&descripcion='+titulo+'&cod_compo=';
                guardar_componente_ci(data, id_pres);
            }
        } else {
            alert('Debe seleccionar un ingresar un Presupuesto');
        }
    }
}
function agregar_compo_ci(cod_compo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede agregar en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var titulo = prompt("Ingrese un nombre al título", "Ingrese un título..");
        if (titulo != null) {
            var i = 1;
            var filas = document.querySelectorAll('#listaCI tbody tr');
            filas.forEach(function(e){
                var colum = e.querySelectorAll('td');
                var padre = colum[11].innerText;
                var unid = colum[2].innerText;
                if (padre == cod_compo && unid == ''){
                    i++;
                }
            });
            var id_pres = $('[name=id_presupuesto]').val();
            var codigo = cod_compo+'.'+leftZero(2,i);
            var data =  'id_pres='+id_pres+'&codigo='+codigo+
                        '&descripcion='+titulo+'&cod_compo='+cod_compo;
            guardar_componente_ci(data, id_pres);
        } else {
            alert("No ha ingresado ningun valor.");
        }
    }
}
function guardar_componente_ci(data, id_pres){
    var rspta = confirm("¿Esta seguro que desea guardar el titulo?");
    if (rspta){
        $.ajax({
            type: 'POST',
            url: 'guardar_componente_ci',
            data: data,
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

function editar_compo_ci(id_ci_compo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede editar en un Presupuesto "'+des_estado+'"');
    } 
    else {
        $("#com-"+id_ci_compo+" td").find("input[name=descripcion]").attr('disabled',false);
        $("#com-"+id_ci_compo+" td").find("i.blue").removeClass('visible');
        $("#com-"+id_ci_compo+" td").find("i.blue").addClass('oculto');
        $("#com-"+id_ci_compo+" td").find("i.green").removeClass('oculto');
        $("#com-"+id_ci_compo+" td").find("i.green").addClass('visible');
    }
}

function update_compo_ci(id_ci_compo){
    var des = $("#com-"+id_ci_compo+" td").find("input[name=descripcion]").val();
    var data =  'id_ci_compo='+id_ci_compo+
                '&descripcion='+des;
    var id_pres = $('[name=id_presupuesto]').val();
    $.ajax({
        type: 'POST',
        url: 'update_componente_ci',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Título actualizado con éxito');
                $("#com-"+id_ci_compo+" td").find("input[name=descripcion]").attr('disabled',true);
                $("#com-"+id_ci_compo+" td").find("i.blue").removeClass('oculto');
                $("#com-"+id_ci_compo+" td").find("i.blue").addClass('visible');
                $("#com-"+id_ci_compo+" td").find("i.green").removeClass('visible');
                $("#com-"+id_ci_compo+" td").find("i.green").addClass('oculto');
                listar_ci(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_compo_ci(id_ci_compo,codigo){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede anular en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var anula = confirm("¿Esta seguro que desea anular éste titulo?");
        if (anula){
            var cod_padre = '';
            var hijos_com = [];
            var hijos_par = [];
            var i = 0;

            var filas = document.querySelectorAll('#listaCI tbody tr');
            filas.forEach(function(e){
                var ids = (e.id).split('-');
                var colum = e.querySelectorAll('td');
                cod_padre = colum[11].innerText;
                
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
                var data =  'id_ci_compo='+id_ci_compo+
                            '&cod_compo='+cod_padre+
                            '&id_pres='+id_pres+
                            '&hijos_com='+hijos_com+
                            '&hijos_par='+hijos_par;
                console.log(data);
        
                $.ajax({
                    type: 'POST',
                    url: 'anular_compo_ci',
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        if (response > 0){
                            alert('Titulo anulado con éxito');
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
}
function crear_titulos_ci(){
    var estado = $('#estado').text();
    var des_estado = $('#des_estado').text();
    if (estado !== '1'){
        alert('No puede realizar ésta acción en un Presupuesto "'+des_estado+'"');
    } 
    else {
        var id_pres = $('[name=id_presupuesto]').val();
        if (id_pres !== ''){
            var filas = document.querySelectorAll('#listaCI tbody tr');
            console.log(filas);
            console.log(filas.length);
            if (filas.length == 0){
                $.ajax({
                    type: 'GET',
                    // headers: {'X-CSRF-TOKEN': token},
                    url: 'crear_titulos_ci/'+id_pres,
                    // data: data,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            // alert('Titulo registrado con éxito');
                            listar_ci(id_pres);
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
            else {
                alert('Solo puede agregar si la lista esta vacía');
            }
        }
        else {
            alert('No existe un presupuesto seleccionado!');
        }
    }
}