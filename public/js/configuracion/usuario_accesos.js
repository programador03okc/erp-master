var array_title=[];
    array_sub_title=[],
    array_disable_accesos=[];
$(document).ready(function () {
    accesosUsuario();
});
function accesosUsuario() {
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'accesos-usuario/'+$('[name="id_usuario"]').val(),
        data: {},
        dataType: 'JSON',
        beforeSend : function(){
            $('[data-select="modulos-select"]').attr('disabled',true);
            $('[data-form="accesos-seleccionados"] .loading').html('<div class="overlay"><i class="fa fa-spinner fa-spin"></i></div>');
        },
        success: function(response){
            if (response.data.length>0) {
                visualizarAccesos(response);
            }
            $('[data-select="modulos-select"]').removeAttr('disabled');
            $('[data-form="accesos-seleccionados"] .loading').remove();
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}
function disableAccesos() {
    var count_label=0,
        count_suma=0;

    $.each(array_disable_accesos, function (index_acceso, value_acceso) {
        $('[data-action="modulo-seleccionado"][data-id-acceso="'+value_acceso+'"]').attr('data-disabled','false');
        $('[data-action="modulo-seleccionado"][data-id-acceso="'+value_acceso+'"]').attr('disabled',true);
        $('[data-action="modulo-seleccionado"][data-id-acceso="'+value_acceso+'"]').addClass('texto-seleccionado');
    });


    array_sub_title.forEach(element => {
        count_label = $('.card.card-body[data-element-id-sub-modulo="'+element+'"] > div.col-md-12 label[data-disabled="true"]').length;
        count_suma=count_suma+count_label;
        if (count_label===0) {
            $('[data-check="selector-check"][data-id-modulo="'+element+'"]').prop('checked',true);
            $('[data-check="selector-check"][data-id-modulo="'+element+'"]').prop('disabled',true);
            if ($('[data-check="selector-check"][data-id-modulo="'+element+'"]').closest('.card.card-body > div.col-md-12 input[type="checkbox"]').is(':not(:checked)')===false && count_suma===0) {
                $('[data-check="selector-check"][data-id-modulo="'+element+'"]').closest('div.card.card-body').closest('div.col-md-12').find('input[name="nivel_1"]').prop('checked',true);
                $('[data-check="selector-check"][data-id-modulo="'+element+'"]').closest('div.card.card-body').closest('div.col-md-12').find('input[name="nivel_1"]').prop('disabled',true);
            }
        }
    });
    count_label=0;
    count_suma=0;
    array_title.forEach(element => {
        count_label = $('.card.card-body[data-element-id-modulo="'+element+'"] > div.col-md-12 label[data-disabled="true"]').length;
        if (count_label===0) {
            $('[data-check="selector-check"][data-id-modulo="'+element+'"]').prop('checked',true);
            $('[data-check="selector-check"][data-id-modulo="'+element+'"]').prop('disabled',true);
        }
    });
}
function visualizarAccesos(response) {
    var html='';
    $.each(response.data, function (index, element) {

        array_disable_accesos.push(element.id_acceso);
        var titulo = (element.id_padre !==0 ?element.modulo_padre.descripcion : element.accesos.modulos.descripcion ),
            sub_titulo  = (element.id_padre !==0 ?element.accesos.modulos.descripcion : null ),
            id_modulo = (element.id_padre !==0 ? element.id_padre : element.id_modulo ),
            id_sub_modulo = (element.id_padre !==0 ? element.id_modulo : null ),
            id_acceso = element.id_acceso,
            acceso = element.accesos.descripcion,
            html = '',
            data_disable = 'true',// $('[data-action="modulo-seleccionado"][data-id-acceso="'+element.id_acceso+'"]').attr('ata-disabled'),
            $this_componente = $('[data-action="modulo-seleccionado"][data-id-acceso="'+element.id_acceso+'"]');
        asignarAccesoss(titulo, sub_titulo, id_modulo, id_sub_modulo, id_acceso, acceso, html, data_disable, $this_componente);

    });
}
$(document).on('change','[data-select="modulos-select"]',function () {
    var data = $(this).val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'get/modulos',
        data: {data:data},
        dataType: 'JSON',
        beforeSend : function(){
            $('[data-select="modulos-select"]').attr('disabled',true);
            $('[data-accesos="accesos"]').html('<div class="overlay"><i class="fa fa-spinner fa-spin"></i></div>');
        },
        success: function(response){
            if (response.status===200) {
                $('[data-accesos="accesos"]').html('');
                crearListaAccesos(response);
            }else{
                $('[data-accesos="accesos"]').html('');
            }
            $('[data-select="modulos-select"]').removeAttr('disabled');
            $('[data-accesos="accesos"]').find('div.overlay').remove();

        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});

function crearListaAccesos(response) {
    var html="";
    var array_modulo_accesos = [],
        array_sub_modulo_accesos = [],
        array_sub_sub_modulos_accesos=[];

    $.each(response.sub_modulos, function (index, element) {
        // nivel 1
        if (array_modulo_accesos.indexOf(element.id_modulo)===-1) {
            html='';
            // html+='<div class="col-md-12">'
            //     // html+='<label data-id-modulo="'+id_modulo+'">'+titulo+'</label>';
            //     html+='<label data-element-id-modulo="'+element.id_modulo+'">'+element.modulo+'</label>';
            // html+='</div>';
            html+='<div class="col-md-12">'
                // html+='<label data-id-modulo="'+id_modulo+'">'+titulo+'</label>';
                html+='<input class="form-check-input" type="checkbox" name="nivel_1" id="selecionar'+index+'" data-check="selector-check" data-id-modulo="'+element.id_modulo+'" value="1" >'
                html+='<label for="selecionar'+index+'"> '+ element.modulo+'</label>';
                html+='<div class="box-tools pull-right">'
                    html+='<button type="button" class="btn btn-box-tool" data-toggle="collapse" data-target="#collapse'+index+'" aria-expanded="false" aria-controls="collapseExample" data-action="box-tool"><i class="fa fa-plus"></i></button>'
                html+='</div>'
                html+='<div class="collapse" id="collapse'+index+'">'
                    html+='<div class="card card-body" data-element-id-modulo="'+element.id_modulo+'">'
                    html+='</div>'
                html+='</div>'
            html+='</div>';
            array_modulo_accesos.push(element.id_modulo);
            $('[data-accesos="accesos"]').append(html);

        }

        if (element.acceso!=null) {
            html='';
            html+='<div class="col-md-12">'
                // html+='<input class="form-check-input" type="checkbox" name="acceso_nivel_1" id="acceso_'+element.id_acceso+'">'
                html+='<label for="acceso_'+element.id_acceso+'" class="btn" data-action="modulo-seleccionado" data-titulo="'+element.modulo+'" data-id-modulo="'+element.id_modulo+'" data-id-acceso="'+element.id_acceso+'" data-acceso="'+element.acceso+'" data-disabled="true" >'+element.acceso+'</label>'
            html+='</div>';
            $('[data-accesos="accesos"] [data-element-id-modulo="'+element.id_modulo+'"]').append(html);
        }
        // fin del nivel 1---
        // nivel 2
        if (element.acceso===null && element.modulos_hijos.length>0 ) {
            $.each(element.modulos_hijos, function (index_hijos, element_hijos) {

                if ( array_sub_modulo_accesos.indexOf(element_hijos.id_modulo) ===-1 ) {
                    html='';
                    html+='<div class="col-md-12">';
                        html+='<input class="form-check-input" type="checkbox" name="nivel_2" id="selecionar'+element_hijos.id_modulo+'" data-check="selector-check" data-id-modulo="'+element_hijos.id_modulo+'" value="2">'
                        html+='<label for="selecionar'+element_hijos.id_modulo+'" >'+element_hijos.modulo+'</label>';
                        html+='<div ></div>'

                        html+='<div class="collapse in" id="collapse'+index+'">'
                            html+='<div class="card card-body" data-element-id-sub-modulo="'+element_hijos.id_modulo+'">'
                        html+='</div>'
                html+='</div>'
                    html+='</div>';
                    array_sub_modulo_accesos.push(element_hijos.id_modulo);
                    $('[data-accesos="accesos"] [data-element-id-modulo="'+element.id_modulo+'"]').append(html);
                }
                if (element_hijos.acceso!=null) {
                    html='';
                    html+='<div class="col-md-12">'
                        html+='<label class="btn" data-action="modulo-seleccionado" data-titulo="'+element.modulo+'" data-sub-titulo="'+element_hijos.modulo+'" data-id-modulo="'+element.id_modulo+'" data-id-sub-modulo="'+element_hijos.id_modulo+'" data-id-acceso="'+element_hijos.id_acceso+'" data-acceso="'+element_hijos.acceso+'" data-disabled="true">'+element_hijos.acceso+'</label>'
                    html+='</div>';
                    $('[data-accesos="accesos"] [data-element-id-modulo="'+element.id_modulo+'"] [data-element-id-sub-modulo="'+element_hijos.id_modulo+'"]').append(html);
                }

                if (element_hijos.acceso===null && element_hijos.modulos_hijos_hijos.length===0) {
                    html='';
                    html+='<div class="col-md-12">'
                        html+='<label class="">Sin accesos nivel 2</label>'
                    html+='</div>';
                    $('[data-accesos="accesos"] [data-element-id-modulo="'+element.id_modulo+'"] [data-element-id-sub-modulo="'+element_hijos.id_modulo+'"]').append(html);
                }

            });
        }
        if (element.acceso===null && element.modulos_hijos.length===0 ) {
            html='';
            html+='<div class="col-md-12">'
                html+='<label class="">Sin accesos nivel 1</label>'
            html+='</div>';
            $('[data-accesos="accesos"] [data-element-id-modulo="'+element.id_modulo+'"]').append(html);
        }

    });
    $('[data-accesos="accesos"]').removeClass('text-center');
    disableAccesos();
}

$(document).on('click','[data-action="modulo-seleccionado"]',function () {
    var titulo      =$(this).attr('data-titulo'),
        sub_titulo  =$(this).attr('data-sub-titulo'),
        id_modulo   =parseInt($(this).attr('data-id-modulo')),
        id_sub_modulo   =parseInt($(this).attr('data-id-sub-modulo')),
        id_acceso   =parseInt($(this).attr('data-id-acceso')),
        acceso      =$(this).attr('data-acceso'),
        html        ='',
        array_title_length = array_title.length,
        data_disable=$(this).attr('data-disabled'),
        $this_componente=$(this),
        count_label  = (id_sub_modulo)?$('.card.card-body[data-element-id-sub-modulo="'+id_sub_modulo+'"] > div.col-md-12 label[data-disabled="true"]').length:$('.card.card-body[data-element-id-modulo="'+id_modulo+'"] > div.col-md-12 label[data-disabled="true"]').length;

        // array_title.splice(index,1);
        if (array_disable_accesos.indexOf(id_acceso)===-1) {
            array_disable_accesos.push(id_acceso);
        }

        $(this).addClass('texto-seleccionado');
        asignarAccesoss(titulo, sub_titulo, id_modulo, id_sub_modulo, id_acceso, acceso, html, data_disable,$this_componente);

        // $.each($(this).closest('div.col-md-12').closest('.card.card-body').children(), function (index, element) {
        //     console.log(element);
        // });
        if (count_label===1 || count_label===0) {
            if (id_sub_modulo) {
                $('[data-check="selector-check"][data-id-modulo="'+id_sub_modulo+'"]').prop('checked',true);
                $('[data-check="selector-check"][data-id-modulo="'+id_sub_modulo+'"]').prop('disabled',true);
                if ($('.card.card-body[data-element-id-modulo="'+id_modulo+'"] > div.col-md-12 input[type="checkbox"]').is(':not(:checked)')===false) {
                    $('[data-check="selector-check"][data-id-modulo="'+id_modulo+'"]').prop('checked',true);
                    $('[data-check="selector-check"][data-id-modulo="'+id_modulo+'"]').prop('disabled',true);
                }
                // console.log($('.card.card-body[data-element-id-modulo="'+id_modulo+'"] > div.col-md-12 input[type="checkbox"]').is(':not(:checked)'));
            } else {
                $('[data-check="selector-check"][data-id-modulo="'+id_modulo+'"]').click();
            }
        }
        // console.log($('.card.card-body[data-element-id-sub-modulo="57"] > div.col-md-12 label[data-disabled="true"]').length);
});

function asignarAccesoss(titulo, sub_titulo, id_modulo, id_sub_modulo, id_acceso, acceso, html, data_disable,$this_componente) {
    if (data_disable=='true') {
        $this_componente.attr('data-disabled','false');
        if (array_title.indexOf(parseInt(id_modulo))===-1) {
            html+='<div class="col-md-12" data-count="col" data-key="'+id_modulo+'">'
                html+='<label data-id-modulo="'+id_modulo+'">'+titulo+'</label>';
            html+='</div>';
            array_title.push(parseInt(id_modulo));
            $('[data-accesos="select-accesos"]').append(html);

        }
        html='';
        if (!sub_titulo) {
            html+='<div class="col-md-12">'
                html+='<label class="btn" data-action="disabled-accesos" data-id-acceso="'+id_acceso+'" data-action-id-modulo="'+id_modulo+'">'+acceso+'</label>'
                html+='<input type="hidden" value="'+id_acceso+'" name="id_acceso['+id_modulo+'][]" data-input="'+id_acceso+'">'
                html+='<input type="hidden" value="'+id_acceso+'" name="id_modulo_padre['+0+']['+id_modulo+'][]" data-input="'+id_acceso+'">'
            html+='</div>'
            $('[data-accesos="select-accesos"] [data-id-modulo="'+id_modulo+'"]').append(html);
        }
        html='';
        if (sub_titulo) {
            if (array_sub_title.indexOf(id_sub_modulo)===-1) {
                html+='<div class="col-md-12" data-count="col-hijo" data-key="'+id_sub_modulo+'">';
                    html+='<label data-id-sub-modulo="'+id_sub_modulo+'">'+sub_titulo+'</label>';
                html+='</div>';
                array_sub_title.push(parseInt(id_sub_modulo));
                $('[data-accesos="select-accesos"] [data-id-modulo="'+id_modulo+'"]').append(html);
            }
        }
        html='';
        if (id_sub_modulo) {
            html+='<div class="col-md-12">'
                html+='<label class="btn" data-action="disabled-accesos" data-action-id-modulo="'+id_modulo+'" data-action-id-sub-modulo="'+id_sub_modulo+'" data-id-acceso="'+id_acceso+'">'+acceso+'</label>'
                html+='<input type="hidden" value="'+id_acceso+'" name="id_acceso['+id_sub_modulo+'][]" data-input="'+id_acceso+'">'
                html+='<input type="hidden" value="'+id_acceso+'" name="id_modulo_padre['+id_modulo+']['+id_sub_modulo+'][]" data-input="'+id_acceso+'">'
            html+='</div>';
            $('[data-accesos="select-accesos"] [data-id-sub-modulo="'+id_sub_modulo+'"]').append(html);
        }

        $('[data-accesos="select-accesos"]').removeClass('text-center');
        $('[data-action="text-selct"]').remove();
        $this_componente.attr('disabled',true);
    }
}
$(document).on('click','[data-action="disabled-accesos"]',function () {
    var id_acceso= $(this).attr('data-id-acceso')
    $('[data-id-acceso="'+id_acceso+'"]').attr('data-disabled','true');
    $('[data-id-acceso="'+id_acceso+'"]').removeAttr('disabled');
    $('[data-id-acceso="'+id_acceso+'"]').removeClass('texto-seleccionado');
    $('[data-input="'+id_acceso+'"]').remove();
    $(this).parent().remove();

    var id_modulo = $(this).attr('data-action-id-modulo');
    var id_sub_modulo = $(this).attr('data-action-id-sub-modulo');

    array_disable_accesos.splice(array_disable_accesos.indexOf(parseInt(id_acceso)),1);

    if ($('[data-count="col-hijo"][data-key="'+id_sub_modulo+'"] div').length===0) {
        $('[data-count="col-hijo"][data-key="'+id_sub_modulo+'"]').remove();
        index_hijo = array_sub_title.indexOf(parseInt(id_sub_modulo));
        array_sub_title.splice(index_hijo,1);
    }
    if ($('[data-count="col"][data-key="'+id_modulo+'"] div').length===0) {
        index = array_title.indexOf(parseInt(id_modulo));
        array_title.splice(index,1);
        $('[data-count="col"][data-key="'+id_modulo+'"]').remove();
    }

    $('[data-check="selector-check"][data-id-modulo="'+id_modulo+'"]').prop('disabled',false);
    $('[data-check="selector-check"][data-id-modulo="'+id_modulo+'"]').prop('checked', false);
    $('[data-check="selector-check"][data-id-modulo="'+id_sub_modulo+'"]').prop('disabled',false);
    $('[data-check="selector-check"][data-id-modulo="'+id_sub_modulo+'"]').prop('checked', false);

});
$(document).on('click','[data-action="guardar"]',function () {
    var data = $('[data-form="accesos-seleccionados"]').serialize();
    Swal.fire({
        title: 'Guardar',
        text: "¿Esta seguro de guardar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: 'guardar-accesos',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    if (response.status===200) {
                        Swal.fire(
                            'Éxito',
                            'Se guardo con éxito su registro',
                            'success'
                        )
                    }
                }
            }).fail( function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            })
        }
    })

});
$(document).on('change','[data-check="selector-check"]',function () {
    var id_modulo = $(this).attr('data-id-modulo');

    if($(this).prop("checked") == true) {
        if ($(this).val()==1) {
            $('[data-action="modulo-seleccionado"][data-id-modulo="'+id_modulo+'"]').click();
            $('[data-element-id-modulo="'+id_modulo+'"]').find('[name="nivel_2"]').click();
        } else {
            $('[data-action="modulo-seleccionado"][data-id-sub-modulo="'+id_modulo+'"]').click()
        }
        $(this).attr('disabled','true');
    }else{
        // if ($(this).val()==1) {
        //     $('[data-action="modulo-seleccionado"][data-id-modulo="'+id_modulo+'"]').click()
        //     $('[data-element-id-modulo="'+id_modulo+'"]').find('[name="nivel_2"]').click();

        // } else {
        //     $('[data-action="modulo-seleccionado"][data-id-sub-modulo="'+id_modulo+'"]').click()
        // }
    }
});
$(document).on('click','[data-action="box-tool"]',function () {
    // $(this).find('i').remove();
    var success=$(this).attr('aria-expanded');
    if (success=='true') {
        $(this).find('i').remove();
        $(this).html('<i class="fa fa-minus"></i>');
    }else{
        $(this).find('i').remove();
        $(this).html('<i class="fa fa-plus"></i>');
    }
});
