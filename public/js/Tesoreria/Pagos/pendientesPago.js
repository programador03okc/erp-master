class RequerimientoPago {
    constructor(permisoConfirmarDenegarPago) {
        this.permisoConfirmarDenegarPago = permisoConfirmarDenegarPago;
        this.listarRequerimientos();
        this.listarComprobantes();
        this.listarOrdenes();
    }

    listarRequerimientos() {
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
                { 'data': 'razon_social_empresa', 'name': 'empresa.razon_social' },
                { 'data': 'codigo', 'name': 'requerimiento_pago.codigo', 'className': 'text-center' },
                { 'data': 'grupo_descripcion', 'name': 'sis_grupo.descripcion' },
                { 'data': 'concepto', 'name': 'requerimiento_pago.concepto' },
                { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                // { 'data': 'fecha_registro', 'name': 'requerimiento_pago.fecha_registro', 'className': 'text-center' },
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
                        return '<span class="label label-' + (row['id_estado'] == 9 ? 'primary' : 'default') + '">' + row['estado_doc'] + '</span>'
                    }
                },
                {
                    'render':
                        function (data, type, row) {
                            return `<div class="btn-group" role="group">
                            ${row['id_estado'] == 1 ?
                                    `<button type="button" class="pago btn btn-danger boton" data-toggle="tooltip" data-placement="bottom" 
                                    data-id="${row['id_requerimiento_pago']}" data-cod="${row['codigo']}" data-tipo="requerimiento"
                                    data-total="${row['monto_total']}" data-pago="${row['suma_pagado']}" data-nrodoc="${row['nro_documento']}"
                                    data-moneda="${row['simbolo']}" data-prov="${encodeURIComponent(row['razon_social'])}" 
                                    data-cta="${row['nro_cuenta']}" data-tpcta="${row['tipo_cuenta']}" 
                                    title="Registrar Pago"> 
                                    <i class="fas fa-hand-holding-usd"></i> </button>`: ''}
                            ${row['suma_pagado'] > 0 ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_requerimiento_pago']}" title="Ver detalle de los pagos" >
                                    <i class="fas fa-chevron-down"></i></button>`: ''}
                            </div>`;

                        }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });

    }

    listarOrdenes() {
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
                { 'data': 'razon_social_empresa', 'name': 'empresa.razon_social' },
                { 'data': 'codigo' },
                { 'data': 'codigo_softlink' },
                { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
                {
                    'render': function (data, type, row) {
                        return (row['fecha'] !== null ? formatDate(row['fecha']) : '');
                    }, 'className': 'text-center', 'searchable': false
                },
                { 'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion' },
                { 'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta' },
                { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center' },
                {
                    'render': function (data, type, row) {
                        return (row['suma_total'] !== null ? formatNumber.decimal(row['suma_total'], '', -2) : '0.00');
                    }, 'className': 'text-right'
                },
                {
                    'render': function (data, type, row) {
                        var pagado = formatDecimal(row['suma_pagado'] !== null ? row['suma_pagado'] : 0);
                        var total = formatDecimal(row['suma_total']);
                        var por_pagar = (total - pagado);
                        return por_pagar > 0 ? '<strong>' + formatNumber.decimal(por_pagar, '', -2) + '</strong>' : formatNumber.decimal(por_pagar, '', -2);
                    }, 'className': 'text-right celestito'
                },
                {
                    'render': function (data, type, row) {
                        return '<span class="label label-' + (row['estado'] == 9 ? 'primary' : 'default') + '">' + row['estado_doc'] + '</span>'
                    }
                },
                {
                    'render':
                        function (data, type, row) {
                            return `<div class="btn-group" role="group">
                    ${row['estado'] !== 9 ?
                                    `<button type="button" class="pago btn btn-danger boton" data-toggle="tooltip" data-placement="bottom" 
                                    data-id="${row['id_orden_compra']}" data-cod="${row['codigo']}" data-tipo="orden"
                                    data-total="${row['suma_total']}" data-pago="${row['suma_pagado']}" data-nrodoc="${row['nro_documento']}" 
                                    data-moneda="${row['simbolo']}" data-prov="${encodeURIComponent(row['razon_social'])}" 
                                    data-cta="${row['nro_cuenta']}" data-tpcta="${row['tipo_cuenta']}"
                                    title="Registrar Pago" >
                                <i class="fas fa-hand-holding-usd"></i></button>`: ''}
                    ${row['suma_pagado'] > 0 ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_orden_compra']}" title="Ver detalle de los pagos" >
                                <i class="fas fa-chevron-down"></i></button>`: ''}
                        </div>`;
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
                                    `<button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-danger boton" data-toggle="tooltip" data-placement="bottom" 
                                    data-id="${row['id_doc_com']}" data-cod="${row['serie'] + '-' + row['numero']}" data-tipo="comprobante"
                                    data-total="${row['total_a_pagar']}" data-pago="${row['suma_pagado']}" data-nrodoc="${row['nro_documento']}"
                                    data-moneda="${row['simbolo']}" data-prov="${encodeURIComponent(row['razon_social'])}" 
                                    data-cta="${row['nro_cuenta']}" title="Registrar Pago"> 
                                    <i class="fas fa-hand-holding-usd"></i> </button>`: ''}
                            ${row['suma_pagado'] > 0 ?
                                    `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_doc_com']}" title="Ver detalle de los pagos" >
                                    <i class="fas fa-chevron-down"></i></button>`: ''}
                            </div>`;

                        }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });

    }

}

$('#listaRequerimientos tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
    /*
    var id_req = $(this).data('id');
    var codigo = $(this).data('cod');
    var total = $(this).data('total');
    var pago = ($(this).data('pago') !== null ? parseFloat($(this).data('pago')) : 0);
    var moneda = $(this).data('moneda');
    var nrodoc = $(this).data('nrodoc');
    var prov = $(this).data('prov');
    var tpcta = $(this).data('tpcta');
    var cta = $(this).data('cta');

    var total_pago = formatDecimal(parseFloat(total) - pago);
    console.log(cta);

    const $modal = $('#modal-procesarPago');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    $('[name=id_requerimiento_pago]').val(id_req);
    $('[name=id_oc]').val('');
    $('[name=id_doc_com]').val('');
    $('[name=codigo]').val(codigo);
    $('[name=cod_serie_numero]').text(codigo);

    $('[name=total_pago]').val(total_pago);
    $('[name=total]').val(total_pago);
    $('[name=total_pagado]').text(formatNumber.decimal(pago, moneda, -2));
    $('[name=monto_total]').text(formatNumber.decimal(total, moneda, -2));

    $('[name=observacion]').val('');
    $('[name=simbolo]').val(moneda);
    $('[name=nro_documento]').text(nrodoc);
    $('[name=razon_social]').text(decodeURIComponent(prov));
    $('[name=tp_cta_bancaria]').text(cta !== 'undefined' ? tpcta : '');
    $('[name=cta_bancaria]').text(cta !== 'undefined' ? cta : '');

    $('#submit_procesarPago').removeAttr('disabled');*/
});

$('#listaOrdenes tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
    /*
    var id_oc = $(this).data('id');
    var codigo = $(this).data('cod');
    var total = $(this).data('total');
    var pago = ($(this).data('pago') !== null ? parseFloat($(this).data('pago')) : 0);
    var moneda = $(this).data('moneda');
    var nrodoc = $(this).data('nrodoc');
    var prov = $(this).data('prov');
    var tpcta = $(this).data('tpcta');
    var cta = $(this).data('cta');

    // var monto_pago = (pago !== null ? parseFloat(pago) : 0);
    var total_pago = formatDecimal(parseFloat(total) - pago);
    console.log(total_pago);

    const $modal = $('#modal-procesarPago');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    $('[name=id_requerimiento_pago]').val('');
    $('[name=id_oc]').val(id_oc);
    $('[name=id_doc_com]').val('');
    $('[name=codigo]').val(codigo);
    $('[name=cod_serie_numero]').text(codigo);

    $('[name=total_pago]').val(total_pago);
    $('[name=total]').val(total_pago);
    $('[name=total_pagado]').text(formatNumber.decimal(pago, moneda, -2));
    $('[name=monto_total]').text(formatNumber.decimal(total, moneda, -2));

    $('[name=observacion]').val('');
    $('[name=simbolo]').val(moneda);
    $('[name=nro_documento]').text(nrodoc);
    $('[name=razon_social]').text(decodeURIComponent(prov));
    $('[name=tp_cta_bancaria]').text(cta !== 'undefined' ? tpcta : '');
    $('[name=cta_bancaria]').text(cta !== 'undefined' ? cta : '');

    $('#submit_procesarPago').removeAttr('disabled');*/
});

$('#listaComprobantes tbody').on("click", "button.pago", function () {
    openRegistroPago($(this));
    /*
    var id_doc_com = $(this).data('id');
    var codigo = $(this).data('cod');
    var total = $(this).data('total');
    var pago = ($(this).data('pago') !== null ? parseFloat($(this).data('pago')) : 0);
    var moneda = $(this).data('moneda');
    var nrodoc = $(this).data('nrodoc');
    var prov = $(this).data('prov');
    var tpcta = $(this).data('tpcta');
    var cta = $(this).data('cta');

    var total_pago = formatDecimal(parseFloat(total) - pago);
    console.log(nrodoc);

    const $modal = $('#modal-procesarPago');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    console.log(codigo);
    $('[name=id_doc_com]').val(id_doc_com);
    $('[name=id_requerimiento_pago]').val('');
    $('[name=id_oc]').val('');
    $('[name=codigo]').val(codigo);
    $('[name=cod_serie_numero]').text(codigo);

    $('[name=total_pago]').val(total_pago);
    $('[name=total]').val(total_pago);
    $('[name=total_pagado]').text(formatNumber.decimal(pago, moneda, -2));
    $('[name=monto_total]').text(formatNumber.decimal(total, moneda, -2));

    $('[name=observacion]').val('');
    $('[name=simbolo]').val(moneda);
    $('[name=nro_documento]').text(nrodoc);
    $('[name=razon_social]').text(decodeURIComponent(prov));
    $('[name=tp_cta_bancaria]').text(cta !== 'undefined' ? tpcta : '');
    $('[name=cta_bancaria]').text(cta !== 'undefined' ? cta : '');

    $('#submit_procesarPago').removeAttr('disabled');*/
});

function openRegistroPago(data) {
    var id = data.data('id');
    var tipo = data.data('tipo');
    var codigo = data.data('cod');
    var total = data.data('total');
    var pago = (data.data('pago') !== null ? parseFloat(data.data('pago')) : 0);
    var moneda = data.data('moneda');
    var nrodoc = data.data('nrodoc');
    var prov = data.data('prov');
    var tpcta = data.data('tpcta');
    var cta = data.data('cta');

    var total_pago = formatDecimal(parseFloat(total) - pago);
    console.log(cta);

    const $modal = $('#modal-procesarPago');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    if (tipo == 'requerimiento') {
        $('[name=id_requerimiento_pago]').val(id);
        $('[name=id_oc]').val('');
        $('[name=id_doc_com]').val('');
    }
    else if (tipo == 'orden') {
        $('[name=id_requerimiento_pago]').val('');
        $('[name=id_oc]').val(id);
        $('[name=id_doc_com]').val('');
    }
    else if (tipo == 'comprobante') {
        $('[name=id_requerimiento_pago]').val('');
        $('[name=id_oc]').val('');
        $('[name=id_doc_com]').val(id);
    }

    $('[name=codigo]').val(codigo);
    $('[name=cod_serie_numero]').text(codigo);

    $('[name=total_pago]').val(total_pago);
    $('[name=total]').val(total_pago);
    $('[name=total_pagado]').text(formatNumber.decimal(pago, moneda, -2));
    $('[name=monto_total]').text(formatNumber.decimal(total, moneda, -2));

    $('[name=observacion]').val('');
    $('[name=simbolo]').val(moneda);
    $('[name=nro_documento]').text(nrodoc !== 'undefined' ? nrodoc : '');
    $('[name=razon_social]').text(decodeURIComponent(prov));
    $('[name=tp_cta_bancaria]').text(cta !== 'undefined' ? tpcta : '');
    $('[name=cta_bancaria]').text(cta !== 'undefined' ? cta : '');

    $('#submit_procesarPago').removeAttr('disabled');
}

$("#form-procesarPago").on("submit", function (e) {
    e.preventDefault();
    $('#submit_procesarPago').attr('disabled', 'true');
    procesarPago();
});

function procesarPago() {
    var formData = new FormData($('#form-procesarPago')[0]);
    var id_oc = $('[name=id_oc]').val();
    var id_doc_com = $('[name=id_doc_com]').val();
    var id_requerimiento_pago = $('[name=id_requerimiento_pago]').val();
    console.log(formData);

    $.ajax({
        type: 'POST',
        url: 'procesarPago',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('#modal-procesarPago').modal('hide');

            if (id_oc !== '') {
                $('#listaOrdenes').DataTable().ajax.reload();
            }
            else if (id_doc_com !== '') {
                $('#listaComprobantes').DataTable().ajax.reload();
            }
            else if (id_requerimiento_pago !== '') {
                $('#listaRequerimientos').DataTable().ajax.reload();
            }
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Pago registrado con éxito.'
            });

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


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
        formatPagosOrdenes(iTableCounter, id, row);
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
        formatPagosComprobante(iTableCounterComp, id, row);
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
        formatPagosRequerimientos(iTableCounterReq, id, row);
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

function formatPagosOrdenes(table_id, id, row) {
    $.ajax({
        type: 'GET',
        url: 'pagosOrdenes/' + id,
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
                        '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                        '<td style="border: none; text-align: right">' + element.simbolo + '</td>' +
                        '<td style="border: none; text-align: right">' + formatDecimal(element.total_pago) + '</td>' +
                        '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/' + element.adjunto + '" target="_blank">' + (element.adjunto !== null ? element.adjunto : '') + '</a></td>' +
                        '<td style="border: none; text-align: center">' + element.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + element.fecha_registro + '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Moneda</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table>`;
            }
            else {
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function formatPagosComprobante(table_id, id, row) {
    $.ajax({
        type: 'GET',
        url: 'pagosComprobante/' + id,
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
                        '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                        '<td style="border: none; text-align: right">' + element.simbolo + '</td>' +
                        '<td style="border: none; text-align: right">' + formatDecimal(element.total_pago) + '</td>' +
                        '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/' + element.adjunto + '" target="_blank">' + (element.adjunto !== null ? element.adjunto : '') + '</a></td>' +
                        '<td style="border: none; text-align: center">' + element.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + element.fecha_registro + '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Moneda</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table>`;
            }
            else {
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function formatPagosRequerimientos(table_id, id, row) {
    $.ajax({
        type: 'GET',
        url: 'pagosRequerimientos/' + id,
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
                        '<td style="border: none; text-align: center">' + element.observacion + '</td>' +
                        '<td style="border: none; text-align: right">' + element.simbolo + '</td>' +
                        '<td style="border: none; text-align: right">' + formatDecimal(element.total_pago) + '</td>' +
                        '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/' + element.adjunto + '" target="_blank">' + (element.adjunto !== null ? element.adjunto : '') + '</a></td>' +
                        '<td style="border: none; text-align: center">' + element.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + element.fecha_registro + '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Moneda</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table>`;
            }
            else {
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
