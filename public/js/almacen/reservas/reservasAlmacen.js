let $tableReservas;

function listarReservasAlmacen(id_usuario) {
    console.log('list');
    var vardataTables = funcDatatables();
    let botones = [];

    $("#reservasAlmacen").on('search.dt', function () {
        $('#reservasAlmacen_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#reservasAlmacen").on('processing.dt', function (e, settings, processing) {
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

    $tableReservas = $('#reservasAlmacen').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // destroy: true,
        pageLength: 10,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $("#reservasAlmacen_filter");
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
                $tableReservas.search($input.val()).draw();
            });

            // const $form = $('#formFiltrosSalidasPendientes');
            // $('#reservasAlmacen_wrapper .dt-buttons').append(
            //     `<div style="display:flex">
            //         <label style="text-align: center;margin-top: 7px;margin-left: 10px;margin-right: 10px;">Mostrar: </label>
            //         <select class="form-control" id="selectMostrarPendientes">
            //             <option value="0" >Todos</option>
            //             <option value="1" >Priorizados</option>
            //             <option value="2" selected>Los de Hoy</option>
            //         </select>
            //     </div>`
            // );
            // $("#selectMostrarPendientes").on("change", function (e) {
            //     var sed = $(this).val();
            //     console.log('sel ' + sed);
            //     $('#formFiltrosSalidasPendientes').find('input[name=select_mostrar_pendientes]').val(sed);
            //     $("#reservasAlmacen").DataTable().ajax.reload(null, false);
            // });

        },
        drawCallback: function (settings) {
            $("#reservasAlmacen_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop("disabled", false);
            $("#reservasAlmacen_filter input").trigger("focus");
        },
        ajax: {
            url: 'listarReservasAlmacen',
            type: 'POST',
            // data: function (params) {
            //     var x = $('[name=select_mostrar_pendientes]').val();
            //     console.log(x);
            //     return Object.assign(params, objectifyForm($('#formFiltrosSalidasPendientes').serializeArray()))
            // }
        },
        columns: [
            { data: 'id_reserva' },
            { data: 'codigo', className: "text-center" },
            {
                data: 'codigo_req', name: 'alm_req.codigo', className: "text-center",
                'render': function (data, type, row) {
                    return (row['codigo_req'] !== null ? `<a href="/necesidades/requerimiento/elaboracion/index?id=${row['id_requerimiento']}"
                        target="_blank" title="Abrir Requerimiento">${row['codigo_req'] ?? ''}</a>` : '') +
                        (row['estado_requerimiento'] == 38
                            ? ' <i class="fas fa-exclamation-triangle red" data-toggle="tooltip" data-placement="bottom" title="Requerimiento por regularizar"></i> '
                            : (row['estado_requerimiento'] == 39 ?
                                ' <i class="fas fa-pause orange" data-toggle="tooltip" data-placement="bottom" title="Requerimiento en pausa"></i> ' : ''))
                        + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '');
                }
            },
            {
                data: 'codigo_producto', name: 'alm_prod.codigo',
                'render': function (data, type, row) {
                    return `<a href="#" class="verProducto" data-id="${row['id_producto']}" >${row['codigo_producto']}</a>`
                }
            },
            { data: 'part_number', name: 'alm_prod.part_number' },
            { data: 'descripcion', name: 'alm_prod.descripcion' },
            { data: 'almacen', name: 'alm_almacen.descripcion', className: "text-center" },
            { data: 'stock_comprometido', className: "text-center" },
            // { data: 'numero', name: 'guia_com.numero', className: "text-center" },
            {
                data: 'numero', name: 'guia_com.numero', className: "text-center",
                'render': function (data, type, row) {
                    return row['numero'] !== null ? (row['serie'] + '-' + row['numero']) : '';
                }
            },
            { data: 'codigo_transferencia', name: 'trans.codigo', className: "text-center" },
            { data: 'codigo_transformado', name: 'transformacion.codigo', className: "text-center" },
            { data: 'codigo_materia', name: 'materia.codigo', className: "text-center" },
            { data: 'nombre_corto', name: 'sis_usua.nombre_corto', className: "text-center" },
            { data: 'fecha_registro', className: "text-center" },
            // { data: 'codigo_req', name: 'alm_req.codigo', className: "text-center" },
            {
                data: 'estado_doc', name: 'adm_estado_doc.bootstrap_color', className: "text-center",
                'render': function (data, type, row) {
                    return '<span class=" '+(row['estado']==7?'handleClickDetalleAnulacion':'')+' label label-' + row['bootstrap_color'] + '" data-usuario-anulacion="'+row['usuario_anulacion']+'" data-motivo-anulacion="'+row['motivo_anulacion']+'"  data-fecha-anulacion="'+row['deleted_at']+'"  '+(row['estado']==7?'style="cursor:pointer;"':'')+' >' + row['estado_doc'] + '</span>'
                }
            },
        ],
        columnDefs: [

            { 'aTargets': [0], 'sClass': 'invisible' },

            {

                'render': function (data, type, row) {

                    // let $btn_editar = (id_usuario == '3' || id_usuario == '16' || id_usuario == '17' || id_usuario == '93') ?
                    // ((row['estado']===1)?
                    // (array_accesos.find(element => element === 155)?`<button type="button" class="editar btn btn-primary btn-flat boton" data-toggle="tooltip"

                    // data-placement="bottom" title="Editar Reserva"  data-id="${row['id_reserva']}"

                    // data-almacen="${row['id_almacen_reserva']}"  data-stock="${row['stock_comprometido']}"

                    // data-codigo="${row['codigo_req']}">

                    // <i class="fas fa-edit"></i>

                    // </button>`:``):``)
                    // : '';
                    let btnAjustaReserva = '<button type="button" class="btn btn-default btn-xs handleClickAjustarEstadoReserva" style="color:red;" name="btnRetornarAjustarReserva" title="Ajustar Estado Reserva" data-id-reserva="' + row.id_reserva + '" data-codigo-reserva="' + row.codigo + '"><i class="fas fa-sliders-h"></i></button>';

                    let $btn_editar = array_accesos.find(element => element === 155)?`<button type="button" class="editar btn btn-primary btn-flat boton" data-toggle="tooltip"

                    data-placement="bottom" title="Editar Reserva"  data-id="${row['id_reserva']}"

                    data-almacen="${row['id_almacen_reserva']}"  data-stock="${row['stock_comprometido']}"

                    data-codigo="${row['codigo_req']}">

                    <i class="fas fa-edit"></i>

                    </button>`:``;

                    let $btn_eliminar = (row['numero'] == null && row['estado']===1 || row['id_tipo_requerimiento']===4) ?

                    (array_accesos.find(element => element === 156)?`<button type="button" class="anular btn btn-danger btn-flat boton" data-toggle="tooltip"

                    data-placement="bottom" title="Anular Reserva" data-id="${row['id_reserva']}" data-detalle="${row['id_detalle_requerimiento']}" data-id-tipo-requerimiento="${row['id_tipo_requerimiento']}">



                        <i class="fas fa-trash"></i>

                    </button>`:``)
                    :'';

                    return $btn_editar+$btn_eliminar+((([17,27,1,3,77].includes(auth_user.id_usuario))? btnAjustaReserva :''));


                }, targets: 15

            }

        ],
        'order': [[0, "desc"]],
    });
    vista_extendida();
}

