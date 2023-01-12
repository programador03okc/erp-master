$(document).ready(function () {

});
var array_tipo=[];
$(document).on('click','[data-action="generar"]',function () {
    var tipo = $(this).attr('data-tipo');

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
function generarModelo(data) {
    var html = '',
        array_id_medelo,
        html_presupuesto='',
        key = Math.random();

    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-6').removeClass('animate__animated animate__fadeIn');

    if (data.id_tipo == '3') {
        $('[data-select="presupuesto-1"]').closest('div.col-md-6').addClass('d-none');
        $('[data-select="presupuesto-2"]').closest('div.col-md-6').addClass('d-none');
    }else{
        $('[data-select="presupuesto-3"]').closest('div.col-md-6').addClass('d-none');
    }

    $.each(data.presupuesto, function (index, element) {
        var array = element.partida.split('.'),
            descripcion ='',
            input_key=Math.random();

        if (array.length==3 && data.id_tipo!=='3') {
            descripcion='<input type="text" value="'+element.descripcion+'" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][descripcion]">';
        }else{
            descripcion= '<input type="hidden" value="'+element.descripcion+'" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][descripcion]"><span>'+element.descripcion+'</span>';
        }

        html+='<tr key="'+key+'" data-nivel="'+array.length+'" data-partida="'+element.partida+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" >'
            html+='<td><span>'+element.partida+'</span><input type="hidden" value="'+element.partida+'" name="'+data.tipo.toLowerCase()+'['+input_key+'][partida]"></td>'
            html+='<td>'+descripcion+'</td>'
            html+='<td>'
                html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+key+'" data-action="click-nuevo" data-nivel="'+array.length+'" data-name-table="'+data.tipo+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Agregar nuevo"><i class="fa fa-level-down-alt"></i></button>'
                html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+key+'" data-action="click-agregar" data-nivel="'+array.length+'" title="Agregar partida" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'"><i class="fa fa-plus"></i></button>'
                html+='<button type="button" class="btn btn-xs" data-partida="'+element.partida+'" key="'+key+'" data-action="click-eliminar" data-nivel="'+array.length+'" title="Eliminar" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'"><i class="fa fa-trash"></i></button>'
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
                        <th class="text-left" width="">DESCRIPCION</th>
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
$(document).on('click','[data-action="click-nuevo"]',function () {
    var key = $(this).attr('key'),
        html='',
        nivel = $(this).attr('data-nivel'),
        nivel_hijo = parseInt(nivel)+1,
        partida = $(this).attr('data-partida'),
        data_id = $(this).attr('data-id'),
        data_id_random = Math.random(),
        data_id_padre = $(this).attr('data-id-padre'),
        data_text_presupuesto = $(this).attr('data-tipo-text');

    console.log(data_text_presupuesto);
    var optener_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').attr('data-partida'),
        array_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').length>0? optener_partida_hijos.split('.'):['00'],
        next_partida = parseInt(array_partida_hijos[(array_partida_hijos.length-1)])+1,
        partida_nueva = partida+'.'+zfill(next_partida,2);

    html= `
        <tr key="`+Math.random()+`" data-nivel="`+nivel_hijo+`" data-partida="`+partida_nueva+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`">
            <td>
                <input
                    type="hidden"
                    class="form-control input-sm"
                    name="`+data_text_presupuesto+`[`+data_id_random+`][partida]"
                    value="`+partida_nueva+`"
                >
                <span>`+partida_nueva+`</span>
            </td>
            <td>
                <input type="text" value="nuevo" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][descripcion]">
            </td>
            <td>
                <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+Math.random()+`" data-action="click-nuevo" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`"><i class="fa fa-level-down-alt" title="Agregar nuevo"></i></button>
                <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+Math.random()+`" data-action="click-agregar" data-nivel="`+nivel_hijo+`" title="Agregar partida" data-id="`+data_id_random+`" data-id-padre="`+data_id+`"><i class="fa fa-plus"></i></button>
                <button type="button" class="btn btn-xs" data-partida="`+partida_nueva+`" key="`+Math.random()+`" data-action="click-eliminar" data-nivel="`+nivel_hijo+`" title="Eliminar" data-id="`+data_id_random+`" data-id-padre="`+data_id+`"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
    `;

    // console.log($('tr[data-id-padre="'+data_id+'"]:last'));

    if (data_id_padre!=='0') {

        //ir al ultimo hijo del cual seleccionamos
        var data_id_next = $('tr[data-id-padre="'+data_id+'"]:last').attr('data-id');
        console.log(array_partida_hijos);
        if ($('tr[data-id-padre="'+data_id_next+'"]').length===0) {

            if (data_id_next===undefined) {
                //agregamos cuando no tiene ni un hijo
                $('tr[data-id="'+data_id+'"]:last').after(html);
                console.log('entro');
            }else{
                //agregamos en el ultimo tr
                $('tr[data-id-padre="'+data_id+'"]:last').after(html);
            }
        }else{
            $('tr[data-id-padre="'+data_id_next+'"]:last').after(html);
        }
    }else{
        console.log(array_partida_hijos);
        $(this).closest('tr').closest('tbody').append(html);
    }
});
$(document).on('click','[data-action="click-eliminar"]',function () {
    var key = $(this).attr('key');
    $(this).closest('tr').remove();
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
                    }
                })
            }
        }
    });
});
