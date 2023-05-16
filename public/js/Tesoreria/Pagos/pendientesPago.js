var array_adjuntos = [];
class RequerimientoPago {
    constructor(permisoVer, permisoEnviar, permisoRegistrar) {
        this.permisoVer = permisoVer;
        this.permisoEnviar = permisoEnviar;
        this.permisoRegistrar = permisoRegistrar;
        this.listarRequerimientos();
        this.listarOrdenes();
        // this.listarComprobantes();
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
                { 'data': 'codigo_empresa', 'name': 'adm_empresa.codigo' },
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
                // { 'data': 'grupo_descripcion', 'name': 'sis_grupo.descripcion' },
                { 'data': 'concepto', 'name': 'requerimiento_pago.concepto' },
                { 'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto' },
                { data: 'persona' },
                {
                    'render': function (data, type, row) {
                        return (row['fecha_registro'] !== null ? (row['fecha_registro']) : '');
                    }, 'className': 'text-center', 'data': 'fecha_registro', 'name': 'requerimiento_pago.fecha_registro',
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
                    'data': 'estado_doc', 'name': 'requerimiento_pago_estado.descripcion',
                    'render': function (data, type, row) {
                        // var estadoAdd = '';
                        // var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        // var total = formatDecimal(row['monto_total']);
                        // var por_pagar = (total - pagado);
                        // if (por_pagar > 0 && por_pagar < total) {
                        //     estadoAdd = '<span class="label label-danger">Saldo por pagar</span>';
                        // }
                        return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>';

                    }, className: 'text-center'
                },
                {
                    'data': 'nombre_autorizado', 'name': 'autorizado.nombre_corto',
                    'render': function (data, type, row) {
                        if (row['nombre_autorizado'] !== null) {
                            return row['nombre_autorizado'] + ' el ' + formatDateHour(row['fecha_autorizacion']);
                        } else {
                            return '';
                        }
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
                                    `
                                    : ''
                                }
                                ${row['id_estado'] == 5 || row['id_estado'] == 9 ?
                                    `${permisoRegistrar == '1' ?
                                        `<button type="button" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom"
                                        data-id="${row['id_requerimiento_pago']}" data-cod="${row['codigo']}" data-tipo="requerimiento"
                                        data-total="${row['monto_total']}" data-pago="${row['suma_pagado']}" data-moneda="${row['simbolo']}"
                                        data-nrodoc="${row['nro_documento'] !== null ? row['nro_documento'] : (row['dni_persona'] !== undefined ? row['dni_persona'] : '')}"
                                        data-prov="${encodeURIComponent(row['razon_social'] !== null ? row['razon_social'] : (row['persona'] !== undefined ? row['persona'] : ''))}"
                                        data-cta="${row['nro_cuenta'] !== null ? row['nro_cuenta'] : row['nro_cuenta_persona']}"
                                        data-cci="${row['nro_cuenta_interbancaria'] !== null ? row['nro_cuenta_interbancaria'] : row['nro_cci_persona']}"
                                        data-tpcta="${row['tipo_cuenta'] !== null ? row['tipo_cuenta'] : row['tipo_cuenta_persona']}"
                                        data-banco="${row['banco_persona'] !== null ? row['banco_persona'] : row['banco_contribuyente']}"
                                        data-empresa="${row['razon_social_empresa']}" data-idempresa="${row['id_empresa']}"
                                        data-motivo="${encodeURIComponent(row['concepto'])}"
                                        data-observacion-requerimiento="${row['comentario']}"
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
            'order': [[11, "asc"], [0, "desc"]]
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
            'pageLength': 10,
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
                { 'data': 'requerimientos', orderable: false, filterable: true },
                { 'data': 'codigo_empresa', 'name': 'adm_empresa.codigo' },
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
                // { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                {
                    'data': 'fecha_solicitud_pago',
                    'render': function (data, type, row) {
                        return (row['fecha_solicitud_pago'] !== null ? row['fecha_solicitud_pago'] : '');
                    }, 'className': 'text-center', 'searchable': true
                },
                // { 'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion' },
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
                        if(JSON.parse(row['tiene_pago_en_cuotas'])==true){
                            return ((parseFloat(row['ultima_monto_cuota'])>0?(formatNumber.decimal( row['ultima_monto_cuota'],'',-2)):(row['monto_total'] !== null ? formatNumber.decimal(row['monto_total'], '', -2) : '0.00')) );
                        }else{
                            return '(No aplica)';
                        }
                    }, 'className': 'text-right'
                },
                {
                    'data': 'estado_doc', 'name': 'requerimiento_pago_estado.descripcion',
                    'render': function (data, type, row) {
                        // var estadoAdd = '';
                        // var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        // var total = formatDecimal(row['monto_total']);
                        // var por_pagar = (total - pagado);
                        // if (por_pagar > 0 && por_pagar < total) {
                        //     estadoAdd = '<span class="label label-danger">Saldo por pagar</span>';
                        // }
                        return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>';
                    }, className: 'text-center'
                },
                {
                    'data': 'nombre_autorizado', 'name': 'autorizado.nombre_corto',
                    'render': function (data, type, row) {
                        if (row['nombre_autorizado'] !== null) {
                            return row['nombre_autorizado'] + ' el ' + formatDateHour(row['fecha_autorizacion']);
                        } else {
                            return '';
                        }
                    }
                },
                {
                    'render':
                        function (data, type, row) {

                            let observacionRequerimiento = row.requerimientos != null && row.requerimientos.length > 0 ? row.requerimientos.map(e => (e.observacion)).join(",") : '';

                            let nombreDestinatario = '';
                            let cuentaDestinatario = '';
                            let nroDocumentoDestinatario = '';
                            let tipoCuentaDestinatario = '';
                            let cuentaCCIDestinatario = '';
                            let bancoDestinatario = '';
                            switch (row['id_tipo_destinatario_pago']) {
                                case 1:
                                    nombreDestinatario = row['nombre_completo_persona'] ?? '';
                                    nroDocumentoDestinatario = row['nro_documento_persona'] ?? '';
                                    tipoCuentaDestinatario = row['tipo_cuenta_persona'] ?? '';
                                    cuentaDestinatario = row['nro_cuenta_persona'] ?? '';
                                    cuentaCCIDestinatario = row['nro_cci_persona'] ?? '';
                                    bancoDestinatario = row['banco_persona'] ?? '';

                                    break;
                                case 2:
                                    nombreDestinatario = encodeURIComponent(row['razon_social']) ?? '';
                                    nroDocumentoDestinatario = row['nro_documento'] ?? '';
                                    cuentaDestinatario = row['nro_cuenta'] ?? '';
                                    tipoCuentaDestinatario = row['tipo_cuenta'] ?? '';
                                    cuentaCCIDestinatario = row['nro_cuenta_interbancaria'] ?? '';
                                    bancoDestinatario = row['banco_contribuyente'] ?? '';
                                    break;

                                default:
                                    break;
                            }
                            console.log(row['id_estado'] == 10  );
                            return `<div class="btn-group" role="group">
                ${(row['estado_pago'] == 8 && permisoEnviar == '1' && row['tiene_pago_en_cuotas']===false) ?
                                    `<button type="button" class="autorizar btn btn-info boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_orden_compra']}" data-tipo="orden"
                                title="Autorizar pago" >
                                <i class="fas fa-share"></i></button>`: ''}
                ${(permisoEnviar == '1' && row['tiene_pago_en_cuotas']===true) ?
                                    `<button type="button" class="visualizarPagosEnCuotas btn btn-info boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_orden_compra']}" data-tipo="orden"
                                title="Visualizar pagos en cuotas" >
                                <i class="fas fa-calendar-check"></i></button>`: ''}
                            ${row['estado_pago'] == 5 ?
                                    `${permisoEnviar == '1' ?
                                        `<button type="button" class="revertir btn btn-danger boton" data-toggle="tooltip"
                                    data-placement="bottom" data-id="${row['id_orden_compra']}" data-tipo="orden"
                                    title="Revertir autorización y solicitud de pago"><i class="fas fa-undo-alt"></i></button>` : ''}
                                    `
                                    : ''}

                                ${row['estado_pago'] == 5 || row['estado_pago'] == 9 || (row['estado_pago'] == 10 && parseFloat(row['suma_cuotas_con_autorizacion'])>0) ?
                                    `${permisoRegistrar == '1' ?
                                        `<button type="button" class="pago btn btn-success boton" data-toggle="tooltip" data-placement="bottom"
                                    data-id="${row['id_orden_compra']}" data-cod="${row['codigo']}" data-tipo="orden"
                                    data-total="${row['monto_total']}"
                                    data-pago="${row['suma_pagado']}"
                                    data-suma-cuota-con-autorizacion="${row['suma_cuotas_con_autorizacion']}"
                                    data-moneda="${row['simbolo']}"
                                    data-cantidad-adjuntos-logisticos="${row['cantidad_adjuntos_logisticos']}"
                                    data-nrodoc="${nroDocumentoDestinatario}"
                                    data-prov="${nombreDestinatario}"
                                    data-cta="${cuentaDestinatario}"
                                    data-cci="${cuentaCCIDestinatario}"
                                    data-tpcta="${tipoCuentaDestinatario}"
                                    data-banco="${bancoDestinatario}"
                                    data-empresa="${row['razon_social_empresa']}" data-idempresa="${row['id_empresa']}"
                                    data-motivo="${encodeURIComponent(row['condicion_pago'])}"
                                    data-comentario-pago-logistica="${row['comentario_pago']}"
                                    data-tiene-pago-en-cuotas="${row['tiene_pago_en_cuotas']}"

                                    data-observacion-requerimiento="${observacionRequerimiento}"
                                    title="Registrar Pago"><i class="fas fa-hand-holding-usd"></i></button>`: ''}`
                                    : ''}
                            ${row['suma_pagado'] > 0 && permisoVer == '1' ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_orden_compra']}" title="Ver detalle de los pagos" >
                                <i class="fas fa-chevron-down"></i></button>`
                                    : ''}

                                    <button type="button" class="adjuntos btn btn-default boton"
                                    data-toggle="tooltip" data-placement="bottom" data-id="${row['id_orden_compra']}" data-codigo="${row['codigo']}"
                                    title="Ver adjuntos"><i class="fas fa-paperclip"></i></button>
                        </div> `;
                        }, 'searchable': false
                },
            ],
            'columnDefs': [
                { 'aTargets': [0], 'sClass': 'invisible' },
                {
                    render: function (data, type, row) {
                        // console.log(row.requerimientos_codigo);
                        // console.log(row.requerimientos);
                        var text = '';
                        row.requerimientos.forEach(element => {
                            text += `<a href="/necesidades/requerimiento/elaboracion/index?id=${element.id_requerimiento}"
                            target="_blank" title="Abrir Requerimiento">${element.codigo}</a>`;
                        });
                        return text;
                    },
                    targets: 2
                },
            ],
            'order': [[11, "asc"], [6, "asc"]]
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
    $('#modal-verAdjuntos input[name="codigo_requerimiento"]').val('');
    $('#modal-verAdjuntos input[name="id_requerimiento_pago"]').val('');
    $('#modal-verAdjuntos input[name="id_orden"]').val('');
    var id = $(this).data('id');
    var codigo = $(this).data('codigo');
    var codigo = $(this).data('codigo');
    $('#modal-verAdjuntos input[name="id_requerimiento_pago"]').val(id)
    $('#modal-verAdjuntos input[name="codigo_requerimiento"]').val(codigo)
    $('#modal-verAdjuntos [data-action="table-body"]').html('');
    $('#modal-verAdjuntos [data-table="adjuntos-pagos"]').html('');
    $(":file").filestyle('clear');
    array_adjuntos=[];
    verAdjuntos(id, codigo);
});
$('#listaOrdenes tbody').on("click", "button.adjuntos", function () {
    $('#modal-verAdjuntos input[name="codigo_requerimiento"]').val('');
    $('#modal-verAdjuntos input[name="id_requerimiento_pago"]').val('');
    $('#modal-verAdjuntos input[name="id_orden"]').val('');
    var id = $(this).data('id');
    var codigo = $(this).data('codigo');
    $('#modal-verAdjuntos input[name="id_orden"]').val(id);
    // $('#modal-verAdjuntos input[name="codigo_requerimiento"]').val(codigo)
    $('#modal-verAdjuntos [data-action="table-body"]').html('');
    $('#modal-verAdjuntos [data-table="adjuntos-pagos"]').html('');
    $(":file").filestyle('clear');
    array_adjuntos=[];
    verAdjuntosOrden(id, codigo);
});

$('#modal-verAdjuntos').on("click", "button.handleClickAnularAdjuntoTesoreria", (e) => {
    anularAdjuntoTesoreria(e.currentTarget);
});

function anularAdjuntoTesoreria(obj){
    let idAdjunto = obj.dataset.idAdjunto;
    if (idAdjunto > 0) {
        $.ajax({
            type: 'POST',
            url: 'anular-adjunto-requerimiento-pago-tesoreria',
            data: { id_adjunto: idAdjunto },
            dataType: 'JSON',
            beforeSend: (data) => { // Are not working with dataType:'jsonp'
                $('#modal-verAdjuntos .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) => {
                if (response.status == 'success') {
                    $('#modal-verAdjuntos .modal-content').LoadingOverlay("hide", true);

                    obj.closest('tr').remove();
                    Lobibox.notify('success', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });

                } else {
                    $('#modal-verAdjuntos .modal-content').LoadingOverlay("hide", true);
                    // console.log(response);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );
                }
            },
            fail: (jqXHR, textStatus, errorThrown) => {
                $('#modal-verAdjuntos .modal-content').LoadingOverlay("hide", true);
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar anular los adjuntos, por favor vuelva a intentarlo',
                    'error'
                );
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    } else {
        Swal.fire(
            '',
            'No existen un ID adjuntos para continuar con la acción',
            'warning'
        );
    }

}
function obteneAdjuntosOrden(id_orden) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-archivos-adjuntos-orden/${id_orden}`,
            dataType: 'JSON',
            beforeSend: (data) => {
            // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("show", {
            //     imageAutoResize: true,
            //     progress: true,
            //     imageColor: "#3c8dbc"
            // });
        },
            success(response) {
                // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                resolve(response);
            },
            error: function (err) {
                // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                reject(err)
            }
        });
    });
}

function verAdjuntos(id, codigo) {
    $('#modal-verAdjuntos').modal({
        show: true
    });
    // document.querySelector("fieldset[id='fieldsetDatosRequerimiento']").classList.remove('oculto');
    document.querySelector("fieldset[id='fieldsetDatosOrden']").classList.add('oculto');
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
            var html = '';
            // if (response.adjuntos_pago.length > 0) {
            //     let tieneAccesoParaEliminarAdjuntos = false;
            //     if (response.id_usuario_propietario_requerimiento > 0 && response.id_usuario_propietario_requerimiento == auth_user.id_usuario) {
            //         tieneAccesoParaEliminarAdjuntos = true;
            //     }
            //     response.adjuntos_pago.forEach(function (element) {
            //         html += `<tr>
            //             <td><a target="_blank" href="/files/tesoreria/pagos/${element.adjunto}">${element.adjunto}</a></td>
            //             <td style="text-align:center;">
            //                 <button type="button" class="btn btn-xs btn-danger handleClickAnularAdjuntoTesoreria" data-id-adjunto="${element.id_requerimiento_pago_adjunto}" title="Anular adjunto" ${tieneAccesoParaEliminarAdjuntos == true ? '' : 'disabled'}><i class="fas fa-times fa-xs"></i></button>
            //             </td>
            //         </tr>`;
            //     });
            //     // $('#modal-verAdjuntos [data-table="adjuntos-pagos"]').html(html);
            // }

            if (response.adjuntos_pagos_complementarios.length > 0) {
                // var html = '';
                let tieneAccesoParaEliminarAdjuntos = false;
                if (response.id_usuario_propietario_requerimiento > 0 && response.id_usuario_propietario_requerimiento == auth_user.id_usuario) {
                    tieneAccesoParaEliminarAdjuntos = true;
                }
                response.adjuntos_pagos_complementarios.forEach(function (element) {
                    html += `<tr>
                        <td><a target="_blank" href="/files/tesoreria/otros_adjuntos/${element.archivo}">${element.archivo}</a></td>
                        <td>${element.fecha_registro??''}</td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-xs btn-danger handleClickAnularAdjuntoTesoreria" data-id-adjunto="${element.id_requerimiento_pago_adjunto}" title="Anular adjunto" ${tieneAccesoParaEliminarAdjuntos == true ? '' : 'disabled'}><i class="fas fa-times fa-xs"></i></button>
                        </td>
                    </tr>`;
                });
                // $('#modal-verAdjuntos [data-table="adjuntos-pagos"]').append(html);
            }
            $('#modal-verAdjuntos [data-table="otros-adjuntos-tesoreria"]').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });


    
    obteneAdjuntosPago(id).then((res) => {
        // console.log(res.data);
        let htmlPago = '';
        if (res.length > 0) {
            (res).forEach(element => {
                    htmlPago += `<tr>
                    <td style="text-align:left;"><p><a href="/files/tesoreria/pagos/${element.adjunto}" target="_blank">${element.adjunto}</a></p>
                    <td>${element.fecha_registro??''}</td>
                    </td>
                    </tr>`;

            });
        }else{
            htmlPago += `<tr>
            <td style="text-align:center;" colspan="2">Sin adjuntos para mostrar</td>
            </tr>`;
        }
        // document.querySelector("div[id='modal-verAdjuntos'] tbody[id='body_archivos_pago']").insertAdjacentHTML('beforeend', htmlPago);
        $('#body_archivos_pago').html(htmlPago);


    }).catch(function (err) {
        console.log(err)
    })
}
function verAdjuntosOrden(id, codigo) {
    $('#modal-verAdjuntos').modal({
        show: true
    });
    // document.querySelector("fieldset[id='fieldsetDatosRequerimiento']").classList.add('oculto');
    document.querySelector("fieldset[id='fieldsetDatosOrden']").classList.remove('oculto');

    $('[name=codigo_requerimiento_pago]').text(codigo);
    $('[name=codigo_requerimiento_pago]').text(codigo);
    $('#adjuntosCabecera tbody').html('');
    $('#adjuntosDetalle tbody').html('');

    $.ajax({
        type: 'GET',
        url: 'verAdjuntosRegistroPagoOrden/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            if (response.adjuntos_pagos_complementarios.length > 0) {
                // var html = '';
                let tieneAccesoParaEliminarAdjuntos = false;
                if (response.id_usuario_propietario_requerimiento > 0 && response.id_usuario_propietario_requerimiento == auth_user.id_usuario) {
                    tieneAccesoParaEliminarAdjuntos = true;
                }
                response.adjuntos_pagos_complementarios.forEach(function (element) {
                    html += `<tr>
                        <td><a target="_blank" href="/files/tesoreria/otros_adjuntos/${element.archivo}">${element.archivo}</a></td>
                        <td>${element.fecha_registro??''}</td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-xs btn-danger handleClickAnularAdjuntoTesoreria" data-id-adjunto="${element.id_requerimiento_pago_adjunto}" title="Anular adjunto" ${tieneAccesoParaEliminarAdjuntos == true ? '' : 'disabled'}><i class="fas fa-times fa-xs"></i></button>
                        </td>
                    </tr>`;
                });
                // $('#modal-verAdjuntos [data-table="adjuntos-pagos"]').append(html);
            }
            $('#modal-verAdjuntos [data-table="otros-adjuntos-tesoreria"]').html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

    $.ajax({
        type: 'GET',
        url: 'verAdjuntosRequerimientoDeOrden/' + id,
        dataType: 'JSON',
        success: function (response) {
            if (response.adjuntoPadre.length > 0) {
                var html = '';
                response.adjuntoPadre.forEach(function (element) {
                    html += `<tr>
                        <td><a target="_blank" href="/files/necesidades/requerimientos/bienes_servicios/cabecera/${element.archivo}">${element.archivo}</a></td>
                        <td>${element.categoria_adjunto != null ? element.categoria_adjunto.descripcion : ''}</td>
                    </tr>`;
                });
                $('#adjuntosCabecera tbody').html(html);
            }
        
            if (response.adjuntoDetalle.length > 0) {
                var html = '';
                response.adjuntoDetalle.forEach(function (element) {
                    html += `<tr>
                        <td><a target="_blank" href="/files/necesidades/requerimientos/bienes_servicios/detalle/${element.archivo}">${element.archivo}</a></td>
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




    obteneAdjuntosOrden(id).then((res) => {

        let htmlAdjunto = '';
        console.log(res);
        if (res.length > 0) {
            (res).forEach(element => {

                    htmlAdjunto+= '<tr id="'+element.id_adjunto+'">'
                        htmlAdjunto+='<td>'
                            htmlAdjunto+='<a href="/files/logistica/comporbantes_proveedor/'+element.archivo+'" target="_blank">'+element.archivo+'</a>'
                        htmlAdjunto+='</td>'

                        htmlAdjunto+='<td>'
                            htmlAdjunto+='<span name="fecha_emision_text">'+element.fecha_emision+'</span><input type="date" class="form-control handleChangeFechaEmision oculto" name="fecha_emision" placeholder="Fecha emisión"  value="'+element.fecha_emision+'">'
                        htmlAdjunto+='</td>'

                        htmlAdjunto+='<td>'
                            htmlAdjunto+='<span name="nro_comprobante_text">'+(element.nro_comprobante !=null && element.nro_comprobante.length > 0?element.nro_comprobante:"")+'</span><input type="text" class="form-control handleChangeNroComprobante oculto" name="nro_comprobante"  placeholder="Nro comprobante" value="'+element.nro_comprobante+'">'
                        htmlAdjunto+='</td>'

                        htmlAdjunto+='<td>'
                            htmlAdjunto+=''+element.descripcion+''
                        htmlAdjunto+='</td>'
                    htmlAdjunto+= '</tr>'

            });
        }else{
            htmlAdjunto = `<tr>
            <td style="text-align:center;" colspan="3">Sin adjuntos para mostrar</td>
            </tr>`;
        }
        $('#body_adjuntos_logisticos').html(htmlAdjunto)


    }).catch(function (err) {
        console.log(err)
    })
    
    obteneAdjuntosPago(id).then((res) => {
        // console.log(res.data);
        let htmlPago = '';
        if (res.length > 0) {
            (res).forEach(element => {
                    htmlPago += `<tr>
                    <td style="text-align:left;"><p><a href="/files/tesoreria/pagos/${element.adjunto}" target="_blank">${element.adjunto}</a></p>
                    </td>
                    </tr>`;

            });
        }else{
            htmlPago += `<tr>
            <td style="text-align:center;" colspan="2">Sin adjuntos para mostrar</td>
            </tr>`;
        }
        // document.querySelector("div[id='modal-verAdjuntos'] tbody[id='body_archivos_pago']").insertAdjacentHTML('beforeend', htmlPago);
        $('#body_archivos_pago').html(htmlPago);


    }).catch(function (err) {
        console.log(err)
    })
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

function actualizarEstadoPago() {

    $.ajax({
        type: 'GET',
        url: 'actualizarEstadoPago',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify('success', {
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response
            });
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
// var array_adjuntos = [];
$(document).on('change','[data-action="adjuntos"]',function () {

    $.each($(this)[0].files, function (index, element) {
        array_adjuntos.push(element);
    });
    pesoArchivos();
    adjuntosSeleccionados();
});
function pesoArchivos() {
    var peso_archivo= 0,
        peso_total = 0;
    $.each(array_adjuntos, function (indexInArray, valueOfElement) {
        peso_archivo=peso_archivo+valueOfElement.size;
    });
    peso_total = peso_archivo/(1000000);
    $('#modal-verAdjuntos #peso-estimado').text(peso_total.toFixed(2)+'MB');

    if (peso_archivo<=2000000) {
        $('.guardar-adjuntos').removeAttr('disabled');

    }else{
        $('.guardar-adjuntos').attr('disabled',true);
    }
}
function adjuntosSeleccionados() {
    var html='';
    $.each(array_adjuntos, function (indexInArray, valueOfElement) {
        html+='<tr data-key="'+indexInArray+'">'
            html+='<td>'
                html+=valueOfElement.name
            html+='</td>'
            html+='<td><buton class="btn btn-danger btn-xs" data-action="eliminar-adjunto" data-key="'+indexInArray+'"><i class="fas fa-trash-alt"></i></button></td>'
        html+='</tr>'
    });
    $('[data-action="table-body"]').html(html);
}
$(document).on('click','[data-action="eliminar-adjunto"]',function () {
    var key_item = $(this).attr('data-key');
    array_adjuntos = array_adjuntos.filter((item, key) => key !== parseInt(key_item));
    if (array_adjuntos.length===0) {
        $('[name="adjuntos[]"]').val('');
    }
    adjuntosSeleccionados();
    pesoArchivos();
});
$(document).on('submit','[data-form="guardar-adjuntos"]',function (e) {
    e.preventDefault();
    var data_forma_adjuntos = new FormData($(this)[0]);
    $.each(array_adjuntos, function (indexInArray, valueOfElement) {
        data_forma_adjuntos.append('archivos[]', valueOfElement);
    });
    Swal.fire({
        title: 'Adjuntos',
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
                type: 'POST',
                url: 'guardar-adjuntos-tesoreria',
                data: data_forma_adjuntos,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    console.log(data);
                }
            }).done(function(response) {
                console.log(response);
                return response
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });

          },
      }).then((result) => {
        if (result.isConfirmed) {
            console.log(result.value.status);
            if (result.value.status=='success') {
                $('#modal-verAdjuntos').modal('hide');
            }
        }
    })

});

$('#listaOrdenes tbody').on('click', 'td button.visualizarPagosEnCuotas', function () {
    var tr = $(this).closest('tr');
    var row = tableOrdenes.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagosEnCuotas(iTableCounter, id, row, "orden");
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

function formatPagosEnCuotas(table_id, id, row, tipo) {
    // console.log(tipo)
    $.ajax({
        type: 'GET',
        url: 'listarPagosEnCuotas/' + tipo + '/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var i = 1;

            let orden = response.orden;
            let numeroCuotas = response.numero_de_cuotas;
            let detalle = response.detalle;

            if (response.hasOwnProperty('detalle') && detalle.length > 0) {
                detalle.forEach(element => {
                    enlaceAdjunto=[];
                    (element.adjuntos).forEach(element => {
                        enlaceAdjunto.push('<a href="/files/logistica/comporbantes_proveedor/'+element.archivo+'" target="_blank">'+element.archivo+'</a>');
                    });
                    if(element.estado.id_requerimiento_pago_estado != 7){
                        html += '<tr id="' + element.id_pago_cuota_detalle + '">' +
                            '<td style="border: none; text-align: center">' + (element.monto_cuota !== null ? element.monto_cuota : '') + '</td>' +
                            '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                            '<td style="border: none; text-align: center">' +  (numeroCuotas>1?(i+'/'+numeroCuotas):i) + '</td>' +
                            '<td style="border: none; text-align: center">' + enlaceAdjunto.toString().replace(",","<br>") + '</td>' +
                            '<td style="border: none; text-align: center">' + element.creado_por.nombre_corto + '</td>' +
                            '<td style="border: none; text-align: center">' + element.fecha_registro + '</td>' +
                            '<td style="border: none; text-align: center">' + (element.fecha_autorizacion??'') + '</td>' +
                            '<td style="border: none; text-align: center">' + element.estado.descripcion + '</td>' +
                            '<td style="border: none; text-align: center">' +
                            `<button type = "button" class= "btn btn-${element.fecha_autorizacion !=null?'success':'info'} boton" data-toggle="tooltip"
                                data - placement="bottom"
                                onClick = "enviarPagoEnCuotas(${orden.id_orden_compra},${element.id_pago_cuota_detalle},'${tipo}',event)" title = "${element.fecha_autorizacion !=null?'Pago Autorizado':'Autorizar pago'}" ${element.fecha_autorizacion !=null?'disabled':''}>
                                ${element.fecha_autorizacion !=null?'<i class="fas fa-check-double"></i> Autorizado':'<i class="fas fa-check"></i> Autorizar'}
                                </button>` +
                                // (element.estado.id_requerimiento_pago_estado==5?`
                                // <button type = "button" class= "btn btn-danger boton" data-toggle="tooltip"
                                // data - placement="bottom"
                                // onClick = "revertirPagoEnCuotas(${orden.id_orden_compra},${element.id_pago_cuota_detalle},'${tipo}',event)" title = "Revertir autorización">
                                // <i class="fas fa-undo-alt"></i>
                                // </button>`
                                // :'')
                            '</td>' +
                            '</tr>';
                        i++;

                    }
                });
                var tabla = `<table class= "table table-sm" style = "border: none;"
                id = "detalle_${table_id}" >
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">Monto a pagar</th>
                        <th style="border: none;">Observación</th>
                        <th style="border: none;">Cuotas</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                        <th style="border: none;">Fecha Autorización</th>
                        <th style="border: none;">Estado</th>
                        <th style="border: none;">Acción</th>
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

function obteneAdjuntosPago(idRequerimiento) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `lista-adjuntos-pago/${idRequerimiento}`,
            dataType: 'JSON',
            beforeSend: (data) => {
            $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDePagos').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
        },
            success(response) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDePagos').LoadingOverlay("hide", true);
                resolve(response);
            },
            error: function (err) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDePagos').LoadingOverlay("hide", true);
                reject(err)
            }
        });
    });
}
