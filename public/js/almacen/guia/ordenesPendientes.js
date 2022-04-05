let oc_seleccionadas = [];
let oc_det_seleccionadas = [];
let ingresos_seleccionados = [];

let acceso = null;

function iniciar(permiso) {

    acceso = permiso;
    listarIngresos();
    // actualizarFiltrosPendientes();
    listarOrdenesPendientes();
    oc_seleccionadas = [];

    $('#myTabOrdenesPendientes a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let tab = $(e.target).attr("href") // activated tab

        if (tab == '#pendientes') {
            $("#ordenesPendientes").DataTable().ajax.reload(null, false);
            // actualizarFiltrosPendientes();
        }
        else if (tab == '#transformaciones') {
            listarTransformaciones();
        }
        else if (tab == '#ingresadas') {
            $("#listaIngresosAlmacen").DataTable().ajax.reload(null, false);
        }
    });

    vista_extendida();
}

var table;

function listarOrdenesPendientes() {
    var vardataTables = funcDatatables();

    let botones = [];
    if (acceso == '1') {
        botones.push({
            text: ' Ingresar Guía',
            action: function () {
                open_guia_create_seleccionadas();
            }, className: 'btn-primary disabled btnIngresarGuia'
        },
            {
                text: ' Exportar Excel',
                action: function () {
                    exportarOrdenesPendientes();
                }, className: 'btn-success btnExportarOrdenesPendientes'
            });
    }

    $("#ordenesPendientes").on('search.dt', function () {
        $('#ordenesPendientes_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#ordenesPendientes").on('processing.dt', function (e, settings, processing) {
        if (processing) {
            $(e.currentTarget).LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                zIndex: 10,
                imageColor: "#3c8dbc"
            });
        } else {
            $(e.currentTarget).LoadingOverlay("hide", true);
        }
    });

    table = $("#ordenesPendientes").DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // bDestroy: true,
        serverSide: true,
        pageLength: 20,
        initComplete: function (settings, json) {
            const $filter = $("#ordenesPendientes_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscar" class="btn btn-default btn-sm btn-flat" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscar").trigger("click");
                }
            });
            $("#btnBuscar").on("click", e => {
                table.search($input.val()).draw();
            });

            const $form = $('#formFiltrosOrdenesPendientes');
            $('#ordenesPendientes_wrapper .dt-buttons').append(
                `<div style="display:flex">
                    <input type="text" class="form-control date-picker" size="10" id="txtOrdenPendienteFechaInicio" 
                        value="${$form.find('input[name=ordenes_fecha_inicio]').val()}"/>
                    <input type="text" class="form-control date-picker" size="10" id="txtOrdenPendienteFechaFin" 
                        value="${$form.find('input[name=ordenes_fecha_fin]').val()}"/>
                    <select class="form-control" id="selectOrdenPendienteSede">
                        <option value="0" selected>Mostrar Todos</option>
                    </select>
                    
                </div>`
            );
            $('input.date-picker').datepicker({
                language: "es",
                orientation: "bottom auto",
                format: 'dd-mm-yyyy',
                autoclose: true
            });
            listarSedes('ordenes');

            $("#txtOrdenPendienteFechaInicio").on("change", function (e) {
                var ini = $(this).val();
                $('#formFiltrosOrdenesPendientes').find('input[name=ordenes_fecha_inicio]').val(ini);
                $("#ordenesPendientes").DataTable().ajax.reload(null, false);
            });
            $("#txtOrdenPendienteFechaFin").on("change", function (e) {
                // $(e.preventDefault());
                var fin = $(this).val();
                $('#formFiltrosOrdenesPendientes').find('input[name=ordenes_fecha_fin]').val(fin);
                $("#ordenesPendientes").DataTable().ajax.reload(null, false);
            });
            $("#selectOrdenPendienteSede").on("change", function (e) {
                var sed = $(this).val();
                $('#formFiltrosOrdenesPendientes').find('input[name=ordenes_id_sede]').val(sed);
                $("#ordenesPendientes").DataTable().ajax.reload(null, false);
            });
        },
        drawCallback: function (settings) {
            $("#ordenesPendientes_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>'
            ).prop("disabled", false);
            //$('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
            $('#ordenesPendientes tbody tr td input[type="checkbox"]').iCheck({
                checkboxClass: "icheckbox_flat-blue"
            });
            $("#ordenesPendientes_filter input").trigger("focus");
        },
        ajax: {
            url: "listarOrdenesPendientes",
            type: "POST",
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosOrdenesPendientes').serializeArray()))
            }
        },
        columns: [
            { data: "id_orden_compra", name: "log_ord_compra.id_orden_compra" },
            { data: "id_orden_compra", name: "log_ord_compra.id_orden_compra" },
            { data: "codigo_softlink", name: "log_ord_compra.codigo_softlink" },
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
            { data: "razon_social", name: "adm_contri.razon_social" },
            {
                data: "fecha", name: "log_ord_compra.fecha",
                render: function (data, type, row) {
                    return formatDateHour(row["fecha"]);
                }
            },
            // {
            //     render: function (data, type, row) {
            //         var dias_restantes = restarFechas(
            //             fecha_actual(),
            //             sumaFecha(row["plazo_entrega"], row["fecha"])
            //         );
            //         var porc = (dias_restantes * 100) / parseFloat(row["plazo_entrega"]);
            //         var color = porc > 50 ? "success" : porc <= 50 && porc > 20
            //             ? "warning" : "danger";
            //         return `<div class="progress-group">
            //                 <span class="progress-text">Nro días Restantes</span>
            //                 <span class="float-right"><b> ${dias_restantes < 0 ? "0" : dias_restantes
            //             }</b> / ${row["plazo_entrega"]}</span>
            //                 <div class="progress progress-sm">
            //                     <div class="progress-bar bg-${color}" style="width: ${porc}%"></div>
            //                 </div>
            //             </div>`;
            //     }
            // },
            { data: "sede_descripcion", name: "sis_sede.descripcion" },
            { data: "nombre_corto", name: "sis_usua.nombre_corto" },
            {
                data: "estado_doc", name: "estados_compra.descripcion",
                render: function (data, type, row) {
                    return (
                        '<span class="label label-' + (row["estado_doc"] == "Enviado"
                            ? "default" : "warning") + '">' +
                        row["estado_doc"] + "</span>"
                    );
                }
            }
        ],
        columnDefs: [
            { targets: [0], className: "invisible" },
            {
                targets: 1,
                searchable: false,
                orderable: false,
                className: "dt-body-center",
                checkboxes: {
                    selectRow: true,
                    selectCallback: function (nodes, selected) {
                        $('input[type="checkbox"]', nodes).iCheck("update");
                    },
                    selectAllCallback: function (nodes, selected, indeterminate) {
                        $('input[type="checkbox"]', nodes).iCheck("update");
                    }
                }
            },
            {
                searchable: false,
                orderable: false,
                render: function (data, type, row) {
                    if (acceso == "1") {
                        return `<div style="display:flex;">
                            <button type="button" class="ver-detalle btn btn-default boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" title="Ver Detalle" data-id="${row["id_orden_compra"]}">
                                <i class="fas fa-chevron-down"></i></button>
                                
                            <button type="button" class="guia btn btn-info boton btn-flat" data-toggle="tooltip" 
                                data-placement="bottom" title="Generar Guía" >
                                <i class="fas fa-sign-in-alt"></i></button>
                            </div>`;
                    } else {
                        return (
                            `<button type="button" class="ver-detalle btn btn-default boton" data-toggle="tooltip"
                            data-placement="bottom" title="Ver Detalle" data-id="${row["id_orden_compra"]}">
                            <i class="fas fa-chevron-down"></i></button>`
                        );
                    }
                },
                targets: 9
            }
        ],
        order: [[0, "desc"]]
    });

    $($("#ordenesPendientes").DataTable().table().container()).on("ifChanged", ".dt-checkboxes", function (event) {
        var cell = $("#ordenesPendientes").DataTable().cell($(this).closest("td"));
        cell.checkboxes.select(this.checked);

        var data = $("#ordenesPendientes").DataTable().row($(this).parents("tr")).data();
        console.log(this.checked);

        if (data !== null && data !== undefined) {
            if (this.checked) {
                oc_seleccionadas.push(data);
                $('.btnIngresarGuia').removeClass('disabled');
            } else {
                var index = oc_seleccionadas.findIndex(function (item, i) {
                    return item.id_orden_compra == data.id_orden_compra;
                });
                if (index !== null) {
                    oc_seleccionadas.splice(index, 1);
                    $('.btnIngresarGuia').addClass('disabled');
                }
            }
        }
    });
}


