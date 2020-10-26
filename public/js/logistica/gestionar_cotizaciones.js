var rutaSedeByEmpresa;

function inicializar(_rutaSedeByEmpresa) {
    rutaSedeByEmpresa = _rutaSedeByEmpresa;
}

var listCheckReq = [];
var listCheckReqDet = [];
var adjuntoList = [];
let adjuntos_info_coti = [];
let only_adjuntos_coti = [];
var id_cotizacion_creada = 0;
var cantidadUpdateInItemList=[];

$(function () {

    lista_cotizaciones()

    var idReqCot = localStorage.getItem('idReqCot')
    if (idReqCot != null) {

        statusCkeckBoxListReq('DISABLED',true,'listaRequerimientoPendientes');
        document.getElementById('menu_tab_crear_coti').childNodes[1].children[0].setAttribute('data-toggle', 'notab');
        document.getElementById('menu_tab_crear_coti').childNodes[1].className ='disabled';
        document.getElementById('menu_tab_crear_coti').childNodes[3].className ='active';
        document.getElementById('contenido_tab_crear_coti').childNodes[1].className = 'tab-pane';
        document.getElementById('contenido_tab_crear_coti').childNodes[3].className = 'active';
 
        listCheckReq.push(
        {'id_req': idReqCot,
        'stateCheck': true}
        );
        
        getAllDetalleReqOfList('listaItemsRequerimiento');
        localStorage.removeItem('idReqCot');
    }

    $('#listaRequerimientoPendientes tbody').on('click', 'tr', function () {
        if ($(this).hasClass('eventClick')) {
            $(this).removeClass('eventClick')
        } else {
            $('#listaRequerimientoPendientes')
                .dataTable()
                .$('tr.eventClick')
                .removeClass('eventClick')
            $(this).addClass('eventClick')
        }
        let id = $(this)[0].childNodes[1].childNodes[0].dataset.idRequerimiento
        let stateCheck = $(this)[0].childNodes[1].childNodes[0].checked

        checkReq = {
            id_req: id,
            stateCheck: stateCheck,
        }
        let arrIdReq = []
        let countStateCheckTrue = 0
        // console.log(checkReq);
        if (listCheckReq.length == 0) {
            listCheckReq.push(checkReq)
            document
                .getElementById('btnGotToSecondTab')
                .removeAttribute('disabled')
        } else if (listCheckReq.length > 0) {
            listCheckReq.map(value => {
                arrIdReq.push(value.id_req)
            })

            if (arrIdReq.includes(checkReq.id_req) == true) {
                // actualiza
                listCheckReq.map(value => {
                    if (value.id_req == checkReq.id_req) {
                        value.stateCheck = checkReq.stateCheck
                    }
                })
            } else {
                listCheckReq.push(checkReq)
            }
            // si disabled btn
            listCheckReq.map(value => {
                if (value.stateCheck == true) {
                    countStateCheckTrue += 1
                }
            })

            if (countStateCheckTrue > 0) {
                document
                    .getElementById('btnGotToSecondTab')
                    .removeAttribute('disabled')
            } else {
                document
                    .getElementById('btnGotToSecondTab')
                    .setAttribute('disabled', true)
            }
        }
        // console.log(listCheckReq);
    })

    $('#listaRequerimientoPendientesAgregar tbody').on('click', 'tr', function () {
        if ($(this).hasClass('eventClick')) {
            $(this).removeClass('eventClick')
        } else {
            $('#listaRequerimientoPendientesAgregar')
                .dataTable()
                .$('tr.eventClick')
                .removeClass('eventClick')
            $(this).addClass('eventClick')
        }
        let id = $(this)[0].childNodes[1].childNodes[0].dataset.idRequerimiento
        let stateCheck = $(this)[0].childNodes[1].childNodes[0].checked
    
        checkReq = {
            id_req: id,
            stateCheck: stateCheck,
        }
        let arrIdReq = []
        let countStateCheckTrue = 0
        // console.log(checkReq);
        if (listCheckReq.length == 0) {
            listCheckReq.push(checkReq)
            document
                .getElementById('btnGotToSecondTabAgregar')
                .removeAttribute('disabled')
        } else if (listCheckReq.length > 0) {
            listCheckReq.map(value => {
                arrIdReq.push(value.id_req)
            })
    
            if (arrIdReq.includes(checkReq.id_req) == true) {
                // actualiza
                listCheckReq.map(value => {
                    if (value.id_req == checkReq.id_req) {
                        value.stateCheck = checkReq.stateCheck
                    }
                })
            } else {
                listCheckReq.push(checkReq)
            }
            // si disabled btn
            listCheckReq.map(value => {
                if (value.stateCheck == true) {
                    countStateCheckTrue += 1
                }
            })
    
            if (countStateCheckTrue > 0) {
                document
                    .getElementById('btnGotToSecondTabAgregar')
                    .removeAttribute('disabled')
            } else {
                document
                    .getElementById('btnGotToSecondTabAgregar')
                    .setAttribute('disabled', true)
            }
        }
        // console.log(listCheckReq);
    })


    $('#listaItemsRequerimiento tbody').on('click', 'tr', function () {
        // if ($(this).hasClass('eventClick')){
        //     $(this).removeClass('eventClick');
        // }
        // else {
        //     $('#listaItemsRequerimiento').dataTable().$('tr.eventClick').removeClass('eventClick');
        //     $(this).addClass('eventClick');
        // }
        let idReq = $(this)[0].childNodes[1].childNodes[0].dataset.idRequerimiento;
        let idDetReq = $(this)[0].childNodes[1].childNodes[0].dataset.idDetalleRequerimiento;
        let stateCheck = $(this)[0].childNodes[1].childNodes[0].checked;
        let codigo = $(this)[0].childNodes[3].childNodes[0].textContent;
        // console.log(codigo);
        checkDetReq = {
            id_req: idReq,
            codigo: codigo,
            id_det_req: idDetReq,
            stateCheck: stateCheck,
            newCantidad:0
        }
        let arrIdDetReq = []
        let countStateCheckTrue = 0

        // console.log(checkDetReq);
        if (listCheckReqDet.length == 0) {
            listCheckReqDet.push(checkDetReq)
            document
                .getElementById('btnGotToThirdTab')
                .removeAttribute('disabled')
        } else if (listCheckReqDet.length > 0) {
            listCheckReqDet.map(value => {
                arrIdDetReq.push(value.id_det_req)
            })

            if (arrIdDetReq.includes(checkDetReq.id_det_req) == true) {
                // actualiza
                listCheckReqDet.map(value => {
                    if (value.id_det_req == checkDetReq.id_det_req) {
                        value.stateCheck = checkDetReq.stateCheck
                    }
                })
            } else {
                listCheckReqDet.push(checkDetReq)
            }
            // si disabled btn
            listCheckReqDet.map(value => {
                if (value.stateCheck == true) {
                    countStateCheckTrue += 1
                }
            })

            if (countStateCheckTrue > 0) {
                document
                    .getElementById('btnGotToThirdTab')
                    .removeAttribute('disabled')
            } else {
                document
                    .getElementById('btnGotToThirdTab')
                    .setAttribute('disabled', true)
            }
        }
        // console.log('listaItemsRequerimiento click');
        // console.log(listCheckReqDet);
    })

    $('#listaItemsRequerimientoModalEditCoti tbody').on('click', 'tr', function () {

        let idReq = $(this)[0].childNodes[2].childNodes[0].dataset.idRequerimiento;
        let idDetReq = $(this)[0].childNodes[2].childNodes[0].dataset.idDetalleRequerimiento;
        let stateCheck = $(this)[0].childNodes[2].childNodes[0].checked;

        checkDetReq = {
            id_req: idReq,
            id_det_req: idDetReq,
            stateCheck: stateCheck,
            newCantidad:0
        }
        let arrIdDetReq = []
        let countStateCheckTrue = 0

        // console.log(checkDetReq);
        if (listCheckReqDet.length == 0) {
            listCheckReqDet.push(checkDetReq);
            document.getElementById('btnEliminarItemDeCotizacion').removeAttribute('disabled');
        } else if (listCheckReqDet.length > 0) {
            listCheckReqDet.map(value => {
                arrIdDetReq.push(value.id_det_req);
            })

            if (arrIdDetReq.includes(checkDetReq.id_det_req) == true) {
                // actualiza
                listCheckReqDet.map(value => {
                    if (value.id_det_req == checkDetReq.id_det_req) {
                        value.stateCheck = checkDetReq.stateCheck;
                    }
                })
            } else {
                listCheckReqDet.push(checkDetReq)
            }
            // si disabled btn
            listCheckReqDet.map(value => {
                if (value.stateCheck == true) {
                    countStateCheckTrue += 1;
                }
            })

            if (countStateCheckTrue > 0) {
                document.getElementById('btnEliminarItemDeCotizacion').removeAttribute('disabled');
            } else {
                document.getElementById('btnEliminarItemDeCotizacion').setAttribute('disabled', true)
            }
        }
        console.log('listaItemsRequerimiento click');
        console.log(listCheckReqDet);

        
    })



    $('#listaItemsRequerimientoAgregar tbody').on('click', 'tr', function () {

        let idReq = $(this)[0].childNodes[1].childNodes[0].dataset.idRequerimiento;
        let idDetReq = $(this)[0].childNodes[1].childNodes[0].dataset.idDetalleRequerimiento;
        let stateCheck = $(this)[0].childNodes[1].childNodes[0].checked;

        checkDetReq = {
            id_req: idReq,
            id_det_req: idDetReq,
            stateCheck: stateCheck,
            newCantidad:0
        }
        let arrIdDetReq = []
        let countStateCheckTrue = 0

        // console.log(checkDetReq);
        if (listCheckReqDet.length == 0) {
            listCheckReqDet.push(checkDetReq)
            document
                .getElementById('btnAddAllItemReqToCoti')
                .removeAttribute('disabled')
        } else if (listCheckReqDet.length > 0) {
            listCheckReqDet.map(value => {
                arrIdDetReq.push(value.id_det_req)
            })

            if (arrIdDetReq.includes(checkDetReq.id_det_req) == true) {
                // actualiza
                listCheckReqDet.map(value => {
                    if (value.id_det_req == checkDetReq.id_det_req) {
                        value.stateCheck = checkDetReq.stateCheck
                    }
                })
            } else {
                listCheckReqDet.push(checkDetReq)
            }
            // si disabled btn
            listCheckReqDet.map(value => {
                if (value.stateCheck == true) {
                    countStateCheckTrue += 1
                }
            })

            if (countStateCheckTrue > 0) {
                document
                    .getElementById('btnAddAllItemReqToCoti')
                    .removeAttribute('disabled')
            } else {
                document
                    .getElementById('btnAddAllItemReqToCoti')
                    .setAttribute('disabled', true)
            }
        }
        // console.log(listCheckReqDet);
    })



    let defaultIdEmpresa = 1
    document.getElementById('id_empresa_select_req').value = defaultIdEmpresa // default select filter 1 = okc
    listarRequerimiento(defaultIdEmpresa)
})


function handleChangeIncluirSede(event){
    
    let selectEmpresa = document.querySelector("div[id='requerimiento'] select[id='id_empresa_select_req']");
    let id_empresa = selectEmpresa.value;

    if(event.target.checked == true){
        document.querySelector("div[id='requerimiento'] select[id='id_sede_select_req']").removeAttribute('disabled');
        getDataSelectSede(id_empresa,'div',"requerimiento","id_sede_select_req");

    }else{
        document.querySelector("div[id='requerimiento'] select[id='id_sede_select_req']").setAttribute('disabled',true);
        let selectElement = document.querySelector("div[id='requerimiento'] select[id='id_sede_select_req']");
        var i, L = selectElement.options.length - 1;
        for(i = L; i >= 0; i--) {
            selectElement.remove(i);
        }
        listar_requerimientos(id_empresa,null,null);

    }

}
function getDataSelectSede(id_empresa = null, element , idElement ,selector){
    if(id_empresa >0){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: rutaSedeByEmpresa+ '/' + id_empresa,
            dataType: 'JSON',
            success: function(response){
                llenarSelectSede(response,element,idElement,selector);
            }
        });
    }
    return false;
}

