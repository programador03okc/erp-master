$(function(){
    $('#listaHorario tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaHorario').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer label').text(idTr);
    });

});

function modalHorarios(){
    $('#modal-horario').modal({
        show: true,
        backdrop: 'static'
    });
    listarHorarios();
}

function listarHorarios() {
    var vardataTables = funcDatatables();
    $('#listaHorario').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_horarios',
        'columns': [
            {'data': 'id_horario'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatHour(row['hora_ent_reg_sem']) + ' - ' + formatHour(row['hora_sal_reg_sem']));
                }
            },
            {'render':
                function (data, type, row){
                    return (formatHour(row['hora_sal_alm_sem']) + ' - ' + formatHour(row['hora_ent_alm_sem']));
                }
            },
            {'render':
                function (data, type, row){
                    return (formatHour(row['hora_ent_reg_sab']) + ' - ' + formatHour(row['hora_sal_reg_sab']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}]
    });
}

function selectValue(){
    var myId = $('.modal-footer label').text();
    $('[name=id_horario]').val(myId);
    mostrar_horario(myId)
    changeStateButton('historial');
    $('#modal-horario').modal('hide');
}

function mostrar_horario(id){
    baseUrl = 'cargar_horario/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=hora_ini_reg]').val(response[0].hora_ent_reg_sem);
            $('[name=hora_fin_reg]').val(response[0].hora_sal_reg_sem);
            $('[name=hora_ini_alm]').val(response[0].hora_sal_alm_sem);
            $('[name=hora_fin_alm]').val(response[0].hora_ent_alm_sem);
            $('[name=hora_ini_sab]').val(response[0].hora_ent_reg_sab);
            $('[name=hora_fin_sab]').val(response[0].hora_sal_reg_sab);
            $('[name=dias_sem]').val(response[0].dias_sem);
            $('[name=hora_sem]').val(response[0].hora_sem);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_horario(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_horario';
        msj = 'Horario registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_horario';
        msj = 'Horario editado con exito';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert(msj);
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_horario(ids){
    baseUrl = 'anular_horario/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Horario anulada con exito');
                changeStateButton('anular');
                clearForm('form-horario');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}