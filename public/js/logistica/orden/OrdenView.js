// ============== View =========================
var vardataTables = funcDatatables();
var simboloMoneda = '';
class OrdenView {
    init() {
        var reqTrueList = JSON.parse(sessionStorage.getItem('reqCheckedList'));
        var tipoOrden = sessionStorage.getItem('tipoOrden');
        if (reqTrueList != null && (reqTrueList.length > 0)) {
            // ordenView.changeStateInput('form-crear-orden-requerimiento', false);
            // ordenView.changeStateButton('editar');
            ordenCtrl.obtenerRequerimiento(reqTrueList, tipoOrden);
            let btnVinculoAReq = `<span class="text-info" id="text-info-req-vinculado" > <a onClick="window.location.reload();" style="cursor:pointer;" title="Recargar con Valores Iniciales del Requerimiento">(vinculado a un Requerimiento)</a> <span class="badge label-danger" onClick="ordenView.eliminarVinculoReq();" style="position: absolute;margin-top: -5px;margin-left: 5px; cursor:pointer" title="Eliminar vínculo">×</span></span>`;
            document.querySelector("section[class='content-header']").children[0].innerHTML += btnVinculoAReq;

        }
        var idOrden = sessionStorage.getItem('idOrden');
        if (idOrden > 0) {
            mostrarOrden(idOrden);
            changeStateButton('historial');

        }
        this.getTipoCambioCompra();

    }

    getTipoCambioCompra() {

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        let fechaHoy = now.toISOString().slice(0, 10)

        ordenCtrl.getTipoCambioCompra(fechaHoy).then(function (tipoCambioCompra) {
            document.querySelector("input[name='tipo_cambio_compra']").value = tipoCambioCompra;
        }).catch(function (err) {
            console.log(err)
        })
    }
    changeMoneda(event) {
        simboloMoneda = event.options[event.selectedIndex].dataset.simboloMoneda;

        this.updateAllSimboloMoneda();
    }

    updateAllSimboloMoneda() {

        if (simboloMoneda == '') {
            let selectMoneda = document.querySelector("select[name='id_moneda']");
            simboloMoneda = selectMoneda.options[selectMoneda.selectedIndex].dataset.simboloMoneda;

        }
        let simboloMonedaAll = document.querySelectorAll("span[name='simboloMoneda']");
        simboloMonedaAll.forEach((element, indice) => {
            simboloMonedaAll[indice].textContent = simboloMoneda;
        });

    }

    changeSede(obj) {
        ordenCtrl.changeSede(obj);
    }

    changeLogoEmprsa(id_empresa) {
        switch (id_empresa) {
            case '1':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src', '/images/logo_okc.png');
                break;
            case '2':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src', '/images/logo_proyectec.png');
                break;
            case '3':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src', '/images/logo_smart.png');
                break;
            case '4':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src', '/images/jedeza_logo.png');
                break;
            case '5':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src', '/images/rbdb_logo.png');
                break;
            case '6':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src', '/images/protecnologia_logo.png');
                break;
            default:
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src', '/images/img-wide.png');
                break;
        }
    }

    handlechangeCondicion(event) {
        let condicion = document.getElementsByName('id_condicion')[0];
        let text_condicion = condicion.options[condicion.selectedIndex].text;

        if (text_condicion == 'CONTADO CASH' || text_condicion == 'Contado cash') {
            document.getElementsByName('plazo_dias')[0].value = null;
            document.getElementsByName('plazo_dias')[0].setAttribute('class', 'form-control activation group-elemento invisible');
            document.getElementsByName('text_dias')[0].setAttribute('class', 'form-control group-elemento invisible');
        } else if (text_condicion == 'CREDITO' || text_condicion == 'Crédito') {
            document.getElementsByName('plazo_dias')[0].setAttribute('class', 'form-control activation group-elemento');
            document.getElementsByName('text_dias')[0].setAttribute('class', 'form-control group-elemento');

        }
    }

