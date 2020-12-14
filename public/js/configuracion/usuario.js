$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaUsuarios').dataTable({
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_usuarios',
        'columns': [
            {'data': 'id_usuario'},
            {'render':
            function (data, type, row, meta){
                return (row['nombre_completo_usuario']);
            }
            },
            {'data': 'usuario'},
            {'render':
            function (data, type, row, meta){
                return row['clave']?'<p></span>   <i class="fas fa-eye-slash" onmousedown="showPasswordUser(this,'+row['id_usuario']+');"  onmouseup="hiddenPasswordUser(this); "style="cursor:pointer;"></i> <span name="password">**********</p>':'';
            }
            },
            {'data': 'email'},
            {'render':
            function (data, type, row, meta){
                return row['rol']?row['rol']:'';
            }
            },
            {'data': 'fecha_registro'},
            {'render':
                function (data, type, row, meta){
                    return ('<div class=""><button type="button" class="btn bg-primary btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Editar" onclick="editarUsuario('+row['id_usuario']+');"><i class="fas fa-edit"></i></button>'+
                    '<button type="button" class="btn bg-olive btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Asignar Accesos" onclick="AccesoUsuario('+row['id_usuario']+');"><i class="fas fa-user-tag"></i></button></div>'
                    );
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [6, 'desc']
        ]
    });
    resizeSide();

    /* Seleccionar valor del DataTable */
    $('#listaTrabajadorUser tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaTrabajadorUser').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var nameTr = $(this)[0].childNodes[2].innerHTML;
        $('.modal-footer #idTr').text(idTr);
        $('.modal-footer #nameTr').text(nameTr);
    });

    $('#formPage').on('submit', function(){
        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar este registro?');

        if (ask == true){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: 'guardar_usuarios',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    if (response > 0){
                        alert('Se registro al usuario correctamente');
                        $('#formPage')[0].reset();
                        $('#listaUsuarios').DataTable().ajax.reload();
                    }else if (response == 'exist'){
                        alert('Ya existe usuario registrado para dicho trabajador');
                    }else{
                        alert('Error, inténtelo más tarde');
                    }
                }
            }).fail( function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
            return false;
        }else{
            return false;
        }
    });

    $('#todos').change(function(){
        if($(this).prop('checked') == true) {
            $('.check-okc').prop('checked', true);
        }else{
            $('.check-okc').prop('checked', false);
        }
    });
});

function editarUsuario(id){
    $('#modal-editar-usuario').modal({
        show: true,
        backdrop: 'static'
    });
}
function AccesoUsuario(id){
    $('#modal-asignar-accesos').modal({
        show: true,
        backdrop: 'static'
    });

}
function getPasswordUserDecode(id){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url: '/configuracion/usuario/password-user-decode/'+id,
            dataType: 'JSON',
            success(response) {
                resolve(response) // Resolve promise and go to then() 
            },
            error: function(err) {
            reject(err) // Reject the promise and go to catch()
            }
            });
        });
}

function showPasswordUser(obj,id){
    getPasswordUserDecode(id).then(function(res) {
        // Run this when your request was successful
        // console.log(res)
        if(res.status ==200){
            obj.className="fas fa-eye";
            obj.parentNode.children[1].innerText=res.data;
        }
    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })


}
function hiddenPasswordUser(obj){
    obj.className="fas fa-eye-slash";
    obj.parentNode.children[1].innerText="**********";
}

function crear_usuario(){
    $('.formularioUsu')[0].reset();
    $('.formularioUsu').attr('type', 'register');
    $('#modal-agregarUsuario').modal({
        show: true,
        backdrop: 'static'
    });
}

function modalTrabajadores(){
    $('#modal-trabajador').modal({
        show: true,
        backdrop: 'static'
    });
    listarTrabajador();
}

function selectValueTrab(){
    var myId = $('.modal-footer #idTr').text();
    var myName = $('.modal-footer #nameTr').text();
    $('[name=id_trabajador]').val(myId);
    $('[name=trab]').val(myName);
    $('#modal-trabajador').modal('hide');
}

function listarTrabajador(){
    var vardataTables = funcDatatables();
    $('#listaTrabajadorUser').dataTable({
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_trabajador',
        'columns': [
            {'data': 'id_trabajador'},
            {'data': 'nro_documento'},
            {'data': 'datos_trabajador'},
            {'data': 'empresa'}
        ]
    });
}

function deleteUser(id){
    var ask = confirm('¿Desea eliminar este registro');
    if (ask == true){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'anular_usuarios/' + id,
            success: function(response){
                if(response > 0){
                    alert('Usuario anulado exitosamente');
                    $('#listaUsuarios').DataTable().ajax.reload();
                }else{
                    alert('Error, inténtelo mas tarde');
                }
            }
        });
    }else{
        return false;
    }
}

function AccesosUser(id){
    $('#formAccess')[0].reset();
    $('#domAccess').empty();
    $('[name="id_usuario"]').val(id);
    $.ajax({
        type: 'GET',
        url: 'cargar_roles_usuario/' + id,
        dataType: 'JSON',
        success: function(response){
            $('[name=role]').html('<option value="0" selected disable>Elija una opcion</option>' + response);
            $('#modal-accesos').modal({show: true});
        }
    }).fail( function( jqXHR, textStatus, errorThrown ) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cargarAplicaciones(value){
    var user = $('[name=id_usuario]').val();
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'cargar_aplicaciones_mod/' + value + '/' + user,
        success: function(response){
            if (response.access > 0) {
                $('#domAccess').html(response.view);
                $('[name=id_acceso]').val(response.access);
            }else{
                $('#domAccess').html(response.view);
            }
        }
    });
}

function guardarAcceso(){
    var access = $('[name=id_acceso]').val();
    var user = $('[name=id_usuario]').val();
    var role = $('[name=role]').val();
    var modle = $('[name=modulo]').val();
    var obj = {}

    $(".check-okc").map(function(){
        var value = (this.checked ? 1 : 0);
        var name = this.name;
        obj[name] = value;
    });
    var objeto = JSON.stringify(obj);

    if (access > 0){
        baseUrl = 'editar_accesos';
        dataAccess = 'id_acceso=' + access + '&id_usuario=' + user + '&id_rol=' + role + '&id_modulo=' + modle + '&aplicaciones=' + objeto;
    }else{
        baseUrl = 'guardar_accesos';
        dataAccess = 'id_usuario=' + user + '&id_rol=' + role + '&id_modulo=' + modle + '&aplicaciones=' + objeto;
    }

    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: dataAccess,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                if (access > 0){
                    alert('Acceso editado con éxito');
                }else{
                    alert('Acceso asignado con éxito');
                }
                $('#modal-accesos').modal('hide');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    return false;
}