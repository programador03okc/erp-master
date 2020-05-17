function nuevo_presEstructura(){
    $('#form-presEstructura')[0].reset();
    $('#codigo').text('');
    $('[name=descripcion]').val('Presupuesto Base');
    $('#listaPresupuesto tbody').html('');
}

function save_pres_estructura(data, action){
    console.log(action);
    console.log(data);

    if (action == 'register'){
        baseUrl = 'guardar_pres_estructura';
    } else if (action == 'edition'){
        baseUrl = 'update_pres_estructura';
    }
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('[name=id_presup]').val(response);
                alert('Se guardó exitosamente!');
                changeStateButton('guardar');
            } else {
                $('[name=id_presup]').val('');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_presupuesto(id_pres){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_presupuesto/'+id_pres,
        dataType: 'JSON',
        success: function(response){
            $('#listaPresupuesto tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cargar_grupos(){
    var id_sede = $('[name=id_sede]').val();
    console.log(id_sede);
    if (id_sede !== ''){
        $.ajax({
            type: 'GET',
            url: 'cargar_grupos/'+id_sede,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var option = '';
                var id_empresa = 0;
                for (var i=0; i<response.length; i++){
                    id_empresa = response[i].id_empresa;
                    option+='<option value="'+response[i].id_grupo+'">'+response[i].descripcion+'</option>';
                }
                $('[name=id_empresa]').val(id_empresa);
                $('[name=id_grupo]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function agregar_primer_titulo(){
    var id_pres = $('[name=id_presup]').val();
    if (id_pres !== ''){
        var titulo = prompt("Ingrese un nombre al título", "Ingrese un título...");
        if (titulo != null) {
            var i = 1;
            var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
            filas.forEach(function(e){
                var colum = e.querySelectorAll('td');
                var padre = colum[5].innerText;
                if (padre == ''){
                    i++;
                }
            });
            var data =  'id_presup='+id_pres+'&codigo='+leftZero(2,i)+
                        '&descripcion='+titulo+'&cod_padre=';
            guardar_titulo(data, id_pres);
        }
    } else {
        alert('Debe seleccionar un ingresar un Presupuesto');
    }
}

function agregar_titulo(cod_padre){
    console.log('cod_padre'+cod_padre);
    var titulo = prompt("Ingrese un nombre al título", "Ingrese un título..");
    if (titulo != null) {
        var i = 1;
        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[5].innerText;
            var rel = colum[3].innerText;
            console.log(colum[3].innerText);
            console.log('rel: '+rel);
            if (padre == cod_padre && rel == ''){
                i++;
            }
        });
        var id_pres = $('[name=id_presup]').val();
        var codigo = cod_padre+'.'+leftZero(2,i);
        var data =  'id_presup='+id_pres+'&codigo='+codigo+
                    '&descripcion='+titulo+'&cod_padre='+cod_padre;

        guardar_titulo(data, id_pres);
    } else {
        alert("No ha ingresado ningun valor.");
    }
}

function guardar_titulo(data, id_pres){
    // var token = $('#token').val();
    console.log(data);
    var rspta = confirm("¿Esta seguro que desea guardar el titulo?");
    if (rspta){
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_titulo',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    listar_presupuesto(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function editar_titulo(id_titulo){
    $("#ti-"+id_titulo+" td").find("input[name=descripcion]").attr('disabled',false);
    $("#ti-"+id_titulo+" td").find("i.blue").removeClass('visible');
    $("#ti-"+id_titulo+" td").find("i.blue").addClass('oculto');
    $("#ti-"+id_titulo+" td").find("i.green").removeClass('oculto');
    $("#ti-"+id_titulo+" td").find("i.green").addClass('visible');
}

function update_titulo(id_titulo){
    var des = $("#ti-"+id_titulo+" td").find("input[name=descripcion]").val();
    var data =  'id_titulo='+id_titulo+
                '&descripcion='+des;
    var id_pres = $('[name=id_presup]').val();

    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'update_titulo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // alert('Título actualizado con éxito');
                $("#ti-"+id_titulo+" td").find("input[name=descripcion]").attr('disabled',true);
                $("#ti-"+id_titulo+" td").find("i.blue").removeClass('oculto');
                $("#ti-"+id_titulo+" td").find("i.blue").addClass('visible');
                $("#ti-"+id_titulo+" td").find("i.green").removeClass('visible');
                $("#ti-"+id_titulo+" td").find("i.green").addClass('oculto');
                listar_presupuesto(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_titulo(id_titulo, codigo){
    var anula = confirm("¿Esta seguro que desea anular éste título?");
    if (anula){
        var cod_padre = '';
        var hijos_titu = [];
        var hijos_par = [];
        var i = 0;

        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        filas.forEach(function(e){
            var ids = (e.id).split('-');
            var colum = e.querySelectorAll('td');
            cod_padre = colum[4].innerText;
            
            if (cod_padre === codigo){
                if (ids[0] === "ti"){
                    hijos_titu[i] = ids[1];
                } 
                else if (ids[0] === "par"){
                    hijos_par[i] = ids[1];
                }
                i++;
            }
        });
        var rspta = true;
        if (hijos_titu.length > 0 || hijos_par.length > 0){
            rspta = confirm("Este titulo tiene dependientes. \n¿Está seguro que desea anularlo con sus dependientes?");
        }
        if (rspta) {
            var id_pres = $('[name=id_presup]').val();
            var data =  'id_titulo='+id_titulo+
                        '&cod_padre='+cod_padre+
                        '&id_presup='+id_pres+
                        '&hijos_titu='+hijos_titu+
                        '&hijos_par='+hijos_par;
            console.log(data);
    
            $.ajax({
                type: 'POST',
                url: 'anular_titulo',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        // alert('Titulo anulado con éxito');
                        listar_presupuesto(id_pres);
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
