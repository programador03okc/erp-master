$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaProducto tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaProducto").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var data = $('#listaProducto').DataTable().row($(this)).data();

        var sel = {
            'id_producto': data.id_producto,
            'part_number': data.part_number,
            'codigo': data.codigo,
            'descripcion': data.descripcion,
            'unid_med': data.abreviatura
        }
        if (origen == 'transformado') {
            agregar_producto_transformado(sel);
        }
        else if (origen == 'sobrante') {
            agregarCustomizacionSobrante(sel);
        }
        $("#modal-productoCatalogo").modal("hide");
    });
});

function listarProductosCatalogo() {
    var vardataTables = funcDatatables();
    console.log('pppp');
    $('#listaProducto').DataTable({
        dom: vardataTables[1],
        language: vardataTables[0],
        serverSide: true,
        ajax: {
            url: "mostrar_prods",
            type: "POST"
        },
        'columns': [
            { 'data': 'id_producto' },
            { 'data': 'codigo' },
            { 'data': 'cod_softlink' },
            { 'data': 'part_number' },
            { 'data': 'descripcion' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}