$(document).ready(function () {
    pagos();
    ordenes();
});
function pagos() {
    $.ajax({
        type: 'GET',
        url: 'cuadro-comparativo-pagos',
        data: {},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            // console.log(data);
        }
    }).done(function({empresas, estados, monedas}) {

        // console.log(empresas);
        renderizarCuadro(empresas, estados, monedas,'cuadro-pagos')
        // return response
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ordenes() {
    $.ajax({
        type: 'GET',
        url: 'cuadro-comparativo-ordenes',
        data: {},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            // console.log(data);
        }
    }).done(function({empresas, estados, monedas}) {
    // }).done(function(respuesta) {

        console.log(empresas);
        console.log(estados);
        renderizarCuadro(empresas, estados, monedas,'cuadro-ordenes')
        // return response
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function renderizarCuadro(empresas, estados, monedas, tabla) {
    let html = ``;
    let count_estados = estados.length;
    let count_monedas = monedas.length;

    $.each(empresas, function (index, element) {
        html += `<tr data-empresa="${element.id_empresa}">
            <td>${element.codigo}</td>`;
            $.each(estados, function (estado_index, estado_element) {
                html+=`<td data-estado="${estado_element.id_requerimiento_pago_estado}"><span></span></td>`;
            });
            $.each(monedas, function (moneda_index, moneda_element) {
                html+=`<td data-moneda="${moneda_element.id_moneda}"><span></span></td>`;
            });
        html+=`</tr>`;
    });
    $('#'+tabla).find('tbody').html(html);

    if (count_estados>0) {
        $.each(estados, function (index, element) {
            $.each(element.grupo, function (index_grupo, element_grupo) {
                $('#'+tabla).find('tbody').find('tr[data-empresa="'+element_grupo.empresa_id+'"]').find('td[data-estado="'+element_grupo.estado_id+'"]').find('span').text(element_grupo.cantidad);
            });
        });
    }

    if (count_monedas>0) {
        $.each(monedas, function (index, element) {
            $.each(element.grupo, function (index_grupo, element_moneda) {
                $('#'+tabla).find('tbody').find('tr[data-empresa="'+element_moneda.empresa_id+'"]').find('td[data-moneda="'+element_moneda.moneda_id+'"]').find('span').text(element_moneda.total);
            });
        });
    }



}
