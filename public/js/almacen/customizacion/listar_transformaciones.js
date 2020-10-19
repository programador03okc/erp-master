class GestionCustomizacion
{
    constructor(permiso)
    {
        this.permiso = permiso;
        // this.listarCuadrosCostos();
        this.listarTransformaciones();
    }

    listarCuadrosCostos() {
        const permiso = this.permiso;
        var vardataTables = funcDatatables();
        var tabla = $('#listaCuadrosCostos').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy' : true,
            'serverSide' : true,
            'ajax': {
                url: 'listarCuadrosCostos',
                type: 'POST'
            },
            'columns': [
                {'data': 'id'},
                {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
                {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
                {'data': 'nombre', 'name': 'entidades.nombre'},
                {'data': 'estado', 'name': 'estados_aprobacion.estado'},
                {'data': 'prioridad'},
                {'data': 'fecha_entrega'},
                {'render': function (data, type, row){
                        return row['tipo_cuadro'] == 1 ? 'Acuerdo Marco' : 'Venta Directa';
                    }
                },
                {'data': 'name', 'name': 'users.name'}
            ],
            'columnDefs': [
                {'aTargets': [0], 'sClass': 'invisible'},
                {'render': function (data, type, row){
                    // console.log(permiso == '1');
                        // if (permiso !== '1') {
                            return `<button type="button" class="generar_transformacion btn btn-success btn-sm " data-toggle="tooltip"
                            data-placement="bottom" data-id="${row['id']}" data-tipo="${row['tipo_cuadro']}" data-oportunidad="${row['oportunidad']}" 
                            title="Generar Hoja de Transformación"><i class="fas fa-angle-double-right"></i></button>`;
                        // }
                    }, targets: 9
                }
            ],
        });
        generar("#listaCuadrosCostos tbody", tabla);
    }

    listarTransformaciones(){
        var vardataTables = funcDatatables();
        var tabla = $('#listaTransformacionesMadres').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy':true,
            'ajax' : 'listar_todas_transformaciones',
            // 'ajax': {
            //     url:'listar_transferencias_pendientes/'+alm_origen+'/'+alm_destino,
            //     dataSrc:''
            // },
            'columns': [
                {'data': 'id_transformacion'},
                {'render':
                    function (data, type, row){
                        return (formatDate(row['fecha_registro']));
                    }
                },
                {'render':
                    function (data, type, row){
                        return (formatDate(row['fecha_transformacion']));
                    }
                },
                {'render':
                    function (data, type, row){
                        return ('<label class="lbl-codigo" title="Abrir Transformación" onClick="abrir_transformacion('+row['id_transformacion']+')">'+row['codigo']+'</label>');
                    }
                },
                {'data': 'codigo_oportunidad'},
                {'data': 'descripcion'},
                {'data': 'nombre_responsable'},
                {'data': 'observacion'},
                // {'data': 'nombre_registrado'},
                {'render':
                    function (data, type, row){
                        return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                    }
                },
                {'render':
                    function (data, type, row){
                        return ('<button type="button" class="salida btn btn-success boton" data-toggle="tooltip" '+
                                    'data-placement="bottom" title="Ver Salida" data-id="'+row['id_transformacion']+'">'+
                                    '<i class="fas fa-search"></i></button>'+
                                    
                                '<button type="button" class="ingreso btn btn-primary boton" data-toggle="tooltip" '+
                                    'data-placement="bottom" title="Ver Ingreso" data-id="'+row['id_transformacion']+'">'+
                                    '<i class="fas fa-search"></i></button>'+
                                
                                '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                                    'data-placement="bottom" title="Anular" >'+
                                    '<i class="fas fa-trash"></i></button>');
                    }
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }
}

$('#listaTransformacionesMadres tbody').on("click","button.ingreso", function(){
    var id = $(this).data('id');
    abrir_ingreso(id);
});

$('#listaTransformacionesMadres tbody').on("click","button.salida", function(){
    var id = $(this).data('id');
    abrir_salida(id);
});

function abrir_salida(id_transformacion){
    if (id_transformacion != ''){
        $.ajax({
            type: 'GET',
            url: 'id_salida_transformacion/'+id_transformacion,
            dataType: 'JSON',
            success: function(id_salida){
                if (id_salida > 0){
                    console.log(id_salida);
                    var id = encode5t(id_salida);
                    window.open('imprimir_salida/'+id);
                } else {
                    alert('Esta Transformación no tiene Salida');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar una Transformación!');
    }
}

function abrir_ingreso(id_transformacion){
    if (id_transformacion != ''){
        $.ajax({
            type: 'GET',
            url: 'id_ingreso_transformacion/'+id_transformacion,
            dataType: 'JSON',
            success: function(id_ingreso){
                if (id_ingreso > 0){
                    console.log(id_ingreso);
                    var id = encode5t(id_ingreso);
                    window.open('imprimir_ingreso/'+id);
                } else {
                    alert('Esta Transformación no tiene Ingreso');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar una Transformación!');
    }
}

let id_cc = null;
let tipo = null;
let id_almacen = null;
let oportunidad = null;

let lista_materias = [];
let lista_servicios = [];
let lista_sobrantes = [];
let lista_transformados = [];

function generar(tbody, tabla){
    console.log("ver");
    $(tbody).on("click","button.generar_transformacion", function(){
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
        obtenerCuadro(id_cc,tipo);
    });
}

function obtenerCuadro(id_cc,tipo){
    $.ajax({
        type: 'GET',
        url: 'obtenerCuadro/'+id_cc+'/'+tipo,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            response['materias_primas'].forEach(
                function(element) {
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
                function(element) {
                    if (element.part_no !== null && element.part_no !== 'NULL'){
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
                function(element) {
                    var gasto = {
                        'descripcion': element.descripcion,
                        'total': element.costo
                    };
                    lista_servicios.push(gasto);
                }
            );
            mostrarCuadros();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function mostrarCuadros(){
    var html_materia = '';
    var i = 1;
    lista_materias.forEach(function(element) {
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
    lista_servicios.forEach(function(element) {
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

$("#form-transformacion_create").on("submit", function(e){
    e.preventDefault();
    var alm = $('[name=id_almacen]').val();

    if (alm !== '0'){
        var serial = $(this).serialize();
        var data = serial+
        '&lista_materias='+JSON.stringify(lista_materias)+
        '&lista_servicios='+JSON.stringify(lista_servicios)+
        '&lista_sobrantes='+JSON.stringify(lista_sobrantes)+
        '&lista_transformados='+JSON.stringify(lista_transformados);

        $('#submit_transformacion').attr('disabled','true');
        generarTransformacion(data);
        $('#modal-transformacion_create').modal('hide');
    } else {
        alert('Es necesario que seleccione un almacén!');
    }
});

function generarTransformacion(data){
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
        success: function(response){
            console.log(response);
            alert(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function ver(tbody, tabla){
    console.log("ver");
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined && data.id_guia_com !== null){
        //     abrir_ingreso(data.id_guia_com);
        // }
    });
}
function atender(tbody, tabla){
    console.log("atender");
    $(tbody).on("click","button.atender", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        // if (data !== undefined){
        //     open_transferencia_detalle(data);
        // }
    });
}
function anular(tbody, tabla){
    console.log("anular");
    $(tbody).on("click","button.anular", function(){
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
function abrir_transformacion(id_transformacion){
    console.log('abrir_transformacio'+id_transformacion);
    localStorage.setItem("id_transfor",id_transformacion);
    location.assign("/logistica/almacen/customizacion/hoja-transformacion/index");
}