function llenarSelectSede(array,element, idElement,selector){

    let selectElement = document.querySelector(element+"[id='"+idElement+"'] select[id='"+selector+"']");
    // console.log(tabId);
    // console.log(selector);
    // console.log(selectElement);
    
    if(selectElement.options.length>0){
        var i, L = selectElement.options.length - 1;
        for(i = L; i >= 0; i--) {
            selectElement.remove(i);
        }
    }

    array.forEach(element => {
        let option = document.createElement("option");
        option.text = element.descripcion;
        option.value = element.id_sede;
        selectElement.add(option);
    });

    // console.log(selectElement.value);
    let id_empresa = document.querySelector("div[id='requerimiento'] select[id='id_empresa_select_req']");
    let id_sede= selectElement.value;
    listar_requerimientos(id_empresa,id_sede,null);

}

function handleChangeFilterCrearCotiByEmpresa(e){
    let id_empresa =e.target.value;
    getDataSelectSede(id_empresa,"div", "crear_coti","id_sede_crear_coti");

}

function handleChangeFilterReqBySede(e){
    let id_sede =e.target.value;
    let id_empresa = document.querySelector("div[id='requerimiento'] select[id='id_empresa_select_req']");
    listar_requerimientos(id_empresa,id_sede,null);

    
}

function handleChangeFilterReqByEmpresa(e) {
    getDataSelectSede(e.target.value,'div',"requerimiento", "id_sede_select_req");
    listar_requerimientos(e.target.value,null,null);
}

// if(value.id_det_req == checkDetReq.id_det_req){
//     value.stateCheck=checkDetReq.stateCheck
// }else{
//     listCheckReqDet.push(checkDetReq);
// }
// if(value.stateCheck == true){
//     document.getElementById('btnGotToThirdTab').removeAttribute("disabled");
// }else{
//     document.getElementById('btnGotToThirdTab').setAttribute("disabled",true);

// }
function allowCheckBoxListReq(event){
    event.preventDefault();
    statusCkeckBoxListReq('DISABLED',false,'listaRequerimientoPendientes');
    statusCkeckBoxListReq('CHECKED',false, 'listaRequerimientoPendientes');
    listCheckReq = [];
    listCheckReqDet = [];
}

function statusCkeckBoxListReq(option, value,loadTo){
    let sizeTr =document.querySelector('table[id="'+loadTo+'"] tbody').children.length;

    switch (option) {
        case 'DISABLED':
            for(i=0;i<sizeTr;i++){
                document.querySelector('table[id="'+loadTo+'"] tbody').children[i].cells[1].children[0].disabled = value;
            }
            break;
        case 'CHECKED':
            for(i=0;i<sizeTr;i++){
                document.querySelector('table[id="'+loadTo+'"] tbody').children[i].cells[1].children[0].checked = value;
            }
        
            break;

        default:
            break;
    }
}

function gotToSecondTab(e) {
    e.preventDefault();
    statusCkeckBoxListReq('DISABLED',true,'listaRequerimientoPendientes');
    document.getElementById('menu_tab_crear_coti').childNodes[1].children[0].setAttribute('data-toggle', 'notab');
    document.getElementById('menu_tab_crear_coti').childNodes[1].className ='disabled';
    document.getElementById('menu_tab_crear_coti').childNodes[3].className ='active';
    document.getElementById('contenido_tab_crear_coti').childNodes[1].className = 'tab-pane';
    document.getElementById('contenido_tab_crear_coti').childNodes[3].className = 'active';
    getAllDetalleReqOfList('listaItemsRequerimiento');
}

function gotToSecondTabAgregar(e) {
    e.preventDefault();
    statusCkeckBoxListReq('DISABLED',true,'listaRequerimientoPendientesAgregar');
    document.getElementById('menu_tab_crear_coti_agregar').childNodes[1].children[0].setAttribute('data-toggle', 'notab');
    document.getElementById('menu_tab_crear_coti_agregar').childNodes[1].className ='disabled';
    document.getElementById('menu_tab_crear_coti_agregar').childNodes[3].className ='active';
    document.getElementById('contenido_tab_crear_coti_agregar').childNodes[1].className = 'tab-pane';
    document.getElementById('contenido_tab_crear_coti_agregar').childNodes[3].className = 'active';
    getAllDetalleReqOfList('listaItemsRequerimientoAgregar');
}

function gotToThirdTab(e) {
    e.preventDefault();
    let tablelistaItemsRequerimiento = document.getElementById('listaItemsRequerimiento');
    let tableChildren = tablelistaItemsRequerimiento.children[1].children;
    let sizeTableChildren = tableChildren.length;
    for(i=0;i<sizeTableChildren;i++){
        if(tableChildren[i].cells.length >0){
            cantidadUpdateInItemList.push( {
                'id_det_req':tableChildren[i].cells[11].children[0].dataset.idDetReq, 
                'cantidad':tableChildren[i].cells[11].children[0].value
        });
        };
    }

    // console.log(listCheckReqDet);
    // console.log(cantidadUpdateInItemList);
    //  if has cantidad => insert to listCheckReqDet 
    cantidadUpdateInItemList.map((ItemWithNewValue,i) => {
        listCheckReqDet.map((reqDet,j) => {            
           if(ItemWithNewValue.id_det_req == reqDet.id_det_req){
            listCheckReqDet[j].newCantidad = ItemWithNewValue.cantidad;
           }
        });
        
    });

    // console.log('gotToThirdTab');
    // console.log(listCheckReqDet);



    // document.getElementsByName('id_sede')[0].value = document.getElementById(
    //     'id_sede_select_req'
    // ).value

    document
        .getElementById('menu_tab_crear_coti')
        .childNodes[3].children[0].setAttribute('data-toggle', 'notab')
    document.getElementById('menu_tab_crear_coti').childNodes[3].className =
        'disabled'
    document.getElementById('menu_tab_crear_coti').childNodes[5].className =
        'active'
    document.getElementById(
        'contenido_tab_crear_coti'
    ).childNodes[3].className = 'tab-pane'
    document.getElementById(
        'contenido_tab_crear_coti'
    ).childNodes[5].className = 'active'
}
function openModalEnviarCoti(e) {
    e.preventDefault();
    if (id_cotizacion_creada > 0) {
        $.ajax({
            type: 'GET',
            url: 'listaCotizacionesPorGrupo/' + id_cotizacion_creada,
            dataType: 'JSON',
            success: function (response) {
                // console.log(response);
                envioCotizacionModal(response['data'][0], id_cotizacion_creada)



            },
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR)
            console.log(textStatus)
            console.log(errorThrown)
        })
    }
}

function resetProcessCreateCoti(e){
	e.preventDefault();
	document.getElementsByName('id_empresa')[0].value='';
	document.getElementsByName('razon_social')[0].value='';
	document.getElementsByName('id_contacto')[0].value='';
	document.getElementsByName('id_proveedor')[0].value='';
	document.getElementsByName('id_contrib')[0].value='';
	id_cotizacion_creada =0;
	var listCheckReq = [];
	var listCheckReqDet = [];
	var adjuntoList = [];
	let adjuntos_info_coti = [];
	let only_adjuntos_coti = [];
	limpiarTabla('listaRequerimientoPendientes');
	limpiarTabla('listaItemsRequerimiento');
	document.getElementById('btnGotToSecondTab').setAttribute('disabled',true);
    document.getElementById('btnGotToThirdTab').setAttribute('disabled',true);
    listar_requerimientos(null,null,null);

	
	document.getElementById('menu_tab_crear_coti').childNodes[5].children[0].setAttribute('data-toggle', 'notab')
	document.getElementById('menu_tab_crear_coti').childNodes[5].className ='disabled'
	document.getElementById('menu_tab_crear_coti').childNodes[1].className ='active'
	document.getElementById('contenido_tab_crear_coti').childNodes[5].className = 'tab-pane'
	document.getElementById('contenido_tab_crear_coti').childNodes[1].className = 'active'
	
}

function gotToSecondToFirstTab(e) {
	e.preventDefault();
	document
	.getElementById('menu_tab_crear_coti')
	.childNodes[3].children[0].setAttribute('data-toggle', 'notab')
	document.getElementById('menu_tab_crear_coti').childNodes[3].className =
	'disabled'
	document.getElementById('menu_tab_crear_coti').childNodes[1].className =
	'active'
	document.getElementById(
	'contenido_tab_crear_coti'
	).childNodes[3].className = 'tab-pane'
	document.getElementById(
	'contenido_tab_crear_coti'
	).childNodes[1].className = 'active'
}
function gotToSecondToFirstTabAgregar(e) {
	e.preventDefault();
	document.getElementById('menu_tab_crear_coti_agregar').childNodes[3].children[0].setAttribute('data-toggle', 'notab');
	document.getElementById('menu_tab_crear_coti_agregar').childNodes[3].className ='disabled';
	document.getElementById('menu_tab_crear_coti_agregar').childNodes[1].className ='active';
	document.getElementById('contenido_tab_crear_coti_agregar').childNodes[3].className = 'tab-pane';
	document.getElementById('contenido_tab_crear_coti_agregar').childNodes[1].className = 'active';
}

function gotToThirdToSecondTab(e) {
	e.preventDefault();
    document
        .getElementById('menu_tab_crear_coti')
        .childNodes[5].children[0].setAttribute('data-toggle', 'notab')
    document.getElementById('menu_tab_crear_coti').childNodes[5].className =
        'disabled'
    document.getElementById('menu_tab_crear_coti').childNodes[3].className =
        'active'
    document.getElementById(
        'contenido_tab_crear_coti'
    ).childNodes[5].className = 'tab-pane'
    document.getElementById(
        'contenido_tab_crear_coti'
    ).childNodes[3].className = 'active'
}
// function gotToFourthToThirdTab() {
//     document
//         .getElementById('menu_tab_crear_coti')
//         .childNodes[7].children[0].setAttribute('data-toggle', 'notab')
//     document.getElementById('menu_tab_crear_coti').childNodes[7].className =
//         'disabled'
//     document.getElementById('menu_tab_crear_coti').childNodes[5].className =
//         'active'
//     document.getElementById(
//         'contenido_tab_crear_coti'
//     ).childNodes[7].className = 'tab-pane'
//     document.getElementById(
//         'contenido_tab_crear_coti'
//     ).childNodes[5].className = 'active'


// }

function getAllDetalleReqOfList(tableName) {
    // console.log("cargar todo los detalles requerimientos cargados");
    //  console.log(listCheckReq);
    mostrar_detalle_requerimiento(listCheckReq, tableName)
}

