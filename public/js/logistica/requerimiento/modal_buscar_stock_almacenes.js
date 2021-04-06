function buscarStockEnAlmacenesModal(id_item){
    $('#modal-buscar-stock-almacenes').modal({
        show: true,
        backdrop: 'true'
    });

    const item = data_item.filter(item => item.id_item == id_item);
    document.querySelector("div[id='modal-buscar-stock-almacenes'] span[id='nombre_producto']").textContent = item[0].des_item;

    llenarListaAlmacenesConStock(id_item);
}

function llenarListaAlmacenesConStock(id_item){
    var vardataTables = funcDatatables();

    $('#listaAlmacenesConStock').dataTable({
        "order": [[ 0, "asc" ]],
        'dom': vardataTables[1],
        'buttons': [
        ],
        'language' : vardataTables[0],
        'serverSide' : false,
        'bInfo': false,
        "bLengthChange" : false,
        "scrollCollapse": true,
        'paging': false,
        'searching': false,
        'bDestroy' : true,
        'ajax': 'buscar-stock-almacenes/'+id_item,
        'columns': [
            {'render':
                function (data, type, row, meta){
                    return meta.row+1;
                }
            },
            {'data': 'descripcion'},
            {'data': 'codigo'},
            {'render':
            function (data, type, row, meta){
                return `<div class="btn-group btn-group-xs  " role="group" aria-label="Second group">
                                <button type="button" class="btn btn-success btn-xs" name="btnReservar" data-toggle="tooltip" title="Reservar" onclick="reservarProducto(this, ${meta.row});"><i class="fas fa-clipboard-check"></i></button>
                        </div>`;
            }
        }
        ],
        'columnDefs': [
 
    ],
    });
}