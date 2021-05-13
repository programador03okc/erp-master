
// ============== View =========================
var vardataTables = funcDatatables();

class RequerimientoPendienteView {
    init() {
        this.renderRequerimientoPendienteListModule(null, null);
    }

    renderRequerimientoPendienteListModule(id_empresa = null, id_sede = null) {
        requerimientoPendienteCtrl.getRequerimientosPendientes(id_empresa, id_sede).then(function (res) {
            requerimientoPendienteView.construirTablaListaRequerimientosPendientes(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    abrirRequerimiento(idRequerimiento){
        // Abrir nuevo tab
        localStorage.setItem("id_requerimiento",idRequerimiento);
        let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
        var win = window.open(url, '_blank');
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();
    }

    construirTablaListaRequerimientosPendientes(data) {
        vista_extendida();
        $('#listaRequerimientosPendientes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'order': [[10, 'desc']],
            'destroy': true,
            "bInfo": true,
            "bLengthChange": false,

            'data': data,
            'columns': [
                {
                    render: function (data, type, row) {
                        return `${row.id_requerimiento}">`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `<div class="text-center"><input type="checkbox" data-id-requerimiento="${row.id_requerimiento}" /></div>`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `<label class="lbl-codigo" title="Abrir Requerimiento" onClick="requerimientoPendienteView.abrirRequerimiento(${row.id_requerimiento})">${row.codigo}</label>`;
                    }
                },

                { 'data': 'concepto' },
                { 'data': 'fecha_requerimiento' },
                { 'data': 'tipo_req_desc' },
                {
                    render: function (data, type, row) {
                        let entidad = '';
                        if (row.id_cliente > 0) {
                            entidad = `${row.cliente_razon_social} ${row.cliente_ruc == null ? '' : ('RUC: ' + row.cliente_ruc)}`;
                        } else if (row.id_persona > 0) {
                            entidad = `${row.nombre_persona}`;
                        }
                        return entidad;
                    }
                },
                { 'data': 'empresa_sede' },
                { 'data': 'usuario' },
                { 'data': 'estado_doc' },
                {
                    render: function (data, type, row) {

                        // if(permisoCrearOrdenPorRequerimiento == '1') {
                        let tieneTransformacion = row.tiene_transformacion;
                        let cantidadItemBase = row.cantidad_items_base;
                        if (tieneTransformacion == true && cantidadItemBase == 0) {
                            return ('<div class="btn-group" role="group">' +
                                '<button type="button" class="btn btn-primary btn-xs" name="btnAgregarItemBase" title="Agregar items del base" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalAgregarItemBase(this);"  style="background:#b498d0;">' +
                                '<i class="fas fa-puzzle-piece fa-sm"></i>' +
                                '</button>' +
                                '</div>' +
                                '<div class="btn-group" role="group">' +
                                '<button type="button" class="btn btn-info btn-xs" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalCuadroCostos(this);">' +
                                '<i class="fas fa-eye fa-sm"></i>' +
                                '</button>' +

                                '</div>');
                        } else {
                            let openDiv = '<div class="btn-group" role="group">';
                            let btnAtenderAlmacen = '<button type="button" class="btn btn-primary btn-xs" name="btnOpenModalAtenderConAlmacen" title="Atender con almacén" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.atenderConAlmacen(this);"><i class="fas fa-dolly fa-sm"></i></button>';
                            let btnAgregarItemBase = '<button type="button" class="btn btn-primary btn-xs" name="btnAgregarItemBase" title="Agregar items del base" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalAgregarItemBase(this);"  style="background:#b498d0;"><i class="fas fa-puzzle-piece fa-sm"></i></button>';
                            let btnCrearOrdenCompra = '<button type="button" class="btn btn-warning btn-xs" name="btnCrearOrdenCompraPorRequerimiento" title="Crear Orden de Compra" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.crearOrdenCompraPorRequerimiento(this);"><i class="fas fa-file-invoice"></i></button>';
                            let btnCrearOrdenServicio = '<button type="button" class="btn btn-danger btn-xs" name="btnCrearOrdenServicioPorRequerimiento" title="Crear Orden de Servicio" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.crearOrdenServicioPorRequerimiento(this);"><i class="fas fa-file-invoice fa-sm"></i></button>';
                            let btnVercuadroCostos = '<button type="button" class="btn btn-info btn-xs" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalCuadroCostos(this);"><i class="fas fa-eye fa-sm"></i></button>';
                            let closeDiv = '</div>';

                            let cantidadItemTipoServicio = 0;
                            row.detalle.forEach(element => {
                                if (element.id_tipo_item == 2) {
                                    cantidadItemTipoServicio += 1;
                                }
                            });
                            if (cantidadItemTipoServicio >= 1) {
                                return (openDiv + btnAtenderAlmacen + btnAgregarItemBase + btnCrearOrdenCompra + btnCrearOrdenServicio + btnVercuadroCostos + closeDiv);
                            } else {
                                return (openDiv + btnAtenderAlmacen + btnAgregarItemBase + btnCrearOrdenCompra + btnVercuadroCostos + closeDiv);
                            }
                        }
                    },
                }
            ],
            'initComplete': function () {
                var trs = document.querySelectorAll('#listaRequerimientosPendientes tr');
                trs.forEach(function (tr) {
                    tr.addEventListener('click', handleTrClick);
                });
                function handleTrClick() {
                    if (this.classList.contains('eventClick')) {
                        this.classList.remove('eventClick');
                    } else {
                        this.classList.add('eventClick');
                    }
                    let id = this.childNodes[1].childNodes[0].childNodes[0].dataset.idRequerimiento
                    let stateCheck = this.childNodes[1].childNodes[0].childNodes[0].checked
                    requerimientoPendienteCtrl.controlListCheckReq(id, stateCheck);
                }

                let listaRequerimientosPendientes_filter = document.querySelector("div[id='listaRequerimientosPendientes_filter']");
                var buttonFiler = document.createElement("button");
                buttonFiler.type = "button";
                buttonFiler.className = "btn btn-default pull-left";
                buttonFiler.style = "margin-right: 30px;";
                buttonFiler.innerHTML = "<i class='fas fa-filter'></i> Filtros";
                buttonFiler.addEventListener('click', requerimientoPendienteView.abrirModalFiltrosRequerimientosPendientes, false);
                listaRequerimientosPendientes_filter.appendChild(buttonFiler);

            },
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
            "createdRow": function (row, data, dataIndex) {
                if (data.tiene_transformacion == true) {
                    $(row.childNodes[2]).css('background-color', '#d8c74ab8');
                    $(row.childNodes[2]).css('font-weight', 'bold');
                }
                else if (data.tiene_transformacion == false) {
                    $(row.childNodes[2]).css('background-color', '#b498d0');
                    $(row.childNodes[2]).css('font-weight', 'bold');
                }

            }
        });
    }

    // filtros

    abrirModalFiltrosRequerimientosPendientes() {
        $('#modal-filtro-requerimientos-pendientes').modal({
            show: true,
            backdrop: 'static'
        });
    }

    chkEmpresa(e) {

        if (e.target.checked == true) {
            document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").removeAttribute('readOnly');

        } else {
            document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").setAttribute('readOnly', true);

        }
    }

    chkSede(e) {

        if (e.target.checked == true) {
            document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").removeAttribute('readOnly');
        } else {
            document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").setAttribute('readOnly', true);

        }
    }


    handleChangeFilterReqByEmpresa(event) {
        let id_empresa = event.target.value;
        requerimientoPendienteCtrl.getDataSelectSede(id_empresa).then(function (res) {
            requerimientoPendienteView.llenarSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })

    }

    llenarSelectSede(array) {
        let selectElement = document.querySelector("select[name='sede']");

        if (selectElement.options.length > 0) {
            var i, L = selectElement.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }

        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_sede;
            selectElement.add(option);
        });
    }

    aplicarFiltros() {
        let idEmpresa = null;
        let idSede = null;

        let chkEmpresa = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] input[name='chkEmpresa']").checked;
        let chkSede = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] input[name='chkSede']").checked;

