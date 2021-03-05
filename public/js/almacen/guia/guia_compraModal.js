$(function(){
    $('#listaGuiasCompra tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaGuiasCompra').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var idPr = $(this)[0].childNodes[5].innerHTML;
        $('.modal-footer #mid_guia_com').text(id);
        $('.modal-footer #mid_guia_prov').text(idPr);
    });
});

function guia_compraModal(){
    // clearDataTable();
    let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
    if (formName =='guia_compra' || formName == 'transferencias'){
        $('#modal-guia_compra').modal({
            show: true
        });
        listarGuiasCompra();
    } 
    else if (formName =='doc_compra'){

        var id_proveedor = $('[name=id_proveedor]').val();
        if (id_proveedor !== null && id_proveedor !== '' && id_proveedor !== 0){
            $('#modal-guia_compra').modal({
                show: true
            });
            listarGuiasProveedor(id_proveedor);
        } else {
            alert('No ha ingresado un proveedor!');
        }
    }
}

function listarGuiasCompra(){
    var vardataTables = funcDatatables();
    $('#listaGuiasCompra').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'listar_guias_compra',
        'columns': [
            {'data': 'id_guia'},
            {'render':
                function (data, type, row){
                    return (row['serie']+'-'+row['numero']);
                }
            },
            {'data': 'razon_social'},
            {'data': 'almacen_descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'codigo'},
            {'data': 'des_estado'},
            {'data': 'id_proveedor'},
        ],
        'columnDefs': [{ 'aTargets': [0,7], 'sClass': 'invisible'}],
        'order': [[0, 'desc']]
    });
}

function llenarTablaListaGuiasCompra(data){
    
    var vardataTables = funcDatatables();
    $('#listaGuiasCompra').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'data':data,
        'columns': [
            {'data': 'id_guia'},
            {'data': 'razon_social'},
            {'render':
                function (data, type, row){
                    return (row['serie']+'-'+row['numero']);
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'des_estado'},
            {'data': 'id_proveedor'},
        ],
        'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
        'order': [[0, 'desc']]
    });
}

function listarGuiasProveedor(id_proveedor){

    $.ajax({
        type: 'GET',
        url:  `listar_guias_proveedor/${id_proveedor}`,
        dataType: 'JSON',
        success: function(response){
            llenarTablaListaGuiasCompra(response.data);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
 }

function selectGuiaCompra(){
    var myId = $('.modal-footer #mid_guia_com').text();
    var idPr = $('.modal-footer #mid_guia_prov').text();
    var page = $('.page-main').attr('type');

    if (page == "guia_compra"){
        var activeTab = $("#tab-guia_compra #myTab li.active a").attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        actualizar_tab(activeForm, myId, idPr);
    }
    else if (page == "doc_compra"){
        if (myId !== null && myId !== ''){
            agrega_guia(myId);
        }
    }
    else if (page == "transferencias"){
        if (myId !== null && myId !== ''){
            ver_transferencia(myId);
        }
    } 
    $('#modal-guia_compra').modal('hide');
}