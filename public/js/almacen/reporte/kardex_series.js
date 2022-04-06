$(function () {
    var fecha = new Date();
    var anio = fecha.getFullYear();
    $('[name=fecha_inicio]').val(anio + '-01-01');
    $('[name=fecha_fin]').val(fecha_actual());
});

function listarKardexSeries() {
    var serie = $('[name=serie]').val();
    var descripcion = $('[name=descripcion]').val();
    var codigo = $('[name=codigo]').val();
    var part_number = $('[name=part_number]').val();

    console.log('serie:' + serie);

    if (serie == '' && codigo == '' && part_number == '' && descripcion == '') {
        alert('No ha ingresado ningún parametro de entrada!');
    }
    else {
        $('.dataTable').dataTable().fnDestroy();

        var vardataTables = funcDatatables();
        $('#listaKardexSeries').dataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'bDestroy': true,
            'retrieve': true,
            'ajax': 'listar_serie_productos/' + (serie !== '' ? serie : null) + '/' + (descripcion !== '' ? descripcion : null)
                + '/' + (codigo !== '' ? codigo : null) + '/' + (part_number !== '' ? part_number : null),
            'columns': [
                { 'data': 'id_prod_serie' },
                { 'data': 'serie' },
                { 'data': 'codigo' },
                { 'data': 'part_number' },
                { 'data': 'descripcion' },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });
    }
}

$('#listaKardexSeries tbody').on("click", "tr", function () {
    var data = $('#listaKardexSeries').DataTable().row($(this)).data();
    console.log(data);
    $('#modal-modalKardexSerie').modal({
        show: true
    });
    $('#serie').text(data.serie);
    $('#codigo').text(data.codigo);
    $('#part_number').text(data.part_number);
    $('#descripcion').text(data.descripcion);
    listar_kardex_serie(data.serie, data.id_prod);
});

function listar_kardex_serie(serie, id_prod) {
    $.ajax({
        type: 'GET',
        url: 'listar_kardex_serie/' + serie + '/' + id_prod,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            response.forEach(element => {
                html += `<tr>
                <td><span class="ver label label-success" data-id="${element.id_prod}" >I</span></td>
                <td>${element.ingreso_codigo ?? 'STOCK INICIAL'}</td>
                <td>${element.almacen_compra ?? ''}</td>
                <td>${element.fecha_guia_com ?? ''}</td>
                <td>${element.guia_com ?? ''}</td>
                <td>${element.doc_com ?? ''}</td>
                <td>${element.razon_social_prove ?? ''}</td>
                <td>${element.operacion_compra ?? ''}</td>
                <td>${element.responsable_compra ?? ''}</td>
                </tr>
                ${element.id_guia_ven_det !== null ?
                        `<tr>
                <td><span class="ver label label-danger" data-id="${element.id_prod}" >S</span></td>
                <td>${element.salida_codigo ?? ''}</td>
                <td>${element.almacen_venta ?? ''}</td>
                <td>${element.fecha_guia_ven ?? ''}</td>
                <td>${element.guia_ven ?? ''}</td>
                <td></td>
                <td>${element.razon_social_cliente ?? ''}</td>
                <td>${element.operacion_venta ?? ''}</td>
                <td>${element.responsable_venta ?? ''}</td>
                </tr>` : ''}`;
            });
            $('#listaMovimientosSerie tbody').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function vista_extendida() {
    let body = document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse");
}