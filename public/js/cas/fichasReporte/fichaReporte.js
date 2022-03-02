function listarIncidencias() {
    var vardataTables = funcDatatables();
    $('#listaIncidencias').dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        ajax: {
            url: "listarIncidencias",
            type: "POST"
        },
        'columns': [
            { 'data': 'id_incidencia' },
            { 'data': 'codigo' },
            { 'data': 'empresa_razon_social', 'name': 'empresa.razon_social' },
            { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
            { 'data': 'concepto', 'name': 'alm_req.concepto' },
            {
                data: 'numero', name: 'guia_ven.numero',
                'render': function (data, type, row) {
                    return (row['serie'] !== null ? row['serie'] + '-' + row['numero'] : '');
                }
            },
            { 'data': 'nombre', name: 'adm_ctb_contac.nombre' },
            { 'data': 'telefono', name: 'adm_ctb_contac.telefono' },
            { 'data': 'cargo', name: 'adm_ctb_contac.cargo' },
            { 'data': 'direccion', name: 'adm_ctb_contac.direccion' },
            { 'data': 'horario', name: 'adm_ctb_contac.horario' },
            {
                data: 'fecha_reporte',
                'render': function (data, type, row) {
                    return (row['fecha_reporte'] !== undefined ? formatDate(row['fecha_reporte']) : '');
                }
            },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            { 'data': 'falla_reportada' },
            {
                'data': 'estado_doc', name: 'estado.estado_doc',
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + '">' +
                        row['estado_doc'] + '</span>';
                }, className: "text-center"
            },
            {
                'render':
                    function (data, type, row) {
                        return `
                        <div class="btn-group" role="group">
                            <button type="button" class="detalle btn btn-success boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_incidencia']}" title="Agregar ficha reporte" >
                            <i class="fas fa-plus"></i></button>
                        </div>`;
                    }, className: "text-center"
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}
