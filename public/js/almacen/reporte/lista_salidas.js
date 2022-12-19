var empresa;
var sede;
var almacenes;
var condiciones;
var fini;
var ffin;
var cli;
var id_usuario;
var moneda;

var $tablaListaSalidas;
function SetDefaultFiltroEmpresa(){
    empresa=0
}
function SetDefaultFiltroSede(){
    sede=0;
}
function SetDefaultFiltroAlmacenes(){
    $('[name=almacen] option').each(function(){
        $(this).prop("selected",true);
    });

    almacenes = $('[name=almacen]').val();

}
function SetDefaultFiltroCondiciones(){
    $('[name=condicion] option').each(function(){
        $(this).prop("selected",true);
    });
    condiciones = $('[name=condicion]').val();

}
function SetDefaultFiltroRangoFechaEmision(){
    $('[name=fecha_inicio]').val(((new Date()).getFullYear())+'-01-01');
    $('[name=fecha_fin]').val(((new Date()).getFullYear())+'-12-31');
    fini = $('[name=fecha_inicio]').val();
    ffin = $('[name=fecha_fin]').val();

}

function SetDefaultFiltroCliente(){
    cli = 0;

}
function SetDefaultFiltroMoneda(){
    moneda = 0;

}
function descargarSalidasExcel(){
    window.open('listar-salidas-excel/'+empresa+'/'+sede+'/'+almacenes+'/'+condiciones+'/'+fini+'/'+ffin+'/'+cli+'/'+id_usuario+'/'+moneda );

}

