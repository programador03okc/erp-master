let table;
class GestionCustomizacion {
    constructor(permiso) {
        this.permiso = permiso;
        this.listarTransformacionesPendientes();
        this.listarTransformaciones();
    }

    listarTransformacionesPendientes() {
        var vardataTables = funcDatatables();
        let botones = [];

        $("#listaTransformacionesPendientes").on('search.dt', function () {
            $('#listaTransformacionesPendientes_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
        });

        $("#listaTransformacionesPendientes").on('processing.dt', function (e, settings, processing) {
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

        table = $('#listaTransformacionesPendientes').DataTable({
            dom: vardataTables[1],
            buttons: botones,
            language: vardataTables[0],
            pageLength: 50,
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $("#listaTransformacionesPendientes_filter");
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


                // $('#listaTransformacionesPendientes_wrapper .dt-buttons').append(
                //     `<div style="display:flex">
                //         <label style="text-align: center;margin-top: 7px;margin-left: 10px;margin-right: 10px;">Mostrar: </label>
                //         <select class="form-control" id="selectMostrarPendientes">
                //             <option value="0" selected>Todos</option>
                //             <option value="1" >Priorizados</option>
                //             <option value="2" >Los de Hoy</option>
                //         </select>
                //     </div>`
                // );

                // $("#selectMostrarPendientes").on("change", function (e) {
                //     var sed = $(this).val();
                //     console.log('sel ' + sed);
                //     $('#formFiltrosTransformacionesPendientes').find('input[name=select_mostrar_pendientes]').val(sed);
                //     $("#listaTransformacionesPendientes").DataTable().ajax.reload(null, false);
                // });
            },
            drawCallback: function (settings) {
                $("#listaTransformacionesPendientes_filter input").prop("disabled", false);
                $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop("disabled", false);
                $("#listaTransformacionesPendientes_filter input").trigger("focus");
            },
            // ajax: 'listar_transformaciones_pendientes',
            ajax: {
                url: 'listar_transformaciones_pendientes',
                type: 'POST',
                data: function (params) {
                    // var x = $('[name=select_mostrar_pendientes]').val();
                    // console.log(x);
                    return Object.assign(params, objectifyForm($('#formFiltrosTransformacionesPendientes').serializeArray()))
                }
            },
            columns: [
                { data: 'id_transformacion' },
                {
                    data: 'id_transformacion',// searchable: 'false',
                    'render':
                        function (data, type, row) {
                            return (row['estado'] == 21 ? (row['conformidad'] ?
                                `<button type="button" class="conformidad btn btn-success boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_transformacion']}" title="Cambiar a No Conforme" >
                        <i class="fas fa-check"></i></button>` :

                                `<button type="button" class="noconformidad btn btn-danger boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_transformacion']}" title="Cambiar a Recibido Conforme" >
                        <i class="fas fa-times"></i></button>`) :

                                row['estado'] == 24 ? '<i class="fas fa-check green" style="font-size: 15px;"></i>'
                                    : ((row['estado'] == 1 || row['estado'] == 25) ? '' : '<i class="fas fa-check-double blue"  style="font-size: 15px;"></i>'));
                        }
                },
                {
                    data: 'codigo', name: 'transformacion.codigo',
                    'render':
                        function (data, type, row) {
                            return ('<label class="lbl-codigo" title="Abrir Transformación" onClick="abrir_transformacion(' + row['id_transformacion'] + ')">' + row['codigo'] + '</label>');
                        }
                },
                // { data: 'fecha_entrega_req', className: "text-center" },
                {
                    data: 'fecha_entrega_req', name: 'alm_req.fecha_entrega', className: "text-center",
                    'render':
                        function (data, type, row) {
                            return (formatDate(row['fecha_entrega_req']));
                        }
                },
                {
                    data: 'nro_orden', name: 'oc_propias_view.nro_orden',
                    render: function (data, type, row) {
                        if (row["nro_orden"] == null) {
                            return '';
                        } else {
                            return (
                                `<a href="#" class="archivos" data-id="${row["id_oc_propia"]}" data-tipo="${row["tipo"]}">
                                ${row["nro_orden"]}</a>`
                            );
                        }
                    }, className: "text-center"
                },
                { data: 'codigo_oportunidad', name: 'oc_propias_view.codigo_oportunidad', className: "text-center" },
                { data: 'razon_social', name: 'adm_contri.razon_social' },
                { data: 'codigo_req', name: 'alm_req.codigo', className: "text-center" },
                {
                    data: 'fecha_despacho', name: 'orden_despacho.fecha_despacho', className: "text-center",
                    'render':
                        function (data, type, row) {
                            return (formatDate(row['fecha_despacho']));
                        }
                },
                {
                    data: 'fecha_inicio', name: 'transformacion.fecha_inicio', className: "text-center",
                    'render':
                        function (data, type, row) {
                            return (formatDate(row['fecha_inicio']));
                        }
                },
                { data: 'descripcion', name: 'alm_almacen.descripcion' },
                {
                    data: 'estado_doc', name: 'adm_estado_doc.estado_doc',
                    'render':
                        function (data, type, row) {
                            return ('<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>');
                        }
                },
                {
                    data: 'id_transformacion',// searchable: 'false',
                    'render':
                        function (data, type, row) {
                            return (`<button type="button" class="imprimir btn btn-info btn-flat boton" data-toggle="tooltip" 
                                    data-placement="bottom" title="Imprimir Hoja de Transformación" data-id="${row['id_transformacion']}">
                                    <i class="fas fa-print"></i></button>`+
                                (row['estado'] == 21 ? //entregado
                                    `<button type="button" class="iniciar btn btn-primary btn-flat boton" data-toggle="tooltip" 
                                    data-placement="bottom" title="Iniciar Transformación" data-id="${row['id_transformacion']}"
                                    data-estado="${row['estado']}">
                                    <i class="fas fa-step-forward"></i></button>`: '')
                            );
                        }
                },
            ],
            'order': [[0, "desc"]],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });
    }

    listarTransformaciones() {
        var vardataTables = funcDatatables();
        var tabla = $('#listaTransformaciones').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            'ajax': 'listarTransformacionesProcesadas',
            // 'ajax': {
            //     url:'listar_transferencias_pendientes/'+alm_origen+'/'+alm_destino,
            //     dataSrc:''
            // },
            'columns': [
                { 'data': 'id_transformacion' },
                {
                    'render':
                        function (data, type, row) {
                            return ('<label class="lbl-codigo" title="Abrir Transformación" onClick="abrir_transformacion(' + row['id_transformacion'] + ')">' + row['codigo'] + '</label>');
                        }
                },
                { 'data': 'fecha_entrega_req' },

                { 'data': 'orden_am', 'name': 'oc_propias.orden_am' },
                // {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
                // {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
                { 'data': 'nombre', 'name': 'entidades.nombre' },
                // {'render':
                //     function (data, type, row){
                //         return (formatDate(row['fecha_registro']));
                //     }
                // },
                { 'data': 'codigo_req' },
                { 'data': 'fecha_registro' },
                { 'data': 'fecha_inicio' },
                { 'data': 'fecha_transformacion' },
                // {'render':
                //     function (data, type, row){
                //         return (formatDate(row['fecha_transformacion']));
                //     }
                // },
                // {'data': 'codigo_oportunidad'},
                { 'data': 'descripcion' },
                { 'data': 'nombre_responsable' },
                { 'data': 'observacion' },
                // {'data': 'nombre_registrado'},
                // {'render':
                //     function (data, type, row){
                //         return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                //     }
                // },
                {
                    'render':
                        function (data, type, row) {
                            return ('<button type="button" class="salida btn btn-success btn-flat boton" data-toggle="tooltip" ' +
                                'data-placement="bottom" title="Ver Salida" data-id="' + row['id_salida'] + '">' +
                                '<i class="fas fa-sign-out-alt"></i></button>' +

                                '<button type="button" class="ingreso btn btn-primary btn-flat boton" data-toggle="tooltip" ' +
                                'data-placement="bottom" title="Ver Ingreso" data-id="' + row['id_ingreso'] + '">' +
                                '<i class="fas fa-sign-in-alt"></i></button>');
                        }
                },
            ],
            'order': [[0, "desc"]],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        });
    }
}

