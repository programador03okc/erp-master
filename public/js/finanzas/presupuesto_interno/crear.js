var meses_anual = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'],
    $porcentajes=(array.length>0?array:[]);
$(document).ready(function () {
    vista_extendida();
    $('[data-form="guardar-partida"]').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    $('[data-form="editar-partida"]').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
});
var array_tipo=[];
$(document).on('click','[data-action="generar"]',function () {
    var tipo = $(this).attr('data-tipo');
    // $('[name="id_tipo_presupuesto"]').val(tipo);
    $porcentajes=[]
    if (tipo === '1') {
        $('[name="tipo_ingresos"]').val(tipo);
    }
    if (tipo === '3') {
        $('[name="tipo_gastos"]').val(tipo);
    }
    $(this).closest('.box-tools.pull-right').find('button[type="submit"]').removeAttr('disabled');
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

    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-12').removeClass('animate__animated animate__fadeIn');

    $.each(data.presupuesto, function (index, element) {
        var array = element.partida.split('.'),
            descripcion ='',
            id=Math.random(),
            id_padre=Math.random(),
            input_key=Math.random(),
            // array_excluidos = ['03.01.02.01','03.01.02.02','03.01.02.03','03.01.03.01','03.01.03.02','03.01.03.03'],
            array_excluidos = [],
            partida_hidden = array_excluidos.includes(element.partida);

        // console.log(partida_hidden);

        html+='<tr key="'+input_key+'" data-nivel="'+array.length+'" data-partida="'+element.partida+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" '+(array.length==2?'class="text-primary"':'')+' '+(array.length==4?'class="bg-danger"':'')+'>'
            html+='<td data-td="partida" >'
                html+='<input type="hidden" value="'+element.partida+'" name="'+data.tipo.toLowerCase()+'['+input_key+'][partida]" class="form-control input-sm">'
                // text-primary
                html+='<input type="hidden" value="'+element.id_modelo_presupuesto_interno+'" name="'+data.tipo.toLowerCase()+'['+input_key+'][id_hijo]" class="form-control input-sm">'
                html+='<input type="hidden" value="'+element.id_padre+'" name="'+data.tipo.toLowerCase()+'['+input_key+'][id_padre]" class="form-control input-sm">'

                html+='<input type="hidden" value="0" name="'+data.tipo.toLowerCase()+'['+input_key+'][porcentaje_gobierno]" class="form-control input-sm">'
                html+='<input type="hidden" value="0" name="'+data.tipo.toLowerCase()+'['+input_key+'][porcentaje_privado]" class="form-control input-sm">'
                html+='<input type="hidden" value="0" name="'+data.tipo.toLowerCase()+'['+input_key+'][porcentaje_comicion]" class="form-control input-sm">'
                html+='<input type="hidden" value="0" name="'+data.tipo.toLowerCase()+'['+input_key+'][porcentaje_penalidad]" class="form-control input-sm">'

                html+='<span>'+element.partida+'</span></td>'

            // if ((array.length==3) || (array.length==4)) {
                html+='<td data-td="descripcion"><input type="hidden" value="'+element.descripcion+'" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][descripcion]" placeholder="'+element.descripcion+'"><span>'+element.descripcion+'</span></td>'

                html+='<td data-td="porcentaje" '+(data.id_tipo==='2'?'':'hidden')+'>'+(array.length===4?'<span>0</span>%':'')+'<input type="hidden" value="0" name="'+data.tipo.toLowerCase()+'['+input_key+'][porcentaje_costo]" class="form-control input-sm"></td>'

            // inputs del mes
                html+='<td data-td="enero">'+
                    '<input '+
                        'type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" '+
                        'value="0.00" '+
                        'class="form-control input-sm" '+
                        'name="'+data.tipo.toLowerCase()+'['+input_key+'][enero]" '+
                        'placeholder="Ingrese monto" '+
                        'key="'+input_key+'"  '+
                        'data-nivel="'+array.length+'" '+
                        'data-id="'+element.id_modelo_presupuesto_interno+'" '+
                        'data-id-padre="'+element.id_padre+'" '+
                        'data-tipo-text="'+data.tipo.toLowerCase()+'" '+
                        'data-mes="enero" '+(array.length===4?'data-input="partida" title="ENERO"':'')+''+
                    '>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+''+
                '</td>'

                html+='<td data-td="febrero"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][febrero]" placeholder="Ingrese monto"  key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="febrero" '+(array.length===4?'data-input="partida" title="FEBRERO"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="marzo"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][marzo]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="marzo" '+(array.length===4?'data-input="partida" title="MARZO"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="abril"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][abril]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="abril" '+(array.length===4?'data-input="partida" title="ABRIL"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="mayo"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][mayo]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="mayo" '+(array.length===4?'data-input="partida" title="MAYO"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="junio"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][junio]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="junio" '+(array.length===4?'data-input="partida" title="JUNIO"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="julio"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][julio]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="julio" '+(array.length===4?'data-input="partida" title="JULIO"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="agosto"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][agosto]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="agosto" '+(array.length===4?'data-input="partida" title="AGOSTO"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="setiembre"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][setiembre]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="setiembre" '+(array.length===4?'data-input="partida" title="SETIEMBRE"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="octubre"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][octubre]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="octubre" '+(array.length===4?'data-input="partida" title="OCTUBRE"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="noviembre"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][noviembre]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="noviembre" '+(array.length===4?'data-input="partida" title="NOVIEMBRE"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'

                html+='<td data-td="diciembre"><input type="'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'text':'hidden')+'" value="0.00" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][diciembre]" placeholder="Ingrese monto" key="'+input_key+'"  data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" data-mes="diciembre" '+(array.length===4?'data-input="partida" title="DICIEMBRE"':'')+'>'+(array.length===4 && data.id_tipo!=='2' && partida_hidden===false?'':'<span>'+0+'.00</span>')+'</td>'
            // }else{
            //     html+='<td colspan="2" data-td="descripcion"><input type="hidden" value="'+element.descripcion+'" class="form-control input-sm" name="'+data.tipo.toLowerCase()+'['+input_key+'][descripcion]"><span>'+element.descripcion+'</span></td>'
            // }
            // if (data.id_tipo!=='2') {
            html+='<td data-td="accion" '+(data.id_tipo==='1' && array.length==4 ?'':'hidden')+'>'
                html+='<div class="btn-group">'
                    html+='<div class="btn-group">'
                        html+='<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                        html+='<span class="caret"></span>'
                        html+='</button>'
                        html+='<ul class="dropdown-menu dropdown-menu-right">'
                        if (array.length!=4) {
                            html+='<input type="hidden" name="'+data.tipo.toLowerCase()+'['+input_key+'][registro]" value="1">'
                            // html+='<li><a href="#" class="" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-nuevo" data-select="titulo" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Agregar titulo" data-tipo="nuevo">Agregar titulo</a></li>'

                            // html+='<li><a href="#" class="" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-partida" data-select="partida" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Agregar partida" data-tipo="nuevo">Agregar partida</a></li>'

                            // html+='<li><a href="#" class="" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-nuevo" data-select="titulo" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Editar" data-tipo="editar">Editar</a></li>'
                        }

                        if (array.length==4) {
                            html+='<input type="hidden" name="'+data.tipo.toLowerCase()+'['+input_key+'][registro]" value="2">'
                            // html+='<li><a href="#" class="" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-partida" data-select="partida" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Editar partida" data-tipo="editar">Editar partida</a></li>'

                            html+='<li><a href="#" class="" key="'+input_key+'" data-action="click-porcentaje" data-nivel="'+array.length+'" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'" title="Editar porcentaje" data-tipo="editar" data-text-partida="'+element.partida+'" >Editar porcentaje</a></li>'
                        }
                        if (array.length!==1) {
                            // html+='<li><a href="#" class="" data-partida="'+element.partida+'" key="'+input_key+'" data-action="click-eliminar" data-nivel="'+array.length+'" title="Eliminar" data-id="'+element.id_modelo_presupuesto_interno+'" data-id-padre="'+element.id_padre+'" data-tipo-text="'+data.tipo.toLowerCase()+'">Eliminar</a></li>'
                        }
                        html+='</ul>'
                    html+='</div>'
                html+='</div>'
            html+='</td>'
            // }
        html+='</tr>';
    });

    html_presupuesto=`
        <div class="col-md-12">
            <label>`+data.tipo+`</label>
            <div class="pull-right">
                <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_`+data.tipo+`">
                <i class="fa fa-minus"></i></a>

                <button type="button" class="btn btn-box-tool"  title="" data-tipo="`+data.id_tipo+`" data-action="remove">
                <i class="fa fa-times"></i></button>

                <button type="button" class="btn btn-primary btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="`+data.tipo_next+`" data-action="generar"></i></button>

            </div>
        </div>
        <div class="col-md-12 panel-collapse collapse in" id="collapse_`+data.tipo+`">
            <table class="table small" id="partida-`+data.tipo+`">
                <thead>
                    <tr>
                        <th class="text-left" width="30">PARTIDA</th>
                        <th class="text-left" width="">DESCRIPCION</th>
                        <th class="text-left" width="" `+(data.id_tipo==='2' ?`` :`hidden`)+`>%</th>

                        <th class="text-left" width=""colspan="">ENE </th>
                        <th class="text-left" width=""colspan="">FEB</th>
                        <th class="text-left" width=""colspan="">MAR</th>
                        <th class="text-left" width=""colspan="">ABR</th>
                        <th class="text-left" width=""colspan="">MAY</th>
                        <th class="text-left" width=""colspan="">JUN</th>
                        <th class="text-left" width=""colspan="">JUL</th>
                        <th class="text-left" width=""colspan="">AGO</th>
                        <th class="text-left" width=""colspan="">SET</th>
                        <th class="text-left" width=""colspan="">OCT</th>
                        <th class="text-left" width=""colspan="">NOV</th>
                        <th class="text-left" width=""colspan="">DIC</th>
                        <th class="text-center" width="10" `+(data.id_tipo==='2' ?`` :`hidden`)+`></th>

                    </tr>
                </thead>
                <tbody data-table-presupuesto="ingreso">`+html+`</tbody>
            </table>
        </div>
    `
    $('[data-select="presupuesto-'+data.id_tipo+'"]').html(html_presupuesto);
    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-12').removeClass('d-none');

    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-12').addClass('animate__animated animate__fadeIn');

    if (data.id_tipo == '1') {
        getModelo(2);
    }

}
// abre el modal para agregar un nuevo titulo o editarlo
$(document).on('click','[data-action="click-nuevo"]',function (e) {
    e.preventDefault();
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
            <tr key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-partida="`+partida_nueva+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" `+(nivel_hijo===2?'class="text-primary"':'')+`>
                <td data-td="partida">
                    <input
                        type="hidden"
                        class="form-control input-sm"
                        name="`+data_text_presupuesto+`[`+data_id_random+`][partida]"
                        value="`+partida_nueva+`"
                    >
                    <input type="hidden" value="`+data_id_random+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_hijo]" placeholder="Nuevo Titulo">
                    <input type="hidden" value="`+data_id+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_padre]" placeholder="Nuevo Titulo">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_gobierno]" class="form-control input-sm">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_privado]" class="form-control input-sm">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_comicion]" class="form-control input-sm">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_penalidad]" class="form-control input-sm">

                    <span>`+partida_nueva+`</span>
                </td>
                <td data-td="descripcion">
                    <input type="hidden" value="`+descripcion_titulo+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][descripcion]" placeholder="Nuevo Titulo">
                    <span>`+descripcion_titulo+`</span>
                </td>

                <td data-td="enero" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][enero]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="enero">
                    <span>0.00</span>
                </td>
                <td data-td="febrero" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][febrero]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="febrero">
                    <span>0.00</span>
                </td>
                <td data-td="marzo" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][marzo]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="marzo">
                    <span>0.00</span>
                </td>
                <td data-td="abril" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][abril]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="abril">
                    <span>0.00</span>
                </td>
                <td data-td="mayo" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][mayo]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="mayo">
                    <span>0.00</span>
                </td>
                <td data-td="junio" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][junio]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="junio">
                    <span>0.00</span>
                </td>
                <td data-td="julio" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][julio]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="julio">
                    <span>0.00</span>
                </td>
                <td data-td="agosto" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][agosto]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="agosto">
                    <span>0.00</span>
                </td>
                <td data-td="setiembre" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][setiembre]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="setiembre">
                    <span>0.00</span>
                </td>
                <td data-td="octubre" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][octubre]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="octubre">
                    <span>0.00</span>
                </td>
                <td data-td="noviembre" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][noviembre]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="noviembre">
                    <span>0.00</span>
                </td>
                <td data-td="diciembre" style="padding-right: 0px;">
                    <input type="hidden" value="0.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][diciembre]" placeholder="Ingrese monto" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="diciembre">
                    <span>0.00</span>
                </td>


                <td data-td="accion">
                    <div class="btn-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <input type="hidden" name="`+data_text_presupuesto+`[`+data_id_random+`][registro]" value="1">
                                <li><a href="#" class="" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-nuevo" data-select="titulo" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Agregar titulo" data-tipo="nuevo">Agregar titulo</a></li>

                                <li><a href="#" class="" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-partida" data-select="partida" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Agregar partida" data-tipo="nuevo">Agregar partida</a></li>

                                <li><a href="#" class="" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-nuevo" data-select="titulo" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Editar" data-tipo="editar">Editar</a></li>

                                <li><a href="#" class="" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-eliminar" data-nivel="`+nivel_hijo+`" title="Eliminar" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`">Eliminar</a></li>

                            </ul>
                        </div>
                    </div>
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
$(document).on('click','[data-action="click-eliminar"]',function (e) {
    e.preventDefault();
    var key = $(this).attr('key'),
        data_id = $(this).attr('data-id'),
        data_id_padre = $(this).attr('data-id-padre'),
        data_text_presupuesto = $(this).attr('data-tipo-text'),
        numero_mes=0;
    $(this).closest('tr').remove();
    $('tr[data-id-padre="'+data_id+'"]').remove();

    // console.log(meses_anual);
    for (let index = 0; index < meses_anual.length; index++) {
        mes = meses_anual[index];
        numero_mes = numeroMes(mes);
        sumarPartidas(
            data_id,
            data_id_padre,
            data_text_presupuesto,
            mes,
            numero_mes
        );
    }

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
$(document).on('click','[data-action="click-partida"]',function (e) {
    e.preventDefault();
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
        // monto_editar = $(this).closest('tr[key="'+key+'"]').find('td[data-td="monto"] [name="'+data_text_presupuesto+'['+key+'][monto]"]').val();
        // monto_editar = parseFloat(monto_editar).toFixed(2);
        // var array_aplit = monto_editar.split(',');
        // monto_editar='';

        // for (let index = 0; index < array_aplit.length; index++) {
        //     monto_editar = monto_editar + array_aplit[index];
        // }
        $('#modal-partida [data-form="guardar-partida-modal"] [name="descripcion"]').val(descripcion_editar);
        // $('#modal-partida [data-form="guardar-partida-modal"] [name="monto"]').val(monto_editar);
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
        descripcion_partida = $(this).find('[name="descripcion"]').val();

        // monto_partida = $(this).find('[name="monto"]').val();
        // monto_partida = parseFloat(monto_partida).toFixed(2);
        // monto_partida = separator(monto_partida);

    if (data_tipo==='nuevo') {
        var optener_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').attr('data-partida'),
            array_partida_hijos = $('tr[data-id-padre="'+data_id+'"]:last').length>0? optener_partida_hijos.split('.'):['00'],
            next_partida = parseInt(array_partida_hijos[(array_partida_hijos.length-1)])+1,
            partida_nueva = partida+'.'+zfill(next_partida,2);

        html= `
            <tr key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-partida="`+partida_nueva+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" class="bg-danger">
                <td data-td="partida">
                    <input
                        type="hidden"
                        class="form-control input-sm"
                        name="`+data_text_presupuesto+`[`+data_id_random+`][partida]"
                        value="`+partida_nueva+`"
                    >
                    <input type="hidden" value="`+data_id_random+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_hijo]" placeholder="Nueva partida">
                    <input type="hidden" value="`+data_id+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][id_padre]" placeholder="Nueva partida">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_gobierno]" class="form-control input-sm">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_privado]" class="form-control input-sm">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_comicion]" class="form-control input-sm">

                    <input type="hidden" value="0" name="`+data_text_presupuesto+`[`+data_id_random+`][porcentaje_penalidad]" class="form-control input-sm">


                    <span>`+partida_nueva+`</span>
                </td>
                <td data-td="descripcion" style="padding-right: 0px;" >
                    <input type="hidden" value="`+descripcion_partida+`" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][descripcion]" placeholder="Nueva partida">
                    <span>`+descripcion_partida+`</span>
                </td>


                <td data-td="enero" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][enero]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="enero" data-input="partida" title="ENERO">

                </td>
                <td data-td="febrero" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][febrero]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="febrero" data-input="partida" title="FEBRERO">

                </td>
                <td data-td="marzo" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][marzo]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="marzo" data-input="partida" title="MARZO">

                </td>
                <td data-td="abril" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][abril]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="abril" data-input="partida" title="ABRIL">

                </td>
                <td data-td="mayo" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][mayo]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="mayo" data-input="partida" title="MAYO" >

                </td>
                <td data-td="junio" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][junio]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="junio" data-input="partida" title="JUNIO">

                </td>
                <td data-td="julio" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][julio]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="julio" data-input="partida" title="JULIO">

                </td>
                <td data-td="agosto" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][agosto]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="agosto" data-input="partida" title="AGOSTO">

                </td>
                <td data-td="setiembre" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][setiembre]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="setiembre" data-input="partida" title="SETIEMBRE">

                </td>
                <td data-td="octubre" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][octubre]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="octubre" data-input="partida" title="OCTUBRE">

                </td>
                <td data-td="noviembre" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][noviembre]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="noviembre" data-input="partida" title="NOVIEMBRE">

                </td>

                <td data-td="diciembre" style="padding-right: 0px;">
                    <input type="text" value="`+0+`.00" class="form-control input-sm" name="`+data_text_presupuesto+`[`+data_id_random+`][diciembre]" placeholder="Ingrese monto" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" data-mes="diciembre" data-input="partida" title="DICIEMBRE">

                </td>

                <td data-td="accion">

                    <div class="btn-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                            <input type="hidden" name="`+data_text_presupuesto+`[`+data_id_random+`][registro]" value="2">
                                <li><a href="#" class="" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-partida" data-select="partida" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Editar partida" data-tipo="editar">Editar partida</a></li>

                                <li><a href="#" class="" key="`+data_id_random+`" data-action="click-porcentaje" data-nivel="`+nivel_hijo+`" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`" title="Editar porcentaje" data-tipo="editar" data-text-partida="`+partida_nueva+`">Editar porcentaje</a></li>

                                <li><a href="#" class="" data-partida="`+partida_nueva+`" key="`+data_id_random+`" data-action="click-eliminar" data-nivel="`+nivel_hijo+`" title="Eliminar" data-id="`+data_id_random+`" data-id-padre="`+data_id+`" data-tipo-text="`+data_text_presupuesto+`">Eliminar</a></li>
                            </ul>
                        </div>
                    </div>


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
    }else{
        $('tr[key="'+key+'"] td[data-td="descripcion"] [name="'+data_text_presupuesto+'['+key+'][descripcion]"]').val(descripcion_partida);
        $('tr[key="'+key+'"] td[data-td="descripcion"] span').text(descripcion_partida);

        // $('tr[key="'+key+'"] td[data-td="monto"] [name="'+data_text_presupuesto+'['+key+'][monto]"]').val(monto_partida);
        // $('tr[key="'+key+'"] td[data-td="monto"] span').text(monto_partida);


    }

    $('#modal-partida').modal('hide');


});
// editas la vista
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
                Swal.fire({
                    title: result.value.titulo,
                    text: result.value.texto,
                    icon: result.value.icono,
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        // $('#nuevo-cliente').modal('hide');
                        // window.location.href = "lista";
                    }
                })
            }
        }
    });
});

function sumarPartidas(data_id,data_id_padre,data_text_presupuesto,mes,numero_mes) {
    var suma_partida = 0;
    while (data_id_padre!=='0' && data_id_padre!==undefined) {
        suma_partida = 0;

        $.each($('tr[data-id-padre="'+data_id_padre+'"]'), function (index, element) {

            var array_aplit = element.children[numero_mes].children[0].value,
                array_aplit = array_aplit.split(','),
                monto_editar='';

            for (let index = 0; index < array_aplit.length; index++) {
                monto_editar = monto_editar + array_aplit[index];
            }
            suma_partida = parseFloat(suma_partida) + parseFloat(monto_editar);


        });

        suma_partida = suma_partida.toFixed(2);
        suma_partida = separator(suma_partida);

        data_td_key = $('tr[data-id="'+data_id_padre+'"]').attr('key');

        $('tr[data-id="'+data_id_padre+'"] td[data-td="'+mes+'"] [name="'+data_text_presupuesto+'['+data_td_key+']['+mes+']"]').val(suma_partida);
        $('tr[data-id="'+data_id_padre+'"] td[data-td="'+mes+'"] span').text(suma_partida);

        data_id = $('tr[data-id="'+data_id_padre+'"]').attr('data-id')
        data_id_padre = $('tr[data-id="'+data_id_padre+'"]').attr('data-id-padre')
    }
}

function separator(numb) {
    var str = numb.toString().split(".");
    str[0] = str[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return str.join(".");
}
$('[name="id_grupo"]').change(function (e) {
    e.preventDefault();
    var id_grupo = $(this).val(),
        html='';
    $.ajax({
        type: 'GET',
        url: 'get-area',
        data: {id_grupo:id_grupo},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            // console.log(data);
        }
    }).done(function(response) {
        html ='<option value="" hidden>Seleccione...</option>';
        $.each(response.data, function (index, element) {
            html+='<option value="'+element.id_division+'" >'+element.descripcion+'</option>';
        });
        $('[name="id_area"]').html(html);

    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});
$(document).on('change','[data-input="partida"]',function (e) {
    e.preventDefault();
    const PORCENTAJE_ESSALUD        = 0.09;
    const PORCENTAJE_SCTR           = 0.0158;
    const PORCENTAJE_ESSALUD_VIDA   = 0.0127;

    const PORCENTAJE_SERVICIOS  = 0.0833;
    const GRATIFICACIONES       = 6;
    const VACACIONES            = 12;

    var value = $(this).val();

    value=(!isNaN(parseFloat(value))?value:'0.00');
    var array_value = value.split(','),
        monto='',
        data_id = $(this).attr('data-id'),
        data_id_padre = $(this).attr('data-id-padre'),
        data_text_presupuesto = $(this).attr('data-tipo-text'),
        mes = $(this).attr('data-mes'),
        key = $(this).attr('key'),
        data_auxiliar_valor = $(this).attr('data-auxiliar-valor'), //saldo
        data_valor = $(this).attr('data-valor'), //valor inicial
        numero_mes=0;
    for (let index = 0; index < array_value.length; index++) {
        monto = monto + array_value[index];
    }
    // de string a valor numerico y redondearlo a 2 digitos
    monto  = parseFloat(monto).toFixed(2);
    monto_calculable  = parseFloat(monto);


    console.log(data_auxiliar_valor);
    var valor_auxiliar='', //saldo
        valor='',//valor inicial
        gasto=0,//valor gasto
        diferencia=0,
        modificacion = true;
    if (data_auxiliar_valor!==undefined && data_valor!==undefined) {
        $.each(data_auxiliar_valor.split(','), function (index_valor, element_valor) {
            valor_auxiliar = valor_auxiliar + element_valor;
        });
        $.each(data_valor.split(','), function (index_valor, element_valor) {
            valor = valor + element_valor;
        });

        diferencia = monto_calculable - valor;
        diferencia = (diferencia>=0?diferencia:(diferencia*-1));
        gasto = parseFloat(valor) - parseFloat(valor_auxiliar);

        if (monto_calculable<gasto) {
            modificacion = false
            Swal.fire(
                'Información',
                'Usted tiene un gasto de  '+gasto+', el monto debe de ser mayor o igual al gasto.',
                'warning'
            )
            $(this).val(separator(gasto));
            $(this).trigger('change');
        }
    }


    if (modificacion===true) {
            // agregarle las comas
        monto  = separator(monto);
        $(this).val(monto);
        $(this).closest('td').find('span').text(monto);
        numero_mes = numeroMes(mes);

        var total_partidas_modificar = 0,
            estado = $('[name="estado"]').val(),
            exedio = false,
            limite_total = '',
            limite_string = $('tr[data-id="'+data_id_padre+'"]').find('td[data-td="'+mes+'"]').find('label.total-limite').text();
        if (estado==='2') {

            $.each($('tr[data-id-padre="'+data_id_padre+'"]'), function (index, element) {
                var this_valor = element.children[numero_mes].children[0].value,
                    array_valor = this_valor.split(','),
                    valor_string = '';

                $.each(array_valor, function (index_valor, element_valor) {
                    valor_string = valor_string + element_valor;
                });

                total_partidas_modificar = total_partidas_modificar + parseFloat(valor_string);

            });

            $.each(limite_string.split(','), function (index_valor, element_valor) {
                limite_total = limite_total + element_valor;
            });
            limite_total = parseFloat(limite_total);
            if (total_partidas_modificar > limite_total) {
                exedio = true;
            }else{
                exedio = false;
            }
            // $('[name="id_presupuesto_interno"]').val()

        }

        if (exedio===false) {
            // suma todas las partidas
            sumarPartidas(
                data_id,
                data_id_padre,
                data_text_presupuesto,
                mes,
                numero_mes
            );
            // calcular las celdas de costos
            switch (data_text_presupuesto) {
                case "ingresos":
                    if ($porcentajes.length>0) {
                        var porcentaje_gobierno = $(this).closest('tr[key="'+key+'"]').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_gobierno]"]').val(),
                            porcentaje_privado = $(this).closest('tr[key="'+key+'"]').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_privado]"]').val(),
                            porcentaje_comicion = $(this).closest('tr[key="'+key+'"]').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_comicion]"]').val(),
                            porcentaje_penalidad = $(this).closest('tr[key="'+key+'"]').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_penalidad]"]').val(),
                            valor_padre = $('tr[data-id="'+data_id_padre+'"] td[data-td="'+mes+'"]').find('input').val(),
                            partida_ingreso = $(this).closest('tr[key="'+key+'"]').find('td[data-td="partida"]').find('[name="ingresos['+key+'][partida]"]').val(),
                            partida_costo = '02',
                            numero_partida='',
                            partida_comision='',
                            partida_penalidad='';

                        var costo_gobierno,
                            costo_privado,
                            costo_comisiones,
                            costo_penalidades,
                            valor_cabecera='';

                        valor_padre = valor_padre.split(',');
                        valor_padre.forEach(element => {
                            valor_cabecera = valor_cabecera + element;
                        });
                        valor_cabecera = parseFloat(valor_cabecera);
                        costo_gobierno      = monto_calculable * (parseFloat(porcentaje_gobierno)/100);
                        costo_privado       = monto_calculable * (parseFloat(porcentaje_privado)/100);
                        costo_comisiones    = parseFloat(valor_cabecera) * (parseFloat(porcentaje_comicion)/100);
                        costo_penalidades   = parseFloat(valor_cabecera) * (parseFloat(porcentaje_penalidad)/100);


                        partida_ingreso = partida_ingreso.split('.');
                        $.each(partida_ingreso, function (index, element) {
                            if ((index+1)==partida_ingreso.length) {
                                numero_partida = element;

                                partida_comision = partida_costo + '.03';
                                partida_penalidad = partida_costo + '.04';
                            }
                            if (index!==0) {
                                partida_costo = partida_costo+'.'+element;
                            }

                        });

                        if (numero_partida == '01') {
                            $('input[value="'+partida_costo+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(costo_gobierno.toFixed(2))
                            $('input[value="'+partida_costo+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('span').text(costo_gobierno.toFixed(2))

                            $('input[value="'+partida_costo+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input[data-input="partida"]').trigger('change')

                        }
                        if (numero_partida == '02') {
                            $('input[value="'+partida_costo+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(costo_privado.toFixed(2))
                            $('input[value="'+partida_costo+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('span').text(costo_privado.toFixed(2))

                            $('input[value="'+partida_costo+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input[data-input="partida"]').trigger('change')
                        }

                        $('input[value="'+partida_comision+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(costo_comisiones.toFixed(2))
                        $('input[value="'+partida_comision+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('span').text(costo_comisiones.toFixed(2))

                        $('input[value="'+partida_comision+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input[data-input="partida"]').trigger('change')

                        $('input[value="'+partida_penalidad+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(costo_penalidades.toFixed(2))
                        $('input[value="'+partida_penalidad+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('span').text(costo_penalidades.toFixed(2))

                        $('input[value="'+partida_penalidad+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input[data-input="partida"]').trigger('change')
                    }
                break;

                case "gastos":
                    var partida_padre = $('tr[data-id="'+data_id_padre+'"] td[data-td="partida"]').find('input[name="gastos['+$('tr[data-id="'+data_id_padre+'"]').attr('key')+'][partida]"]').val(),
                        partida_hijo = $(this).closest('tr').find('td[data-td="partida"]').find('input[name="gastos['+key+'][partida]"]').val(),
                        total=0,
                        essalud     =0,
                        sctr        =0,
                        essalud_vida=0,
                        servicios       =0,
                        gratificaciones =0,
                        vacacione       =0;

                    if (partida_hijo === '03.01.01.01' || partida_hijo === '03.01.01.02' || partida_hijo === '03.01.01.03') {
                        $.each($('tr[data-id-padre="'+data_id_padre+'"]'), function (index, element) {
                            if (index<3) {
                                var this_valor = element.children[numero_mes].children[0].value,
                                    array_valor = this_valor.split(','),
                                    valor_string = '';

                                $.each(array_valor, function (index_valor, element_valor) {
                                    valor_string = valor_string + element_valor;
                                });

                                total = total + parseFloat(valor_string);

                            }

                        });
                        total = total.toFixed(2);
                        essalud         = (total * PORCENTAJE_ESSALUD).toFixed(0);
                        sctr            = (total * PORCENTAJE_SCTR).toFixed(0);
                        essalud_vida    = (total * PORCENTAJE_ESSALUD_VIDA).toFixed(0);

                        servicios       = (total * PORCENTAJE_SERVICIOS).toFixed(2);
                        gratificaciones = (total / GRATIFICACIONES).toFixed(2);
                        vacacione       = (total / VACACIONES).toFixed(2);
                        console.log(partida_padre);
                        var array_partida_padre = partida_padre.split('.'),
                            partida_ESSALUD = '.01',
                            partida_SCTR = '.02',
                            partida_ESSALUD_VIDA = '.03',
                            partida_SERVICIOS = '.01',
                            partida_GRATIFICACIONES = '.02',
                            partida_VACACIONES = '.03',

                            partida_PATRONALES = '.02',
                            partida_PROVISIONES = '.03',
                            partida_abuelo;
                        $.each(array_partida_padre, function (index, element) {
                            if (index < array_partida_padre.length-1) {
                                partida_abuelo = (index===0 ?element: partida_abuelo + '.'+element);
                            }

                        });
                        partida_PATRONALES = partida_abuelo+ partida_PATRONALES
                        partida_PROVISIONES = partida_abuelo + partida_PROVISIONES

                        partida_ESSALUD         = partida_PATRONALES + partida_ESSALUD;
                        partida_SCTR            = partida_PATRONALES + partida_SCTR;
                        partida_ESSALUD_VIDA    = partida_PATRONALES + partida_ESSALUD_VIDA;

                        partida_SERVICIOS       = partida_PROVISIONES + partida_SERVICIOS;
                        partida_GRATIFICACIONES = partida_PROVISIONES + partida_GRATIFICACIONES;
                        partida_VACACIONES      = partida_PROVISIONES + partida_VACACIONES;

                        // 03.01.02.01	ESSALUD
                        // $('input[value="'+partida_ESSALUD+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(essalud)
                        // $('input[value="'+partida_ESSALUD+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').trigger('change')
                        // 03.01.02.02	SCTR
                        // $('input[value="'+partida_SCTR+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(sctr)
                        // $('input[value="'+partida_SCTR+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').trigger('change')
                        // 03.01.02.03	ESSALUD VIDA
                        // $('input[value="'+partida_ESSALUD_VIDA+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(essalud_vida)
                        // $('input[value="'+partida_ESSALUD_VIDA+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').trigger('change')

                        // 03.01.03.01	COMPENSACION POR TIEMPO DE SERVICIOS
                        // $('input[value="'+partida_SERVICIOS+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(servicios)
                        // $('input[value="'+partida_SERVICIOS+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').trigger('change')
                        // 03.01.03.02	GRATIFICACIONES
                        // $('input[value="'+partida_GRATIFICACIONES+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(gratificaciones)
                        // $('input[value="'+partida_GRATIFICACIONES+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').trigger('change')
                        // 03.01.03.03	VACACIONES
                        // $('input[value="'+partida_VACACIONES+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').val(vacacione)
                        // $('input[value="'+partida_VACACIONES+'"]').closest('tr').find('td[data-td="'+mes+'"]').find('input').trigger('change')

                    }

                break;
            }
            if ($porcentajes.length>0 && data_text_presupuesto=="ingresos") {


            }
        }else{
            $(this).val($(this).closest('tr').find('td[data-td="'+mes+'"]').find('label.total-limite').text());
            $(this).closest('td').find('span').text($(this).closest('tr').find('td[data-td="'+mes+'"]').find('label.total-limite').text());
            $(this).trigger('change');
            Swal.fire(
                'Información',
                'Solo puede ingresar montos que al ser sumados no exedan los '+limite_string,
                'warning'
            )
        }
    }






});

$(document).on('click','[data-action="remove"]',function () {
    var tipo = $(this).attr('data-tipo');

    // $('[data-select="presupuesto-'+tipo+'"]').closest('.box.box-success').closest('div.col-md-12').addClass('animate__animated animate__fadeOut');
    $('[data-select="presupuesto-'+tipo+'"]').closest('.box.box-success').closest('div.col-md-12').addClass('d-none');

    $('[data-select="presupuesto-'+tipo+'"] div').remove();
    // $('[data-select="presupuesto-'+tipo+'"]').;

});
function numeroMes(mes) {
    var numero_mes=0;
    switch (mes) {
        case 'enero':
            numero_mes=3;
        break;

        case 'febrero':
            numero_mes=4;
        break;
        case 'marzo':
            numero_mes=5;
        break;
        case 'abril':
            numero_mes=6;
        break;
        case 'mayo':
            numero_mes=7;
        break;
        case 'junio':
            numero_mes=8;
        break;
        case 'julio':
            numero_mes=9;
        break;
        case 'agosto':
            numero_mes=10;
        break;
        case 'setiembre':
            numero_mes=11;
        break;
        case 'octubre':
            numero_mes=12;
        break;
        case 'noviembre':
            numero_mes=13;
        break;
        case 'diciembre':
            numero_mes=14;
        break;
    }
    return numero_mes;
}
// abre el modal para los porcentajes
$(document).on('click','[data-input="partida"]',function (e) {
    e.preventDefault();
    var text = $(this).attr('data-tipo-text'),
        key = $(this).attr('key'),
        data_id = $(this).attr('data-id'),
        data_id_padre = $(this).attr('data-id-padre'),
        array_split=[],
        numero_partida = 0,
        html='',
        partida = '',
        partida_padre = '',
        partida_gobierno = '',
        partida_privada = '';
    var elemento, elemento_find;

    if (text === 'ingresos') {


        partida = $(this).closest('tr[key="'+key+'"]').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][partida]"]').val();

        array_split = partida.split('.');

        numero_partida = array_split[array_split.length-1];

        var concatener_partida = '';

        $.each(array_split, function (index, element) {
            if (index<array_split.length-1) {
                concatener_partida = (index===0? element : concatener_partida+'.'+element);
            }
            if (index<array_split.length-1) {
                partida_padre =(index === 0 ? element : partida_padre+'.'+element)
            }
            // partida_padre = partida_padre+'.'+element
        });
        // console.log(partida_padre);

        // var procentaje_gobierno = (numero_partida==='01'?$(this).closest('tr').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_gobierno]"]').val():0),
        //     procentaje_privado = (numero_partida==='02'?$(this).closest('tr').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_privado]"]').val():0),
        //     porcentaje_comisiones = $(this).closest('tr').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_comicion]"]').val(),
        //     porcentaje_penalidades = $(this).closest('tr').find('td[data-td="partida"]').find('input[name="ingresos['+key+'][porcentaje_penalidad]"]').val(),
        //     modal=0;


        // var elemento_objt = $porcentajes.find(e => e.partida == partida_padre);
        // if (!elemento_objt) {

        //     $porcentajes.push({
        //         partida:concatener_partida,
        //         partida_gobierno:partida_gobierno,
        //         partida_privada:partida_privada,
        //         procentaje_gobierno:0,
        //         porcentaje_privado:0,
        //         porcentaje_comisiones:0,
        //         porcentaje_penalidades:0
        //     });
        // }else{
        //     elemento.partida_gobierno = partida_gobierno;
        //     elemento.partida_privada = partida_privada;
        // }
        // console.log(partida);
        modalPorcentaje(
            numero_partida,
            partida,
            concatener_partida,
            key,
            data_id,
            data_id_padre

            // procentaje_gobierno,
            // procentaje_privado,
            // porcentaje_comisiones,
            // porcentaje_penalidades
        )


    }
});
// guarda los porcentajes ingresados
$(document).on('submit','[data-form="guardar-costos-modal"]',function (e) {
    e.preventDefault();
    var data = $(this).serializeArray()
        partida = data[0].value,
        partida_hijo = data[1].value,
        partida_privada = '',
        key = $(this).find('input[name="key"]').val(),
        data_id = $(this).find('input[name="data_id"]').val(),
        data_id_padre = $(this).find('input[name="data_id_padre"]').val(),
        numero_partida = partida_hijo.split('.')
        ;

    var partida_hijo_coto = '02',
        partida_comicion='.03',
        partida_penalidad='.04';

    $.each(partida_hijo.split('.'), function (index, element) {
        if (index !==0) {
            partida_hijo_coto = partida_hijo_coto+'.'+element;
            if (index == partida_hijo.split('.').length-2) {
                partida_comicion = partida_hijo_coto+partida_comicion;
                partida_penalidad = partida_hijo_coto+partida_penalidad;
            }
        }

    });
    numero_partida = numero_partida[numero_partida.length-1];

    if (numero_partida=='01') {
        $porcentajes.forEach(element => {
            if (element.partida==partida) {
                element.partida=partida;
                element.partida_gobierno=partida_hijo;
                element.procentaje_gobierno=data[2].value;
                element.porcentaje_comisiones=data[3].value;
                element.porcentaje_penalidades=data[4].value;
                // partida_privada = element.partida_privada;
                if (element.partida_privada) {
                    var key_partida_privada = $('input[value="'+element.partida_privada+'"]').closest('tr').attr('key');

                    $('tr[key="'+key_partida_privada+'"] td[data-td="partida"]').find('input[name="ingresos['+key_partida_privada+'][porcentaje_comicion]"]').val(data[3].value)

                    $('tr[key="'+key_partida_privada+'"] td[data-td="partida"]').find('input[name="ingresos['+key_partida_privada+'][porcentaje_penalidad]"]').val(data[4].value)
                }

            }
        });

        $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"] td[data-td="partida"] input[name="ingresos['+key+'][porcentaje_gobierno]"]').val(data[2].value);

        $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"] td[data-td="partida"] input[name="ingresos['+key+'][porcentaje_comicion]"]').val(data[3].value);
        $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"] td[data-td="partida"] input[name="ingresos['+key+'][porcentaje_penalidad]"]').val(data[4].value);
        // $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"] td[data-td="partida"] input[name="ingresos['+key+'][porcentaje_gobierno]"]').val(data[2].value)
        console.log(data);
        $('input[value="'+partida_hijo_coto+'"]').closest('tr').find('td[data-td="porcentaje"]').find('span').text(data[2].value);
        $('input[value="'+partida_hijo_coto+'"]').closest('tr').find('td[data-td="porcentaje"]').find('input').val(data[2].value);

        $('input[value="'+partida_comicion+'"]').closest('tr').find('td[data-td="porcentaje"]').find('span').text(data[3].value);
        $('input[value="'+partida_comicion+'"]').closest('tr').find('td[data-td="porcentaje"]').find('input').val(data[3].value);

        $('input[value="'+partida_penalidad+'"]').closest('tr').find('td[data-td="porcentaje"]').find('span').text(data[4].value);
        $('input[value="'+partida_penalidad+'"]').closest('tr').find('td[data-td="porcentaje"]').find('input').val(data[4].value);


    }
    if (numero_partida=='02') {
        elemento = $porcentajes.find(e => e.partida_gobierno == partida);
        $porcentajes.forEach(element => {
            if (element.partida==partida) {
                element.partida=partida;
                element.partida_privada=partida_hijo;
                element.porcentaje_privado=data[2].value;

                $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"] td[data-td="partida"] input[name="ingresos['+key+'][porcentaje_comicion]"]').val((element.porcentaje_comisiones?element.porcentaje_comisiones:0));

                $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"] td[data-td="partida"] input[name="ingresos['+key+'][porcentaje_penalidad]"]').val((element.porcentaje_penalidades?element.porcentaje_penalidades:0));


                $('input[value="'+partida_comicion+'"]').closest('tr').find('td[data-td="porcentaje"]').find('span').text((element.porcentaje_comisiones?element.porcentaje_comisiones:0));
                $('input[value="'+partida_comicion+'"]').closest('tr').find('td[data-td="porcentaje"]').find('input').val((element.porcentaje_comisiones?element.porcentaje_comisiones:0));

                $('input[value="'+partida_penalidad+'"]').closest('tr').find('td[data-td="porcentaje"]').find('span').text((element.porcentaje_penalidades?element.porcentaje_penalidades:0));
                $('input[value="'+partida_penalidad+'"]').closest('tr').find('td[data-td="porcentaje"]').find('input').val((element.porcentaje_penalidades?element.porcentaje_penalidades:0));
            }
        });
        $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"] td[data-td="partida"] input[name="ingresos['+key+'][porcentaje_privado]"]').val(data[2].value);

        $('input[value="'+partida_hijo_coto+'"]').closest('tr').find('td[data-td="porcentaje"]').find('span').text(data[2].value);
        $('input[value="'+partida_hijo_coto+'"]').closest('tr').find('td[data-td="porcentaje"]').find('input').val(data[2].value);
    }
    $('tr[key="'+key+'"][data-id="'+data_id+'"][data-id-padre="'+data_id_padre+'"]').find('[data-input="partida"]').trigger('change');


    $('#modal-costos').modal('hide');
});
$(document).on('click','[data-action="click-porcentaje"]',function (e) {
    e.preventDefault();
    var partida = $(this).attr('data-text-partida'),
        object_partida,
        numero_partida,
        array_aplit,
        data_id = $(this).attr('data-id'),
        key = $(this).attr('key'),
        data_id_padre = $(this).attr('data-id-padre'),
        key_padre = $('tr[data-id="'+data_id_padre+'"]').attr('key') ;
        partida_padre = $('tr[data-id="'+data_id_padre+'"]').find('td[data-td="partida"]').find('input[name="ingresos['+key_padre+'][partida]"]').val() ;

    array_aplit = partida.split('.');
    numero_partida = array_aplit[array_aplit.length-1];
    $.each($porcentajes, function (index, element) {
        if (element.partida_privada == partida) {
            object_partida = element;
            // numero_partida = '02';
            return object_partida;
        }
        if (element.partida_gobierno == partida) {
            object_partida = element;
            // numero_partida = '01';
            return object_partida;
        }
    });
    // $(this).closest('tr').find('input[data-input="partida"]').trigger('change');
    var procentaje_gobierno = object_partida ?object_partida.procentaje_gobierno:0,
        procentaje_privado = object_partida ?object_partida.porcentaje_privado:0,
        porcentaje_comisiones = object_partida ?object_partida.porcentaje_comisiones:0,
        porcentaje_penalidades = object_partida ?object_partida.porcentaje_penalidades:0;

    modalPorcentaje(numero_partida, partida, partida_padre, key, data_id, data_id_padre, procentaje_gobierno, procentaje_privado,porcentaje_comisiones, porcentaje_penalidades,1);


});
function modalPorcentaje(numero_partida, partida, concatener_partida, key, data_id, data_id_padre, procentaje_gobierno = 0, procentaje_privado = 0,porcentaje_comisiones = 0, porcentaje_penalidades = 0, modal=0) {
    var html = `<div class="form-group"><p>No ingreso ni un porcentaje</p></div>`,
        elemento,
        elemento_find,
        partida_privada,
        partida_gobierno;

    if (numero_partida==='01') {
        partida_gobierno=partida;
        html=`
        <input type="hidden" name="partida" value="`+concatener_partida+`">
        <input type="hidden" name="partida_gobierno" value="`+partida_gobierno+`">
        <div class="form-group">
            <label for="procentaje_gobierno">Ingrese Porcentaje de costo :</label>
            <input id="procentaje_gobierno" class="form-control" type="number" name="procentaje_gobierno" value="`+procentaje_gobierno+`" required>
        </div>
        <div class="form-group">
            <label for="porcentaje_comisiones">Ingrese Porcentaje de comicion :</label>
            <input id="porcentaje_comisiones" class="form-control" type="number" name="porcentaje_comisiones" value="`+porcentaje_comisiones+`" required>
        </div>
        <div class="form-group">
            <label for="porcentaje_penalidades">Ingrese Porcentaje de penalidad :</label>
            <input id="porcentaje_penalidades" class="form-control" type="number" name="porcentaje_penalidades" value="`+porcentaje_penalidades+`" required>
        </div>
        <input type="hidden" name="key" value="`+key+`">
        <input type="hidden" name="data_id" value="`+data_id+`">
        <input type="hidden" name="data_id_padre" value="`+data_id_padre+`">
        `;
        elemento = $porcentajes.find(e => e.partida == concatener_partida);
        elemento_find = $porcentajes.find(e => e.partida_gobierno==partida_gobierno);
        if (elemento) {
            partida_privada = elemento.partida_privada;
        }

    }
    if (numero_partida==='02') {
        partida_privada=partida;
        html=`
            <input type="hidden" name="partida" value="`+concatener_partida+`">
            <input type="hidden" name="partida_privada" value="`+partida_privada+`">
            <div class="form-group">
                <label for="porcentaje_privado">Ingrese Porcentaje de costo :</label>
                <input id="porcentaje_privado" class="form-control" type="number" name="procentaje_privado" value="`+procentaje_privado+`" required>
            </div>
            <input type="hidden" name="key" value="`+key+`">
            <input type="hidden" name="data_id" value="`+data_id+`">
            <input type="hidden" name="data_id_padre" value="`+data_id_padre+`">
        `;
        elemento = $porcentajes.find(e => e.partida == concatener_partida );

        elemento_find = $porcentajes.find(e => e.partida_privada==partida_privada);
        if (elemento) {
            partida_gobierno = elemento.partida_gobierno;
        }

    }
    $('#modal-costos .modal-body').html(html);

    if (!elemento_find) {
        $('#modal-costos').modal({
            show: true,
            backdrop: "static"
        });
    }
    if (modal!==0) {
        $('#modal-costos').modal({
            show: true,
            backdrop: "static"
        });
    }
    if (!elemento) {

        $porcentajes.push({
            partida:concatener_partida,
            partida_gobierno:partida_gobierno,
            partida_privada:partida_privada,
            procentaje_gobierno:0,
            porcentaje_privado:0,
            porcentaje_comisiones:0,
            porcentaje_penalidades:0
        });
    }else{
        elemento.partida_gobierno = partida_gobierno;
        elemento.partida_privada = partida_privada;
    }
}

// sedes por empresa
$('[name="empresa_id"]').change(function (e) {
    var id = $(this).val(),
        html = '';
    console.log(id);
    $.ajax({
        type: 'GET',
        url: '/necesidades/requerimiento/elaboracion/listar-sedes-por-empresa/'+id,
        data: {},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            // console.log(data);
        }
    }).done(function(response) {
        html = '<option value="">Seleccione...</option>';
        $.each(response, function (index, element) {
            html += '<option value="'+element.id_sede+'">'+element.descripcion+'</option>';
        });
        $('[name="sede_id"]').html(html);
        console.log(response);
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

});
