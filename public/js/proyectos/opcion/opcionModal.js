$(function(){
    $('#listaOpcion tbody').on("click","tr", function(){
        // var data = tabla.row($(this)).data();
        var id = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;

        let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');

        if (formName =='requerimiento'){
            $('[name=id_op_com]').val(id);
            $('[name=codigo_opcion]').val(cod);
            $('[name=nombre_opcion]').val(des);
            $('#modal-opcion').modal('hide');
        }
        else if (formName == 'preseje'){
            var rspta = confirm('¿Está seguro que desea generar el Presupuesto de Ejecución según la opción seleccionada? : \n'+data.descripcion);
            if (rspta){
                $('#modal-opcion').modal('hide');
                generar_preseje(id);
            }
        }
        else if (formName == 'propuesta'){
            $('[name=id_op_com]').val(id);
            $('[name=nombre_opcion]').val(des);
            $('#modal-opcion').modal('hide');
            mostrar_total_presint(id);
        }
        else if (formName == 'presint'){
            $('[name=id_op_com]').val(id);
            $('[name=nombre_opcion]').val(des);
            $('#modal-opcion').modal('hide');
        }
        else if (formName == 'proyecto'){
            $('#modal-opcion').modal('hide');
            mostrar_opcion(id);
            // $('[name=id_op_com]').val(data.id_op_com);
            // $('[name=codigo_opcion]').val(data.codigo);
            // $('[name=nombre_opcion]').val(data.descripcion);
            // $('[name=id_empresa]').val(data.id_empresa);
            // $('[name=tp_proyecto]').val(data.tp_proyecto);
            // $('[name=modalidad]').val(data.modalidad);
            // $('[name=unid_program]').val(data.unid_program);
            // $('[name=plazo_ejecucion]').val(data.cantidad);
            // $('[name=id_cliente]').val(data.cliente);
            // $('[name=id_contrib]').val(data.id_contribuyente);
            // $('[name=cliente_razon_social]').val(data.razon_social);
            // $('#modal-opcion').modal('hide');
            // if (formName == 'proyecto'){
            //     change_fechas();
            // }
        }
    });

});

function listarOpcion(ruta){
    var vardataTables = funcDatatables();
    var tabla = $('#listaOpcion').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': '/'+ruta,
        'bDestroy': true,
        'retrieve': true,
        'columns': [
            {'data': 'id_op_com'},
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

function open_opcion_modal(){
    $('#modal-opcion').modal({
        show: true
    });
    
    let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
    
    if(formName =='preseje'){
        listarOpcion('listar_opciones_sin_preseje');
    } 
    else if(formName =='presint'){
        listarOpcion('listar_opciones_sin_presint');
    } 
    else {
        listarOpcion('listar_opciones');
    }
}
