$(function(){
    var vardataTables = funcDatatables();
    listarTablaProveedores();

    
});

function listarTablaProveedores(){
    var vardataTables = funcDatatables();
    $('#ListaProveedores').dataTable({
        'dom': 'frtip',
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': '/logistica/listar_proveedores',
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    $('#ListaProveedores').DataTable().on("draw", function(){
        resizeSide();
    });

    $('#ListaProveedores tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        }else{
            $('#ListaProveedores').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_proveedor').text(idTr);
    });
}

function selectValue(){
    var myId = $('.modal-footer #id_proveedor').text();
    
    $('#modal-lista-proveedores').modal('hide');
    // changeStateButton('historial');
    
    var activeTab = $('#tab-proveedores ul li.active a').attr('type');
    // var activeForm = "form-"+activeTab.substring(1);
    // actualizarForm(activeForm, myId);
    actualizarForm(myId);
}

// function actualizarForm(activeForm,myId){


    function limpiarTabla(idElement){
        var table = document.getElementById(idElement);
        for(var i = table.rows.length - 1; i > 0; i--)
        {
            table.deleteRow(i);
        }
        return null;
    }
    
 