
var
    rutaListaItemsCuadroCostosPorRequerimiento,
    rutaBuscarItemEnCatalogo,
    rutaObtenerGrupoSelectItemParaCpmpra,
    rutaGuardarItemsEnDetalleRequerimiento
    ;

var listCheckReq = [];
// var detalleRequerimientoSelected = [];
// var selectRequirementsToLink='';
// var linksToReqObjArray=[];
// var payload_orden=[];
var tempDetalleItemsParaCompraCC = [];
var itemsParaCompraList = [];
// var data_item_para_compra =[];
var dataSelect = [];
var infoStateInput = [];
var reqTrueList = [];

function inicializarModalItemsParaCompra(
    _rutaListaItemsCuadroCostosPorRequerimiento,
    _rutaBuscarItemEnCatalogo,
    _rutaObtenerGrupoSelectItemParaCpmpra,
    _rutaGuardarItemsEnDetalleRequerimiento
) {

    rutaListaItemsCuadroCostosPorRequerimiento = _rutaListaItemsCuadroCostosPorRequerimiento;
    rutaBuscarItemEnCatalogo = _rutaBuscarItemEnCatalogo;
    rutaObtenerGrupoSelectItemParaCpmpra = _rutaObtenerGrupoSelectItemParaCpmpra;
    rutaGuardarItemsEnDetalleRequerimiento = _rutaGuardarItemsEnDetalleRequerimiento;

}



