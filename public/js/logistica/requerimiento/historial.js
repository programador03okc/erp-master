 

function inicializarSelect(){
    listar_almacenes();
    listar_sedes();

}

function selectRequerimiento(){
    // console.log("selectRequerimiento");
    var id = $('#id_requerimiento').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    
    if (page=='transferencias'){
        ver_requerimiento(id);
    } else {
        inicializarSelect();
        clearForm(form); //function.js
        changeStateButton('historial'); //init.js
        mostrar_requerimiento(id); // mostrar.js

        var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
        disabledControl(btnTrazabilidadRequerimiento,false);
    }
        // console.log($(":file").filestyle('disabled'));
    $('#modal-requerimiento').modal('hide');
}

$('#listaRequerimiento tbody').on('click', 'tr', function(){
    if ($(this).hasClass('eventClick')){
        $(this).removeClass('eventClick');
    } else {
        $('#listaRequerimiento').dataTable().$('tr.eventClick').removeClass('eventClick');
        $(this).addClass('eventClick');
    }
    var idTr = $(this)[0].firstChild.innerHTML;
    $('.modal-footer #id_requerimiento').text(idTr);
    
});