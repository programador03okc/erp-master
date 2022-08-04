
$(function(){
    var id_usuario = localStorage.getItem("id_usuario");
    getUsuario(id_usuario);
    getModulos()
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
function getModulos() {
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'get/modulos/',
        data: {},
        dataType: 'JSON',
        success: function(response){
            crearNavTabs(response);

        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function crearNavTabs(json) {
    var html ='',  hrml_panel='';
    $.each(json.padre, function (index, element) {
        if (index==0) {
            html +='<li role="presentation" class="active"><a href="#modulo'+element.id_modulo+'" aria-controls="modulo'+element.id_modulo+'" role="tab" data-toggle="tab" data-id="'+element.id_modulo+'" data-action="navTabs"> '+element.descripcion+' </a></li>'

            hrml_panel +='<div role="tabpanel" class="tab-pane active" id="modulo'+element.id_modulo+'">'
                hrml_panel +='<div class="panel panel-default">'
                    hrml_panel +='<div class="panel-body" style="overflow: scroll; height: 35vh;">'
                        hrml_panel +='<div class="row">'
                            hrml_panel +='<div class="col-md-12" data-select="tabs-'+element.id_modulo+'" data-id="'+element.id_modulo+'">'
                            hrml_panel +='</div>'
                        hrml_panel +='</div>'
                    hrml_panel +='</div>'
                hrml_panel +='</div>'
            hrml_panel +='</div>'

        } else {
            html +='<li role="presentation" class=""><a href="#modulo'+element.id_modulo+'" aria-controls="modulo'+element.id_modulo+'" role="tab" data-toggle="tab" data-id="'+element.id_modulo+'" data-action="navTabs"> '+element.descripcion+' </a></li>'

            hrml_panel +='<div role="tabpanel" class="tab-pane" id="modulo'+element.id_modulo+'" data-tab="sub-modulo-'+element.id_modulo+'">'
                hrml_panel +='<div class="panel panel-default">'
                    hrml_panel +='<div class="panel-body" style="overflow: scroll; height: 35vh;">'
                        hrml_panel +='<div class="row">'
                            hrml_panel +='<div class="col-md-12" data-select="tabs-'+element.id_modulo+'" data-id="'+element.id_modulo+'">'
                            hrml_panel +='</div>'
                        hrml_panel +='</div>'
                    hrml_panel +='</div>'
                hrml_panel +='</div>'
            hrml_panel +='</div>'

        }

    });
    $('#tab_modulos').html(html);
    $('#tabpanel_modulos').html(hrml_panel);
    // $('[data-action="navTabs"]').click();

}

$(document).on('click','[data-action="navTabs"]',function (e) {
    e.preventDefault();
    var id_modulo = $(this).attr('data-id');
    getModulosHijos(id_modulo);
});
function getModulosHijos(id_modulo) {
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'get/modulos/hijos/'+id_modulo,
        data: {},
        dataType: 'JSON',
        success: function(response){

        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
