
$(function(){
    var id_usuario = localStorage.getItem("id_usuario");
    $('[name="id_usuario"]').val(id_usuario);
    getUsuario(id_usuario);
    getModulos(id_usuario)
})
function getUsuario(id) {
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'get/usuario/'+id,
        data: {},
        dataType: 'JSON',
        success: function(response){
            $('[data-name="name"]').text(response.nombre_completo_usuario);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
function getModulos(id_usuario) {
    var data_selector = $('[data-selector="tap-pane"]');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'get/modulos/',
        data: {id_usuario:id_usuario},
        dataType: 'JSON',
        success: function(response){
            crearNavTabs(response,data_selector);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function crearNavTabs(json,data_selector) {
    var html ='',  hrml_panel='';
    $.each(json.padre, function (index, element) {
        if (index==0) {
            html +='<li role="presentation" class="active"><a dat-click="'+index+'" href="#modulo'+element.id_modulo+'" aria-controls="modulo'+element.id_modulo+'" role="tab" data-toggle="tab" data-id="'+element.id_modulo+'" data-action="navTabs"> '+element.descripcion+' </a></li>'

            hrml_panel +='<div role="tabpanel" class="tab-pane active" data-selector="tap-pane" id="modulo'+element.id_modulo+'">'
                hrml_panel +='<div class="panel panel-default">'
                    hrml_panel +='<div class="panel-body" style="overflow: scroll; ">'
                        hrml_panel +='<div class="row">'
                            hrml_panel +='<div class="col-md-12" data-select="tabs-'+element.id_modulo+'" data-id="'+element.id_modulo+'">'
                            hrml_panel +='</div>'
                        hrml_panel +='</div>'
                    hrml_panel +='</div>'
                hrml_panel +='</div>'
            hrml_panel +='</div>'

        } else {
            html +='<li role="presentation" class=""><a href="#modulo'+element.id_modulo+'" aria-controls="modulo'+element.id_modulo+'" role="tab" data-toggle="tab" data-id="'+element.id_modulo+'" data-action="navTabs"> '+element.descripcion+' </a></li>'

            hrml_panel +='<div role="tabpanel" class="tab-pane" data-selector="tap-pane" id="modulo'+element.id_modulo+'" data-tab="sub-modulo-'+element.id_modulo+'">'
                hrml_panel +='<div class="panel panel-default">'
                    hrml_panel +='<div class="panel-body" style="overflow: scroll;">'
                        hrml_panel +='<div class="row" data-select="tabs-'+element.id_modulo+'" data-id="'+element.id_modulo+'">'
                        hrml_panel +='</div>'
                    hrml_panel +='</div>'
                hrml_panel +='</div>'
            hrml_panel +='</div>'
        }
    });
    $('#tab_modulos').html(html);
    $('#tabpanel_modulos').html(hrml_panel);
    var html_hijo='';
    $.each(json.hijo, function (index, element) {
        html_hijo='';
        html_hijo +='<div class="col-md-12">'
            html_hijo +='<div class="row">'
                html_hijo +='<div class="col-md-4">'
                    html_hijo +='<label style="">'
                        html_hijo +='<input type="checkbox"  name="checkModulo[]" data-id-padre="'+element.id_padre+'" data-id-modulo="'+element.id_modulo+'" value="'+element.id_modulo+'" data-check="selector"> '
                        html_hijo +=element.descripcion
                    html_hijo +='</label>'
                html_hijo +='</div>'

                html_hijo +='<div class="col-md-2" data-id-modulo="'+element.id_modulo+'">'
                    html_hijo +='<label style="display:block; ">'
                        html_hijo +='<input type="checkbox"  name="ver['+element.id_modulo+'][]" value="1" data-id-modulo="'+element.id_modulo+'" data-selector-input="ver">'
                        html_hijo +=' Ver'
                    html_hijo +='</label>'
                html_hijo +='</div>'
                html_hijo +='<div class="col-md-2" data-id-modulo="'+element.id_modulo+'">'
                    html_hijo +='<label style="display:block; ">'
                        html_hijo +='<input type="checkbox"  name="nuevo['+element.id_modulo+'][]" value="1" data-id-modulo="'+element.id_modulo+'" data-selector-input="nuevo">'
                        html_hijo +=' Nuevo'
                    html_hijo +='</label>'
                html_hijo +='</div>'
                html_hijo +='<div class="col-md-2" data-id-modulo="'+element.id_modulo+'">'
                    html_hijo +='<label style="display:block;">'
                        html_hijo +='<input type="checkbox"  name="modificar['+element.id_modulo+'][]" value="1" data-id-modulo="'+element.id_modulo+'" data-selector-input="modificar">'
                        html_hijo +=' Modificar'
                    html_hijo +='</label>'
                html_hijo +='</div>'
                html_hijo +='<div class="col-md-2" data-id-modulo="'+element.id_modulo+'">'
                    html_hijo +='<label style="display:block; ">'
                        html_hijo +='<input type="checkbox"  name="eliminar['+element.id_modulo+'][]" value="1" data-id-modulo="'+element.id_modulo+'" data-selector-input="eliminar">'
                        html_hijo +=' Eliminar'
                    html_hijo +='</label>'
                html_hijo +='</div>'

            html_hijo +='</div>'

            html_hijo +='<div class="row">'
                html_hijo +='<div class="col-md-4" data-sub-hijo="selctor-modulo" data-id-modulo="'+element.id_modulo+'"></div>';

                html_hijo +='<div class="col-md-2" data-action="ver" data-id-modulo="'+element.id_modulo+'"></div>';
                html_hijo +='<div class="col-md-2" data-action="nuevo" data-id-modulo="'+element.id_modulo+'"></div>';
                html_hijo +='<div class="col-md-2" data-action="modificar" data-id-modulo="'+element.id_modulo+'"></div>';
                html_hijo +='<div class="col-md-2" data-action="eliminar" data-id-modulo="'+element.id_modulo+'"></div>';

            html_hijo +='</div>'

            html_hijo +='<hr />'
        html_hijo +='</div>'

        $('[data-selector="tap-pane"] [data-select="tabs-'+element.id_padre+'"]').append(html_hijo);
    });
    var html_ver='' ,html_nuevo='', html_modificar='', html_eliminar='';
    $.each(json.sub_hijo, function (index_hijo, element_hijo) {
        html_ver='' ;
        html_nuevo='';
        html_modificar='';
        html_eliminar='';

        html_hjo ='';
        html_hjo +=''
            html_hjo +='<label style="display:block; margin-left:21px">'
                html_hjo +='<input type="checkbox"  name="checkModulo[]" data-id-padre="'+element_hijo.id_padre+'" data-id-modulo="'+element_hijo.id_modulo+'" value="'+element_hijo.id_modulo+'" data-check="selector" data-checkbox="selector-subhijo"> '
                html_hjo += element_hijo.descripcion
            html_hjo +='</label>';

        html_ver +='<label style="display:block; ">'
            html_ver +='<input type="checkbox"  name="ver['+element_hijo.id_modulo+'][]" value="1" data-id-modulo="'+element_hijo.id_modulo+'" data-selector-input="ver">'
            html_ver +=' Ver'
        html_ver +='</label>';

        html_nuevo +='<label style="display:block; ">'
            html_nuevo +='<input type="checkbox"  name="nuevo['+element_hijo.id_modulo+'][]" value="1" data-id-modulo="'+element_hijo.id_modulo+'" data-selector-input="nuevo">'
            html_nuevo +=' Nuevo'
        html_nuevo +='</label>';

        html_modificar +='<label style="display:block;">'
            html_modificar +='<input type="checkbox"  name="modificar['+element_hijo.id_modulo+'][]" value="1" data-id-modulo="'+element_hijo.id_modulo+'" data-selector-input="modificar">'
            html_modificar +=' Modificar'
        html_modificar +='</label>';

        html_eliminar +='<label style="display:block; ">'
            html_eliminar +='<input type="checkbox"  name="eliminar['+element_hijo.id_modulo+'][]" value="1" data-id-modulo="'+element_hijo.id_modulo+'" data-selector-input="eliminar">'
            html_eliminar +=' Eliminar'
        html_eliminar +='</label>';

        $('[data-sub-hijo="selctor-modulo"][data-id-modulo="'+element_hijo.id_padre+'"]').append(html_hjo);

        $('[data-action="ver"][data-id-modulo="'+element_hijo.id_padre+'"]').append(html_ver);
        $('[data-action="nuevo"][data-id-modulo="'+element_hijo.id_padre+'"]').append(html_nuevo);
        $('[data-action="modificar"][data-id-modulo="'+element_hijo.id_padre+'"]').append(html_modificar);
        $('[data-action="eliminar"][data-id-modulo="'+element_hijo.id_padre+'"]').append(html_eliminar);
    });
    $.each(json.accesos, function (index, element) {
        $('[data-id-modulo="'+element.id_modulo+'"][data-check="selector"]').attr('checked', true);
        if (element.ver==1) {
            $('[data-id-modulo="'+element.id_modulo+'"][data-selector-input="ver"]').attr('checked', true);
        }
        if (element.nuevo==1) {
            $('[data-id-modulo="'+element.id_modulo+'"][data-selector-input="nuevo"]').attr('checked', true);
        }
        if (element.modificar==1) {
            $('[data-id-modulo="'+element.id_modulo+'"][data-selector-input="modificar"]').attr('checked', true);
        }
        if (element.eliminar==1) {
            $('[data-id-modulo="'+element.id_modulo+'"][data-selector-input="eliminar"]').attr('checked', true);
        }
    });
}

$(document).on('change','[data-checkbox="selector-subhijo"]',function () {
    var id_padre = $(this).attr('data-id-padre'),
        contador_checked=0;
    $('[data-checkbox="selector-subhijo"][data-id-padre="'+id_padre+'"]').each(function() {
        if ($(this).is(':checked')) {
            contador_checked++;
        }
    });
    if($(this).is(':checked')){
        $('[data-check="selector"][data-id-modulo="'+id_padre+'"]').attr('checked', true);
    } else {
        if (contador_checked===0) {
           $('[data-check="selector"][data-id-modulo="'+id_padre+'"]').removeAttr('checked');
        }
    }
})

// $(document).on('click','[data-check="selector"]',function () {
//     var id_modulo = $(this).attr('data-id-modulo');
//     if($(this).is(':checked')){
//         $('[data-checkbox="selector-subhijo"][data-id-padre="'+id_modulo+'"]').attr('checked', true);
//     } else {
//         $('[data-checkbox="selector-subhijo"][data-id-padre="'+id_modulo+'"]').removeAttr('checked');
//     }
// })

$(document).on('submit','[data-form="enviar-data"]',function (e) {
    e.preventDefault();
    var data= $(this).serialize();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'asignar/modulos',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});
