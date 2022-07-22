function listar_doc_compra() {

    var vardataTables = funcDatatables();
    $('#listaComprobantesCompra').DataTable({
        dom: vardataTables[1],
        buttons: vardataTables[2],
        language: vardataTables[0],
        // 'destroy': true,
        serverSide: true,
        ajax: {
            url: 'listar_docs_compra',
            type: 'POST'
        },
        columns: [
            { data: 'id_doc_com' },
            { data: 'razon_social_empresa', name: 'empresa.razon_social' },
            { data: 'tipo_documento', name: 'cont_tp_doc.descripcion' },
            { data: 'serie' },
            { data: 'numero' },
            { data: 'codigo_softlink' },
            { data: 'nro_documento', name: 'adm_contri.nro_documento' },
            { data: 'razon_social', name: 'adm_contri.razon_social' },
            { data: 'fecha_emision' },
            { data: 'condicion_pago', name: 'log_cdn_pago.descripcion' },
            { data: 'fecha_vcmto' },
            { data: 'simbolo', name: 'sis_moneda.simbolo' },
            { data: 'total_a_pagar' },
            // {
            //     data: 'estado_doc', name: 'adm_estado_doc.estado_doc',
            //     'render': function (data, type, row) {
            //         return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
            //     }
            // },
            {
                searchable: false,
                orderable: false,
                render: function (data, type, row) {
                    return `<div class="btn-group" role="group">
                                ${row['estado'] == 1 ?
                            `<button type="button" class="ver_doc btn btn-info btn-xs" data-toggle="tooltip"
                                    data-placement="bottom" title="Ver Comprobante" data-doc="${row['id_doc_com']}">
                                    <i class="fas fa-file-medical"></i></button>
                                `: ''}
                                <button type="button" style="padding-left:8px;padding-right:7px;" class="enviar btn btn-warning btn-xs" data-toggle="tooltip"
                                    data-placement="bottom" data-id="${row['id_doc_com']}" title="Enviar a softlink" >
                                    <i class="fas fa-share"></i></button>
                            </div>`;
                    // <button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-warning btn-xs" data-toggle="tooltip"
                    // data-placement="bottom" data-id="${row['id_doc_com']}" data-cod="${row['serie']+'-'+row['numero']}" title="Mandar A Pago" >
                    // <i class="fas fa-hand-holding-usd"></i></button>
                }
            },
        ],

        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });

    $('#listaComprobantesCompra tbody').on("click", "button.enviar", function () {
        var id = $(this).data('id');

        Swal.fire({
            title: "¿Está seguro que desea enviar el documento a Softlink?",
            // text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, Enviar"
        }).then(result => {
            if (result.isConfirmed) {
                enviarComprobanteSoftlink(id);
            }
        });
    });

    $('#listaComprobantesCompra tbody').on("click", "button.pago", function () {
        var id = $(this).data('id');
        var cod = $(this).data('cod');
        var rspta = confirm('¿Está seguro que desea mandar a Pago el Doc ' + cod + '?');

        if (rspta) {
            documentoAPago(id);
        }
    });

    $('#listaComprobantesCompra tbody').on("click", "button.ver_doc", function () {
        var id_doc = $(this).data('doc');
        console.log('id_doc' + id_doc);
        documentosVer(id_doc);
    });
}

function enviarComprobanteSoftlink(id) {
    $.ajax({
        type: 'GET',
        url: 'enviarComprobanteSoftlink/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            $('#listaComprobantesCompra').DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function documentoAPago(id) {
    $.ajax({
        type: 'GET',
        url: 'documentoAPago/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                alert('Se envió correctamente a Pago');
                $('#listaComprobantesCompra').DataTable().ajax.reload(null, false);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function actualizarSedesFaltantes() {
    $.ajax({
        type: 'GET',
        url: 'actualizarSedesFaltantes',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Se encontraron ' + response['nro_docs'] + ' documentos. Se actualizaron ' + response['actualizaciones'] + ' comprobantes correctamente.'
            });
            $('#listaComprobantesCompra').DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function actualizarProveedores() {
    $.ajax({
        type: 'GET',
        url: 'actualizarProveedorComprobantes',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Se encontraron ' + response['nro_docs'] + ' documentos. Se actualizaron ' + response['nro_actualizados'] + ' documentos.'
            });
            $('#listaComprobantesCompra').DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function migrarComprobantesSoftlink() {
    $.ajax({
        type: 'GET',
        url: 'migrarComprobantesSoftlink',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Se enviaron ' + response['nro_docs'] + ' documentos.'
            });
            $('#listaComprobantesCompra').DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function migrarItemsComprobantesSoftlink() {
    $.ajax({
        type: 'GET',
        url: 'migrarItemsComprobantesSoftlink',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Se actualizaron los documentos.'
            });
            $('#listaComprobantesCompra').DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularComprobanteCompra(obj) {
    let id_doc_com = obj.dataset.idDocCom;
    anular_doc_compra(id_doc_com);
}

