$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaServicio tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaServicio').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var code = $(this)[0].childNodes[1].innerHTML;
        var desc = $(this)[0].childNodes[2].innerHTML;
        // var unid = $(this)[0].childNodes[5].innerHTML;
        $('.modal-footer #id_servicio').text(idTr);
        $('.modal-footer #codigo').text(code);
        $('.modal-footer #descripcion').text(desc);
        // $('.modal-footer #unid_med').text(unid);
    });
});
function listarServicios(){
    var vardataTables = funcDatatables();
    $('#listaServicio').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        // 'processing': true,
        'ajax': 'listar_servicio',
        'columns': [
            {'data': 'id_servicio'},
            {'data': 'codigo'},
            // {'data': 'cod_antiguo'},
            {'data': 'descripcion'},
            // {'data': 'codigo_anexo'},
            // {'data': 'id_unidad_medida'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function servicioModal(){
    var page = $('.page-main').attr('type');
    console.log('page: '+page);
    if (page == 'transformacion'){
        var est = $('[name=cod_estado]').val();
        console.log('estado: '+est);
        if (est == '1'){
            $('#modal-servicio').modal({
                show: true
            });
            clearDataTable();
            listarServicios();
        } else {
            alert('La transformación ya fue procesada y/o anulada');
        }
    }
    else if (page == 'transformaciones'){
        $('#modal-servicio').modal({
            show: true
        });
        clearDataTable();
        listarServicios();
    }
}
function selectServicio(){
    var myId = $('.modal-footer #id_servicio').text();
    var code = $('.modal-footer #codigo').text();
    var desc = $('.modal-footer #descripcion').text();
    // var unid = $('.modal-footer #unid_med').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    if (form == undefined){
        var form = $('.page-main form[type=edition]').attr('id');
    }
    console.log('form:'+form);
    console.log('id:'+myId+' code:'+code+' desc:'+desc);

    if (page == "transformacion"){
        var acordion = $('#accordion .in')[0].id;
        console.log($('#accordion .in')[0].id);

        if (acordion == "collapseTwo"){//servicios directos
            guardar_directo(myId);
        }
        else if (acordion == "collapseThree"){//costos indirectos
            guardar_indirecto(myId);
        }
        else if (acordion == "collapseFour"){//sobrantes
            // guardar_materia(myId);
        }
        else if (acordion == "collapseFive"){//productos transformados
            // guardar_materia(myId);
        }
    }
    $('#modal-servicio').modal('hide');
}
