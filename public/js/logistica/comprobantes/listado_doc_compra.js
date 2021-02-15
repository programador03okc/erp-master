$(function(){
 
    listar_doc_compra();
});

function listar_doc_compra(){
    $.ajax({
        type: 'GET',
        url: 'listar_docs_compra/',
        dataType: 'JSON',
        success: function(response){
            if(response.data.length >0){
                llenarTablaListaComprobanteCompra(response.data);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });   
}

function llenarTablaListaComprobanteCompra(data){

    var vardataTables = funcDatatables();
    $('#listaComprobantesCompra').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'data':data,
        'buttons': [
            'excel','print'
        ],
        'columns': [
            {'render':
                function (data, type, row,meta){
                    return meta.row +1;
                }
            },
            {'data': 'serie'},
            {'data': 'numero'},
            {'data': 'tipo_documento'},
            {'data': 'fecha_emision'},
            {'data': 'condicion_pago'},
            {'data': 'razon_social'},
            {'data': 'fecha_vcmto'},
            {'data': 'moneda'},
            {'data': 'total_a_pagar'},
            {'data': 'des_estado'},
            {'render':
            function (data, type, row){
            return `<div class="btn-group" role="group">
                        <button type="button" class="btn btn-danger btn-xs" name="btnAnularComprobanteCompra" title="Anular" data-id-doc-com="${row.id_doc_com}" onclick="anularComprobanteCompra(this);">
                            <i class="fas fa-trash fa-sm"></i>
                        </button>
                    </div>`;
            }
            },
        ],
        
        // 'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
    });
}

function anularComprobanteCompra(obj){
    let id_doc_com = obj.dataset.idDocCom;
    anular_doc_compra(id_doc_com);
}