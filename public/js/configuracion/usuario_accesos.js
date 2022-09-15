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
        success: function(response){

            if (response.data.length>0) {
                visualizarAccesos(response);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
}
function disableAccesos() {

    $.each(array_disable_accesos, function (index_acceso, value_acceso) {
        $('[data-action="modulo-seleccionado"][data-id-acceso="'+value_acceso+'"]').attr('data-disabled','false');
        $('[data-action="modulo-seleccionado"][data-id-acceso="'+value_acceso+'"]').attr('disabled',true);
        $('[data-action="modulo-seleccionado"][data-id-acceso="'+value_acceso+'"]').addClass('texto-seleccionado');
    });
    // $this_componente.attr('data-disabled','false');
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
        success: function(response){
            if (response.status===200) {
                $('[data-accesos="accesos"]').html('');
                crearListaAccesos(response);
            }else{
                $('[data-accesos="accesos"]').html('');
            }

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
        array_sub_modulo_accesos = [];

    $.each(response.sub_modulos, function (index, element) {

        if (array_modulo_accesos.indexOf(element.id_modulo)===-1) {
            html='';
            html+='<div class="col-md-12">'
                // html+='<label data-id-modulo="'+id_modulo+'">'+titulo+'</label>';
                html+='<label data-element-id-modulo="'+element.id_modulo+'">'+element.modulo+'</label>';
            html+='</div>';
            array_modulo_accesos.push(element.id_modulo);
            $('[data-accesos="accesos"]').append(html);

        }

        if (element.acceso!=null) {
            html='';
            html+='<div class="col-md-12">'
                html+='<label class="btn" data-action="modulo-seleccionado" data-titulo="'+element.modulo+'" data-id-modulo="'+element.id_modulo+'" data-id-acceso="'+element.id_acceso+'" data-acceso="'+element.acceso+'" data-disabled="true" >'+element.acceso+'</label>'
            html+='</div>';
            $('[data-accesos="accesos"] [data-element-id-modulo="'+element.id_modulo+'"]').append(html);
        }

        if (element.acceso===null && element.modulos_hijos.length>0 ) {
            $.each(element.modulos_hijos, function (index_hijos, element_hijos) {

                if ( array_sub_modulo_accesos.indexOf(element_hijos.id_modulo) ===-1 ) {
                    html='';
                    html+='<div class="col-md-12">';
                        html+='<label data-element-id-sub-modulo="'+element_hijos.id_modulo+'">'+element_hijos.modulo+'</label>';

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
                }else{
                    html='';
                    html+='<div class="col-md-12">'
                        html+='<label class="">Sin accesos</label>'
                    html+='</div>';
                    $('[data-accesos="accesos"] [data-element-id-modulo="'+element.id_modulo+'"] [data-element-id-sub-modulo="'+element_hijos.id_modulo+'"]').append(html);
                }


            });
        }
        if (element.acceso===null && element.modulos_hijos.length===0 ) {
            html='';
            html+='<div class="col-md-12">'
                html+='<label class="">Sin accesos</label>'
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
        $this_componente=$(this);

        // array_title.splice(index,1);
        if (array_disable_accesos.indexOf(id_acceso)===-1) {
            array_disable_accesos.push(id_acceso);
        }
        $(this).addClass('texto-seleccionado');
        asignarAccesoss(titulo, sub_titulo, id_modulo, id_sub_modulo, id_acceso, acceso, html, data_disable,$this_componente);
});

function asignarAccesoss(titulo, sub_titulo, id_modulo, id_sub_modulo, id_acceso, acceso, html, data_disable,$this_componente) {
    if (data_disable=='true') {
        $this_componente.attr('data-disabled','false');
        if (array_title.indexOf(id_modulo)===-1) {
            html+='<div class="col-md-12" data-count="col" data-key="'+id_modulo+'">'
                html+='<label data-id-modulo="'+id_modulo+'">'+titulo+'</label>';
            html+='</div>';
            array_title.push(id_modulo);
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
    console.log(id_modulo);
    console.log(id_sub_modulo);
    array_disable_accesos.splice(array_disable_accesos.indexOf(id_acceso),1);

    if ($('[data-count="col-hijo"][data-key="'+id_sub_modulo+'"] div').length===0) {
        $('[data-count="col-hijo"][data-key="'+id_sub_modulo+'"]').remove();
        index_hijo = array_sub_title.indexOf(id_sub_modulo);
        array_sub_title.splice(index_hijo,1);
    }
    if ($('[data-count="col"][data-key="'+id_modulo+'"] div').length===0) {
        index = array_title.indexOf(id_modulo);
        array_title.splice(index,1);
        $('[data-count="col"][data-key="'+id_modulo+'"]').remove();
    }
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
