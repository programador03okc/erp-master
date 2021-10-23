var empresa;
var sede;
var almacenes;
var condiciones;
var fini;
var ffin;
var prov;
var id_usuario;
var moneda;
var tra;

var  $tablalistaIngresos;

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

function SetDefaultFiltroProveedor(){
    prov = 0;

}
function SetDefaultFiltroMoneda(){
    moneda = 0;

}
function SetDefaultFiltroTransportista(){
    tra = 0;

}
function descargarIngresosExcel(){
    window.open('listar-ingresos-excel/'+empresa+'/'+sede+'/'+almacenes+'/'+condiciones+'/'+fini+'/'+ffin+'/'+prov+'/'+id_usuario+'/'+moneda+'/'+tra );

}
function actualizarLista(option=null){
    $('#modal-filtros').modal('hide');
    if(option =='DEFAULT'){
        SetDefaultFiltroEmpresa();
        SetDefaultFiltroSede();
        SetDefaultFiltroAlmacenes();
        SetDefaultFiltroCondiciones();
        SetDefaultFiltroRangoFechaEmision();
        SetDefaultFiltroProveedor();
        SetDefaultFiltroMoneda();
        SetDefaultFiltroTransportista();

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
        if(modalFiltro.querySelector("input[name='chkProveedor']").checked){
            let id_proveedor = $('[name=id_proveedor]').val();
            prov = (id_proveedor !== '' ? id_proveedor : 0);

        }else{
            SetDefaultFiltroProveedor();
        }
        if(modalFiltro.querySelector("input[name='chkMoneda']").checked){
            moneda = $('[name=moneda]').val();
        }else{
            SetDefaultFiltroMoneda();
        }
    }

        id_usuario = $('[name=responsable]').val();
        let id_proveedor_tra = $('[name=id_proveedor_tra]').val();
        tra = (id_proveedor_tra !== '' ? id_proveedor_tra : 0);

     

    
    var vardataTables = funcDatatables();
    $tablalistaIngresos = $('#listaIngresos').DataTable({
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
                    descargarIngresosExcel();

                },
                className: 'btn-default btn-sm'
            }
        ],
        'language' : vardataTables[0],
        // "scrollX": true,
        // 'pageLength': 50,
        'serverSide': true,
        'ajax': {
            url:'listar-ingresos',
            type: 'POST',
            data:{'idEmpresa':empresa,'idSede':sede,'idAlmacenList':almacenes,'idCondicionList':condiciones,'fechaInicio':fini,'fechaFin':ffin,'idProveedor':prov,'id_usuario':id_usuario,'idMoneda':moneda}
        },
        'columns': [
            { 'data': 'id_mov_alm', 'name': 'mov_alm.id_mov_alm', 'className': 'text-center','visible':false, "searchable": false },
            { 'data': 'revisado', 'name': 'revisado', 'className': 'text-center', "searchable": false },
            { 'data': 'fecha_emision', 'name': 'mov_alm.fecha_emision', 'className': 'text-center', "searchable": false },
            { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
            { 'data': 'guia_compra.fecha_emision', 'name': 'guia_compra.fecha_emision', 'className': 'text-center'},
            { 'data': 'guia_compra.id_guia', 'name': 'guia_compra.id_guia', 'className': 'text-center',"searchable": false },
            { 'data': 'comprobantes', 'name': 'comprobantes', 'className': 'text-center', "searchable": false },
            { 'data': 'guia_compra.proveedor.contribuyente.nro_documento', 'name': 'guia_compra.proveedor.contribuyente.nro_documento', 'className': 'text-center' ,"searchable": false},
            { 'data': 'guia_compra.proveedor.contribuyente.razon_social', 'name': 'guia_compra.proveedor.contribuyente.razon_social', 'className': 'text-center' , "searchable": false},
            { 'data': 'ordenes_compra', 'name': 'ordenes_compra', 'className': 'text-center',"searchable": false },
            { 'data': 'id_mov_alm', 'name': 'id_mov_alm', 'className': 'text-center', "searchable": false }, // moneda
            { 'data': 'id_mov_alm', 'name': 'id_mov_alm', 'className': 'text-center', "searchable": false }, // total
            { 'data': 'id_mov_alm', 'name': 'id_mov_alm', 'className': 'text-center', "searchable": false }, // total_igv
            { 'data': 'id_mov_alm', 'name': 'id_mov_alm', 'className': 'text-center', "searchable": false }, // total_a_pagar
            { 'data': 'id_mov_alm', 'name': 'id_mov_alm', 'className': 'text-center', "searchable": false }, // des_condicion
            { 'data': 'operacion.descripcion', 'name': 'operacion.descripcion', 'className': 'text-center' ,"searchable": false}, // des_condicion
            { 'data': 'usuario.nombre_corto', 'name': 'usuario.nombre_corto', 'className': 'text-center',"searchable": false },
            {'data': 'movimiento_detalle[0].guia_compra_detalle[0].documento_compra_detalle[0].documento_compra.tipo_cambio','name': 'movimiento_detalle[0].guia_compra_detalle[0].documento_compra_detalle[0].documento_compra.tipo_cambio', 'className': 'text-center', "searchable": false },
            {'data': 'almacen.descripcion','name': 'almacen.descripcion', 'className': 'text-center' , "searchable": false},
            { 'data': 'fecha_registro', 'name': 'fecha_registro', 'className': 'text-center', "searchable": false }
        ],
        'columnDefs': [
            {
                'render': function (data, type, row) {
                    return row['id_mov_alm'];
                }, targets: 0
            },
            {
                'render': function (data, type, row) {
                    var html = '<select class="form-control '+
                    ((row['revisado'] == 0) ? 'btn-danger' : 
                    ((row['revisado'] == 1) ? 'btn-success' : 'btn-warning'))+
                    ' " style="font-size:11px;width:85px;padding:3px 4px;" id="revisado">'+
                        '<option value="0" '+(row['revisado'] == 0 ? 'selected' : '')+'>No Revisado</option>'+
                        '<option value="1" '+(row['revisado'] == 1 ? 'selected' : '')+'>Revisado</option>'+
                        '<option value="2" '+(row['revisado'] == 2 ? 'selected' : '')+'>Observado</option>'+
                    '</select>';
                return (html);
                }, targets: 1
            },
            {
                'render': function (data, type, row) {
                return (row['guia_compra']['serie']+'-'+row['guia_compra']['numero']);
                }, targets: 5
            },
            {
                'render': function (data, type, row) {
                    let moneda=''
                    row.movimiento_detalle.forEach(md => {
                        if(md.guia_compra_detalle.length >0){
                            (md.guia_compra_detalle).forEach(element => {
                                moneda=element.documento_compra_detalle[0].documento_compra.moneda.simbolo;
                        });
                    }                        
                    });
                return moneda;
                }, targets: 10
            },
            {
                'render': function (data, type, row) {
                    let subTotal=0
                    row.movimiento_detalle.forEach(md => {
                        if(md.guia_compra_detalle.length >0){
                            (md.guia_compra_detalle).forEach(element => {
                            subTotal=$.number(element.documento_compra_detalle[0].documento_compra.sub_total,2);
                        });
                    }                        
                    });
                return subTotal;
                }, targets: 11
            },
            {
                'render': function (data, type, row) {
                    let totalIGV=0
                    row.movimiento_detalle.forEach(md => {
                        if(md.guia_compra_detalle.length >0){
                            (md.guia_compra_detalle).forEach(element => {
                            totalIGV=$.number(element.documento_compra_detalle[0].documento_compra.total_igv,2);

                        });
                    }
                        
                    });
                return totalIGV;
                }, targets: 12
            },
            {
                'render': function (data, type, row) {
                    let totalAPagar=0
                    row.movimiento_detalle.forEach(md => {
                        if(md.guia_compra_detalle.length >0){
                            (md.guia_compra_detalle).forEach(element => {
                                totalAPagar=$.number(element.documento_compra_detalle[0].documento_compra.total_a_pagar,2);
                        });
                    }
                        
                    });
                return totalAPagar;
                }, targets: 13
            },
            {
                'render': function (data, type, row) {
                    let condicionPago=0
                    row.movimiento_detalle.forEach(md => {
                        if(md.guia_compra_detalle.length >0){
                            (md.guia_compra_detalle).forEach(element => {
                                condicionPago=element.documento_compra_detalle[0].documento_compra.condicion_pago.descripcion;
                        });
                    }                        
                    });
                return condicionPago;
                }, targets: 14
            },
            {
                'render': function (data, type, row) {
                    let tipoCambio=0
                    row.movimiento_detalle.forEach(md => {
                        if(md.guia_compra_detalle.length >0){
                            (md.guia_compra_detalle).forEach(element => {
                                tipoCambio=$.number(element.documento_compra_detalle[0].documento_compra.tipo_cambio,2);
                        });
                    }                        
                    });
                return tipoCambio;
                }, targets: 17
            },
 
        ],
        'initComplete': function () {
            updateContadorFiltro();

            const $filter = $('#listaIngresos_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tablalistaIngresos.search($input.val()).draw();
            })
        },
        "order": [[2, "asc"],[5, "asc"]]
    });
    botones('#listaIngresos tbody',$tablalistaIngresos);
    vista_extendida();
    // $('[name=no_revisado]').change(function(){
    //     if($(this).prop('checked') == true) {
    //         tabla.column(1).search( 0 ).draw();
    //         // var data = tabla.rows().data(); 
    //         // data.each(function (value, index) { 
    //         //     console.log('Data in index: ' + index);
    //         //     console.log(value);
    //         // }); 
    //         // tabla.column(1).data().filter( function ( value, index ) {
    //         //     console.log('value'+value+' index'+index);
    //         //     console.log(value !== 1);
    //         //     return (value != 1 ? true : false);
    //         // } );
    //     }
    // });
    // $('[name=revisado]').change(function(){
    //     var valor = "";
    //     if($(this).prop('checked') == true) {
    //         valor = "1";
    //     }
    // });
    // $('[name=observado]').change(function(){
    //     if($(this).prop('checked') == true) {
    //         tabla.column(1).search( 2 ).draw();
    //     }
    // });
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
    console.log(valor);
    var tabla = $('#listaIngresos').DataTable();
    tabla.column(1).search(valor,true,false).draw();
}
function botones(tbody, tabla){
    // console.log("change");
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
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}