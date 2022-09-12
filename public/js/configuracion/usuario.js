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
                return (row['nombre_corto']);
            }
            },
            {'data': 'usuario'},
            {'render':
            function (data, type, row, meta){
                return row['clave']?'<p></span>   <i class="fas fa-eye-slash" onmousedown="showPasswordUser(this,'+row['id_usuario']+');"  onmouseup="hiddenPasswordUser(this); "style="cursor:pointer;"></i> <span name="password">**********</p>':'';
            }
            },
            {'data': 'email'},
            // {'render':
            // function (data, type, row, meta){
            //     return row['rol']?row['rol']:'';
            // }
            // },
            {'data': 'fecha_registro'},
            {'render':
                function (data, type, row, meta){
                    return (`<div class="d-flex">
                            <button type="button" class="btn bg-primary btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Editar clave" data-calve="change-clave" data-id="${row['id_usuario']}">
                                <i class="fas fa-key"></i>
                            </button>
                            <a class="btn btn-default btn-flat botonList" data-toggle="tooltip" data-placement="bottom"  data-id="${row['id_usuario']}" href="accesos/${row['id_usuario']}">
                                <i class="fas fa-user-cog text-black"></i>
                            </a>

                            <button type="button" class="btn bg-orange btn-flat botonList" data-toggle="tooltip"
                                data-placement="bottom" title="Editar" onclick="editarUsuario(${row['id_usuario']});">
                                <i class="fas fa-edit"></i></button>

                            <button type="button" class="btn bg-red btn-flat botonList" data-toggle="tooltip"
                                data-placement="bottom" title="Anular" onclick="anularUsuario(${row['id_usuario']});">
                                <i class="fas fa-trash-alt"></i></button>
                            </div>`
                    );
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [0, 'desc']
        ]
    });
    resizeSide();
    // del boton

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

    $('#formPage').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        // var ask = confirm('¿Desea guardar este registro?');
        Swal.fire({
            title: 'Nuevo usuario',
            text: "¿Esta seguro de guardar?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'no'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'guardar_usuarios',
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        if (response.status===200) {
                            Swal.fire({
                                title: 'Éxito',
                                text: "Se guardo con éxito",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Si'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            })
                        }
                        // if (response > 0){
                        //     alert('Se registro al usuario correctamente');
                        //     $('#formPage')[0].reset();
                        //     $('#listaUsuarios').DataTable().ajax.reload();
                        //     $('#modal-agregarUsuario').modal('hide');
                        // }else if (response == 'exist'){
                        //     alert('Ya existe usuario registrado para dicho trabajador');
                        // }else{
                        //     alert('Error, inténtelo más tarde');
                        // }
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        })

    });

    $('#todos').change(function(){
        if($(this).prop('checked') == true) {
            $('.check-okc').prop('checked', true);
        }else{
            $('.check-okc').prop('checked', false);
        }
    });
});

$(document).on('submit','[data-form="actualizar-usuario"]',function (e) {
    e.preventDefault();
    var data = $(this).serialize();

    $.ajax({
        type: 'POST',
        url:'/configuracion/usuario/perfil',
        data: data,
        beforeSend: function(){
        },
        success: function(response){
            console.log(response);
        }
    });
});