// function obtenerListaItemsCuadroCostosPorIdRequerimiento(reqTrueList) {
//     $.ajax({
//         type: 'POST',
//         url: rutaListaItemsCuadroCostosPorRequerimiento,
//         data: { 'requerimientoList': reqTrueList },
//         dataType: 'JSON',
//         success: function (response) {
//             // console.log(response);
//             if (response.status == 200) {
//                 tempDetalleItemsParaCompraCC = response.data;
//                 llenarTablaDetalleCuadroCostos(response.data);
//             }
//             // listar_detalle_orden_requerimiento(response.det_req);
//             // console.log(response.det_req); 
//         }
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function llenarTablaDetalleCuadroCostos(data) {
    var dataTableListaModalDetalleCuadroCostos = $('#ListaModalDetalleCuadroCostos').DataTable({
        'processing': false,
        'serverSide': false,
        'bDestroy': true,
        'bInfo': false,
        'dom': 'Bfrtip',
        'paging': false,
        'searching': false,
        'data': data,
        'columns': [
            {
                'render': function (data, type, row) {
                    return `${row['part_no']?row['part_no']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['descripcion']}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['pvu_oc']}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['flete_oc']}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['cantidad']}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['garantia']?row['garantia']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['razon_social_proveedor']}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['nombre_autor']}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['fecha_creacion']}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `<button class="btn btn-xs btn-default" onclick="procesarItemParaCompraDetalleCuadroCostos(${row['id']});" title="Agregar Item" style="background-color:#714fa7; color:white;"><i class="fas fa-plus"></i></button>`;
                }
            }
        ]
    });

    document.querySelector("table[id='ListaModalDetalleCuadroCostos']").tHead.style.fontSize = '11px',
        document.querySelector("table[id='ListaModalDetalleCuadroCostos']").tBodies[0].style.fontSize = '11px';
    dataTableListaModalDetalleCuadroCostos.buttons().destroy();
    document.querySelector("table[id='ListaModalDetalleCuadroCostos'] thead").style.backgroundColor = "#5d4d6d";
    $('#ListaModalDetalleCuadroCostos tr').css('cursor', 'default');


}



function buscarItemEnCatalogo(data) {

    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'POST',
            data: data,
            url: rutaBuscarItemEnCatalogo,
            dataType: 'JSON',
            success(response) {
                resolve(response) // Resolve promise and go to then() 
            },
            error: function (err) {
                reject(err) // Reject the promise and go to catch()
            }
        });
    });
}

 
function cleanCharacterReference(text){
    let str = text;
    characterReferenceList=['&nbsp;','nbsp;','&amp;','amp;',"&lt;"];
    characterReferenceList.forEach(element => {
        while (str.search(element) > -1) {
            str=  str.replace(element,"");

        }
    });
        return str;

}

function procesarItemParaCompraDetalleCuadroCostos(id) {
    let detalleItemsParaCompraCCSelected = '';
    // console.log(tempDetalleItemsParaCompraCC);
    tempDetalleItemsParaCompraCC.forEach(element => {
        if (element.id == id) {
            detalleItemsParaCompraCCSelected = element;
        }
    });
    // mostrarCatalogoItems();
    // console.log(tempDetalleItemsParaCompraCC);

    let data_item_CC_selected = {
        'id_item': "",
        'id_producto': "",
        'id_tipo_item': "1",
        'id_cc_am': detalleItemsParaCompraCCSelected.id_cc_am?detalleItemsParaCompraCCSelected.id_cc_am:null,
        'id_cc_venta': detalleItemsParaCompraCCSelected.id_cc_venta?detalleItemsParaCompraCCSelected.id_cc_venta:null,
        'part_number': detalleItemsParaCompraCCSelected.part_no,
        'descripcion': cleanCharacterReference(detalleItemsParaCompraCCSelected.descripcion),
        'alm_prod_codigo': "",
        'categoria': "",
        'clasificacion': "NUEVO",
        'codigo_item': "",
        'id_categoria': '',
        'id_clasif': 5,
        'id_subcategoria': '',
        'id_unidad_medida': 30,
        'unidad_medida': "Caja",
        'subcategoria': "",
        'id_moneda': 1,
        'cantidad': detalleItemsParaCompraCCSelected.cantidad,
        'precio': "",
        'tiene_transformacion': false

    };
    // console.log(data_item_CC_selected);

    buscarItemEnCatalogo(data_item_CC_selected).then(function (data) {
        // Run this when your request was successful
        if (data.length > 0) {
            // console.log(data)
            // console.log(data[0]);
            data[0].cantidad = data_item_CC_selected.cantidad;
            data[0].id_cc_am = data_item_CC_selected.id_cc_am;
            data[0].id_cc_venta = data_item_CC_selected.id_cc_venta;
            data[0].precio = '';
            data[0].tiene_transformacion = false;

            if (data[0].id_moneda == null) {
                data[0].id_moneda = 1;
                data[0].moneda = 'Soles';
            }
            // console.log(data[0]);

            itemsParaCompraList.push(data[0]);
            agregarItemATablaListaItemsParaCompra(itemsParaCompraList);

        } else {

            itemsParaCompraList.push(data_item_CC_selected);
            agregarItemATablaListaItemsParaCompra(itemsParaCompraList);

            alert('No se encontró el producto seleccionado en el catalogo');
        }
 
    }).catch(function (err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })

}

function getDataAllSelect() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: rutaObtenerGrupoSelectItemParaCpmpra,
            dataType: 'JSON',
            success(response) {
                resolve(response) // Resolve promise and go to then() 
            },
            error: function (err) {
                reject(err) // Reject the promise and go to catch()
            }
        });
    });
}

function agregarItemATablaListaItemsParaCompra(data) {
    // console.log(data);
    if (dataSelect.length > 0) {
        componerTdItemsParaCompra(data, dataSelect[0].categoria, dataSelect[0].subcategoria, dataSelect[0].clasificacion, dataSelect[0].moneda, dataSelect[0].unidad_medida);

    } else {
        getDataAllSelect().then(function (response) {
            if (response.length > 0) {
                dataSelect = response;

                componerTdItemsParaCompra(data, response[0].categoria, response[0].subcategoria, response[0].clasificacion, response[0].moneda, response[0].unidad_medida);

            } else {
                alert('No se pudo obtener data de select de item');
            }

        }).catch(function (err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })

    }


}

function makeSelectedToSelect(indice, type, data, id, hasDisabled) {
    let html = '';
    switch (type) {
        case 'categoria':
            html = `<select class="form-control" name="categoria" ${hasDisabled} data-indice="${indice}" onChange="updateInputCategoriaModalItemsParaCompra(event);">`;
            data.forEach(item => {
                if (item.id_categoria == id) {
                    html += `<option value="${item.id_categoria}" selected>${item.descripcion}</option>`;
                } else {
                    html += `<option value="${item.id_categoria}">${item.descripcion}</option>`;
                }
            });
            html += '</select>';
            break;
        case 'subcategoria':
            html = `<select class="form-control" name="subcategoria" ${hasDisabled} data-indice="${indice}" onChange="updateInputSubcategoriaModalItemsParaCompra(event);">`;
            data.forEach(item => {
                if (item.id_subcategoria == id) {
                    html += `<option value="${item.id_subcategoria}" selected>${item.descripcion}</option>`;
                } else {
                    html += `<option value="${item.id_subcategoria}">${item.descripcion}</option>`;
                }
            });
            html += '</select>';
            break;
        case 'clasificacion':
            html = `<select class="form-control" name="clasificacion" ${hasDisabled} data-indice="${indice}" onChange="updateInputClasificacionModalItemsParaCompra(event);">`;
            data.forEach(item => {
                if (item.id_clasificacion == id) {
                    html += `<option value="${item.id_clasificacion}" selected>${item.descripcion}</option>`;
                } else {
                    html += `<option value="${item.id_clasificacion}">${item.descripcion}</option>`;

                }
            });
            html += '</select>';
            break;
        case 'unidad_medida':
            html = `<select class="form-control" name="unidad_medida" ${hasDisabled} data-indice="${indice}" onChange="updateInputUnidadMedidaModalItemsParaCompra(event);">`;
            data.forEach(item => {
                if (item.id_unidad_medida == id) {
                    html += `<option value="${item.id_unidad_medida}" selected>${item.descripcion}</option>`;
                } else {
                    html += `<option value="${item.id_unidad_medida}">${item.descripcion}</option>`;

                }
            });
            html += '</select>';
            break;

        default:
            break;
    }

    return html;
}

function validarObjItemsParaCompra() {
    infoStateInput = [];
    if (itemsParaCompraList.length > 0) {
        // console.log(itemsParaCompraList);
        itemsParaCompraList.forEach(element => {
            if (element.id_producto == '' || element.id_producto == null) {
                infoStateInput.push('Guardar item');
            }
            if (element.id_categoria == '' || element.id_categoria == null) {
                infoStateInput.push('Completar Categoría');
            }
            if (element.id_subcategoria == '' || element.id_subcategoria == null) {
                infoStateInput.push('Completar Subcategoría');
            }
            if (element.id_clasif == '' || element.id_clasif == null) {
                infoStateInput.push('Completar Clasificación');
            }
            if (element.id_unidad_medida == '' || element.id_unidad_medida == null) {
                infoStateInput.push('Completar Unidad de Medida');
            }
            if (element.cantidad == '' || element.cantidad == null) {
                infoStateInput.push('Completar Cantidad');
            }

        });

        if (infoStateInput.length > 0) {

            document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrACrearOrden']").setAttribute('title', 'Falta: ' + infoStateInput.join());
            document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrACrearOrden']").setAttribute('disabled', true);
        } else {
            document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrACrearOrden']").setAttribute('title', 'Siguiente');
            document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrACrearOrden']").removeAttribute('disabled');

        }
    }
}

function componerTdItemsParaCompra(data, selectCategoria, selectSubCategoria, selectClasCategoria, selectMoneda, selectUnidadMedida) {
    // console.log(data);
    htmls = '<tr></tr>';
    $('#ListaItemsParaComprar tbody').html(htmls);
    var table = document.getElementById("ListaItemsParaComprar");


    for (var a = 0; a < data.length; a++) {
        if (data[a].estado != 7) {

            var row = table.insertRow(-1);

            if (data[a].id_producto == '') {
                row.insertCell(0).innerHTML = data[a].codigo_item ? data[a].codigo_item : '';
                row.insertCell(1).innerHTML = `<input type="text" class="form-control" name="part_number" data-id_cc_am="${data[a].id_cc_am ? data[a].id_cc_am : ''}" data-id_cc_venta="${data[a].id_cc_venta ? data[a].id_cc_venta : ''}"  value="${data[a].part_number ? data[a].part_number : ''}" data-indice="${a}" onkeyup="updateInputPartNumberModalItemsParaCompra(event);">`;
                row.insertCell(2).innerHTML = makeSelectedToSelect(a, 'categoria', selectCategoria, data[a].id_categoria, '');
                row.insertCell(3).innerHTML = makeSelectedToSelect(a, 'subcategoria', selectSubCategoria, data[a].id_subcategoria, '');
                row.insertCell(4).innerHTML = makeSelectedToSelect(a, 'clasificacion', selectClasCategoria, data[a].id_clasif, '');
                row.insertCell(5).innerHTML = `<span name="descripcion">${data[a].descripcion ? data[a].descripcion : '-'}</span> `;
                row.insertCell(6).innerHTML = makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, data[a].id_unidad_medida, '');
                row.insertCell(7).innerHTML = `<input type="text" class="form-control" name="cantidad" data-indice="${a}" onkeyup ="updateInputCantidadModalItemsParaCompra(event);" value="${data[a].cantidad}">`;
            } else {
                row.insertCell(0).innerHTML = data[a].codigo_item ? data[a].codigo_item : '';
                row.insertCell(1).innerHTML = `<input type="text" class="form-control" name="part_number" value="${data[a].part_number ? data[a].part_number : ''}" data-indice="${a}" onkeyup="updateInputPartNumberModalItemsParaCompra(event);" disabled>`;
                row.insertCell(2).innerHTML = makeSelectedToSelect(a, 'categoria', selectCategoria, data[a].id_categoria, 'disabled');
                row.insertCell(3).innerHTML = makeSelectedToSelect(a, 'subcategoria', selectSubCategoria, data[a].id_subcategoria, 'disabled');
                row.insertCell(4).innerHTML = makeSelectedToSelect(a, 'clasificacion', selectClasCategoria, data[a].id_clasif, 'disabled');
                row.insertCell(5).innerHTML = `<span name="descripcion">${data[a].descripcion ? data[a].descripcion : '-'}</span> `;
                row.insertCell(6).innerHTML = makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, data[a].id_unidad_medida, '');
                row.insertCell(7).innerHTML = `<input type="text" class="form-control" name="cantidad" data-indice="${a}" onkeyup="updateInputCantidadModalItemsParaCompra(event);" value="${data[a].cantidad}">`;
            }

            var tdBtnAction = row.insertCell(8);
            var btnAction = '';
            // tdBtnAction.className = classHiden;
            var hasAttrDisabled = '';
            tdBtnAction.setAttribute('width', 'auto');

            btnAction = `<div class="btn-group btn-group-sm" role="group" aria-label="Second group">`;
            if (data[a].id_producto == '') {
                btnAction += `<button class="btn btn-success btn-sm"  name="btnGuardarItem" data-toggle="tooltip" title="Guardar en Catálogo" onClick="guardarItemParaCompraEnCatalogo(this, ${a});" ${hasAttrDisabled}><i class="fas fa-save"></i></button>`;

            }
            // btnAction += `<button class="btn btn-primary btn-sm" name="btnRemplazarItem" data-toggle="tooltip" title="Remplazar" onClick="buscarRemplazarItemParaCompra(this, ${a});" ${hasAttrDisabled}><i class="fas fa-search"></i></button>`;
            btnAction += `<button class="btn btn-danger btn-sm"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" onclick="eliminarItemDeListadoParaCompra(this, ${a});" ${hasAttrDisabled} ><i class="fas fa-trash-alt"></i></button>`;
            btnAction += `</div>`;
            tdBtnAction.innerHTML = btnAction;


        }
    }

    validarObjItemsParaCompra();
}



function updateIdItemParaCompraList(id_item,id_producto,indiceSelected) {
    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].id_producto = parseInt(id_producto);
            itemsParaCompraList[index].id_item = parseInt(id_item);
        }
    });
    validarObjItemsParaCompra();
}