$("#reservasAlmacen tbody").on("click", "button.editar", function () {
    var id = $(this).data("id");
    var alm = $(this).data("almacen");
    var stock = $(this).data("stock");
    var codigo = $(this).data("codigo");

    $('#modal-editarReserva').modal({
        show: true
    });

    $('[name=id_reserva]').val(id);
    $('[name=id_almacen_reserva]').val(alm);
    $('[name=stock_comprometido]').val(stock);
    $('#codigo_req').text(codigo);
});
$("#reservasAlmacen tbody").on("click", "span.handleClickDetalleAnulacion", function () {
    var usuario_anulacion = $(this).data("usuarioAnulacion");
    var motivo_anulacion = $(this).data("motivoAnulacion");
    var fecha_anulacion = $(this).data("fechaAnulacion");

    var swal_html = `<dl>
    <dt>Anulado por</dt>
    <dd>${usuario_anulacion}</dd>
    <dt style="ma">Motivo</dt>
    <dd>${motivo_anulacion}</dd>
    <dt>Fecha anulación</dt>
    <dd>${fecha_anulacion}</dd>
  </dl>`;
    Swal.fire({title:"Detalle de Anulación", html: swal_html});
});


$("#form-editarReserva").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea guardar los cambios?",
        text: "Los cambios son irreversibles",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {

            var data = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'actualizarReserva',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    Lobibox.notify("success", {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Se actualizó correctamente'
                    });
                    $('#modal-editarReserva').modal('hide');
                    $("#reservasAlmacen").DataTable().ajax.reload(null, false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
});


