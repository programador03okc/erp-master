$(function () {
    listar();
});

function listar() {
    const $tabla = $('#tabla').DataTable({
        dom: 'Bfrtip',
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
            {data: 'estado'},
            {data: 'accion', orderable: false, searchable: false, className: 'text-center'}
        ],
        buttons: [
            {
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
            },
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