    loadHeadRequerimiento(data, idTipoOrden) {
        if (idTipoOrden == 3) { // orden de servicio
            this.ocultarBtnCrearProducto();
        }
        document.querySelector("select[name='id_tp_documento']").value = idTipoOrden;
        document.querySelector("img[id='logo_empresa']").setAttribute("src", data.logo_empresa);
        document.querySelector("input[name='cdc_req']").value = data.codigo_oportunidad ? data.codigo_oportunidad : data.codigo;
        document.querySelector("input[name='ejecutivo_responsable']").value = data.nombre_ejecutivo_responsable ? data.nombre_ejecutivo_responsable : '';
        document.querySelector("input[name='direccion_destino']").value = data.direccion_fiscal_empresa_sede ? data.direccion_fiscal_empresa_sede : '';
        document.querySelector("input[name='id_ubigeo_destino']").value = data.id_ubigeo_empresa_sede ? data.id_ubigeo_empresa_sede : '';
        document.querySelector("input[name='ubigeo_destino']").value = data.ubigeo_empresa_sede ? data.ubigeo_empresa_sede : '';
        document.querySelector("select[name='id_sede']").value = data.id_sede ? data.id_sede : '';
        document.querySelector("input[name='id_cc']").value = data.id_cc ? data.id_cc : '';
        document.querySelector("textarea[name='observacion']").value = data.observacion ? data.observacion : '';

        this.updateAllSimboloMoneda();

    }