$("#ordenesPendientes tbody").on("click", "a.verOrden", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    if (id !== "") {
        let url = `/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${id}`;
        var win = window.open(url, "_blank");
        win.focus();
    }
});

$("#ordenesPendientes tbody").on("click", "button.guia", function () {
    var data = $("#ordenesPendientes")
        .DataTable()
        .row($(this).parents("tr"))
        .data();
    console.log("data.id_orden_compra" + data.id_orden_compra);
    open_guia_create(data, $(this).closest("tr"));
});

function cargar_almacenes(sede, id_almacen) {
    if (sede !== "") {
        $.ajax({
            type: "GET",
            url: "cargar_almacenes/" + sede,
            dataType: "JSON",
            success: function (response) {
                console.log(response);
                var option = "";


                for (var i = 0; i < response.length; i++) {

                    if (id_almacen == 0) {
                        if (response[i].id_tipo_almacen == 1) {//principal sugerido
                            option +=
                                '<option data-id-sede="' + response[i].id_sede + '" data-id-empresa="' +
                                response[i].id_empresa + '" value="' + response[i].id_almacen +
                                '" selected>' + response[i].codigo + " - " + response[i].descripcion +
                                "</option>";
                        } else {
                            option +=
                                '<option data-id-sede="' + response[i].id_sede + '" data-id-empresa="' +
                                response[i].id_empresa + '" value="' + response[i].id_almacen + '">' +
                                response[i].codigo + " - " + response[i].descripcion + "</option>";
                        }
                    } else {
                        if (response[i].id_almacen == id_almacen) {
                            option +=
                                '<option data-id-sede="' + response[i].id_sede + '" data-id-empresa="' +
                                response[i].id_empresa + '" value="' + response[i].id_almacen +
                                '" selected>' + response[i].codigo + " - " + response[i].descripcion +
                                "</option>";
                        } else {
                            option +=
                                '<option data-id-sede="' + response[i].id_sede + '" data-id-empresa="' +
                                response[i].id_empresa + '" value="' + response[i].id_almacen + '">' +
                                response[i].codigo + " - " + response[i].descripcion + "</option>";
                        }
                    }
                }
                $("[name=id_almacen]").html(option);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

// function listarAlmacenes() {
//     $.ajax({
//         type: "GET",
//         url: "almacenesPorUsuario",
//         dataType: "JSON",
//         success: function (response) {
//             console.log(response);
//             var option = '<option value="0">Todos los almacenes</option>';
//             response.forEach(element => {
//                 if (response.length == 1) {
//                     option +=
//                         '<option value="' + element.id_almacen + '" selected>' + element.descripcion + "</option>";
//                 } else {
//                     option +=
//                         '<option value="' + element.id_almacen + '">' + element.descripcion + "</option>";
//                 }
//             });
//             $("#id_sede_filtro_ordenes").html(option);
//         }
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function listarSedes(origen) {
    $.ajax({
        type: "GET",
        url: "sedesPorUsuario",
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            var option = '<option value="0">Todos las sedes</option>';
            response.forEach(element => {
                if (response.length == 1) {
                    option +=
                        '<option value="' + element.id_sede + '" selected>' + element.descripcion + "</option>";
                } else {
                    option +=
                        '<option value="' + element.id_sede + '">' + element.descripcion + "</option>";
                }
            });
            if (origen == 'ordenes') {
                $("#selectOrdenPendienteSede").html(option);
            } else if (origen == 'ingresos') {
                $("#selectIngresoProcesadoSede").html(option);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function exportarOrdenesPendientes() {
    $('#formFiltrosOrdenesPendientes').trigger('submit');
}