        if (chkEmpresa == true) {
            idEmpresa = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").value;

        }
        if (chkSede == true) {
            idSede = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").value;
        }

        $('#modal-filtro-requerimientos-pendientes').modal('hide');

        this.renderRequerimientoPendienteListModule(idEmpresa > 0 ? idEmpresa : null, idSede > 0 ? idSede : null);

    }

    statusBtnGenerarOrden() {
        let countStateCheckTrue = 0;

        listCheckReq.map(value => {
            if (value.stateCheck == true) {
                countStateCheckTrue += 1;
            }
        })


        if (countStateCheckTrue > 0) {
            document
                .getElementById('btnCrearOrdenCompra')
                .removeAttribute('disabled')
        } else {
            document
                .getElementById('btnCrearOrdenCompra')
                .setAttribute('disabled', true)
        }
    }




    // atender con almacen
    atenderConAlmacen(obj) {
        requerimientoPendienteCtrl.openModalAtenderConAlmacen(obj).then(function (res) {
            requerimientoPendienteView.construirTablaListaItemsRequerimientoParaAtenderConAlmacen(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaListaItemsRequerimientoParaAtenderConAlmacen(data) { // data.almacenes, data.detalle_requerimiento
        let data_detalle_requerimiento = data.detalle_requerimiento;
        let data_almacenes = data.almacenes;
        $('#listaItemsRequerimientoParaAtenderConAlmacen').dataTable({
            'scrollY': '50vh',
            'info': false,
            'searching': false,
            'paging': false,
            'scrollCollapse': true,
            'language': vardataTables[0],
            'processing': true,
            "bDestroy": true,
            "scrollX": true,
            'data': data_detalle_requerimiento,
            'columns': [
                { 'data': 'codigo_item' },
                { 'data': 'part_number' },
                { 'data': 'descripcion' },
                { 'data': 'unidad_medida' },
                {
                    render: function (data, type, row) {
                        // return  parseInt(row.cantidad - row.suma_transferencias);
                        return parseInt(row.cantidad);
                    }
                },
                { 'data': 'razon_social_proveedor_seleccionado' },
                {
                    render: function (data, type, row) {
                        let estado = '';
                        if (row.suma_transferencias > 0) {
                            estado = row.estado_doc + '<br><span class="label label-info">Con Transferencia</span>';
                        } else {
                            estado = row.estado_doc;
                        }

                        if (row.tiene_transformacion == true) {
                            estado += '<br><span class="label label-default">Producto Transformado</span>';
                        }

                        return estado;
                    }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            let select = '';
                            if (row.tiene_transformacion == false) {
                                select = `<select class="form-control" data-indice="${meta.row}" onChange="requerimientoPendienteCtrl.updateSelectAlmacenAAtender(this,event)" >`;
                                select += `<option value ="0">Sin selección</option>`;
                                data_almacenes.forEach(element => {
                                    if (row.id_almacen_reserva == element.id_almacen) {
                                        select += `<option value="${element.id_almacen}" data-id-empresa="${element.id_empresa}" selected>${element.descripcion}</option> `;

                                    } else {
                                        select += `<option value="${element.id_almacen}" data-id-empresa="${element.id_empresa}">${element.descripcion}</option> `;
                                    }
                                });
                                select += `</select>`;
                            }


                            return select;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            let action = '';
                            if (row.tiene_transformacion == false) {
                                action = `<input type="text" name="cantidad_a_atender" class="form-control" style="width: 70px;" data-indice="${meta.row}" onkeyup="requerimientoPendienteCtrl.updateInputCantidadAAtender(this,event);" value="${parseInt(row.stock_comprometido ? row.stock_comprometido : 0)}" />`;

                                requerimientoPendienteView.updateObjCantidadAAtender(meta.row, row.stock_comprometido);

                            }
                            return action;
                        }
                }
            ],
            "createdRow": function (row, data, dataIndex) {

                // $(row.childNodes[7]).css('background-color', '#586c86');  
                // $(row.childNodes[7]).css('font-weight', 'bold');
                // $(row.childNodes[8]).css('background-color', '#586c86');  
                // $(row.childNodes[8]).css('font-weight', 'bold');

            }
            // 'order': [
            //     [0, 'asc']
            // ]
        });
        let tablelistaitem = document.getElementById(
            'listaItemsRequerimientoParaAtenderConAlmacen_wrapper'
        )
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    }


    updateObjCantidadAAtender(indice, valor) {
        requerimientoPendienteCtrl.updateObjCantidadAAtender(indice, valor);
    }


    guardarAtendidoConAlmacen() {
        requerimientoPendienteCtrl.guardarAtendidoConAlmacen().then(function(res) {
            if (res.update_det_req > 0) {
                alert("Se realizo con éxito la reserva");
                requerimientoPendienteCtrl.getDataItemsRequerimientoParaAtenderConAlmacen(res.id_requerimiento).then(function (res) {
                    requerimientoPendienteView.construirTablaListaItemsRequerimientoParaAtenderConAlmacen(res);
                }).catch(function (err) {
                    console.log(err)
                })

                requerimientoPendienteView.renderRequerimientoPendienteListModule(null,null);

            } else {
                alert("Ocurrio un problema al intentar guardar la reserva");
            }
        }).catch(function (err) {
            console.log(err)
        })
    }

    // Agregar item base
    openModalAgregarItemBase(obj) {
        requerimientoPendienteCtrl.openModalAgregarItemBase();
        requerimientoPendienteCtrl.tieneItemsParaCompra(obj);
        requerimientoPendienteCtrl.getDataListaItemsCuadroCostosPorIdRequerimiento();
        requerimientoPendienteCtrl.getDataListaItemsCuadroCostosPorIdRequerimientoPendienteCompra();
        requerimientoPendienteCtrl.validarObjItemsParaCompra();

    }



    componerTdItemsParaCompra(data, selectCategoria, selectSubCategoria, selectClasCategoria, selectMoneda, selectUnidadMedida) {
        let htmls = '<tr></tr>';
        $('#ListaItemsParaComprar tbody').html(htmls);
        var table = document.getElementById("ListaItemsParaComprar");


        for (var a = 0; a < data.length; a++) {
            if (data[a].estado != 7) {

                var row = table.insertRow(-1);

                if (data[a].id_producto == '') {
                    row.insertCell(0).innerHTML = data[a].alm_prod_codigo ? data[a].alm_prod_codigo : '';
                    row.insertCell(1).innerHTML = `<input type="text" class="form-control" name="part_number" data-id_cc_am="${data[a].id_cc_am ? data[a].id_cc_am : ''}" data-id_cc_venta="${data[a].id_cc_venta ? data[a].id_cc_venta : ''}"  value="${data[a].part_number ? data[a].part_number : ''}" data-indice="${a}" onkeyup="updateInputPartNumberModalItemsParaCompra(event);">`;
                    row.insertCell(2).innerHTML = this.makeSelectedToSelect(a, 'categoria', selectCategoria, data[a].id_categoria, '');
                    row.insertCell(3).innerHTML = this.makeSelectedToSelect(a, 'subcategoria', selectSubCategoria, data[a].id_subcategoria, '');
                    row.insertCell(4).innerHTML = this.makeSelectedToSelect(a, 'clasificacion', selectClasCategoria, data[a].id_clasif, '');
                    row.insertCell(5).innerHTML = `<span name="descripcion">${data[a].descripcion ? data[a].descripcion : '-'}</span> `;
                    row.insertCell(6).innerHTML = this.makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, data[a].id_unidad_medida, '');
                    row.insertCell(7).innerHTML = `<input type="text" class="form-control" name="cantidad" data-indice="${a}" onkeyup ="requerimientoPendienteView.updateInputCantidadModalItemsParaCompra(event);" value="${data[a].cantidad}">`;
                } else {
                    row.insertCell(0).innerHTML = data[a].alm_prod_codigo ? data[a].alm_prod_codigo : '';
                    row.insertCell(1).innerHTML = `<input type="text" class="form-control" name="part_number" value="${data[a].part_number ? data[a].part_number : ''}" data-indice="${a}" onkeyup="requerimientoPendienteView.updateInputPartNumberModalItemsParaCompra(event);" disabled>`;
                    row.insertCell(2).innerHTML = this.makeSelectedToSelect(a, 'categoria', selectCategoria, data[a].id_categoria, 'disabled');
                    row.insertCell(3).innerHTML = this.makeSelectedToSelect(a, 'subcategoria', selectSubCategoria, data[a].id_subcategoria, 'disabled');
                    row.insertCell(4).innerHTML = this.makeSelectedToSelect(a, 'clasificacion', selectClasCategoria, data[a].id_clasif, 'disabled');
                    row.insertCell(5).innerHTML = `<span name="descripcion">${data[a].descripcion ? data[a].descripcion : '-'}</span> `;
                    row.insertCell(6).innerHTML = this.makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, data[a].id_unidad_medida, '');
                    row.insertCell(7).innerHTML = `<input type="text" class="form-control" name="cantidad" data-indice="${a}" onkeyup="requerimientoPendienteView.updateInputCantidadModalItemsParaCompra(event);" value="${data[a].cantidad}">`;
                }

                var tdBtnAction = row.insertCell(8);
                var btnAction = '';
                // tdBtnAction.className = classHiden;
                var hasAttrDisabled = '';
                tdBtnAction.setAttribute('width', 'auto');

                btnAction = `<div class="btn-group btn-group-sm" role="group" aria-label="Second group">`;
                if (data[a].id_producto == '') {
                    btnAction += `<button class="btn btn-success btn-sm"  name="btnGuardarItem" data-toggle="tooltip" title="Guardar en Catálogo" onClick="requerimientoPendienteView.guardarItemParaCompraEnCatalogo(this, ${a});" ${hasAttrDisabled}><i class="fas fa-save"></i></button>`;

                }
                // btnAction += `<button class="btn btn-primary btn-sm" name="btnRemplazarItem" data-toggle="tooltip" title="Remplazar" onClick="buscarRemplazarItemParaCompra(this, ${a});" ${hasAttrDisabled}><i class="fas fa-search"></i></button>`;
                btnAction += `<button class="btn btn-danger btn-sm"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" data-id="${data[a].id}" onclick="requerimientoPendienteView.eliminarItemDeListadoParaCompra(this, ${a});" ${hasAttrDisabled} ><i class="fas fa-trash-alt"></i></button>`;
                btnAction += `</div>`;
                tdBtnAction.innerHTML = btnAction;
            }
        }
        // requerimientoPendienteCtrl.quitarItemsDetalleCuadroCostosAgregadosACompra(data);
        // requerimientoPendienteCtrl.validarObjItemsParaCompra();

    }


    updateInputCategoriaModalItemsParaCompra(event) {
        requerimientoPendienteCtrl.updateInputCategoriaModalItemsParaCompra(event)
    }
    updateInputSubcategoriaModalItemsParaCompra(event) {
        requerimientoPendienteCtrl.updateInputSubcategoriaModalItemsParaCompra(event);
    }
    updateInputClasificacionModalItemsParaCompra(event) {
        requerimientoPendienteCtrl.updateInputClasificacionModalItemsParaCompra(event)
    }
    updateInputUnidadMedidaModalItemsParaCompra(event) {
        requerimientoPendienteCtrl.updateInputUnidadMedidaModalItemsParaCompra(event);
    }

    updateInputCantidadModalItemsParaCompra(event) {
        requerimientoPendienteCtrl.updateInputCantidadModalItemsParaCompra(event);
    }
    updateInputPartNumberModalItemsParaCompra(event) {
        requerimientoPendienteCtrl.updateInputPartNumberModalItemsParaCompra(event);
    }

    guardarItemParaCompraEnCatalogo(obj, indice) {
        requerimientoPendienteCtrl.guardarItemParaCompraEnCatalogo(obj, indice);

    }
    eliminarItemDeListadoParaCompra(obj, indice) {
        let id = obj.dataset.id;
        let tr = obj.parentNode.parentNode.parentNode;
        tr.remove(tr);
        requerimientoPendienteCtrl.eliminarItemDeListadoParaCompra(indice)
        this.retornarItemAlDetalleCC(id);
        this.actualizarIndicesDeTabla();


    }

    retornarItemAlDetalleCC(id) {
        var table = document.querySelector("table[id='ListaModalDetalleCuadroCostos'] tbody");
        var trs = table.querySelectorAll("tr");
        let idItemDetCCList = [];
        // console.log(trs);
        // if(trs.length ==1){
        //     if(trs[0].className=='odd'){
        //         trs[0].remove();
        //     }
        // }
        requerimientoPendienteCtrl.limpiarTabla('ListaModalDetalleCuadroCostos');

        if (trs.length > 1) {
            trs.forEach(tr => {
                idItemDetCCList.push(tr.children[9].children[0].dataset.id)
            });
        }
        if (!idItemDetCCList.includes(id)) {
            tempDetalleItemsParaCompraCC.forEach(element => {
                if (element.id == id) {
                    var row = table.insertRow(-1);
                    row.style.cursor = "default";

                    row.insertCell(0).innerHTML = element.part_no ? element.part_no : '';
                    var tdDesc = row.insertCell(1)
                    tdDesc.setAttribute('width', '50%')
                    tdDesc.innerHTML = element.descripcion ? element.descripcion : '';

                    row.insertCell(2).innerHTML = element.pvu_oc ? element.pvu_oc : '';
                    row.insertCell(3).innerHTML = element.flete_oc ? element.flete_oc : '';
                    row.insertCell(4).innerHTML = element.cantidad ? element.cantidad : '';
                    row.insertCell(5).innerHTML = element.garantia ? element.garantia : '';
                    row.insertCell(6).innerHTML = element.razon_social_proveedor ? element.razon_social_proveedor : '';
                    row.insertCell(7).innerHTML = element.nombre_autor ? element.nombre_autor : '';
                    row.insertCell(8).innerHTML = element.fecha_creacion ? element.fecha_creacion : '';
                    row.insertCell(9).innerHTML = `<button class="btn btn-xs btn-default" data-id="${element.id}"
                        onclick="requerimientoPendienteCtrl.procesarItemParaCompraDetalleCuadroCostos(this,${element.id});" 
                        title="Agregar Item" 
                        style="background-color:#714fa7; 
                        color:white;">
                        <i class="fas fa-plus"></i>
                        </button>`;

                }

            });
        }
    }

    actualizarIndicesDeTabla() {
        let trs = document.querySelector("table[id='ListaItemsParaComprar'] tbody").children;
        let i = 0;
        for (let index = 1; index < trs.length; index++) {
            trs[index].querySelector("input[name='part_number']").dataset.indice = i;
            trs[index].querySelector("select[name='categoria']").dataset.indice = i;
            trs[index].querySelector("select[name='subcategoria']").dataset.indice = i;
            trs[index].querySelector("select[name='clasificacion']").dataset.indice = i;
            trs[index].querySelector("select[name='unidad_medida']").dataset.indice = i;
            trs[index].querySelector("input[name='cantidad']").dataset.indice = i;
            i++;
        }
    }


    makeSelectedToSelect(indice, type, data, id, hasDisabled) {

        let html = '';
        switch (type) {
            case 'categoria':
                html = `<select class="form-control" name="categoria" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputCategoriaModalItemsParaCompra(event);">`;
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
                html = `<select class="form-control" name="subcategoria" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputSubcategoriaModalItemsParaCompra(event);">`;
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
                html = `<select class="form-control" name="clasificacion" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputClasificacionModalItemsParaCompra(event);">`;
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
                html = `<select class="form-control" name="unidad_medida" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputUnidadMedidaModalItemsParaCompra(event);">`;
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


    llenarTablaDetalleCuadroCostos(data) {
        var dataTableListaModalDetalleCuadroCostos = $('#ListaModalDetalleCuadroCostos').DataTable({
            'processing': false,
            'serverSide': false,
            'bDestroy': true,
            'bInfo': false,
            'dom': 'Bfrtip',
            'paging': false,
            'searching': false,
            'order': false,
            'columnDefs': [{
                'targets': "_all",
                'orderable': false
            }],
            'data': data,
            'columns': [
                {
                    'render': function (data, type, row) {
                        return `${row['part_no'] ? row['part_no'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['descripcion'] ? row['descripcion'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['pvu_oc'] ? row['pvu_oc'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['flete_oc'] ? row['flete_oc'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['cantidad'] ? row['cantidad'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['garantia'] ? row['garantia'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['razon_social_proveedor'] ? row['razon_social_proveedor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['nombre_autor'] ? row['nombre_autor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['fecha_creacion'] ? row['fecha_creacion'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `<button class="btn btn-xs btn-default"data-id="${row.id}" onclick="requerimientoPendienteCtrl.procesarItemParaCompraDetalleCuadroCostos(this,${row['id']});" title="Agregar Item" style="background-color:#714fa7; color:white;"><i class="fas fa-plus"></i></button>`;
                    }
                }
            ]
        });
        $('#ListaModalDetalleCuadroCostos thead th').off('click')
        document.querySelector("table[id='ListaModalDetalleCuadroCostos']").tHead.style.fontSize = '11px',
            document.querySelector("table[id='ListaModalDetalleCuadroCostos']").tBodies[0].style.fontSize = '11px';
        // dataTableListaModalDetalleCuadroCostos.buttons().destroy();
        document.querySelector("table[id='ListaModalDetalleCuadroCostos'] thead").style.backgroundColor = "#5d4d6d";
        $('#ListaModalDetalleCuadroCostos tr').css('cursor', 'default');

    }

    guardarItemsEnDetalleRequerimiento() {
        requerimientoPendienteCtrl.guardarItemsEnDetalleRequerimiento();

    }

    agregarItemsBaseParaCompraFinalizado(status) {
        if (status == 200) {
            alert('Item(s) Guardado');
            $('#modal-agregar-items-para-compra').modal('hide');
            requerimientoPendienteView.renderRequerimientoPendienteListModule(null, null);
        } else {
            alert('Ocurrio un problema, no se pudo agregar los items al requerimiento');
        }

    }

    totalItemsAgregadosParaCompraCompletada() {

        alert('Ya fueron agregados todos los items disponibles del Cuadro de Costos al Requerimiento');
        document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('disabled', true);
        let btnEliminarItem = document.querySelectorAll("div[id='modal-agregar-items-para-compra'] button[name='btnEliminarItem']");
        for (var i = 0; i < btnEliminarItem.length; i++) {
            btnEliminarItem[i].setAttribute('disabled', true);
        }

    }
    totalItemsAgregadosParaCompraPendiente() {

        document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").removeAttribute('disabled');
        let btnEliminarItem = document.querySelectorAll("div[id='modal-agregar-items-para-compra'] button[name='btnEliminarItem']");
        for (var i = 0; i < btnEliminarItem.length; i++) {
            btnEliminarItem[i].removeAttribute('disabled');
        }

    }


    // ver detalle cuadro de costos
    openModalCuadroCostos(obj) {
        requerimientoPendienteCtrl.openModalCuadroCostos(obj).then(function (res) {
            if (res.status == 200) {
                requerimientoPendienteView.llenarCabeceraModalDetalleCuadroCostos(res.head)
                requerimientoPendienteView.construirTablaListaDetalleCuadroCostos(res.detalle);
            }
        }).catch(function (err) {
            console.log(err)
        })
    }

    llenarCabeceraModalDetalleCuadroCostos(data){
        document.querySelector("div[id='modal-ver-cuadro-costos'] span[id='codigo']").textContent=data.orden_am;
    }

    construirTablaListaDetalleCuadroCostos(data) {

        var dataTablelistaModalVerCuadroCostos = $('#listaModalVerCuadroCostos').DataTable({
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
                        return `${row['part_no'] ? row['part_no'] : ''}`;
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
                        return `${row['flete_oc'] ? row['flete_oc'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['cantidad']}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['garantia'] ? row['garantia'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['origen_costo'] ? row['origen_costo'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['razon_social_proveedor']}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['costo_unitario_proveedor'] ? row['costo_unitario_proveedor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['plazo_proveedor'] ? row['plazo_proveedor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['flete_proveedor'] ? row['flete_proveedor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['comentario_proveedor'] ? row['comentario_proveedor'] : ''}`;
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
                }
            ]
        });

        document.querySelector("table[id='listaModalVerCuadroCostos']").tHead.style.fontSize = '11px',
            document.querySelector("table[id='listaModalVerCuadroCostos']").tBodies[0].style.fontSize = '11px';
        // dataTablelistaModalVerCuadroCostos.buttons().destroy();
        document.querySelector("table[id='listaModalVerCuadroCostos'] thead").style.backgroundColor = "#5d4d6d";
        $('#listaModalVerCuadroCostos tr').css('cursor', 'default');
    }

    // Crear orden por requerimiento
    crearOrdenCompraPorRequerimiento(obj) {
        requerimientoPendienteCtrl.crearOrdenCompraPorRequerimiento(obj);

    }
    // Crear orden de servicio por requerimiento
    crearOrdenServicioPorRequerimiento(obj) {
        requerimientoPendienteCtrl.crearOrdenServicioPorRequerimiento(obj);

    }
}

const requerimientoPendienteView = new RequerimientoPendienteView();
