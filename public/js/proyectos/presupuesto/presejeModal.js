let tp_presup = 2; //Pres.Eje.
let crono_modal = 0;

$(function(){
    $('#listaPresEje tbody').on("click","tr", function(){
        let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
        var id = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;

        if (id !== null && id !== undefined){          
            if (formName =='cronoeje'){
                console.log(cod);    
                $('[name=id_presupuesto]').val(id);
                $('#codigo').text(cod);
                $('#descripcion').text(des);
                if (crono_modal == 0){
                    var rspta = confirm('¿Está seguro que desea generar el Cronograma de Ejecución según el presupuesto seleccionado?:'+cod);
                    if (rspta){
                        listar_acus_crono(id);
                        $('[name=modo]').val('new');
                    }
                } else {
                    console.log('mostrar_gant');
                    listar_acus_cronograma(id);
                    mostrar_gant(id);
                    $('[name=modo]').val('update');
                }
                $('#modal-preseje').modal('hide');
            } 
            else if (formName =='cronovaleje'){
                $('[name=id_presupuesto]').val(id);
                $('#codigo').text(cod);
                $('[name=nombre_opcion]').val(des);
                $('#modal-preseje').modal('hide');

                if (crono_modal == 0){
                    $('[name=modo]').val('new');
                    $('[name=numero]').attr('disabled',false);
                    $('[name=unid_program]').attr('disabled',false);
                    $('[name=btn_actualizar]').attr('disabled',false);
                } else {
                    $('[name=modo]').val('update');
                    $('[name=numero]').attr('disabled',true);
                    $('[name=unid_program]').attr('disabled',true);
                    $('[name=btn_actualizar]').attr('disabled',true);
                    mostrar_crono_valorizado();
                } 
            }
            else {
                mostrar_preseje(id);
                $('#modal-preseje').modal('hide');       
            }
        }
    });
});

function listarPresEje(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPresEje').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'mostrar_presupuestos/'+tp_presup,//2 Presupuesto de Ejecucion
        'columns': [
            {'data': 'id_presupuesto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'simbolo'},
            {'render':
                function (data, type, row){
                    return (formatNumber.decimal(row['sub_total'],'',-2));
                },'class':' right'
            },
            {'data': 'moneda'}
        ],
        'columnDefs': [{ 'aTargets': [0,6], 'sClass': 'invisible'}],
    });
    
}
function presejeModal(modal){
    $('#modal-preseje').modal({
        show: true
    });
    clearDataTable();

    if (modal == ''){
        listarPresEje();
    } 
    else if (modal === 'nuevo'){
        console.log('modal:'+modal);
        crono_modal = 0;
        listarPresEjeCrono(crono_modal);
    } 
    else if (modal === 'modal'){
        console.log('modal:'+modal);
        crono_modal = 1;
        listarPresEjeCrono(crono_modal);
    }
    else if (modal === 'crononuevo'){
        crono_modal = 0;
        listarPresEjeCronoVal(crono_modal);
    }
    else if (modal === 'cronomodal'){
        crono_modal = 1;
        listarPresEjeCronoVal(crono_modal);
    }
}
function listarPresEjeCrono(modal){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPresEje').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'listar_pres_crono/'+modal+'/'+tp_presup,
        'columns': [
            {'data': 'id_presupuesto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'simbolo'},
            {'render':
                function (data, type, row){
                    return (formatNumber.decimal(row['sub_total'],'',-2));
                }
            },
            {'data': 'moneda'}
        ],
        'columnDefs': [{ 'aTargets': [0,6], 'sClass': 'invisible'}],
    });
}

function listarPresEjeCronoVal(modal){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPresEje').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'listar_pres_cronoval/'+modal+'/'+tp_presup,
        'columns': [
            {'data': 'id_presupuesto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'simbolo'},
            {'render':
                function (data, type, row){
                    return (formatNumber.decimal(row['sub_total'],'',-2));
                }
            },
            {'data': 'moneda'}
        ],
        'columnDefs': [{ 'aTargets': [0,6], 'sClass': 'invisible'}],
    });
}
