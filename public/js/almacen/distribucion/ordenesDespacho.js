let od_seleccionadas = [];

$(function(){
    $("#tab-reqPendientes section:first form").attr('form', 'formulario');
    listarRequerimientosPendientes();

    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').attr('hidden', true);
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        
        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);

        clearDataTable();
        if (activeForm == "form-pendientes"){
            listarRequerimientosPendientes();
        } 
        else if (activeForm == "form-despachos"){
            listarOrdenesPendientes();
        }
        else if (activeForm == "form-despachados"){
            listarGruposDespachados();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    
});

function listarRequerimientosPendientes(){
    var vardataTables = funcDatatables();
    var tablaReq = $('#requerimientosPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarRequerimientosPendientes',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            {'data': 'observacion'},
            {'data': 'grupo'},
            {'data': 'nombre_corto'},
            {'defaultContent': 
            '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
            'data-placement="bottom" title="Ver Detalle" >'+
            '<i class="fas fa-list-ul"></i></button>'+
            '<button type="button" class="despacho btn btn-success boton" data-toggle="tooltip" '+
            'data-placement="bottom" title="Generar Orden de Despacho" >'+
            '<i class="fas fa-sign-in-alt"></i></button>'}
            // {'data': 'id_sede'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    
    $('#requerimientosPendientes tbody').on("click","button.detalle", function(){
        var data = tablaReq.row($(this).parents("tr")).data();
        console.log('data.id_requerimiento'+data.id_requerimiento);
        open_detalle_requerimiento(data);
    });
    $('#requerimientosPendientes tbody').on("click","button.despacho", function(){
        var data = tablaReq.row($(this).parents("tr")).data();
        console.log('data.id_requerimiento'+data.id_requerimiento);
        open_despacho_create(data);
    });
}

function open_detalle_requerimiento(data){
    $('#modal-requerimientoDetalle').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo+' - '+data.concepto);
    var idTabla = 'detalleRequerimiento';
    listar_detalle_requerimiento(data.id_requerimiento, idTabla);
}

function listarOrdenesPendientes(){
    var vardataTables = funcDatatables();
    var tablaOD = $('#ordenesDespacho').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarOrdenesDespacho',
        'columns': [
            {'data': 'id_od'},
            {'data': 'codigo'},
            {'data': 'razon_social'},
            {'data': 'codigo_req'},
            {'data': 'concepto'},
            {'data': 'ubigeo_descripcion'},
            {'data': 'direccion_destino'},
            {'data': 'fecha_despacho'},
            {'data': 'fecha_entrega'},
            {'data': 'nombre_corto'},
            {'render': 
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'defaultContent': 
            '<button type="button" class="od_detalle btn btn-primary boton" data-toggle="tooltip" '+
            'data-placement="bottom" title="Ver Detalle" >'+
            '<i class="fas fa-list-ul"></i></button>'}
            // {'data': 'id_sede'}
        ],
        'drawCallback': function(){
            $('input[type="checkbox"]').iCheck({
               checkboxClass: 'icheckbox_flat-blue'
            });
         },
         'columnDefs': [
            {
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-center',
                // 'checkboxes': {
                //     'selectRow': true
                //  }
                'checkboxes': {
                    'selectRow': true,
                    'selectCallback': function(nodes, selected){
                        $('input[type="checkbox"]', nodes).iCheck('update');
                    },
                    'selectAllCallback': function(nodes, selected, indeterminate){
                        $('input[type="checkbox"]', nodes).iCheck('update');
                    }
                }
            }
         ],
        'select': 'multi',
        'order': [[1, 'asc']]
    });
    
    $('#ordenesDespacho tbody').on("click","button.od_detalle", function(){
        var data = tablaOD.row($(this).parents("tr")).data();
        console.log('data.id_od'+data.id_od);
        open_detalle_despacho(data);
    });
    // Handle iCheck change event for checkboxes in table body
    $(tablaOD.table().container()).on('ifChanged', '.dt-checkboxes', function(event){
        var cell = tablaOD.cell($(this).closest('td'));
        cell.checkboxes.select(this.checked);

        var data = tablaOD.row($(this).parents("tr")).data();
        console.log(this.checked);
        console.log(tablaOD.row($(this).parents("tr")).data());

        if (data !== null && data !== undefined){
            if (this.checked){
                od_seleccionadas.push(data);
            }
            else {
                var index = od_seleccionadas.findIndex(function(item, i){
                    return item.id_od == data.id_od;
                });
                od_seleccionadas.splice(index,1);
            }
        }
    });
}

