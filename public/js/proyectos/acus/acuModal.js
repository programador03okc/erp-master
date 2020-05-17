$(function(){
    $('#listaAcus tbody').on("click","tr", function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaInsumo').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;
        
        let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');    
        if (formName =='presint' || formName =='preseje'){
            var msj = '';
            document.querySelectorAll('#listaAcusCD tbody tr').forEach(function(e){
                var colum = e.querySelectorAll('td');
                var nombre = colum[2].innerText;
                if (des == nombre){
                    msj = 'Ya existe dicha partida registrada';
                }
            });
            if (msj.length > 0){
                alert(msj);
            } else {
                $('[name=id_cu]').val(myId);
                $('[name=cod_acu]').val(cod);
                $('[name=des_acu]').val(des);
                $('#modal-acu').modal('hide');    
            }
        } else {
            $('[name=id_cu]').val(myId);
            $('[name=cod_acu]').val(cod);
            $('[name=des_acu]').val(des);
            $('#modal-acu').modal('hide');
        }
    });
});
function listarAcus(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaAcus').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'listar_cus',
        'columns': [
            {'data': 'id_cu'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cat_descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        // 'initComplete': function () {
        //     $('#listaAcu_filter label input').focus();
        // }
    });

}
function acuModal(){
    $('#modal-acu').modal({
        show: true
    });
    // $('.dataTable tbody tr').removeClass('eventClick');
    // $('.modal-footer label').text('');
    $('#listaAcus').dataTable().fnDestroy();
    listarAcus();
}
