$(document).ready(function () {

});
var array_tipo=[];
$(document).on('click','[data-action="generar"]',function () {
    var tipo = $(this).attr('data-tipo');
    $('[name="id_tipo_presupuesto"]').val(tipo);
    if (tipo !== '0') {
        getModelo(tipo);
    }

});
function getModelo(tipo) {

    $.ajax({
        type: 'GET',
        url: 'presupuesto-interno-detalle',
        data: {tipo:tipo},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            // console.log(data);
        }
    }).done(function(response) {
        // console.log(response);
        generarModelo(response);
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// genera la tabla de presupuesto
function generarModelo(data) {
    var html = '',
        array_id_medelo,
        html_presupuesto='',
        key = Math.random();

    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-6').removeClass('animate__animated animate__fadeIn');

    if (data.id_tipo == '3') {
        $('[data-select="presupuesto-1"]').closest('div.col-md-6').addClass('d-none');
        $('[data-select="presupuesto-2"]').closest('div.col-md-6').addClass('d-none');

        $('[data-select="presupuesto-1"]').find('div').remove();
        $('[data-select="presupuesto-2"]').find('div').remove();
    }else{
        $('[data-select="presupuesto-3"]').closest('div.col-md-6').addClass('d-none');
        $('[data-select="presupuesto-3"]').find('div').remove();
    }

    $.each(data.presupuesto, function (index, element) {
        var array = element.partida.split('.'),
            descripcion ='',
            id=Math.random(),
            id_padre=Math.random(),
            input_key=Math.random();



        html+='<tr key="'+input_key+'" data-nivel="'+array.length+'" data-partida="'+element.partida+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" >'
            html+='<td data-td="partida">'
                html+='<input type="hidden" value="'+element.partida+'" name="'+data.tipo.toLowerCase()+'['+input_key+'][partida]" class="form-control input-sm">'

                html+='<input type="hidden" value="'+element.id_modelo_presupuesto_interno+'" name="'+data.tipo.toLowerCase()+'['+input_key+'][id_hijo]" class="form-control input-sm">'
                html+='<input type="hidden" value="'+element.id_padre+'" name="'+data.tipo.toLowerCase()+'['+input_key+'][id_padre]" class="form-control input-sm">'
                html+='<span>'+element.partida+'</span></td>'

            // if ((array.length==3) || (array.length==4)) {
                html+='<td data-td="descripcion"><input type="hidden" value="'+element.descripcion+'" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][descripcion]" placeholder="'+element.descripcion+'"><span>'+element.descripcion+'</span></td>'

                html+='<td data-td="monto"><input type="hidden" value="0" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][monto]" placeholder="Ingrese monto" step="0.01"><span>'+0+'</span></td>'
            // }else{
            //     html+='<td colspan="2" data-td="descripcion"><input type="hidden" value="'+element.descripcion+'" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][descripcion]"><span>'+element.descripcion+'</span></td>'
            // }
            html+='<td data-td="accion">'
                if (array.length==3) {

                }
                if (array.length!=4) {
                    html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-nuevo" data-select="titulo" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Agregar titulo" data-tipo="nuevo"><i class="fa fa-level-down-alt"></i></button>'

                    html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-partida" data-select="partida" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Agregar partida" data-tipo="nuevo"><i class="fa fa-plus"></i></button>'
                    html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-nuevo" data-select="titulo" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Editar" data-tipo="editar"><i class="fa fa-edit"></i></button>'
                }

                if (array.length==4) {
                    html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-partida" data-select="partida" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Editar partida" data-tipo="editar"><i class="fa fa-edit"></i></button>'
                }
                if (array.length!==1) {
                    html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-eliminar" data-nivel="'+array.length+'" title="Eliminar" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'"><i class="fa fa-trash"></i></button>'
                }


            html+='</td>'
        html+='</tr>';
    });

    html_presupuesto=`
        <div class="col-md-12">
            <label>`+data.tipo+`</label>
            <div class="pull-right">
                <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_`+data.tipo+`">
                <i class="fa fa-minus"></i></a>

                <button type="button" class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="`+data.tipo_next+`" data-action="generar"></i></button>

            </div>
        </div>
        <div class="col-md-12 panel-collapse collapse in" id="collapse_`+data.tipo+`">
            <table class="table table-hover" id="partida-`+data.tipo+`">
                <thead>
                    <tr>
                        <th class="text-left" width="20%">PARTIDA</th>
                        <th class="text-left" width=""colspan="2">DESCRIPCION</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody data-table-presupuesto="ingreso">`+html+`</tbody>
            </table>
        </div>
    `
    $('[data-select="presupuesto-'+data.id_tipo+'"]').html(html_presupuesto);
    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-6').removeClass('d-none');

    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-6').addClass('animate__animated animate__fadeIn');

    if (data.id_tipo == '1') {
        getModelo(2);
    }

}
// abre el modal para agregar un nuevo titulo o editarlo
$(document).on('click','[data-action="click-nuevo"]',function () {
    var key = $(this).attr('key'),
        html='',
        nivel = $(this).attr('data-nivel'),
        nivel_hijo = parseInt(nivel)+1,//ver como se suma
        partida = $(this).attr('data-partida'),
        data_id = $(this).attr('data-id'),
        data_id_random = Math.random(),
        data_id_padre = $(this).attr('data-id-padre'),
        data_text_presupuesto = $(this).attr('data-tipo-text'),
        data_tipo = $(this).attr('data-tipo');

    $('#modal-titulo [data-form="guardar-formulario"]').find('div.modal-footer').find('button[type="submit"]').attr('key',key);

    $('#modal-titulo [data-form="guardar-formulario"]').find('div.modal-footer').find('button[type="submit"]').attr('data-nivel',nivel);

    $('#modal-titulo [data-form="guardar-formulario"]').find('div.modal-footer').find('button[type="submit"]').attr('data-partida',partida);

    $('#modal-titulo [data-form="guardar-formulario"]').find('div.modal-footer').find('button[type="submit"]').attr('data-id',data_id);

    $('#modal-titulo [data-form="guardar-formulario"]').find('div.modal-footer').find('button[type="submit"]').attr('data-id-padre',data_id_padre);

    $('#modal-titulo [data-form="guardar-formulario"]').find('div.modal-footer').find('button[type="submit"]').attr('data-tipo-text',data_text_presupuesto);

    $('#modal-titulo [data-form="guardar-formulario"]').find('div.modal-footer').find('button[type="submit"]').attr('data-tipo',data_tipo);

    $('#modal-titulo [data-form="guardar-formulario"]')[0].reset();

    if (data_tipo==='editar') {
        descripcion_editar = $(this).closest('tr[key="'+key+'"]').find('td[data-td="descripcion"] [name="'+data_text_presupuesto+'['+key+'][descripcion]"]').val();
        $('#modal-titulo [data-form="guardar-formulario"] [name="descripcion"]').val(descripcion_editar);
    }
    $('#modal-titulo').modal('show');

});
//guardar la descripcion del modal y agrega un titulo nuevo o edita el titulo
$(document).on('submit','[data-form="guardar-formulario"]',function (e) {
    e.preventDefault();
    var key = $(this).find('div.modal-footer').find('button[type="submit"]').attr('key'),
        html='',
        nivel = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-nivel'),
        nivel_hijo = parseInt(nivel)+1,//ver como se suma
        partida = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-partida'),
        data_id = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-id'),
        data_id_random = Math.random(),
        data_id_padre = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-id-padre'),
        data_text_presupuesto = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-tipo-text'),
        data_tipo = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-tipo'),
        descripcion_titulo = $(this).find('[name="descripcion"]').val();


    if (data_tipo==='nuevo') {
        var optener_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').attr('data-partida'),
            array_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').length>0? optener_partida_hijos.split('.'):['00'],
            next_partida = parseInt(array_partida_hijos[(array_partida_hijos.length-1)])+1,
            partida_nueva = partida+'.'+zfill(next_partida,2);

        html= `
            <tr key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-partida="`+partida_nueva+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`">
                <td data-td="partida">
                    <input
                        type="hidden"
                        class="form-control input-sm"
                        name="`+data_text_presupuesto+`[`+data_id_random+`][partida]"
                        value="`+partida_nueva+`"
                    >
                    <input type="hidden" value="`+data_id_random+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_hijo]" placeholder="Nuevo Titulo">
                    <input type="hidden" value="`+data_id+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_padre]" placeholder="Nuevo Titulo">

                    <span>`+partida_nueva+`</span>
                </td>
                <td data-td="descripcion">
                    <input type="hidden" value="`+descripcion_titulo+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][descripcion]" placeholder="Nuevo Titulo">
                    <span>`+descripcion_titulo+`</span>
                </td>

                <td data-td="monto" style="padding-right: 0px;">
                    <input type="hidden" value="0" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][monto]" placeholder="Ingrese monto" step="0.01">
                    <span>0</span>
                </td>

                <td data-td="accion">
                    <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-nuevo" data-select="titulo" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Agregar titulo" data-tipo="nuevo"><i class="fa fa-level-down-alt"></i></button>

                    <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-partida" data-select="partida" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Agregar partida" data-tipo="nuevo"><i class="fa fa-plus"></i></button>

                    <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-nuevo" data-select="titulo" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Editar" data-tipo="editar"><i class="fa fa-edit"></i></button>

                    <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-eliminar" data-nivel="`+nivel_hijo+`" title="Eliminar" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;


        if (data_id_padre!=='0') {

            //ir al ultimo hijo del cual seleccionamos
            var data_id_next = $('tr[data-id-padre="'+data_id+'"]:last').attr('data-id');

            if ($('tr[data-id-padre="'+data_id_next+'"]').length===0) {

                if (data_id_next===undefined) {
                    //agregamos cuando no tiene ni un hijo
                    $('tr[data-id="'+data_id+'"]:last').after(html);

                }else{
                    //agregamos en el ultimo tr
                    $('tr[data-id-padre="'+data_id+'"]:last').after(html);
                }
            }else{
                $('tr[data-id-padre="'+data_id_next+'"]:last').after(html);
            }
        }else{

            $('tr[key="'+key+'"]').closest('tbody').append(html);
        }
    }else{
        $('tr[key="'+key+'"] td[data-td="descripcion"] [name="'+data_text_presupuesto+'['+key+'][descripcion]"]').val(descripcion_titulo);
        $('tr[key="'+key+'"] td[data-td="descripcion"] span').text(descripcion_titulo);
    }

    $('#modal-titulo').modal('hide');
});
$(document).on('click','[data-action="click-eliminar"]',function () {
    var key = $(this).attr('key'),
        data_id = $(this).attr('data-id'),
        data_id_padre = $(this).attr('data-id-padre'),
        data_text_presupuesto = $(this).attr('data-tipo-text');
    $(this).closest('tr').remove();
    $('tr[data-id-padre="'+data_id+'"]').remove();

    sumarPartidas(data_id,data_id_padre,data_text_presupuesto);
});
function zfill(number, width) {
    var numberOutput = Math.abs(number); /* Valor absoluto del número */
    var length = number.toString().length; /* Largo del número */
    var zero = "0"; /* String de cero */

    if (width <= length) {
        if (number < 0) {
             return ("-" + numberOutput.toString());
        } else {
             return numberOutput.toString();
        }
    } else {
        if (number < 0) {
            return ("-" + (zero.repeat(width - length)) + numberOutput.toString());
        } else {
            return ((zero.repeat(width - length)) + numberOutput.toString());
        }
    }
}
// guarda toda la vista
$(document).on('submit','[data-form="guardar-partida"]',function (e) {
    e.preventDefault();
    var data = new FormData($(this)[0]);
    Swal.fire({
        title: 'Guardar',
        text: "¿Está seguro de guardar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'no',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: data,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    // console.log(data);
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        },
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.status===200) {
                console.log(result.value);
                Swal.fire({
                    title: 'Éxito',
                    text: "Se guardo con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        // $('#nuevo-cliente').modal('hide');
                        window.location.href = "lista";
                    }
                })
            }else{
                Swal.fire(
                    result.value.title,
                    result.value.msg,
                    result.value.type
                )
            }
        }
    });
});
// abre el modal para generar la partida
$(document).on('click','[data-action="click-partida"]',function () {
    var key = $(this).attr('key'),
        html='',
        nivel = $(this).attr('data-nivel'),
        nivel_hijo = parseInt(nivel)+1,
        partida = $(this).attr('data-partida'),
        data_id = $(this).attr('data-id'),
        data_id_random = Math.random(),
        data_id_padre = $(this).attr('data-id-padre'),
        data_text_presupuesto = $(this).attr('data-tipo-text'),
        data_tipo = $(this).attr('data-tipo');


    $('#modal-partida [data-form="guardar-partida-modal"]').find('div.modal-footer').find('button[type="submit"]').attr('key',key);
    $('#modal-partida [data-form="guardar-partida-modal"]').find('div.modal-footer').find('button[type="submit"]').attr('data-nivel',nivel);
    $('#modal-partida [data-form="guardar-partida-modal"]').find('div.modal-footer').find('button[type="submit"]').attr('data-partida',partida);
    $('#modal-partida [data-form="guardar-partida-modal"]').find('div.modal-footer').find('button[type="submit"]').attr('data-id',data_id);
    $('#modal-partida [data-form="guardar-partida-modal"]').find('div.modal-footer').find('button[type="submit"]').attr('data-id-padre',data_id_padre);
    $('#modal-partida [data-form="guardar-partida-modal"]').find('div.modal-footer').find('button[type="submit"]').attr('data-tipo-text',data_text_presupuesto);

    $('#modal-partida [data-form="guardar-partida-modal"]').find('div.modal-footer').find('button[type="submit"]').attr('data-tipo',data_tipo);

    $('#modal-partida [data-form="guardar-partida-modal"]')[0].reset();
    if (data_tipo==='editar') {
        descripcion_editar = $(this).closest('tr[key="'+key+'"]').find('td[data-td="descripcion"] [name="'+data_text_presupuesto+'['+key+'][descripcion]"]').val();
        monto_editar = $(this).closest('tr[key="'+key+'"]').find('td[data-td="monto"] [name="'+data_text_presupuesto+'['+key+'][monto]"]').val();
        $('#modal-partida [data-form="guardar-partida-modal"] [name="descripcion"]').val(descripcion_editar);
        $('#modal-partida [data-form="guardar-partida-modal"] [name="monto"]').val(monto_editar);
    }
    $('#modal-partida').modal('show');


});
// guarda el modal de la partida o edita en su defecto
$(document).on('submit','[data-form="guardar-partida-modal"]',function (e) {
    e.preventDefault();
    var key = $(this).find('div.modal-footer').find('button[type="submit"]').attr('key'),
        html='',
        nivel = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-nivel'),
        nivel_hijo = parseInt(nivel)+1,
        partida = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-partida'),
        data_id = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-id'),
        data_id_random = Math.random(),
        data_id_padre = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-id-padre'),
        data_text_presupuesto = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-tipo-text'),
        data_tipo = $(this).find('div.modal-footer').find('button[type="submit"]').attr('data-tipo'),
        descripcion_partida = $(this).find('[name="descripcion"]').val(),
        monto_partida = $(this).find('[name="monto"]').val();

    if (data_tipo==='nuevo') {
        var optener_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').attr('data-partida'),
            array_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').length>0? optener_partida_hijos.split('.'):['00'],
            next_partida = parseInt(array_partida_hijos[(array_partida_hijos.length-1)])+1,
            partida_nueva = partida+'.'+zfill(next_partida,2);

        html= `
            <tr key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-partida="`+partida_nueva+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`">
                <td data-td="partida">
                    <input
                        type="hidden"
                        class="form-control input-sm"
                        name="`+data_text_presupuesto+`[`+data_id_random+`][partida]"
                        value="`+partida_nueva+`"
                    >
                    <input type="hidden" value="`+data_id_random+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_hijo]" placeholder="Nueva partida">
                    <input type="hidden" value="`+data_id+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_padre]" placeholder="Nueva partida">
                    <span>`+partida_nueva+`</span>
                </td>
                <td data-td="descripcion" style="padding-right: 0px;" >
                    <input type="hidden" value="`+descripcion_partida+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][descripcion]" placeholder="Nueva partida">
                    <span>`+descripcion_partida+`</span>
                </td>
                <td data-td="monto" style="padding-right: 0px;">
                    <input type="hidden" value="`+monto_partida+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][monto]" placeholder="Ingrese monto" step="0.01">
                    <span>`+monto_partida+`</span>
                </td>
                <td data-td="accion">

                    <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-partida" data-select="partida" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Editar partida" data-tipo="editar"><i class="fa fa-edit"></i></button>

                    <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-eliminar" data-nivel="`+nivel_hijo+`" title="Eliminar" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;


        if (data_id_padre!=='0') {

            //ir al ultimo hijo del cual seleccionamos
            var data_id_next = $('tr[data-id-padre="'+data_id+'"]:last').attr('data-id');

            if ($('tr[data-id-padre="'+data_id_next+'"]').length===0) {

                if (data_id_next===undefined) {
                    //agregamos cuando no tiene ni un hijo
                    $('tr[data-id="'+data_id+'"]:last').after(html);

                }else{
                    //agregamos en el ultimo tr
                    $('tr[data-id-padre="'+data_id+'"]:last').after(html);
                }
            }else{
                $('tr[data-id-padre="'+data_id_next+'"]:last').after(html);
            }
        }else{
            $('tr[key="'+key+'"]').closest('tbody').append(html);
            // $(this).closest('tr').closest('tbody').append(html);
        }
        // data_id;
        // data_id_padre;
        sumarPartidas(data_id_random,data_id,data_text_presupuesto);
    }else{
        $('tr[key="'+key+'"] td[data-td="descripcion"] [name="'+data_text_presupuesto+'['+key+'][descripcion]"]').val(descripcion_partida);
        $('tr[key="'+key+'"] td[data-td="descripcion"] span').text(descripcion_partida);

        $('tr[key="'+key+'"] td[data-td="monto"] [name="'+data_text_presupuesto+'['+key+'][monto]"]').val(monto_partida);
        $('tr[key="'+key+'"] td[data-td="monto"] span').text(monto_partida);
        sumarPartidas(data_id,data_id_padre,data_text_presupuesto);
    }

    $('#modal-partida').modal('hide');


});
$(document).on('submit','[data-form="editar-partida"]',function (e) {
    e.preventDefault();
    var data = new FormData($(this)[0]);
    Swal.fire({
        title: 'Guardar',
        text: "¿Está seguro de guardar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'no',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: data,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    // console.log(data);
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        },
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.status===200) {
                console.log(result.value);
                Swal.fire({
                    title: 'Éxito',
                    text: "Se guardo con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        // $('#nuevo-cliente').modal('hide');
                        window.location.href = "lista";
                    }
                })
            }
        }
    });
});
function sumarPartidas(data_id,data_id_padre,data_text_presupuesto) {
    var suma_partida = 0;

    $.each($('tr[data-id-padre="'+data_id_padre+'"]'), function (indexInArray, valueOfElement) {
        suma_partida = suma_partida + parseFloat(valueOfElement.children[2].children[0].value);
    });

    data_td_key = $('tr[data-id="'+data_id_padre+'"]').attr('key');

    $('tr[data-id="'+data_id_padre+'"] td[data-td="monto"] [name="'+data_text_presupuesto+'['+data_td_key+'][monto]"]').val(suma_partida);
    $('tr[data-id="'+data_id_padre+'"] td[data-td="monto"] span').text(suma_partida);


    data_id = $('tr[data-id="'+data_id_padre+'"]').attr('data-id')
    data_id_padre = $('tr[data-id="'+data_id_padre+'"]').attr('data-id-padre')

    while (data_id_padre!=='0') {

        $.each($('tr[data-id-padre="'+data_id_padre+'"]'), function (indexInArray, valueOfElement) {
            suma_partida = suma_partida + parseFloat(valueOfElement.children[2].children[0].value);
        });

        data_td_key = $('tr[data-id="'+data_id_padre+'"]').attr('key');

        $('tr[data-id="'+data_id_padre+'"] td[data-td="monto"] [name="'+data_text_presupuesto+'['+data_td_key+'][monto]"]').val(suma_partida);
        $('tr[data-id="'+data_id_padre+'"] td[data-td="monto"] span').text(suma_partida);


        data_id = $('tr[data-id="'+data_id_padre+'"]').attr('data-id')
        data_id_padre = $('tr[data-id="'+data_id_padre+'"]').attr('data-id-padre')
    }
    // if (data_id_padre!=='0') {

    //     sumarPartidas(data_id,data_id_padre,data_text_presupuesto);
    // }
}
