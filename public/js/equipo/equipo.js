$(function(){
    listar_equi_catalogo();
    $("#form-equipo").on("submit", function(){
        var data = $(this).serialize();
        // guardar_equipo(data);
        var id = $('[name=id_equipo]').val();
        var baseUrl = '';
        var msj = '';
        if (id !== ''){
            baseUrl = 'actualizar_equipo';
            msj = 'Equipo actualizado con éxito';
        } else {
            baseUrl = 'guardar_equipo';
            msj = 'Equipo registrado con éxito';
        }
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response.length > 0){
                    alert(response);
                } else { 
                    alert(msj);
                    $('#modal-equipo_create').modal('hide');
                    $('#listaEquiCatalogo').DataTable().ajax.reload();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        return false;
    });
});
function listar_equi_catalogo(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaEquiCatalogo').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_equipos',
        'columns': [
            {'data': 'id_equipo'},
            {'data': 'tipo_descripcion'},
            {'data': 'cat_descripcion'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'placa'},
            {'data': 'modelo'},
            {'data': 'des_tp_combustible'},
            {'render': 
                function (data, type, row){
                    return ('<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Editar" >'+
                    '<i class="fas fa-edit"></i></button>'+
                    '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Dar de baja" >'+
                        '<i class="fas fa-ban"></i></button>'+
                    (row['warning_docs'] == "true" ? '<i class="fas fa-exclamation-triangle" style="position:absolute;"></i>' : '')+
                    '<button type="button" class="seguro btn btn-warning boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Documentos" >'+
                        '<i class="fas fa-file-upload"></i></button>'+
                    (row['warning_mtto'] == "true" ? '<i class="fas fa-exclamation-triangle" style="position:absolute;"></i>' : '')+
                    '<button type="button" class="programacion btn btn-info boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Program. Mtto." >'+
                        '<i class="fas fa-clock"></i></button>');
                }
            }
            // {'defaultContent': 
            // '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Editar" >'+
            //     '<i class="fas fa-edit"></i></button>'+
            // '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Anular" >'+
            //     '<i class="fas fa-trash"></i></button>'+
            // '<i class="fas fa-exclamation-triangle" style="position:absolute;"></i>'+
            // '<button type="button" class="seguro btn btn-warning boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Ver Documentos" >'+
            //     '<i class="fas fa-file-upload"></i></button>'+
            // '<i class="fas fa-exclamation-triangle" style="position:absolute;"></i>'+
            // '<button type="button" class="programacion btn btn-info boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Program. Mtto." >'+
            //     '<i class="fas fa-clock"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaEquiCatalogo tbody',tabla);
}
function botones(tbody, tabla){
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        equipo_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_equipo(data.id_equipo);
    });
    $(tbody).on("click","button.seguro", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_seguro(data);
    });
    $(tbody).on("click","button.programacion", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_programacion(data);
    });
}
function guardar_equipo(data){
    // var id = $('[name=id_equipo]').val();
    // var cat = $('[name=id_categoria]').val();
    // var pro = $('[name=propietario]').val();
    // var cod = $('[name=codigo]').val();
    // var des = $('[name=descripcion]').val();
    // var mar = $('[name=marca]').val();
    // var mod = $('[name=modelo]').val();
    // var pla = $('[name=placa]').val();
    // var tar = $('[name=cod_tarj_propiedad]').val();
    // var ser = $('[name=serie]').val();
    // var anio = $('[name=anio_fabricacion]').val();
    // var carac = $('[name=caracteristicas_adic]').val();
    // var tp = $('[name=tp_combustible]').val();

    // var data = 'id_equipo='+id+
    //         '&id_categoria='+cat+
    //         '&propietario='+pro+
    //         '&codigo='+cod+
    //         '&descripcion='+des+
    //         '&marca='+mar+
    //         '&modelo='+mod+
    //         '&placa='+pla+
    //         '&cod_tarj_propiedad='+tar+
    //         '&serie='+ser+
    //         '&anio_fabricacion='+anio+
    //         '&caracteristicas_adic='+carac+
    //         '&usuario='+auth_user.id_usuario+
    //         '&tp_combustible='+tp;
    console.log(data);

    // var token = $('#token').val();
    var id = $('[name=id_equipo]').val();
    var baseUrl = '';
    var msj = '';
    if (id !== ''){
        baseUrl = 'actualizar_equipo';
        msj = 'Equipo actualizado con éxito';
    } else {
        baseUrl = 'guardar_equipo';
        msj = 'Equipo registrado con éxito';
    }
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response.length);
            if (response.length > 0){
                alert(response);
            } else {
                alert(msj);
                $('#modal-equipo_create').modal('hide');
                // $('#listaEquiCatalogo').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_equipo(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular éste Equipo?")
        if (rspta){
            baseUrl = 'anular_equipo/'+ids;
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Equipo anulado con éxito');
                        $('#listaEquiCatalogo').DataTable().ajax.reload();
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
function equipo_create(data){
    $('#modal-equipo_create').modal({
        show: true
    });
    if (data !== undefined){
        $('[name=id_equipo]').val(data.id_equipo);
        $('[name=id_categoria]').val(data.id_categoria);
        $('[name=propietario]').val(data.propietario);
        $('[name=codigo]').val(data.codigo);
        $('[name=descripcion]').val(data.descripcion);
        $('[name=marca]').val(data.marca);
        $('[name=modelo]').val(data.modelo);
        $('[name=placa]').val(data.placa);
        $('[name=kilometraje_inicial]').val(data.kilometraje_inicial);
        $('[name=cod_tarj_propiedad]').val(data.cod_tarj_propiedad);
        $('[name=serie]').val(data.serie);
        $('[name=anio_fabricacion]').val(data.anio_fabricacion);
        $('[name=caracteristicas_adic]').val(data.caracteristicas_adic);
        $('[name=tp_combustible]').val(data.tp_combustible);
    } else {
        $('[name=id_equipo]').val('');
        $('[name=id_categoria]').val('');
        $('[name=propietario]').val('');
        $('[name=codigo]').val('');
        $('[name=descripcion]').val('');
        $('[name=marca]').val('');
        $('[name=modelo]').val('');
        $('[name=placa]').val('');
        $('[name=kilometraje_inicial]').val('');
        $('[name=cod_tarj_propiedad]').val('');
        $('[name=serie]').val('');
        $('[name=anio_fabricacion]').val('');
        $('[name=caracteristicas_adic]').val('');
        $('[name=tp_combustible]').val('');
    }
}
function elabora_descripcion(name){
    if (name == "id_categoria"){
        var des = $('select[name='+name+'] option:selected').text();
    } else {
        var des = $("[name="+name+"]").val();
    }
    var actual = $("[name=descripcion]").val();
    $('[name=descripcion]').val(actual+" "+des.toUpperCase());
}