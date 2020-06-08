let od_seleccionadas = [];

$(function(){
    $("#tab-ordenesPendientes section:first form").attr('form', 'formulario');
    listarOrdenesPendientes();

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
            listarOrdenesPendientes();
        } 
        else if (activeForm == "form-despachados"){
            // listarOrdenesEntregadas();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    
});

function listarOrdenesPendientes(){
    var vardataTables = funcDatatables();
    var tabla = $('#ordenesDespacho').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarOrdenesDespacho',
        'columns': [
            {'data': 'id_od'},
            {'data': 'codigo'},
            {'data': 'razon_social'},
            {'data': 'concepto'},
            {'data': 'ubigeo_descripcion'},
            {'data': 'direccion_destino'},
            {'data': 'fecha_despacho'},
            {'data': 'fecha_entrega'},
            {'data': 'nombre_corto'},
            {'data': 'nombre_corto'},
            {'defaultContent': 
            '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
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
    botones('#ordenesDespacho tbody',tabla);
    // Handle iCheck change event for checkboxes in table body
    $(tabla.table().container()).on('ifChanged', '.dt-checkboxes', function(event){
        var cell = tabla.cell($(this).closest('td'));
        cell.checkboxes.select(this.checked);

        var data = tabla.row($(this).parents("tr")).data();
        console.log(this.checked);
        console.log(tabla.row($(this).parents("tr")).data());

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

function botones(tbody, tabla){
    $(tbody).on("click","button.detalle", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log('data.id_od'+data.id_od);
        open_detalle(data);
    });
    // $(tbody).on("click","button.despacho", function(){
    //     var data = tabla.row($(this).parents("tr")).data();
    //     console.log('data.id_od'+data.id_od);
    //     open_despacho_create(data);
    // });
}

function open_detalle(data){
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
    
    console.log(od_seleccionadas);
}

