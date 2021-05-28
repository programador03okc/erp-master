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

function listaTrabajadoresModal(){
    $('#modal-lista-trabajadores').modal({
        show: true
    });
    listarTrabajadores();
}

function listarTrabajadores(){
    var vardataTables = funcDatatables();
    $('#listaTrabajadores').dataTable({
        'dom': vardataTables[1],
        'buttons': [],
        'language' : vardataTables[0],
        'lengthChange': false,

        'bDestroy': true,
        'ajax': 'listar_trabajadores',
        'columns': [
            {'data': 'id_trabajador'},
            {'data': 'nro_documento'},
            {'data': 'nombre_trabajador'},
            {'render':
                function (data, type, row){
                    let action = `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success btn-sm" name="btnSeleccionarTrabajador" title="Seleccionar trabajador" 
                            data-id-trabajador="${row.id_trabajador}"
                            data-nombre-trabajador="${row.nombre_trabajador}"
                            data-nro-documento="${row.nro_documento_trabajador}"
                            onclick="selectTrabajador(this);">
                            <i class="fas fa-check"></i>
                            </button>
                        </div>
                        `;
            
                    return action;
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function selectTrabajador(obj){
    let idTrabajador= obj.dataset.idTrabajador;
    let nombreTrabajador= obj.dataset.nombreTrabajador;
    document.querySelector("form[id='form-requerimiento'] input[name='id_trabajador']").value =idTrabajador;
    document.querySelector("form[id='form-requerimiento'] input[name='nombre_trabajador']").value =nombreTrabajador;
    $('#modal-lista-trabajadores').modal('hide');
}