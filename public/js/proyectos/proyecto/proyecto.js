$(function(){
    // $('[name=moneda]').val(1).trigger('change.select2');
    // $('[name=moneda_contrato]').val(1).trigger('change.select2');

    $("#form-proyecto").on("submit", function(e){
        e.preventDefault();
        guardar_proyecto();
    });

    var vardataTables = funcDatatables();
    var tabla = $('#listaProyecto').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_proyectos',
        'columns': [
            {'data': 'id_proyecto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'nombre_tp_proyecto'},
            {'data': 'nombre_modalidad'},
            {'data': 'nombre_sis_contrato'},
            {'data': 'simbolo'},
            {'data': 'importe'},
            {'data': 'usuario'},
            {'render': 
                function (data, type, row){
                    return (row['plazo_ejecucion']+' '+row['des_unid_prog']);
                }
            },
            {'render': 
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'defaultContent': 
            '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Editar" >'+
                '<i class="fas fa-edit"></i></button>'+
            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Anular" >'+
                '<i class="fas fa-trash"></i></button>'+
            '<button type="button" class="contrato btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver Contratos" >'+
                '<i class="fas fa-file-upload"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaProyecto tbody',tabla);
});

function botones(tbody, tabla){
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_proyecto_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_proyecto(data.id_proyecto);
    });
    $(tbody).on("click","button.contrato", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_proyecto_contrato(data);
    });
}

function open_proyecto_create(data){
    $('#modal-proyecto_create').modal({
        show: true
    });
    if (data !== undefined){
        console.log(data);
        $.ajax({
            type: 'GET',
            url: 'mostrar_proyecto/'+data.id_proyecto,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=id_proyecto]').val(response['proyecto'].id_proyecto);
                $('[name=codigo]').val(response['proyecto'].codigo);
                $('[name=id_empresa]').val(response['proyecto'].empresa);
                $('[name=id_op_com]').val(response['proyecto'].id_op_com);
                $('[name=nombre_opcion]').val(response['proyecto'].descripcion);
                $('[name=tp_proyecto]').val(response['proyecto'].tp_proyecto);
                $('[name=id_cliente]').val(response['proyecto'].cliente);
                $('[name=cliente_razon_social]').val(response['proyecto'].razon_social);
                $('[name=moneda]').val(response['proyecto'].moneda);
                $('[name=simbolo]').val(response['proyecto'].simbolo);
                $('[name=importe]').val(response['proyecto'].importe);
                $('[name=sis_contrato]').val(response['proyecto'].sis_contrato);
                $('[name=modalidad]').val(response['proyecto'].modalidad);
                $('[name=plazo_ejecucion]').val(response['proyecto'].plazo_ejecucion);
                $('[name=unid_program]').val(response['proyecto'].unid_program);
                $('[name=fecha_inicio]').val(response['proyecto'].fecha_inicio);
                $('[name=fecha_fin]').val(response['proyecto'].fecha_fin);
                
                if (response['primer_contrato'] !== null){
                    $('[name=id_tp_contrato_proy]').val(response['primer_contrato'].id_tp_contrato);
                    $('[name=nro_contrato_proy]').val(response['primer_contrato'].nro_contrato);
                    $('[name=descripcion_proy]').val(response['primer_contrato'].descripcion);
                    $('[name=fecha_contrato_proy]').val(response['primer_contrato'].fecha_contrato);
                    $('[name=importe_contrato_proy]').val(response['primer_contrato'].importe);
                    $('[name=moneda_contrato]').val(response['primer_contrato'].moneda);
                } else {
                    $('[name=id_tp_contrato_proy]').val('');
                    $('[name=nro_contrato_proy]').val('');
                    $('[name=descripcion_proy]').val('');
                    $('[name=fecha_contrato_proy]').val('');
                    $('[name=importe_contrato_proy]').val('');
                    $('[name=moneda_contrato]').val('');
                }
                // $('[name=fecha_fin]').val(response['primer_contrato'].fecha_fin);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        $('[name=id_proyecto]').val('');
        $('[name=codigo]').val('');
        $('[name=id_empresa]').val('');
        $('[name=id_op_com]').val('');
        $('[name=nombre_opcion]').val('');
        $('[name=tp_proyecto]').val('');
        $('[name=id_cliente]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=moneda]').val('1');
        $('[name=simbolo]').val('S/.');
        $('[name=importe]').val('');
        $('[name=sis_contrato]').val('1');
        $('[name=modalidad]').val('');
        $('[name=plazo_ejecucion]').val('');
        $('[name=unid_program]').val('');
        $('[name=jornal]').val(8);
        
        $('[name=id_tp_contrato_proy]').val('1');
        $('[name=nro_contrato_proy]').val('');
        $('[name=descripcion_proy]').val('');
        $('[name=importe_contrato_proy]').val('');
        $('[name=moneda_contrato]').val('1');
    }
}

function mostrar_opcion(id){
    if (id !== ''){
        $.ajax({
            type: 'GET',
            url: 'mostrar_opcion/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=id_op_com]').val(response.id_op_com);
                $('[name=codigo_opcion]').val(response.codigo);
                $('[name=nombre_opcion]').val(response.descripcion);
                $('[name=id_empresa]').val(response.id_empresa);
                $('[name=tp_proyecto]').val(response.tp_proyecto);
                $('[name=modalidad]').val(response.modalidad);
                $('[name=unid_program]').val(response.unid_program);
                $('[name=plazo_ejecucion]').val(response.cantidad);
                $('[name=importe]').val(formatDecimal(response.sub_total_propuesta));
                $('[name=importe_contrato_proy]').val(formatDecimal(response.sub_total_propuesta));
                $('[name=id_cliente]').val(response.cliente);
                $('[name=id_contrib]').val(response.id_contribuyente);
                $('[name=cliente_razon_social]').val(response.razon_social);
                change_fechas();
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });        
    }
}

