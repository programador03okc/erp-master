$(document).ready(function () {
    listarRegistros();
});
function listarRegistros() {
    var vardataTables = funcDatatables();
        tableClientes = $("#listar-clientes").DataTable({
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
                    (array_accesos.find(element => element === 316)?html+='<button type="button" class="btn btn-primary btn-flat botonList ver-registro" data-toggle="tooltip" title="Ver cliente" data-original-title="Ver cliente" data-id-contribuyente="'+row['id_contribuyente']+'"><i class="fas fa-eye"></i></button>':'');

                    (array_accesos.find(element => element === 317)?html+='<a href="cliente/'+row['id_contribuyente']+'" class="btn btn-warning btn-flat botonList " data-toggle="tooltip" title="Editar" data-original-title="Editar" ><i class="fas fa-edit"></i></a>':'');

                    (array_accesos.find(element => element === 318)?html+='<button type="button" class="btn btn-danger btn-flat botonList eliminar-registro" data-toggle="tooltip" title="Anular" data-original-title="Anular" data-id-contribuyente="'+row['id_contribuyente']+'"><i class="fas fa-trash"></i></button>':'');
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
$(document).on('click','.ver-registro',function () {
    var id_contribuyente = $(this).attr('data-id-contribuyente'),
        html='';
    $.ajax({
        type: 'GET',
        url: 'cliente/ver/'+id_contribuyente,
        data: {},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
        }
    }).done(function(response) {

        $('#tab_datos .pais').text('');
        if (response.pais) {
            $('#tab_datos .pais').text(' '+response.pais.descripcion);
        }

        $('#tab_datos .departamento').text('');

        if (!$.isEmptyObject(response.departamento_first)) {
            $('#tab_datos .departamento').text(' '+response.departamento_first.descripcion);
        }
        $('#tab_datos .provincia').text('');
        if (!$.isEmptyObject(response.provincia_first)) {
            $('#tab_datos .provincia').text(' '+response.provincia_first.descripcion);
        }
        $('#tab_datos .distrito').text('');
        if (!$.isEmptyObject(response.distrito_first)) {
            $('#tab_datos .distrito').text(' '+response.distrito_first.descripcion);
        }
        $('#tab_datos .tipo_documento').text('');
        // console.log(response.tipo_documento);
        if (!$.isEmptyObject(response.tipo_documento)) {
            $('#tab_datos .tipo_documento').text(' '+response.tipo_documento.descripcion);
        }
        $('#tab_datos .tipo_contribuyente').text('');
        if (!$.isEmptyObject(response.tipo_contribuyente)) {
            $('#tab_datos .tipo_contribuyente').text(' '+response.tipo_contribuyente.descripcion);
        }
        $('#tab_datos .documento').text('');
        $('#tab_datos .razon_social').text('');
        $('#tab_datos .direccion').text('');
        $('#tab_datos .telefono').text('');
        $('#tab_datos .celular').text('');
        $('#tab_datos .email').text('');
        if (response.contribuyente.nro_documento) {
            $('#tab_datos .documento').text(' '+response.contribuyente.nro_documento);
        }
        if (response.contribuyente.razon_social) {
        $('#tab_datos .razon_social').text(' '+response.contribuyente.razon_social);
        }
        if (response.contribuyente.direccion_fiscal) {
            $('#tab_datos .direccion').text(' '+response.contribuyente.direccion_fiscal);
        }
        if (response.contribuyente.nro_docutelefonomento) {
            $('#tab_datos .telefono').text(' '+response.contribuyente.telefono);
        }
        if (response.contribuyente.celular) {
            $('#tab_datos .celular').text(' '+response.contribuyente.celular);
        }
        if (response.contribuyente.email) {
            $('#tab_datos .email').text(' '+response.contribuyente.email);
        }
        html='';
        if (response.establecimiento_cliente.length>0) {
            $.each(response.establecimiento_cliente, function (index, element) {
                html +='<tr>'
                    html +='<td data-select="direccion"><label>'+element.direccion+'</label></td>'
                    html +='<td data-select="ubigeo"><label>'+element.ubigeo_text+'</label></td>'
                    html +='<td data-select="horario"><label>'+element.horario+'</label></td>'
                html +='</tr>';
            });
        }

        $('[data-table="tbody-establecimiento"]').html(html);

        html='';
        if (response.contacto.length>0) {
            $.each(response.contacto, function (index, element) {
                html +='<tr>'
                    html +='<td data-select="nombre">'
                        html +=' <label>'+element.nombre+'</label>'
                    html +='</td>'
                    html +='<td data-select="cargo">'
                        html +='<label>'+element.cargo+'</label>'
                    html +='</td>'
                    html +='<td data-select="telefono">'
                        html +='<label>'+element.telefono+'</label>'
                    html +='</td>'
                    html +='<td data-select="email">'
                        html +='<label>'+element.email+'</label>'
                    html +='</td>'
                    html +='<td data-select="direccion">'
                        html +='<label>'+element.direccion+'</label>'
                    html +='</td>'
                    html +='<td data-select="ubigeo">'
                        html +='<label>'+element.ubigeo_text+'</label>'
                    html +='</td>'
                    html +='<td data-select="horario">'
                        html +='<label>'+element.horario+'</label>'
                    html +='</td>'
                html +='</tr>'
            });
        }
        $('[data-table="lista-contactos"]').html(html);

        if (response.cuenta_bancaria.length>0) {
            $.each(response.cuenta_bancaria, function (index, element) {
                html +='<tr>'
                    html +='<td data-select="banco">'
                        html +='<label>'+element.banco_text+'</label>'
                    html +='</td>'
                    html +='<td data-select="tipo_cuenta">'
                        html +='<label>'+element.cuenta_text+'</label>'
                    html +='</td>'
                    html +='<td data-select="moneda">'
                        html +='<label>'+element.modena_text+'</label>'
                    html +='</td>'
                    html +='<td data-select="numero_cuenta">'
                        html +='<label>'+element.nro_cuenta+'</label>'
                    html +='</td>'
                    html +='<td data-select="cuenta_interbancaria">'
                        html +='<label>'+element.nro_cuenta_interbancaria+'</label>'
                    html +='</td>'
                    html +='<td data-select="swift">'
                        html +='<label>'+element.swift+'</label>'
                    html +='</td>'
                html +='</tr>'
                html +='';
            });
        }
        $('[data-table="lista-cuenta-bancaria"]').html(html);

        $('#tab_observaciones [name="observacion"]').val(response.cliente.observacion);
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    $('#ver-cliente').modal('show');
});
