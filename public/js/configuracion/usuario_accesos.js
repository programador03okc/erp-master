$(document).ready(function () {
    accesosUsuario();
});
function accesosUsuario() {
    var array_modulo = [],
        array_sub_modulo = [],
        html='';
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'accesos-usuario/'+$('[name="id_usuario"]').val(),
        data: {},
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            // if (response.data.length>0) {
            //     $.each(response.data, function (index, element) {

            //         if (array_modulo.indexOf(element.id_modulo)===-1) {
            //             html+='<div class="col-md-12" data-count="col">'
            //                 html+='<label data-id-modulo="'+element.id_modulo+'">'+element.modulo+'</label>';
            //                 array_modulo.push(element.id_modulo);
            //             html+='</div>';
            //             $('[data-accesos="select-accesos"]').append(html);
            //         }

            //     });
            // }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}
$(document).on('change','[data-select="modulos-select"]',function () {
    var data = $(this).val(),
        html="",
        modulo_old="",
        modulo_sub_old="";
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'get/modulos',
        data: {data:data},
        dataType: 'JSON',
        success: function(response){
            crearListaAccesos(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
function crearListaAccesos(response) {
    var html="",
        modulo_old="",
        modulo_sub_old="";
    $.each(response.sub_modulos, function (index, element) {
        html+='<div class="col-md-12">';
            if (modulo_old!==element.modulo) {
                html+='<label >'+element.modulo+'</label>';
                modulo_old= element.modulo;
            }
            if (element.acceso!=null) {
                html+='<div class="col-md-12">'
                    html+='<label class="btn" data-action="modulo-seleccionado" data-titulo="'+element.modulo+'" data-id-modulo="'+element.id_modulo+'" data-id-acceso="'+element.id_acceso+'" data-acceso="'+element.acceso+'" data-disabled="true" >'+element.acceso+'</label>'
                html+='</div>';
            }else{
                if (element.modulos_hijos.length>0) {
                    $.each(element.modulos_hijos, function (index_hijos, element_hijos) {

                        html+='<div class="col-md-12">';
                            if (modulo_sub_old!==element_hijos.modulo) {
                                html+='<label >'+element_hijos.modulo+'</label>';
                                modulo_sub_old= element_hijos.modulo;
                            }
                            if (element_hijos.acceso!=null) {
                                html+='<div class="col-md-12">'
                                    html+='<label class="btn" data-action="modulo-seleccionado" data-titulo="'+element.modulo+'" data-sub-titulo="'+element_hijos.modulo+'" data-id-modulo="'+element.id_modulo+'" data-id-sub-modulo="'+element_hijos.id_modulo+'" data-id-acceso="'+element_hijos.id_acceso+'" data-acceso="'+element_hijos.acceso+'" data-disabled="true">'+element_hijos.acceso+'</label>'
                                html+='</div>';
                            }

                        html+='</div>';
                    });

                }else{
                    html+='<div class="col-md-12" >Sin accesos</div>'
                }
            }

        html+='</div>';
    });
    $('[data-accesos="accesos"]').html(html);
    $('[data-accesos="accesos"]').removeClass('text-center');
}
array_title=[];
array_sub_title=[];
$(document).on('click','[data-action="modulo-seleccionado"]',function () {
    var titulo      =$(this).attr('data-titulo'),
        sub_titulo  =$(this).attr('data-sub-titulo'),
        id_modulo   =$(this).attr('data-id-modulo'),
        id_sub_modulo   =$(this).attr('data-id-sub-modulo'),
        id_acceso   =$(this).attr('data-id-acceso'),
        acceso      =$(this).attr('data-acceso'),
        html        ='',
        array_title_length = array_title.length,
        data_disable=$(this).attr('data-disabled');

    if (data_disable=='true') {
        $(this).attr('data-disabled','false');


        if (array_title.indexOf(id_modulo)===-1) {
            html+='<div class="col-md-12" data-count="col">'
                html+='<label data-id-modulo="'+id_modulo+'">'+titulo+'</label>';
                array_title.push(id_modulo);
            html+='</div>';
            $('[data-accesos="select-accesos"]').append(html);
        }

        html='';
        if (!sub_titulo) {
            html+='<div class="col-md-12">'
                html+='<label class="btn" data-action="disabled-accesos" data-id-acceso="'+id_acceso+'" data-action-id-modulo="'+id_modulo+'">'+acceso+'</label>'
                html+='<input type="hidden" value="'+id_acceso+'" name="id_acceso['+id_modulo+'][]" data-input="'+id_acceso+'">'
            html+='</div>'
            $('[data-accesos="select-accesos"] [data-id-modulo="'+id_modulo+'"]').append(html);
        }

        html='';
        if (sub_titulo) {
            if (array_sub_title.indexOf(id_sub_modulo)===-1) {
                html+='<div class="col-md-12" data-count="col-hijo">';
                    html+='<label data-id-sub-modulo="'+id_sub_modulo+'">'+sub_titulo+'</label>';
                html+='</div>';
                array_sub_title.push(id_sub_modulo);
                $('[data-accesos="select-accesos"] [data-id-modulo="'+id_modulo+'"]').append(html);
            }
        }
        html='';
        if (id_sub_modulo) {
            html+='<div class="col-md-12">'
                html+='<label class="btn" data-action="disabled-accesos" data-action-id-sub-modulo="'+id_sub_modulo+'" data-id-acceso="'+id_acceso+'">'+acceso+'</label>'
                html+='<input type="hidden" value="'+id_acceso+'" name="id_acceso['+id_sub_modulo+'][]" data-input="'+id_acceso+'">'
            html+='</div>';
            $('[data-accesos="select-accesos"] [data-id-sub-modulo="'+id_sub_modulo+'"]').append(html);
        }

        $('[data-accesos="select-accesos"]').removeClass('text-center');
        $('[data-action="text-selct"]').remove();
        $(this).attr('disabled',true);
    }
});
$(document).on('click','[data-action="disabled-accesos"]',function () {
    var id_acceso= $(this).attr('data-id-acceso')
    $('[data-id-acceso="'+id_acceso+'"]').attr('data-disabled','true');
    $('[data-id-acceso="'+id_acceso+'"]').removeAttr('disabled');
    $('[data-input="'+id_acceso+'"]').remove();
    $(this).parent().remove();

    var id_modulo = $(this).attr('data-action-id-modulo');
    var id_sub_modulo = $(this).attr('data-action-id-sub-modulo');

    if ($('[data-count="col-hijo"] div').length===0) {
        $('[data-count="col-hijo"]').remove();
        index_hijo = array_sub_title.indexOf(id_sub_modulo);
        array_sub_title.splice(index_hijo,1);
    }

    if ($('[data-count="col"] div').length===0) {
        index = array_title.indexOf(id_modulo);
        array_title.splice(index,1);
        $('[data-count="col"]').remove();
    }
});
$(document).on('click','[data-action="guardar"]',function () {
    var data = $('[data-form="accesos-seleccionados"]').serialize();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'guardar-accesos',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
