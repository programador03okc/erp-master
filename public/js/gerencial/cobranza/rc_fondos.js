$(function () {
    listar();

    $('#formulario').on('submit', function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'guardar',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                $('#tabla').DataTable().ajax.reload();
                Util.notify(response.alerta, response.mensaje);
                $('#modalFondo').modal('hide');
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });

    $('#formulario-cobro').on('submit', function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'guardar-cobro',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                $('#tabla').DataTable().ajax.reload();
                Util.notify(response.alerta, response.mensaje);
                $('#modalControl').modal('hide');
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });

    $('#tabla').on('click', 'button.cobrar', function (e) {
        e.preventDefault();
        $("#formulario-cobro")[0].reset();
        $("[name=cobranza_fondo_id]").val($(e.currentTarget).data('id'));
        $("[name=nro_documento_cobro]").val($(e.currentTarget).data('documento'));
        $("#modalControl").modal("show");
    });

    $('#tabla').on('click', 'button.editar', function (e) {
        e.preventDefault();
        $("#formulario-cobro")[0].reset();

        $.ajax({
            type: 'POST',
            url: 'cargar-cobro',
            data: {id: $(e.currentTarget).data('id')},
            dataType: 'JSON',
            success: function(response) {
                $("[name=id]").val(response.id);
                $("[name=fecha_solicitud]").val(response.fecha_solicitud);
                $("[name=periodo_id]").val(response.periodo_id);
                $("[name=tipo_gestion_id]").val(response.tipo_gestion_id);
                $("[name=tipo_negocio_id]").val(response.tipo_negocio_id);
                $("[name=cliente_id]").val(response.cliente_id).trigger('change');
                $("[name=forma_pago_id]").val(response.forma_pago_id);
                $("[name=moneda_id]").val(response.moneda_id);
                $("[name=importe]").val(response.importe);
                $("[name=nro_documento]").val(response.nro_documento);
                $("[name=responsable_id]").val(response.responsable_id).trigger('change');
                $("[name=pagador]").val(response.pagador);
                $("[name=claim]").val(response.claim);
                $("[name=fecha_inicio]").val(response.fecha_inicio);
                $("[name=fecha_vencimiento]").val(response.fecha_vencimiento);
                $("[name=detalles]").val(response.detalles);
                $("#modalFondo").find(".modal-title").text("Editar el registro");
                $('#modalFondo').modal('show');
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        })
    });

    $('#tabla').on('click', 'button.eliminar', function (e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Desea eliminar el registro?',
            text: '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'eliminar',
                    data: {id: $(e.currentTarget).data('id')},
                    dataType: 'JSON',
                    success: function(response) {
                        $('#tabla').DataTable().ajax.reload();
                        Util.notify(response.alerta, response.mensaje);
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        });
    });
});

function listar() {
    const button_descargar_excel=(array_accesos.find(element => element === 319)?{
        text: '<i class="fas fa-file-excel"></i> Descargar',
        action: () => {
            exportarExcel();
        },
        className: 'btn btn-sm btn-success',
        init: function(api, node, config) {
            $(node).removeClass('btn-default')
        }
    }:[]);
    const button_agregar_registro=(array_accesos.find(element => element === 320)?{
        text: '<i class="fas fa-plus"></i> Agregar registro',
        action: function () {
            $("#formulario")[0].reset();
            $("[name=id]").val(0);
            $("[name=cliente_id]").val(null).trigger('change');
            $("[name=responsable_id]").val(null).trigger('change');
            $("#modalFondo").find(".modal-title").text("Agregar nuevo registro");
            $("#modalFondo").modal("show");
        },
        className: 'btn btn-sm btn-primary',
        init: function(api, node, config) {
            $(node).removeClass('btn-default')
        }
    }:[]);

    const $tabla = $('#tabla').DataTable({
        dom: 'Bfrtip',
        pageLength: 30,
        language: idioma,
        serverSide: true,
        destroy: true,
        initComplete: function (settings, json) {
            const $filter = $('#tabla_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><i class="fas fa-search"></i></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tabla.search($input.val()).draw();
            });
        },
        drawCallback: function (settings) {
            $('#tabla_filter input').prop('disabled', false);
            $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
            $('#tabla_filter input').trigger('focus');
        },
        order: [[0, 'asc']],
        ajax: {
            url: 'listar',
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrf_token}
        },
        columns: [
            {data: 'fecha_solicitud', className: 'text-center'},
            {data: 'tipo_gestion'},
            {data: 'tipo_negocio'},
            {data: 'cliente'},
            {data: 'claim'},
            {data: 'moneda'},
            {data: 'importe'},
            {data: 'nro_documento'},
            {data: 'forma_pago'},
            {data: 'fechas'},
            {data: 'responsable'},
            {data: 'flag_estado'},
            {data: 'accion', orderable: false, searchable: false, className: 'text-center'}
        ],
        buttons: [button_descargar_excel,button_agregar_registro]
    });
    $tabla.on('search.dt', function() {
        $('#tabla_filter input').attr('disabled', true);
        $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
    });
    $tabla.on('init.dt', function(e, settings, processing) {
        $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
    });
    $tabla.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
        } else {
            $(e.currentTarget).LoadingOverlay("hide", true);
        }
    });
}

function exportarExcel() {
    window.open('exportar-excel');
}

