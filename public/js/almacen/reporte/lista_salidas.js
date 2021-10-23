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




    var vardataTables = funcDatatables();
        $tablaListaSalidas = $('#listaSalidas').DataTable({
        'destroy': true,
        'dom': vardataTables[1],
        'buttons': [
            {
                text: '<i class="fas fa-filter"></i> Filtros : 0',
                attr: {
                    id: 'btnFiltros'
                },
                action: () => {
                    open_filtros();

                },
                className: 'btn-default btn-sm'
            },
            {
                text: '<i class="far fa-file-excel"></i> Descargar',
                attr: {
                    id: 'btnDescargarExcel'
                },
                action: () => {
                    descargarSalidasExcel();

                },
                className: 'btn-default btn-sm'
            }
        ],
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
            { 'data': 'fecha_emision', 'name': 'mov_alm.fecha_emision', 'className': 'text-center', "searchable": false },
            { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
            { 'data': 'guia_venta.fecha_emision', 'name': 'guia_venta.fecha_emision', 'className': 'text-center','defaultContent':''},
            { 'data': 'guia_venta.id_guia', 'name': 'guia_venta.id_guia', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.fecha_emision', 'name': 'documento_venta.fecha_emision', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.tipo_documento.abreviatura', 'name': 'documento_venta.tipo_documento.abreviatura', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.id_doc_ven', 'name': 'documento_venta.id_doc_ven', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'guia_venta.cliente.contribuyente.nro_documento', 'name': 'documento_venta.cliente.contribuyente.nro_documento', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'guia_venta.cliente.contribuyente.razon_social', 'name': 'documento_venta.cliente.contribuyente.razon_social', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.moneda.simbolo', 'name': 'documento_venta.moneda.simbolo', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.id_doc_ven', 'name': 'documento_venta.id_doc_ven', 'className': 'text-center','defaultContent':'' ,"searchable": false },
            { 'data': 'documento_venta.id_doc_ven', 'name': 'documento_venta.id_doc_ven', 'className': 'text-center','defaultContent':'' ,"searchable": false },
            { 'data': 'documento_venta.id_doc_ven', 'name': 'documento_venta.id_doc_ven', 'className': 'text-center','defaultContent':'' ,"searchable": false },
            { 'data': 'documento_venta.id_doc_ven', 'name': 'documento_venta.id_doc_ven', 'className': 'text-center','defaultContent':'' ,"searchable": false },
            { 'data': 'documento_venta.condicion_pago.descripcion', 'name': 'documento_venta.condicion_pago.descripcion', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.credito_dias', 'name': 'documento_venta.credito_dias', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'operacion.descripcion', 'name': 'operacion.descripcion', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.fecha_vcmto', 'name': 'documento_venta.fecha_vcmto', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'usuario.nombre_corto', 'name': 'usuario.nombre_corto', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'documento_venta.tipo_cambio', 'name': 'documento_venta.tipo_cambio', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'almacen.descripcion', 'name': 'almacen.descripcion', 'className': 'text-center','defaultContent':'',"searchable": false },
            { 'data': 'fecha_registro', 'name': 'mov_alm.fecha_registro', 'className': 'text-center',"searchable": false },
        ],
        'columnDefs': [
            {
                'render': function (data, type, row) {
                    return row['id_mov_alm'];
                }, targets: 0
            },
            {
                'render': function (data, type, row) {
                    return row['revisado'];
                }, targets: 1
            },
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
            {
                'render': function (data, type, row) {
                    if(row['guia_venta']!=null){
                        return (row['guia_venta']['serie']+'-'+row['guia_venta']['numero']);
                    }else{
                        return ''
                    }
                }, targets: 6
            },
            {
                'render': function (data, type, row) {
                    if(row['documento_venta']!=null){
                        return (row['documento_venta']['serie']+'-'+row['documento_venta']['numero']);
                    }else{
                        return ''
                    }
                }, targets: 9
            },
            {
                'render': function (data, type, row) {
                if(row['documento_venta']!=null){
                    let idMoneda = row['documento_venta']['moneda']['id_moneda'];
                    t = 0;
                    if (moneda == 4){//Convertir a Soles
                        if (idMoneda == 1){//Soles
                            t = row['documento_venta']['total'];
                        } else {
                            t = row['documento_venta']['total'] * row['documento_venta']['tipo_cambio'];
                        }
                    } else if (moneda == 5){//Convertir a Dolares
                        if (idMoneda == 2){//Dolares
                            t = row['documento_venta']['total'];
                        } else {
                            t = row['documento_venta']['total'] / row['documento_venta']['tipo_cambio'];
                        }
                    } else {
                        t = row['documento_venta']['total'];
                    }

                }
                }, targets: 13
            },
            {
                'render': function (data, type, row) {
                    if(row['documento_venta']!=null){
                        let idMoneda = row['documento_venta']['moneda']['id_moneda'];
                        t = 0;
                        if (moneda == 4){//Convertir a Soles
                            if (idMoneda == 1){//Soles
                                t = row['documento_venta']['total_igv'];
                            } else {
                                t = row['documento_venta']['total_igv'] * row['documento_venta']['tipo_cambio'];
                            }
                        } else if (moneda == 5){//Convertir a Dolares
                            if (idMoneda == 2){//Dolares
                                t = row['documento_venta']['total_igv'];
                            } else {
                                t = row['documento_venta']['total_igv'] / row['documento_venta']['tipo_cambio'];
                            }
                        } 
                    }else{
                        return '';
                    }
                }, targets: 14
            },
            {
                'render': function (data, type, row) {
                    if(row['documento_venta']!=null){
                // let simboloMoneda = row['documento_venta']['moneda']['simbolo'];
                let idMoneda = row['documento_venta']['moneda']['id_moneda'];
                t = 0;
                if (moneda == 4){//Convertir a Soles
                    if (idMoneda == 1){//Soles
                        t = row['documento_venta']['total_a_pagar'];
                    } else {
                        t = row['documento_venta']['total_a_pagar'] * row['documento_venta']['tipo_cambio'];
                    }
                } else if (moneda == 5){//Convertir a Dolares
                    if (idMoneda == 2){//Dolares
                        t = row['documento_venta']['total_a_pagar'];
                    } else {
                        t = row['documento_venta']['total_a_pagar'] / row['documento_venta']['tipo_cambio'];
                    }
                } 
                    }else{
                        return '';
                    }


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