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
    table_requerimientos_pagados = $("#lista-ordenes").DataTable({
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
            {data: 'id', name:"id" },

            {data: 'codigo', name:"codigo" , class:"text-center"},
            {data: 'fecha_emision', name:"fecha_emision" , class:"text-center"},
            {data: 'descripcion_estado_pago', name:"descripcion_estado_pago" , class:"text-center"},
            {
                data: 'monto_total', name:"monto_total",
                render : function(data, type, row){
                    let total = (row['simbolo_moneda']===1?'S/.':'$')+row['monto_total']
                    return total
                }
                , class:"text-center"},
            {
                render: function (data, type, row) {
                    html='';
                    html+='<button type="button" class="btn text-black btn-default botonList detalle-orden" data-id="'+row['id']+'" title="Ver detalle"><i class="fas fa-chevron-down"></i></button>'

                    html+='<button type="button" class="btn text-black btn-flat botonList ver-presupuesto-interno" data-id="'+row['id']+'" title="Asignar Partida" ><i class="fas fa-file-excel"></i></button>'



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
            {data: 'fecha_registro', name:"fecha_registro" , class:"text-center"},
            {data: 'nombre_trabajador', name:"nombre_trabajador" , class:"text-center"},
            {data: 'monto_total', name:"monto_total" , class:"text-center"},
            {
                render: function (data, type, row) {
                    html='';
                    html+='<button type="button" class="btn text-black btn-flat botonList" data-id="'+row['id_requerimiento_pago']+'" title="Asignar a partida" data-original-title="Ver" data-action="asignar-partida"><i class="fas fa-share-square"></i></button>'


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
$(document).on('click','.detalle-orden',function (e) {
    e.preventDefault();

    var counter = 1;
    let html = `
    <tr>
        <td colspan="16"><table class="table table-sm" style="border: none; font-size:x-small;" id="detalle_3">
        </table>
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
                        </tr>
                </thead>
                <tbody style="background: #e7e8ea;"><tr>
                        <td style="border: none;"><a style="cursor:pointer;" class="handleClickObtenerArchivos" data-id="1846146" data-tipo="am">OCAM-2022-109-146-0</a></td>
                        <td style="border: none;">OKC2211017</td>
                        <td style="border: none;">UNIVERSIDAD NACIONAL JORGE BASADRE G.</td>
                        <td style="border: none;">J. Alfaro</td>
                        <td style="border: none;"><a href="/necesidades/requerimiento/elaboracion/index?id=6412" target="_blank" title="Abrir Requerimiento">RC-230075</a></td>
                        <td style="border: none;">0016750</td>
                        <td style="border: none;"></td>
                        <td style="border: none;">UPS CDP R-SMART 1010I, INTERACTIVO, 1000VA, 500W, 220V, 10 TOMACORRIENTES. 5 TOMAS UPS/AVR, 5 TOMAS DE SUPRES</td>
                        <td style="border: none;">1</td>
                        <td style="border: none;">UND</td>
                        <td style="border: none;">S/100,000.00</td>
                        <td style="border: none;">S/100,000.00</td>
                        <td style="border: none; text-align:center;">0</td>

                        </tr>
                </tbody>
            </table>
        </td>
    </tr>
    `;

    let tr = (e.currentTarget).closest('tr');
    var row = table_requerimientos_pagados.row(tr);
    var id = $(e.currentTarget).attr('data-id');
    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.classList.remove('shown');
    }
    else {
        // Open this row
        //    row.child( format(iTableCounter, id) ).show();
        buildFormat((e.currentTarget), iTableCounter, id, row);
        tr.classList.add('shown');
        // try datatable stuff
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
            columns: [
            ]
        });
        iTableCounter = iTableCounter + 1;
    }

});
function buildFormat(obj, table_id, id, row) {
    obj.setAttribute('disabled', true);
    $.ajax({
        type: 'GET',
        url:`/logistica/gestion-logistica/compras/ordenes/listado/detalle-orden/${id}`,
        dataType: 'JSON',
        success(response) {
            obj.removeAttribute('disabled');
            construirDetalleOrdenElaboradas(table_id, row, response);
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
    });

    // this.listaOrdenCtrl.obtenerDetalleOrdenElaboradas(id).then((res) => {
    //     // console.log(res);
    //     obj.removeAttribute('disabled');
    //     this.construirDetalleOrdenElaboradas(table_id, row, res);
    // }).catch((err) => {
    //     console.log(err)
    // })
}

function construirDetalleOrdenElaboradas(table_id, row, response) {
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
$("#lista-requerimientos-pagos").on("click", 'button[data-action="asignar-partida"]', (e) => {
    e.preventDefault();
    let id = $(e.currentTarget).attr('data-id');
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
        html = `
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
                                <th scope="col">Enero</th>
                                <th scope="col">Febrero</th>
                                <th scope="col">Marzo</th>
                                <th scope="col">Abril</th>
                                <th scope="col"> - </th>
                            </tr>
                        </thead>
                        <tbody>`;

                            $.each(response.presupuesto_detalle, function (idnex, element) {
                                if (element.registro==='2') {
                                    html+=`<tr>
                                        <td>`+element.partida+`</td>
                                        <td>`+element.descripcion+`</td>
                                        <td>`+element.enero+`</td>
                                        <td>`+element.febrero+`</td>
                                        <td>`+element.marzo+`</td>
                                        <td>`+element.abril+`</td>
                                        <td>
                                            <button class="btn btn-default btn-sm"
                                            data-id-presupuesto-interno="`+element.id_presupuesto_interno+`" data-id-presupuesto-interno-detalle="`+element.id_presupuesto_interno_detalle+`"
                                            data-id-requerimiento-pago="`+id+`"
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
    let this_button = $(this);
    console.log('ss');
    $.ajax({
        type: 'POST',
        url: 'vincular-partida',
        data: {
            presupuesto_interno_id:presupuesto_interno_id,
            presupuesto_interno_detalle_id:presupuesto_interno_detalle_id,
            requerimiento_pago_id:requerimiento_pago_id

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
        console.log(response);
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

$('[data-table="lista-partidas"]').on("click", 'button[data-click="seleccionar-partida"]', (e) => {
    e.preventDefault();

});
