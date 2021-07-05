function listarProductosCatalogo(){
    var vardataTables = funcDatatables();
    $('#productosCatalogo').dataTable({
        // 'dom': vardataTables[1],
        'language' : vardataTables[0],
        // 'processing': true,
        'bDestroy' : true,
        'ajax': 'mostrar_prods',
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success btn-sm" name="btnSeleccionarUbigeo" title="Seleccionar Producto" 
                            data-codigo="${row.codigo}" data-id="${row.id_producto}" 
                            data-partnumber="${row.part_number}" data-descripcion="${row.descripcion}" 
                            onclick="selectProducto(this);">
                            <i class="fas fa-check"></i>
                            </button>
                        </div>
                        `;
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listarProductosSugeridos(part_number){
    var vardataTables = funcDatatables();
    $('#productosSugeridos').dataTable({
        // 'dom': vardataTables[1],
        'language' : vardataTables[0],
        // 'processing': true,
        'bDestroy' : true,
        'ajax': 'mostrar_prods_sugeridos/'+part_number,
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success btn-sm" name="btnSeleccionarUbigeo" title="Seleccionar Producto" 
                            data-codigo="${row.codigo}" data-id="${row.id_producto}" 
                            data-partnumber="${row.part_number}" data-descripcion="${row.descripcion}" 
                            onclick="selectProducto(this);">
                            <i class="fas fa-check"></i>
                            </button>
                        </div>
                        `;
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function selectProducto(obj){
    let id = obj.dataset.id;
    let codigo = obj.dataset.codigo;
    let partnumber = obj.dataset.partnumber;
    let descripcion = obj.dataset.descripcion;
    let id_detalle = $('[name=id_detalle_requerimiento]').val();

    let page = document.getElementsByClassName('page-main')[0].getAttribute('type');
    
    if (page == 'mapeoProductos'){
        let det = detalle.find(element => element.id_detalle_requerimiento==id_detalle);
        console.log(det);
        det.id_producto = id;
        det.codigo = codigo;
        det.part_number = partnumber;
        det.descripcion = descripcion;
        $('#modal-mapeoAsignarProducto').modal('hide');
        mostrar_detalle();
    }
}

$("#form-crear").on("submit", function(e){

    e.preventDefault();
    // var data = $(this).serialize();
    let id_cat = $('[name=id_categoria]').val();
    let id_subcat = $('[name=id_subcategoria]').val();
    let id_clasif = $('[name=id_clasif]').val();
    let id_unid = $('[name=id_unidad_medida]').val();
    let partnumber = $('[name=part_number]').val();
    let descripcion = $('[name=descripcion]').val();
    let id_detalle = $('[name=id_detalle_requerimiento]').val();

    let page = document.getElementsByClassName('page-main')[0].getAttribute('type');
    
    if (page == 'mapeoProductos'){
        let det = detalle.find(element => element.id_detalle_requerimiento==id_detalle);
        console.log(det);
        det.id_producto = null;
        det.codigo = '';
        det.part_number = partnumber;
        det.descripcion = descripcion;
        det.id_categoria = id_cat;
        det.id_subcategoria = id_subcat;
        det.id_clasif = id_clasif;
        det.id_unidad_medida = id_unid;

        $('#modal-mapeoAsignarProducto').modal('hide');
        mostrar_detalle();
    }
});
