$(document).ready(function () {
    listar();
});
function listar() {
    var vardataTables = funcDatatables();

    $('#lista-productos').DataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        // destroy: true,
        ajax: {
            url: route_listar,
            type: "POST",
            // data: function (params) {
                // return Object.assign(params, objectifyForm($('#formFiltrosIncidencias').serializeArray()))
            // }
        },
        'columns': [
            { 'data': 'id_cas_producto', 'name': 'id_cas_producto'},
            { 'data': 'id_cas_producto', 'name': 'id_cas_producto',
                render: function (data, type, row) {
                    return (
                        `0${row["id_cas_producto"]}`
                    );
                }
            },
            { 'data': 'descripcion', 'name': 'descripcion' },

            {
                'render':
                    function (data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <button type="button" class="editar btn btn-warning boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_cas_producto']}" title="Editar registro" >
                                <i class="fas fa-edit"></i></button>

                                <button type="button" class="anular btn btn-danger boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_cas_producto']}" title="Anular registro" >
                                <i class="fas fa-trash"></i></button>
                            </div>`;
                    }, className: "text-center"
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        order: [[0, "desc"]],
    });
}
$(document).on('click','[data-button="nuevo"]',function () {
    $('#nuevo').modal('show');
});
$(document).on('submit','[data-form="guardar"]',function (e) {
    e.preventDefault();
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
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize(),
                // processData: false,
                // contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    // console.log(data);
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
                $('#nuevo').modal('hide');
                $('#lista-productos').DataTable().ajax.reload();
                Swal.fire({
                    title: 'Éxito',
                    text: "Se guardo con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                })
            }
        }
    });
});
$(document).on('click','.editar',function () {
    var id=$(this).attr('data-id');
    $('[data-form="actualizar"] input[name="id"]').val(id);
    $.ajax({
        type: 'GET',
        url: route_editar,
        data: {id:$(this).attr('data-id')},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            // console.log(data);
        }
    }).done(function(response) {
        $('[data-form="actualizar"] input[name="descripcion"]').val(response.data.descripcion);
        $('#editar').modal('show');
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});
$(document).on('submit','[data-form="actualizar"]',function (e) {
    e.preventDefault();
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
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize(),
                // processData: false,
                // contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    // console.log(data);
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
                $('#editar').modal('hide');
                $('#lista-productos').DataTable().ajax.reload();
                Swal.fire({
                    title: 'Éxito',
                    text: "Se guardo con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                })
            }
        }
    });
});
$(document).on('click','.anular',function () {
    var id=$(this).attr('data-id');
    Swal.fire({
        title: 'Guardar',
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
                url: route_eliminar,
                data: {id:id},
                // processData: false,
                // contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    // console.log(data);
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
                $('#lista-productos').DataTable().ajax.reload();
                Swal.fire({
                    title: 'Éxito',
                    text: "Se anulo con éxito",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                })
            }
        }
    });
});
