$("[name=id_empresa]").on('change', function () {
    var id_empresa = $(this).val();
    console.log(id_empresa);

    if (id_empresa == 0) {
        $('[name=id_sede]').val(0);
        $('[name=id_almacen]').val(0);
    } else {

        $('[name=id_sede]').html('');
        $('[name=id_almacen]').html('');
        $.ajax({
            type: 'GET',
            // headers: { 'X-CSRF-TOKEN': token },
            url: 'mostrarSedesPorEmpresa/' + id_empresa,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);

                if (response['sedes'].length > 0) {
                    $('[name=id_sede]').html('');
                    html = '<option value="0" selected>Todos las sedes</option>';
                    response['sedes'].forEach(element => {
                        html += `<option value="${element.id_sede}" >${element.descripcion}</option>`;
                    });
                    $('[name=id_sede]').html(html);
                }

                if (response['almacenes'].length > 0) {
                    $('[name=id_almacen]').html('');
                    html = '';
                    response['almacenes'].forEach(element => {
                        html += `<option value="${element.id_almacen}" selected>${element.codigo} - ${element.descripcion}</option>`;
                    });
                    $('[name=id_almacen]').html(html);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});


$("[name=id_sede]").on('change', function () {
    var id_sede = $(this).val();
    console.log(id_sede);

    // if (id_sede == 0) {
    //     $('[name=id_almacen]').val(0);
    // } else {

    $('[name=id_almacen]').html('');
    $.ajax({
        type: 'GET',
        url: 'mostrarAlmacenesPorSede/' + id_sede,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            if (response.length > 0) {
                $('[name=id_almacen]').html('');
                html = '';
                response.forEach(element => {
                    html += `<option value="${element.id_almacen}" selected>${element.codigo} - ${element.descripcion}</option>`;
                });
                $('[name=id_almacen]').html(html);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    // }
});

$("[name=id_almacen]").on('change', function () {
    var id_almacen = $(this).val();
    console.log(id_almacen);
    if (id_almacen == 0) {
        $('[name=id_empresa]').val(0);
        $('[name=id_sede]').val(0);
    }
});

$("[name=anio]").on('change', function () {
    var anio = $(this).val();
    cargarMeses(anio);
});

function cargarMeses(anio) {
    console.log(anio);
    $.ajax({
        type: "GET",
        url: 'cargarMeses/' + anio,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            $('[name=mes]').html('');
            html = '';
            response.forEach(element => {
                html += `<option value="${element.mes}" >${element.mes}</option>`;
            });
            $('[name=mes]').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}