    listar_detalle_orden_requerimiento(data) {
        $('#listaDetalleOrden').DataTable({
            'bInfo': false,
            // 'scrollCollapse': true,
            'serverSide': false,
            'processing': false,
            'paging': false,
            'searching': false,
            'language': vardataTables[0],
            'destroy': true,
            'dom': 'Bfrtip',
            'order': false,
            'data': data,
            'bDestroy': true,
            'columns': [

                {
                    'render':
                        function (data, type, row, meta) {
                            return row.codigo_requerimiento;
                        }, 'name': 'codigo_requerimiento'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return row.part_number;
                        }, 'name': 'codigo_item'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return row.descripcion_producto ? row.descripcion_producto : row.descripcion_adicional;
                        }, 'name': 'descripcion_adicional'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return row.unidad_medida;
                        }, 'name': 'unidad_medida'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            // return '<input type="text" class="form-control" name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+row.cantidad+'" onChange="updateInputCantidad(event);" style="width: 70px;" disabled/>';
                            return '<span name="cantidad" data-id="' + (row.id) + '" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '">' + row.cantidad + '</span>';

                        }, 'name': 'cantidad'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            if (row.estado == 7) {
                                return '<input type="number" name="precio" data-id="' + (row.id) + '" placeholder="0.00" min="0"  class="form-control" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '" value="' + (row.precio_unitario ? row.precio_unitario : "") + '" onChange="ordenCtrl.updateInputPrecio(event);" style="width:90px;" disabled/>';
                            } else {
                                return '<input type="number" name="precio" data-id="' + (row.id) + '" placeholder="0.00" min="0" class="form-control" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '" value="' + (row.precio_unitario ? row.precio_unitario : "") + '" onChange="ordenCtrl.updateInputPrecio(event);" style="width:90px;"/>';
                            }
                        }, 'name': 'precio'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            if (row.estado == 7) {
                                return '<input type="number" name="cantidad_a_comprar" data-id="' + (row.id) + '" min="0" class="form-control" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '"   onchange="ordenCtrl.updateInputCantidadAComprar(event);" value="' + (row.cantidad_a_comprar ? row.cantidad_a_comprar : row.cantidad) + '" style="width:70px;" disabled />';
                            } else {
                                ordenCtrl.updateInObjCantidadAComprar((row.row + 1), (row.id_requerimiento), (row.id_detalle_requerimiento), (row.cantidad));

                                return '<input type="number" name="cantidad_a_comprar" data-id="' + (row.id) + '" min="0" class="form-control" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '"   onchange="ordenCtrl.updateInputCantidadAComprar(event);" value="' + (row.cantidad_a_comprar ? row.cantidad_a_comprar : row.cantidad) + '" style="width:70px;"/>';
                            }
                        }, 'name': 'cantidad_a_comprar'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            // return '<div style="display:flex;"><var name="simboloMoneda"></var> <div name="subtotal" data-id="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+((Math.round((row.cantidad*row.precio_unitario) * 100) / 100).toFixed(2))+'</div></div>';
                            return '<input type="number" name="subtotal" data-id="' + (row.id) + '" min="0" class="form-control" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '"   onchange="ordenCtrl.updateInputSubtotal(event);" value="' + ((Math.round((row.cantidad * row.precio_unitario) * 100) / 100).toFixed(2)) + '" style="width:90px;"/>';

                        }, 'name': 'subtotal'
                },
                {
                    'render':
                        function (data, type, row, meta) {

                            let action = `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-danger btn-sm activation" name="btnOpenModalEliminarItemOrden" title="Eliminar Item"  data-id="${(row.id)}" data-key="${(row.id)}" data-row="${(meta.row)}" data-id_requerimiento="${(row.id_requerimiento ? row.id_requerimiento : 0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0)}"  onclick="ordenCtrl.openModalEliminarItemOrden(this);">
                                <i class="fas fa-trash fa-sm"></i>
                                </button>
                            </div>
                            `;

                            return action;
                        }
                }
            ],
            "initComplete": function () {
                ordenView.updateAllSimboloMoneda();
                ordenCtrl.calcTotalOrdenDetalleList();
            },
            'rowCallback': function (row, data) {
                if (data.estado == '7') {
                    $('td', row).css({ 'background-color': 'mistyrose', 'color': 'indianred' });
                }
            },

            'columnDefs': [
                {
                    'targets': "_all",
                    'orderable': false
                },
                { width: '10px', targets: 0, sWidth: '8%' },
                { width: '20px', targets: 1, sWidth: '8%' },
                { width: '50px', targets: 2, sWidth: '30%' },
                { width: '10px', targets: 3, sWidth: '5%' },
                { width: '10px', targets: 4, sWidth: '5%' },
                { width: '10px', targets: 5, sWidth: '10%' },
                { width: '10px', targets: 6, sWidth: '10%' },
                { width: '10px', targets: 7, sWidth: '10%' },
                { width: '5px', targets: 8, sWidth: '8%', sClass: 'text-center' }
            ],
            'order': [[1, "asc"]]


        });

        $('#listaDetalleOrden thead th').off('click')
        $('#listaDetalleOrden tr').css('cursor', 'default');



    }


    eliminadoFilaTablaListaDetalleOrden(obj) {
        let tr = obj.parentNode.parentNode.parentNode;
        tr.remove();
    }


    // modal agregar producto en orden 
    catalogoProductosModal() {
        $('#modal-catalogo-items').modal({
            show: true,
            backdrop: 'true',
            keyboard: true

        });
        this.ocultarBtnCrearProducto();
        ordenCtrl.getcatalogoProductos().then(function (res) {
            ordenView.listarItems(res);
        }).catch(function (err) {
            console.log(err)
        })

    }

    ocultarBtnCrearProducto() {
        cambiarVisibilidadBtn("btn-crear-producto", "ocultar");
    }

    listarItems(data) {
        var tablaListaItems = $('#listaItems').dataTable({
            'language': vardataTables[0],
            'processing': true,
            "bDestroy": true,
            // "scrollX": true,
            'data': data,
            'columns': [
                { 'data': 'id_item' },
                { 'data': 'id_producto' },
                { 'data': 'id_servicio' },
                { 'data': 'id_equipo' },
                { 'data': 'codigo' },
                { 'data': 'part_number' },
                { 'data': 'categoria' },
                { 'data': 'subcategoria' },
                { 'data': 'descripcion' },
                { 'data': 'unidad_medida_descripcion' },
                { 'data': 'id_unidad_medida' },
                {
                    'render':
                        function (data, type, row) {
                            if (row.id_unidad_medida == 1) {
                                let btnSeleccionar = `<button class="btn btn-success btn-xs" onclick="ordenView.selectItem(this,${row.id_producto});">Seleccionar</button>`;
                                // let btnVerSaldo = `<button class="btn btn-sm btn-info" onClick="verSaldoProducto('${row.id_producto}');">Stock</button>')`;
                                return btnSeleccionar;

                            } else {
                                return '';
                            }

                        }
                }
            ],
            'columnDefs': [
                { 'aTargets': [0], 'sClass': 'invisible', 'sWidth': '0%' },
                { 'aTargets': [1], 'sClass': 'invisible', 'sWidth': '0%' },
                { 'aTargets': [2], 'sClass': 'invisible', 'sWidth': '0%' },
                { 'aTargets': [3], 'sClass': 'invisible', 'sWidth': '0%' },
                { 'aTargets': [4], 'sWidth': '3%' }, // codigo
                { 'aTargets': [5], 'sWidth': '3%' }, // partnumber
                { 'aTargets': [6], 'sWidth': '5%' }, // categoria
                { 'aTargets': [7], 'sWidth': '5%' }, // subcategoria
                { 'aTargets': [8], 'sWidth': '30%' }, // descripcion
                { 'aTargets': [9], 'sWidth': '5%' }, // unidad medida
                { 'aTargets': [10], 'sClass': 'invisible', 'sWidth': '0%' },
                { 'aTargets': [11], 'sWidth': '4%', 'className': 'text-center' } // accion
            ],
            'order': [
                [8, 'asc']
            ]
        });



        let tablelistaitem = document.getElementById(
            'listaItems_wrapper'
        )
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;

        let listaItems_filter = document.getElementById(
            'listaItems_filter'
        )
        listaItems_filter.querySelector("input[type='search']").style.width = '100%';
    }

    selectItem(obj, idProducto) {
        let tr = obj.closest('tr');
        var idItem = tr.children[0].innerHTML;
        var idProd = tr.children[1].innerHTML;
        var idServ = tr.children[2].innerHTML;
        var idEqui = tr.children[3].innerHTML;
        var codigo = tr.children[4].innerHTML;
        var partNum = tr.children[5].innerHTML;
        var categoria = tr.children[6].innerHTML;
        var subcategoria = tr.children[7].innerHTML;
        var descri = tr.children[8].innerHTML;
        var unidad = tr.children[9].innerHTML;
        var id_unidad = tr.children[10].innerHTML;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent = idItem;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent = codigo;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent = partNum;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent = descri;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent = idProd;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_servicio']").textContent = idServ;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_equipo']").textContent = idEqui;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='unidad_medida']").textContent = unidad;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent = id_unidad;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='categoria']").textContent = categoria;
        document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='subcategoria']").textContent = subcategoria;

        ordenCtrl.selectItem();

    }


    vincularRequerimientoAOrdenModal() {
        $('#modal-vincular-requerimiento-orden').modal({
            show: true,
            backdrop: 'true',
            keyboard: true

        });

        ordenCtrl.getRequerimientosPendientes(null, null).then(function (res) {
            ordenView.ConstruirlistarRequerimientosPendientesParaVincularConOrden(res);

        }).catch(function (err) {
            console.log(err)
        })

    }

    ConstruirlistarRequerimientosPendientesParaVincularConOrden(data) {
        tablaListaRequerimientosParaVincular= $('#listaRequerimientosParaVincular').DataTable({
            'serverSide': false,
            'processing': false,
            'destroy': true,
            'language': vardataTables[0],
            'data': data,
            // "dataSrc":'',
            'dom': 'Bfrtip',
            'bDestroy': true,
            'columns': [
                { 'data': 'codigo' },
                { 'data': 'alm_req_concepto' },
                { 'data': 'fecha_registro' },
                { 'data': 'tipo_req_desc' },
                { 'data': 'moneda' },
                {
                    'render':
                        function (data, type, row) {
                            return '';
                        }
                },
                { 'data': 'empresa_sede' },
                { 'data': 'usuario' },
                { 'data': 'estado_doc' },
                {
                    'render':
                        function (data, type, row) {
                            let containerOpenBrackets = `<div class="btn-group" role="group" style="display: flex;flex-direction: row;flex-wrap: nowrap;">`;
                            let btnVerDetalle = `<button type="button" class="ver-detalle btn btn-default boton" data-id-requerimiento="${row.id_requerimiento}" onclick="ordenView.verDetalleRequerimientoModalVincularRequerimiento(this)" data-toggle="tooltip" data-placement="bottom" title="Ver detalle requerimiento" data-id="${row.id_orden_compra}"> <i class="fas fa-chevron-down fa-sm"></i> </button>`;
                            let btnSeleccionar = `<button type="button" class="ver-detalle btn btn-success boton" onclick="ordenView.vincularRequerimiento(${row.id_requerimiento})" data-toggle="tooltip" data-placement="bottom" title="Seleccionar" data-id="${row.id_orden_compra}"> Seleccionar </button>`;
                            let containerCloseBrackets = `</div>`;
                            return (containerOpenBrackets + btnVerDetalle + btnSeleccionar + containerCloseBrackets);
                        }
                }

            ],
            'columnDefs': [
                { 'aTargets': [0],'className': "text-left", 'sWidth': '5%' },
                { 'aTargets': [1],'className': "text-left", 'sWidth': '40%'},
                { 'aTargets': [2],'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [3],'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [4],'className': "text-center", 'sWidth': '8%' },
                { 'aTargets': [5],'className': "text-center", 'sWidth': '8%' },
                { 'aTargets': [6],'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [7],'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [8],'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [9],'className': "text-center", 'sWidth': '5%' }
            ]

        });

    }


    verDetalleRequerimientoModalVincularRequerimiento(obj){
        ordenCtrl.verDetalleRequerimientoModalVincularRequerimiento(obj);
    }

    construirDetalleRequerimientoModalVincularRequerimiento(table_id,row,response){
        var html = '';
        if (response.length > 0) {
            response.forEach(function (element) {
                html += `<tr>
                    <td style="border: none; text-align:center;">${(element.part_number != null ? element.part_number :'')}</td>
                    <td style="border: none; text-align:left;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion?element.descripcion:'')}</td>
                    <td style="border: none; text-align:center;">${element.abreviatura != null ? element.abreviatura : ''}</td>
                    <td style="border: none; text-align:center;">${element.cantidad >0 ? element.cantidad : ''}</td>
                    <td style="border: none; text-align:center;">${element.precio_unitario >0 ? element.precio_unitario : ''}</td>
                    <td style="border: none; text-align:center;">${parseFloat(element.subtotal) > 0 ?Util.formatoNumero(element.subtotal,2) :Util.formatoNumero((element.cantidad * element.precio_unitario),2)}</td>
                    <td style="border: none; text-align:center;">${element.motivo != null ? element.motivo : ''}</td>
                    <td style="border: none; text-align:center;">${element.observacion != null ? element.observacion : ''}</td>
                    <td style="border: none; text-align:center;">${element.estado_doc != null ? element.estado_doc : ''}</td>
                    </tr>`;
                });
                var tabla = `<table class="table table-condensed table-bordered" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none; text-align:center;">Part number</th>
                        <th style="border: none; text-align:center;">Descripcion</th>
                        <th style="border: none; text-align:center;">Unidad medida</th>
                        <th style="border: none; text-align:center;">cantidad</th>
                        <th style="border: none; text-align:center;">precio_unitario</th>
                        <th style="border: none; text-align:center;">subtotal</th>
                        <th style="border: none; text-align:center;">motivo</th>
                        <th style="border: none; text-align:center;">observacion</th>
                        <th style="border: none; text-align:center;">Estado</th>
                    </tr>
                </thead>
                <tbody style="background: #e7e8ea;">${html}</tbody>
                </table>`;
        }else{
            var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            row.child(tabla).show();
    }

    vincularRequerimiento(idRequerimiento){
        ordenCtrl.vincularRequerimiento(idRequerimiento)
    }

    estadoVinculoRequerimiento(resolve){
        if(resolve.estado == '200'){
            alert(resolve.mensaje);
            $('#modal-vincular-requerimiento-orden').modal('hide');
        }else{
            alert(resolve.mensaje);

        }


    }


    openModalEliminarItemOrden(obj) {
        ordenCtrl.openModalEliminarItemOrden(obj);

    }

    // mostrar info si esta vinculado con un requerimiento
    eliminarVinculoReq() {
        sessionStorage.removeItem('reqCheckedList');
        sessionStorage.removeItem('tipoOrden');
        window.location.reload();
    }


    // guardar orden
    hasCheckedGuardarEnRequerimiento() {
        let hasCheck = document.querySelector("input[name='guardarEnRequerimiento']").checked;
        return hasCheck;
    }

    get_header_orden_requerimiento() {
        let id_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_orden']").value;
        let tipo_cambio_compra = document.querySelector("div[type='crear-orden-requerimiento'] input[name='tipo_cambio_compra']").value;
        let id_tp_documento = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_tp_documento']").value;

        let id_moneda = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_moneda']").value;
        let codigo_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='codigo_orden']").value;
        let fecha_emision = document.querySelector("div[type='crear-orden-requerimiento'] input[name='fecha_emision']").value;
        let incluye_igv = document.querySelector("div[type='crear-orden-requerimiento'] input[name='incluye_igv']").checked;

        let id_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_proveedor']").value;
        let id_contrib = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contrib']").value;
        let id_contacto_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value;
        let id_cuenta_principal_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value;

        let id_condicion = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_condicion']").value;
        let plazo_dias = document.querySelector("div[type='crear-orden-requerimiento'] input[name='plazo_dias']").value;
        let plazo_entrega = document.querySelector("div[type='crear-orden-requerimiento'] input[name='plazo_entrega']").value;
        let id_cc = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_cc']").value;
        let id_tp_doc = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_tp_doc']").value;

        let id_sede = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_sede']").value;
        let direccion_destino = document.querySelector("div[type='crear-orden-requerimiento'] input[name='direccion_destino']").value;
        let id_ubigeo_destino = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value;

        let personal_autorizado_1 = document.querySelector("div[type='crear-orden-requerimiento'] input[name='personal_autorizado_1']").value;
        let personal_autorizado_2 = document.querySelector("div[type='crear-orden-requerimiento'] input[name='personal_autorizado_2']").value;
        let observacion = document.querySelector("div[type='crear-orden-requerimiento'] textarea[name='observacion']").value;

        let data = {
            'id_orden': id_orden,
            'tipo_cambio_compra': tipo_cambio_compra,
            'id_tp_documento': id_tp_documento,
            'id_moneda': id_moneda,
            'codigo_orden': codigo_orden,
            'fecha_emision': fecha_emision,
            'incluye_igv': incluye_igv,

            'id_proveedor': id_proveedor,
            'id_contrib': id_contrib,
            'id_contacto_proveedor': id_contacto_proveedor,
            'id_cuenta_principal_proveedor': id_cuenta_principal_proveedor,

            'id_condicion': id_condicion,
            'plazo_dias': plazo_dias,
            'plazo_entrega': plazo_entrega,
            'id_tp_doc': id_tp_doc,
            'id_cc': id_cc,

            'id_sede': id_sede,
            'direccion_destino': direccion_destino,
            'id_ubigeo_destino': id_ubigeo_destino,

            'personal_autorizado_1': personal_autorizado_1,
            'personal_autorizado_2': personal_autorizado_2,
            'observacion': observacion,

            'detalle': []
        }

        return data;
    }

    incluyeIGVHandle(e) {
        ordenCtrl.calcTotalOrdenDetalleList(e.target.checked);
    }
}

