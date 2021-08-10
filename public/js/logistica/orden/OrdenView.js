// ============== View =========================
var vardataTables = funcDatatables();
var simboloMoneda = '';
var tablaListaRequerimientosParaVincular;
var detalleOrdenList=[];
var iTableCounter = 1;
var oInnerTable;

class OrdenView {
    constructor(ordenCtrl){
        this.ordenCtrl = ordenCtrl;
    }
    
    init() {
        var reqTrueList = JSON.parse(sessionStorage.getItem('reqCheckedList'));
        var tipoOrden = sessionStorage.getItem('tipoOrden');
        if (reqTrueList != null && (reqTrueList.length > 0)) {
            // ordenView.changeStateInput('form-crear-orden-requerimiento', false);
            // ordenView.changeStateButton('editar');
            this.obtenerRequerimiento(reqTrueList, tipoOrden);
            let btnVinculoAReq = `<span class="text-info" id="text-info-req-vinculado" > <a onClick="window.location.reload();" style="cursor:pointer;" title="Recargar con Valores Iniciales del Requerimiento">(vinculado a un Requerimiento)</a> <span class="badge label-danger handleClickEliminarVinculoReq" style="position: absolute;margin-top: -5px;margin-left: 5px; cursor:pointer" title="Eliminar vínculo">×</span></span>`;
            document.querySelector("section[class='content-header']").children[0].innerHTML += btnVinculoAReq;

        }
        var idOrden = sessionStorage.getItem('idOrden');
        var action = sessionStorage.getItem('action');
        if (idOrden > 0) {
            this.mostrarOrden(idOrden);
        }
        if (action.length > 0) {
            switch (action) {
                case 'register':
                    changeStateButton('nuevo');
                    break;
                case 'edition':
                    changeStateButton('editar');
                    $("#form-crear-orden-requerimiento .activation").attr('disabled', false);
                    changeStateInput('form-crear-orden-requerimiento', false);

                    break;
                case 'historial':
                    changeStateButton('historial');
                    $("#form-crear-orden-requerimiento .activation").attr('disabled', true);
                    break;
            }
        }else{
            changeStateButton('inicio');
            $("#form-crear-orden-requerimiento .activation").attr('disabled', true);

        }
        this.getTipoCambioCompra();

    }

    initializeEventHandler(){

        $('.content-header').on("click","span.handleClickEliminarVinculoReq", ()=>{
            this.eliminarVinculoReq();
        });

        $('#form-crear-orden-requerimiento').on("change","select.handleChangeSede", (e)=>{
            this.changeSede(e.currentTarget);
        });
        $('#form-crear-orden-requerimiento').on("change","select.handleChangeCondicion", ()=>{
            this.handleChangeCondicion();
        });
        $('#form-crear-orden-requerimiento').on("change","select.handleChangeMoneda", (e)=>{
            this.changeMoneda(e.currentTarget);
        });
        $('#form-crear-orden-requerimiento').on("click","input.handleClickIncluyeIGV", (e)=>{
            this.incluyeIGV(e);
        });

        $('#form-crear-orden-requerimiento').on("click","button.handleClickCatalogoProductosModal", ()=>{
            this.catalogoProductosModal();
        });
        $('#form-crear-orden-requerimiento').on("click","button.handleClickVincularRequerimientoAOrdenModal", ()=>{
            this.vincularRequerimientoAOrdenModal();
        });
        $('#listaItems tbody').on("click","button.handleClickSelectItem",(e)=>{
            // var data = $('#listaItems').DataTable().row($(this).parents("tr")).data();
                this.selectItem(e.currentTarget,e.currentTarget.dataset.idProducto);
        });


        $('#listaDetalleOrden tbody').on("blur","input.handleBlurUpdateInputPrecio", (e)=>{
            this.updateInputPrecio(e);
        });
        $('#listaDetalleOrden tbody').on("blur","input.handleBlurUpdateInputCantidadAComprar", (e)=>{
            this.updateInputCantidadAComprar(e);
        });

        $('#listaDetalleOrden tbody').on("blur","input.handleBlurUpdateInputSubtotal", (e)=>{
            this.updateInputSubtotal(e);
        });


        $('#listaDetalleOrden tbody').on("click","button.handleClickOpenModalEliminarItemOrden",(e)=>{
            this.openModalEliminarItemOrden(e.currentTarget);
        });
    }

    limpiarTabla(identificador){
        let nodeTbody = document.querySelector("table[id='" + identificador + "'] tbody");

        for(var i = nodeTbody.rows.length - 1; i > 0; i--)
        {
            nodeTbody.deleteRow(i);
        }   
    }

