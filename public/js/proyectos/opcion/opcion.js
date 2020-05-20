$(function(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaOpcion').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_opciones',
        'columns': [
            {'data': 'id_op_com'},
            {'data': 'codigo'},
            {'render': 
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'des_tp_proyecto'},
            {'data': 'cantidad'},
            {'data': 'des_program'},
            {'data': 'des_modalidad'},
            {'data': 'nombre_corto'},
            {'data': 'estado_doc'},
            // {'render':
            //     function (data, type, row){
            //         return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
            //     }
            // },
            {'defaultContent': 
            '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Editar" >'+
                '<i class="fas fa-edit"></i></button>'+
            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Anular" >'+
                '<i class="fas fa-trash"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaOpcion tbody',tabla)
});
function botones(tbody, tabla){
    console.log("editar");
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_opcion_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_opcion(data.id_op_com);
    });
}
function open_opcion_create(data){
    $('#modal-opcion_create').modal({
        show: true
    });

    if (data !== undefined){
        $('[name=id_op_com]').val(data.id_op_com);
        $('[name=id_empresa]').val(data.id_empresa);
        $('[name=codigo]').val(data.codigo);
        $('[name=descripcion]').val(data.descripcion);
        $('[name=cliente_razon_social]').val(data.razon_social);
        $('[name=tp_proyecto]').val(data.tp_proyecto);
        $('[name=id_cliente]').val(data.cliente);
        $('[name=unid_program]').val(data.unid_program);
        $('[name=cantidad]').val(data.cantidad);
        $('[name=modalidad]').val(data.modalidad);
        $('[name=fecha_emision]').val(data.fecha_emision);
        // $('[name=iu]').val(data.iu).trigger('change.select2');
    } else {
        $('[name=id_op_com]').val('');
        $('[name=id_empresa]').val(1);
        $('[name=codigo]').val('');
        $('[name=descripcion]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=tp_proyecto]').val('');
        $('[name=id_cliente]').val('');
        $('[name=unid_program]').val(0);
        $('[name=cantidad]').val('');
        $('[name=modalidad]').val(0);
        // $('[name=fecha_emision]').val('');
    }
    $("#btnGuardarOpcion").removeAttr('disabled');
}
function guardar_opcion(){
    var id = $('[name=id_op_com]').val();
    // var cod = $('[name=codigo]').val();
    var des = $('[name=descripcion]').val();
    var tp = $('[name=tp_proyecto]').val();
    var emp = $('[name=id_empresa]').val();
    var cli = $('[name=id_cliente]').val();
    var mod = $('[name=modalidad]').val();
    var prog = $('[name=unid_program]').val();
    var cant = $('[name=cantidad]').val();
    var fech = $('[name=fecha_emision]').val();

    var data = 'id_op_com='+id+
            '&id_empresa='+emp+
            '&tp_proyecto='+tp+
            '&descripcion='+des+
            '&cliente='+cli+
            '&unid_program='+prog+
            '&cantidad='+cant+
            '&modalidad='+mod+
            '&fecha_emision='+fech;
    console.log(data);

    // var token = $('#token').val();
    var baseUrl;
    if (id !== ''){
        baseUrl = 'actualizar_opcion';
    } else {
        baseUrl = 'guardar_opcion';
    }
    var msj = verificaOpcion();
    if (msj.length > 0){
        alert(msj);
    } 
    else {
        // document.getElementById("btnGuardarOpcion").value = "Enviando...";
        $("#btnGuardarOpcion").attr('disabled','true');
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Opcion Comercial registrada con éxito');
                    $('#modal-opcion_create').modal('hide');
                    $('#listaOpcion').DataTable().ajax.reload();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function anular_opcion(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular ésta Opción?")
        if (rspta){
            baseUrl = 'anular_opcion/'+ids;
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Opción Comercial anulada con éxito');
                        $('#listaOpcion').DataTable().ajax.reload();
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    }
}

function moneda(){
    $moneda = $('select[name="moneda"] option:selected').text();
    console.log($moneda);
    $simbolo = $moneda.split(" - ");
    if ($simbolo.length > 0){
        console.log($simbolo[1]);
        $('[name=simbolo]').val($simbolo[1]);
    } else {
        $('[name=simbolo]').val("");
    }
}

function verificaOpcion(){
    var descripcion = $('[name=descripcion]').val();
    var tp_proyecto = $('[name=tp_proyecto]').val();
    var id_empresa = $('[name=id_empresa]').val();
    var id_cliente = $('[name=id_cliente]').val();
    var cantidad = $('[name=cantidad]').val();
    var unid_program = $('[name=unid_program]').val();
    var msj = '';

    if (descripcion == ''){
        msj+='\n Es necesario que ingrese una descripción';
    }
    if (tp_proyecto == '0' || tp_proyecto == null){
        msj+='\n Es necesario que elija un tipo de proyecto';
    }
    if (id_empresa == '0' || id_empresa == null){
        msj+='\n Es necesario que elija una empresa';
    }
    if (id_cliente == ''){
        msj+='\n Es necesario que elija un cliente';
    }
    if (cantidad == '0' || cantidad == ''){
        msj+='\n Es necesario que ingrese un plazo > 0';
    }
    if (unid_program == '0' || unid_program == null){
        msj+='\n Es necesario que elija una unidad de programación';
    }
    return msj;
}