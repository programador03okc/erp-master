function mostrarCentroCostos() {
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': csrf_token},
        url: 'mostrar-centro-costos',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            response.forEach(element => {
                html += `<tr ${element.id_padre == null ? 'style="background: LightCyan;"' : ''}>
                <td width="15%">${element.codigo}</td>
                <td>${element.descripcion}</td>
                <td width="15%" style="padding:0px;">
                    <button type="button" class="btn btn-box-tool btn-xs btn-info editar" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar" data-id="${element.id_centro_costo}" data-codigo="${element.codigo}" data-descripcion="${element.descripcion}"
                        data-codpadre="" data-despadre="">
                        <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>

                    <button type="button" class="btn btn-box-tool btn-xs btn-danger anular" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular" data-id="${element.id_centro_costo}">
                        <i class="glyphicon glyphicon-remove" aria-hidden="true"></i></button>
                </td>`;
            });
            $('#listaCentroCostos tbody').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-centro-costos").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    var id = $('[name=id_centro_costo]').val();
    var url = '';
    $('#submit-cc').attr('disabled', 'true');

    if (id == '') {
        url = 'guardarCentroCosto';
    } else {
        url = 'actualizar-centro-costo';
    }
    console.log(data);
    guardar_cc(data, url);

});

function guardar_cc(data, url) {
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': csrf_token},
        url: url,
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('[name=id_centro_costo]').val('');
            $('[name=codigo]').val('');
            $('[name=descripcion]').val('');

            if (url == 'guardarCentroCosto') {
                alert('Se guardó exitosamente.');
            } else {
                alert('Se actualizó exitosamente.');
            }
            $('#submit-cc').removeAttr('disabled');
            mostrarCentroCostos();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#listaCentroCostos tbody").on('click', ".editar", function () {
    var id = $(this).data('id');
    var cod = $(this).data('codigo');
    var des = $(this).data('descripcion');

    $('#submit-cc').removeAttr('disabled');

    $('[name=id_centro_costo]').val(id);
    $('[name=codigo]').val(cod);
    $('[name=descripcion]').val(des);

});

$("#listaCentroCostos tbody").on('click', ".anular", function () {
    var id = $(this).data('id');
    var rspta = confirm('¿Está seguro que desea anular?');
    if (rspta) {
        anular_cc(id);
    }
});

function anular_cc(id) {
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': csrf_token},
        url: "anular-centro-costo/" + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            mostrarCentroCostos();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}