$("#reservasAlmacen tbody").on("click", "button.anular", function () {
    let motivoDeAnulacion = '';

    Swal.fire({
        title: "¿Está seguro que desea anular ésta reserva?. Escriba un motivo",
        input: 'textarea',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Registrar',
        allowOutsideClick: () => !Swal.isLoading()
    }).then(result => {
        motivoDeAnulacion = result.value;
        let formData = new FormData();
        formData.append(`id`, $(this).data("id"));
        formData.append(`id_tipo_requerimiento`, $(this).attr('data-id-tipo-requerimiento'));
        formData.append(`id_detalle`, $(this).data('detalle'));
        formData.append(`motivo_de_anulacion`, motivoDeAnulacion);
        
        if(motivoDeAnulacion == null || (motivoDeAnulacion).trim()==''){

            Swal.fire(
                '',
                'Debe ingresar un motivo para anular',
                'info'
            );
            return false;
        } 
        if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'anularReserva',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: (data) => { // Are not working with dataType:'jsonp'

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success:  (response) =>{
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        console.log(response);
                        if (response.respuesta > 0) {
                            Lobibox.notify(response.tipo_estado, {
                                title: false,
                                size: "mini",
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.mensaje
                            });
                            $('#reservasAlmacen').DataTable().ajax.reload(null, false);
                        }else{
                            Swal.fire(
                                '',
                                response.mensaje,
                                response.tipo_estado
                            );
                        }
                    },
                    fail: (jqXHR, textStatus, errorThrown) => {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un problema en el servidor al intentar anular la reserva, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
        }
    });

});

$("#reservasAlmacen tbody").on("click", "a.verProducto", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    localStorage.setItem("id_producto", id);
    var win = window.open("/almacen/catalogos/productos/index", '_blank');
    win.focus();
});

$('#reservasAlmacen tbody').on("click", "button.handleClickAjustarEstadoReserva", (e) => {
    ajustarEstadoReserva(e.currentTarget);
});


function ajustarEstadoReserva(obj){
    var idReserva = obj.dataset.idReserva;
    var codigoReserva = obj.dataset.codigoReserva;

    $('#modal-ajustarEstadoReserva').modal({
        show: true,
        backdrop: 'static'
    });

    document.querySelector("form[id='form-ajustarEstadoReserva'] input[name='id_reserva']").value= idReserva;
    document.querySelector("form[id='form-ajustarEstadoReserva'] label[id='codigo_req']").innerHTML= codigoReserva;

}
$("#form-ajustarEstadoReserva").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea actualizar el estado?",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Actualizar"
    }).then(result => {
        if (result.isConfirmed) {

            var data = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'actualizarEstadoReserva',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    Lobibox.notify(response.estado, {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });
                    $('#modal-ajustarEstadoReserva').modal('hide');
                    $("#reservasAlmacen").DataTable().ajax.reload(null, false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
});


function actualizarReservas() {
    $.ajax({
        type: 'GET',
        url: 'actualizarReservas',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Se actualizaron ' + response.reservas_actualizadas + ' reservas correctamente.'
            });
            $('#reservasAlmacen').DataTable().ajax.reload(null, false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