function open_detalle_despacho(data){
    $('#modal-despachoDetalle').modal({
        show: true
    });
    $('#cabecera').text(data.codigo+' - '+data.concepto);
    verDetalleDespacho(data.id_od);
}

function verDetalleDespacho(id_od){
    $.ajax({
        type: 'GET',
        url: '/verDetalleDespacho/'+id_od,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            // detalle_requerimiento = response;
            response.forEach(element => {
                html+='<tr id="'+element.id_od_detalle+'">'+
                '<td>'+i+'</td>'+
                '<td>'+(element.codigo !== null ? element.codigo : '')+'</td>'+
                '<td>'+(element.descripcion !== null ? element.descripcion : '')+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
                '<td>'+element.posicion+'</td>'+
                '<td>'+element.descripcion_producto+'</td>'+
                '</tr>';
                i++;
            });
            $('#detalleDespacho tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function crear_grupo_orden_despacho() {
    $('#modal-grupo_despacho_create').modal({
        show: true
    });
    var html = '';
    var i = 1;
    od_seleccionadas.forEach(element => {
        html+='<tr id="'+element.id_od+'">'+
        '<td>'+i+'</td>'+
        '<td>'+element.codigo+'</td>'+
        '<td>'+element.razon_social+'</td>'+
        '<td>'+element.codigo_req+'</td>'+
        '<td>'+element.concepto+'</td>'+
        '<td>'+element.ubigeo_descripcion+'</td>'+
        '<td>'+element.direccion_destino+'</td>'+
        '<td>'+element.fecha_despacho+'</td>'+
        '<td>'+element.fecha_entrega+'</td>'+
        '</tr>';
        i++;
    });
    $('#detalleODs tbody').html(html);
    $("#btnGrupoDespacho").removeAttr("disabled");
    console.log(od_seleccionadas);
}

function guardar_grupo_despacho(){
    var resp = $('[name=responsable_grupo]').val();
    var fdes = $('[name=fecha_despacho_grupo]').val();

    var data =  'responsable='+resp+
                '&fecha_despacho='+fdes+
                '&ordenes_despacho='+JSON.stringify(od_seleccionadas);

    $("#btnGrupoDespacho").attr('disabled','true');
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_grupo_despacho',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('El Despacho se generó correctamente.'+response);
                $('#modal-grupo_despacho_create').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarGruposDespachados(){
    var vardataTables = funcDatatables();
    var tablaGOD = $('#gruposDespachados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarGruposDespachados',
        'columns': [
            {'data': 'id_od_grupo'},
            {'data': 'codigo'},
            {'data': 'fecha_despacho'},
            {'data': 'nombre_corto'},
            {'data': 'estado_doc'},
            {'data': 'estado_doc'},
            {'defaultContent': 
            '<button type="button" class="god_detalle btn btn-primary boton" data-toggle="tooltip" '+
            'data-placement="bottom" title="Ver Detalle" >'+
            '<i class="fas fa-list-ul"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    $('#gruposDespachados tbody').on("click","button.god_detalle", function(){
        var data = tablaGOD.row($(this).parents("tr")).data();
        console.log('data.id_od'+data.id_od_grupo);
        open_grupo_detalle(data);
    });
}

function open_grupo_detalle(data){
    $('#modal-grupoDespachoDetalle').modal({
        show: true
    });
    $('#cabeceraGrupo').text(data.codigo);
    $.ajax({
        type: 'GET',
        url: '/verDetalleGrupoDespacho/'+data.id_od_grupo,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr id="'+element.id_od_grupo_detalle+'">'+
                '<td>'+i+'</td>'+
                '<td>'+element.codigo+'</td>'+
                '<td>'+element.razon_social+'</td>'+
                '<td>'+element.codigo_req+'</td>'+
                '<td>'+element.concepto+'</td>'+
                '<td>'+element.ubigeo_descripcion+'</td>'+
                '<td>'+element.direccion_destino+'</td>'+
                '<td>'+element.fecha_despacho+'</td>'+
                '<td>'+element.fecha_entrega+'</td>'+
                '<td>'+element.nombre_corto+'</td>'+
                '</tr>';
                i++;
            });
            $('#detalleGrupoDespacho tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}