// function guardar_proyecto(){
//     var id = $('[name=id_proyecto]').val();
//     var id_op = $('[name=id_op_com]').val();
//     var des = $('[name=nombre_opcion]').val();
//     var tp = $('[name=tp_proyecto]').val();
//     var emp = $('[name=id_empresa]').val();
//     var cli = $('[name=id_cliente]').val();
//     var mon = $('[name=moneda]').val();
//     var imp = $('[name=importe]').val();
//     var sis = $('[name=sis_contrato]').val();
//     var mod = $('[name=modalidad]').val();
//     var plz = $('[name=plazo_ejecucion]').val();
//     var prog = $('[name=unid_program]').val();
//     var fec_ini = $('[name=fecha_inicio]').val();
//     var fec_fin = $('[name=fecha_fin]').val();
//     var jornal = $('[name=jornal]').val();

//     var data = 'id_proyecto='+id+
//             '&id_op_com='+id_op+
//             '&id_empresa='+emp+
//             '&tp_proyecto='+tp+
//             '&descripcion='+des+
//             '&cliente='+cli+
//             '&elaborado_por='+auth_user.id_usuario+
//             '&moneda='+mon+
//             '&importe='+imp+
//             '&sis_contrato='+sis+
//             '&modalidad='+mod+
//             '&plazo_ejecucion='+plz+
//             '&unid_program='+prog+
//             '&fecha_inicio='+fec_ini+
//             '&fecha_fin='+fec_fin+
//             '&jornal='+jornal;

//     console.log(data);

