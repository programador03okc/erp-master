let proyectos = [];
let anulados = [];

$(function(){
    mostrarResidentes();
});

function mostrarResidentes(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaResidentes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_residentes',
        'columns': [
            {'data': 'id_residente'},
            {'data': 'nro_documento'},
            {'data': 'nombre_trabajador'},
            {'data': 'colegiatura'},
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
                '<i class="fas fa-trash"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaResidentes tbody',tabla);
}

function botones(tbody, tabla){
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        if (data !== undefined){
            open_residente_create(data);
        }
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        if (data.id_residente !== undefined && data.id_residente !== null){
            anular_residente(data.id_residente);
        }
    });
}

function open_residente_create(data){
    $('#listaResidenteProyectos tbody').html('');
    proyectos = [];
    anulados = [];

    if (data !== ''){
        if (data.estado !== 7){
            $('#modal-residente_create').modal({
                show: true
            });
            $('[name=id_residente]').val(data.id_residente);
            $('[name=id_trabajador]').val(data.id_trabajador);
            $('[name=nro_documento]').val(data.nro_documento);
            $('[name=nombre_trabajador]').val(data.nombre_trabajador);
            $('[name=colegiatura]').val(data.colegiatura);
            listar_proyectos_residente(data.id_residente);
        } else {
            alert('No puede editar un residente anulado!');
        }
    } else {
        $('#modal-residente_create').modal({
            show: true
        });
        $('[name=id_residente]').val('');
        $('[name=id_trabajador]').val('');
        $('[name=nro_documento]').val('');
        $('[name=nombre_trabajador]').val('');
        $('[name=colegiatura]').val('');
    }
}

