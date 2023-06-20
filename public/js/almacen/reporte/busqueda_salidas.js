$(function () {
    $('[name=id_empresa]').val(4);
    $('[name=almacen]').val(1);
    var fecha = new Date();
    var yyyy = fecha.getFullYear();
    $('[name=fecha_inicio]').val(yyyy + '-01-01');
    $('[name=fecha_fin]').val(yyyy + '-12-31');

    $('[name=todos_documentos]').prop('checked', true);
    $('[name=documento] option').each(function () {
        $(this).prop("selected", true);
    });
});
function actualizarLista() {
    $('#modal-busq_filtros').modal('hide');

    var almacenes = $('[name=almacen]').val();
    var documentos = $('[name=documento]').val();
    var tipo = $('[name=buscar]').val();
    var desc = $('[name=descripcion]').val();
    var descripcion = (desc !== '' ? desc : '<vacio>')
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();

    console.log(almacenes + '/' + tipo + '/' + desc + '/' + documentos + '/' + fini + '/' + ffin);

    var vardataTables = funcDatatables();
    var tabla = $('#listaBusquedaSalidas').DataTable({
        'destroy': true,
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'pageLength': 10,
        "scrollX": true,
        'ajax': {
            url: 'listar_busqueda_salidas/' + almacenes + '/' + tipo + '/' + descripcion + '/' + documentos + '/' + fini + '/' + ffin,
            dataSrc: ''
        },
        'columns': [
            { 'data': 'id_mov_alm_det' },
            { 'data': 'tp_doc' },
            { 'data': 'guia' },
            { 'data': 'fecha_guia' },
            { 'data': 'nro_documento' },
            { 'data': 'razon_social' },
            { 'data': 'ope_descripcion' },
            { 'data': 'codigo' },
            { 'data': 'part_number' },
            { 'data': 'descripcion' },
            { 'data': 'cantidad' },
            { 'data': 'estado_doc' },
            { 'data': 'alm_descripcion' },
            { 'data': 'codigo_requerimiento', 'name':'alm_req.codigo_requerimiento' },
            { 'data': 'cdp', 'name':'oportunidades.codigo_oportunidad' },
            { 'data': 'responsable' ,'name':'users.nombre_corto'},
            // {'data': 'des_condicion'},
            // {'data': 'credito_dias'},
            // {'data': 'des_operacion'},
            // {'data': 'fecha_vcmto'},
            // {'data': 'nombre_trabajador'},
            // {'data': 'tipo_cambio'},
            // {'data': 'des_almacen'},
            { 'data': 'fecha_registro' },
            {
                'defaultContent':
                    '<button type="button" class="ver btn btn-warning boton" data-toggle="tooltip" ' +
                    'data-placement="bottom" title="Ver">' +
                    '<i class="fas fa-search"></i></button>'
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        "order": [[2, "asc"], [3, "asc"]]
    });
    botones('#listaBusquedaSalidas tbody', tabla);
}
function botones(tbody, tabla) {
    console.log("change");
    $(tbody).on("click", "button.ver", function () {
        var data = tabla.row($(this).parents("tr")).data();
        var id = encode5t(data.id_mov_alm);
        window.open('imprimir_salida/' + id);
    });
}
/**Filtros Modal */
function open_filtros() {
    console.log('open_filtros');
    $('#modal-busq_filtros').modal({
        show: true
    });
}
$('[name=todos_documentos]').change(function () {
    if ($(this).prop('checked') == true) {
        $('[name=documento] option').each(function () {
            $(this).prop("selected", true);
        });
    } else {
        $('[name=documento] option').each(function () {
            $(this).prop("selected", false);
        });
    }
});
$('[name=todas_empresas]').change(function () {
    if ($(this).prop('checked') == true) {
        $('[name=almacen] option').each(function () {
            $(this).prop("selected", true);
        });
    } else {
        $('[name=almacen] option').each(function () {
            $(this).prop("selected", false);
        });
    }
});
$('[name=todos_almacenes]').change(function () {
    if ($(this).prop('checked') == true) {
        $('[name=almacen] option').each(function () {
            $(this).prop("selected", true);
        });
    } else {
        $('[name=almacen] option').each(function () {
            $(this).prop("selected", false);
        });
    }
});
$('[name=id_empresa]').change(function () {
    var emp = $('[name=id_empresa]').val();
    if (emp > 0) {
        $.ajax({
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': token },
            url: 'select_almacenes_empresa/' + emp,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                var htmls = '';
                Object.keys(response).forEach(function (key) {
                    htmls += '<option value="' + response[key]['id_almacen'] + '">' + response[key]['descripcion'] + '</option>';
                });
                console.log(htmls);
                $('[name=almacen]').html(htmls);
                $('[name=todas_empresas]').prop("checked", false);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});
