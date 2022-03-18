function listarProductosCatalogo() {
    var vardataTables = funcDatatables();
    $('#productosCatalogo').DataTable({
        'dom': vardataTables[1],
        'buttons': [],
        'language': vardataTables[0],
        "lengthChange": false,
        'order': [[5, 'asc']],
        'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'mostrar_prods',
            'type': 'GET',
            beforeSend: data => {

                $("#productosCatalogo").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }

        },
        'columns': [
            { 'data': 'id_producto', 'name': 'alm_prod.id_producto', "searchable": false },
            { 'data': 'codigo', 'name': 'alm_prod.codigo'},
            { 'data': 'cod_softlink', 'name': 'alm_prod.cod_softlink'},
            { 'data': 'part_number', 'name': 'alm_prod.part_number'},
            { 'data': 'marca', 'name': 'alm_subcat.descripcion'},
            { 'data': 'descripcion', 'name': 'alm_prod.descripcion'},
            { 'data': 'id_producto', 'name': 'alm_prod.id_producto', "searchable": false }
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible','sWidth': '5%' },
            { 'aTargets': [1], 'className': "text-center", 'sWidth': '5%' },
            { 'aTargets': [2], 'className': "text-center", 'sWidth': '5%' },
            { 'aTargets': [3], 'className': "text-center", 'sWidth': '5%' },
            { 'aTargets': [4], 'className': "text-center", 'sWidth': '5%' },
            { 'aTargets': [5], 'className': "text-left", 'sWidth': '70%' },
            {
                'render':
                    function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-success btn-xs" name="btnSeleccionarUbigeo" title="Seleccionar Producto" 
                                data-codigo="${row.codigo}" data-id="${row.id_producto}" 
                                data-partnumber="${row.part_number}" data-descripcion="${encodeURIComponent(row.descripcion)}" 
                                data-abreviatura="${row.abreviatura}" data-series="${row.series}"
                                onclick="selectProductoAsignado(this);">
                                <i class="fas fa-check"></i>
                            </button>
                        `;
                    },'aTargets': 6 ,'className': "text-center", 'sWidth': '5%'
            }
        ],
        initComplete: function (settings, json) {
            // let wraper = $('#productosCatalogo_wrapper .row ')[0].firstChild;
            // wraper.innerHTML = '<label style="font-size:18px">Catálogo de productos</label>';
            // $('#productosCatalogo_wrapper .row ')[0].firstChild.remove('div');
            // console.log(wraper);
            // let lblTitulo;// = document.createElement("div");
            // // lblTitulo.innerHTML = '<label style="font-size:18px">Catálogo de productos</label>';
            // lblTitulo = document.createElement("label");'<label style="font-size:18px">Catálogo de productos</label>';
            // $('#productosCatalogo_wrapper .row ')[0].firstChild.append(lblTitulo);
        },
        "drawCallback": function (settings) {
 
            $("#productosCatalogo").LoadingOverlay("hide", true);
        },
    });
}

 

function listarProductosSugeridos(part_number, descripcion, type) {
    // console.log(part_number, descripcion, type);
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

        $('#productosSugeridos tbody').html('');
        $.ajax({
            type: 'POST',
            url: 'listarProductosSugeridos',
            data: {
                part_number: pn,
                descripcion: ds
            },
            success: function (response) {
                // console.log(response);
                if (response.length >0) {
                    listarSugeridos(response);
                }
            }
        });
    

}

function listarSugeridos(data) {
    // console.log(data);
            html = '';
            if (data.length > 0) {
                data.forEach(function (element) {
                    html += `
                    <tr>
                    <td>${element.codigo ?? ''}</td>
                    <td>${element.cod_softlink ?? ''}</td>
                    <td>${element.part_number ?? ''}</td>
                    <td>${element.marca ?? ''}</td>
                    <td>${element.descripcion ?? ''}</td>
                    <td>
                        <button type="button" class="btn btn-success btn-xs" title="Seleccionar Producto" 
                            data-codigo="${element.codigo}" data-id="${element.id_producto}" 
                            data-partnumber="${element.part_number}" data-descripcion="${encodeURIComponent(element.descripcion)}" 
                            data-abreviatura="${element.abreviatura}" data-series="${element.series}"
                            onclick="selectProductoAsignado(this);">
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

function selectProductoAsignado(obj) {
    // console.log(obj);
    let id = obj.dataset.id;
    let codigo = obj.dataset.codigo;
    let partnumber = obj.dataset.partnumber;
    let descripcion = obj.dataset.descripcion;
    let abreviatura = obj.dataset.abreviatura;
    let series = obj.dataset.series;
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
        det.control_series = series;
        // console.log('--------------');
        // console.log(det);
        // console.log(series_transformacion);
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
        det.control_series = serie;
        det.series = serie;

        $('#modal-mapeoAsignarProducto').modal('hide');
        mostrar_detalle();
        console.log(det);
    }

});

$("[name=id_clasif]").on('change', function () {
    var id_clasificacion = $(this).val();
    // console.log(id_clasificacion);
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
