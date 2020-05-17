$(function(){
    listar_acus();
});
function listar_acus(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaAcu').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'listar_acus',
        'columns': [
            {'data': 'id_cu_partida'},
            {'data': 'cat_descripcion'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            // {'data': 'fecha_registro'},
            {'render':
                function (data, type, row){
                    return (row['rendimiento'] +' '+ row['abreviatura'] + ' / jornada');
                }, className: 'text-right'
            },
            {'data': 'abreviatura'},
            {'render':
                function (data, type, row){
                    return (formatNumber.decimal(row['total'],'',-2));
                }, className: 'text-right'
            },
            {'data': 'presupuestos'},
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
            '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver" >'+
                '<i class="fas fa-list-alt"></i></button>'+
            '<button type="button" class="presupuestos btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver presupuestos enlazados" >'+
                '<i class="fas fa-file-alt"></i></button>'+
            '<button type="button" class="duplicar btn btn-success boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Duplicar A.C.U." >'+
                '<i class="fas fa-copy"></i></button>'}
        ],
        'columnDefs': [ { 'aTargets': [0], 'sClass': 'invisible'} ],
        'order': [[ 2, "asc" ]],
        'initComplete': function () {
            $('#listaAcu_filter label input').focus();
        }
    });
    botones('#listaAcu tbody',tabla);
}
function botones(tbody, tabla){
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        if (data !== undefined){
            if (data.nro_pres > 0){
                alert('No puede editar éste A.C.U porque ya esta relacionado con un Presupuesto Emitido!');
            } else {
                open_acu_partida_create(data);
            }
        }
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_acu(data.id_cu_partida);
    });
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        ver_acu_detalle(data.id_cu_partida,0);
    });
    $(tbody).on("click","button.presupuestos", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);//mostrar_presupuestos_acu/{id}
        if (data !== undefined){
            open_acuPresupuesto(data.id_cu_partida);
        }
    });
    $(tbody).on("click","button.duplicar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        if (data !== undefined){
            duplicar_acu(data);
        }
    });
}

function duplicar_acu(data){
    $('#modal-acu_partida_create').modal({
        show: true
    });
    insumos = [];
    if (data !== undefined){
        console.log(data);
        $('[name=id_cu_partida]').val('');
        $('[name=id_acu]').val(data.id_cu);
        $('[name=cod_acu]').val(data.codigo);
        $('[name=des_acu]').val(data.descripcion);
        $('[name=rendimiento]').val(data.rendimiento);
        $('[name=unid_medida]').val(data.unid_medida);
        $('[name=id_categoria]').val(data.id_categoria);
        $('[name=total_acu]').val(data.total);
        $('[name=observacion]').val(data.observacion);
        unid_abrev();

        $.ajax({
            type: 'GET',
            url: 'listar_acu_detalle/'+data.id_cu_partida,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $i = 1;
                response.forEach(element => {
                    //agregar item a la coleccion
                    let item = {
                        'id_cu_detalle':0,
                        'id_insumo':element.id_insumo,
                        'codigo':element.codigo,
                        'descripcion':element.descripcion,
                        // 'tp_insumo':element.tp_insumo,
                        'cod_tp_insumo':element.cod_tp_insumo,
                        'unidad':element.abreviatura.trim(),
                        'cuadrilla':element.cuadrilla,
                        'cantidad':element.cantidad,
                        'unitario':element.precio_unit,
                        // 'id_precio':element.id_precio,
                        'total':element.precio_total,
                        'nro':$i,
                    }
                    insumos.push(item);
                    $i++;
                });
                listar_insumos();
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_acu(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular éste A.C.U?")
        if (rspta){
            baseUrl = 'anular_acu/'+ids;
            $.ajax({
                type: 'GET',
                // headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Costo Unitario anulado con éxito');
                        $('#listaAcu').DataTable().ajax.reload();
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

//Presupuestos ACU
function open_acuPresupuesto(id){
    console.log('open_acuPresupuesto');
    $('#modal-acuPresupuesto').modal({
        show:true
    });
    listar_presupuestos(id);
}

function listar_presupuestos(id_cu){
    $.ajax({
        type: 'GET',
        url: 'mostrar_presupuestos_acu/'+id_cu,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            response.forEach(element => {
                html+='<div class="panel panel-default">'+
                '<div class="panel-heading">'+element.des_tipo+' : '+element.codigo+'</div>'+
                '<div class="panel-body">'+
                '<div class="row"><div class="col-md-12"><label>Descripción: </label> <h5>'+element.descripcion+'</h5></div></div>'+
                '<div class="row"><div class="col-md-12"><label>Cliente: </label> <h5>'+element.nro_documento+' - '+element.razon_social+'</h5></div></div>'+
                '<div class="row"><div class="col-md-6"><label>Fecha Emisión: </label> <h5>'+formatDate(element.fecha_emision)+'</h5></div>'+
                '<div class="col-md-6"><label>Sub Total: </label> <h5>'+formatNumber.decimal(element.sub_total, element.simbolo, -2)+'</h5></div></div>'+
                '<div class="row"><div class="col-md-12"><label>Estado: </label> <h5>'+element.estado_doc+'</h5></div></div>'+
                '</div></div>';
            });
            $('#contenido').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}