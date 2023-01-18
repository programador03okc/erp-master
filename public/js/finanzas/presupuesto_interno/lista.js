

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
                    window.open('crear');
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
                        html+='<button type="button" class="btn btn-info btn-flat botonList ver-presupuesto-interno" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Ver" data-original-title="Ver"><i class="fas fa-eye"></i></button>';



                        if (row['estado']==1) {
                            html+='<button type="button" class="btn btn-success btn-flat botonList aprobar-presupuesto" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Aprobar" data-original-title="Aprobar"><i class="fas fa-thumbs-up"></i></button>';

                            html+='<button type="button" class="btn btn-warning btn-flat botonList editar-registro" data-id="'+row['id_presupuesto_interno']+'" data-toggle="tooltip" title="Editar" data-original-title="Editar"><i class="fas fa-edit"></i></button>';
                        }


                        html+='<button type="button" class="btn btn-danger btn-flat botonList eliminar" data-id="'+row['id_presupuesto_interno']+'" title="Eliminar"><i class="fas fa-trash"></i></button>';

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
        form = $('<form action="'+route_editar+'" method="POST" target="_blank">'+
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
    var id = $(this).attr('data-id');

    $('#modal-presupuesto').modal('show');
    $.ajax({
        type: 'POST',
        url: 'get-presupuesto-interno',
        data: {id:id},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {

        }
    }).done(function(response) {
        var cantidad_presupuestos =0 ,
            numero_columnas = 0,
            numero_columnas_offset = ' col-md-offset-3',
            html = '';
        $('#modal-presupuesto .codigo').text(response.data.codigo)
        if (response.presupuesto.ingresos.length!==0) {
            cantidad_presupuestos++;
        }
        if (response.presupuesto.costos.length!==0) {
            cantidad_presupuestos++;
        }
        if (response.presupuesto.gastos.length!==0) {
            cantidad_presupuestos++;
        }

        if (response.presupuesto.ingresos.length!==0) {
            html += presupuesto(response.presupuesto.ingresos, cantidad_presupuestos, 'INGRESOS');
        }
        if (response.presupuesto.costos.length!==0) {
            html += presupuesto(response.presupuesto.costos, cantidad_presupuestos, 'COSTOS');
        }
        if (response.presupuesto.gastos.length!==0) {
            html += presupuesto(response.presupuesto.gastos, cantidad_presupuestos, 'GASTOS');
        }
        $('#modal-presupuesto [data-presupuesto="table"]').html(html);

    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});
function presupuesto(data,cantidad_presupuestos, texto) {
    var html = '',
        html_option='';
    console.log(cantidad_presupuestos);
    $.each(data, function (index, element) {
        html_option+=`<tr>
            <td>`+element.partida+`</td>
            <td>`+element.descripcion+`</td>
            <td>`+element.monto+`</td>
        </tr>`
    });

    html = `
        <div class="col-md-`+(cantidad_presupuestos==2?'6':(cantidad_presupuestos==1?'6 col-md-offset-3':(cantidad_presupuestos==3?'4':'')))+`">
            <table class="table small">
                <thead>
                    <tr>
                        <th class="text-left" width="20%">PARTIDA</th>
                        <th class="text-left" width=""colspan="2">DESCRIPCION</th>
                    </tr>
                </thead>
                <tbody data-table-presupuesto="ingreso">
                    `+html_option+`
                </tbody>
            </table>
        </div>
        `;
    return html;
}
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
