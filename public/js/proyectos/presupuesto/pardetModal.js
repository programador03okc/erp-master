$(function(){
    $('#listaParDet tbody').on("click","tr", function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        }else{
            $('#listaParDet').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        console.log('click tr'+id);
        guardar_partida(id);
        $('#modal-pardet').modal('hide');
        // var des = $(this)[0].childNodes[1].innerHTML;
        // $('.modal-footer #id_pardet').text(id);
        // $('[name=id_pardet]').val(id);
        // $('[name=des_pardet]').val(des);
    });
});

function listarParDet(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaParDet').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        // 'retrieve': true,
        'ajax': 'listar_par_det',
        'columns': [
            {'data': 'id_pardet'},
            {'data': 'descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'initComplete': function () {
            $('#listaInsumo_filter label input').focus();
        }
    });
}

function pardetModal(cod_padre){
    console.log('cod_padre: '+cod_padre)
    $('#modal-pardet').modal({
        show: true
    });
    var page = $('.page-main').attr('type');
    console.log('page: '+page);
    var i = 1;
    if (page == "presint"){
        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[5].innerText;
            var imp = colum[2].innerText;
            console.log('padre:'+padre);
            console.log('2 :'+imp);
            if (padre == cod_padre && imp !== ''){
                i++;
            }
        });
    }
    else if (page == "propuesta"){
        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[8].innerText;
            var imp = colum[3].innerText;
            console.log('padre:'+padre);
            console.log('3 :'+imp);
            if (padre == cod_padre && imp !== ''){
                i++;
            }
        });
    }
    else if (page == "presEstructura"){
        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[5].innerText;
            var imp = colum[3];
            console.log('padre:'+padre);
            console.log('3 :'+imp);
            if (padre == cod_padre && imp !== ''){
                i++;
            }
        });
    }
    console.log('i: '+i);
    $('[name=codigo]').val(cod_padre+'.'+leftZero(2,i));
    $('[name=cod_padre]').val(cod_padre);
    listarParDet();
}

function selectParDet(){
    var id = $('.modal-footer #id_pardet').text();
    console.log('click tr'+id);
    guardar_partida(id);
    $('#modal-pardet').modal('hide');
}