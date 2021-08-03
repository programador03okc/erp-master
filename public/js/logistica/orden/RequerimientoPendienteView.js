
// ============== View =========================
var vardataTables = funcDatatables();

class RequerimientoPendienteView {
    constructor(requerimientoPendienteCtrl){
        this.requerimientoPendienteCtrl = requerimientoPendienteCtrl;
    }

    initializeEventHandler(){
        $('#modal-atender-con-almacen').on("click","button.handleClickGuardarAtendidoConAlmacen", ()=>{
            this.guardarAtendidoConAlmacen();
        });

        $('#requerimientos_pendientes').on("click","button.handleClickCrearOrdenCompra", ()=>{
            this.crearOrdenCompra();
        });
    }

    renderRequerimientoPendienteListModule(id_empresa = null, id_sede = null) {
        this.requerimientoPendienteCtrl.getRequerimientosPendientes(id_empresa, id_sede).then( (res) =>{
            this.construirTablaListaRequerimientosPendientes(res);
        }).catch( (err) =>{
            console.log(err)
        })
    }

    abrirRequerimiento(idRequerimiento){
        // Abrir nuevo tab
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
        var win = window.open(url, '_blank');
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();
    }

    construirTablaListaRequerimientosPendientes(data) {
        // requerimientoPendienteCtrl.limpiarTabla('listaRequerimientosPendientes');
        let that =this;

        vista_extendida();
        tablaListaRequerimientosPendientes= $('#listaRequerimientosPendientes').DataTable({
            'dom': vardataTables[1],
            'buttons': [],
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
                        return `<label class="lbl-codigo handleClickAbrirRequerimiento" title="Abrir Requerimiento" data-id-requerimiento="${row.id_requerimiento}">${row.codigo}</label>`;
                    }
                },

                { 'data': 'concepto' },
                { 'data': 'fecha_registro' },
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
                { 'data': 'nombre_usuario' },
                { 'data': 'estado_doc' },
                {
                    render: function (data, type, row) {

                        // if(permisoCrearOrdenPorRequerimiento == '1') {
                        let tieneTransformacion = row.tiene_transformacion;
                        let cantidadItemBase = row.cantidad_items_base;
                        if (tieneTransformacion == true && cantidadItemBase == 0) {
                            return ('<div class="btn-group" role="group">' +
                                '</div>' +
                                '<div class="btn-group" role="group">' +
                                '<button type="button" class="btn btn-info btn-xs" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalCuadroCostos(this);">' +
                                '<i class="fas fa-eye fa-sm"></i>' +
                                '</button>' +

                                '</div>');
                        } else {
                            let openDiv = '<div class="btn-group" role="group">';
                            let btnVerDetalleRequerimiento= '<button type="button" class="btn btn-default btn-xs handleClickVerDetalleRequerimiento" name="btnVerDetalleRequerimiento" title="Ver detalle requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-chevron-down fa-sm"></i></button>';

                            // let btnAgregarItemBase = '<button type="button" class="btn btn-success btn-xs" name="btnAgregarItemBase" title="Mapear productos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalAgregarItemBase(this);"  ><i class="fas fa-sign-out-alt"></i></button>';
                            let btnMapearProductos = '<button type="button" class="mapeo btn btn-success btn-xs" title="Mapear productos" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo="' + row.codigo + '"  ><i class="fas fa-sign-out-alt"></i></button>';
                            let btnAtenderAlmacen='';
                            let btnCrearOrdenCompra = '';
                                if(row.count_pendientes ==0){
                                    btnAtenderAlmacen = '<button type="button" class="btn btn-primary btn-xs handleClickAtenderConAlmacen" name="btnOpenModalAtenderConAlmacen" title="Atender con almacén" data-id-requerimiento="' + row.id_requerimiento + '"><i class="fas fa-dolly fa-sm"></i></button>';
                                    btnCrearOrdenCompra = '<button type="button" class="btn btn-warning btn-xs handleClickCrearOrdenCompraPorRequerimiento" name="btnCrearOrdenCompraPorRequerimiento" title="Crear Orden de Compra" data-id-requerimiento="' + row.id_requerimiento + '"  ><i class="fas fa-file-invoice"></i></button>';
                                }
                            let btnCrearOrdenServicio = '<button type="button" class="btn btn-danger btn-xs handleClickCrearOrdenServicioPorRequerimiento" name="btnCrearOrdenServicioPorRequerimiento" title="Crear Orden de Servicio" data-id-requerimiento="' + row.id_requerimiento + '"  ><i class="fas fa-file-invoice fa-sm"></i></button>';
                            let btnVercuadroCostos ='';
                            if(row.id_tipo_requerimiento ==1){
                                btnVercuadroCostos= '<button type="button" class="btn btn-info btn-xs handleClickOpenModalCuadroCostos" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-eye fa-sm"></i></button>';
                            }

                            let closeDiv = '</div>';

                            let cantidadItemTipoServicio = 0;
                            row.detalle.forEach(element => {
                                if (element.id_tipo_item == 2) {
                                    cantidadItemTipoServicio += 1;
                                }
                            });
                            if (cantidadItemTipoServicio >= 1) {
                                return (openDiv + btnVerDetalleRequerimiento + btnAtenderAlmacen + btnMapearProductos + btnCrearOrdenCompra + btnCrearOrdenServicio + btnVercuadroCostos + closeDiv);
                            } else {
                                return (openDiv + btnVerDetalleRequerimiento + btnAtenderAlmacen + btnMapearProductos + btnCrearOrdenCompra + btnVercuadroCostos + closeDiv);
                            }
                        }
                    },
                }
            ],
            'initComplete': function () {
                var trs = this.$('tr');
           
                for (let i = 0; i < trs.length; i++) {
                    trs[i].addEventListener('click', handleTrClick);
                }
                function handleTrClick() {
                    if (this.classList.contains('eventClick')) {
                        this.classList.remove('eventClick');
                    } else {
                        const rows = Array.from(document.querySelectorAll('tr.eventClick'));
                        rows.forEach(row => {
                            row.classList.remove('eventClick');
                        });
                        this.classList.add('eventClick');
                    }
                    let id = this.childNodes[1].childNodes[0].childNodes[0].dataset.idRequerimiento
                    let stateCheck = this.childNodes[1].childNodes[0].childNodes[0].checked
                    that.requerimientoPendienteCtrl.controlListCheckReq(id, stateCheck);
                }

                let listaRequerimientosPendientes_filter = document.querySelector("div[id='listaRequerimientosPendientes_filter']");
                var buttonFiler = document.createElement("button");
                buttonFiler.type = "button";
                buttonFiler.className = "btn btn-default pull-left";
                buttonFiler.style = "margin-right: 30px;";
                buttonFiler.innerHTML = "<i class='fas fa-filter'></i> Filtros";
                buttonFiler.addEventListener('click', this.abrirModalFiltrosRequerimientosPendientes, false);
                listaRequerimientosPendientes_filter.appendChild(buttonFiler);

                $('#listaRequerimientosPendientes tbody').on("click","label.handleClickAbrirRequerimiento",function(e){
                    that.abrirRequerimiento(e.currentTarget.dataset.idRequerimiento);
                });

                $('#listaRequerimientosPendientes tbody').on("click","button.handleClickVerDetalleRequerimiento",function(e){
                    // var data = $('#listaRequerimientosPendientes').DataTable().row($(this).parents("tr")).data();
                    that.verDetalleRequerimiento(e.currentTarget);
                });

                $('#listaRequerimientosPendientes tbody').on("click","button.handleClickAtenderConAlmacen",function(e){
                    that.atenderConAlmacen(e.currentTarget);
                });

                $('#listaRequerimientosPendientes tbody').on("click","button.handleClickOpenModalCuadroCostos",function(e){
                    that.openModalCuadroCostos(e.currentTarget);
                });

                $('#listaRequerimientosPendientes tbody').on("click","button.handleClickCrearOrdenCompraPorRequerimiento",function(e){
                    that.crearOrdenCompraPorRequerimiento(e.currentTarget);
                });
                $('#listaRequerimientosPendientes tbody').on("click","button.handleClickCrearOrdenServicioPorRequerimiento",function(e){
                    that.crearOrdenServicioPorRequerimiento(e.currentTarget);
                });
                

            },
            'columnDefs': [
                { 'aTargets': [0], 'sClass': 'invisible','sWidth': '0%' },
                { 'aTargets': [1], 'sWidth': '3%' },
                { 'aTargets': [2], 'sWidth': '5%' },
                { 'aTargets': [3], 'sWidth': '20%' },
                { 'aTargets': [4], 'sWidth': '5%', 'className': 'text-center'},
                { 'aTargets': [5], 'sWidth': '5%', 'className': 'text-center' },
                { 'aTargets': [6], 'sWidth': '10%' },
                { 'aTargets': [7], 'sWidth': '6%', 'className': 'text-center' },
                { 'aTargets': [8], 'sWidth': '5%' },
                { 'aTargets': [9], 'sWidth': '5%', 'className': 'text-center' },
                { 'aTargets': [10], 'sWidth': '8%', 'className': 'text-left' }
            ],
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
        
