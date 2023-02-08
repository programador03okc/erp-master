

$(document).ready(function () {
    lista();
});
function vistaCrear() {
    window.location.href = "crear";
}
function lista() {
    var vardataTables = funcDatatables();
    var tableRequerimientos = $("#lista-presupuesto-interno").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        lengthChange: false,
        dom: vardataTables[1],
        buttons:[
            {
                text: '<i class="fas fa-plus"></i> Nuevo presupuesto',
                attr: {
                    id: 'btn-nuevo',
                    href:'crear',
                },
                action: () => {
                    // vistaCrear();
                    window.location.href='crear';
                    // window.open('crear');
                },
                className: 'btn-default btn-sm'
            }

        ],
        ajax: {
            url: "lista-presupuesto-interno",
            type: "POST",
            data:{
                // filtros
            },
            beforeSend: data => {
                $("#lista-presupuesto-interno").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        columns: [
            {data: 'id_presupuesto_interno', name:"id_presupuesto_interno" },
            {data: 'codigo', name:"codigo" , class:"text-center"},
            {data: 'descripcion', name:"descripcion" , class:"text-center"},
            {data: 'fecha_registro', name:"fecha_registro" , class:"text-center"},
            {data: 'descripcion', name:"descripcion" , class:"text-center"},
            // {data: 'estado', name:"estado" , class:"text-center"},
            {
                render: function (data, type, row) {
                    var estado = row['estado'],
                        descripcion_estado='';
                    switch (estado) {
                        case 1:
                            descripcion_estado='Elaborado'
                        break;
                        case 2:
                            descripcion_estado='Aprobado'
                        break;
                    }
                    return descripcion_estado
                },
                className: "text-center"
            },
            {
                render: function (data, type, row) {
                    html='';
                        html+='<button type="button" class="btn text-black btn-flat botonList ver-presupuesto-interno" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Exportar Excel" data-original-title="Ver"><i class="fas fa-file-excel"></i></button>';

                        html+='<button type="button" class="btn btn-warning btn-flat botonList editar-registro" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Editar" data-original-title="Editar"><i class="fas fa-edit"></i></button>';

                        html+='<button type="button" class="btn btn-info btn-flat botonList editar-monto-partida" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Editar monto por partida" data-original-title="Editar"><i class="fas fa-pencil-alt"></i></button>';

                        if (row['estado']==1) {
                            html+='<button type="button" class="btn btn-success btn-flat botonList aprobar-presupuesto" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Aprobar" data-original-title="Aprobar"><i class="fas fa-thumbs-up"></i></button>';



                            html+='<button type="button" class="btn btn-danger btn-flat botonList eliminar" data-id="'+row['id_presupuesto_interno']+'" title="Eliminar"><i class="fas fa-trash"></i></button>';
                        }


                    html+='';
                    return html;
                },
                className: "text-center"
            }
        ],
        order: [[1, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        "drawCallback": function (settings) {

            $("#lista-presupuesto-interno").LoadingOverlay("hide", true);
        }
    });
}
$(document).on('click','.editar-registro',function () {
    var id = $(this).attr('data-id'),
        token = $('meta[name="csrf-token"]').attr('content'),
        form = $('<form action="'+route_editar+'" method="POST">'+
            '<input type="hidden" name="_token" value="'+token+'">'+
            '<input type="hidden" name="id" value="'+id+'">'+
        '</form>');
        $('body').append(form);
        form.submit();
});

$(document).on('click','.eliminar',function () {
    var id = $(this).attr('data-id');
    Swal.fire({
        title: 'Anular',
        text: "¿Está seguro de anular?",
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
                url: 'eliminar',
                data: {id:id},
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
                $('#lista-presupuesto-interno').DataTable().ajax.reload();
                Swal.fire({
                    title: 'Éxito',
                    text: "Se guardo con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {

                    }
                })
            }
        }
    });
});
$(document).on('click','.ver-presupuesto-interno',function () {
    var id = $(this).attr('data-id'),
        token = $('meta[name="csrf-token"]').attr('content'),
        form = $('<form action="get-presupuesto-interno" method="POST" target="_blank">'+
            '<input type="hidden" name="_token" value="'+token+'">'+
            '<input type="hidden" name="id" value="'+id+'">'+
        '</form>');
        $('body').append(form);
        form.submit();


});
$(document).on('click','.aprobar-presupuesto',function () {
    var id = $(this).attr('data-id');
    Swal.fire({
        title: 'Aprobar',
        text: "¿Está seguro de aprobar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        showLoaderOnConfirm: true,
        preConfirm: (login) => {
            return $.ajax({
                type: 'POST',
                url: 'aprobar',
                data: {id:id},
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
                $('#lista-presupuesto-interno').DataTable().ajax.reload();
                Swal.fire({
                    title: 'Éxito',
                    text: "Se aprobar con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {

                    }
                })
            }
        }
    });
});
$(document).on('click','.editar-monto-partida',function () {
    var id = $(this).attr('data-id');
    $('[data-form="editar-monto-partida"]')[0].reset();
    $('[data-form="editar-monto-partida"]').find('input[name="id"]').val(id);
    $('#modal-editar-monto-partida').modal('show');
    $('.search-partidas').val(null).trigger('change');
});
$(document).on('submit','[data-form="editar-monto-partida"]',function (e) {
    e.preventDefault();
    var data = $(this).serialize(),
        this_button = $(this).find('[type="submit"]');

        this_button.attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'editar-monto-partida',
        data: data,
        dataType: 'JSON',
        beforeSend: (data) => {

        }
    }).done(function(response) {
        if (response===true) {
            $('#modal-editar-monto-partida').modal('hide');
        }
        if (response===false) {
            Swal.fire(
                'Información',
                'El registo que ingreso no es posible modificarse',
                'warning'
            )
        }
        this_button.removeAttr('disabled');
    }).fail( function( jqXHR, textStatus, errorThrown ){
        this_button.removeAttr('disabled');
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});