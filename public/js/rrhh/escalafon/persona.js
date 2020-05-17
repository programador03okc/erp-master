$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaPersona tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersona').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer label').text(idTr);
    });

});

function modalPersona(){
    $('#modal-persona').modal({
        show: true,
        backdrop: 'static'
    });
    listarPersonas();
}
function listarPersonas() {
    var vardataTables = funcDatatables();
    $('#listaPersona').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'listar_personas',
        'columns': [
            {'data': 'id_persona',},
            {'data': 'nro_documento'},
            {'render':
                function (data, type, row){
                    return (row['apellido_paterno'] + ' ' + row['apellido_materno'] + ' ' + row['nombres']);
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_nacimiento']));
                }
            },
            {'render':
                function (data, type, row){
                    return (calcularEdad(row['fecha_nacimiento']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'asc']
        ]
    });
}

function selectValue(){
    var myId = $('.modal-footer label').text();
    $('[name=id_persona]').val(myId);
    mostrar_persona(myId)
    changeStateButton('historial');
    $('#modal-persona').modal('hide');
}

function mostrar_persona(id){
    baseUrl = 'cargar_persona/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_documento_identidad]').val(response[0].id_documento_identidad);
            $('[name=nro_documento]').val(response[0].nro_documento);
            $('[name=nombres]').val(response[0].nombres);
            $('[name=apellido_paterno]').val(response[0].apellido_paterno);
            $('[name=apellido_materno]').val(response[0].apellido_materno);
            $('[name=sexo]').val(response[0].sexo);
            $('[name=fecha_nacimiento]').val(response[0].fecha_nacimiento);
            $('[name=id_estado_civil]').val(response[0].id_estado_civil);
            valueLengthDoc(response[0].id_documento_identidad);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_persona(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_persona';
        msj = 'Persona registrada con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_persona';
        msj = 'Persona editada con exito';
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
                $('[name=nro_documento]').attr('maxlength', 0);
            }else if(response == 'exist'){
                alert('El DNI ingresado ya pertenece a una persona');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_persona(ids){
    baseUrl = 'anular_persona/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Persona anulada con exito');
                changeStateButton('anular');
                clearForm('form-persona');
            }else{
                alert('No puede eliminar el registro, está vinculada con un postulante');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function valueLengthDoc(value){
    baseUrl = 'digitos_documento/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=nro_documento]').attr('maxlength', response[0].longitud);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}