function updateInputCategoriaModalItemsParaCompra(event) {
    let idValor = event.target.value;
    let textValor = event.target.options[event.target.selectedIndex].textContent;
    let indiceSelected = event.target.dataset.indice;

    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].id_categoria = parseInt(idValor);
            itemsParaCompraList[index].categoria = textValor;

        }
    });
    validarObjItemsParaCompra();

    // console.log(itemsParaCompraList);
}
function updateInputSubcategoriaModalItemsParaCompra(event) {
    let idValor = event.target.value;
    let textValor = event.target.options[event.target.selectedIndex].textContent;
    let indiceSelected = event.target.dataset.indice;

    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].id_subcategoria = parseInt(idValor);
            itemsParaCompraList[index].subcategoria = textValor;

        }
    });
    validarObjItemsParaCompra();

    // console.log(itemsParaCompraList);
}

function updateInputClasificacionModalItemsParaCompra(event) {
    let idValor = event.target.value;
    let textValor = event.target.options[event.target.selectedIndex].textContent;
    let indiceSelected = event.target.dataset.indice;

    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].id_clasificacion = parseInt(idValor);
            itemsParaCompraList[index].clasificacion = textValor;

        }
    });
    validarObjItemsParaCompra();

    // console.log(itemsParaCompraList);
}
function updateInputMonedaModalItemsParaCompra(event) {
    let idValor = event.target.value;
    let textValor = event.target.options[event.target.selectedIndex].textContent;
    let indiceSelected = event.target.dataset.indice;

    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].id_moneda = parseInt(idValor);
            itemsParaCompraList[index].moneda = textValor;

        }
    });
    validarObjItemsParaCompra();

    // console.log(itemsParaCompraList);
}

