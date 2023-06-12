var table_requerimientos_pagados, table_ordenes;
let $data={
    mes:"01",
    division: ""
};

$(document).ready(function () {
    $data.mes = $('[data-form="buscar"]').find('[name="mes"]').val();
    $data.division = $('[data-form="buscar"]').find('[name="division"]').val();
    listarRequerimientosPagos();
    listarOrdenes();
});
$('[data-form="buscar"]').on("submit", (e) => {
    e.preventDefault();
    let data = $(e.currentTarget).serialize();
    $data.mes = $('[data-form="buscar"]').find('[name="mes"]').val();
    $data.division = $('[data-form="buscar"]').find('[name="division"]').val();
    $(e.currentTarget).find('button[type="submit"]').attr('disabled','true');
    listarRequerimientosPagos();
    listarOrdenes();
    $(e.currentTarget).find('button[type="submit"]').removeAttr('disabled','true');
});

// en lista las ordenes
function listarOrdenes() {
    var vardataTables = funcDatatables();
    table_ordenes = $("#lista-ordenes").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        lengthChange: false,
        dom: vardataTables[1],
        buttons:[],
        ajax: {
            url: "listar-ordenes",
            type: "POST",
            data:$data,
            beforeSend: data => {
                $("#lista-ordenes").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        columns: [
            {data: 'id_orden_compra', name:"id_orden_compra" },

            {data: 'codigo', name:"codigo" , class:"text-center"},
            {data: 'fecha_autorizacion', name:"fecha_autorizacion" , class:"text-center"},
            {data: 'comentario_pago', name:"comentario_pago" , class:"text-center"},
            {
                data: 'monto_total', name:"monto_total",
                render : function(data, type, row){
                    let total = (row['id_moneda']===1?'S/.':'$')+row['monto_total']
                    return total
                }
                , class:"text-center"},
            {
                render: function (data, type, row) {
                    html='';
                    html+='<button type="button" class="btn text-black btn-default botonList detalle-orden" data-id="'+row['id_orden_compra']+'" title="Ver detalle"><i class="fas fa-chevron-down"></i></button>'

                    html+='';
                    return html;
                },
                className: "text-center"
            }
        ],
        order: [[0, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        "drawCallback": function (settings) {

            $("#lista-ordenes").LoadingOverlay("hide", true);
        }
    });
}
// en lista los requerimientos de pagos
function listarRequerimientosPagos() {
    var vardataTables = funcDatatables();
    table_requerimientos_pagados = $("#lista-requerimientos-pagos").DataTable({
        language: vardataTables[0],
        destroy: true,
        pageLength: 10,
        serverSide: true,
        lengthChange: false,
        dom: vardataTables[1],
        buttons:[],
        ajax: {
            url: "listar-requerimientos-pagos",
            type: "POST",
            data:$data,
            beforeSend: data => {
                $("#lista-requerimientos-pagos").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        columns: [
            {data: 'id_requerimiento_pago', name:"id_requerimiento_pago" },
            {data: 'codigo', name:"codigo" , class:"text-center"},
            {data: 'concepto', name:"concepto" , class:"text-center"},
            // {data: 'fecha_registro', name:"fecha_registro" , class:"text-center"},
            {data: 'fecha_autorizacion', name:"fecha_autorizacion" , class:"text-center"},
            {data: 'nombre_trabajador', name:"nombre_trabajador" , class:"text-center"},
            {data: 'monto_total', name:"monto_total" , class:"text-center"},
            {
                render: function (data, type, row) {
                    html='';
                    html+='<button type="button" class="btn text-black btn-default botonList detalle-requerimiento-pago" data-id="'+row['id_requerimiento_pago']+'" title="Ver detalle" data-mes="'+row['mes']+'"><i class="fas fa-chevron-down"></i></button>'

                    html+='';
                    return html;
                },
                className: "text-center"
            }
        ],
        order: [[0, "desc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        "drawCallback": function (settings) {

            $("#lista-requerimientos-pagos").LoadingOverlay("hide", true);
        }
    });
}
var iTableCounter = 1;
var iTableCounterPago = 1;
$(document).on('click','.detalle-orden',function (e) {
    e.preventDefault();

    var counter = 1;

    let tr = (e.currentTarget).closest('tr');
    var row = table_ordenes.row(tr);
    var id = $(e.currentTarget).attr('data-id');
    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.classList.remove('shown');
    }
    else {
        // Open this row
        //    row.child( format(iTableCounter, id) ).show();
        buildFormat((e.currentTarget), iTableCounter, id, row, 'orden');
        tr.classList.add('shown');
        // try datatable stuff
        oInnerTable = $('#lista-ordenes_' + iTableCounter).dataTable({
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
            columns: [
            ]
        });
        iTableCounter = iTableCounter + 1;
    }

});
$(document).on('click','.detalle-requerimiento-pago',function (e) {
    e.preventDefault();

    var counter = 1;

    let tr = (e.currentTarget).closest('tr');
    var row = table_requerimientos_pagados.row(tr);
    var id = $(e.currentTarget).attr('data-id');
    var mes = $(e.currentTarget).attr('data-mes');
    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.classList.remove('shown');
    }
    else {
        // Open this row
        //    row.child( format(iTableCounter, id) ).show();
        buildFormat((e.currentTarget), iTableCounterPago, id, row, 'pago', mes);
        tr.classList.add('shown');
        // try datatable stuff
        oInnerTable = $('#lista-requerimientos-pagos_' + iTableCounterPago).dataTable({
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
            columns: [
            ]
        });
        iTableCounterPago = iTableCounterPago + 1;
    }

});
function buildFormat(obj, table_id, id, row, key, mes) {
    obj.setAttribute('disabled', true);
    switch (key) {
        case 'orden':
            $.ajax({
                type: 'GET',
                url:`/logistica/gestion-logistica/compras/ordenes/listado/detalle-orden/${id}`,
                dataType: 'JSON',
                success(response) {
                    obj.removeAttribute('disabled');
                    construirDetalleOrdenElaboradas(table_id, row, response, mes);
                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
            });
        break;

        case 'pago':
            $.ajax({
                type: 'GET',
                url:`detalle-requerimiento-pago/${id}`,
                dataType: 'JSON',
                success(response) {
                    obj.removeAttribute('disabled');
                    construirDetalleRequerimientosPagos(table_id, row, response, mes);
                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
            });
        break;
    }

}

function construirDetalleOrdenElaboradas(table_id, row, response,mes) {
    var html = '';
    if (response.length > 0) {
        response.forEach(function (element) {
            let stock_comprometido = 0;
            (element.reserva).forEach(reserva => {
                if (reserva.estado == 1) {
                    stock_comprometido += parseFloat(reserva.stock_comprometido);
                }
            });

            html += `<tr>
                <td style="border: none;">${(element.nro_orden !== null ? `<a  style="cursor:pointer;" class="handleClickObtenerArchivos" data-id="${element.id_oc_propia}" data-tipo="${element.tipo_oc_propia}">${element.nro_orden}</a>` : '')}</td>
                <td style="border: none;">${element.codigo_oportunidad !== null ? element.codigo_oportunidad : ''}</td>
                <td style="border: none;">${element.nombre_entidad !== null ? element.nombre_entidad : ''}</td>
                <td style="border: none;">${element.nombre_corto_responsable !== null ? element.nombre_corto_responsable : ''}</td>
                <td style="border: none;"><a href="/necesidades/requerimiento/elaboracion/index?id=${element.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${element.codigo_req ?? ''}</a></td>
                <td style="border: none;">${element.codigo ?? ''}</td>
                <td style="border: none;">${element.part_number ?? ''}</td>
                <td style="border: none;">${element.descripcion ? element.descripcion : (element.descripcion_adicional ? element.descripcion_adicional : '')}</td>
                <td style="border: none;">${element.cantidad ? element.cantidad : ''}</td>
                <td style="border: none;">${element.abreviatura ? element.abreviatura : ''}</td>
                <td style="border: none;">${element.moneda_simbolo}${$.number(element.precio, 2,".",",")}</td>
                <td style="border: none;">${element.moneda_simbolo}${$.number((element.cantidad * element.precio), 2,".",",")}</td>
                <td style="border: none; text-align:center;">${stock_comprometido != null ? stock_comprometido : ''}</td>

                <td style="border: none; text-align:center;"></td>
                    <button type="button" class="btn text-black btn-flat botonList" data-id-requerimiento-pago="`+element.id_requerimiento_pago+`" data-id-requerimiento-pago-detalle="`+element.id_requerimiento_pago_detalle+`" title="Asignar a partida" data-original-title="Ver" data-action="asignar-partida" data-tap="orden" data-mes="`+mes+`">
                        <i class="fas fa-share-square"></i>
                    </button>
                </tr>`;
        });
        var tabla = `<table class="table table-sm" style="border: none; font-size:x-small;"
            id="detalle_${table_id}">
            <thead style="color: black;background-color: #c7cacc;">
                <tr>
                    <th style="border: none;">O/C</th>
                    <th style="border: none;">Cod.CDP</th>
                    <th style="border: none;">Cliente</th>
                    <th style="border: none;">Responsable</th>
                    <th style="border: none;">Cod.Req.</th>
                    <th style="border: none;">Código</th>
                    <th style="border: none;">Part number</th>
                    <th style="border: none;">Descripción</th>
                    <th style="border: none;">Cantidad</th>
                    <th style="border: none;">Und.Med</th>
                    <th style="border: none;">Prec.Unit.</th>
                    <th style="border: none;">Total</th>
                    <th style="border: none;">Reserva almacén</th>
                    <th style="border: none;">-</th>
                </tr>
            </thead>
            <tbody style="background: #e7e8ea;">${html}</tbody>
            </table>`;
    } else {
        var tabla = `<table class="table table-sm" style="border: none;"
            id="detalle_${table_id}">
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
            </table>`;
    }
    row.child(tabla).show();
}
function construirDetalleRequerimientosPagos(table_id, row, response, mes) {
    var html = '';
    if (response.length > 0) {

        var tabla = `<table class="table table-sm contenido-detalle" style="border: none; font-size:x-small;height: 0;
        transition: .3s height;"
            id="detalle_${table_id}">
            <thead style="color: black;background-color: #c7cacc;">
                <tr>
                    <th style="border: none;">Descripción</th>
                    <th style="border: none;">Cantidad</th>
                    <th style="border: none;">Precio Unitario</th>
                    <th style="border: none;">Sub total</th>
                    <th style="border: none;"> - </th>
                </tr>
            </thead>
            <tbody style="background: #e7e8ea;">`;
                $.each(response, function (index, element) {
                    tabla+=`<tr>
                        <td class="text-center">`+element.descripcion+`</td>
                        <td class="text-center">`+element.cantidad+`</td>
                        <td class="text-center">`+element.precio_unitario+`</td>
                        <td class="text-center">`+element.subtotal+`</td>
                        <td class="text-center">
                            <button type="button" class="btn text-black btn-flat botonList"
                            data-id-requerimiento-pago="`+element.id_requerimiento_pago+`"
                            data-id-requerimiento-pago-detalle="`+element.id_requerimiento_pago_detalle+`" title="Asignar a partida" data-original-title="Ver" data-action="asignar-partida" data-tap="requerimiento de pago" data-mes="`+mes+`"><i class="fas fa-share-square"></i></button>
                        </td>
                    </tr>`;
                });
            tabla+=`</tbody>
            </table>`;
    } else {
        var tabla = `<table class="table table-sm" style="border: none;"
            id="detalle_${table_id}">
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
            </table>`;
    }
    row.child(tabla).show();
}
$("#lista-requerimientos-pagos").on("click", 'button[data-action="asignar-partida"]', (e) => {
    e.preventDefault();
    let id = $(e.currentTarget).attr('data-id-requerimiento-pago');
    let id_detalle = $(e.currentTarget).attr('data-id-requerimiento-pago-detalle');
    let tap = $(e.currentTarget).attr('data-tap');
    let mes = $(e.currentTarget).attr('data-mes');
    let html = ``;
    $('#normalizar-partida').modal('show');
    $.ajax({
        type: 'POST',
        url: 'obtener-presupuesto',
        data: {id:id,mes:$data.mes,division:$data.division},
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            $('#normalizar-partida').find('div.modal-body').html(`<div class="text-center"> <i class="fa fa-spinner fa-pulse fa-lg" style="font-size: 80px;"></i></div>`);
        }
    }).done(function(response) {
        if (response.status===200) {
            html = `
                <div class="row">
                    <div class="col-md-12" data-mensaje="respuesta">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p>Código :`+response.presupuesto.codigo+`</p>
                        <p>Descripción :`+response.presupuesto.descripcion+`</p>
                        <table class="table table-bordered table-hover dataTable"
                        id="lista-partidas" data-table="lista-partidas">
                            <thead>
                                <tr>
                                    <th scope="col">Partida</th>
                                    <th scope="col">Descripcion</th>
                                    <th scope="col" style="`+(mes==='01'?'background-color: #bb24249c;':'')+`">Enero</th>
                                    <th scope="col" style="`+(mes==='02'?'background-color: #bb24249c;':'')+`">Febrero</th>
                                    <th scope="col" style="`+(mes==='03'?'background-color: #bb24249c;':'')+`">Marzo</th>
                                    <th scope="col" style="`+(mes==='04'?'background-color: #bb24249c;':'')+`">Abril</th>
                                    <th scope="col"> - </th>
                                </tr>
                            </thead>
                            <tbody>`;

                                $.each(response.presupuesto_detalle, function (idnex, element) {
                                    if (element.registro==='2') {
                                        html+=`<tr>
                                            <td>`+element.partida+`</td>
                                            <td>`+element.descripcion+`</td>
                                            <td style="`+(mes==='01'?'background-color: #bb24249c;':'')+`">`+element.enero+`</td>
                                            <td style="`+(mes==='02'?'background-color: #bb24249c;':'')+`">`+element.febrero+`</td>
                                            <td style="`+(mes==='03'?'background-color: #bb24249c;':'')+`">`+element.marzo+`</td>
                                            <td style="`+(mes==='04'?'background-color: #bb24249c;':'')+`">`+element.abril+`</td>
                                            <td>
                                                <button class="btn btn-default btn-sm"
                                                data-id-presupuesto-interno="`+element.id_presupuesto_interno+`" data-id-presupuesto-interno-detalle="`+element.id_presupuesto_interno_detalle+`"
                                                data-id-requerimiento-pago="`+id+`"
                                                data-id-requerimiento-pago-detalle="`+id_detalle+`"
                                                data-tap="`+tap+`"
                                                data-click="seleccionar-partida">Asignar</button>
                                            </td>
                                        </tr>`;
                                    }

                                });

                            html+=`</tbody>
                        </table>
                    </div>
                </div>
            `;
        }else{
            html=`
            <div class="alert alert-`+response.tipo+`" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only">`+response.titulo+` :</span>
                `+response.mensaje+`
            </div>
            `;
        }

        $('#normalizar-partida').find('div.modal-body').html(html);

    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

$(document).on('click','button[data-click="seleccionar-partida"]',function (e) {
    let presupuesto_interno_id = $(this).attr('data-id-presupuesto-interno');
    let presupuesto_interno_detalle_id = $(this).attr('data-id-presupuesto-interno-detalle');
    let requerimiento_pago_id = $(this).attr('data-id-requerimiento-pago')
    let requerimiento_pago_detalle_id = $(this).attr('data-id-requerimiento-pago-detalle')
    let tap = $(this).attr('data-tap')
    let this_button = $(this);
    let html='';

    $.ajax({
        type: 'POST',
        url: 'vincular-partida',
        data: {
            presupuesto_interno_id:presupuesto_interno_id,
            presupuesto_interno_detalle_id:presupuesto_interno_detalle_id,
            requerimiento_pago_id:requerimiento_pago_id,
            requerimiento_pago_detalle_id:requerimiento_pago_detalle_id,
            tap:tap,
            mes:$data.mes
        },
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            this_button.html(`<i class="fa fa-spinner fa-pulse"></i> Cargando`);
            this_button.attr('disabled','true');
        }
    }).done(function(response) {
        this_button.html(`Asignar`);
        this_button.removeAttr('disabled');
        html = `
        <div class="alert alert-`+response.tipo+`" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">`+response.titulo+` :</span>
            `+response.mensaje+`
        </div>
        `;
        $('[data-mensaje="respuesta"]').html(html);
        console.log(response);
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

