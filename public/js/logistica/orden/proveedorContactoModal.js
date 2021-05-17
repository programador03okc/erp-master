$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaContactosProveedor tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaContactosProveedor').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var nom = $(this)[0].childNodes[1].innerHTML;
        var car = $(this)[0].childNodes[2].innerHTML;
        var tel = $(this)[0].childNodes[3].innerHTML;
        var em = $(this)[0].childNodes[4].innerHTML;
        var dir = $(this)[0].childNodes[5].innerHTML;
  
        $('.modal-footer #select_id_contacto').text(idTr);
        $('.modal-footer #select_nombre_contacto').text(nom);
        $('.modal-footer #select_cargo_contacto').text(car);
        $('.modal-footer #select_telefono_contacto').text(tel);
        $('.modal-footer #select_email_contacto').text(em);
        $('.modal-footer #select_direccion_contacto').text(dir);
      });
});

function contactoModal(){
    var page = $('.page-main').attr('type');
    // console.log(page);
    if(page =='crear-orden-requerimiento'){
        let id_proveedor = document.querySelector("input[name='id_proveedor']").value;
        if(id_proveedor>0){
            $('#modal-contacto-proveedor').modal({
                show: true
            });

            listarContactosProveedor(id_proveedor);

        }else{
            alert("Antes debe seleccione un proveedor");
        }

    }
    
}

function listarContactosProveedor(id_proveedor){
    var vardataTables = funcDatatables();
    $('#listaContactosProveedor').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'lista_contactos_proveedor/'+id_proveedor,
        'columns': [
            {'data': 'id_contacto'},
            {'data': 'nombre_contacto'},
            {'data': 'cargo_contacto'},
            {'data': 'telefono_contacto'},
            {'data': 'email_contacto'},
            {'data': 'direccion_contacto'},
            {'render':
                function (data, type, row){
                    let action = `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success btn-sm" name="btnSeleccionarContactoProveedor" title="Seleccionar contacto de proveedor" 
                            data-id-contacto="${row.id_contacto}"
                            data-nombre-contacto="${row.nombre_contacto}"
                            data-cargo-contacto="${row.cargo_contacto}"
                            data-telefono-contacto="${row.telefono_contacto}"
                            data-email-contacto="${row.email_contacto}"
                            data-direccion-contacto="${row.direccion_contacto}"
                            onclick="selectContactoProveedor(this);">
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

function selectContactoProveedor(obj){

    let idContacto= obj.dataset.idContacto;
    let nombreContacto= obj.dataset.nombreContacto;
    let cargoContacto= obj.dataset.cargoContacto;
    let telefonoContacto= obj.dataset.telefonoContacto;
    let emailContacto= obj.dataset.emailContacto;
    let direccionContacto= obj.dataset.direccionContacto;

    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value =idContacto;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value =nombreContacto;;
    // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_cargo']").value =cargoContacto;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value =telefonoContacto;
    // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_email']").value =emailContacto;
    // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_direccion']").value =direccionContacto;

    
    $('#modal-contacto-proveedor').modal('hide');
}