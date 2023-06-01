var table;
let despachos_seleccionados = [];
let usuarioSesion;

function listarRequerimientosPendientes(usuario) {
    usuarioSesion = usuario;
    var vardataTables = funcDatatables();
    let botones = [];
    // if (acceso == '1') {
    const button_descargar_excel=(array_accesos.find(element => element === 259)?{
            text: ' Exportar Excel',
            action: function () {
                exportarDespachosExternos();
            }, className: 'btn-success btnExportarDespachosExternos'
        }:[]);
    botones.push(
        // {
        //     text: ' Priorizar seleccionados',
        //     action: function () {
        //         priorizar();
        //     }, className: 'btn-primary disabled btnPriorizar'
        // },
        button_descargar_excel
    );
    // }

    $("#requerimientosEnProceso").on('search.dt', function () {
        $('#requerimientosEnProceso_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#requerimientosEnProceso").on('processing.dt', function (e, settings, processing) {
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

    table = $('#requerimientosEnProceso').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // 'bDestroy': true,
        pageLength: 20,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $("#requerimientosEnProceso_filter");
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

            const $form = $('#formFiltrosDespachoExterno');

            // $('#requerimientosEnProceso_wrapper .dt-buttons').append(
            //     `<div style="display:flex">
            //         <label style="text-align: center;margin-left: 20px;margin-top: 7px;margin-right: 10px;">Mostrar: </label>
            //         <select class="form-control" id="selectMostrar">
            //             <option value="0" selected>Todos</option>
            //             <option value="1" >Priorizados</option>
            //             <option value="2" >Los de Hoy</option>
            //         </select>

            //     </div>`
            // );

            // $("#selectMostrar").on("change", function (e) {
            //     var sed = $(this).val();
            //     $('#formFiltrosDespachoExterno').find('input[name=select_mostrar]').val(sed);
            //     $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
            // });
        },
        drawCallback: function (settings) {
            $("#requerimientosEnProceso_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>'
            ).prop("disabled", false);
            //$('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
            $('#requerimientosEnProceso tbody tr td input[type="checkbox"]').iCheck({
                checkboxClass: "icheckbox_flat-blue"
            });
            $("#requerimientosEnProceso_filter input").trigger("focus");
        },
        ajax: {
            url: 'listarRequerimientosPendientesDespachoExterno',
            type: 'POST',
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosDespachoExterno').serializeArray()))
            }
        },
        columns: [
            { data: 'id_requerimiento', name: 'alm_req.id_requerimiento' },
            {
                data: 'codigo', name: 'alm_req.codigo', className: "text-center",
                'render': function (data, type, row) {
                    return (row['codigo'] !== null ? `<a href="/necesidades/requerimiento/elaboracion/index?id=${row.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${row.codigo}</a>` : '') + (row['estado'] == 38
                        ? ' <i class="fas fa-exclamation-triangle red" data-toggle="tooltip" data-placement="bottom" title="Requerimiento por regularizar"></i> '
                        : (row['estado'] == 39 ?
                            ' <i class="fas fa-pause orange" data-toggle="tooltip" data-placement="bottom" title="Requerimiento en pausa"></i> ' : ''))
                        + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '')
                        + ('<br>' + row['sede_descripcion_req']);
                }
            },
            { data: 'tipo_requerimiento_descripcion', name: 'alm_tp_req.descripcion', className: "text-center" },
            {
                data: 'fecha_entrega', name: 'oc_propias_view.fecha_entrega',
                'render': function (data, type, row) {
                    return (row['fecha_entrega'] !== null ? formatDate(row['fecha_entrega']) : '');
                }
            },
            {
                data: 'nro_orden', name: 'oc_propias_view.nro_orden',
                render: function (data, type, row) {
                    if (row["nro_orden"] == null) {
                        return '';
                    } else {
                        return (
                            '<a href="#" class="archivos" data-id="' + row["id_oc_propia"] + '" data-tipo="' + row["tipo"] + '">' +
                            row["nro_orden"] + "</a><br>" + row['estado_oc']
                        );
                    }
                }, className: "text-center"
            },//
            // { data: 'estado_oc', name: 'oc_propias_view.estado_oc' },
            {
                data: 'monto_total', name: 'oc_propias_view.monto_total',
                render: function (data, type, row) {
                    return (row['monto_total'] !== null ? formatNumber.decimal(row['monto_total'], (row['moneda_oc'] == 's' ? 'S/' : '$'), '-2') : '');
                }, className: "text-right"
            },
            {
                data: 'orden_compra', name: 'oc_propias_view.orden_compra',
                render: function (data, type, row) {
                    return `<input type="text" style="width:70px; text-align:center;" class="oc_fisica" placeholder="Orden física"
                        value="${row["orden_compra"] !== null ? row["orden_compra"] : ""}"/><br>
                    <input type="text" style="width:70px; text-align:center;" class="siaf"  placeholder="SIAF"
                        value="${row["siaf"] !== null ? row["siaf"] : ""}"/>`;
                }, className: "text-center"
            },
            { data: 'occ', name: 'oc_propias_view.occ' },
            {
                data: 'codigo_oportunidad', name: 'oportunidades.codigo_oportunidad',
                render: function (data, type, row) {
                    if (row["codigo_oportunidad"] !== null) {
                        return (
                            '<a target="_blank" href="https://mgcp.okccloud.com/mgcp/cuadro-costos/detalles/' + row['id_oportunidad'] + '">' +
                            row["codigo_oportunidad"] + "</a><br>" + (row['estado_aprobacion_cuadro'] ?? '')
                        );
                    } else {
                        return '';
                    }
                }, className: "text-center"
            },
            { data: 'nombre_entidad', name: 'oc_propias_view.nombre_entidad' },
            // {
            //     data: 'cliente_razon_social', name: 'adm_contri.razon_social',
            //     render: function (data, type, row) {
            //         if (row["cliente_razon_social"] !== null) {
            //             return row["cliente_razon_social"];
            //         } else {
            //             return row["nombre_entidad"];
            //         }
            //     }
            // },
            // { data: 'responsable', name: 'sis_usua.nombre_corto' },
            {
                data: 'responsable', name: 'sis_usua.nombre_corto',
                render: function (data, type, row) {
                    if (row["responsable"] !== null) {
                        return row["responsable"];
                    } else {
                        return row["nombre_largo_responsable"];
                    }
                }
            },
            // { data: 'sede_descripcion_req', name: 'sede_req.descripcion', className: "text-center" },
            // { data: 'codigo_od', name: 'orden_despacho.codigo', className: "text-center" },
            {
                data: 'fecha_despacho_real', name: 'orden_despacho.fecha_despacho_real',
                'render': function (data, type, row) {
                    return (row['fecha_despacho_real'] !== null ? formatDate(row['fecha_despacho_real']) : '');
                }
            },
            {
                data: 'importe_flete', name: 'orden_despacho.importe_flete',
                'render': function (data, type, row) {
                    return (row['importe_flete'] !== null ? formatNumber.decimal(row['importe_flete'], 'S/', -2) : '');
                }
            },
            {
                // data: 'importe_flete', name: 'orden_despacho.importe_flete',
                'render': function (data, type, row) {
                    return (row['gasto_extra'] !== null ? formatNumber.decimal(row['gasto_extra'], 'S/', -2) : '');
                }, searchable: 'false', orderable: 'false'
            },
            {
                data: 'fecha_entregada', name: 'orden_despacho.fecha_entregada',
                'render': function (data, type, row) {
                    return (row['fecha_entregada'] !== null ? formatDate(row['fecha_entregada']) : '');
                }
            },
            {
                data: 'adjunto',
                'render': function (data, type, row) {
                    return (row['adjunto'] !== null ?
                        `<a target="_blank" href="/files/almacen/trazabilidad_envio/${row['adjunto']}">Adjunto</a>` : '');
                }, className: "text-center", searchable: 'false', orderable: 'false'
            },
            {
                data: 'estado_doc', name: 'adm_estado_doc.estado_doc',
                'render': function (data, type, row) {
                    var color = row['estado_doc'] == 'Elaborado' || row['estado_doc'] == 'Aprobado';
                    return '<span class="label label-' + (color ? 'primary' : row['bootstrap_color']) + '">' +
                        (color ? 'Pendiente' : row['estado_doc']) + '</span>'
                }, className: "text-center"
            },
            { data: 'id_od', name: 'orden_despacho.id_od' },
        ],
        columnDefs: [
            { targets: [0], className: "invisible" },
            {
                render: function (data, type, row) {
                    if (row["codigo"] !== null) {
                        return (
                            '<a href="#" class="verRequerimiento" data-id="' + row["id_requerimiento"] + '" >' + row["codigo"] + "</a>" + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '')
                        );
                    } else {
                        return '';
                    }
                }, targets: 1
            },
            {
                'render': function (data, type, row) {
                    return `<div>
                    <div style="display:flex;">`+
                        (array_accesos.find(element => element === 260)?`<button type="button" class="detalle btn btn-default btn-flat btn-xs " data-toggle="tooltip"
                        data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                        <i class="fas fa-chevron-down"></i></button>`:``)+`

                        ${row['tiene_transformacion'] ?
                        (array_accesos.find(element => element === 261)?`<button type="button" class="interno btn btn-${row['codigo_despacho_interno'] !== null ? 'danger' : 'default'} btn-flat btn-xs " data-toggle="tooltip"
                        data-placement="bottom" title="Despacho Interno" data-id="${row['id_requerimiento']}"
                        data-estado="${row['estado_di']}" data-estado-despacho="${row['estado_despacho']}"
                        data-idod="${row['id_despacho_interno']}" >
                        <strong>ODI</strong></button>`:'')
                        : ''}

                        ${row['id_requerimiento'] !== null ?
                        (array_accesos.find(element => element === 262)?`<button type="button" class="envio_od btn btn-${row['id_od'] !== null ? 'warning' : 'default'} btn-flat btn-xs " data-toggle="tooltip"
                            data-placement="bottom" title="Orden de Despacho. ${row['productos_no_mapeados']>0?"Faltan "+row['productos_no_mapeados']+ " items por mapear":""}" data-id="${row['id_requerimiento']}" data-tipo="${row['id_tipo_requerimiento']}"
                            data-fentrega="${row['fecha_entrega']}" data-cdp="${row['codigo_oportunidad']}" data-cantidad-productos-no-mapeados="${row['productos_no_mapeados']}" ${row['productos_no_mapeados'] >0 ?"disabled":""}>
                            <strong>ODE</strong></button>
                            `:''): ''}
                        `+

                        // <button type="button" class="envio btn btn-${row['id_od'] !== null ? 'warning' : 'default'} btn-flat btn-xs " data-toggle="tooltip"
                        //     data-placement="bottom" title="Generar Orden de Despacho" data-id="${row['id_requerimiento']}"
                        //     data-fentrega="${row['fecha_entrega']}" data-cdp="${row['codigo_oportunidad']}">
                        //     <i class="fas fa-file-alt"></i></button>

                        // <button type="button" class="trazabilidad btn btn-warning btn-flat btn-xs " data-toggle="tooltip"
                        //     data-placement="bottom" title="Ver Trazabilidad de Docs"  data-id="${row['id_requerimiento']}">
                        //     <i class="fas fa-route"></i></button>

                        /*(row['id_od'] == null && row['productos_no_mapeados'] == 0)*/
                        `${row["tiene_comentarios"] ?
                        (array_accesos.find(element => element === 263)?`<button type="button" class="comentarios btn btn-default btn-flat btn-xs" data-toggle="tooltip"
                                data-placement="bottom" title="Ver comentarios mgcp" data-oc="${row["id_oc_propia"]}" data-tp="${row["tipo"]}"
                                data-nro="${row["nro_orden"]}" style="background-color: #e4a8ff;">
                                <i class="fas fa-comment"></i></button>`:'') : ''
                        }
                        ${row['id_od'] !== null ?
                        (array_accesos.find(element => element === 265)?`<button type="button" class="btn btn-success btn-flat btn-xs adjuntos-despacho" data-toggle="tooltip"
                                data-placement="bottom" title="Ver adjuntos de notificaciones"
                                >
                                <i class="fas fa-file-archive"></i></button>`:'') : ''
                        }
                        </div>
                        <div style="display:flex;">
                            `+(array_accesos.find(element => element === 264)?`<button type="button" class="contacto btn btn-${(row['id_contacto'] !== null && row['enviar_contacto']) ? 'success' : 'default'} btn-flat btn-xs "
                            data-toggle="tooltip" data-placement="bottom" data-id="${row['id_od']}" title="Datos del contacto" >
                            <i class="fas fa-id-badge"></i></button>`:``)+`
                        `+
                        (row['id_od'] !== null ?
                            ``+(array_accesos.find(element => element === 266)?`<button type="button" class="transportista btn btn-${row['id_transportista'] !== null ? 'info' : 'default'} btn-flat btn-xs " data-toggle="tooltip"
                            data-placement="bottom" data-od="${row['id_od']}" data-idreq="${row['id_requerimiento']}" title="Agencia de transporte" >
                            <i class="fas fa-truck"></i></button>`:``)+`

                            `+(array_accesos.find(element => element === 267)?`<button type="button" class="estados btn btn-${row["count_estados_envios"] > 0 ? 'primary' : 'default'} btn-flat btn-xs" data-toggle="tooltip"
                            data-placement="bottom" title="Ver Trazabilidad de Envío" data-id="${row["id_od"]}">
                            <i class="fas fa-route"></i></button></div>`:``)+``: '') +
                        // ((row['id_od'] !== null && parseInt(row['estado_od']) == 1) ?
                        //     `<button type="button" class="anular_od btn btn-flat btn-danger btn-xs boton" data-toggle="tooltip"
                        //             data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Anular Orden Despacho Externo" >
                        //             <i class="fas fa-trash"></i></button>` : '') +
                        /*(row["nro_orden"] !== null && row['productos_no_mapeados'] == 0
                           ? `<button type="button" class="facturar btn btn-flat btn-xs btn-${row["enviar_facturacion"] ? "info" : "default"}
                                   boton" data-toggle="tooltip" data-placement="bottom" title="Enviar a Facturación"
                                   data-id="${row["id_requerimiento"]}" data-cod="${row["codigo"]}" data-envio="${row["enviar_facturacion"]}">
                                   <i class="fas fa-file-upload"></i></button>`
                           : '')*/
                        `</div>`
                }, targets: 17
            }
        ],
        // select: "multi",
        order: [[0, "desc"]], //, [2, "desc"]
    });
    vista_extendida();

    // $($("#requerimientosEnProceso").DataTable().table().container()).on("ifChanged", ".dt-checkboxes", function (event) {
    //     var cell = $("#requerimientosEnProceso").DataTable().cell($(this).closest("td"));
    //     cell.checkboxes.select(this.checked);

    //     var data = $("#requerimientosEnProceso").DataTable().row($(this).parents("tr")).data();

    //     if (data !== null && data !== undefined) {
    //         console.log(data.id_od);
    //         if (data.id_od !== null) {
    //             if (this.checked) {
    //                 despachos_seleccionados.push({
    //                     'id_od': data.id_od,
    //                     'id_requerimiento': data.id_requerimiento
    //                 });
    //                 $('.btnPriorizar').removeClass('disabled');
    //             } else {
    //                 var index = despachos_seleccionados.findIndex(function (item, i) {
    //                     return item.id_od == data.id_od;
    //                 });
    //                 if (index !== null) {
    //                     despachos_seleccionados.splice(index, 1);
    //                     $('.btnPriorizar').addClass('disabled');
    //                 }
    //             }
    //         } else {
    //             Lobibox.notify("error", {
    //                 title: false,
    //                 size: "mini",
    //                 rounded: true,
    //                 sound: false,
    //                 delayIndicator: false,
    //                 msg: 'El requerimiento seleccionado no cuenta con una orden de despacho aún.'
    //             });
    //             // this.checked = false;
    //             // console.log(this.checked);
    //             // cell.checkboxes.select(this.checked);
    //         }
    //     }
    // });
}