//     // var token = $('#token').val();
//     var baseUrl;
//     if (id !== ''){
//         baseUrl = 'actualizar_proyecto';
//     } else {
//         baseUrl = 'guardar_proyecto';
//     }
//     console.log(baseUrl);
//     $.ajax({
//         type: 'POST',
//         // headers: {'X-CSRF-TOKEN': token},
//         url: baseUrl,
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 alert('Proyecto registrado con éxito');
//                 $('#modal-proyecto_create').modal('hide');
//                 $('#listaProyecto').DataTable().ajax.reload();
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
function validaCabecera(){
    var op_com = $('[name=id_op_com]').val();
    var tp_proy = $('[name=tp_proyecto]').val();
    var emp = $('[name=id_empresa]').val();
    var cli = $('[name=id_cliente]').val();
    var sis = $('[name=sis_contrato]').val();
    var plz = $('[name=plazo_ejecucion]').val();
    var prog = $('[name=unid_program]').val();
    var imp = $('[name=importe]').val();
    var mod = $('[name=modalidad]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();
    var tp_con = $('[name=id_tp_contrato_proy]').val();
    var nro_con = $('[name=nro_contrato_proy]').val();
    var des_con = $('[name=descripcion_proy]').val();
    var fec_con = $('[name=fecha_contrato_proy]').val();
    var imp_con = $('[name=importe_contrato_proy]').val();
    var mnd_con = $('[name=moneda_contrato]').val();
    var adj = $('[name=primer_adjunto]').val();
    var msj = '';

    if (op_com == ''){
        msj+='\n Es necesario que elija una Opción Comercial';
    }
    if (tp_proy == '' || tp_proy == '0'){
        msj+='\n Es necesario que seleccione un Tipo de Proyecto';
    }
    if (emp == '' || emp == '0'){
        msj+='\n Es necesario que seleccione una Empresa';
    }
    if (cli == ''){
        msj+='\n Es necesario que ingrese un Cliente';
    }
    if (sis == '' || sis == '0'){
        msj+='\n Es necesario que elija un Sistema de Contrato';
    }
    if (plz == ''){
        msj+='\n Es necesario que ingrese un Plazo de Ejecución';
    }
    if (prog == '' || prog == '0'){
        msj+='\n Es necesario que elija una unidad de programación';
    }
    if (plz == ''){
        msj+='\n Es necesario que ingrese un Plazo de Ejecución';
    }
    if (imp == ''){
        msj+='\n Es necesario que ingrese un Importe';
    }
    if (mod == '' || mod == '0'){
        msj+='\n Es necesario que seleccione una modalidad';
    }
    if (fini == ''){
        msj+='\n Es necesario que ingrese una Fecha Inicio';
    }
    if (ffin == ''){
        msj+='\n Es necesario que ingrese una Fecha Fin';
    }
    if (tp_con == ''){
        msj+='\n Es necesario que ingrese un Tipo de Contrato';
    }
    if (nro_con == ''){
        msj+='\n Es necesario que ingrese un Nro de Contrato';
    }
    if (des_con == ''){
        msj+='\n Es necesario que ingrese una Descripcion del contrato';
    }
    if (fec_con == ''){
        msj+='\n Es necesario que ingrese una Fecha de Contrato';
    }
    if (imp_con == ''){
        msj+='\n Es necesario que ingrese un Importe de Contrato';
    }
    if (mnd_con == ''){
        msj+='\n Es necesario que ingrese una Moneda de Contrato';
    }
    var id_pro = $('[name=id_proyecto]').val();
    if (id_pro == '' && adj == ''){
        msj+='\n Es necesario que ingrese un Adjunto';
    }
    return msj;
}

function guardar_proyecto(){
    var id_pro = $('[name=id_proyecto]').val();
    var formData = new FormData($('#form-proyecto')[0]);
    console.log(formData);
    console.log('id_pro:'+id_pro);
    var url = '';
    if (id_pro == ''){
        url = 'guardar_proyecto';
    } else {
        url = 'actualizar_proyecto';
    }
    var msj = validaCabecera();

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Proyecto registrado con éxito');
                    $('#modal-proyecto_create').modal('hide');
                    $('#listaProyecto').DataTable().ajax.reload();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function anular_proyecto(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular éste Proyecto?")
        if (rspta){
            baseUrl = 'anular_proyecto/'+ids;
            $.ajax({
                type: 'GET',
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Proyecto anulado con éxito');
                        $('#listaProyecto').DataTable().ajax.reload();
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

function change_moneda(){
    var id_moneda = $('[name=moneda]').val();

    if (id_moneda == 1){
        $('[name=simbolo]').val("S/.");
    } 
    else if (id_moneda == 2){
        $('[name=simbolo]').val("US$");
    }
    else {
        $('[name=simbolo]').val("");
    }
}

function change_fechas(){
    var fini = $('[name=fecha_inicio]').val();
    var cant = $('[name=plazo_ejecucion]').val();
    var unid = $('[name=unid_program]').val();
    console.log(unid);
    var dias = 0;
    switch(unid){
        case '1':
            dias = parseFloat(cant) * 1;
            break;
        case '2':
            dias = parseFloat(cant) * 7;
            break;
        case '3':
            dias = parseFloat(cant) * 15;
            break;
        case '4':
            dias = parseFloat(cant) * 30;
            break;
        case '5':
            dias = parseFloat(cant) * 365;
            break;
        default:
            break;        
    }
    console.log('dias'+dias+' fini'+fini);
    var ffin = sumaFecha(dias, fini);
    $("[name=fecha_fin]").val(ffin);
}