function actualizarLista(option=null){
    $('#modal-filtros').modal('hide');

    if(option =='DEFAULT'){
        SetDefaultFiltroEmpresa();
        SetDefaultFiltroSede();
        SetDefaultFiltroAlmacenes();
        SetDefaultFiltroCondiciones();
        SetDefaultFiltroRangoFechaEmision();
        SetDefaultFiltroCliente();
        SetDefaultFiltroMoneda();

    }else{
        const modalFiltro = document.querySelector("div[id='modal-filtros']");
        if(modalFiltro.querySelector("input[name='chkEmpresa']").checked){
            empresa = $('[name=empresa]').val();
        }else{
            SetDefaultFiltroEmpresa();

        }
        if(modalFiltro.querySelector("input[name='chkSede']").checked){
            sede = $('[name=sede]').val();
        }else{
            SetDefaultFiltroSede();

        }
        if(modalFiltro.querySelector("input[name='chkAlmacen']").checked){
            almacenes = $('[name=almacen]').val();
        }else{
            SetDefaultFiltroAlmacenes();

        }
        if(modalFiltro.querySelector("input[name='chkCondicion']").checked){
            condiciones = $('[name=condicion]').val();
        }else{
            SetDefaultFiltroCondiciones();
        }
        if(modalFiltro.querySelector("input[name='chkFechaRegistro']").checked){
            fini = $('[name=fecha_inicio]').val();
            ffin = $('[name=fecha_fin]').val();
        }else{
            SetDefaultFiltroRangoFechaEmision();
        }
        if(modalFiltro.querySelector("input[name='chkCliente']").checked){
            let id_cliente = $('[name=id_cliente]').val();
            cli = (id_cliente !== '' ? id_cliente : 0);

        }else{
            SetDefaultFiltroCliente();
        }
        if(modalFiltro.querySelector("input[name='chkMoneda']").checked){
            moneda = $('[name=moneda]').val();
        }else{
            SetDefaultFiltroMoneda();
        }
    }



    const button_filtro = (array_accesos.find(element => element === 164)?{
            text: '<i class="fas fa-filter"></i> Filtros : 0',
            attr: {
                id: 'btnFiltros'
            },
            action: () => {
                open_filtros();

            },
            className: 'btn-default btn-sm'
        }:[]),
        button_descargar_excel = (array_accesos.find(element => element === 165)?{
            text: '<i class="far fa-file-excel"></i> Descargar',
            attr: {
                id: 'btnDescargarExcel'
            },
            action: () => {
                descargarSalidasExcel();

            },
            className: 'btn-default btn-sm'
        }:[]);
    var vardataTables = funcDatatables();
    var button_filtros = (array_accesos.find(element => element === 164)?{
        text: '<i class="fas fa-filter"></i> Filtros : 0',
        attr: {
            id: 'btnFiltros'
        },
        action: () => {
            open_filtros();

        },
        className: 'btn-default btn-sm'
    }:[]);
    var button_descargar_Excel = (array_accesos.find(element => element === 165)?{
        text: '<i class="far fa-file-excel"></i> Descargar',
        attr: {
            id: 'btnDescargarExcel'
        },
        action: () => {
            descargarSalidasExcel();

        },
        className: 'btn-default btn-sm'
    }:[]);

        $tablaListaSalidas = $('#listaSalidas').DataTable({
        'destroy': true,
        'dom': vardataTables[1],
<<<<<<< HEAD
        'buttons': [button_filtros,button_descargar_Excel],
=======
        'buttons': [button_filtro,button_descargar_excel],
>>>>>>> develop
        'language' : vardataTables[0],
        "scrollX": true,
        'serverSide': true,
        'ajax': {
            url:'listar-salidas',
            type: 'POST',
            data:{'idEmpresa':empresa,'idSede':sede,'idAlmacenList':almacenes,'idCondicionList':condiciones,'fechaInicio':fini,'fechaFin':ffin,'idCliente':cli,'idUsuario':id_usuario,'idMoneda':moneda}
        },
        'columns': [
            { 'data': 'id_mov_alm', 'name': 'mov_alm.id_mov_alm', 'className': 'text-center','visible':false, "searchable": false },
            { 'data': 'revisado', 'name': 'mov_alm.revisado', 'className': 'text-center', 'visible':false,"searchable": false },
            { 'data': 'revisado', 'name': 'mov_alm.revisado', 'className': 'text-center',"searchable": false },
            { 'data': 'fecha_emision', 'name': 'mov_alm.fecha_emision', 'className': 'text-center' },
            { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
            { 'data': 'fecha_guia', 'name': 'guia_ven.fecha_emision', 'className': 'text-center','defaultContent':''},
            { 'data': 'guia', 'name': 'guia', 'className': 'text-center','defaultContent':'' },
            { 'data': 'fecha_doc', 'name': 'doc_ven.fecha_emision', 'className': 'text-center','defaultContent':''},
            { 'data': 'abreviatura', 'name': 'abreviatura', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'doc', 'name': 'doc', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'simbolo', 'name': 'sis_moneda.simbolo', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'total', 'name': 'doc_ven.total', 'className': 'text-center','defaultContent':'' ,"searchable": false },
            { 'data': 'total_igv', 'name': 'doc_ven.total_igv', 'className': 'text-center','defaultContent':'' ,"searchable": false },
            { 'data': 'total_a_pagar', 'name': 'doc_ven.total_a_pagar', 'className': 'text-center','defaultContent':'' ,"searchable": false },
            { 'data': 'id_mov_alm', 'name': 'mov_alm.id_mov_alm', 'className': 'text-center',"searchable": false },
            { 'data': 'des_condicion', 'name': 'log_cdn_pago.descripcion', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'credito_dias', 'name': 'doc_ven.credito_dias', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'des_operacion', 'name': 'tp_ope.descripcion', 'className': 'text-center','defaultContent':'' },
            { 'data': 'fecha_vcmto', 'name': 'doc_ven.fecha_vcmto', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'nombre_trabajador', 'name': 'sis_usua.nombre_corto', 'className': 'text-center','defaultContent':'' },
            { 'data': 'tipo_cambio', 'name': 'doc_ven.tipo_cambio', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'des_almacen', 'name': 'alm_almacen.descripcion', 'className': 'text-center','defaultContent':'' },
            { 'data': 'fecha_registro', 'name': 'mov_alm.fecha_registro', 'className': 'text-center','defaultContent':'' }
        ],
        'columnDefs': [
            {'render':
                function(data, type, row){
                    var html = '<select class="form-control '+
                        ((row['revisado'] == 0) ? 'btn-danger' :
                        ((row['revisado'] == 1) ? 'btn-success' : 'btn-warning'))+
                        ' " style="font-size:11px;width:85px;padding:3px 4px;" id="revisado">'+
                            '<option value="0" '+(row['revisado'] == 0 ? 'selected' : '')+'>No Revisado</option>'+
                            '<option value="1" '+(row['revisado'] == 1 ? 'selected' : '')+'>Revisado</option>'+
                            '<option value="2" '+(row['revisado'] == 2 ? 'selected' : '')+'>Observado</option>'+
                        '</select>';
                    return (html);
                }, targets: 2
            },
            // {
            //     'render': function (data, type, row) {
            //         if(row['guia_venta']!=null){
            //             return (row['guia_venta']['serie']+'-'+row['guia_venta']['numero']);
            //         }else{
            //             return ''
            //         }
            //     }, targets: 6
            // },
            // {
            //     'render': function (data, type, row) {
            //         if(row['documento_venta']!=null){
            //             return (row['documento_venta']['serie']+'-'+row['documento_venta']['numero']);
            //         }else{
            //             return ''
            //         }
            //     }, targets: 9
            // },
                {'render':
                function(data, type, row){
                    if (moneda == 4){
                        return 'S/';
                    } else if (moneda == 5){
                        return 'US$';
                    } else {
                        return row['simbolo'];
                    }
                }, targets: 12
            },
            {
                'render': function (data, type, row) {
                    t = 0;
                    if (moneda == 4){//Convertir a Soles
                        if (row['moneda'] == 1){//Soles
                            t = row['total'];
                        } else {
                            t = row['total'] * row['tipo_cambio'];
                        }
                    } else if (moneda == 5){//Convertir a Dolares
                        if (row['moneda'] == 2){//Dolares
                            t = row['total'];
                        } else {
                            t = row['total'] / row['tipo_cambio'];
                        }
                    } else {
                        t = row['total'];
                    }
                    return formatDecimal(t);
                }, targets: 13
            },
            {
                'render': function (data, type, row) {
                    t = 0;
                    if (moneda == 4){//Convertir a Soles
                        if (row['moneda'] == 1){//Soles
                            t = row['total_igv'];
                        } else {
                            t = row['total_igv'] * row['tipo_cambio'];
                        }
                    } else if (moneda == 5){//Convertir a Dolares
                        if (row['moneda'] == 2){//Dolares
                            t = row['total_igv'];
                        } else {
                            t = row['total_igv'] / row['tipo_cambio'];
                        }
                    }
                    return formatDecimal(t);

                }, targets: 14
            },
            {
                'render': function (data, type, row) {
                    t = 0;
                    if (moneda == 4){//Convertir a Soles
                        if (row['moneda'] == 1){//Soles
                            t = row['total_a_pagar'];
                        } else {
                            t = row['total_a_pagar'] * row['tipo_cambio'];
                        }
                    } else if (moneda == 5){//Convertir a Dolares
                        if (row['moneda'] == 2){//Dolares
                            t = row['total_a_pagar'];
                        } else {
                            t = row['total_a_pagar'] / row['tipo_cambio'];
                        }
                    }
                    return formatDecimal(t);


                }, targets: 15
            },
            {
                'render': function (data, type, row) {
                    return 0
                }, targets: 16
            },

        ],
        'initComplete': function () {
            updateContadorFiltro();

            const $filter = $('#listaSalidas_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tablaListaSalidas.search($input.val()).draw();
            })
        },
        "order": [[2, "asc"],[5, "asc"]]
    });
    botones('#listaSalidas tbody',$tablaListaSalidas);
    vista_extendida();
}
function search(){
    console.log('search');
    var nr = $('[name=no_revisado]').prop('checked');
    var r = $('[name=revisado]').prop('checked');
    var o = $('[name=observado]').prop('checked');
    console.log('nr'+nr+' r'+r+' o'+o);
    var valor = "";
    if (nr == true){
        valor = "0";
    }
    console.log(valor);
    if (r == true){
        if (valor == ""){
            valor = "1";
        } else {
            valor += "|1";
        }
    }
    console.log(valor);
    if (o == true){
        if (valor == ""){
            valor = "2";
        } else {
            valor += "|2";
            console.log(valor);
        }
    }
    // console.log(valor);
    var tabla = $('#listaSalidas').DataTable();
    tabla.column(1).search(valor,true,false).draw();
}
function botones(tbody, tabla){
    console.log("change");
    $(tbody).on("change","select", function(){
        var data = tabla.row($(this).parents("tr")).data();
        var revisado = $(this).val();
        if (revisado == 0){
            $(this).addClass('btn-danger');
            $(this).removeClass('btn-success');
            $(this).removeClass('btn-warning');
        } else if (revisado == 1){
            $(this).addClass('btn-success');
            $(this).removeClass('btn-danger');
            $(this).removeClass('btn-warning');
        } else if (revisado == 2){
            $(this).addClass('btn-warning');
            $(this).removeClass('btn-danger');
            $(this).removeClass('btn-success');
        }

        var obs = prompt("Ingrese una nota:");
        console.log('obs:'+obs);

        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'update_revisado/'+data.id_mov_alm+'/'+revisado+'/'+obs,
            dataType: 'JSON',
            success: function(response){
                if (response > 0){
                    alert('Nota registrada con éxito');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

        console.log(data);
        console.log(revisado);
    });
}
function limpiar_cliente(){
    $('[name=id_cliente]').val('');
    $('[name=id_contrib]').val('');
    $('[name=razon_social]').val('');
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse");
}
