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
        html_presupuesto='';

    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-4').removeClass('animate__animated animate__fadeIn');

    $.each(data.presupuesto, function (index, element) {
        html+='<tr>'
            html+='<td>'+element.partida+'</td>'
            html+='<td>'+element.descripcion+'</td>'
        html+='</tr>';
    });
    html_presupuesto=`
        <div class="col-md-12">
            <label>`+data.tipo+`</label>
            <div class="pull-right">
                <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_`+data.tipo+`">
                <i class="fa fa-minus"></i></a>

                <button class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="`+data.tipo_next+`" data-action="generar"></i></button>

            </div>
        </div>
        <div class="col-md-12 panel-collapse collapse in" id="collapse_`+data.tipo+`">
            <table class="table table-hover" id="listaPartidas">
                <thead>
                    <tr>
                        <th class="text-left" width="20%">PARTIDA</th>
                        <th class="text-left">DESCRIPCION</th>
                    </tr>
                </thead>
                <tbody data-table-presupuesto="ingreso">`+html+`</tbody>
            </table>
        </div>
    `
    $('[data-select="presupuesto-'+data.id_tipo+'"]').html(html_presupuesto);
    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-4').removeClass('d-none');

    $('[data-select="presupuesto-'+data.id_tipo+'"]').closest('.box.box-success').closest('div.col-md-4').addClass('animate__animated animate__fadeIn');

    if (data.id_tipo == '1') {
        getModelo(2);
        // generarModelo(data);
    }
}
