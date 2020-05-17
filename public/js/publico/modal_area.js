function cargarEstOrg(id){
    // limpiar();
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: '/cargar_estructura_org/' + id,
        dataType: 'JSON',
        success: function(response){
            $('#mostrar-arbol').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function showEfectOkc(id){
    $('#detalle-'+id).toggle();
}

function areaSelectModal(sede, grupo, area, text){
    // console.log('sede:'+sede+' grupo:'+grupo+' area:'+area + 'text: '+ text);
    $('[name=id_grupo]').val(grupo);
    $('[name=id_area]').val(area);
    $('[name=nombre_area]').val(text);
    $('#modal-empresa-area').modal('hide');
    
    if (page === 'requerimiento'){
        if(text == 'PROYECTOS' || text == 'DPTO. FORMULACIÓN' || text == 'DPTO. EJECUCIÓN'){
            document.getElementById('section-proyectos').setAttribute('class', 'col');
        }
        if(text == 'COMERCIAL' ||  text == 'DPTO. VENTAS'){
            document.getElementById('section-comercial').setAttribute('class', 'col');
        }
    }
    else if (page === 'equi_sol'){
        cambiarArea();
    }
    
}
