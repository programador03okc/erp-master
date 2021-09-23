
function openAsignarProducto(partnumber, desc, id, type) {

    $('#part_number').text(partnumber);
    $('#descripcion_producto').text(decodeURIComponent(desc));
    $('[name=id_detalle_requerimiento]').val(id);
    $('[name=part_number]').val(partnumber);
    $('[name=descripcion]').val(decodeURIComponent(desc));
    $('[name=id_tipo_producto]').val('');
    $('[name=id_categoria]').val('');
    $('[name=id_subcategoria]').val('');
    $('[name=id_clasif]').val(5);
    $('[name=id_unidad_medida]').val(1);
    $('[name=series]').iCheck('uncheck');

    listarProductosCatalogo();
    listarProductosSugeridos(partnumber, decodeURIComponent(desc), type);

    $('#modal-mapeoAsignarProducto').modal('show');
    $('[href="#seleccionar"]').tab('show');
    $('#submit_mapeoAsignarProducto').removeAttr('disabled');
}

$('#tab-productos .tab-content a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    let tab = $(e.target).attr("href") // activated tab
    console.log('tab: ' + tab);

    if (tab == '#seleccionar') {
        if ($('#productosSugeridos').length == 0) {
            $('#productosSugeridos').DataTable().ajax.reload();
            $('#productosCatalogo').DataTable().ajax.reload();
        }
    }
    else if (tab == '#crear') {
        // $('#listaComprobantes').DataTable().ajax.reload();
    }
});