function exportarDespachosExternos() {
    $('#formFiltrosDespachoExterno').trigger('submit');
}

$("#requerimientosEnProceso tbody").on("click", "button.facturar", function () {
    var id = $(this).data("id");
    var cod = $(this).data("cod");
    var envio = $(this).data("envio");

    if (envio) {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Ya se envió a facturación.'
        });
    } else {
        Swal.fire({
            title: "¿Está seguro que desea mandar a facturar el " + cod + "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, Guardar"
        }).then(result => {
            if (result.isConfirmed) {
                $('#modal-enviarFacturacion').modal({
                    show: true
                });
                $('[name=id_requerimiento]').val(id);
                $('#cod_req').text(cod);
            }
        });
    }
});

$("#form-enviarFacturacion").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    enviarFacturacion(data);
});

$("#requerimientosEnProceso tbody").on("click", "a.verRequerimiento", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    localStorage.setItem("idRequerimiento", id);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, "_blank");
    win.focus();
});

$("#requerimientosEnProceso tbody").on("click", "a.archivos", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");
    obtenerArchivosMgcp(id, tipo);
});

$('#requerimientosEnProceso tbody').on("click", "button.envio_od", function (e) {
    $(e.preventDefault());

    if(e.currentTarget.dataset.cantidadProductosNoMapeados == 0){
        var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
        console.log(data);
    
        if (data.tiene_transformacion) {
    
            if (data.codigo_despacho_interno !== null) {
                $('[name=envio]').val('envio');
                openOrdenDespachoEnviar(data);
            } else {
                Lobibox.notify('warning', {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Aún le falta emitir el Despacho Interno.'
                });
            }
        } else {
            $('[name=envio]').val('envio');
            openOrdenDespachoEnviar(data);
        }

    }else{
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Aún le falta mapear '+e.currentTarget.dataset.cantidadProductosNoMapeados+' productos'
        });

    }
});
$('#requerimientosEnProceso tbody').on("click", "button.adjuntos-despacho", function (e) {
    $(e.preventDefault());
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data(),
        html='';
    $('#modal-adjuntos-despacho').modal('show');
    $('#codigo_adjunto').text((data.codigo_oportunidad ?? '') + '-' + (data.codigo ?? ''));
    console.log(data);
    $.ajax({
        type: 'GET',
        url: 'adjuntos-despacho',
        data: data,
        dataType: 'JSON',
    }).done(function (response) {
        console.log(response);
        if (response.success===true) {
            console.log('si');
            $.each(response.data, function (index, element) {
                html+='<tr>'
                    html+='<td> <a href="/files/logistica/despacho/'+element.archivo+'" target="_blank" >'+element.archivo+'</a></td>'
                    html+='<td> '+element.fecha_registro+'</td>'
                html+='</tr>';
            });

        }else{
            console.log('no');
            html+='<tr>'
                html+='<td class="text-center"> Sin adjuntos...</td>'
            html+='</tr>';
        }
        $('[data-table="adjuntos-archivos"]').html(html);
    }).always(function () {
    }).fail(function (jqXHR) {
        console.log('Error devuelto: ' + jqXHR.responseText);
    });
});

