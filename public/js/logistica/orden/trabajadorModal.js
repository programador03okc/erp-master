$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaTrabajadores tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaTrabajadores').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var doc = $(this)[0].childNodes[1].innerHTML;
        var nom = $(this)[0].childNodes[2].innerHTML;
 
  
        $('.modal-footer #select_id_trabajador').text(idTr);
        $('.modal-footer #select_nro_documento_trabajador').text(doc);
        $('.modal-footer #select_nombre_trabajador').text(nom);
        
      });
});

function trabajadoresModal(){
    var page = $('.page-main').attr('type');
    // console.log(page);
    if(page =='crear-orden-requerimiento'){
            $('#modal-trabajadores').modal({
                show: true
            });

            listarTrabajadores();
    }
    
}

function listarTrabajadores(){
    var vardataTables = funcDatatables();
    $('#listaTrabajadores').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_trabajadores',
        'columns': [
            {'data': 'id_trabajador'},
            {'data': 'nro_documento'},
            {'data': 'nombre_trabajador'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function selectTrabajador(){
    var id = $('.modal-footer #select_id_trabajador').text();
    var doc = $('.modal-footer #select_nro_documento_trabajador').text();
    var nom = $('.modal-footer #select_nombre_trabajador').text();

    var page = $('.page-main').attr('type');
    // console.log('page: '+page);
    
    var page = $('.page-main').attr('type');
    // console.log(page);
    if(page =='crear-orden-requerimiento'){

        $('[name=id_trabajador]').val(id);
        $('[name=nombre_persona_autorizado]').val(nom);
        $('[name=nro_documento_trabajador]').val(doc);   
     }

    else {
        
        $('[name=id_trabajador]').val(id);
        $('[name=nomre_trabajador]').val(nom);
        $('[name=nro_documento_trabajador]').val(doc);   
   
 
    }
    
    $('#modal-trabajadores').modal('hide');
}