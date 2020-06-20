$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaPersonas tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPersonas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var dni = $(this)[0].childNodes[1].innerHTML;
        var name = $(this)[0].childNodes[2].innerHTML;
        var tel = $(this)[0].childNodes[3].innerHTML;

        $('[name=id_persona]').val(myId);    
        $('[name=dni_persona]').val(dni);    
        $('[name=nombre_persona]').val(name);    
        $('[name=telefono_persona]').val(tel);    
        $('#modal-personaModal').modal('hide');
    });
});

function modalPersona(){
    $('#modal-personaModal').modal({
        show: true,
        backdrop: 'static'
    });
    listarPersonas();
}
function listarPersonas() {
    var vardataTables = funcDatatables();
    $('#listaPersonas').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': '/listar_personas',
        'columns': [
            {'data': 'id_persona',},
            {'data': 'nro_documento'},
            {'render':
            function (data, type, row){
                return (row['apellido_paterno'] + ' ' + row['apellido_materno'] + ' ' + row['nombres']);
            }
        },
        {'data': 'telefono','defaultContent': ''},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'asc']
        ]
    });
}

// function selectValue(){
//     var myId = $('.modal-footer label').text();
//     $('[name=id_persona]').val(myId);
//     mostrar_persona(myId)
//     // changeStateButton('historial');
//     $('#modal-personaModal').modal('hide');
// }

/*function mostrar_persona(id){
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
}*/