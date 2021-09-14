let oc_seleccionadas = [];
let oc_det_seleccionadas = [];
let ingresos_seleccionados = [];

let acceso = null;

function iniciar(permiso) {
    $("#tab-ordenes section:first form").attr("form", "formulario");
    acceso = permiso;
    listarIngresos();
    listarOrdenesPendientes();
    oc_seleccionadas = [];

    $("ul.nav-tabs li a").on("click", function () {
        $("ul.nav-tabs li").removeClass("active");
        $(this)
            .parent()
            .addClass("active");
        $(".content-tabs section").attr("hidden", true);
        $(".content-tabs section form").removeAttr("type");
        $(".content-tabs section form").removeAttr("form");

        var activeTab = $(this).attr("type");
        var activeForm = "form-" + activeTab.substring(1);

        $("#" + activeForm).attr("type", "register");
        $("#" + activeForm).attr("form", "formulario");
        changeStateInput(activeForm, true);

        // clearDataTable();
        if (activeForm == "form-pendientes") {
            // listarOrdenesPendientes();
            $("#ordenesPendientes").DataTable().ajax.reload();
        } else if (activeForm == "form-transformaciones") {
            listarTransformaciones();
        } else if (activeForm == "form-ingresadas") {
            // listarIngresos();
            $("#listaIngresosAlmacen").DataTable().ajax.reload();
        }
        $(activeTab).attr("hidden", false); //inicio botones (estados)
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
            }, className: 'btn-success disabled btnIngresarGuia'
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
        pageLength: 50,
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
            type: "POST"
        },
        columns: [
            { data: "id_orden_compra" },
            { data: "id_orden_compra" },
            { data: "codigo_softlink" },
            { data: "codigo" },
            { data: "razon_social", name: "adm_contri.razon_social" },
            {
                data: "fecha",
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
                            '<button type="button" class="ver-detalle btn btn-default boton" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Ver Detalle" data-id="' +
                            row["id_orden_compra"] +
                            '">' +
                            '<i class="fas fa-chevron-down"></i></button>'
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

// botones('#ordenesPendientes tbody',$('#ordenesPendientes').DataTable());
// $("#ordenesPendientes tbody").on("click", "button.detalle", function () {
//     var data = $("#ordenesPendientes")
//         .DataTable()
//         .row($(this).parents("tr"))
//         .data();
//     console.log("data.id_orden_compra" + data.id_orden_compra);
//     // var data = $(this).data('id');
//     open_detalle(data);
// });

$("#ordenesPendientes tbody").on("click", "button.guia", function () {
    var data = $("#ordenesPendientes")
        .DataTable()
        .row($(this).parents("tr"))
        .data();
    console.log("data.id_orden_compra" + data.id_orden_compra);
    open_guia_create(data, $(this).closest("tr"));
});

// function open_detalle(data) {
//     $("#modal-ordenDetalle").modal({
//         show: true
//     });
//     $("#cabecera_orden").text(data.codigo_orden + " - " + data.razon_social);
//     listar_detalle_orden(data.id_orden_compra);
// }

function cargar_almacenes(sede) {
    if (sede !== "") {
        $.ajax({
            type: "GET",
            url: "cargar_almacenes/" + sede,
            dataType: "JSON",
            success: function (response) {
                console.log(response);
                var option = "";
                for (var i = 0; i < response.length; i++) {
                    if (response.length == 1) {
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
                $("[name=id_almacen]").html(option);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

// function open_guias(data) {
//     $("#modal-guias").modal({
//         show: true
//     });
//     $("#cabecera_orden").text(
//         data.codigo_orden + " - " + data.razon_social +
//         " - Total: " + data.simbolo + data.monto_total
//     );
//     listar_guias_orden(data.id_orden_compra);
// }

// function listar_detalle_orden(id_orden) {
//     console.log("id_orden", id_orden);
//     $.ajax({
//         type: "GET",
//         url: "detalleOrden/" + id_orden,
//         dataType: "JSON",
//         success: function (response) {
//             console.log(response);
//             var html = "";
//             var i = 1;
//             response.forEach(element => {
//                 html +=
//                     '<tr id="' + element.id_detalle_orden + '">' +
//                     "<td>" + i + "</td>" +
//                     "<td>" + element.codigo + "</td>" +
//                     "<td>" + element.part_number + "</td>" +
//                     "<td>" + element.categoria + "</td>" +
//                     "<td>" + element.subcategoria + "</td>" +
//                     "<td>" + element.descripcion + "</td>" +
//                     "<td>" + element.cantidad + "</td>" +
//                     "<td>" + element.abreviatura + "</td>" +
//                     "<td>" +
//                     (element.cantidad_ingresada !== null
//                         ? element.cantidad_ingresada
//                         : "0") +
//                     "</td>" +
//                     '<td><span class="label label-' +
//                     element.bootstrap_color + '">' +
//                     element.estado_doc + "</span></td>" +
//                     "</tr>";
//                 i++;
//             });
//             $("#detalleOrden tbody").html(html);
//         }
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

// function listar_guias_orden(id_orden) {
//     $.ajax({
//         type: "GET",
//         url: "verGuiasOrden/" + id_orden,
//         dataType: "JSON",
//         success: function (response) {
//             console.log(response);
//             var html = "";
//             var i = 1;
//             response.forEach(element => {
//                 html +=
//                     '<tr id="' +
//                     element.id_guia_com_oc +
//                     '">' +
//                     "<td>" +
//                     i +
//                     "</td>" +
//                     '<td><label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_compra(' +
//                     element.id_guia_com +
//                     ')">' +
//                     element.serie +
//                     "-" +
//                     element.numero +
//                     "</label></td>" +
//                     "<td>" +
//                     element.fecha_emision +
//                     "</td>" +
//                     "<td>" +
//                     element.almacen +
//                     "</td>" +
//                     "<td>" +
//                     element.operacion +
//                     "</td>" +
//                     "<td>" +
//                     element.nombre_responsable +
//                     "</td>" +
//                     "<td>" +
//                     element.nombre_registrado_por +
//                     "</td>" +
//                     '<td><span class="label label-' +
//                     element.bootstrap_color +
//                     '">' +
//                     element.estado_doc +
//                     "</span></td>" +
//                     "</tr>";
//                 i++;
//             });
//             $("#guiasOrden tbody").html(html);
//         }
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

// function abrir_guia_compra(id_guia_compra) {
//     console.log("abrir_guia_compra()");
//     localStorage.setItem("id_guia_com", id_guia_compra);
//     location.assign("guia_compra");
// }
