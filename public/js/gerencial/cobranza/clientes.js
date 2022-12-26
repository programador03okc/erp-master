$(document).ready(function () {
    listarRegistros();
});
function listarRegistros() {
    var vardataTables = funcDatatables();
        tableRequerimientos = $("#listar-clientes").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        lengthChange: false,
        dom: vardataTables[1],
        buttons:[],
        ajax: {
            url: "clientes",
            type: "POST",
            // data:filtros,
            beforeSend: data => {
                $("#listar-clientes").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        columns: [
            {data: 'id_contribuyente', name:"id_contribuyente"},
            {data: 'nro_documento', name:"nro_documento"},
            {data: 'razon_social', name:"razon_social"},
            {
                render: function (data, type, row) {
                    html='';
                        html+='<button type="button" class="btn btn-warning btn-flat botonList editar-registro" data-toggle="tooltip" title="Editar" data-original-title="Editar" data-id-contribuyente="'+row['id_contribuyente']+'"><i class="fas fa-edit"></i></button>';
                        html+='<button type="button" class="btn btn-danger btn-flat botonList eliminar-registro" data-toggle="tooltip" title="Eliminar" data-original-title="Anular" data-id-contribuyente="'+row['id_contribuyente']+'"><i class="fas fa-trash"></i></button>';

                    html+='';
                    return html;
                },
                className: "text-center"
            }
        ],
        order: [[0, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        "drawCallback": function (settings) {

            $("#listar-clientes").LoadingOverlay("hide", true);
        }
    });
}
$(document).on('click','[data-action="nuevo-cliente"]',function () {
    $('#nuevo-cliente').modal('show');
    $('[data-form="guardar-cliente"]')[0].reset();
});
$(document).on('change','[data-select="departamento-select"]',function () {
    var id_departamento = $(this).val()
        this_select = $(this).closest('div.modal-body').find('div [name="provincia"]'),
        html='';

    if (id_departamento!==null && id_departamento!=='') {
        $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'provincia/'+id_departamento,
            data: {},
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response.status===200) {
                    html='<option value=""> Seleccione...</option>';
                    $.each(response.data, function (index, element) {
                        html+='<option value="'+element.id_prov+'">'+element.descripcion+'</option>'
                    });
                    // console.log(this_select);
                    // $('[data-form="guardar-cliente"] [name="provincia"]').html(html);
                    this_select.html(html);
                }else{
                    this_select.html(html);
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    }else{
        this_select.html('<option value=""> Seleccione...</option>');
        $(this).closest('div.modal-body').find('div [name="distrito"]').html('<option value=""> Seleccione...</option>');
    }

});
$(document).on('change','[data-select="provincia-select"]',function () {
    var id_provincia = $(this).val(),
        this_select = $(this).closest('div.modal-body').find('div [name="distrito"]'),
        html='';

    if (id_provincia!==null && id_provincia!=='') {
        $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: 'distrito/'+id_provincia,
            data: {},
            dataType: 'JSON',
            success: function(response){
                if (response.status===200) {
                    html='<option value=""> Seleccione...</option>';
                    $.each(response.data, function (index, element) {
                        html+='<option value="'+element.id_dis+'">'+element.descripcion+'</option>'
                    });
                    this_select.html(html);
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    } else {
        this_select.html('<option value=""> Seleccione...</option>');
    }

});
$(document).on('submit','[data-form="guardar-cliente"]',function (e) {
    e.preventDefault();
    var data = new FormData($(this)[0]);
    Swal.fire({
        title: 'Guardar',
        text: "¿Está seguro de guardar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'no',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: $(this).attr('type'),
                url: $(this).attr('action'),
                data: data,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        },
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: result.value.title,
                text: result.value.text,
                icon: result.value.icon,
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((resultado) => {
                if (resultado.isConfirmed) {
                    if (result.value.status===200) {
                        $('#nuevo-cliente').modal('hide');
                        $('#listar-clientes').DataTable().ajax.reload();
                    }

                }
            })
        }
    });
});
$(document).on('click','.editar-registro',function () {
    var id_contribuyente = $(this).attr('data-id-contribuyente');
    $('#editar-cliente .modal-body input[name="id_contribuyente"]').val(id_contribuyente)
    $('[data-form="editar-cliente"]')[0].reset();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: 'clientes/editar',
        data: {id_contribuyente:id_contribuyente},
        dataType: 'JSON',
        success: function(response){
            if (response.status===200) {
                $('#editar-cliente').modal('show');
                html='<option value="">Seleccione...</option>';
                if (response.provincia_all.length>0) {
                    $.each(response.provincia_all, function (index, element) {
                        html+='<option value="'+element.id_prov+'">'+element.descripcion+'</option>'
                    });
                }
                $('#editar-cliente .modal-body select[name="provincia"]').html(html);
                if (response.distrito_all.length>0) {
                    html='<option value="">Seleccione...</option>';
                    $.each(response.distrito_all, function (index, element) {
                        html+='<option value="'+element.id_dis+'">'+element.descripcion+'</option>'
                    });
                    $('#editar-cliente .modal-body select[name="distrito"]').html(html);
                }

                $('#editar-cliente .modal-body select[name="pais"] option').removeAttr('selected');
                $('#editar-cliente .modal-body select[name="pais"] option[value="'+response.contribuyente.id_pais+'"]').attr('selected',true)

                $('#editar-cliente .modal-body select[name="departamento"] option').removeAttr('selected');
                $('#editar-cliente .modal-body select[name="departamento"] option[value="'+response.departamento.id_dpto+'"]').attr('selected',true)

                $('#editar-cliente .modal-body select[name="provincia"] option').removeAttr('selected');
                $('#editar-cliente .modal-body select[name="provincia"] option[value="'+response.provincia.id_prov+'"]').attr('selected',true)

                $('#editar-cliente .modal-body select[name="distrito"] option').removeAttr('selected');
                $('#editar-cliente .modal-body select[name="distrito"] option[value="'+response.distrito.id_dis+'"]').attr('selected',true)

                $('#editar-cliente .modal-body select[name="tipo_documnto"] option').removeAttr('selected');
                $('#editar-cliente .modal-body select[name="tipo_documnto"] option[value="'+response.contribuyente.id_doc_identidad+'"]').attr('selected',true)

                $('#editar-cliente .modal-body input[name="documento"]').val(response.contribuyente.nro_documento)
                $('#editar-cliente .modal-body input[name="razon_social"]').val(response.contribuyente.razon_social)
            }else{
                Swal.fire(
                    'Error',
                    'Comuniquese con TI.',
                    'error'
                )
            }

        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
});
$(document).on('submit','[data-form="editar-cliente"]',function (e) {
    e.preventDefault();
    var data = new FormData($(this)[0]);
    Swal.fire({
        title: 'Guardar',
        text: "¿Está seguro de guardar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'no',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: $(this).attr('type'),
                url: $(this).attr('action'),
                data: data,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        },
    }).then((resultado) => {
        if (resultado.isConfirmed) {
            Swal.fire({
                title: resultado.value.title,
                text: resultado.value.text,
                icon: resultado.value.icon,
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (resultado.value.status===200) {
                        $('#editar-cliente').modal('hide');
                        $('#listar-clientes').DataTable().ajax.reload();
                    }
                }
            })
        }
    });
});
$(document).on('click','.eliminar-registro',function () {
    var id_contribuyente = $(this).attr('data-id-contribuyente');
    Swal.fire({
        title: 'Anular',
        text: "¿Está seguro de Anular?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'no',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: 'POST',
                url: 'clientes/eliminar',
                data: {id_contribuyente:id_contribuyente},
                // processData: false,
                // contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                }
            }).done(function(response) {
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

        },
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.status===200) {
                Swal.fire({
                    title: result.value.title,
                    text: result.value.text,
                    icon: result.value.icon,
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        if (result.value.status===200) {
                            $('#listar-clientes').DataTable().ajax.reload();
                        }else{

                        }
                    }
                })
            }
        }
    });

});
