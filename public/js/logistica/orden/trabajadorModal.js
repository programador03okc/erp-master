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

function trabajadoresModal(numero_persona_autorizada){
    document.querySelector("div[id='modal-trabajadores'] label[id='numero_persona_autorizada']").textContent=numero_persona_autorizada;

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
            {'data': 'nombre_trabajador'},
            {'render':
                function (data, type, row){
                    let action = `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success btn-sm" name="btnSeleccionarTrabajador" title="Seleccionar trabajador" 
                            data-id-trabajador="${row.id_trabajador}"
                            data-nombre-trabajador="${row.nombre_trabajador}"
                            data-nro-documento="${row.nro_documento}"
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
    let nroDocumento= obj.dataset.nroDocumento;
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nro_documento_trabajador']").value =nroDocumentoTrabajador;

    switch (document.querySelector("div[id='modal-trabajadores'] label[id='numero_persona_autorizada']").textContent) {
        case '1':
            document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_1']").value =idTrabajador;
            document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_1']").value =nombreTrabajador+(nroDocumento.length >=8?(' (DNI:'+nroDocumento+')'):'');
            break;
    
        case '2':
            document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_2']").value =idTrabajador;
            document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_2']").value =nombreTrabajador+(nroDocumento >=9?('  (DNI:'+nroDocumento+')'):'');
            break;
    
        default:
            break;
    }

    
    $('#modal-trabajadores').modal('hide');
}