    obtenerRequerimiento(reqTrueList,tipoOrden){
        this.limpiarTabla('listaDetalleOrden');
        let idTipoItem = 0;
        let idTipoOrden = 0;
        if(tipoOrden== 'COMPRA'){
            idTipoItem=1;
            idTipoOrden=2;
        }else if(tipoOrden =='SERVICIO'){
            idTipoItem=2;
            idTipoOrden=3;

        }
        detalleOrdenList=[];
        $.ajax({
            type: 'POST',
            url: 'detalle-requerimiento-orden',
            data:{'requerimientoList':reqTrueList},
            dataType: 'JSON',
            success: (response)=>{
                response.det_req.forEach(element => {
                    if(element.cantidad >0 && (![28,5].includes(element.estado)) && element.id_tipo_item==idTipoItem){
                        detalleOrdenList.push(
                            {
                                'id': element.id,
                                'id_detalle_requerimiento': element.id_detalle_requerimiento,
                                'codigo_item': element.codigo_item,
                                'id_producto':element.id_producto,
                                'id_item': element.id_item,
                                'id_tipo_item': element.id_tipo_item,
                                'id_requerimiento':element.id_requerimiento,
                                'codigo_requerimiento': element.codigo_requerimiento,
                                'cantidad': element.cantidad,
                                'cantidad_a_comprar': element.cantidad_a_comprar?element.cantidad_a_comprar:element.cantidad,
                                'descripcion_producto':element.descripcion,
                                'descripcion_adicional':element.descripcion_adicional,
                                'estado': element.estado,
                                'fecha_registro':element.fecha_registro,
                                'id_unidad_medida':element.id_unidad_medida,
                                'lugar_entrega': element.lugar_entrega,
                                'observacion': element.observacion,
                                'part_number': element.part_number,
                                'precio_unitario':element.precio_unitario,
                                'stock_comprometido':element.stock_comprometido,
                                'subtotal':element.subtotal,
                                'unidad_medida':element.unidad_medida
                            }
                        );
                        if(detalleOrdenList.length ==0){
                            Swal.fire(
                                '',
                                'No puede generar una orden sin antes agregar item(s) base',
                                'info'
                            );
        
                        }else{
                            this.loadHeadRequerimiento(response.requerimiento[0],idTipoOrden);
                            this.listar_detalle_orden_requerimiento(detalleOrdenList);
                            // changeStateInput('form-crear-orden-requerimiento', false);
                            // changeStateButton('editar');
                            
                        }
                    }
                });
            }
        }).fail( ( jqXHR, textStatus, errorThrown )=>{
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }

    getTipoCambioCompra() {

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        let fechaHoy = now.toISOString().slice(0, 10)

        this.ordenCtrl.getTipoCambioCompra(fechaHoy).then(function (tipoCambioCompra) {
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
        var id_empresa = obj.options[obj.selectedIndex].getAttribute('data-id-empresa');
        var id_ubigeo = obj.options[obj.selectedIndex].getAttribute('data-id-ubigeo');
        var ubigeo_descripcion = obj.options[obj.selectedIndex].getAttribute('data-ubigeo-descripcion');
        var direccion = obj.options[obj.selectedIndex].getAttribute('data-direccion');
        this.changeLogoEmprsa(id_empresa);
        this.llenarUbigeo(direccion,id_ubigeo,ubigeo_descripcion);
    }

    llenarUbigeo(direccion,id_ubigeo,ubigeo_descripcion){
        document.querySelector("input[name='direccion_destino']").value=direccion;
        document.querySelector("input[name='id_ubigeo_destino']").value=id_ubigeo;
        document.querySelector("input[name='ubigeo_destino']").value=ubigeo_descripcion;
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

    handleChangeCondicion() {
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
        this.limpiarTabla('listaDetalleOrden');

        const that = this;
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
                                return '<input type="number" name="precio" data-id="' + (row.id) + '" placeholder="0.00" min="0"  class="form-control activation handleBlurUpdateInputPrecio" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '" value="' + (row.precio_unitario ? row.precio_unitario : "") + '" style="width:90px;" disabled/>';
                            } else {
                                return '<input type="number" name="precio" data-id="' + (row.id) + '" placeholder="0.00" min="0" class="form-control activation handleBlurUpdateInputPrecio" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '" data-producto-regalo="'+(row.producto_regalo?row.producto_regalo:false)+'" value="' + (row.precio_unitario ? row.precio_unitario : "") + '" style="width:90px;" disabled/>';
                            }
                        }, 'name': 'precio'
                },
                {
                    'render':
                         (data, type, row, meta)=> {
                            if (row.estado == 7) {
                                return '<input type="number" name="cantidad_a_comprar" data-id="' + (row.id) + '" min="0" class="form-control activation handleBlurUpdateInputCantidadAComprar" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '" value="' + (row.cantidad_a_comprar ? row.cantidad_a_comprar : row.cantidad) + '" style="width:70px;" disabled />';
                            } else {
                                that.updateInObjCantidadAComprar((row.row + 1), (row.id_requerimiento), (row.id_detalle_requerimiento), (row.cantidad));

                                return '<input type="number" name="cantidad_a_comprar" data-id="' + (row.id) + '" min="0" class="form-control activation handleBlurUpdateInputCantidadAComprar" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '" value="' + (row.cantidad_a_comprar ? row.cantidad_a_comprar : row.cantidad) + '" style="width:70px;" disabled/ >';
                            }
                        }, 'name': 'cantidad_a_comprar'
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return '<input type="number" name="subtotal" data-id="' + (row.id) + '" min="0" class="form-control activation handleBlurUpdateInputSubtotal" data-row="' + (meta.row + 1) + '" data-id_requerimiento="' + (row.id_requerimiento ? row.id_requerimiento : 0) + '" data-id_detalle_requerimiento="' + (row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0) + '" value="' + ((Math.round((row.cantidad * row.precio_unitario) * 100) / 100).toFixed(2)) + '" style="width:90px;" disabled />';

                        }, 'name': 'subtotal'
                },
                {
                    'render':
                        function (data, type, row, meta) {

                            let action = `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-danger btn-sm activation handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item"  data-id="${(row.id)}" data-key="${(row.id)}" data-row="${(meta.row)}" data-id_requerimiento="${(row.id_requerimiento ? row.id_requerimiento : 0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento ? row.id_detalle_requerimiento : 0)}" >
                                <i class="fas fa-trash fa-sm"></i>
                                </button>
                            </div>
                            `;

                            return action;
                        }
                }
            ],
            "initComplete": ()=> {
                this.updateAllSimboloMoneda();
                this.calcTotalOrdenDetalleList();

                
            },
            'rowCallback': (row, data) =>{
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




    openModalEliminarItemOrden(obj){
        var ask = confirm('Esta seguro que quiere anular el item ?');
        if (ask == true){
            this.eliminadoFilaTablaListaDetalleOrden(obj);
            let id= obj.dataset.id;
            if(id.length >0){

                    detalleOrdenList = detalleOrdenList.filter((item, i) => item.id != id);

                this.calcTotalOrdenDetalleList();
            }else{
                Swal.fire(
                    '',
                    'Hubo un problema al intentar anular el item.',
                    'error'
                );
            }
        }else{
            return false;
        }
    }

    calcTotalDetalleOrden(keySelected){
        let sizeInputTotal = document.querySelectorAll("div[name='subtotal']").length;
        for (let index = 0; index < sizeInputTotal; index++) {
            let key = document.querySelectorAll("div[name='subtotal']")[index].dataset.key;
            if(key == keySelected){
                let precio = document.querySelectorAll("input[name='precio']")[index].value?document.querySelectorAll("input[name='precio']")[index].value:0;
                let cantidad =document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value;
                let subtotal = (parseFloat(precio) * parseFloat(cantidad)).toFixed(2);
                document.querySelectorAll("div[name='subtotal']")[index].textContent=subtotal;
                    detalleOrdenList.forEach((element,index) => {
                        if(element.id == key){
                                detalleOrdenList[index].subtotal = subtotal;
                            
                        }
                    });
                
            }
        }
        
        this.calcTotalOrdenDetalleList();
    
    }

    calcTotalOrdenDetalleList(hasIGV =null){
        
        let sizeInputTotal = document.querySelectorAll("input[name='subtotal']").length;
        let total =0;
        let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;

        if (hasIGV == null){
            hasIGV= document.querySelector("input[name='incluye_igv']").checked;
        }

        if(hasIGV == true){
            for (let index = 0; index < sizeInputTotal; index++) {
                let num = document.querySelectorAll("input[name='subtotal']")[index].value?document.querySelectorAll("input[name='subtotal']")[index].value:0;
                total += parseFloat(num);
            }
    
            let montoNeto= (Math.round(total * 100) / 100).toFixed(2);
            let igv = (Math.round((total*0.18) * 100) / 100).toFixed(2);
            let montoTotal= (Math.round((parseFloat(montoNeto)+parseFloat(igv)) * 100) / 100).toFixed(2)
            document.querySelector("tfoot span[name='simboloMoneda']").textContent= simbolo_moneda_selected;
            document.querySelector("label[name='montoNeto']").textContent=montoNeto;
            document.querySelector("label[name='igv']").textContent= igv;
            document.querySelector("label[name='montoTotal']").textContent= montoTotal;
        }else if(hasIGV == false){
            for (let index = 0; index < sizeInputTotal; index++) {
                let num = document.querySelectorAll("input[name='subtotal']")[index].value?document.querySelectorAll("input[name='subtotal']")[index].value:0;
                total += parseFloat(num);
            }

            let montoNeto= (Math.round(total * 100) / 100).toFixed(2);
            let montoTotal= (Math.round((parseFloat(montoNeto)) * 100) / 100).toFixed(2)
            document.querySelector("tfoot span[name='simboloMoneda']").textContent= simbolo_moneda_selected;
            document.querySelector("label[name='montoNeto']").textContent=montoNeto;
            document.querySelector("label[name='igv']").textContent= '0.00';
            document.querySelector("label[name='montoTotal']").textContent= montoTotal;
        }
    
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
        this.ordenCtrl.getcatalogoProductos().then( (res)=> {
            this.listarItems(res);
        }).catch(function (err) {
            console.log(err)
        })

    }

    ocultarBtnCrearProducto() {
        cambiarVisibilidadBtn("btn-crear-producto", "ocultar");
    }

    listarItems(data) {
        let that = this;
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
                                let btnSeleccionar = `<button class="btn btn-success btn-xs handleClickSelectItem" data-id-producto="${row.id_producto}">Seleccionar</button>`;
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
            'initComplete': function () {

            },
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

    updateInputCantidadAComprar(event){
        let nuevoValor =event.target.value;
        let idSelected = event.target.dataset.id;
        let sizeInputCantidad = document.querySelectorAll("span[name='cantidad']").length;
        let cantidad =0;
        for (let index = 0; index < sizeInputCantidad; index++) {
            let id = document.querySelectorAll("span[name='cantidad']")[index].dataset.id;
            if(id == idSelected){
                cantidad = document.querySelectorAll("span[name='cantidad']")[index].textContent;
                if(parseFloat(nuevoValor) >0){                
                    // actualizar datadetreq cantidad
                    this.updateInObjCantidadAComprar(idSelected,nuevoValor);
                    this.calcTotalDetalleRequerimiento(idSelected);
    
                    // console.log(detalleOrdenList);
                    // 
                }
                
                // if(parseFloat(nuevoValor) > parseFloat(cantidad)){
                //     alert("La cantidad a comprar no puede ser mayor a la cantidad `solicitada");
                //     document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value= cantidad;
                //     updateInObjCantidadAComprar(rowNumberSelected,idRequerimientoSelected,idDetalleRequerimientoSelected,cantidad);
    
                // }
            }
        }
    }
    updateInObjCantidadAComprar(id,valor){
        detalleOrdenList.forEach((element,index) => {
                if(element.id == id){
                detalleOrdenList[index].cantidad_a_comprar = valor;
                }
        });
    }

    updateInputPrecio(event){
        let nuevoValor =event.target.value;
        let id = event.target.dataset.id;
        let isGift =(event.target.dataset.productoRegalo);
        if(isGift =='true'){
            if(nuevoValor>10){
                Swal.fire(
                    '',
                    'El precio fijado para un producto de regalo no puede ser mayor a 10.00',
                    'info'
                );
                event.target.value='';
            }else{
                this.updateInObjPrecioReferencial(id,nuevoValor);
                this.calcTotalDetalleRequerimiento(id);
            }
        }else{
            this.updateInObjPrecioReferencial(id,nuevoValor);
            this.calcTotalDetalleRequerimiento(id);
        }

    }

    updateInObjPrecioReferencial(id,valor){
        detalleOrdenList.forEach((element,index) => {
            if(element.id == id){
                detalleOrdenList[index].precio_unitario = valor;
            }

        });
    }


    calcTotalDetalleRequerimiento(id){
        let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;
        let sizeInputTotal = document.querySelectorAll("input[name='subtotal']").length;
        for (let index = 0; index < sizeInputTotal; index++) {
            let idElement = document.querySelectorAll("input[name='subtotal']")[index].dataset.id;
            if(idElement == id){
                let precio = document.querySelectorAll("input[name='precio']")[index].value?document.querySelectorAll("input[name='precio']")[index].value:0;
                let cantidad =( document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value)>0?document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value:document.querySelectorAll("input[name='cantidad']")[index].value;
                let calSubtotal =(parseFloat(precio) * parseFloat(cantidad));
                
                let subtotal = formatDecimalDigitos(calSubtotal,2);
                // console.log(subtotal);
                document.querySelectorAll("input[name='subtotal']")[index].value=subtotal;
                this.updateInObjSubtotal(id,subtotal);
            }
        }
        let total =0;
        for (let index = 0; index < sizeInputTotal; index++) {
            let num = document.querySelectorAll("input[name='subtotal']")[index].value?document.querySelectorAll("input[name='subtotal']")[index].value:0;
            total += parseFloat(num);
        }

        let montoNeto= total;
        let igv = (total*0.18);
        let montoTotal=  parseFloat(montoNeto)+parseFloat(igv);
        document.querySelector("tfoot span[name='simboloMoneda']").textContent= simbolo_moneda_selected;
        document.querySelector("label[name='montoNeto']").textContent=Util.formatoNumero(montoNeto, 2);
        document.querySelector("label[name='igv']").textContent= Util.formatoNumero(igv, 3);
        document.querySelector("label[name='montoTotal']").textContent= Util.formatoNumero(montoTotal, 2);
    }

    updateInObjSubtotal(id,valor){
        detalleOrdenList.forEach((element,index) => {
            if(element.id == id){
                detalleOrdenList[index].subtotal = valor;
            }
    });
    }

    updateInputSubtotal(event){
        let nuevoValor =event.target.value;
        let idSelected = event.target.dataset.id;
        let cantidadAComprar =0;
        let precio =0;
        let sizeInputCantidad = document.querySelectorAll("span[name='cantidad']").length;
        for (let index = 0; index < sizeInputCantidad; index++) {
            let id = document.querySelectorAll("input[name='cantidad_a_comprar']")[index].dataset.id;
            if(id == idSelected){
                cantidadAComprar = document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value;
                precio = document.querySelectorAll("input[name='precio']")[index].value;
                if(parseFloat(nuevoValor) >0){                
                    // actualizar datadetreq cantidad
                    let nuevoPrecio= (nuevoValor/cantidadAComprar)
                    this.updateInObjPrecioReferencial(id,nuevoPrecio);
                    document.querySelectorAll("input[name='precio']")[index].value=nuevoPrecio;
                    this.calcTotalDetalleRequerimiento(idSelected);

                }
                
 
            }
        }
    }

    selectItem(obj, idProducto) {
        let tr = obj.closest('tr');
        var idItem = tr.children[0].innerHTML;
        var idProd = tr.children[1].innerHTML;
        var idServ = tr.children[2].innerHTML;
        var idEqui = tr.children[3].innerHTML;
        var codigo = tr.children[4].innerHTML;
        var partNum = (tr.children[5].innerHTML)+'<br><span class="label label-default">Producto de regalo</span>';
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

        this.componerItemSeleccionado();

    }

    componerItemSeleccionado(){
        let data = {
            'id': this.makeId(),
            'cantidad': 1,
            'cantidad_a_comprar': 1,
            'codigo_item': null,
            'codigo_producto': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent,
            'codigo_requerimiento': "",
            'descripcion_adicional': null,
            'descripcion_producto': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent,
            'estado': 0,
            'garantia': null,
            'id_detalle_orden': null,
            'id_detalle_requerimiento': null,
            'id_item': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent,
            'id_tipo_item':1,
            'id_producto': parseInt(document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent),
            'id_requerimiento': null,
            'id_unidad_medida': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent,
            'lugar_despacho': null,
            'part_number':   parseInt(document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent)>0? (document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent):' (Sin mapear)',
            'precio_unitario': 0,
            'id_moneda': 1,
            'stock_comprometido': null,
            'subtotal': 0,
            'tiene_transformacion': false,
            'producto_regalo': true,
            'unidad_medida': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='unidad_medida']").textContent
            };
            this.agregarProductoADetalleOrdenList(data);
        
            $('#modal-catalogo-items').modal('hide');
    }

    makeId (){
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for ( var i = 0; i < 12; i++ ) {
          ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }

    agregarProductoADetalleOrdenList(data){
        if(typeof detalleOrdenList != 'undefined'){
            detalleOrdenList.push(data);
            this.listar_detalle_orden_requerimiento(detalleOrdenList);
    
        }else{
            alert("Hubo un problema al agregar el producto al Listado");
        }
    }

    vincularRequerimientoAOrdenModal() {
        $('#modal-vincular-requerimiento-orden').modal({
            show: true,
            backdrop: 'true',
            keyboard: true

        });

        this.ordenCtrl.getRequerimientosPendientes(null, null).then( (res)=> {
            this.ConstruirlistarRequerimientosPendientesParaVincularConOrden(res);

        }).catch( (err)=> {
            console.log(err)
        })

    }

    ConstruirlistarRequerimientosPendientesParaVincularConOrden(data) {
        const that = this;
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
                            let btnVerDetalle = `<button type="button" class="ver-detalle btn btn-default boton handleClickVerDetalleRequerimientoModalVincularRequerimiento" data-id-requerimiento="${row.id_requerimiento}"  data-toggle="tooltip" data-placement="bottom" title="Ver detalle requerimiento" data-id="${row.id_orden_compra}"> <i class="fas fa-chevron-down fa-sm"></i> </button>`;
                            let btnSeleccionar = `<button type="button" class="ver-detalle btn btn-success boton handleClickVincularRequerimiento" data-toggle="tooltip" data-placement="bottom" title="Seleccionar" data-id="${row.id_orden_compra}"> Seleccionar </button>`;
                            let containerCloseBrackets = `</div>`;
                            return (containerOpenBrackets + btnVerDetalle + btnSeleccionar + containerCloseBrackets);
                        }
                }

            ],
            'initComplete': function () {
                $('#listaRequerimientosParaVincular tbody').on("click","button.handleClickVerDetalleRequerimientoModalVincularRequerimiento",function(e){
                    that.verDetalleRequerimientoModalVincularRequerimiento(e.currentTarget);
                });
                $('#listaRequerimientosParaVincular tbody').on("click","button.handleClickVincularRequerimiento",function(e){
                    var data = $('#listaRequerimientosParaVincular').DataTable().row($(this).parents("tr")).data();
                    that.vincularRequerimiento(data.id_requerimiento);
                });
            },
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
        let tr = obj.closest('tr');
        var row = tablaListaRequerimientosParaVincular.row(tr);
        var id = obj.dataset.idRequerimiento;
        if (row.child.isShown()) {
            //  This row is already open - close it
            row.child.hide();
            tr.classList.remove('shown');
        }
        else {
            // Open this row
            //    row.child( format(iTableCounter, id) ).show();
            this.buildFormatModalVincularRequerimiento(iTableCounter, id, row);
            tr.classList.add('shown');
            // try datatable stuff
            oInnerTable = $('#listaRequerimientosParaVincular_' + iTableCounter).dataTable({
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

    buildFormatModalVincularRequerimiento(table_id, id, row) {
        this.ordenCtrl.obtenerDetalleRequerimientos(id).then((res)=> {
            this.construirDetalleRequerimientoModalVincularRequerimiento(table_id,row,res);
        }).catch(function(err) {
            console.log(err)
        })
    }

    construirDetalleRequerimientoModalVincularRequerimiento(table_id,row,response){
        var html = '';
        if (response.length > 0) {
            response.forEach( (element)=> {
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
        let i=0;
        this.ordenCtrl.obtenerDetalleRequerimientos(idRequerimiento).then((res)=> {
            res.forEach((element) => {
                i++;
                this.agregarProductoADetalleOrdenList({
                    'id': this.makeId(),
                    'cantidad': 1,
                    'cantidad_a_comprar': 1,
                    'codigo_item': null,
                    'codigo_producto': element.producto_codigo,
                    'codigo_requerimiento': element.codigo_requerimiento,
                    'descripcion_adicional': null,
                    'descripcion_producto': element.producto_descripcion !=null? element.producto_descripcion: element.descripcion,
                    'estado': 0,
                    'garantia': null,
                    'id_detalle_orden': null,
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_item':null,
                    'id_tipo_item':1,
                    'id_producto': element.id_producto,
                    'id_requerimiento': element.id_requerimiento,
                    'id_unidad_medida': element.id_unidad_medida,
                    'lugar_despacho': null,
                    'part_number':(!element.id_producto>0 ?'(Sin mapear)':(element.part_number?element.part_number:'')),
                    'precio_unitario': 0,
                    'id_moneda': 1,
                    'stock_comprometido': null,
                    'subtotal': 0,
                    'tiene_transformacion': false,
                    'unidad_medida': element.abreviatura
                    });
        });

        if(i>0){
            this.estadoVinculoRequerimiento({'mensaje':`Se agregó ${i} Item(s) a la orden`,'estado':'200'})
            
        }else{
            this.estadoVinculoRequerimiento({'mensaje':`No se puedo agregar Item(s) a la orden`,'estado':'204'})

        }



        }).catch(function(err) {
            console.log(err)
        })

    }

    estadoVinculoRequerimiento(resolve){
        if(resolve.estado == '200'){
            alert(resolve.mensaje);
            $('#modal-vincular-requerimiento-orden').modal('hide');
        }else{
            alert(resolve.mensaje);

        }


    }


    // mostrar info si esta vinculado con un requerimiento
    eliminarVinculoReq() {
        console.log('remove sessionStorage');
        sessionStorage.removeItem('reqCheckedList');
        sessionStorage.removeItem('tipoOrden');
        window.location.reload();
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

    incluyeIGV(e) {
        this.calcTotalOrdenDetalleList(e.currentTarget.checked);
    }

    save_orden(data, action) {
        let payload_orden = this.get_header_orden_requerimiento();
        payload_orden.detalle = (typeof detalleOrdenList != 'undefined') ? detalleOrdenList : detalleOrdenList;
        this.guardar_orden_requerimiento(action, payload_orden);
    }

    validaOrdenRequerimiento(){
        var codigo_orden = $('[name=codigo_orden]').val();
        var id_proveedor = $('[name=id_proveedor]').val();
        var plazo_entrega = $('[name=plazo_entrega]').val();
        var id_tp_documento = $('[name=id_tp_documento]').val();
        var msj = '';
        if (codigo_orden == ''){
            msj+='Es necesario que ingrese un código de orden Softlink.<br>';
        }
        if (id_proveedor == ''){
            msj+='Es necesario que seleccione un Proveedor.<br>';
        }
        if (id_tp_documento!= '3' && plazo_entrega == ''){
            msj+='Es necesario que ingrese un plazo de entrega.<br>';
        }
        let cantidadInconsistenteInputPrecio=0;
        let cantidadInconsistenteMapeoProducto=0;
        // let inputPrecio= document.querySelectorAll("table[id='listaDetalleOrden'] input[name='precio']");
        detalleOrdenList.forEach((element)=>{
            if(!parseFloat(element.precio_unitario) >0  && element.estado !=7){
                cantidadInconsistenteInputPrecio++;
            }
            if((element.id_tipo_item==1) && (element.id_producto =='' || element.id_producto ==null)){
                cantidadInconsistenteMapeoProducto++;
            }

        })
        if(cantidadInconsistenteInputPrecio>0){
            msj+='Es necesario que ingrese un precio / precio mayor a cero.<br>';
        }
        if(cantidadInconsistenteMapeoProducto>0){
            msj+='Tiene productos sin mapear.<br>';
        }

        let cantidadInconsistenteInputCantidadAComprar=0;
        let inputCantidadAComprar= document.querySelectorAll("table[id='listaDetalleOrden'] input[name='cantidad_a_comprar']");
        inputCantidadAComprar.forEach((element)=>{
            if(element.value == null || element.value =='' || element.value ==0){
                cantidadInconsistenteInputCantidadAComprar++;
            }
        })
        if(cantidadInconsistenteInputCantidadAComprar>0){
            msj+='Es necesario que ingrese una cantidad a comprar / cantidad a comprar mayor a cero.<br>';
    
        }           
        return  msj;
    }


    guardar_orden_requerimiento(action,data){
        console.log(action);
        console.log(data);
        if (action == 'register'){
            var msj = this.validaOrdenRequerimiento();
            if (msj.length > 0){
                Swal.fire({
                    title:'',
                    html:msj,
                    icon:'warning'
                }
                );
                // changeStateButton('editar');
                // changeStateButton('guardar');
                // $('#form-crear-orden-requerimiento').attr('type', 'register');
                // changeStateInput('form-crear-orden-requerimiento', false);
            } else{
                $.ajax({
                    type: 'POST',
                    url: 'guardar',
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        // console.log(response);
                        if (response > 0){
                            Lobibox.notify('success', {
                                title:false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Orden de registrada con éxito`
                            });
                            changeStateButton('guardar');
                            $('#form-crear-orden-requerimiento').attr('type', 'register');
                            changeStateInput('form-crear-orden-requerimiento', true);
    
                            sessionStorage.removeItem('reqCheckedList');
                            sessionStorage.removeItem('tipoOrden');
                            window.open("generar-orden-pdf/"+response, '_blank');
    
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    Swal.fire(
                        '',
                        'Hubo un problema al intentar guardar la orden, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        
        }else if(action == 'edition'){
            $.ajax({
                type: 'POST',
                url: 'actualizar',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    // console.log(response);
                    if (response > 0){
                        Lobibox.notify('success', {
                            title:false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `Orden de actualizada con éxito`
                        });
                        changeStateButton('guardar');
                        $('#form-crear-orden-requerimiento').attr('type', 'register');
                        changeStateInput('form-crear-orden-requerimiento', true);
                    }
                }
            }).fail( function(jqXHR, textStatus, errorThrown){
                Swal.fire(
                    '',
                    'Hubo un problema al intentar actualizar la orden, por favor vuelva a intentarlo',
                    'error'
                );
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });   
        }else{
            Swal.fire(
                '',
                'Hubo un error en la acción de la botonera, el action no esta definido',
                'error'
            );
        }
    }

    fechaHoy() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value = now.toISOString().slice(0, -1);
    };
    

    nuevaOrden() {
        $('#form-crear-orden-requerimiento')[0].reset();
        fechaHoy();

        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='cdc_req']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ejecutivo_responsable']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_destino']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_1']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_2']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_1']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_2']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] textarea[name='observacion']").value = '';
        // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='incluye_igv']").checked = true;
    
    
        this.limpiarTabla('listaDetalleOrden');
    }




    //  modal ordenes elaboradas 

    ordenesElaboradasModal(){
        $('#modal-ordenes-elaboradas').modal({
            show: true,
            backdrop: 'true'
        });
        this.listarOrdenesElaboradas();
        
    }
    
    listarOrdenesElaboradas(){
            let that = this;
            var vardataTables = funcDatatables();
            var tabla = $('#listaOrdenesElaboradas').DataTable({
                'processing':true,
                'destroy':true,
                'dom': vardataTables[1],
                'buttons': vardataTables[2],
                'language' : vardataTables[0],
                'ajax': 'listar-historial-ordenes-elaboradas',
                // "dataSrc":'',
                'order': [[1,'desc']],
                'scrollX': false,
                'columns': [
                    {'data': 'id_orden_compra'},
                    {'data': 'fecha'},
                    {'data': 'codigo'},
                    {'data': 'nro_documento'},
                    {'data': 'razon_social'},
                    {'data': 'moneda_simbolo'},
                    {'data': 'condicion'},
                    {'data': 'plazo_entrega'},
                    {'data': 'descripcion_sede_empresa'},
                    {'data': 'direccion_destino'},
                    {'data': 'ubigeo_destino'},
                    {'data': 'estado_doc'},
                    {
                        'render': (data, type, row)=> {

                            return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                            <button type="button" class="btn btn-xs btn-success handleClickSelectOrden" title="Seleccionar" >Seleccionar</button>
                            </div></center>
                            `;
                        }
                    },
                    
                ],
                'initComplete':  ()=> {
                    $('#listaOrdenesElaboradas tbody').on("click","button.handleClickSelectOrden", function(){
                        var data = $('#listaOrdenesElaboradas').DataTable().row($(this).parents("tr")).data();
                        that.selectOrden(data.id_orden_compra);
                    });
                },
                'columnDefs': [{ className: "text-right", 'aTargets': [0], 'sClass': 'invisible'}]
            });
        
    }
    
    // $('#listaOrdenesElaboradas tbody').on('click', 'tr', function(){
    //     if ($(this).hasClass('eventClick')){
    //         $(this).removeClass('eventClick');
    //     } else {
    //         $('#listaOrdenesElaboradas').dataTable().$('tr.eventClick').removeClass('eventClick');
    //         $(this).addClass('eventClick');
    //     }
    //     var idTr = $(this)[0].firstChild.innerHTML;
    //     $('.modal-footer #id_orden').text(idTr);
        
    // });
    
    selectOrden(idOrden){
        this.mostrarOrden(idOrden);
        changeStateInput('form-crear-orden-requerimiento', true);
        $('#modal-ordenes-elaboradas').modal('hide');
    }
    
    mostrarOrden(id){
        $.ajax({
            type: 'GET',
            url: 'mostrar-orden/'+id,
            dataType: 'JSON',
            success: (response)=>{
                // console.log(response);
                this.loadHeadOrden(response.head);
                this.listar_detalle_orden_requerimiento(response.detalle);
                detalleOrdenList= response.detalle;
                
                
            }
        }).fail(( jqXHR, textStatus, errorThrown )=>{
            Swal.fire(
                '',
                'Hubo un problema al intentar mostrar la orden, por favor vuelva a intentarlo.',
                'error'
            );
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }

    
    loadHeadOrden(data){
        // console.log(data);
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_orden']").value=data.id_orden_compra?data.id_orden_compra:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_documento']").value=data.id_tp_documento?data.id_tp_documento:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_moneda']").value=data.id_moneda?data.id_moneda:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent=data.codigo_orden?data.codigo_orden:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='codigo_orden']").value=data.codigo_softlink?data.codigo_softlink:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value=data.fecha?data.fecha.replace(" ","T"):'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_sede']").value=data.id_sede?data.id_sede:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] img[id='logo_empresa']").setAttribute("src",data.logo_empresa);
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='incluye_igv']").checked=data.incluye_igv;
        
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value=data.id_proveedor?data.id_proveedor:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value=data.id_contribuyente?data.id_contribuyente:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='razon_social']").value=data.razon_social?data.razon_social:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value=data.direccion_fiscal?data.direccion_fiscal:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value=data.ubigeo?data.ubigeo:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value=data.ubigeo_proveedor?data.ubigeo_proveedor:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value=data.id_contacto?data.id_contacto:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value=data.nombre_contacto?data.nombre_contacto:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value=data.telefono_contacto?data.telefono_contacto:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value=data.id_cta_principal?data.id_cta_principal:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nro_cuenta_principal_proveedor']").value=data.nro_cuenta?data.nro_cuenta:'';
        
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_condicion']").value=data.id_condicion?data.id_condicion:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_dias']").value=data.plazo_dias?data.plazo_dias:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_entrega']").value=data.plazo_entrega?data.plazo_entrega:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='cdc_req']").value=data.codigo_cc?data.codigo_cc:data.codigo_requerimiento;
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ejecutivo_responsable']").value=data.nombre_responsable_cc?data.nombre_responsable_cc:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_doc']").value=data.id_tp_doc?data.id_tp_doc:'';
    
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_destino']").value=data.direccion_destino?data.direccion_destino:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value=data.ubigeo_destino_id?data.ubigeo_destino_id:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_destino']").value=data.ubigeo_destino?data.ubigeo_destino:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_1']").value=data.personal_autorizado_1?data.personal_autorizado_1:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_2']").value=data.personal_autorizado_2?data.personal_autorizado_2:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_1']").value=data.nombre_personal_autorizado_1?data.nombre_personal_autorizado_1:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_2']").value=data.nombre_personal_autorizado_2?data.nombre_personal_autorizado_2:'';
        document.querySelector("form[id='form-crear-orden-requerimiento'] textarea[name='observacion']").value=data.observacion?data.observacion:'';
    }


    anularOrden(id){
        this.ordenCtrl.anularOrden(id).then((res)=> {
            if (res.status == 200) {
                Lobibox.notify('success', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Orden anulada'
                });
                let url = "/logistica/gestion-logistica/compras/ordenes/listado/index";
                window.location.replace(url);
            } else {
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar anular la orden, por favor vuelva a intentarlo',
                    'error'
                );
                console.log(res);
            }
        }).catch( (err)=> {
            console.log(err)
            Swal.fire(
                '',
                'Lo sentimos hubo un error en el servidor, por favor vuelva a intentarlo',
                'error'
            );
        });
    }
}