function updateInputUnidadMedidaModalItemsParaCompra(event) {
    let idValor = event.target.value;
    let textValor = event.target.options[event.target.selectedIndex].textContent;
    let indiceSelected = event.target.dataset.indice;

    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].id_unidad_medida = parseInt(idValor);
            itemsParaCompraList[index].unidad_medida = textValor;

        }
    });
    validarObjItemsParaCompra();

    // console.log(itemsParaCompraList);
}

function updateInputCantidadModalItemsParaCompra(event) {
    let nuevoValor = event.target.value;
    let indiceSelected = event.target.dataset.indice;
    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].cantidad = nuevoValor;

        }
    });
    validarObjItemsParaCompra();

    // console.log(itemsParaCompraList);
}

function updateInputPartNumberModalItemsParaCompra(event) {
    let nuevoValor = event.target.value;
    let indiceSelected = event.target.dataset.indice;

    itemsParaCompraList.forEach((element, index) => {
        if (index == indiceSelected) {
            itemsParaCompraList[index].part_number = nuevoValor;

        }
    });
    validarObjItemsParaCompra();

    // console.log(itemsParaCompraList);
}


function guardarItemParaCompraEnCatalogo(obj, index) {
    let tr = obj.parentNode.parentNode.parentNode;
    let inputPartNumber = tr.querySelector("input[name='part_number']").value;
    let id_cc_am = tr.querySelector("input[name='part_number']").dataset.id_cc_am;
    let id_cc_venta = tr.querySelector("input[name='part_number']").dataset.id_cc_venta;
    let inputDescripcion = tr.querySelector("span[name='descripcion']").textContent;
    let inputCategoria = tr.querySelector("select[name='categoria']").value;
    let inputSubCategoria = tr.querySelector("select[name='subcategoria']").value;
    let inputClasificacion = tr.querySelector("select[name='clasificacion']").value;
    let inputUnidadMedida = tr.querySelector("select[name='unidad_medida']").value;
    let inputCantidad = tr.querySelector("input[name='cantidad']").value;


    if (inputPartNumber, inputCategoria, inputSubCategoria, inputClasificacion, inputUnidadMedida != '') {
        let data = {
            'part_number': (inputPartNumber.length>0)?inputPartNumber:null,
            'id_cc_am': id_cc_am,
            'id_cc_venta': id_cc_venta,
            'descripcion': inputDescripcion,
            'id_categoria': inputCategoria,
            'id_subcategoria': inputSubCategoria,
            'id_clasif': inputClasificacion,
            'id_unidad_medida': inputUnidadMedida,
            'cantidad': inputCantidad
        }
        // console.log(data);
        crearNuevoProductoEnCatalogo(data, tr, index);

    } else {
        alert('Complete todo los campos antes de hacer clic en guardar ');
    }
}