$("#listaTransformacionesPendientes tbody").on("click", "a.archivos", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");
    console.log(id);
    obtenerArchivosMgcp(id, tipo);
});

$('#listaTransformacionesPendientes tbody').on("click", "button.imprimir", function () {
    var id = $(this).data('id');
    if (id !== null && id !== '') {
        window.open('imprimir_transformacion/' + id);
    } else {
        alert('Debe seleccionar una Hoja de Transformación.');
    }
});

$('#listaTransformacionesPendientes tbody').on("click", "button.iniciar", function () {
    var id = $(this).data('id');
    var estado = $(this).data('estado');

    if (id !== null && id !== '') {
        openIniciar(id, estado);
    } else {
        alert('Debe seleccionar una Hoja de Transformación.');
    }
});

$('#listaTransformaciones tbody').on("click", "button.salida", function () {
    var idSalida = $(this).data('id');
    console.log(idSalida);
    if (idSalida !== "") {
        window.open("imprimir_salida/" + idSalida);
    }
});
$('#listaTransformaciones tbody').on("click", "button.ingreso", function () {
    var idIngreso = $(this).data('id');
    console.log(idIngreso);
    if (idIngreso !== "") {
        window.open("imprimir_ingreso/" + idIngreso);
    }
});
// $('#listaTransformacionesPendientes tbody').on("mouseover","button.conformidad", function(){
//     $(this).find('i.fas').removeClass('fa-check');
//     $(this).find('i.fas').addClass('fa-times');
// });
// $('#listaTransformacionesPendientes tbody').on("mouseout","button.conformidad", function(){
//     $(this).find('i.fas').removeClass('fa-times');
//     $(this).find('i.fas').addClass('fa-check');
// });