const ordenView = new OrdenView();



function save_orden(data, action) {
    let hasCheck = ordenView.hasCheckedGuardarEnRequerimiento();
    payload_orden = ordenView.get_header_orden_requerimiento();
    if (hasCheck == true) {
        let coutReqInObj = ordenCtrl.countRequirementsInObj();
        if (coutReqInObj == 1) {
            // console.log(listCheckReq);
            // console.log(detalleOrdenList);
            // vincultar item con req unico
            let id_req = listCheckReq[0].id_req;
            detalleOrdenList.forEach(drs => {
                if (drs.id > 0) {
                    drs.id_requerimiento = id_req;
                }
            });

            payload_orden.detalle = detalleOrdenList;
            // payload_orden += '&detalle_requerimiento='+JSON.stringify(detalleOrdenList);
            ordenCtrl.guardar_orden_requerimiento(action, payload_orden);

        } else if (coutReqInObj > 1) {
            // console.log('open modal to select item/req');
            $('#modal-vincular-item-requerimiento').modal({
                show: true,
                backdrop: 'static'
            });
            // fillListaRequerimientosVinculados();


        } else { //no existen nuevos item argregados, guardar nromal (no habra que guardar en req)
            payload_orden.detalle = detalleOrdenList;
            ordenCtrl.guardar_orden_requerimiento(action, payload_orden);

        }
    } else { // sin guardar en req
        payload_orden = ordenView.get_header_orden_requerimiento();
        payload_orden.detalle = (typeof detalleOrdenList != 'undefined') ? detalleOrdenList : detalleOrdenList;
        ordenCtrl.guardar_orden_requerimiento(action, payload_orden);
    }
}

function anular_orden(id) {
    baseUrl = 'anular/' + id;
    $.ajax({
        type: 'PUT',
        url: baseUrl,
        dataType: 'JSON',
        success: function (res) {
            if (res.status == 200) {
                alert(res.mensaje);
                let url = "/logistica/gestion-logistica/compras/ordenes/listado/index";
                window.location.replace(url);
            } else {
                console.log(res);
                alert(res.mensaje);

            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function fechaHoy() {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value = now.toISOString().slice(0, -1);
};

function nueva_orden() {
    fechaHoy();
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='cdc_req']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ejecutivo_responsable']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_destino']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_1']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_2']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_1']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_2']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] textarea[name='observacion']").value = '';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='incluye_igv']").checked = true;


    ordenCtrl.limpiarTabla('listaDetalleOrden');
}

