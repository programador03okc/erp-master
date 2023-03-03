$(function () {
    listar();

    $('#tabla').on('click', 'button.cobrar', function (e) {
        e.preventDefault();
        $("#formulario-cobro")[0].reset();
        $("[name=cobranza_penalidad_id]").val($(e.currentTarget).data('id'));
        $("#modalControl").modal("show");
    });

    $('#formulario-cobro').on('submit', function (e) {
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
                $('#modalControl').modal('hide');
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });
});

function listar() {
    const $tabla = $('#tabla').DataTable({
        dom: 'frtip',
        pageLength: 30,
        language: idioma,
        serverSide: true,
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
            {data: 'empresa', className: 'text-center'},
            {data: 'ocam', className: 'text-center'},
            {data: 'cliente'},
            {data: 'factura', className: 'text-center'},
            {data: 'oc_fisica', className: 'text-center'},
            {data: 'siaf', className: 'text-center'},
            {data: 'gestion', className: 'text-center'},
            {data: 'pagador'},
            {data: 'moneda'},
            {data: 'importe', className: 'text-right'},
            {data: 'importe_cobro', className: 'text-right'},
            {data: 'motivo'},
            {data: 'estado'},
            {data: 'accion', orderable: false, searchable: false, className: 'text-center'}
        ]
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