$('#listaTransformacionesPendientes tbody').on("click", "button.conformidad", function () {
    var id = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: 'no_conforme_transformacion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('#listaTransformacionesPendientes').DataTable().ajax.reload();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

// $('#listaTransformacionesPendientes tbody').on("mouseover","button.noconformidad", function(){
//     $(this).find('i.fas').removeClass('fa-times');
//     $(this).find('i.fas').addClass('fa-check');
// });
// $('#listaTransformacionesPendientes tbody').on("mouseout","button.noconformidad", function(){
//     $(this).find('i.fas').removeClass('fa-check');
//     $(this).find('i.fas').addClass('fa-times');
// });

$('#listaTransformacionesPendientes tbody').on("click", "button.noconformidad", function () {
    var id = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: 'recibido_conforme_transformacion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('#listaTransformacionesPendientes').DataTable().ajax.reload();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});

function openIniciar(id_transformacion, est) {
    if (est == '1') {
        alert('A la espera de que Almacén genere la salida de los productos.');
    }
    else if (est == '9') {
        alert('La transformación ya fue procesada.');
    }
    else if (est == '7') {
        alert('No puede procesar. La transformación esta Anulada.');
    }
    else if (est == '24') {
        alert('Ésta Transformación ya fue iniciada.');
    }
    else if (est == '21') {
        $.ajax({
            type: 'GET',
            url: 'iniciar_transformacion/' + id_transformacion,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                $('#listaTransformacionesPendientes').DataTable().ajax.reload();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

// $('#listaTransformaciones tbody').on("click", "button.ingreso", function () {
//     var id = $(this).data('id');
//     abrir_ingreso(id);
// });

// $('#listaTransformaciones tbody').on("click", "button.salida", function () {
//     var id = $(this).data('id');
//     abrir_salida(id);
// });

// function abrir_salida(id_transformacion) {
//     if (id_transformacion != '') {
//         $.ajax({
//             type: 'GET',
//             url: 'id_salida_transformacion/' + id_transformacion,
//             dataType: 'JSON',
//             success: function (id_salida) {
//                 if (id_salida > 0) {
//                     console.log(id_salida);
//                     // var id = encode5t(id_salida);
//                     window.open('imprimir_salida/' + id_salida);
//                 } else {
//                     alert('Esta Transformación no tiene Salida');
//                 }
//             }
//         }).fail(function (jqXHR, textStatus, errorThrown) {
//             console.log(jqXHR);
//             console.log(textStatus);
//             console.log(errorThrown);
//         });
//     } else {
//         alert('Debe seleccionar una Transformación!');
//     }
// }

// function abrir_ingreso(id_transformacion) {
//     if (id_transformacion != '') {
//         $.ajax({
//             type: 'GET',
//             url: 'id_ingreso_transformacion/' + id_transformacion,
//             dataType: 'JSON',
//             success: function (id_ingreso) {
//                 if (id_ingreso > 0) {
//                     console.log(id_ingreso);
//                     // var id = encode5t(id_ingreso);
//                     window.open('imprimir_ingreso/' + id_ingreso);
//                 } else {
//                     alert('Esta Transformación no tiene Ingreso');
//                 }
//             }
//         }).fail(function (jqXHR, textStatus, errorThrown) {
//             console.log(jqXHR);
//             console.log(textStatus);
//             console.log(errorThrown);
//         });
//     } else {
//         alert('Debe seleccionar una Transformación!');
//     }
// }

let id_cc = null;
let tipo = null;
let id_almacen = null;
let oportunidad = null;

let lista_materias = [];
let lista_servicios = [];
let lista_sobrantes = [];
let lista_transformados = [];

function generar(tbody, tabla) {
    console.log("ver");
    $(tbody).on("click", "button.generar_transformacion", function () {
        id_cc = $(this).data('id');
        tipo = $(this).data('tipo');
        oportunidad = $(this).data('oportunidad');
        $('[name=id_cc]').val(id_cc);
        $('[name=tipo]').val(tipo);
        $('[name=oportunidad]').val(oportunidad);
        $('#modal-transformacion_create').modal({
            show: true
        });
        $('#submit_transformacion').removeAttr('disabled');
        lista_materias = [];
        lista_servicios = [];
        lista_sobrantes = [];
        lista_transformados = [];
        obtenerCuadro(id_cc, tipo);
    });
}

function obtenerCuadro(id_cc, tipo) {
    $.ajax({
        type: 'GET',
        url: 'obtenerCuadro/' + id_cc + '/' + tipo,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            response['materias_primas'].forEach(
                function (element) {
                    var materia = {
                        'part_no': element.part_no,
                        'descripcion': element.descripcion,
                        'cantidad': element.cantidad,
                        'unitario': element.precio,
                        'total': (element.cantidad * element.precio)
                    };
                    lista_materias.push(materia);
                }
            );
            response['servicios'].forEach(
                function (element) {
                    if (element.part_no !== null && element.part_no !== 'NULL') {
                        var gasto = {
                            'descripcion': element.descripcion,
                            'total': element.costo
                        };
                        lista_servicios.push(gasto);
                    } else {
                        var servicio = {
                            'part_no': element.part_no,
                            'descripcion': element.descripcion,
                            'cantidad': element.cantidad,
                            'unitario': element.precio,
                            'total': (element.cantidad * element.precio)
                        };
                        lista_materias.push(servicio);
                    }
                }
            );
            response['gastos'].forEach(
                function (element) {
                    var gasto = {
                        'descripcion': element.descripcion,
                        'total': element.costo
                    };
                    lista_servicios.push(gasto);
                }
            );
            mostrarCuadros();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function mostrarCuadros() {
    var html_materia = '';
    var i = 1;
    lista_materias.forEach(function (element) {
        html_materia += `<tr id="${i}">
            <td>${element.part_no}</td>
            <td>${element.descripcion}</td>
            <td>${element.cantidad}</td>
            <td>${element.unitario}</td>
            <td>${element.total}</td>
            </tr>`;
        i++;
    });
    $('#listaMateriasPrimas tbody').html(html_materia);

    var html_servicio = '';
    i = 1;
    lista_servicios.forEach(function (element) {
        html_servicio += `<tr id="${i}">
            <td>${element.descripcion}</td>
            <td>${element.total}</td>
            <td>
                <i class="fas fa-trash icon-tabla red boton delete" 
                data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
            </td>
        </tr>`;
        i++;
    });
    $('#listaServiciosDirectos tbody').html(html_servicio);

}

$("#form-transformacion_create").on("submit", function (e) {
    e.preventDefault();
    var alm = $('[name=id_almacen]').val();

    if (alm !== '0') {
        var serial = $(this).serialize();
        var data = serial +
            '&lista_materias=' + JSON.stringify(lista_materias) +
            '&lista_servicios=' + JSON.stringify(lista_servicios) +
            '&lista_sobrantes=' + JSON.stringify(lista_sobrantes) +
            '&lista_transformados=' + JSON.stringify(lista_transformados);

        $('#submit_transformacion').attr('disabled', 'true');
        generarTransformacion(data);
        $('#modal-transformacion_create').modal('hide');
    } else {
        alert('Es necesario que seleccione un almacén!');
    }
});

function generarTransformacion(data) {
    // var data =  'id_cc='+id_cc+
    //             '&tipo='+tipo+
    //             '&oportunidad='+oportunidad+
    //             '&id_almacen='+id_almacen+
    //             '&lista_materias='+JSON.stringify(lista_materias)+
    //             '&lista_servicios='+JSON.stringify(lista_servicios)+
    //             '&lista_sobrantes='+JSON.stringify(lista_sobrantes)+
    //             '&lista_transformados='+JSON.stringify(lista_transformados);
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'generarTransformacion',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            alert(response);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function ver(tbody, tabla) {
    console.log("ver");
    $(tbody).on("click", "button.ver", function () {
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined && data.id_guia_com !== null){
        //     abrir_ingreso(data.id_guia_com);
        // }
    });
}
function atender(tbody, tabla) {
    console.log("atender");
    $(tbody).on("click", "button.atender", function () {
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined){
        //     open_transferencia_detalle(data);
        // }
    });
}
function anular(tbody, tabla) {
    console.log("anular");
    $(tbody).on("click", "button.anular", function () {
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined){
        //     if (data.guia_com == '-'){
        //         $.ajax({
        //             type: 'GET',
        //             url: 'anular_transferencia/'+data.id_transferencia,
        //             dataType: 'JSON',
        //             success: function(response){
        //                 if (response > 0){
        //                     alert('Transferencia anulada con éxito');
        //                 }
        //             }
        //         }).fail( function( jqXHR, textStatus, errorThrown ){
        //             console.log(jqXHR);
        //             console.log(textStatus);
        //             console.log(errorThrown);
        //         });
        //     } else {
        //         alert('No se puede anular por que ya tiene Ingreso a Almacén.');
        //     }
        // }
    });
}
function abrir_transformacion(id_transformacion) {
    console.log('abrir_transformacio' + id_transformacion);
    localStorage.setItem("id_transfor", id_transformacion);
    // location.assign("/logistica/almacen/customizacion/hoja-transformacion/index");
    var win = window.open("/cas/customizacion/hoja-transformacion/index", '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}