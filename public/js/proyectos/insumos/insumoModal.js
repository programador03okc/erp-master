$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaInsumo tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaInsumo').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;
        var tp = $(this)[0].childNodes[3].innerHTML;
        var unid = $(this)[0].childNodes[4].innerHTML;
        var prec = $(this)[0].childNodes[5].innerHTML;
        var idund = $(this)[0].childNodes[6].innerHTML;

        var filas = document.querySelectorAll('#AcuInsumos tbody tr');
        var existe = false;
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            if (colum[0].innerText == cod){
                existe = true;
            }
        });

        if (!existe){
            if (idund == 6){// si unidad = %mo
                var total = 0;
                filas.forEach(function(e){
                    var colum = e.querySelectorAll('td');
                    console.log(colum[2].innerText);
                    if (colum[2].innerText == 'MO'){
                        total += parseFloat(colum[7].innerText);
                    }
                });
                console.log('total'+total);
                $('[name=precio_unitario_cu]').val(total);
            } else {
                $('[name=precio_unitario_cu]').val(prec);
            }
            $('[name=id_insumo]').val(myId);
            $('[name=cod_insumo]').val(cod);
            $('[name=des_insumo]').val(des);
            $('[name=tp_insumo]').val(tp);
            $('[name=unidad]').val(unid);
            $('[name=id_unidad_medida]').val(idund);
            $('#modal-insumo').modal('hide');
        } else {
            alert('El insumo seleccionado ya existe en la Lista!');
        }
    });
});
function listarInsumos(){
    var vardataTables = funcDatatables();
    var table = $('#listaInsumo').DataTable({
        'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'buttons': [
            {
                text: "Crear Insumo",
                className: 'btn btn-warning',
                action: function(){
                    open_insumo_create();
                }
            }
        ],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_insumos',
        'columns': [
            {'data': 'id_insumo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cod_tp_insumo'},
            {'data': 'abreviatura'},
            {'render': 
                function (data, type, row){
                    if (row['precio_insumo'] !== null){
                        return row['precio_insumo'];
                    } else {
                        return row['precio'];
                    }
                }
            },
            // {'data': 'precio_insumo'},
            {'data': 'unid_medida'},
        ],
        'columnDefs': [{ 'aTargets': [0,6], 'sClass': 'invisible'}],
        'initComplete': function () {
            $('#listaInsumo_filter label input').focus();
        }
    });
}

function insumoModal(){
    $('#modal-insumo').modal({
        show: true
    });
    listarInsumos();
}