        $('#listaRequerimientosPendientes tbody').on("click","button.mapeo", function(){
            var id_requerimiento = $(this).data('idRequerimiento');
            var codigo = $(this).data('codigo');
            
            $('#modal-mapeoItemsRequerimiento').modal({
                show: true
            });
            $('[name=id_requerimiento]').val(id_requerimiento);
            $('#cod_requerimiento').text(codigo);
            listarItemsRequerimientoMapeo(id_requerimiento);
            
            $('#submit_mapeoItemsRequerimiento').removeAttr('disabled');
        });
    }

    verDetalleRequerimiento(obj){
        this.verDetalleRequerimientoListaRequerimientosPendientes(obj);

    }


    verDetalleRequerimientoListaRequerimientosPendientes(obj) {
        
        let tr = obj.closest('tr');
        var row = tablaListaRequerimientosPendientes.row(tr);
        var id = obj.dataset.idRequerimiento;
        if (row.child.isShown()) {
            //  This row is already open - close it
            row.child.hide();
            tr.classList.remove('shown');
        }
        else {
            // Open this row
            //    row.child( format(iTableCounter, id) ).show();
            this.buildFormatListaRequerimientosPendientes(iTableCounter, id, row);
            tr.classList.add('shown');
            // try datatable stuff
            oInnerTable = $('#listaRequerimientosPendientes_' + iTableCounter).dataTable({
                //    data: sections, 
                autoWidth: true,
                deferRender: true,
                info: false,
                lengthChange: false,
                ordering: false,
                paging: false,
                scrollX: false,
                scrollY: false,
                searching: false,
                columns: [
                ]
            });
            iTableCounter = iTableCounter + 1;
        }
    }

    buildFormatListaRequerimientosPendientes(table_id, id, row) {
        this.requerimientoPendienteCtrl.obtenerDetalleRequerimientos(id).then((res) =>{
            this.construirDetalleRequerimientoListaRequerimientosPendientes(table_id,row,res);
        }).catch(function(err) {
            console.log(err)
        })
    }

    construirDetalleRequerimientoListaRequerimientosPendientes(table_id,row,response){
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






    // atender con almacen
    atenderConAlmacen(obj) {
        this.requerimientoPendienteCtrl.openModalAtenderConAlmacen(obj).then((res)=> {
            this.construirTablaListaItemsRequerimientoParaAtenderConAlmacen(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaListaItemsRequerimientoParaAtenderConAlmacen(data) { // data.almacenes, data.detalle_requerimiento
        let that =this;
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
         
                {
                    render: function (data, type, row) {
                        return (row.codigo_producto?row.codigo_producto:'');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.part_number?row.part_number:row.producto_part_number);
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.descripcion?row.descripcion:row.producto_descripcion);
                    }
                },
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
                                select = `<select class="form-control handleChangeUpdateSelectAlmacenAAtender" data-indice="${meta.row}" >`;
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
                                action = `<input type="text" name="cantidad_a_atender" class="form-control handleBlurUpdateInputCantidadAAtender" style="width: 70px;" data-indice="${meta.row}" value="${parseInt(row.stock_comprometido ? row.stock_comprometido : 0)}" />`;

                                that.updateObjCantidadAAtender(meta.row, row.stock_comprometido);

                            }
                            return action;
                        }
                }
            ],
            'initComplete': function () {

                $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("change","select.handleChangeUpdateSelectAlmacenAAtender",(e)=>{
                    that.requerimientoPendienteCtrl.updateSelectAlmacenAAtender(e.currentTarget);
                });

                $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("blur","input.handleBlurUpdateInputCantidadAAtender", (e)=>{
                    that.requerimientoPendienteCtrl.updateInputCantidadAAtender(e.currentTarget);
                });

            },
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
        this.requerimientoPendienteCtrl.updateObjCantidadAAtender(indice, valor);
    }


    guardarAtendidoConAlmacen() {
        this.requerimientoPendienteCtrl.guardarAtendidoConAlmacen().then((res) =>{
            if (res.update_det_req > 0) {
                Lobibox.notify('success', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `Reserva actualizsada`
                });
                this.requerimientoPendienteCtrl.getDataItemsRequerimientoParaAtenderConAlmacen(res.id_requerimiento).then(function (res) {
                    this.construirTablaListaItemsRequerimientoParaAtenderConAlmacen(res);
                }).catch(function (err) {
                    console.log(err)
                    Swal.fire(
                        '',
                        'Hubo un problema al intentar guardar la reserva, por favor vuelva a intentarlo',
                        'error'
                    );
                })

                this.renderRequerimientoPendienteListModule(null,null);

            } else {
                Swal.fire(
                    '',
                    'Hubo un problema al intentar guardar la reserva, por favor vuelva a intentarlo',
                    'error'
                );
            }
        }).catch(function (err) {
            console.log(err)
        })
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
        this.requerimientoPendienteCtrl.updateInputCategoriaModalItemsParaCompra(event)
    }
    updateInputSubcategoriaModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputSubcategoriaModalItemsParaCompra(event);
    }
    updateInputClasificacionModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputClasificacionModalItemsParaCompra(event)
    }
    updateInputUnidadMedidaModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputUnidadMedidaModalItemsParaCompra(event);
    }

    updateInputCantidadModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputCantidadModalItemsParaCompra(event);
    }
    updateInputPartNumberModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputPartNumberModalItemsParaCompra(event);
    }

    guardarItemParaCompraEnCatalogo(obj, indice) {
        this.requerimientoPendienteCtrl.guardarItemParaCompraEnCatalogo(obj, indice);

    }
    eliminarItemDeListadoParaCompra(obj, indice) {
        let id = obj.dataset.id;
        let tr = obj.parentNode.parentNode.parentNode;
        this.requerimientoPendienteCtrl.eliminarItemDeListadoParaCompra(indice)
        this.retornarItemAlDetalleCC(id);
        tr.remove(tr);
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
        this.requerimientoPendienteCtrl.guardarItemsEnDetalleRequerimiento();

    }

    agregarItemsBaseParaCompraFinalizado(response) {

        if (response.status == 200) {
            alert(response.mensaje);
            $('#modal-agregar-items-para-compra').modal('hide');
            requerimientoPendienteView.renderRequerimientoPendienteListModule(null, null);
        } else {
            alert(response.mensaje);
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
        this.requerimientoPendienteCtrl.openModalCuadroCostos(obj).then((res) =>{
            if (res.status == 200) {
                this.llenarCabeceraModalDetalleCuadroCostos(res.head)
                this.construirTablaListaDetalleCuadroCostos(res.detalle);
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
                        let simboloMoneda=( row.moneda_costo_unitario_proveedor == 's')?'S/':(row.moneda_costo_unitario_proveedor=='d')?'$':row.moneda_costo_unitario_proveedor;

                        return `${simboloMoneda} ${row['costo_unitario_proveedor'] ? row['costo_unitario_proveedor'] : ''}`;
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
                        return `${row['fondo_proveedor'] ? ('<span style="color:red">'+row['fondo_proveedor']+' </span>') : 'Ninguno'}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        let simboloMoneda=( row.moneda_costo_unitario_proveedor == 's')?'S/':(row.moneda_costo_unitario_proveedor=='d')?'$':row.moneda_costo_unitario_proveedor;

                       let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        return `${simboloMoneda} ${costoUnitario}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        let tipoCambio = row.tipo_cambio;
                        let costoUnitarioSoles = costoUnitario * tipoCambio;
                        return `S/${costoUnitarioSoles}`;
                    }
                },
                {
                    'render': function (data, type, row) {

                        let totalFleteProveedor= (Math.round((row.cantidad*row.flete_proveedor) * 100) / 100).toFixed(2);
                        return `S/${(totalFleteProveedor)}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        let simboloMoneda=( row.moneda_costo_unitario_proveedor == 's')?'S/':(row.moneda_costo_unitario_proveedor=='d')?'$':row.moneda_costo_unitario_proveedor;

                        let totalFleteProveedor= (Math.round((row.cantidad*row.flete_proveedor) * 100) / 100).toFixed(2);
                        let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        let tipoCambio = row.tipo_cambio;
                        let costoUnitarioSoles = costoUnitario * tipoCambio;
                        let costoCompraMasFlete = costoUnitarioSoles + totalFleteProveedor;
                        return `S/${costoCompraMasFlete}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return ``;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return ``;
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
        this.requerimientoPendienteCtrl.crearOrdenCompraPorRequerimiento(obj);

    }
    // Crear orden de servicio por requerimiento
    crearOrdenServicioPorRequerimiento(obj) {
        this.requerimientoPendienteCtrl.crearOrdenServicioPorRequerimiento(obj);

    }

    crearOrdenCompra() {
        this.requerimientoPendienteCtrl.crearOrdenCompra();

    }
}

