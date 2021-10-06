$(function () {
    vista_extendida();
    mostrarSaldos();
});

function mostrarSaldos() {
    var almacen = $('[name=almacen]').val();
    if (almacen !== null && almacen !== '') {
        listarSaldos(almacen);
    }
}

function listarSaldos(almacen) {

    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'destroy': true,
        'ajax': 'listar_saldos/' + almacen,
        'columns': [
            { 'data': 'id_prod_ubi' },
            { 'data': 'codigo' },
            { 'data': 'part_number' },
            { 'data': 'descripcion' },
            { 'data': 'abreviatura' },
            { 'data': 'stock', 'class': 'text-center' },
            {
                'render':
                    function (data, type, row) {
                        if (row['cantidad_reserva'] !== null) {
                            return `<h5 style="margin-top: 0px;margin-bottom: 0px; cursor:pointer;">
                                    <span class="ver label label-danger" data-id="${row['id_producto']}" data-almacen="${row['id_almacen']}" >
                                    ${row['cantidad_reserva']} </span></h5>`;
                        } else {
                            return '';
                        }
                    }, 'class': 'text-center'
            },
            {
                'render':
                    function (data, type, row) {
                        let reserva = (row['cantidad_reserva'] !== null ? row['cantidad_reserva'] : 0);
                        return parseFloat(row['stock']) - parseFloat(reserva);
                    }, 'class': 'text-center'
            },
            { 'data': 'almacen_descripcion' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        "order": [[3, "asc"]]
    });
}

$('#listaSaldos tbody').on("click", "span.ver", function () {
    let id = $(this).data('id');
    let almacen = $(this).data('almacen');
    $('#modal-verRequerimientoEstado').modal({
        show: true
    });
    $('#nombreEstado').text('Requerimientos que generan la Reserva');
    console.log(id + ',' + almacen);
    verRequerimientosReservados(id, almacen);
});

function openReservados(id_producto, id_almacen) {
    $('#modal-verRequerimientoEstado').modal({
        show: true
    });
    $('#nombreEstado').text('Requerimientos que generan la Reserva');
    console.log(id_producto + ',' + id_almacen);
    verRequerimientosReservados(id_producto, id_almacen);
}

function verRequerimientosReservados(id_producto, id_almacen) {
    let baseUrl = 'verRequerimientosReservados/' + id_producto + '/' + id_almacen;
    console.log(baseUrl);
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var i = 1;
            var total = 0;
            response.forEach(element => {
                total += parseFloat(element.stock_comprometido);
                html += `<tr id="${element.id_requerimiento}">
                    <td class="text-center">
                    <label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento(${element.id_requerimiento})">
                    ${element.codigo}</label></td>
                    <td>${element.concepto}</td>
                    <td class="text-center">${element.almacen_descripcion}</td>
                    <td class="text-center">${(element.stock_comprometido !== null ? element.stock_comprometido : 0)}</td>
                    <td class="text-center">${(element.nombre_corto !== null ? element.nombre_corto : '')}</td>
                    <td class="text-center">${(element.guia_com !== null ? element.guia_com : '')}</td>
                    <td class="text-center">${(element.codigo_trans !== null ? element.codigo_trans : '')}</td>
                    <td class="text-center">${(element.codigo_transfor_materia !== null ? element.codigo_transfor_materia :
                        (element.codigo_transfor_transformado !== null ? element.codigo_transfor_transformado : ''))}</td>
                    </tr>`;
                i++;
            });
            $('#listaRequerimientosEstado tbody').html(html);
            $('#listaRequerimientosEstado tfoot').html(`<tr>
            <td colSpan="3"></td>
            <td class="text-center">${total}</td>
            <td></td>
            </tr>`);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function abrir_requerimiento(id_requerimiento) {
    localStorage.setItem("idRequerimiento", id_requerimiento);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    win.focus();
}