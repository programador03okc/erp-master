class RequerimientoPago {
    constructor(permisoVer, permisoEnviar, permisoRegistrar) {
        this.permisoVer = permisoVer;
        this.permisoEnviar = permisoEnviar;
        this.permisoRegistrar = 1;
        this.listarRequerimientos();
        this.listarComprobantes();
        this.listarOrdenes();
    }

    listarRequerimientos() {
        const permisoVer = this.permisoVer;
        const permisoEnviar = this.permisoEnviar;
        const permisoRegistrar = this.permisoRegistrar;

        var vardataTables = funcDatatables();

        tableRequerimientos = $('#listaRequerimientos').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            'serverSide': true,
            'ajax': {
                url: 'listarRequerimientosPago',
                type: 'POST',
            },
            'columns': [
                { 'data': 'id_requerimiento_pago', 'name': 'requerimiento_pago.id_requerimiento_pago' },
                {
                    'data': 'prioridad', 'name': 'adm_prioridad.descripcion',
                    'render': function (data, type, row) {
                        var imagen = '';
                        if (row['prioridad'] == 'Normal') {
                            imagen = '<i class="fas fa-thermometer-empty green" data-toggle="tooltip" data-placement="right" title="Normal"></i>';
                        }
                        else if (row['prioridad'] == 'Crítica') {
                            imagen = '<i class="fas fa-thermometer-full red" data-toggle="tooltip" data-placement="right" title="Crítica"></i>';
                        }
                        else if (row['prioridad'] == 'Alta') {
                            imagen = '<i class="fas fa-thermometer-half orange" data-toggle="tooltip" data-placement="right" title="Alta"></i>';
                        }
                        return imagen;
                    }, 'className': 'text-center'
                },
                { 'data': 'razon_social_empresa', 'name': 'empresa.razon_social' },
                // { 'data': 'codigo', 'name': 'requerimiento_pago.codigo', 'className': 'text-center' },
                {
                    data: "codigo", name: "requerimiento_pago.codigo",
                    render: function (data, type, row) {
                        return (
                            `<a href="#" class="verRequerimiento" data-id="${row["id_requerimiento_pago"]}" >
                            ${row["codigo"]}</a>`
                        );
                    },
                    className: "text-center"
                },
                { 'data': 'grupo_descripcion', 'name': 'sis_grupo.descripcion' },
                { 'data': 'concepto', 'name': 'requerimiento_pago.concepto' },
                { 'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto' },
                // {
                //     'render': function (data, type, row) {
                //         console.log(row['persona']);
                //         return (row['persona'][0] !== undefined ? row['persona'][0].nro_documento : row['nro_documento']);
                //     }, 'className': 'text-center', 'searchable': false
                // },
                {
                    'render': function (data, type, row) {
                        return (row['persona'][0] !== undefined ? row['persona'][0].nombre_completo : row['razon_social']);
                    }, 'searchable': false
                },
                {
                    'render': function (data, type, row) {
                        return (row['fecha_registro'] !== null ? formatDate(row['fecha_registro']) : '');
                    }, 'className': 'text-center', 'searchable': false
                },
                // { 'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta' },
                { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center' },
                {
                    'data': 'monto_total', 'name': 'requerimiento_pago.monto_total',
                    'render': function (data, type, row) {
                        return formatNumber.decimal(row['monto_total'], '', -2);
                    }, 'className': 'text-right'
                },
                {
                    'render': function (data, type, row) {
                        var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        var total = formatDecimal(row['monto_total']);
                        var por_pagar = (total - pagado);
                        return por_pagar > 0 ? '<strong>' + formatNumber.decimal(por_pagar, '', -2) + '</strong>' : formatNumber.decimal(por_pagar, '', -2);
                    }, 'className': 'text-right celestito'
                },
                {
                    'render': function (data, type, row) {
                        return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                    }
                },
                {
                    'render':
                        function (data, type, row) {
                            // <button type="button" class="autorizar btn btn-default boton" data-toggle="tooltip" 
                            //     data-placement="bottom" data-id="${row['id_requerimiento_pago']}" data-tipo="requerimiento"
                            //     title="Ver requerimiento de pago"> <i class="fas fa-eye"></i></button>
                            return `<div class="btn-group" role="group">

                            <button type="button" class="adjuntos btn btn-${(row['count_adjunto_cabecera'] + row['count_adjunto_detalle']) == 0 ? 'default' : 'warning'} boton" 
                                data-toggle="tooltip" data-placement="bottom" data-id="${row['id_requerimiento_pago']}" data-codigo="${row['codigo']}" 
                                title="Ver adjuntos"><i class="fas fa-paperclip"></i></button>

                            ${(row['id_estado'] == 2 && permisoEnviar == '1') ?
                                    `<button type="button" class="autorizar btn btn-info boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_requerimiento_pago']}" data-tipo="requerimiento"
                                title="Autorizar pago"> <i class="fas fa-share"></i></button>`
                                    : ''}
                            ${row['id_estado'] == 5 ?
                                    `${permisoEnviar == '1' ?
                                        `<button type="button" class="revertir btn btn-danger boton" data-toggle="tooltip" 
                                        data-placement="bottom" data-id="${row['id_requerimiento_pago']}" data-tipo="requerimiento"
                                        title="Revertir autorización"><i class="fas fa-undo-alt"></i></button>`: ''}
                                    ${permisoRegistrar == '1' ?
                                        `<button type="button" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom" 
                                        data-id="${row['id_requerimiento_pago']}" data-cod="${row['codigo']}" data-tipo="requerimiento"
                                        data-total="${row['monto_total']}" data-pago="${row['suma_pagado']}" data-moneda="${row['simbolo']}" 
                                        data-nrodoc="${row['nro_documento'] !== null ? row['nro_documento'] : (row['persona'][0] !== undefined ? row['persona'][0].nro_documento : '')}"
                                        data-prov="${encodeURIComponent(row['razon_social'] !== null ? row['razon_social'] : (row['persona'][0] !== undefined ? row['persona'][0].nombre_completo : ''))}" 
                                        data-cta="${row['nro_cuenta'] !== null ? row['nro_cuenta'] : row['nro_cuenta_persona']}" 
                                        data-cci="${row['nro_cuenta_interbancaria'] !== null ? row['nro_cuenta_interbancaria'] : row['nro_cci_persona']}" 
                                        data-tpcta="${row['tipo_cuenta'] !== null ? row['tipo_cuenta'] : row['tipo_cuenta_persona']}" 
                                        data-banco="${row['banco_persona'] !== null ? row['banco_persona'] : row['banco_contribuyente']}"
                                        data-empresa="${row['razon_social_empresa']}" data-idempresa="${row['id_empresa']}"
                                        data-motivo="${encodeURIComponent(row['concepto'])}"
                                        title="Registrar Pago"> 
                                    <i class="fas fa-hand-holding-usd"></i> </button>`
                                        : ''}`
                                    : ''
                                }
                            ${row['suma_pagado'] > 0 && permisoVer == '1' ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_requerimiento_pago']}" title="Ver detalle de los pagos" >
                                    <i class="fas fa-chevron-down"></i></button>`: ''
                                }
                                
                            </div> `;

                        }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
            'order': [[0, "desc"]]
        });

    }

    listarOrdenes() {
        const permisoVer = this.permisoVer;
        const permisoEnviar = this.permisoEnviar;
        const permisoRegistrar = this.permisoRegistrar;

        var vardataTables = funcDatatables();
        tableOrdenes = $('#listaOrdenes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            'serverSide': true,
            'ajax': {
                url: 'listarOrdenesCompra',
                type: 'POST'
            },
            'columns': [
                { 'data': 'id_orden_compra' },
                {
                    'data': 'prioridad', 'name': 'adm_prioridad.descripcion',
                    'render': function (data, type, row) {
                        var imagen = '';
                        if (row['prioridad'] == 'Normal') {
                            imagen = '<i class="fas fa-thermometer-empty green" data-toggle="tooltip" data-placement="right" title="Normal"></i>';
                        }
                        else if (row['prioridad'] == 'Crítica') {
                            imagen = '<i class="fas fa-thermometer-full red" data-toggle="tooltip" data-placement="right" title="Crítica"></i>';
                        }
                        else if (row['prioridad'] == 'Alta') {
                            imagen = '<i class="fas fa-thermometer-half orange" data-toggle="tooltip" data-placement="right" title="Alta"></i>';
                        }
                        return imagen;
                    }, 'className': 'text-center'
                },
                { 'data': 'requerimientos_codigo' },
                { 'data': 'razon_social_empresa', 'name': 'empresa.razon_social' },
                // { 'data': 'codigo' },
                {
                    data: "codigo", name: "log_ord_compra.codigo",
                    render: function (data, type, row) {
                        return (
                            `<a href="#" class="verOrden" data-id="${row["id_orden_compra"]}" >
                            ${row["codigo"]}</a>`
                        );
                    },
                    className: "text-center"
                },
                // { 'data': 'codigo_softlink' },
                { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                {
                    'render': function (data, type, row) {
                        return (row['fecha'] !== null ? formatDate(row['fecha']) : '');
                    }, 'className': 'text-center', 'searchable': false
                },
                { 'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion' },
                // { 'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta' },
                { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center' },
                {
                    'render': function (data, type, row) {
                        return (row['monto_total'] !== null ? formatNumber.decimal(row['monto_total'], '', -2) : '0.00');
                    }, 'className': 'text-right'
                },
                {
                    'render': function (data, type, row) {
                        var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        var total = formatDecimal(row['monto_total']);
                        var por_pagar = (total - pagado);
                        return por_pagar > 0 ? '<strong>' + formatNumber.decimal(por_pagar, '', -2) + '</strong>' : formatNumber.decimal(por_pagar, '', -2);
                    }, 'className': 'text-right celestito'
                },
                {
                    'render': function (data, type, row) {
                        return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                    }
                },
                {
                    'render':
                        function (data, type, row) {
                            return `<div class="btn-group" role="group">
                ${(row['estado_pago'] == 8 && permisoEnviar == '1') ?
                                    `<button type="button" class="autorizar btn btn-info boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_orden_compra']}" data-tipo="orden"
                                title="Autorizar pago" >
                                <i class="fas fa-share"></i></button>`: ''}
                            ${row['estado_pago'] == 5 ?
                                    (`${permisoEnviar == '1' ?
                                        `<button type="button" class="revertir btn btn-danger boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_orden_compra']}" data-tipo="orden"
                                    title="Revertir autorización"><i class="fas fa-undo-alt"></i></button>` : ''}
                                    
                                ${permisoRegistrar == '1' ?
                                            `<button type="button" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom" 
                                    data-id="${row['id_orden_compra']}" data-cod="${row['codigo']}" data-tipo="orden"
                                    data-total="${row['monto_total']}" data-pago="${row['suma_pagado']}" 
                                    data-moneda="${row['simbolo']}" 

                                    data-nrodoc="${row['nro_documento'] !== null ? row['nro_documento'] : row['persona'][0].nro_documento}"
                                    data-prov="${encodeURIComponent(row['razon_social'] !== null ? row['razon_social'] : row['persona'][0].nombre_completo)}" 
                                    data-cta="${row['nro_cuenta'] !== null ? row['nro_cuenta'] : row['nro_cuenta_persona']}" 
                                    data-cci="${row['nro_cuenta_interbancaria'] !== null ? row['nro_cuenta_interbancaria'] : row['nro_cci_persona']}" 
                                    data-tpcta="${row['tipo_cuenta'] !== null ? row['tipo_cuenta'] : row['tipo_cuenta_persona']}" 
                                    data-banco="${row['banco_persona'] !== null ? row['banco_persona'] : row['banco_contribuyente']}" 
                                    data-empresa="${row['razon_social_empresa']}" data-idempresa="${row['id_empresa']}"
                                    data-motivo="${encodeURIComponent(row['condicion_pago'])}"
                                    title="Registrar Pago"><i class="fas fa-hand-holding-usd"></i></button>`: ''}`)
                                    : ''}
                            ${row['suma_pagado'] > 0 && permisoVer == '1' ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_orden_compra']}" title="Ver detalle de los pagos" >
                                <i class="fas fa-chevron-down"></i></button>`
                                    : ''}
                        </div> `;
                        }, 'searchable': false
                },
            ],
            'columnDefs': [
                { 'aTargets': [0], 'sClass': 'invisible' }
            ],
        });

    }

    listarComprobantes() {
        var vardataTables = funcDatatables();
        // console.time();
        tableComprobantes = $('#listaComprobantes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            'serverSide': true,
            'ajax': {
                url: 'listarComprobantesPagos',
                type: 'POST',
                // complete: function(){
                //     console.timeEnd();
                // }
            },
            'columns': [
                { 'data': 'id_doc_com', 'name': 'doc_com.id_doc_com' },
                { 'data': 'tipo_documento', 'name': 'cont_tp_doc.descripcion', 'className': 'text-center' },
                { 'data': 'serie', 'name': 'doc_com.serie', 'className': 'text-center' },
                { 'data': 'numero', 'name': 'doc_com.numero', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                { 'data': 'fecha_emision', 'name': 'doc_com.fecha_emision', 'className': 'text-center' },
                { 'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion', 'className': 'text-center' },
                { 'data': 'fecha_vcmto', 'name': 'doc_com.fecha_vcmto', 'className': 'text-center' },
                { 'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta' },
                { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center' },
                { 'data': 'total_a_pagar_format', 'className': 'text-right' },
                {
                    'render': function (data, type, row) {
                        var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        var total = formatDecimal(row['total_a_pagar']);
                        var por_pagar = (total - pagado);
                        return por_pagar > 0 ? '<strong>' + formatNumber.decimal(por_pagar, '', -2) + '</strong>' : formatNumber.decimal(por_pagar, '', -2);
                        // return (formatDecimal(formatDecimal(row['total_a_pagar']) - formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0)));
                    }, 'className': 'text-right celestito'
                },
                { 'data': 'span_estado', 'searchable': false, 'className': 'text-center' },
                {
                    'render':
                        function (data, type, row) {
                            return `<div class="btn-group" role="group">
                            ${row['estado'] == 1 ?
                                    `<button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom" 
                                    data-id="${row['id_doc_com']}" data-cod="${row['serie'] + '-' + row['numero']}" data-tipo="comprobante"
                                    data-total="${row['total_a_pagar']}" data-pago="${row['suma_pagado']}" data-nrodoc="${row['nro_documento']}"
                                    data-moneda="${row['simbolo']}" data-prov="${encodeURIComponent(row['razon_social'])}" 
                                    data-cta="${row['nro_cuenta']}" title="Registrar Pago"> 
                                    <i class="fas fa-hand-holding-usd"></i> </button>`: ''
                                }
                            ${row['suma_pagado'] > 0 ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_doc_com']}" title="Ver detalle de los pagos" >
                                    <i class="fas fa-chevron-down"></i></button>`: ''
                                }
                            </div> `;

                        }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });

    }

}

$('#listaRequerimientos tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
});

$('#listaOrdenes tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
});

$('#listaComprobantes tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
});

$('#listaRequerimientos tbody').on("click", "button.autorizar", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    enviarAPago(tipo, id);
});

$('#listaOrdenes tbody').on("click", "button.autorizar", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    enviarAPago(tipo, id);
});

$('#listaRequerimientos tbody').on("click", "button.revertir", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    revertirEnvio(tipo, id);
});

$('#listaOrdenes tbody').on("click", "button.revertir", function () {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo');
    revertirEnvio(tipo, id);
});

$('#listaRequerimientos tbody').on("click", "button.adjuntos", function () {
    var id = $(this).data('id');
    var codigo = $(this).data('codigo');
    verAdjuntos(id, codigo);
});

function verAdjuntos(id, codigo) {
    $('#modal-verAdjuntos').modal({
        show: true
    });
    $('[name=codigo_requerimiento_pago]').text(codigo);
    $('#adjuntosCabecera tbody').html('');
    $('#adjuntosDetalle tbody').html('');

    $.ajax({
        type: 'GET',
        url: 'verAdjuntos/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            if (response.adjuntoPadre.length > 0) {
                var html = '';
                response.adjuntoPadre.forEach(function (element) {
                    html += `<tr>
                        <td><a target="_blank" href="/files/necesidades/requerimientos/pago/cabecera/${element.archivo}">${element.archivo}</a></td>
                        <td>${element.categoria_adjunto !== null ? element.categoria_adjunto.descripcion : ''}</td>
                    </tr>`;
                });
                $('#adjuntosCabecera tbody').html(html);
            }

            if (response.adjuntoDetalle.length > 0) {
                var html = '';
                response.adjuntoDetalle.forEach(function (element) {
                    html += `<tr>
                        <td><a target="_blank" href="/files/necesidades/requerimientos/pago/detalle/${element.archivo}">${element.archivo}</a></td>
                    </tr>`;
                });
                $('#adjuntosDetalle tbody').html(html);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#listaRequerimientos tbody").on("click", "a.verRequerimiento", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    if (id !== "") {
        // let url = `/necesidades/pago/listado/imprimir-requerimiento-pago-pdf/${id}`;
        // var win = window.open(url, "_blank");
        // win.focus();
        $('#modal-vista-rapida-requerimiento-pago').modal({
            show: true
        });
        limpiarVistaRapidaRequerimientoPago();
        cargarDataRequerimientoPago(id);
    }
});

$("#listaOrdenes tbody").on("click", "a.verOrden", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    if (id !== "") {
        let url = `/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${id}`;
        var win = window.open(url, "_blank");
        win.focus();
    }
});

var iTableCounter = 1;
var oInnerTable;
var tableOrdenes;

$('#listaOrdenes tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableOrdenes.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagos(iTableCounter, id, row, "orden");
        tr.addClass('shown');
        oInnerTable = $('#listaOrdenes_' + iTableCounter).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounter = iTableCounter + 1;
    }
});

var iTableCounterComp = 1;
var oInnerTableComp;
var tableComprobantes;

$('#listaComprobantes tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableComprobantes.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagos(iTableCounter, id, row, "comprobante");
        tr.addClass('shown');
        oInnerTableComp = $('#listaComprobantes_' + iTableCounterComp).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounterComp = iTableCounterComp + 1;
    }
});


var iTableCounterReq = 1;
var oInnerTableReq;
var tableRequerimientos;

$('#listaRequerimientos tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableRequerimientos.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagos(iTableCounter, id, row, "requerimiento");
        tr.addClass('shown');
        oInnerTableReq = $('#listaRequerimientos_' + iTableCounterReq).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounterReq = iTableCounterReq + 1;
    }
});

function formatPagos(table_id, id, row, tipo) {
    console.log(tipo)
    $.ajax({
        type: 'GET',
        url: 'listarPagos/' + tipo + '/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var i = 1;

            if (response.length > 0) {
                response.forEach(element => {
                    html += '<tr id="' + element.id_pago + '">' +
                        '<td style="border: none;">' + i + '</td>' +
                        '<td style="border: none; text-align: center">' + (element.fecha_pago !== null ? formatDate(element.fecha_pago) : '') + '</td>' +
                        '<td style="border: none; text-align: center">' + element.razon_social_empresa + '</td>' +
                        '<td style="border: none; text-align: center">' + element.nro_cuenta + '</td>' +
                        '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                        '<td style="border: none; text-align: center">' + element.simbolo + ' ' + formatDecimal(element.total_pago) + '</td>' +
                        '<td style="border: none; text-align: center">' + (element.count_adjuntos > 0 ? '<a href="#" onClick="verAdjuntosPago(' + element.id_pago + ');">' + element.count_adjuntos + ' archivos adjuntos </a>' : '') + '</td>' +
                        '<td style="border: none; text-align: center">' + element.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + formatDateHour(element.fecha_registro) + '</td>' +
                        '<td style="border: none; text-align: center">' +
                        `<button type = "button" class= "btn btn-danger boton" data - toggle="tooltip" 
                            data - placement="bottom" data - row="${row}"
                            onClick = "anularPago(${element.id_pago},'${tipo}')" title = "Anular pago">
        <i class="fas fa-trash"></i></button` +
                        '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `<table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Empresa</th>
                        <th style="border: none;">Cuenta origen</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                        <th style="border: none;">Anular</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table> `;
            }
            else {
                var tabla = `<table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
                </table> `;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function verAdjuntosPago(id_pago) {

    if (id_pago !== "") {
        $('#modal-verAdjuntosPago').modal({
            show: true
        });
        $('#adjuntosPago tbody').html('');

        $.ajax({
            type: 'GET',
            url: 'verAdjuntosPago/' + id_pago,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.length > 0) {
                    var html = '';
                    response.forEach(function (element) {
                        html += `<tr>
                            <td><a target="_blank" href="/files/tesoreria/pagos/${element.adjunto}">${element.adjunto}</a></td>
                        </tr>`;
                    });
                    $('#adjuntosPago tbody').html(html);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
