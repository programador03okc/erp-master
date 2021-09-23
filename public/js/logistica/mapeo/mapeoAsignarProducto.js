function listarProductosCatalogo() {
    var vardataTables = funcDatatables();
    $('#productosCatalogo').dataTable({
        // 'dom': vardataTables[1],
        'buttons': [],
        'language': vardataTables[0],
        "lengthChange": false,
        'bDestroy': true,
        'ajax': 'mostrar_prods',
        initComplete: function (settings, json) {
            let lblTitulo = document.createElement("div");
            lblTitulo.innerHTML = '<label style="font-size:18px">Catálogo de productos</label>';
            $('#productosCatalogo_wrapper .row ')[0].firstChild.append(lblTitulo);
        },
        'columns': [
            { 'data': 'id_producto' },
            { 'data': 'codigo' },
            { 'data': 'part_number' },
            { 'data': 'marca' },
            { 'data': 'descripcion' },
            {
                'render':
                    function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-success btn-xs" name="btnSeleccionarUbigeo" title="Seleccionar Producto" 
                                data-codigo="${row.codigo}" data-id="${row.id_producto}" 
                                data-partnumber="${row.part_number}" data-descripcion="${encodeURIComponent(row.descripcion)}" 
                                data-abreviatura="${row.abreviatura}" onclick="selectProductoAsignado(this);">
                                <i class="fas fa-check"></i>
                            </button>
                        `;
                    }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}

let sDesc = '';
let sPart = '';

function listarProductosSugeridos(part_number, descripcion, type) {
    var pn = '', ds = '';
    if (type == 1) {
        pn = part_number;
        ds = null;
    }
    else if (type == 2) {
        pn = null;
        ds = descripcion;
    }
    else {
        if (part_number !== '' && part_number !== null) {
            pn = part_number;
            ds = null;
        } else {
            pn = null;
            ds = descripcion;
        }
    }
    console.log(part_number, descripcion, type);
    if (part_number !== sPart || descripcion !== sDesc) {
        // console.log(pn, ds);
        $('#productosSugeridos tbody').html('');
        $.ajax({
            type: 'POST',
            url: 'actualizarSugeridos',
            data: {
                part_number: pn,
                descripcion: ds
            },
            success: function (response) {
                console.log(response);
                if (response.response == 'ok') {
                    listarSugeridos();
                    sPart = part_number;
                    sDesc = descripcion;
                }
            }
        });
    }

}

function listarSugeridos() {
    $.ajax({
        type: 'GET',
        url: 'listarProductosSugeridos',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            // console.log(response.length);
            html = '';
            if (response.length > 0) {
                response.forEach(function (element) {
                    html += `
                    <tr>
                    <td>${element.codigo ?? ''}</td>
                    <td>${element.part_number ?? ''}</td>
                    <td>${element.marca ?? ''}</td>
                    <td>${element.descripcion ?? ''}</td>
                    <td>
                        <button type="button" class="btn btn-success btn-xs" title="Seleccionar Producto" 
                            data-codigo="${element.codigo}" data-id="${element.id_producto}" 
                            data-partnumber="${element.part_number}" data-descripcion="${encodeURIComponent(element.descripcion)}" 
                            data-abreviatura="${element.abreviatura}" onclick="selectProductoAsignado(this);">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colSpan="5" class="text-center">No hay datos para mostrar</td></tr>';
            }
            $('#productosSugeridos tbody').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function selectProductoAsignado(obj) {
    let id = obj.dataset.id;
    let codigo = obj.dataset.codigo;
    let partnumber = obj.dataset.partnumber;
    let descripcion = obj.dataset.descripcion;
    let abreviatura = obj.dataset.abreviatura;
    let id_detalle = $('[name=id_detalle_requerimiento]').val();

    // console.log('selectProductoAsignado');
    var page = $('.page-main').attr('type');

    if (page == "ordenesPendientes") {
        let det = series_transformacion.find(element => element.id == id_detalle);
        det.id_producto = id;
        det.cod_prod = codigo;
        det.part_number = partnumber;
        det.descripcion = decodeURIComponent(descripcion);
        det.abreviatura = abreviatura;
        $('#modal-mapeoAsignarProducto').modal('hide');
        mostrar_detalle_transformacion();
    } else {
        let det = detalle.find(element => element.id_detalle_requerimiento == id_detalle);

        det.id_producto = id;
        det.codigo = codigo;
        det.part_number = partnumber;
        det.descripcion = decodeURIComponent(descripcion);
        $('#modal-mapeoAsignarProducto').modal('hide');
        mostrar_detalle();
    }

}

$("#form-crear").on("submit", function (e) {

    e.preventDefault();
    // var data = $(this).serialize();
    let id_cat = $('[name=id_categoria]').val();
    let id_subcat = $('[name=id_subcategoria]').val();
    let id_clasif = $('[name=id_clasif]').val();
    let id_unid = $('[name=id_unidad_medida]').val();
    let abreviatura = $('select[name="id_unidad_medida"] option:selected').text();
    let partnumber = $('[name=part_number]').val();
    let descripcion = $('[name=descripcion]').val();
    let id_detalle = $('[name=id_detalle_requerimiento]').val();
    let serie = $('[name=series]').is(':checked');

    var page = $('.page-main').attr('type');

    if (page == "ordenesPendientes") {
        let det = series_transformacion.find(element => element.id == id_detalle);
        det.id_producto = null;
        det.cod_prod = '';
        det.part_number = partnumber;
        det.descripcion = descripcion;
        det.id_categoria = id_cat;
        det.id_subcategoria = id_subcat;
        det.id_clasif = id_clasif;
        det.id_unidad_medida = id_unid;
        det.abreviatura = abreviatura;
        det.control_series = serie;

        $('#modal-mapeoAsignarProducto').modal('hide');
        mostrar_detalle_transformacion();
    } else {
        let det = detalle.find(element => element.id_detalle_requerimiento == id_detalle);
        det.id_producto = null;
        det.codigo = '';
        det.part_number = partnumber;
        det.descripcion = descripcion;
        det.id_categoria = id_cat;
        det.id_subcategoria = id_subcat;
        det.id_clasif = id_clasif;
        det.id_unidad_medida = id_unid;
        det.series = serie;

        $('#modal-mapeoAsignarProducto').modal('hide');
        mostrar_detalle();
        console.log(det);
    }

});

$("[name=id_clasif]").on('change', function () {
    var id_clasificacion = $(this).val();
    console.log(id_clasificacion);
    $('[name=id_tipo_producto]').html('');
    $('[name=id_categoria]').html('');
    $.ajax({
        type: 'GET',
        // headers: { 'X-CSRF-TOKEN': token },
        url: 'mostrar_tipos_clasificacion/' + id_clasificacion,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);

            if (response.length > 0) {
                $('[name=id_tipo_producto]').html('');
                html = '<option value="0" >Elija una opción</option>';
                response.forEach(element => {
                    html += `<option value="${element.id_tipo_producto}" >${element.descripcion}</option>`;
                });
                $('[name=id_tipo_producto]').html(html);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

$("[name=id_tipo_producto]").on('change', function () {
    var id_tipo = $(this).val();
    // console.log(id_tipo);
    $.ajax({
        type: 'GET',
        url: 'mostrar_categorias_tipo/' + id_tipo,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);

            if (response.length > 0) {
                $('[name=id_categoria]').html('');
                html = '<option value="" >Elija una opción</option>';
                response.forEach(element => {
                    html += `<option value="${element.id_categoria}" >${element.descripcion}</option>`;
                });
                $('[name=id_categoria]').html(html);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});
