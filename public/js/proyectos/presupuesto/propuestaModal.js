let crono_modal = 0;

$(function(){
    $('#listaPropuestas tbody').on("click","tr", function(){
        let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
        var id = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;
        console.log(id);

        if (id !== null && id !== undefined){

            if (formName == 'cronovalpro'){
                $('[name=id_presupuesto]').val(id);
                $('#codigo').text(cod);
                $('[name=nombre_opcion]').val(des);
                $('#modal-propuesta').modal('hide');
                
                if (crono_modal == 0){
                    $('[name=modo]').val('new');
                    $('[name=numero]').attr('disabled',false);
                    $('[name=unid_program]').attr('disabled',false);
                    $('[name=btn_actualizar]').attr('disabled',false);
                } else {
                    $('[name=modo]').val('update');
                    $('[name=numero]').attr('disabled',true);
                    $('[name=unid_program]').attr('disabled',true);
                    $('[name=btn_actualizar]').attr('disabled',true);
                    mostrar_cronoval_propuesta();
                } 
            }
            else if (formName == 'cronopro'){
                if (crono_modal == 0){
                    var rspta = confirm('¿Está seguro que desea generar el Cronograma de Propuesta según el presupuesto seleccionado?:'+cod);
                    if (rspta){
                        $('[name=id_presupuesto]').val(id);
                        $('#codigo').text(cod);
                        $('#descripcion').text(des);
                        $('[name=modo]').val('new');
                        $('#modal-propuesta').modal('hide');
                        listar_crono_propuesta(id);
                    }
                } else {
                    console.log('id: '+id);
                    $('[name=id_presupuesto]').val(id);
                    $('#codigo').text(cod);
                    $('#descripcion').text(des);
                    $('[name=modo]').val('update');
                    $('#modal-propuesta').modal('hide');
                    listar_cronograma_propuesta(id);
                    mostrar_gant_propuesta(id);
                }
            }
            else if (formName == 'valorizacion'){
                $('[name=id_presup]').val(id);
                $('#codigo').text(cod);
                $('[name=nombre_opcion]').val(des);
                $('[name=fecha_valorizacion]').val(fecha_actual());
                $('[name=modo]').val('new');
                $('#modal-propuesta').modal('hide');
                mostrar_nueva_valorizacion(id);
            }
            else if (formName == 'curvas'){
                var id_pres = $(this)[0].childNodes[3].innerHTML;
                $('[name=id_presup]').val(id);
                $('#codigo').text(cod);
                $('[name=nombre_opcion]').val(des);
                $('#modal-propuesta').modal('hide');
                mostrar_graficos(id,id_pres);
            }
            else if (formName == 'propuesta'){
                mostrar_propuesta(id);
                document.getElementById('btnCopiar').removeAttribute("disabled");
            }
        }
    });
});

function propuestaModal(modal){
    $('#modal-propuesta').modal({
        show: true
    });
    clearDataTable();
    console.log('modal'+modal);
    if (modal == ''){
        listarPropuesta();
    }
    else if (modal === 'nuevo'){
        crono_modal = 0;
        listarPropuestaCrono(crono_modal);
    }
    else if (modal === 'modal'){
        crono_modal = 1;
        listarPropuestaCrono(crono_modal);
    }
    else if (modal === 'nuevaval'){
        crono_modal = 0;
        listarPropuestaCronoVal(crono_modal);
    }
    else if (modal === 'modalval'){
        crono_modal = 1;
        listarPropuestaCronoVal(crono_modal);
    }
    else if (modal === 'valorizacion'){
        listarPropuestasActivas();
    }
    else if (modal === 'curvas'){
        listarPropuestaPresEje();
    }
}

function listarPropuestaPresEje(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPropuestas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_propuestas_preseje',
        'columns': [
            {'data': 'id_presup'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'id_presupuesto'}
        ],
        'columnDefs': [{ 'aTargets': [0,3], 'sClass': 'invisible'}],
    });
}

function listarPropuesta(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPropuestas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_propuestas',
        'columns': [
            {'data': 'id_presup'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listarPropuestaCrono(crono_modal){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPropuestas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_propuesta_crono/'+crono_modal,
        'columns': [
            {'data': 'id_presup'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listarPropuestaCronoVal(crono_modal){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPropuestas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_propuesta_cronoval/'+crono_modal,
        'columns': [
            {'data': 'id_presup'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listarPropuestasActivas(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPropuestas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_propuestas_activas',
        'columns': [
            {'data': 'id_presup'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