function mostrar_detalle_requerimiento(listCheckReq, tableName) {
    let data = { data: listCheckReq }
    // console.log(data);

    limpiarTabla(tableName);
    $.ajax({
        type: 'GET',
        url: 'detalle_requerimiento',
        dataType: 'JSON',
        data: data,

        success: function (response) {
            // console.log(response);
            // console.log(tableName);
            // console.log(response);
            
            if (response.length > 0) {
                if (tableName === 'listaItemsRequerimiento') {
                    llenarTablaListaItemRequerimiento(response,tableName)
                } else if (tableName === 'listaDetReqACotizar') {
                    llenarTablaDetRequerimientoAcotizar(response);                    
                } else if (tableName === 'listaItemsRequerimientoAgregar') {
                    llenarTablaListaItemRequerimiento(response,tableName);
                }
            } else {
                alert('no hay detalle requerimiento')
            }

            // var verifica = false;
            // $('#listaItemsRequerimiento tbody tr').each(function(e){
            //     var id_requerimiento = $(this).find("td input[name=id_requerimiento]").val();
            //     if (id_requerimiento == id){
            //         verifica = true;
            //     }
            // });
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

function llenarTablaListaItemRequerimiento(data,tableName) {
    // console.log(data);
    
    // limpiarTabla(tableName);
    htmls = '<tr></tr>';
    let optionAlmacen = '';
    // $('#listaItemsRequerimiento tbody').html(htmls);
    // var table = document.getElementById('listaItemsRequerimiento');
    // var table = document.querySelector('table[id="listaItemsRequerimiento"] tbody');
   var table = document.getElementById(tableName).getElementsByTagName('tbody')[0];
   var newRow = table.insertRow(table.rows.length);
   newRow.innerHTML = htmls;

   for (var a = 0; a < data.length; a++) {
        var row = table.insertRow(a + 1);

        var tdIdDetReq = row.insertCell(0)
        tdIdDetReq.setAttribute('class', 'hidden')
        tdIdDetReq.innerHTML = data[a].id_detalle_requerimiento
        row.insertCell(1).innerHTML =
            '<input type="checkbox" data-id-requerimiento="' +
            data[a].id_requerimiento +
            '" data-id-detalle-requerimiento="' +
            data[a].id_detalle_requerimiento +
            '"/>';
        row.insertCell(2).innerHTML = a + 1;
        row.insertCell(3).innerHTML = data[a].cod_req;
        row.insertCell(4).innerHTML = data[a].cod_producto;
        row.insertCell(5).innerHTML = data[a].descripcion_adicional;
        row.insertCell(6).innerHTML = data[a].unidad_medida_detalle_req;
        row.insertCell(7).innerHTML = data[a].cantidad;
        row.insertCell(8).innerHTML = data[a].precio_referencial;
        row.insertCell(9).innerHTML = data[a].fecha_entrega;
        row.insertCell(10).innerHTML = data[a].lugar_entrega;
        row.insertCell(11).innerHTML = '<input type="number" min="0" max="' + data[a].cantidad + '" value="" class="form-control" data-id-det-req="' + data[a].id_detalle_requerimiento + '" data-id-req="' + data[a].id_requerimiento + '" name="nueva_cantidad_cotiza[]" >';
        row.insertCell(12).innerHTML =
            '<button type="button" class="btn btn-primary btn-sm" title="Ver Saldos" onClick="ver_saldos(' +
            data[a].id_producto +
            ',' +
            data[a].id_tipo_item +
            ');"> <i class="fas fa-search"></i></button>';

        // if (data[a].almacen.length == 0) {
        //     row.insertCell(11).innerHTML = '<input type="number" min="0" max="' + data[a].cantidad + '" value="' + data[a].stock_comprometido + '" class="form-control activation stock_comprometido" data-id-det-req="' + data[a].id_detalle_requerimiento + '" data-id-req="' + data[a].id_requerimiento + '" name="stock_comprometido[]" disabled >';
        //     optionAlmacen += '<select class="form-control almacen_selected" data-id-det-req="' + data[a].id_detalle_requerimiento + '" disabled >';
        // } else {
        //     row.insertCell(11).innerHTML =
        //         '<input type="number" min="0" max="' + data[a].cantidad + '" value="' + data[a].stock_comprometido + '" class="form-control activation stock_comprometido" data-id-det-req="' + data[a].id_detalle_requerimiento + '" data-id-req="' + data[a].id_requerimiento + '"name="stock_comprometido[]" >';
        //     optionAlmacen = '<select class="form-control almacen_selected" data-id-det-req="' + data[a].id_detalle_requerimiento + '" >';
        // }

        // if (data[a].almacen.length > 0) {
        //     data[a].almacen.forEach(element => {
        //         optionAlmacen +=
        //             '<option value="' +
        //             element.id_almacen +
        //             '">' +
        //             element.descripcion +
        //             '</option>';
        //     })
        // }
        // optionAlmacen += '</select>'
        // row.insertCell(12).innerHTML = optionAlmacen;
        // optionAlmacen = '';


    }
    // var row = table.insertRow(data.length + 1)
    // let cell1 = row.insertCell(0);
    // cell1.innerHTML = '';
    // cell1.colSpan = 10;
    // let cell2 = row.insertCell(1);
    // cell2.innerHTML =
    //     '<button class="btn btn-success" role="button" data-toggle="collapse"  onClick="guardarStockComprometido();"> Guardar Stock <i class="fas fa-save"></i></button>';
    // cell2.colSpan = 2;
    // cell2.setAttribute('class', 'text-center');

    return null;
}
function llenarTablaDetRequerimientoAcotizar(data) {
    console.log('data');
    console.log(data);

    limpiarTabla('listaDetReqACotizar');
    htmls = '<tr></tr>';
    let optionAlmacen = '';
    $('#listaDetReqACotizar tbody').html(htmls);
    var table = document.getElementById('listaDetReqACotizar');
    for (var a = 0; a < data.length; a++) {
        var row = table.insertRow(a + 1);

        var tdIdDetReq = row.insertCell(0);
        tdIdDetReq.setAttribute('class', 'hidden');
        tdIdDetReq.innerHTML = data[a].id_detalle_requerimiento;
        row.insertCell(1).innerHTML = a + 1;
        row.insertCell(2).innerHTML = data[a].cod_req;
        row.insertCell(3).innerHTML = data[a].cod_producto;
        row.insertCell(4).innerHTML = data[a].descripcion_adicional;
        row.insertCell(5).innerHTML = data[a].unidad_medida_detalle_req;
        row.insertCell(6).innerHTML = data[a].cantidad;
        row.insertCell(7).innerHTML = data[a].precio_referencial;
        row.insertCell(8).innerHTML = data[a].fecha_entrega;
        row.insertCell(9).innerHTML = data[a].lugar_entrega;
        row.insertCell(10).innerHTML = data[a].stock_comprometido;
        row.insertCell(11).innerHTML = data[a].descripcion_almacen;
    }

    return null;
}
// function mostrar_detalle_requerimiento(id,arr,view){
//     // console.log('enviando....');
//     // console.log('id',id);
//     // console.log('arr',arr);
//     // console.log('view',view);

//     limpiarTabla('listaDetReqACotizar');
//     $.ajax({
//         type: 'GET',
//         url: '/detalle_requerimiento/'+id+'/'+view,
//         dataType: 'JSON',
//         data: arr,

//         success: function(response){

//             // console.log(response);

//             // var verifica = false;
//             // $('#listaItemsRequerimiento tbody tr').each(function(e){
//             //     var id_requerimiento = $(this).find("td input[name=id_requerimiento]").val();
//             //     if (id_requerimiento == id){
//             //         verifica = true;
//             //     }
//             // });
//             // if (!verifica){
//                 if(view =='VIEW_CHECKBOX'){

//                     $('#listaItemsRequerimiento tbody').append(response);
//                 }else{

//                     $('#listaDetReqACotizar tbody').append(response);
//                 }

//             // } else {
//             //     alert('El requerimiento seleccionado ya fue agregado!');
//             // }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function hasAllInputFill() {
    let id_empresa = document.getElementsByName('id_empresa')[0].value;
    let razon_social= document.getElementsByName('razon_social')[0].value;
    let id_contacto= document.getElementsByName('id_contacto')[0].value;

    if ((id_empresa.length > 0) && (razon_social.length > 0) && (id_contacto.length > 0)) {
        return true;
    } else {
        return false;
    }
}
function generar_cotizacion() {
	// e.preventDefault();
    if (hasAllInputFill() == false) {
        alert('para seguir Debe completar todo los campos');
    } else {
        // validar input stock comprometido
        let inputsStockComprometido = document.querySelectorAll('.stock_comprometido')
        let countExceed = 0
        inputsStockComprometido.forEach(function (element) {
            let MaxValue = parseInt(element.max)
            let ActualValue = parseInt(element.value)
            if (ActualValue > MaxValue) {
                countExceed += 1
            }
        })

        if (countExceed == 0) {
            GuardarCotizacion();
        } else {
            alert('Stock Comprometido es mayor a la cantidad solicitada')
        }
    }

}
function get_data_form_generar_cotizacion() {
    let id_empresa = document.getElementsByName('id_empresa')[0].value
    let id_sede = document.getElementById('id_sede_crear_coti').value
    let id_proveedor = document.getElementsByName('id_proveedor')[0].value
    let id_contacto = document.getElementsByName('id_contacto')[0].value
    let text_contacto = document.getElementsByName('id_contacto')[0].textContent
    let cont_array = text_contacto.split('-')
    let email_contacto = cont_array[2]

    let data = {
        id_empresa: id_empresa,
        id_sede: id_sede,
        id_proveedor: id_proveedor,
        id_contacto: id_contacto,
        email_contacto: email_contacto,
    }
    return data
}

function GuardarCotizacion() {
    // console.log(listCheckReqDet);
    let formCotizacionProvEmpresa = get_data_form_generar_cotizacion();
    // console.log(get_data_form_generar_cotizacion);
    var items = {};
    var data = {};

    // listCheckReqDet.cotizacion =formCotizacionProvEmpresa;

    data.req = listCheckReqDet;
    data.formdata = formCotizacionProvEmpresa;
        console.log(data);

    items = { data: data };
    var id_grupo_cotizacion = $('[name=id_grupo_cotizacion]').val();
    // console.log('items enviados=>');
    console.log('id_grupo_cotizacion',id_grupo_cotizacion);

    if (listCheckReqDet.length > 0) {
        $.ajax({
            type: 'POST',
            url: 'guardar_cotizacion/' + id_grupo_cotizacion,
            dataType: 'JSON',
            data: items,
            success: function (response) {
                console.log(response);

                if (response.status == 'success') {
                    if (response['id_cotizacion'] > 0) {
                        id_cotizacion_creada = response['id_cotizacion'];
                        alert('Cotización registrada con éxito')
                        var id_grupo = response['id_grupo'];
                        var codigo_cuadro_comparativo = response['codigo_grupo'];
                        $('[name=id_grupo_cotizacion]').val(id_grupo)
                        $('[name=codigo_cuadro_comparativo]').val(codigo_cuadro_comparativo)
                        lista_cotizaciones()
                        // listarRequerimiento(1)
                        document.getElementById('btnOpenModalEnviarCoti').removeAttribute('disabled');
                        document.getElementById('btnResetProcessCreateCoti').removeAttribute('disabled');
                        // listar_cotizaciones(id_grupo);
                        // listar_items_cotizaciones(id_grupo);
                        // limpiarTabla('listaItemsRequerimiento');

                        // changeStateButton('guardar');
                    }
                } else {
                    console.log(response.message)
                }
            },
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR)
            console.log(textStatus)
            console.log(errorThrown)
        })
    }
}

// function listar_cotizaciones(id_grupo){

//     $.ajax({
//         type: 'GET',
//         url: '/cotizaciones_por_grupo/'+id_grupo,
//         dataType: 'JSON',
//         success: function(response){
//             // console.log(response);

//             $('#listaCotizaciones tbody').html(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// function listar_items_cotizaciones(id_grupo){
//     $.ajax({
//         type: 'GET',
//         url: '/items_cotizaciones_por_grupo/'+id_grupo,
//         dataType: 'JSON',
//         success: function(response){
//             // console.log(response);

//             $('#listaItemsRequerimiento tbody').html(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
function mostrar_grupo_cotizacion(id_grupo) {
    $.ajax({
        type: 'GET',
        url: 'mostrar_grupo_cotizacion/' + id_grupo,
        dataType: 'JSON',
        success: function (response) {
            $('[name=id_grupo_cotizacion]').val(response.id_grupo_cotizacion)
            $('[name=codigo_grupo]').val(response.codigo_grupo)
            $('[name=fecha_inicio]').val(response.fecha_inicio)
            $('[name=fecha_fin]').val(response.fecha_fin)
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}


function mostrar_cotizacion(id_cotizacion, modal) {
    // console.log('id_cotizacion'+id_cotizacion);
    $.ajax({
        type: 'GET',
        url: 'mostrar_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);
            // $('[name=id_cotizacion]').val(response['cotizacion'].id_cotizacion)
            $('#codigo_cotizacion').val(response['cotizacion'].codigo_cotizacion)
            document.querySelector('div[id="modal-'+modal+'"] input[name="id_cotizacion"]').value = response['cotizacion'].id_cotizacion;
            document.querySelector('div[id="modal-'+modal+'"] input[name="codigo_cotizacion"]').value = response['cotizacion'].codigo_cotizacion;
            document.querySelector('div[id="modal-'+modal+'"] input[name="razon_social"]').value = response['cotizacion'].razon_social;
            document.querySelector('div[id="modal-'+modal+'"] input[name="id_proveedor"]').value = response['cotizacion'].id_proveedor;
            document.querySelector('div[id="modal-'+modal+'"] select[name="id_empresa"]').value = response['cotizacion'].id_empresa;
            // $('[name=codigo_cotizacion]').val(response['cotizacion'].codigo_cotizacion)
            // $('[name=razon_social]').val(response['cotizacion'].razon_social)
            // $('[name=id_proveedor]').val(response['cotizacion'].id_proveedor)
            // $('[name=id_empresa]').val(response['cotizacion'].id_empresa)

            var option = ''
            for (var i = 0; i < response['contacto'].length; i++) {
                option +=
                    '<option value="' +
                    response['contacto'][i].id_datos_contacto +
                    '">' +
                    response['contacto'][i].nombre +
                    ' - ' +
                    response['contacto'][i].cargo +
                    ' - ' +
                    response['contacto'][i].email +
                    '</option>'
            }
            document.querySelector('div[id="modal-'+modal+'"] select[name="id_contacto"]').innerHTML = '<option value="0" disabled selected>Elija una opción</option>' +
            option;

            // $('[name=id_contacto]').html(
            //     '<option value="0" disabled selected>Elija una opción</option>' +
            //     option
            // )
            document.querySelector('div[id="modal-'+modal+'"] select[name="id_contacto"]').value = response['cotizacion'].id_contacto;
            // $('[name=id_contacto]').val(response['cotizacion'].id_contacto)
            cargar_imagen()
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}
function change_proveedor(id_prov) {
    $.ajax({
        type: 'GET',
        url: 'mostrar_email_proveedor/' + id_prov,
        dataType: 'JSON',
        success: function (response) {
            //  console.log(response);
            var option = ''
            for (var i = 0; i < response.length; i++) {
                option +=
                    '<option value="' +
                    response[i].id_datos_contacto +
                    '">' +
                    response[i].nombre +
                    ' - ' +
                    response[i].cargo +
                    ' - ' +
                    response[i].email +
                    '</option>'
            }
            $('[name=id_contacto]').html(
                '<option value="0" disabled selected>Elija una opción</option>' +
                option
            )
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

// function action_cotizacion(option) {
//     let urlBase = ''
//     switch (option) {
//         case 'UPDATE':
//             urlBase = '/update_cotizacion'
//             break

//         case 'DUPLICATE':
//             urlBase = '/duplicate_cotizacion'
//             break

//         default:
//             console.log('no hay acción disponible')

//             break
//     }
//     var id_cotizacion = $('[name=id_cotizacion]').val()
//     var id_proveedor = $('[name=id_proveedor]').val()
//     var id_empresa = $('[name=id_empresa]').val()
//     var id_contacto = $('[name=id_contacto]').val()
//     var contacto = $('select[name="id_contacto"] option:selected').text()
//     var cont_array = contacto.split(' - ')
//     // console.log(cont_array[2]);

//     var data =
//         'id_proveedor=' +
//         id_proveedor +
//         '&id_cotizacion=' +
//         id_cotizacion +
//         '&id_empresa=' +
//         id_empresa +
//         '&id_contacto=' +
//         id_contacto +
//         '&email_proveedor=' +
//         cont_array[2]

//     // console.log(data);

//     if (id_proveedor !== '') {
//         if (id_empresa !== null) {
//             if (id_contacto !== null) {
//                 $.ajax({
//                     type: 'POST',
//                     url: urlBase,
//                     data: data,
//                     dataType: 'JSON',
//                     success: function (response) {
//                         // console.log(response);

//                         if (response.status == 'success') {
//                             var id_grupo = $('[name=id_grupo_cotizacion]').val()
//                             // console.log('id_grupo',id_grupo);

//                             // listar_cotizaciones(id_grupo);

//                             if (option == 'UPDATE') {
//                                 $('#modal-cotizacion_proveedor').modal('hide')
//                             }
//                         } else {
//                             console.log(response.message)
//                         }
//                     },
//                 }).fail(function (jqXHR, textStatus, errorThrown) {
//                     console.log(jqXHR)
//                     console.log(textStatus)
//                     console.log(errorThrown)
//                 })
//             } else {
//                 alert('Es necesario que seleccione un email-proveedor')
//             }
//         } else {
//             alert('Es necesario que seleccione una empresa')
//         }
//     } else {
//         alert('Es necesario que seleccione un proveedor')
//     }
// }

function downloadSolicitudCotizacion(id_cotizacion) {
    if (id_cotizacion == 0) {
        alert('NO existe un ID de cotización')
        
    } else {

        $.ajax({
            type: 'GET',
            url: 'descargar_olicitud_cotizacion_excel/'+id_cotizacion,
            dataType: 'JSON',
            success: function(response){
                data = response;
                // console.log(response.status);
                // console.log(response.ruta);
                // console.log(response.message);
                if(response.status >0){
                    window.open(response.ruta);

                }else{
                    alert(response.message);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

    }
}

function ModalArchivosAdjuntosCotizacion(id_cotizacion) {
    $('#modal-adjuntos_cotizacion').modal({
        show: true,
    })
    if (id_cotizacion > 0) {
        listar_archivos_adjuntos_cotizacion(id_cotizacion)
    } else {
        alert('ERROR - No existe id_cotizacion')
    }
}

function listar_archivos_adjuntos_cotizacion(id_cotizacion) {
    let adjuntos_cotizacion = []

    $.ajax({
        type: 'GET',
        url: 'archivos_adjuntos_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            // console.log(response);

            if (response.length > 0) {
                for (x = 0; x < response.length; x++) {
                    adjuntos_cotizacion.push({
                        id_adjunto: response[x].id_adjunto,
                        id_detalle_requerimiento:
                            response[x].id_detalle_requerimiento,
                        archivo: response[x].archivo,
                        fecha_registro: response[x].fecha_registro,
                        estado: response[x].estado,
                        file: [],
                    })
                }

                llenar_tabla_archivos_adjuntos_cotizacion(adjuntos_cotizacion)
            }
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

function llenar_tabla_archivos_adjuntos_cotizacion(adjuntos) {
    limpiarTabla('listaArchivosCotizacion')
    htmls = '<tr></tr>'
    $('#listaArchivosCotizacion tbody').html(htmls)
    var table = document.getElementById('listaArchivosCotizacion')
    for (var a = 0; a < adjuntos.length; a++) {
        var row = table.insertRow(a + 1)
        var tdIdArchivo = row.insertCell(0)
        tdIdArchivo.setAttribute('class', 'hidden')
        tdIdArchivo.innerHTML = adjuntos[a].id_adjunto
            ? adjuntos[a].id_adjunto
            : '0'
        var tdIdDetalleReq = row.insertCell(1)
        tdIdDetalleReq.setAttribute('class', 'hidden')
        tdIdDetalleReq.innerHTML = adjuntos[a].id_detalle_requerimiento
            ? adjuntos[a].id_detalle_requerimiento
            : '0'
        row.insertCell(2).innerHTML = a + 1
        row.insertCell(3).innerHTML = adjuntos[a].archivo
            ? adjuntos[a].archivo
            : '-'
        row.insertCell(4).innerHTML =
            '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
            '<a' +
            '    class="btn btn-primary btn-sm "' +
            '    name="btnAdjuntarArchivos"' +
            '    href="/files/logistica/detalle_requerimiento/' +
            adjuntos[a].archivo +
            '"' +
            '    target="_blank"' +
            '    data-original-title="Descargar Archivo"' +
            '>' +
            '    <i class="fas fa-file-download"></i>' +
            '</a>' +
            '</div>'
    }
    return null
}

function limpiarTabla(idElement) {
    var table = document.getElementById(idElement)
    for (var i = table.rows.length - 1; i > 0; i--) {
        table.deleteRow(i)
    }
    return null
}

function anular_cotizacion(id_cotizacion) {
    var rspta = confirm('¿Está seguro que desea anular ésta cotización?')
    if (rspta) {
        $.ajax({
            type: 'GET',
            url: 'anular_cotizacion/' + id_cotizacion,
            dataType: 'JSON',
            success: function (response) {
                // console.log(response);
                if (response > 0) {
                    alert('Cotización anulada con éxito.')
                    lista_cotizaciones();
                }
            },
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR)
            console.log(textStatus)
            console.log(errorThrown)
        })
    }
}
function cargar_imagen() {
    // var e = document.querySelector('div[id="modal-ver-cotizacion"] select[name="id_empresa"]');
    // if (e.options[e.selectedIndex] != undefined) {
    //     let urlLogo = e.options[e.selectedIndex].dataset.urlLogo;
    //     let logo = urlLogo?urlLogo:'/images/img-default.jpg';
    //     $('#img').attr('src',logo );
    // }
    cargarImagenModalVerCotizacion();

    let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
    if(classModalEditarCotizacion ==  "modal fade in"){
        
        cargarImagenModalEditarCotizacion();
    }

}

function cargarImagenModalVerCotizacion(){
    var e =  document.querySelector('form[id="form-ver-cotizacion"] select[name="id_empresa"]');
    if (e.options[e.selectedIndex] != undefined) {
        let urlLogo = e.options[e.selectedIndex].dataset.urlLogo;
        let logo = urlLogo?urlLogo:'/images/img-default.jpg';
        document.querySelector('form[id="form-ver-cotizacion"] img').setAttribute('src',logo);
    }
    
}
function cargarImagenModalEditarCotizacion(){
    var e =  document.querySelector('form[id="form-editar-cotizacion"] select[name="id_empresa"]');
    if (e.options[e.selectedIndex] != undefined) {
        let urlLogo = e.options[e.selectedIndex].dataset.urlLogo;
        let logo = urlLogo?urlLogo:'/images/img-default.jpg';
        document.querySelector('form[id="form-editar-cotizacion"] img').setAttribute('src',logo);
    }
    
}

function onChangeEmpresaModalEditarCotizacion(){
    cargarImagenModalEditarCotizacion();
    var id_cotizacion =  document.querySelector('form[id="form-editar-cotizacion"] input[name="id_cotizacion"]').value;
    var id_empresa =  document.querySelector('form[id="form-editar-cotizacion"] select[name="id_empresa"]').value;
    // console.log(id_cotizacion);
    // console.log(id_empresa);
    let payload = {'id_empresa': id_empresa, 'id_cotizacion':id_cotizacion};
    $.ajax({
        type: 'PUT',
        url: 'actulizar-empresa-cotizacion',
        dataType: 'JSON',
        data: {data:payload},
        success: function(response){
            // console.log(response);
            if(response.status == 'success'){
                mostrar_cotizacion(id_cotizacion, 'editar-cotizacion');
                alert('Empresa Actualizada');
            }else{
                alert(response.message);
                return false;
            }                        
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function onChangeContactoModalEditarCotizacion(){
    
    var id_cotizacion =  document.querySelector('form[id="form-editar-cotizacion"] input[name="id_cotizacion"]').value;
    var id_contacto =  document.querySelector('form[id="form-editar-cotizacion"] select[name="id_contacto"]').value;
    var text_contacto =  document.querySelector('form[id="form-editar-cotizacion"] select[name="id_contacto"]').textContent;
    let cont_array = text_contacto.split('-')
    let email_contacto = cont_array[2]
    if(id_contacto > 0){

        let payload = {'id_contacto': id_contacto, 'id_cotizacion':id_cotizacion,'email_contacto':email_contacto};
        $.ajax({
        type: 'PUT',
        url: 'actulizar-contacto-cotizacion',
        dataType: 'JSON',
        data: {data:payload},
        success: function(response){
            // console.log(response);
            if(response.status == 'success'){
                mostrar_cotizacion(id_cotizacion, 'editar-cotizacion');
                document.querySelector('form[id="form-editar-cotizacion"] select[name="id_contacto"]').parentNode.setAttribute('class','');
                alert('Contacto Actualizada');
            }else{
                alert(response.message);
                return false;
            }                        
        }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }else{
        document.querySelector('form[id="form-editar-cotizacion"] select[name="id_contacto"]').parentNode.setAttribute('class','form-group has-danger');

    }
}

function ver_saldos(id_producto, tipo) {
    // console.log('id_producto'+id_producto+' tipo'+tipo);
    if (tipo == 1) {
        $('#modal-saldos_producto').modal({
            show: true,
        })
        // $('#des_producto').text(descripcion);
        $('#listaSaldos tbody').html('')
        listar_saldos_productos(id_producto)
    }
}
function listar_saldos_productos(id_producto) {
    var vardataTables = funcDatatables()
    var tabla = $('#listaSaldos').DataTable({
        dom: 'rt',
        // 'buttons': vardataTables[2],
        language: vardataTables[0],
        destroy: true,
        ajax: {
            url: 'saldo_por_producto/' + id_producto,
            dataSrc: '',
        },
        columns: [
            { data: 'id_prod_ubi' },
            // {'data': 'codigo'},
            // {'data': 'descripcion'},
            { data: 'des_almacen' },
            { data: 'cod_posicion' },
            { data: 'stock' },
            // {'defaultContent':
            // '<button type="button" class="saldos btn btn-primary boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Separar" >'+
            //     '<i class="fas fa-search-plus"></i></button>'}
        ],
        columnDefs: [{ aTargets: [0], sClass: 'invisible' }],
    })
    botones('#listaSaldos tbody', tabla)
}
function botones(tbody, tabla) {
    // console.log("saldos");
    $(tbody).on('click', 'button.saldos', function () {
        var data = tabla.row($(this).parents('tr')).data()
        // console.log(data);
    })
}

// function guardarStockComprometido() {
//     let inputs = document.getElementsByClassName('stock_comprometido')
//     let ComprometerStock = [].map.call(inputs, function (input) {
//         return {
//             id_requerimiento: input.dataset.idReq,
//             id_detalle_requerimiento: input.dataset.idDetReq,
//             stock_comprometido: input.value,
//         }
//     })
//     let selects = document.getElementsByClassName('almacen_selected')
//     let almacenSelect = [].map.call(selects, function (select) {
//         return {
//             id_detalle_requerimiento: select.dataset.idDetReq,
//             id_almacen: select.value,
//         }
//     })
//     let payload = {
//         comprometer_stock: ComprometerStock,
//         almacen: almacenSelect,
//     }


//     baseUrl = '/logistica/actualizar_stock_comprometido'
//     $.ajax({
//         type: 'PUT',
//         url: baseUrl,
//         data: payload,
//         dataType: 'JSON',
//         success: function (response) {
//             // console.log(response);

//             let status_stock = response.status_stock
//             let status_almacen = response.status_almacen
//             if (status_stock == 'success' && status_almacen == 'success') {
//                 alert('Stocks comprometidos y Almacen Actualizados')
//             } else {
//                 alert('Error al actualizar!!!!')
//             }
//         },
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR)
//         console.log(textStatus)
//         console.log(errorThrown)
//     })
// }

function listar_requerimientos(id_empresa = null,id_sede = null,loadTo=null) {

    let nameTableLitaRequerimientoPendientes= '#listaRequerimientoPendientes';
    if(loadTo != null){
        nameTableLitaRequerimientoPendientes= loadTo;
        // nameTableLitaRequerimientoPendientes='#listaRequerimientoPendientesAgregar';
    }
    

    var vardataTables = funcDatatables()
    $(nameTableLitaRequerimientoPendientes).dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        bDestroy: true,
        order: [[8, 'desc']],
        language: vardataTables[0],
        ajax: 'requerimientos_entrante_a_cotizacion/' + id_empresa+'/'+id_sede,
        columns: [
            { data: 'id_requerimiento' },
            {
                render: function (data, type, row) {
                    let checkbox =
                        '<input type="checkbox" data-id-requerimiento="' +
                        row.id_requerimiento +
                        '" />'
                    return checkbox
                },
            },
            { data: 'codigo' },
            { data: 'concepto' },
            { data: 'des_area' },
            {
                render: function (data, type, row) {
                    let estadoReq = ''
                    if (row.estado_doc == 'Elaborado') {
                        estadoReq =
                            '<span class="label label-default" title="Estado" >' +
                            row.estado_doc +
                            '</span>'
                    }
                    if (row.estado_doc == 'Aprobado') {
                        estadoReq =
                            '<span class="label label-primary" title="Estado" >' +
                            row.estado_doc +
                            '</span>'
                    }
                    if (row.estado_doc == 'Observado') {
                        estadoReq =
                            '<span class="label label-warning" title="Estado" >' +
                            row.estado_doc +
                            '</span>'
                    }
                    if (
                        row.estado_doc == 'Denegado' ||
                        row.estado_doc == 'Anulado'
                    ) {
                        estadoReq =
                            '<span class="label label-danger" title="Estado" >' +
                            row.estado_doc +
                            '</span>'
                    }
                    return '<center>' + estadoReq + '</center>'
                },
            },
            {
                render: function (data, type, row) {
                    if (row.has_cotizacion == true) {
                        hasCoti =
                            '<span class="label label-success" title="Tiene Cotización" >C</span>'
                    } else {
                        hasCoti = ''
                    }
                    return '<center>' + hasCoti + '</center>'
                },
            },
            { data: 'fecha_requerimiento' },
            {
                render: function (data, type, row) {
                    return (
                        '<button class="btn btn-info btn-sm"  data-id-requerimiento="' +
                        row.id_requerimiento +
                        '" data-id-det-req="' +
                        row.id_detalle_requerimiento +
                        '" onclick="verDetReqACotizar(event);" > <i class="fas fa-eye"></i></botton>'
                    )
                },
            },
        ],
        columnDefs: [{ aTargets: [0], sClass: 'invisible' }],
    })

    let tablelistaitem = document.getElementById(
        'listaRequerimientoPendientes_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true
}

function verDetReqACotizar(event) {
    event.preventDefault();
    // console.log(event.currentTarget);
    $('#modal-detalle-requerimiento-a-cotizar').modal({
        show: true,
    })

    let idReq = event.currentTarget.dataset.idRequerimiento
    let arr = [{ id_req: idReq, stateCheck: true }]
    // let arr = { 'detalle_requerimiento':idDetReq.split(",").map(Number)};

    mostrar_detalle_requerimiento(arr, 'listaDetReqACotizar')
}

function listarRequerimiento(id_empresa) {
    clearDataTable()
    listar_requerimientos(id_empresa,null,null)
    document
        .getElementById('menu_tab_crear_coti')
        .childNodes[3].children[0].setAttribute('data-toggle', 'notab')
    document
        .getElementById('menu_tab_crear_coti')
        .childNodes[5].children[0].setAttribute('data-toggle', 'notab')
    // document
    //     .getElementById('menu_tab_crear_coti')
    //     .childNodes[7].children[0].setAttribute('data-toggle', 'notab')
}

// function selectRequerimiento(){
//     changeStateButton('historial');

//     var myId = $('.modal-footer #id_requerimiento').text();

//     var arrIdDetReqList = {'detalle_requerimiento':($('.modal-footer #id_det_req_list').text()).split(",").map(Number)};
//     var page = $('.page-main').attr('type');
//     var form = $('.page-main form[type=register]').attr('id');
//     // console.log(arrIdDetReqList);

//     if (page == "cotizacion"){
//         // console.log('requerimiento'+myId);
//         mostrar_detalle_requerimiento(myId,arrIdDetReqList,'VIEW_CHECKBOX');
//     }

//     $('#modal-requerimiento').modal('hide');
// }

function vista_extendida() {
    let body = document.getElementsByTagName('body')[0]
    body.classList.add('sidebar-collapse')
}

function lista_cotizaciones() {
    var vardataTables = funcDatatables()
    var payload = ''
    $('#listaCotizacionesPorGrupo').dataTable({
        order: [[13, 'desc']],
        dom: vardataTables[1],
        buttons: vardataTables[2],
        language: vardataTables[0],
        destroy: true,
        ajax: 'listaCotizacionesPorGrupo/null',
        columns: [
            { data: 'id_grupo_cotizacion' },
            { data: 'id_cotizacion' },
            {
                render: function (data, type, row) {
                    var id_req = ''
                    for (i = 0; i < row['requerimiento'].length; i++) {
                        if (id_req !== '') {
                            id_req +=
                                ', ' + row['requerimiento'][0].id_requerimiento
                        } else {
                            id_req += row['requerimiento'][0].id_requerimiento
                        }
                    }
                    return id_req
                },
            },
            { data: 'id_proveedor' },
            { data: 'id_contribuyente' },
            { data: 'codigo_grupo' },
            { data: 'nro_documento' },
            { data: 'razon_social' },
            { data: 'email_contacto' },
            { data: 'codigo_cotizacion' },
            {
                render: function (data, type, row) {
                    payload = JSON.stringify(row)

                    // console.log(payload);
                    var req = ''
                    for (i = 0; i < row['requerimiento'].length; i++) {
                        if (req !== '') {
                            req +=
                                ', ' +
                                row['requerimiento'][i].codigo_requerimiento
                        } else {
                            req += row['requerimiento'][0].codigo_requerimiento
                        }
                    }
                    return req
                },
            },
            {
                render: function (data, type, row) {
                  let status = '<span class="label label-default">'+row.descripcion_estado+'</span>';
                    if(row.estado_envio >0){
                        status='<span class="label label-info">'+row.descripcion_estado_envio+'</span>';
                    }
                    return status;
                },
            },
            { data: 'razon_social_empresa' },
            { data: 'fecha_registro' },

            {
                render: function (data, type, row) {
                    let statusBtn ='';
                    if(row.estado_envio ==17){
                        statusBtn = 'disabled';
                    }
                    htmlAction = '<center>' +
                    '<div class="btn-group" role="group" style="margin-bottom: 5px; width:200px;">' +
                    '<button type="button" class="btn btn-sm btn-default" title="Ver detalle rápido" onclick="verSolicitudCotizacion('+row.id_cotizacion+');"><i class="fas fa-eye fa-xs"></i></button> ' +
                    '<button type="button" class="btn btn-sm btn-log bg-primary" title="Editar" onclick="editarCotizacion('+row.id_cotizacion+');" '+statusBtn+'><i class="fas fa-edit fa-xs"></i></button>' +
                    '<button type="button" class="btn btn-warning btn-sm" title="Duplicar Cotización" onclick="modalDuplicarSolicitudCotizacion('+row.id_cotizacion+');"><i class="fas fa-clone fa-xs"></i></button> ' +
                    '<button type="button" class="btn btn-success btn-sm" title="Formato de Solicitud de Cotizacion" onclick="downloadSolicitudCotizacion(' + row.id_cotizacion +');"><i class="fas fa-file-excel"></i></button>' +
                    "<button type='button' class='btn btn-sm btn-log btn-info' title='Enviar Cotizacion' onclick='envioCotizacionModal(" + payload + ",null);'><i class='fas fa-envelope fa-xs'></i></button>" +
                    '<button type="button" class="btn btn-sm btn-log bg-maroon" title="Anular" onclick="anular_cotizacion('+row.id_cotizacion+');" '+statusBtn+'><i class="fas fa-trash fa-xs"></i></button> ' +
                    '</div>' +
                    '</center>';
                    return (
                        htmlAction
                    )
                },
            },
        ],
        columnDefs: [{ aTargets: [0, 1, 2, 3, 4], sClass: 'invisible' }],
    })
}

function verSolicitudCotizacion(id_cotizacion){
    $('#modal-ver-cotizacion').modal({
        show: true,
    })
    var delayInMilliseconds = 2000; //1 second
    setTimeout(function() {
        cargarImagenModalVerCotizacion();
  //your code to be executed after 1 second
    }, delayInMilliseconds);

    getAttachFileStatusModalVerCoti(id_cotizacion);
    mostrar_cotizacion(id_cotizacion,'ver-cotizacion');
    getItemsRequerimientoModalVerCoti(id_cotizacion);
}


function editarCotizacion(id_cotizacion){
    
    $('#modal-editar-cotizacion').modal({
        show: true,
    })
    

    var delayInMilliseconds = 2000; //1 second
    setTimeout(function() {
        cargarImagenModalEditarCotizacion();
  //your code to be executed after 1 second
    }, delayInMilliseconds);

    getAttachFileStatusModalEditarCoti(id_cotizacion);
    mostrar_cotizacion(id_cotizacion, 'editar-cotizacion');
    getItemsRequerimientoModalEditarCoti(id_cotizacion);
}

function modalDuplicarSolicitudCotizacion(id_cotizacion){
    $('#modal-duplicar-cotizacion').modal({
        show: true,
    });

    document.querySelector('form[id="form-duplicar-cotizacion"] input[name="id_cotizacion"]').value = id_cotizacion;

    $.ajax({
        type: 'GET',
        url: 'mostrar_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            document.querySelector('form[id="form-duplicar-cotizacion"] select[name="id_empresa"]').value = response['cotizacion'].id_empresa;

        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

function duplicarCotizacion(){
    let id_cotizacion = document.querySelector('form[id="form-duplicar-cotizacion"] input[name="id_cotizacion"]').value;
    let id_empresa = document.querySelector('form[id="form-duplicar-cotizacion"] select[name="id_empresa"]').value;
    let id_proveedor = document.querySelector('form[id="form-duplicar-cotizacion"] input[name="id_proveedor"]').value;
    // let razon_social = document.querySelector('form[id="form-duplicar-cotizacion"] input[name="razon_social"]').value;
    
    let id_contacto = document.querySelector('form[id="form-duplicar-cotizacion"] select[name="id_contacto"]').value;
    let selectionContacto = document.querySelector('form[id="form-duplicar-cotizacion"] select[name="id_contacto"]').options.selectedIndex;
    let contactoText = document.querySelector('form[id="form-duplicar-cotizacion"] select[name="id_contacto"]').options[selectionContacto].textContent;
    let cont_array = contactoText.split('-');
    let email_contacto = cont_array[2].trim();

    var data =
    'id_proveedor=' +
    id_proveedor +
    '&id_cotizacion=' +
    id_cotizacion +
    '&id_empresa=' +
    id_empresa +
    '&id_contacto=' +
    id_contacto +
    '&email_proveedor=' +
    email_contacto;
    // console.log(data);
    if (id_proveedor !== '') {
        if (id_empresa !== null) {
            if (id_contacto !== null) {
                $.ajax({
                    type: 'POST',
                    url: 'duplicate_cotizacion',
                    data: data,
                    dataType: 'JSON',
                    success: function (response) {
                        // console.log(response);
                        if (response.status == 'success') {
                            lista_cotizaciones();
                            alert('Se creo una nueva cotización');
                            $('#modal-duplicar-cotizacion').modal('hide')
                        } else {
                            alert(response.message)
                        }
                    },
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR)
                    console.log(textStatus)
                    console.log(errorThrown)
                })
            } else {
                alert('Es necesario que seleccione un email-proveedor')
            }
        } else {
            alert('Es necesario que seleccione una empresa')
        }
    } else {
        alert('Es necesario que seleccione un proveedor')
    }
}

function getItemsRequerimientoModalVerCoti(id_cotizacion){
    $.ajax({
        type: 'GET',
        url: 'get_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            llenarTablaItemsModalVerCoti(response[0].items);
            },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}
function getItemsRequerimientoModalEditarCoti(id_cotizacion){
    $.ajax({
        type: 'GET',
        url: 'get_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            llenarTablaItemsModalEditarCoti(response[0].items);
            },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

function llenarTablaItemsModalVerCoti(data){
    limpiarTabla('listaItemsRequerimientoModalVerCoti');
    htmls = '<tr></tr>';
    var table = document.getElementById('listaItemsRequerimientoModalVerCoti').getElementsByTagName('tbody')[0];
    var newRow = table.insertRow(table.rows.length);
    newRow.innerHTML = htmls;
    // console.log(data);
    
    if( data.length >0){
        for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(a + 1);
    
            var tdIdDetReq = row.insertCell(0)
            tdIdDetReq.setAttribute('class', 'hidden')
            tdIdDetReq.innerHTML = data[a].id_cotizacion
            row.insertCell(1).innerHTML = a + 1;
            row.insertCell(2).innerHTML = data[a].codigo_requerimiento;
            row.insertCell(3).innerHTML = data[a].codigo;
            row.insertCell(4).innerHTML = data[a].descripcion;
            row.insertCell(5).innerHTML = data[a].unidad_medida_descripcion;
            row.insertCell(6).innerHTML = data[a].cantidad;
            row.insertCell(7).innerHTML = data[a].cantidad_cotizada;
            row.insertCell(8).innerHTML = data[a].precio_referencial;
            row.insertCell(9).innerHTML = data[a].fecha_entrega;
            row.insertCell(10).innerHTML = data[a].lugar_entrega;
        }
    }
}
function llenarTablaItemsModalEditarCoti(data){
    limpiarTabla('listaItemsRequerimientoModalEditCoti');
    htmls = '<tr></tr>';
    var table = document.getElementById('listaItemsRequerimientoModalEditCoti').getElementsByTagName('tbody')[0];
    var newRow = table.insertRow(table.rows.length);
    newRow.innerHTML = htmls;
    // console.log(data);
    
    if( data.length >0){
        for (var a = 0; a < data.length; a++) {
            var row = table.insertRow(a + 1);
    
            var tdIdDetReq = row.insertCell(0)
            tdIdDetReq.setAttribute('class', 'hidden')
            tdIdDetReq.innerHTML = data[a].id_cotizacion
            row.insertCell(1).innerHTML = a + 1;
            row.insertCell(2).innerHTML ='<input type="checkbox" data-id-requerimiento="' + data[a].id_requerimiento + '" data-id-detalle-requerimiento="' + data[a].id_detalle_requerimiento + '"/>';
            row.insertCell(3).innerHTML = data[a].codigo_requerimiento;
            row.insertCell(4).innerHTML = data[a].codigo;
            row.insertCell(5).innerHTML = data[a].descripcion;
            row.insertCell(6).innerHTML = data[a].unidad_medida_descripcion;
            row.insertCell(7).innerHTML = data[a].cantidad;
            row.insertCell(8).innerHTML = data[a].cantidad_cotizada;
            row.insertCell(9).innerHTML = data[a].precio_referencial;
            row.insertCell(10).innerHTML = data[a].fecha_entrega;
            row.insertCell(11).innerHTML = data[a].lugar_entrega;
            row.insertCell(12).innerHTML ='<button type="button" class="btn btn-primary btn-sm" title="Agregar Adjunto al Ítem" onClick="agregarAdjuntosItemReq(' + data[a].id_requerimiento + ',' + data[a].id_detalle_requerimiento + ');"> <i class="fas fa-upload"></i></button>';
        }
    }
}

function envioCotizacionModal(data, id_cotizacion_creada) {
    let id_cotizacionCurrent = 0;
    if (id_cotizacion_creada > 0) {
        id_cotizacionCurrent = id_cotizacion_creada;
    } else {
        id_cotizacionCurrent = data.id_cotizacion;
    }
    // console.log(data);
    $('#modal-envio-cotizacion').modal({
        show: true,
    })
    document.getElementById('btnEnviarCotizacion').removeAttribute('disabled');

    document.getElementById('form-envio_cotizacion').reset();

    fill_form_envio_cotizacion(data);
    getAttachFileStatusNewCotiza(id_cotizacionCurrent);
    statusInputFormEnviarCoti('DISABLED', false);
    document.getElementById('estado_email').setAttribute('class', 'label label-info ivisible');
    document.getElementById('estado_email').textContent = '';

}

function fill_form_envio_cotizacion(data) {
    // console.log(data);

    document.getElementById('title-envio-cotizacion').textContent =
        'Enviar Cotización ' + data.codigo_cotizacion

    document.getElementById('id_cotizacion').value = data.id_cotizacion
    document.getElementById('email_destinatario').value = data.email_contacto
    document.getElementById('email_remitente').value = data.id_empresa
    document.getElementById('email_asunto').value =
        'Solicitud de Cotización ' +
        data.codigo_cotizacion +
        ' - ' +
        data.razon_social_empresa
    document.getElementById('email_contenido').value =
        'Señor ' +
        data.nombre_contacto +
        ' de la empresa ' +
        data.razon_social +
        ', de nuestra consideración tengo el agrado de dirigirme a usted, para saludarle cordialmente en nombre de ' +
        data.razon_social_empresa +
        ' y le solicitamos cotizar los siguientes ítems de acuerdo a los términos que se adjuntan. \n\nAtte: RICHARD BALTAZAR DORADO BACA - Jefe de Logística'
}

function getAttachFileStatus(id_cotizacion){
    $.ajax({
        type: 'GET',
        url: 'estado_archivos_adjuntos_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            if (response.status <= 0) {
                alert('error al obtener los archivos adjuntos')
            }
            adjuntoList = response.data
            // console.log(adjuntoList)
           
            
 
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

function getAttachFileStatusNewCotiza(id_cotizacion) {
    // console.log(id_cotizacion);
    
    textAdjuntos=[];
    $.ajax({
        type: 'GET',
        url: 'estado_archivos_adjuntos_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            if (response.status <= 0) {
                alert('error al obtener los archivos adjuntos')
            }
            adjuntoList = response.data
            // console.log(adjuntoList)
            adjuntoList.forEach(function(element,index) {
                let partOfAdjunto = element.ruta.split('/')
                let nameFile = partOfAdjunto[partOfAdjunto.length - 1]
                // let typeFile = partOfAdjunto[2].toUpperCase();
                textAdjuntos += `<div><a href="${
                    element.ruta
                    }" target="_blank" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> ${nameFile} </a> <a type="button" onClick="quitarAdjunto( event, ${
                    index
                    } )" style="color: indianred; margin-left: 5px;  cursor:pointer;"><i class="fas fa-times fa-sm"></i></a> <br></div>`
            })
            document.getElementById('attachment-container-nueva-cotiza').innerHTML = textAdjuntos;
            
            let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
            if(classModalEditarCotizacion ==  "modal fade in"){
                document.getElementById('attachment-container-editar-cotiza').innerHTML = textAdjuntos;
            
            }
            
 
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })


}

function getAttachFileStatusModalVerCoti(id_cotizacion){
    textAdjuntos=[];

    $.ajax({
        type: 'GET',
        url: 'estado_archivos_adjuntos_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            if (response.status <= 0) {
                alert('error al obtener los archivos adjuntos')
            }
            adjuntoList = response.data
            // console.log(adjuntoList)
            adjuntoList.forEach(function(element,index)  {
                let partOfAdjunto = element.ruta.split('/')
                let nameFile = partOfAdjunto[partOfAdjunto.length - 1]
                textAdjuntos += `<div><a href="${
                    element.ruta
                    }" target="_blank" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> ${nameFile} </a> <br></div>`
            });
            document.getElementById('attachment-container-ver-cotiza').innerHTML = textAdjuntos;
            
 
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
    

}
function getAttachFileStatusModalEditarCoti(id_cotizacion){
    textAdjuntos=[];

    $.ajax({
        type: 'GET',
        url: 'estado_archivos_adjuntos_cotizacion/' + id_cotizacion,
        dataType: 'JSON',
        success: function (response) {
            if (response.status <= 0) {
                alert('error al obtener los archivos adjuntos')
            }
            adjuntoList = response.data
            // console.log(adjuntoList)
            adjuntoList.forEach(function(element,index)  {
                let partOfAdjunto = element.ruta.split('/')
                let nameFile = partOfAdjunto[partOfAdjunto.length - 1]
                textAdjuntos += `<div><a href="${
                    element.ruta
                    }" target="_blank" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> ${nameFile} </a> <a type="button" onClick="quitarAdjunto( event, ${
                    index
                    } )" style="color: indianred; margin-left: 5px;  cursor:pointer;"><i class="fas fa-times fa-sm"></i></a> <br></div>`
            });
            document.getElementById('attachment-container-editar-cotiza').innerHTML = textAdjuntos;
            
 
        },
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
    

}


$(document).on('submit', '.formEnviarCoti', function (e) {
    e.preventDefault();
    const nombreform = $(this).attr('id');

    if (nombreform == 'form-envio_cotizacion') {
        var miurl = 'enviar_correo';

        let id_cotizacion = document.getElementById('id_cotizacion').value;
        let destinatario = document.getElementById('email_destinatario').value;
        let selectionEmailRemitente = document.getElementById('email_remitente')
            .options.selectedIndex;
        let remitente = document.getElementById('email_remitente').options[
            selectionEmailRemitente
        ].textContent;
        let asunto = document.getElementById('email_asunto').value;
        let contenido_mail = document.getElementById('email_contenido').value;
        let filesArray = adjuntoList;
        // let file = document.getElementById('file').value
        // console.log('file')
        // console.log(file)

        let myformData = new FormData();
        myformData.append('id_cotizacion', id_cotizacion);
        myformData.append('remitente', remitente);
        myformData.append('destinatario', destinatario);
        myformData.append('asunto', asunto);
        myformData.append('contenido_mail', contenido_mail);
        // myformData.append('file', $('#file')[0].file[0])
        myformData.append('adjunto_server', JSON.stringify(filesArray));

        // console.log(...myformData);

        $.ajax({
            url: miurl,
            type: 'POST',
            // Form data
            // datos del formulario
            data: myformData,
            // necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            // mientras enviamos el archivo
            beforeSend() {
                $('.loading').removeClass('invisible');
            },
            // una vez finalizado correctamente
            success(data) {
                // console.log('succces=>');
                console.log(data)
                document.getElementById('btnEnviarCotizacion').setAttribute('disabled', true);
                lista_cotizaciones();

                $('.loading').addClass('invisible');
                $('#estado_email').html(data);

            },
            // si ha ocurrido un error
            error(data) {
                alert('ha ocurrido un error');
            },
        })
    }
})

function quitarAdjunto(event, i) {
    // console.log(id);
    adjuntoList.map((item, index) => {
        if (index == i){
            item.active = false
            event.target.parentNode.parentNode.remove()
        }
    })
    // console.log(adjuntoList);
    // verificar si es un archivo subido o archivos del servidro
    // si es archivos del servidor

    // $.ajax({
    //     type: 'GET',
    //     url: '/descartar_archivo_adjunto/'+id+'/'+typeDoc,
    //     dataType: 'JSON',
    //     success: function(response){
    //         // if(response.status<=0){
    //         console.log(response);
    //     }
    // }).fail( function( jqXHR, textStatus, errorThrown ){
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // });
}

function agregarAdjuntoCotizacion(event) {
    //  console.log(event.target.value);
    let fileList = event.target.files
    let file = fileList[0];
    let idCoti = 0;
    
    let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
    if(classModalEditarCotizacion ==  "modal fade in"){     
        idCoti = parseInt(document.getElementsByName('id_cotizacion')[0].value);
    }else{
        idCoti = parseInt(document.getElementById('id_cotizacion').value);

    }
    

    if (((Number.isInteger(idCoti) == false) && parseInt(idCoti) <= 0) || isNaN(idCoti)==true ) {
            alert('ERROR, EL ID DE LA COTIZACIÓN NO PUEDE SER CERO O MENOR A CERO');
            return false;
        
    }

    let extension = file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase() // assuming that this file has any extension
    //  console.log(extension);
    if (
        extension === 'dwg' ||
        extension === 'dwt' ||
        extension === 'cdr' ||
        extension === 'back' ||
        extension === 'backup' ||
        extension === 'psd' ||
        extension === 'sql' ||
        extension === 'exe' ||
        extension === 'html' ||
        extension === 'js' ||
        extension === 'php' ||
        extension === 'ai' ||
        extension === 'mp4' ||
        extension === 'mp3' ||
        extension === 'avi' ||
        extension === 'mkv' ||
        extension === 'flv' ||
        extension === 'mov' ||
        extension === 'wmv'
    ) {
        alert(
            'Extensión de archivo incorrecta (NO se permite .' +
            extension +
            ').  La entrada del archivo se borra.'
        )
        event.target.value = ''
    } else {
        let archivo = {
            id: 0,
            id_cotizacion: idCoti,
            archivo: file.name,
            fecha_registro: new Date().toJSON().slice(0, 10),
            active: true,
            // file:event.target.files[0]
        }
        let only_file = event.target.files[0]
        adjuntos_info_coti.push(archivo);
        only_adjuntos_coti.push(only_file);
        document.getElementById('btnUploadFileCoti').setAttribute('class', 'btn btn-info')
        document.querySelector('.group-span-filestyle.input-group-btn').setAttribute('class', 'group-span-filestyle input-group-btn hidden')

        let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
        if(classModalEditarCotizacion ==  "modal fade in"){
            document.getElementById('btnUploadFileCotiEditar').setAttribute('class', 'btn btn-info')
            document.querySelector('.group-span-filestyle.input-group-btn').setAttribute('class', 'group-span-filestyle input-group-btn hidden')
    
        }

        // console.log("agregar adjunto");
        // console.log(adjuntos);
        // console.log(only_adjuntos_coti);
        //    imprimir_tabla_adjuntos();
        // console.log(adjuntos_info_coti);

    }
}

function guardarAdjuntoCoti() {

    // console.log(obs);
    let id_req = $('[name=id_requerimiento]').val();
    if (id_req < 0) {
        alert("error 790: GuardarAdjunto");
    }

    // console.log(adjuntos_info_coti);
    // console.log(only_adjuntos_coti);

    const onlyNewAdjuntos = adjuntos_info_coti.filter(adj => adj.active == true); // solo enviar los registros nuevos

    var myformData = new FormData();
    for (let i = 0; i < only_adjuntos_coti.length; i++) {
        myformData.append('only_adjuntos_coti[]', only_adjuntos_coti[i]);

    }

    myformData.append('info_adjuntos', JSON.stringify(onlyNewAdjuntos));

    baseUrl = 'guardar-archivos-adjuntos-cotizacion';
    // console.log(...myformData)

    $.ajax({
        type: 'POST',
        processData: false,
        contentType: false,
        cache: false,
        data: myformData,
        enctype: 'multipart/form-data',
        // dataType: 'JSON',
        url: baseUrl,
        success: function (response) {
            // console.log(response);     
            if (response > 0) {

                only_adjuntos_coti = [];


                let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
                if(classModalEditarCotizacion ==  "modal fade in"){
                    document.getElementById('btnUploadFileCotiEditar').setAttribute('class', 'btn btn-info hidden')
                    document.querySelector('.group-span-filestyle.input-group-btn').setAttribute('class', 'group-span-filestyle input-group-btn')
                    document.getElementById('attachment-container-editar-cotiza').innerHTML = '';
                    getAttachFileStatusNewCotiza(parseInt(document.getElementsByName('id_cotizacion')[0].value));
    
                }else{
                    document.getElementById('btnUploadFileCoti').setAttribute('class', 'btn btn-info hidden')
                    document.querySelector('.group-span-filestyle.input-group-btn').setAttribute('class', 'group-span-filestyle input-group-btn')
    
                    document.getElementById('attachment-container-nueva-cotiza').innerHTML = '';
                    getAttachFileStatusNewCotiza(parseInt(document.getElementById('id_cotizacion').value));
                    // statusInputFormEnviarCoti('DISABLED', true);
    
                }


                alert("Archivo(s) Guardado(s)");
            } else {
                alert("NO SE PUDO GUARDAR EL ARCHIVO");
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function statusInputFormEnviarCoti(option, status) {
    switch (option) {
        case 'DISABLED':
            if (status == true) {
                document.getElementById('email_destinatario').setAttribute('disabled', status);
                document.getElementById('email_remitente').setAttribute('disabled', status);
                document.getElementById('email_contenido').setAttribute('disabled', status);
                document.getElementById('email_asunto').setAttribute('disabled', status);
            } else if (status == false) {
                document.getElementById('email_destinatario').removeAttribute('disabled');
                document.getElementById('email_remitente').removeAttribute('disabled');
                document.getElementById('email_contenido').removeAttribute('disabled');
                document.getElementById('email_asunto').removeAttribute('disabled');

            }

            break;

        default:
            break;

    }
}


function agregarAdjuntosItemReq(id_requerimiento,id_detalle_requerimiento){

    if (((Number.isInteger(id_detalle_requerimiento) == false) && parseInt(id_detalle_requerimiento) <= 0) || isNaN(id_detalle_requerimiento)==true ) {
        alert('ERROR, EL ID DE LA COTIZACIÓN NO PUEDE SER CERO O MENOR A CERO');
        return false;

    }else{
        
        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'static'
        });

        document.querySelector('div[id="modal-adjuntar-archivos-detalle-requerimiento"] div[class="modal-footer"] label[id="id_requerimiento"]').textContent =id_requerimiento;
        document.querySelector('div[id="modal-adjuntar-archivos-detalle-requerimiento"] div[class="modal-footer"] label[id="id_detalle_requerimiento"]').textContent =id_detalle_requerimiento;
        get_data_archivos_adjuntos(id_detalle_requerimiento);

    }

}


function get_data_archivos_adjuntos(index){
    adjuntos=[];
    limpiarTabla('listaArchivos');
    baseUrl = 'logistica/mostrar-archivos-adjuntos/'+index;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if(response.length >0){

                for (x=0; x<response.length; x++){
                    id_detalle_requerimiento= response[x].id_detalle_requerimiento;
                        adjuntos.push({ 
                            'id_adjunto':response[x].id_adjunto,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
            llenar_tabla_archivos_adjuntos(adjuntos);
            
            }else{
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData =  row.insertCell(0);
                tdSinData.setAttribute('colspan','5');
                tdSinData.setAttribute('class','text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}


function llenar_tabla_archivos_adjuntos(adjuntos){
    // console.log(adjuntos);
    
    limpiarTabla('listaArchivos');
    htmls ='<tr></tr>';
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    for(var a=0;a < adjuntos.length;a++){
        var row = table.insertRow(a+1);
        var tdIdArchivo =  row.insertCell(0);
            tdIdArchivo.setAttribute('class','hidden');
            tdIdArchivo.innerHTML = adjuntos[a].id_adjunto?adjuntos[a].id_adjunto:'0';
        var tdIdDetalleReq =  row.insertCell(1);
            tdIdDetalleReq.setAttribute('class','hidden');
            tdIdDetalleReq.innerHTML = adjuntos[a].id_detalle_requerimiento?adjuntos[a].id_detalle_requerimiento:'0';
        row.insertCell(2).innerHTML = a+1;
        row.insertCell(3).innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '<button'+
        '    class="btn btn-danger btn-sm "'+
        '    name="btnEliminarArchivoAdjunto"'+
        '    onclick="eliminarArchivoAdjunto('+a+','+adjuntos[a].id_adjunto+','+adjuntos[a].id_detalle_requerimiento+')"'+
        '    title="Eliminar Archivo"'+
        '>'+
        '    <i class="fas fa-trash"></i>'+
        '</button>'+
        '</div>';

    }
    return null;
}

let only_adjuntos=[];
function agregarAdjunto(event){ //agregando nuevo archivo adjunto
   
    //  console.log(event.target.value);
     let fileList = event.target.files;
     let file = fileList[0];

     let extension = file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase(); // assuming that this file has any extension
    //  console.log(extension);
    if (extension === 'dwg' 
        || extension === 'dwt' 
        || extension === 'cdr' 
        || extension === 'back' 
        || extension === 'backup' 
        || extension === 'psd' 
        || extension === 'sql' 
        || extension === 'exe' 
        || extension === 'html' 
        || extension === 'js' 
        || extension === 'php' 
        || extension === 'ai' 
        || extension === 'mp4' 
        || extension === 'mp3' 
        || extension === 'avi' 
        || extension === 'mkv' 
        || extension === 'flv' 
        || extension === 'mov' 
        || extension === 'wmv' 
        ) {
            alert('Extensión de archivo incorrecta (NO se permite .'+extension+').  La entrada del archivo se borra.');
            event.target.value = '';
        }
        else {


            let archivo ={
                id_adjunto: 0,
                id_detalle_requerimiento: document.querySelector('div[id="modal-adjuntar-archivos-detalle-requerimiento"] div[class="modal-footer"] label[id="id_detalle_requerimiento"]').textContent,
                archivo:file.name,
                fecha_registro: new Date().toJSON().slice(0, 10),
                estado: 1
                // file:event.target.files[0]
            }
            let only_file = event.target.files[0]
            adjuntos.push(archivo);
            only_adjuntos.push(only_file);
            // console.log("agregar adjunto");
            // console.log(adjuntos);
            // console.log(only_adjuntos);
            imprimir_tabla_adjuntos();
            
    }
}


function imprimir_tabla_adjuntos(){
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    var indicadorTd='';
    for(var a=0;a < adjuntos.length;a++){
        var row = table.insertRow(-1);

        if(adjuntos[a].id_adjunto ==0){
            indicadorTd="green"; // si es nuevo
        }
        var tdIdArchivo =  row.insertCell(0);
        tdIdArchivo.setAttribute('class','hidden');
        tdIdArchivo.innerHTML = adjuntos[a].id_adjunto?adjuntos[a].id_adjunto:'0';
        var tdIdDetalleReq =  row.insertCell(1);
        tdIdDetalleReq.setAttribute('class','hidden');
        tdIdDetalleReq.innerHTML = 0;
        var tdNumItem = row.insertCell(2);
        tdNumItem.innerHTML = a+1;
        var tdNameFile = row.insertCell(3);
        tdNameFile.innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        tdNameFile.setAttribute('class',indicadorTd);
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '<button'+
        '    class="btn btn-danger btn-sm "'+
        '    name="btnEliminarArchivoAdjunto"'+
        '    onclick="eliminarArchivoAdjunto('+a+','+adjuntos[a].id_adjunto+')"'+
        '    title="Eliminar Archivo"'+
        '>'+
        '    <i class="fas fa-trash"></i>'+
        '</button>'+
        '</div>';
    }
}

function guardarAdjuntos(){
    
    // console.log(obs);
    let id_req = document.querySelector('div[id="modal-adjuntar-archivos-detalle-requerimiento"] div[class="modal-footer"] label[id="id_requerimiento"]').textContent;
    if(id_req < 0){
        alert("error 790: GuardarAdjunto");
    }
    
    // console.log(adjuntos);
    // console.log(only_adjuntos);
    let id_detalle_requerimiento = adjuntos[0].id_detalle_requerimiento;

    const onlyNewAdjuntos = adjuntos.filter(id => id.id_adjunto == 0); // solo enviar los registros nuevos

        var myformData = new FormData();        
        // myformData.append('archivo_adjunto', JSON.stringify(adjuntos));
        for(let i=0;i<only_adjuntos.length;i++){
            myformData.append('only_adjuntos[]', only_adjuntos[i]);
            
        }
        
        myformData.append('detalle_adjuntos', JSON.stringify(onlyNewAdjuntos));
        myformData.append('id_detalle_requerimiento', id_detalle_requerimiento);
    
        baseUrl = 'logistica/guardar-archivos-adjuntos-detalle-requerimiento';
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            // dataType: 'JSON',
            url: baseUrl,
            success: function(response){
                // console.log(response);     
                if (response > 0){
                    alert("Archivo(s) Guardado(s)");
                    only_adjuntos=[];
                    get_data_archivos_adjuntos(id_detalle_requerimiento);
                    let ask = confirm('¿Desea seguir agregando más archivos ?');
                    if (ask == true){
                        return false;
                    }else{
                        $('#modal-adjuntar-archivos-detalle-requerimiento').modal('hide');
                    }
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
}


function eliminarArchivoAdjunto(indice,id_adjunto,id_detalle_requerimiento){

    document.getElementById('nombre_archivo_coti_editar').value='';

    if(id_adjunto >0){
        var ask = confirm('¿Desea eliminar este archivo ?');
        if (ask == true){
            $.ajax({
                type: 'PUT',
                url: 'eliminar-archivo-adjunto-detalle-requerimiento/'+id_adjunto,
                dataType: 'JSON',
                success: function(response){
                    if(response.status == 'ok'){
                        alert("Archivo Eliminado");
                        get_data_archivos_adjuntos(id_detalle_requerimiento);
        
                    }else{
                        alert("No se pudo eliminar el archivo")
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            return false;
        }
    }else{
        only_adjuntos.splice(indice,1 );
        adjuntos.splice(indice,1);
        imprimir_tabla_adjuntos();

    }    

}


function agregarItemACotizacion(){
    
    let id_cotizacion= document.querySelector('form[id="form-editar-cotizacion"] input[name="id_cotizacion"]').value;
    let id_empresa= document.querySelector('form[id="form-editar-cotizacion"] select[name="id_empresa"]').value;
    
    if (((Number.isInteger(id_cotizacion) == false) && parseInt(id_cotizacion) <= 0) || isNaN(id_cotizacion)==true ) {
        alert('ERROR, EL ID DE LA COTIZACIÓN NO PUEDE SER CERO O MENOR A CERO');
        return false;

    }else{
        
        $('#modal-agregar-item-req-a-cotiza').modal({
            show: true,
            backdrop: 'static'
        });
        
        listar_requerimientos(id_empresa,null,'#listaRequerimientoPendientesAgregar');

    }
}

function allowCheckBoxListReqAgregar(event){
    event.preventDefault();
    statusCkeckBoxListReq('DISABLED',false,'listaRequerimientoPendientesAgregar');
    statusCkeckBoxListReq('CHECKED',false, 'listaRequerimientoPendientesAgregar');
    listCheckReq = [];
    listCheckReqDet = [];
}


function addAllItemReqToCoti(e){    
    e.preventDefault();
    let tablelistaItemsRequerimiento = document.getElementById('listaItemsRequerimientoAgregar');
    let tableChildren = tablelistaItemsRequerimiento.children[1].children;
    let sizeTableChildren = tableChildren.length;
    // console.log(sizeTableChildren);
    
    for(let i=0;i<sizeTableChildren;i++){
        // console.log(tableChildren[i].cells.length);
        
        if(tableChildren[i].cells.length >0){
            cantidadUpdateInItemList.push( {
                'id_det_req':tableChildren[i].cells[11].children[0].dataset.idDetReq, 
                'cantidad':tableChildren[i].cells[11].children[0].value
            });
        };
    }
    // console.log(listCheckReqDet);
    
    cantidadUpdateInItemList.map((ItemWithNewValue,i) => {
        listCheckReqDet.map((reqDet,j) => {            
            if(ItemWithNewValue.id_det_req == reqDet.id_det_req){
                if(reqDet.newCantidad >0){

                    listCheckReqDet[j].newCantidad = ItemWithNewValue.cantidad;
                }
            }
        });
        
    });

    var id_cotizacion =  document.querySelector('form[id="form-editar-cotizacion"] input[name="id_cotizacion"]').value;
    if((listCheckReqDet.length >0) && (id_cotizacion >0)){
        var ask = confirm('¿Seguro que desea agregar el item(s) a la cotización ?');
        if (ask == true){
            $.ajax({
                type: 'POST',
                url: 'agregar-item-cotizacion/'+id_cotizacion,
                dataType: 'JSON',
                data: {data:listCheckReqDet},
                success: function(response){
                    console.log(response.message);
                    
                    if(response.status == 'success'){
                        getItemsRequerimientoModalEditarCoti(id_cotizacion);
                        alert('Se inserto un nuevo item a la cotización');
                    }else{
                        alert('ERROR INESPERADO, NO SE PUDO INGRESAR CORRECTAMENTE EL ITEM A LA COTIZACIÓN');
                    }                        
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            return false;
        }
    }
}

function discardFalseStateInCheckBox(listCheckReqDet){
    let listItem =[];
    listCheckReqDet.forEach(element => {
        if(element.stateCheck == true){
            listItem.push(element);
        }
    });    
    return listItem;
}

function eliminarItemDeCotizacion(){
    let id_cotizacion = document.querySelector('form[id="form-editar-cotizacion"] input[name="id_cotizacion"]').value;
    if(id_cotizacion <= 0){
        alert('Error inesperado, el ID de la cotización es menor o igual a cero');
    }else{
        if(listCheckReqDet.length <=0){
            alert('No se seleccionó ningun item');
        }else{
            
            let payload = {data:discardFalseStateInCheckBox(listCheckReqDet)};
            var rspta = confirm('¿Está seguro que desea eliminar el item de la cotización?')
            if (rspta) {
                $.ajax({
                    type: 'POST',
                    url: 'eliminar-item-cotizacion/' + id_cotizacion,
                    dataType: 'JSON',
                    data:payload,
                    success: function (response) {
                        // console.log(response);
                        if(response.status == 'success'){
                            getItemsRequerimientoModalEditarCoti(id_cotizacion);
                            listCheckReqDet=[];
                            document.getElementById('btnEliminarItemDeCotizacion').setAttribute('disabled', true)
                            alert('se quito el item de la cotización');
                        }else{
                            alert('ERROR INESPERADO, NO SE PUDO ELIMINAR CORRECTAMENTE EL ITEM EN LA COTIZACIÓN');
                        }
                    },
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR)
                    console.log(textStatus)
                    console.log(errorThrown)
                })
            }
        }
    }

}


function nuevaSolicitudCotizacion(){
    $('#modal-nueva-solicitud-cotizacion').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });

    let id_empresa =document.getElementById('id_empresa_select_req').value;
    document.querySelector("form[id='form-nueva-solicitud-cotizacion'] select[name='id_empresa']").value = id_empresa;

    getDataSelectSede(id_empresa,"form", "form-nueva-solicitud-cotizacion","id_sede_crear_coti");

    let id_sede =document.getElementById('id_sede_select_req').value;
    if(id_sede > 0){
        document.querySelector("form[id='form-nueva-solicitud-cotizacion'] select[id='id_sede_crear_coti']").value = id_sede;
    }

}

