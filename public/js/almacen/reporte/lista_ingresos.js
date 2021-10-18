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
    var tabla = $('#listaIngresos').DataTable({
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
        'pageLength': 50,
        'ajax': {
            url:'listar_ingresos/'+empresa+'/'+sede+'/'+almacenes+'/'+/*documentos+'/'+*/condiciones+'/'+fini+'/'+ffin+'/'+prov+'/'+id_usuario+'/'+moneda+'/'+tra,
            dataSrc:''
            // type: 'POST'
        },
        'columns': [
            {'data': 'id_mov_alm'},
            {'data': 'revisado'},
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
                }
            },
            {'data': 'fecha_emision'},
            {'data': 'codigo'},
            {'data': 'fecha_guia'},
            {'data': 'guia'},
            {'data': 'fecha_doc'},
            // {'data': 'abreviatura'},
            {'data': 'documentos'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'ordenes'},
            {'render': 
                function(data, type, row){
                    // if (moneda == 4){
                    //     return 'S/';
                    // } else if (moneda == 5){
                    //     return 'US$';
                    // } else {
                        return row['simbolo'];
                    // }
                }
            },
            {'render': 
                function(data, type, row){
                    t = 0;
                    // if (moneda == 4){//Convertir a Soles
                    //     if (row['moneda'] == 1){//Soles
                    //         t = row['total'];
                    //     } else {
                    //         t = row['total'] * row['tipo_cambio'];
                    //     }
                    // } else if (moneda == 5){//Convertir a Dolares
                    //     if (row['moneda'] == 2){//Dolares
                    //         t = row['total'];
                    //     } else {
                    //         t = row['total'] / row['tipo_cambio'];
                    //     }
                    // } else {
                        t = row['total'];
                    // }
                    return formatDecimal(t);
                }
            },
            {'render': 
                function(data, type, row){
                    // t = 0;
                    // if (moneda == 4){//Convertir a Soles
                    //     if (row['moneda'] == 1){//Soles
                    //         t = row['total_igv'];
                    //     } else {
                    //         t = row['total_igv'] * row['tipo_cambio'];
                    //     }
                    // } else if (moneda == 5){//Convertir a Dolares
                    //     if (row['moneda'] == 2){//Dolares
                    //         t = row['total_igv'];
                    //     } else {
                    //         t = row['total_igv'] / row['tipo_cambio'];
                    //     }
                    // }
                    return formatDecimal(row['total_igv']);
                }
            },
            {'render': 
                function(data, type, row){
                    // t = 0;
                    // if (moneda == 4){//Convertir a Soles
                    //     if (row['moneda'] == 1){//Soles
                    //         t = row['total_a_pagar'];
                    //     } else {
                    //         t = row['total_a_pagar'] * row['tipo_cambio'];
                    //     }
                    // } else if (moneda == 5){//Convertir a Dolares
                    //     if (row['moneda'] == 2){//Dolares
                    //         t = row['total_a_pagar'];
                    //     } else {
                    //         t = row['total_a_pagar'] / row['tipo_cambio'];
                    //     }
                    // }
                    return formatDecimal(row['total_a_pagar']);
                }
            },
            // {'render': 
            //     function(data, type, row){
            //         return 0;
            //     }
            // },
            {'data': 'des_condicion'},
            // {'data': 'credito_dias'},
            {'data': 'des_operacion'},
            // {'data': 'fecha_vcmto'},
            {'data': 'nombre_trabajador'},
            // {'data': 'tipo_cambio'},
            {'data': 'des_almacen'},
            {'data': 'fecha_registro'},
        ],
        'columnDefs': [
            {   'aTargets': [0,1], 
                'sClass': 'invisible'
            },
            // {   'render': 
            //         function (data, type, row) {
            //             return row.comprobantes;
            //         }, targets: 9
            // },
        ],
        'initComplete': function () {
            updateContadorFiltro();
        },
        "order": [[2, "asc"],[5, "asc"]]
    });
    botones('#listaIngresos tbody',tabla);
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