function crearNuevoProductoEnCatalogo(data, tr, index) {
    $.ajax({
        type: 'POST',
        url: 'guardar-producto',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            if (response['msj'].length > 0) {
                alert(response['msj']);
            } else {
                if (response.id_producto > 0) {
                    updateIdItemParaCompraList(response.id_item,response.id_producto,index)
                    alert('Se Guardó con éxito el producto en el Catálogo');
                    tr.querySelector("button[name='btnGuardarItem']").remove();
                    tr.querySelector("input[name='part_number']").setAttribute('disabled',true);
                    tr.querySelector("select[name='categoria']").setAttribute('disabled',true);
                    tr.querySelector("select[name='subcategoria']").setAttribute('disabled',true);
                    tr.querySelector("select[name='clasificacion']").setAttribute('disabled',true);
                } else {
                    alert('ocurrio un problema al generar el codigo del producto');
                }
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function buscarRemplazarItemParaCompra(obj, index) {
    console.log(obj, index);

}

function actualizarIndicesDeTabla(){
    let trs= document.querySelector("table[id='ListaItemsParaComprar'] tbody").children;
    let i=0;
    for (let index = 1; index < trs.length; index++) {
            trs[index].querySelector("input[name='part_number']").dataset.indice = i;
            trs[index].querySelector("select[name='categoria']").dataset.indice = i;
            trs[index].querySelector("select[name='subcategoria']").dataset.indice = i;
            trs[index].querySelector("select[name='clasificacion']").dataset.indice = i;
            trs[index].querySelector("select[name='unidad_medida']").dataset.indice = i;
            trs[index].querySelector("input[name='cantidad']").dataset.indice = i;
            trs[index].querySelector("select[name='moneda']").dataset.indice = i;
            trs[index].querySelector("input[name='precio']").dataset.indice = i;
            i++;
    }

}

function eliminarItemDeListadoParaCompra(obj, index) {
    // console.log(obj,index);

    let row = obj.parentNode.parentNode.parentNode;
    row.remove(row);

    itemsParaCompraList = itemsParaCompraList.filter((item, i) => i !== index);

    actualizarIndicesDeTabla();
    // console.log(itemsParaCompraList);
}


function irACrearOrden() {
    // console.log(reqTrueList);
    // console.log(itemsParaCompraList);
    if(reqTrueList.length ==1){
        guardarMasItemsAlDetalleRequerimiento(reqTrueList,itemsParaCompraList); // solo un id_requerimiento;
    }else{
        alert("Lo sentimos, La implementación para generar orden mas de un requerimiento aun no esta completa, Seleccione solo un requerimiento");
    }

}

function guardarMasItemsAlDetalleRequerimiento(id_requerimiento_list,item_list){
    $.ajax({
        type: 'POST',
        url: rutaGuardarItemsEnDetalleRequerimiento,
        data: { 'id_requerimiento_list': id_requerimiento_list, 'items':item_list },
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response.status == 200) {
                $('#modal-agregar-items-para-compra').modal('hide');

                $('#modal-orden-requerimiento').modal({
                    show: true,
                    backdrop: 'true'
                });

                obtenerRequerimiento(reqTrueList);

            }else{
                alert('Ocurrio un problema, no se pudo agregar los items al requerimiento');
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function mostrarCatalogoItems(){
//     $('#modal-catalogo-items').modal({
//         show: true,
//         backdrop: 'true',
//         keyboard: true

//     });
//     listarItems();
// }

// function listarItems() {
//     var vardataTables = funcDatatables();
//     $('#listaItems').dataTable({
//         // 'dom': vardataTables[1],
//         // 'buttons': vardataTables[2],
//         // "dom": '<"toolbar">frtip',

//         'scrollY': '30vh',
//         'scrollCollapse': true,
//         'language' : vardataTables[0],
//         'processing': true,
//         "bDestroy": true,
//         "scrollX": true,
//         'ajax': '/logistica/mostrar_items',
//         'columns': [
//             {'data': 'id_item'},
//             {'data': 'id_producto'},
//             {'data': 'id_servicio'},
//             {'data': 'id_equipo'},
//             {'data': 'codigo'},
//             {'data': 'part_number'},
//             {'data': 'categoria'},
//             {'data': 'subcategoria'},
//             {'data': 'descripcion'},
//             {'data': 'unidad_medida_descripcion'},
//             {'data': 'id_unidad_medida'},
//             {'render':
//                 function (data, type, row){
//                     if(row.id_unidad_medida == 1){
//                         return ('<button class="btn btn-sm btn-info" onClick="verSaldoProducto('+row.id_producto+ ');">Stock</button>');
//                     }else{ 
//                         return '';
//                     }

//                 }
//             }
//         ],
//         'columnDefs': [
//             { 'aTargets': [0], 'sClass': 'invisible'},
//             { 'aTargets': [1], 'sClass': 'invisible'},
//             { 'aTargets': [2], 'sClass': 'invisible'},
//             { 'aTargets': [3], 'sClass': 'invisible'},
//             { 'aTargets': [10], 'sClass': 'invisible'}
//                     ],
//         'order': [
//             [8, 'asc']
//         ],
//         initComplete: function( settings, json ) {
//             // console.log('data cargada');
//             if(detalleItemsParaCompraCCSelected.hasOwnProperty('descripcion')){
//                 if(detalleItemsParaCompraCCSelected.descripcion.length >0){
//                     $('#example_filter input').val(detalleItemsParaCompraCCSelected.descripcion);
//                     this.api().search(detalleItemsParaCompraCCSelected.descripcion).draw();
//                 }
//             }
//         }

//     });
//     let tablelistaitem = document.getElementById(
//         'listaItems_wrapper'
//     )
//     tablelistaitem.childNodes[0].childNodes[0].hidden = true;

//     let listaItems_filter = document.getElementById(
//         'listaItems_filter'
//     )
//     listaItems_filter.querySelector("input[type='search']").style.width='100%';

// }


// function selectItem(){

//     var id_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent;
//     var codigo_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent;
//     var part_number = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent;
//     var id_producto = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent;
//     var id_servicio = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_servicio']").textContent;
//     var id_equipo = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_equipo']").textContent;
//     var descripcion_producto = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent;
//     var unidad_medida_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='unidad_medida']").textContent;
//     var id_unidad_medida_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent;
//     var newItem={};

//     if(id_item.length >0){
//         // console.log(detalleRequerimientoSelected);

//         newItem= {
//             'id': 0,
//             'cantidad': 1,
//             'cantidad_a_comprar': 1,
//             'codigo_item': codigo_item,
//             'codigo_requerimiento': null,
//             'descripcion': descripcion_producto,
//             'descripcion_adicional': descripcion_producto,
//             'estado': 1,
//             'fecha_entrega': null,
//             'fecha_registro': new Date().toISOString().slice(0, 10),
//             'id_detalle_requerimiento': 0,
//             'id_item': id_item,
//             'id_producto': id_producto,
//             'id_requerimiento': 0,
//             'id_tipo_item': 1,
//             'id_unidad_medida': id_unidad_medida_item,
//             'lugar_entrega': null,
//             'obs': null,
//             'part_number': part_number,
//             'precio_referencial': 0,
//             'stock_comprometido': 0,
//             'subtotal': 0,
//             'unidad_medida': unidad_medida_item
//         };

//         detalleRequerimientoSelected.push(newItem)
//         // agregarItemATablaListaDetalleOrden(newItem);
//         agregarItemATablaListaItemsParaCompra(newItem);
//         document.querySelector("div[id='check-guarda_en_requerimiento']").setAttribute("style",'display:inline-block');
//         document.querySelector("div[id='input-group-fecha_entrega']").setAttribute("style",'display:none');
//         $('#modal-catalogo-items').modal('hide');

//     }else{
//         alert('hubo un error, no existe un id_item');
//     }
// }