$('#requerimientosEnProceso tbody').on("click", "button.interno", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var od = $(this).data("idod");
    var estado = $(this).data("estado");
    var estado_despacho = $(this).data("estadoDespacho");
    console.log(estado);
    var msj = '';

    // if (estado_despacho == 23) {
    //     msj = 'Ya se generó un despacho externo. No es posible modificar.';
    // }
    // else {
    if (estado == null || estado == 1) {
        openFechaProgramada(id, od);
    } else if (estado == 10) {
        msj = 'Ya se finalizó el proceso de transformación. No es posible modificar.';
    } else {
        msj = 'Ya se inició el proceso de transformación. No es posible modificar.';
    }
    // }
    if (msj !== '') {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: msj
        });
    }
});

$('#requerimientosEnProceso tbody').on("click", "button.envio", function (e) {
    $(e.preventDefault());
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    $('[name=envio]').val('');
    generarOrdenDespacho(data);
});

$('#modal-comentarios_oc_mgcp').on('shown.bs.modal', function (e) {

    $('#divComentarios').LoadingOverlay("show", {
        imageAutoResize: true,
        imageColor: "#3c8dbc"
    });

    $.ajax({
        type: "POST",
        url: "listarPorOc",
        data: $('#hdnComentariosMgcpData').val(),
        dataType: "JSON",
    }).done(function (response) {
        console.log(response);
        let html = '';
        response['comentarios'].forEach(element => {
            html += `<tr>
                <td>${element.usuario.name}</td>
                <td>${element.comentario}</td>
                <td>${element.fecha}</td>
            </tr>`;
        });
        $('#listaComentarios tbody').html(html);

    }).always(function () {
        $('#divComentarios').LoadingOverlay("hide", true);

    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

$('#requerimientosEnProceso tbody').on("click", "button.comentarios", function (e) {//mgcp
    //$(e.preventDefault());
    var oc = $(this).data("oc");
    var tipo = $(this).data("tp");
    var nro = $(this).data("nro");
    let data = 'idOc=' + oc + '&tipo=' + tipo;
    $('#hdnComentariosMgcpData').val(data)
    //console.log(data);
    $('#modal-comentarios_oc_mgcp').modal('show');
    $('#listaComentarios tbody').html('');
    $('#nro_orden').text(nro);

});

$('#requerimientosEnProceso tbody').on("blur", "input.oc_fisica", function (e) {
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    var value = $(this).val();
    console.log(data);
    actualizarOcFisica(data, value);
});

$('#requerimientosEnProceso tbody').on("blur", "input.siaf", function (e) {
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    var value = $(this).val();
    console.log(data);
    actualizarSiaf(data, value);
});

function actualizarOcFisica(data, value) {
    console.log(value);
    $.ajax({
        type: "POST",
        url: "actualizarOcFisica",
        data: {
            tipo: data.tipo,
            id: data.id_oc_propia,
            nro_orden: data.nro_orden,
            oc_fisica: value
        },
        dataType: "JSON",
    }).done(function (response) {
        console.log(response);
        Lobibox.notify(response.tipo, {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: response.mensaje
        });
        // $('#listaComentarios tbody').html(html);
    }).always(function () {
        // $('#divComentarios').LoadingOverlay("hide", true);

    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function actualizarSiaf(data, value) {
    console.log(value);
    $.ajax({
        type: "POST",
        url: "actualizarSiaf",
        data: {
            tipo: data.tipo,
            id: data.id_oc_propia,
            nro_orden: data.nro_orden,
            siaf: value
        },
        dataType: "JSON",
    }).done(function (response) {
        console.log(response);
        Lobibox.notify(response.tipo, {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: response.mensaje
        });
        // $('#listaComentarios tbody').html(html);
    }).always(function () {
        // $('#divComentarios').LoadingOverlay("hide", true);

    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$('#requerimientosEnProceso tbody').on("click", "button.anular", function () {
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var origen = 'despacho';
    openRequerimientoObs(id, cod, origen);
});

$('#requerimientosEnProceso tbody').on("click", "button.contacto", function () {
    // var id = $(this).data('id');
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    // tab_origen = 'enProceso';
    openDespachoContacto(data);
});

$('#requerimientosEnProceso tbody').on("click", "button.anular_od", function () {
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    Swal.fire({
        title: "¿Está seguro que desea anular la Orden de Transformación " + cod + "?",
        text: "No podrás revertir esto.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Si, anular"
    }).then(result => {
        if (result.isConfirmed) {
            anularOrdenDespacho(id);
        }
    });
});

$("#requerimientosEnProceso tbody").on("click", "button.trazabilidad", function () {
    var id = $(this).data("id");
    mostrarTrazabilidad(id)
});

$("#requerimientosEnProceso tbody").on("click", "button.transportista", function () {
    // var id_od = $(this).data("od");
    // var id_req = $(this).data("idreq");
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    openAgenciaTransporte(data);
});

function enviarFacturacion(data) {
    $.ajax({
        type: "POST",
        url: "enviarFacturacion",
        data: data,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            if (response > 0) {
                $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularOrdenDespacho(id) {
    $.ajax({
        type: 'GET',
        url: 'anular_orden_despacho/' + id + '/externo',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "Orden de Despacho anulado con éxito."
                });
                $('#requerimientosEnProceso').DataTable().ajax.reload(null, false);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function migrarDespachos() {
    console.log(usuarioSesion);

    if (usuarioSesion == 'Rocio.Condori') {
        $.ajax({
            type: 'GET',
            url: 'migrarDespachos',
            dataType: 'JSON',
            success: function (response) {
                console.log(response);

                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "Despachos migrados con exito."
                });
                $('#requerimientosEnProceso').DataTable().ajax.reload(null, false);

            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function open_detalle_transferencia(id) {
    $('#modal-detalleTransferencia').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        url: 'listarDetalleTransferencias/' + id,
        dataType: 'JSON',
        success: function (response) {
            $('#detalleTransferencias tbody').html(response);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

var iTableCounter = 1;
var oInnerTable;

$('#requerimientosEnProceso tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var id = $(this).data('id');

    const $boton = $(this);
    // $boton.prop('disabled', true);

    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        // $boton.prop('disabled', false);
    }
    else {
        format(iTableCounter, id, row, $boton);
        tr.addClass('shown');

        oInnerTable = $('#requerimientosEnProceso_' + iTableCounter).dataTable({
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


$("#requerimientosEnProceso tbody").on("click", "td button.estados", function () {
    var tr = $(this).closest("tr");
    var row = table.row(tr);
    var id = $(this).data("id");

    if (id !== null) {

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass("shown");
        } else {
            formatTimeLine(iTableCounter, id, row);
            tr.addClass("shown");
            oInnerTable = $("#requerimientosEnProceso_" + iTableCounter).dataTable({
                //    data: sections,
                autoWidth: true,
                deferRender: true,
                info: false,
                lengthChange: false,
                ordering: false,
                paging: false,
                scrollX: true,
                scrollY: false,
                searching: false,
                columns: []
            });
            iTableCounter = iTableCounter + 1;
        }
    } else {
        Lobibox.notify("error", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: "El requerimiento seleccionado no tiene un despacho externo."
        });
    }
});

function abrir_requerimiento(id_requerimiento) {
    localStorage.setItem("id_requerimiento", id_requerimiento);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    win.focus();
}
