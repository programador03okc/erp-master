$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaInsumoPrecios tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaInsumoPrecios').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');

        // if (formName =='acu'){
            var precio = $(this)[0].firstChild.innerHTML;
            // var prec = $(this)[0].childNodes[3].innerHTML;
            $('[name=precio_unitario_cu]').val(precio);
            calculaPrecioTotalCU();
        // }
        $('#modal-insumo_precio').modal('hide');
    });
});

function listarInsumoPrecios(id_insumo){
    var vardataTables = funcDatatables();
    var table = $('#listaInsumoPrecios').DataTable({
        'dom': vardataTables[1],
        'buttons': [],
        // 'buttons': [
        //     {
        //         text: "Crear Precio",
        //         className: 'btn btn-warning',
        //         action: function(){
        //             open_precio_create();
        //         }
        //     }
        // ],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_insumo_precios/'+id_insumo,
        'columns': [
            {'data': 'precio_unit'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'render':
                function (data, type, row){
                    return (formatNumber.decimal(row['precio_unit'],'S/.',-4));
                },'class': 'resaltar right'
            },
            {'data': 'descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [[ 2, "asc" ]],
        'initComplete': function () {
            $('#listaInsumoPrecios_filter label input').focus();
        }
    });
}

function insumoPrecioModal(){
    var id_insumo = $('[name=id_insumo]').val();
    if (id_insumo !== ''){
        $('#modal-insumo_precio').modal({
            show: true
        });
        listarInsumoPrecios(id_insumo);
    } else {
        alert('Debe ingresar un Insumo!');
    }
}

function open_precio_modal(id_insumo){
    if (id_insumo !== ''){
        $('#modal-insumo_precio').modal({
            show: true
        });
        listarInsumoPrecios(id_insumo);
    } else {
        alert('Debe seleccionar un Insumo!');
    }
}

function open_precio_create(){
    var precio = prompt('Ingrese el precio:');
    console.log(Number.isInteger(parseInt(precio)));

    if (Number.isInteger(parseInt(precio))){
        var id_insumo = $('[name=id_insumo]').val();
        var data = 'precio='+precio+'&id_insumo='+id_insumo;
        console.log(data);
        $.ajax({
            type: 'POST',
            url: 'guardar_precio',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Precio registrado con éxito');
                    $('#listaInsumoPrecios').DataTable().ajax.reload();
                    // $('#modal-insumo_create').modal('hide');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Sólo puede ingresar números!');
    }
}