function listar_proyectos_residente(id){
    console.log(id);
    $.ajax({
        type: 'GET',
        url: 'listar_proyectos_residente/'+id,
        dataType: 'JSON',
        success: function(response){
            proyectos = [];
            console.log(response);
            response.forEach(element => {
                let item = {
                    'id_res_con':element.id_res_con,
                    'id_cargo':element.id_cargo,
                    'id_proyecto':element.id_proyecto,
                    'cargo_descripcion':element.cargo_descripcion,
                    'fecha_inicio':element.fecha_inicio,
                    'fecha_fin':element.fecha_fin,
                    'participacion':element.participacion,
                    'codigo':element.codigo,
                    'descripcion':element.descripcion,
                    'razon_social':element.razon_social,
                    'simbolo':element.simbolo,
                    'importe':element.importe,
                    'nro':null
                }
                proyectos.push(item);
            });
            console.log(proyectos);
            listar_proyectos();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_residente(){
    var id = $('[name=id_residente]').val();
    var trab = $('[name=id_trabajador]').val();
    var col = $('[name=colegiatura]').val();
    var msj = '';
    console.log('id_residente: '+id);
    if (trab == ''){
        msj+='Debe seleccionar un trabajador!';
    }
    if (col == ''){
        msj+='\nDebe ingresar una colegiatura!';
    }

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        var id_res_con = [];
        var id_proyecto = [];
        var id_cargo = [];
        var participacion = [];
        var fec_ini = [];
        var fec_fin = [];

        proyectos.forEach(element => {
            id_res_con.push(element.id_res_con);
            id_proyecto.push(element.id_proyecto);
            id_cargo.push(element.id_cargo);
            participacion.push(element.participacion);
            fec_ini.push(element.fecha_inicio);
            fec_fin.push(element.fecha_fin);
        });

        var data = 'id_residente='+id+
            '&id_trabajador='+trab+
            '&colegiatura='+col+
            '&id_res_con='+id_res_con+
            '&id_proyecto='+id_proyecto+
            '&id_cargo='+id_cargo+
            '&participacion='+participacion+
            '&fecha_inicio='+fec_ini+
            '&fecha_fin='+fec_fin+
            '&anulados='+anulados;
        console.log(data);

        var baseUrl;
        if (id !== ''){
            baseUrl = 'update_residente';
        } else {
            baseUrl = 'guardar_residente';
        }
        console.log(baseUrl);
        
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Residente registrado con éxito');
                    $('#modal-residente_create').modal('hide');
                    mostrarResidentes();
                } else {
                    alert('Ya existe el residente ingresado!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_residente(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular el Residente?");
        if (rspta){
            baseUrl = 'anular_residente/'+ids;
            $.ajax({
                type: 'GET',
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Residente anulado con éxito');
                        mostrarResidentes();
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

function agregar(){
    var proy = $('[name=id_proyecto]').val();
    var car = $('[name=id_cargo]').val();
    var car_des = $('select[name="id_cargo"] option:selected').text();
    var cod = $('[name=codigo]').val();
    var des = $('[name=descripcion]').val();
    var fini = $('[name=fecha_inicio]').val();
    var razsoc = $('[name=razon_social]').val();
    var sim = $('[name=simbolo]').val();
    var imp = $('[name=importe]').val();
    var par = $('[name=participacion]').val();
    // var preseje = $('[name=cod_preseje]').val();
    // var filas = document.querySelectorAll('#listaResidenteProyectos tbody tr');
    // var nro = filas.length + 1;
    var msj = '';
    if (proy == ''){
        msj += '\nDebe seleccionar un proyecto!';
    }
    if (car == '' || car == '0'){
        msj += '\nDebe seleccionar un cargo!';
    }
    if (fini == ''){
        msj += '\nDebe ingresar una fecha inicio!';
    }
    if (par == ''){
        msj += '\nDebe ingresar una participación!';
    }

    if (msj.length > 0){
        alert(msj);
    } else {
        var nro = proyectos.length + 1;
        //agregar item a la coleccion
        let item = {
            'id_res_con':0,
            'id_cargo':car,
            'id_proyecto':proy,
            'cargo_descripcion':car_des,
            'fecha_inicio':fini,
            'fecha_fin':'',
            'codigo':cod,
            'descripcion':des,
            'razon_social':razsoc,
            'simbolo':sim,
            'importe':imp,
            'participacion':par,
            'nro':nro
        }
        proyectos.push(item);
        console.log(proyectos);
        listar_proyectos();
        limpiar_nuevo();
    }
}

function anular(id){
    var elimina = confirm("¿Esta seguro que desea anular éste Proyecto?");
    if (elimina){
        var o = id.split("-");
        console.log(o[0].length);
        console.log('o[1]: '+o[1]);
        if (o[0].length == 0){
            var inc = anulados.includes(o[1]);
            if (!inc){
                anulados.push(o[1]);
            }
            console.log('anulados: ');
            console.log(anulados);
        }
        var index = proyectos.findIndex(function(item, i){
            return (item.id_res_con == o[1] && item.nro == (o[0] == '' ? null : o[0]));
        });
        console.log('proyectos: ');
        console.log(proyectos);
        proyectos.splice(index,1);
        listar_proyectos();
    }
}

function listar_proyectos(){
    $('#listaResidenteProyectos tbody').html('');
    proyectos.sort(function(a, b) {
        if (a.fecha_inicio > b.fecha_inicio) {
            return 1;
        }
        if (a.fecha_inicio < b.fecha_inicio) {
            return -1;
        }
        return 0;
    });
    var html = '';
    proyectos.forEach(element => {
        html += '<tr id="'+(element.nro !== null ? element.nro : '')+'-'+element.id_res_con+'">'+
        '<td>'+element.cargo_descripcion+'</td>'+
        '<td class="right blue info">'+element.fecha_inicio+'</td>'+
        '<td class="right blue info">'+(element.fecha_fin!==null ? element.fecha_fin : '')+'</td>'+
        '<td>'+(element.participacion!==null ? (element.participacion+' %') : '')+'</td>'+
        '<td>'+element.codigo+'</td>'+
        '<td>'+element.descripcion+'</td>'+
        '<td>'+element.razon_social+'</td>'+
        '<td class="right green">'+element.simbolo+'</td>'+
        '<td class="right green">'+element.importe+'</td>'+
        '<td class="right">'+
        '<button class="btn btn-danger boton" onClick="anular('+"'"+(element.nro !== null ? element.nro : '')+'-'+element.id_res_con+"'"+');"><i class="fas fa-trash-alt"></i></button></td></tr>';
    });
    $('#listaResidenteProyectos tbody').html(html);
}

function limpiar_nuevo(){
    $('[name=id_proyecto]').val('');
    $('[name=id_cargo]').val('0');
    $('select[name="id_cargo"] option:selected').text('');
    $('[name=codigo]').val('');
    $('[name=descripcion]').val('');
    $('[name=fecha_inicio]').val('');
    $('[name=razon_social]').val('');
    $('[name=simbolo]').val('');
    $('[name=importe]').val('');
    $('[name=participacion]').val('');
    // $('[name=cod_preseje]').val('');
}