function getPerfilUsuario(id){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url:'/configuracion/usuario/perfil' +'/'+id,
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
function loadPerfilUsuario(id){
    getPerfilUsuario(id).then(function(res) {
        if(res.status ==200){
            $('#modal-editar-usuario [name="nro_documento"]').val(res.data.nro_documento);
            $('#modal-editar-usuario [name="nombres"]').val(res.data.nombres);
            $('#modal-editar-usuario [name="apellido_paterno"]').val(res.data.apellido_paterno);
            $('#modal-editar-usuario [name="apellido_materno"]').val(res.data.apellido_materno);
            $('#modal-editar-usuario [name="fecha_nacimiento"]').val(res.data.fecha_nacimiento);
            if (res.data.sexo==='M') {
                $('#modal-editar-usuario [name="sexo"][value="M"]').attr('checked',true);
            }else{
                $('#modal-editar-usuario [name="sexo"][value="F"]').attr('checked',true);
            }
            $('#modal-editar-usuario [name="id_estado_civil"] [value="'+res.data.id_estado_civil+'"]').attr('selected',true);
            $('#modal-editar-usuario [name="telefono"]').val(res.data.telefono);
            $('#modal-editar-usuario [name="direccion"]').val(res.data.direccion);
            $('#modal-editar-usuario [name="email"]').val(res.data.email);
            $('#modal-editar-usuario [name="brevette"]').val(res.data.brevette);
            $('#modal-editar-usuario [name="pais"] [value="'+res.data.id_pais+'"]').attr('selected',true);
            $('#modal-editar-usuario [name="ubigeo"]').val(res.data.ubigeo);
            $('#modal-editar-usuario [name="id_tipo_trabajador"] [value="'+res.data.id_tipo_trabajador+'"]').attr('selected',true);
            $('#modal-editar-usuario [name="id_categoria_ocupacional"] [value="'+res.data.id_categoria_ocupacional+'"]').attr('selected',true);
            $('#modal-editar-usuario [name="id_tipo_planilla"] [value="'+res.data.id_tipo_planilla+'"]').attr('selected',true);
            $('#modal-editar-usuario [name="condicion"]').val(res.data.condicion);
            $('#modal-editar-usuario [name="hijos"]').val(res.data.hijos);
            $('#modal-editar-usuario [name="id_pension"]').val(res.data.id_pension);
            $('#modal-editar-usuario [name="cuspp"]').val(res.data.cuspp);
            $('#modal-editar-usuario [name="seguro"]').val(res.data.seguro);
            if (res.data.confianza===false) {
                $('#modal-editar-usuario [name="confianza"][value="f"]').attr('checked',true);
            }else{
                $('#modal-editar-usuario [name="confianza"][value="t"]').attr('checked',true);
            }
            $('#modal-editar-usuario [name="usuario"]').val(res.data.usuario);
            $('#modal-editar-usuario [name="nombre_corto"]').val(res.data.nombre_corto);
            $('#modal-editar-usuario [name="codvent_softlink"]').val(res.data.codvend_softlink);
            $.each(res.data.usuario_grupo, function (index, element) {
                $('#modal-editar-usuario [name="id_grupo[]"] option[value="'+element.id_grupo+'"]').attr('selected',true);
            });
            $.each(res.data.usuario_rol, function (index, element) {
                $('#modal-editar-usuario [name="id_rol[]"] option[value="'+element.id_rol+'"]').attr('selected',true);
            });
        }
    }).catch(function(err) {
        console.log(err)
    })
}

function editarUsuario(id){
    $('#modal-editar-usuario').modal({
        show: true,
        backdrop: 'static'
    });
    $('#modal-editar-usuario input[name="id_usuario"]').val(id)
    loadPerfilUsuario(id);
}



function updateObjAccesoUsuario(id_accion,valor){
    let updateRegister=false;
    if(acccesoUsuario.length >0){
        acccesoUsuario.forEach((element,index) => {
            if(element.id_accion ==id_accion ){
                acccesoUsuario[index].valor=valor;
                updateRegister=true;
            }
        });
        if(updateRegister==false){
            addObjAccesoUsuario(id_accion,valor);
        }
    }else{
        addObjAccesoUsuario(id_accion,valor);
    }
}

function addObjAccesoUsuario(id_accion,valor){
    acccesoUsuario.push(
        {
            'id_accion':id_accion,
            'valor':valor
        }
    )
}


function anularUsuario(id){
    var rspta = confirm('¿Está seguro que desea anular éste usuario?');

    if (rspta){
        $.ajax({
            type: 'GET',
            url: 'anular_usuario/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                alert('El usuario ha sido anulado');
                $('#listaUsuarios').DataTable().ajax.reload();
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
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

// function modalTrabajadores(){
//     $('#modal-trabajador').modal({
//         show: true,
//         backdrop: 'static'
//     });
//     listarTrabajador();
// }

// function selectValueTrab(){
//     var myId = $('.modal-footer #idTr').text();
//     var myName = $('.modal-footer #nameTr').text();
//     $('[name=id_trabajador]').val(myId);
//     $('[name=trab]').val(myName);
//     $('#modal-trabajador').modal('hide');
// }

// function listarTrabajador(){
//     var vardataTables = funcDatatables();
//     $('#listaTrabajadorUser').dataTable({
//         'language' : vardataTables[0],
//         "processing": true,
//         "bDestroy": true,
//         'ajax': 'listar_trabajador',
//         'columns': [
//             {'data': 'id_trabajador'},
//             {'data': 'nro_documento'},
//             {'data': 'datos_trabajador'},
//             {'data': 'empresa'}
//         ]
//     });
// }

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
$(document).on('click','[data-calve="change-clave"]',function () {

    // $('#modal_cambio_clave').modal('show');
    $('#modal_cambio_clave [name="id_usuario"]').val($(this).attr('data-id'));
    $('#modal_cambio_clave').modal({
        show: true,
        backdrop: 'static'
    });
});
$(document).on('submit','[data-form="cambio-clave"]',function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    if ($(this).find('input[name="nueva_clave"]').val()===$(this).find('input[name="repetir_clave"]').val()) {
        Swal.fire({
            title: '¿Está seguro de cambiar la contraseña?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: 'cambiar-clave',
                        data: data,
                        dataType: 'JSON',
                        success: function(response){
                            console.log(response);
                        }
                    }).fail( function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    })
            },
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Éxito',
                    text: "Se actualizo su clave con éxito",
                    icon: 'sucess',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                })
            }
        })
    }else{
        Swal.fire({
            title: 'Error',
            text: "La clave no coincide, verifique que sean iguales por favor",
            icon: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {

            }
        })
    }
});

