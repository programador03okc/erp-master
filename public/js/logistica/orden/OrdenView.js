
// ============== View =========================
var vardataTables = funcDatatables();
var simboloMoneda = '';
var tablaListaRequerimientosParaVincular;
var $tablaHistorialOrdenesElaboradas;
var $tablaListaCatalogoProductos;
var detalleOrdenList = [];
var iTableCounter = 1;
var oInnerTable;
var actionPage = null;
class OrdenView {
    constructor(ordenCtrl) {
        this.ordenCtrl = ordenCtrl;
        this.codigoRequerimientoList = [];
    }

    init() {
        var reqTrueList = JSON.parse(sessionStorage.getItem('reqCheckedList'));
        var tipoOrden = sessionStorage.getItem('tipoOrden');
        if (reqTrueList != undefined && reqTrueList != null && (reqTrueList.length > 0)) {
            // ordenView.changeStateInput('form-crear-orden-requerimiento', false);
            // ordenView.changeStateButton('editar');
            this.obtenerRequerimiento(reqTrueList, tipoOrden);
            let btnVinculoAReq = `<span class="text-info" id="text-info-req-vinculado" > <a onClick="window.location.reload();" style="cursor:pointer;" title="Recargar con Valores Iniciales del Requerimiento">(vinculado a un Requerimiento)</a> <span class="badge label-danger handleClickEliminarVinculoReq" style="position: absolute;margin-top: -5px;margin-left: 5px; cursor:pointer" title="Eliminar vínculo">×</span></span>`;
            document.querySelector("section[class='content-header']").children[0].innerHTML += btnVinculoAReq;
            sessionStorage.removeItem('reqCheckedList');
            sessionStorage.removeItem('tipoOrden');
        }
        var idOrden = sessionStorage.getItem('idOrden');
        actionPage = sessionStorage.getItem('action');
        // sessionStorage.removeItem('action');

        if (idOrden > 0) {
            this.mostrarOrden(idOrden);
            sessionStorage.removeItem('idOrden');
            sessionStorage.removeItem('action');
        }

        this.getTipoCambioCompra();

    }

    setStatusPage() {
        if (actionPage != undefined && actionPage != null) {
            // console.log(actionPage);
            switch (actionPage) {
                case 'register':
                    changeStateButton('nuevo');
                    changeStateInput('form-crear-orden-requerimiento', false);

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
        } else {
            changeStateButton('inicio');
            $("#form-crear-orden-requerimiento .activation").attr('disabled', true);

        }
    }

    initializeEventHandler() {

        $('#modal-proveedores').on("click", "button.handleClickCrearProveedor", () => {
            this.irACrearProveedor();
        });

        $('#form-crear-orden-requerimiento').on("click", "button.handleClickFechaHoy", () => {
            this.fechaHoy();
        });
        $('#form-crear-orden-requerimiento').on("click", "button.handleClickImprimirOrdenPdf", () => {
            this.imprimirOrdenPDF();
        });
        $('#form-crear-orden-requerimiento').on("change", "select.handleChangeSede", (e) => {
            this.changeSede(e.currentTarget);
        });
        $('#form-crear-orden-requerimiento').on("change", "select.handleChangeCondicion", () => {
            this.handleChangeCondicion();
        });
        $('#form-crear-orden-requerimiento').on("change", "select.handleChangeMoneda", (e) => {
            this.changeMoneda(e.currentTarget);
        });
        $('#form-crear-orden-requerimiento').on("click", "input.handleClickIncluyeIGV", () => {
            this.calcularMontosTotales();
        });

        $('#form-crear-orden-requerimiento').on("click", "button.handleClickCatalogoProductosModal", () => {
            this.catalogoProductosModal();
        });

        $('#form-crear-orden-requerimiento').on("click", "button.handleClickAgregarServicio", () => {
            this.agregarServicio();
        });
        $('#form-crear-orden-requerimiento').on("click", "button.handleClickVincularRequerimientoAOrdenModal", () => {
            this.vincularRequerimientoAOrdenModal();
        });
        $('#listaCatalogoProductos tbody').on("click", "button.handleClickSelectObsequio", (e) => {
            this.selectObsequio(e.currentTarget);
        });



        $('#listaDetalleOrden tbody').on("blur", "input.handleBlurUpdateInputCantidadAComprar", (e) => {
            this.updateInputCantidadAComprar(e);
        });

        $('#listaDetalleOrden tbody').on("blur", "input.handleBlurUpdateInputSubtotal", (e) => {
            this.updateInputSubtotal(e);
        });


        $('#listaDetalleOrden tbody').on("click", "button.handleClickOpenModalEliminarItemOrden", (e) => {
            this.openModalEliminarItemOrden(e.currentTarget);
        });

        $('#listaDetalleOrden tbody').on("blur", "input.handleBurUpdateSubtotal", (e) => {
            this.updateSubtotal(e.target);
        });

        $('#listaOrdenesElaboradas tbody').on("click", "button.handleClickSelectOrden", (e) => {
            this.selectOrden(e.currentTarget.dataset.idOrden);
        });

        $('#listaRequerimientosParaVincular tbody').on("click", "button.handleClickVerDetalleRequerimientoModalVincularRequerimiento", (e) => {
            this.verDetalleRequerimientoModalVincularRequerimiento(e.currentTarget);
        });
        $('#listaRequerimientosParaVincular tbody').on("click", "button.handleClickVincularRequerimiento", (e) => {
            this.vincularRequerimiento(e.currentTarget);
        });
    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if (nodeTbody != null) {
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }

        }
    }

    irACrearProveedor() {
        let url = "/logistica/gestion-logistica/proveedores/index?accion=nuevo";
        var win = window.open(url, '_blank');
    }

    imprimirOrdenPDF() {
        let idOrden = document.querySelector("input[name='id_orden']").value;
        if (parseInt(idOrden) > 0) {
            window.open("generar-orden-pdf/" + idOrden, '_blank');

        } else {
            Swal.fire(
                '',
                'Lo sentimos no se encontro una orden vinculada para imprimir',
                'warning'
            );
        }

    }

    obtenerRequerimiento(reqTrueList, tipoOrden) {
        this.limpiarTabla('listaDetalleOrden');
        let idTipoItem = 0;
        let idTipoOrden = 0;
        if (tipoOrden == 'COMPRA') {
            idTipoItem = 1;
            idTipoOrden = 2;
        } else if (tipoOrden == 'SERVICIO') {
            idTipoItem = 2;
            idTipoOrden = 3;

        }
        detalleOrdenList = [];
        $.ajax({
            type: 'POST',
            url: 'requerimiento-detallado',
            data: { 'requerimientoList': reqTrueList },
            dataType: 'JSON',
            success: (response) => {
                console.log(response);
                response.forEach(req => {
                    req.detalle.forEach(det => {
                        if (det.cantidad > 0 && (![28, 5].includes(det.estado)) && det.id_tipo_item == idTipoItem) {
                            let cantidad_atendido_almacen = 0;
                            if (det.reserva.length > 0) {
                                (det.reserva).forEach(reserva => {
                                    if (reserva.estado == 1) {
                                        cantidad_atendido_almacen += parseFloat(reserva.stock_comprometido);
                                    }
                                });
                            }
                            let cantidad_atendido_orden = 0;
                            if (det.ordenes_compra.length > 0) {
                                (det.ordenes_compra).forEach(orden => {
                                    cantidad_atendido_orden += parseFloat(orden.cantidad);
                                });
                            }
                            let cantidadAAtender = (parseFloat(det.cantidad) - cantidad_atendido_almacen - cantidad_atendido_orden);
                            if (det.tiene_transformacion == false) {
                                detalleOrdenList.push(
                                    {
                                        'id': det.id,
                                        'id_detalle_requerimiento': det.id_detalle_requerimiento,
                                        'id_producto': det.id_producto,
                                        'id_tipo_item': det.id_tipo_item,
                                        'id_requerimiento': det.id_requerimiento,
                                        'codigo_requerimiento': req.codigo,
                                        'id_moneda': req.id_moneda,
                                        'cantidad': det.cantidad,
                                        'cantidad_a_comprar': !(cantidadAAtender >= 0) ? '' : cantidadAAtender,
                                        'cantidad_atendido_almacen': cantidad_atendido_almacen,
                                        'cantidad_atendido_orden': cantidad_atendido_orden,
                                        'descripcion_producto': det.producto !=null? det.producto.descripcion:'',
                                        'codigo_producto': det.producto !=null? det.producto.codigo:'',
                                        'part_number': det.producto !=null? det.producto.part_number:'',
                                        'codigo_softlink': det.producto !=null? det.producto.cod_softlink:'',
                                        'descripcion': det.descripcion,
                                        'estado': det.estado.id_estado_doc,
                                        'fecha_registro': det.fecha_registro,
                                        'id_unidad_medida': det.id_unidad_medida,
                                        'lugar_entrega': det.lugar_entrega,
                                        'observacion': det.observacion,
                                        'precio_unitario': det.precio_unitario,
                                        'stock_comprometido': cantidad_atendido_almacen,
                                        'subtotal': det.subtotal,
                                        'unidad_medida': det.unidad_medida.abreviatura
                                    }
                                );
                            }

                        }
                    });
                });
                if (detalleOrdenList.length == 0) {
                    Swal.fire(
                        '',
                        'No puede generar una orden sin antes agregar item(s) base',
                        'info'
                    );

                } else {
                    this.loadHeadRequerimiento(response, idTipoOrden);
                    this.listarDetalleOrdeRequerimiento(detalleOrdenList);
                    this.setStatusPage();


                }
            }
        }).fail((jqXHR, textStatus, errorThrown) => {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

        // sessionStorage.removeItem('reqCheckedList');
        // sessionStorage.removeItem('tipoOrden');
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
        let simboloMonedaAll = document.querySelectorAll("[name='simboloMoneda']");
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
        this.llenarUbigeo(direccion, id_ubigeo, ubigeo_descripcion);
    }

    llenarUbigeo(direccion, id_ubigeo, ubigeo_descripcion) {
        document.querySelector("input[name='direccion_destino']").value = direccion;
        document.querySelector("input[name='id_ubigeo_destino']").value = id_ubigeo;
        document.querySelector("input[name='ubigeo_destino']").value = ubigeo_descripcion;
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

        let condicion_softlink = document.getElementsByName('id_condicion_softlink')[0];
        // let descripcion_condicion_softlink = condicion_softlink.options[condicion_softlink.selectedIndex].text;
        let dias_condicion_softlink = condicion_softlink.options[condicion_softlink.selectedIndex].dataset.dias;

        if(dias_condicion_softlink>0){
            document.getElementsByName('id_condicion')[0].value = 2;
            document.getElementsByName('plazo_dias')[0].value = dias_condicion_softlink;

        }else{
            document.getElementsByName('id_condicion')[0].value = 1;
            document.getElementsByName('plazo_dias')[0].value = 0;

        }

        // if (text_condicion == 'CONTADO CASH' || text_condicion == 'Contado cash') {
        //     document.getElementsByName('plazo_dias')[0].value = null;
        //     document.getElementsByName('plazo_dias')[0].setAttribute('class', 'form-control activation group-elemento invisible');
        //     document.getElementsByName('text_dias')[0].setAttribute('class', 'form-control group-elemento invisible');
        // } else if (text_condicion == 'CREDITO' || text_condicion == 'Crédito') {
        //     document.getElementsByName('plazo_dias')[0].setAttribute('class', 'form-control activation group-elemento');
        //     document.getElementsByName('text_dias')[0].setAttribute('class', 'form-control group-elemento');

        // }
    }

    loadHeadRequerimiento(data, idTipoOrden) {
        if (idTipoOrden == 3) { // orden de servicio
            this.ocultarBtnCrearProducto();
        }
        // let codigoRequerimiento = [];
        data.forEach(element => {
            let foundRequerimiento = this.codigoRequerimientoList.find(item => item == element.codigo);
            if (foundRequerimiento == undefined) {

                this.codigoRequerimientoList.push(element.codigo);

            }
        });

        document.querySelector("select[name='id_tp_documento']").value = idTipoOrden;
        document.querySelector("img[id='logo_empresa']").setAttribute("src", data[0].empresa.logo_empresa);
        document.querySelector("input[name='cdc_req']").value = this.codigoRequerimientoList.length > 0 ? this.codigoRequerimientoList : '';
        document.querySelector("input[name='ejecutivo_responsable']").value = '';
        document.querySelector("input[name='direccion_destino']").value = data[0].sede ? data[0].sede.direccion : '';
        document.querySelector("input[name='id_ubigeo_destino']").value = data[0].sede ? data[0].sede.id_ubigeo : '';
        document.querySelector("input[name='ubigeo_destino']").value = data[0].sede ? data[0].sede.ubigeo_completo : '';
        document.querySelector("select[name='id_sede']").value = data[0].id_sede ? data[0].id_sede : '';
        document.querySelector("select[name='id_moneda']").value = data[0].id_moneda ? data[0].id_moneda : 1;
        document.querySelector("input[name='id_cc']").value = data[0].id_cc ? data[0].id_cc:'';
        document.querySelector("textarea[name='observacion']").value = '';

        this.updateAllSimboloMoneda();

    }

    listarDetalleOrdeRequerimiento(data) {
        this.limpiarTabla('listaDetalleOrden');
        vista_extendida();

        for (let i = 0; i < data.length; i++) {
            if (data[i].id_tipo_item == 1) { // producto
                if (data[i].id_producto > 0) {
                    document.querySelector("tbody[id='body_detalle_orden']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;" class="${data[i].estado == 7 ? 'danger textRedStrikeHover' : ''};">
                        <td class="text-center">${data[i].codigo_requerimiento ? data[i].codigo_requerimiento : ''} <input type="hidden"  name="idRegister[]" value="${data[i].id_detalle_orden ? data[i].id_detalle_orden : this.makeId()}"> <input type="hidden"  class="idEstado" name="idEstado[]"> <input type="hidden"  name="idDetalleRequerimiento[]" value="${data[i].id_detalle_requerimiento ? data[i].id_detalle_requerimiento : ''}">  <input type="hidden"  name="idTipoItem[]" value="1"></td>
                        <td class="text-center">${data[i].codigo_producto ? data[i].codigo_producto : ''} </td>
                        <td class="text-center">${data[i].codigo_softlink ? data[i].codigo_softlink : ''} </td>
                        <td class="text-center">${data[i].part_number ? data[i].part_number : ''} <input type="hidden"  name="idProducto[]" value="${(data[i].id_producto ? data[i].id_producto : data[i].id_producto)}"></td>
                        <td class="text-left">${(data[i].descripcion_producto ? data[i].descripcion_producto : (data[i].descripcion != null ? data[i].descripcion : ''))} <input type="hidden"  name="descripcion[]" value="${(data[i].descripcion_producto ? data[i].descripcion_producto : data[i].descripcion)} "></td>
                        <td><select name="unidad[]" class="form-control ${(data[i].estado_guia_com_det > 0 && data[i].estado_guia_com_det != 7 ? '' : 'activation')} input-sm unidadMedida" data-valor="${data[i].id_unidad_medida}" disabled>${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                        <td>${(data[i].cantidad ? data[i].cantidad : '')}</td>
                        <td>${(data[i].cantidad_atendido_almacen ? data[i].cantidad_atendido_almacen : '')}</td>
                        <td>${(data[i].cantidad_atendido_orden ? data[i].cantidad_atendido_orden : '')}</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</div>
                                <input class="form-control precio input-sm text-right ${(data[i].estado_guia_com_det > 0 && data[i].estado_guia_com_det != 7 ? '' : 'activation')}  handleBurUpdateSubtotal" data-id-tipo-item="1" data-producto-regalo="${(data[i].producto_regalo ? data[i].producto_regalo : false)}" type="number" min="0" name="precioUnitario[]"  placeholder="" value="${data[i].precio_unitario ? data[i].precio_unitario : 0}" disabled>
                            </div>
                        </td>
                        <td>
                            <input class="form-control cantidad_a_comprar input-sm text-right ${(data[i].estado_guia_com_det > 0 && data[i].estado_guia_com_det != 7 ? '' : 'activation')}  handleBurUpdateSubtotal"  data-id-tipo-item="1" type="number" min="0" name="cantidadAComprarRequerida[]"  placeholder="" value="${data[i].cantidad_a_comprar ? data[i].cantidad_a_comprar : 0}" disabled>
                        </td>
                        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm ${(data[i].estado_guia_com_det > 0 && data[i].estado_guia_com_det != 7 ? '' : 'activation')} handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" disabled>
                            <i class="fas fa-trash fa-sm"></i>
                            </button>
                        </td>
                    </tr>`);

                }
            } else { //servicio
                document.querySelector("tbody[id='body_detalle_orden']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;" class="${data[i].estado == 7 ? 'danger textRedStrikeHover' : ''};">
                    <td>${data[i].codigo_requerimiento ? data[i].codigo_requerimiento : ''} <input type="hidden"  name="idRegister[]" value="${data[i].id_detalle_orden ? data[i].id_detalle_orden : this.makeId()}"><input type="hidden"  class="idEstado" name="idEstado[]"> <input type="hidden"  name="idDetalleRequerimiento[]" value="${data[i].id_detalle_requerimiento ? data[i].id_detalle_requerimiento : ''}"> <input type="hidden"  name="idTipoItem[]" value="1"></td>
                    <td>(No aplica) <input type="hidden" value=""></td>
                    <td>(No aplica) <input type="hidden" value=""></td>
                    <td>(No aplica) <input type="hidden"  name="idProducto[]" value=""></td>
                    <td><textarea name="descripcion[]" placeholder="Descripción" class="form-control descripcion_servicio activation" value="${(data[i].descripcion ? data[i].descripcion : '')}" style="width:100%;height: 60px;overflow: scroll;"> </textarea> </td>
                    <td><select name="unidad[]" class="form-control activation input-sm" value="${data[i].id_unidad_medida}" disabled>${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                    <td>${(data[i].cantidad ? data[i].cantidad : '')}</td>
                    <td></td>
                    <td></td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</div>
                            <input class="form-control precio input-sm text-right activation  handleBurUpdateSubtotal" data-id-tipo-item="2" type="number" min="0" name="precioUnitario[]"  placeholder="" value="${data[i].precio_unitario ? data[i].precio_unitario : 0}" disabled>
                        </div>
                    </td>
                    <td>
                        <input class="form-control cantidad_a_comprar input-sm text-right activation handleBurUpdateSubtotal" data-id-tipo-item="2" type="number" min="0" name="cantidadAComprarRequerida[]"  placeholder="" value="${data[i].cantidad_a_comprar ? data[i].cantidad_a_comprar : ''}" disabled>
                    </td>
                    <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm activation handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" disabled>
                        <i class="fas fa-trash fa-sm"></i>
                        </button>
                    </td>
                </tr>`);
            }

        }
        this.autoUpdateSubtotal();
        this.UpdateSelectUnidadMedida();

    }

    autoUpdateSubtotal() {
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_orden']").children;
        for (let i = 0; i < tbodyChildren.length; i++) {
            this.updateSubtotal(tbodyChildren[i]);
        }
    }

    updateSubtotal(obj) {
        let tr = obj.closest("tr");
        let isGift = (tr.querySelector("input[class~='precio']").dataset.productoRegalo);
        let cantidad = parseFloat(tr.querySelector("input[class~='cantidad_a_comprar']").value);
        let precioUnitario = parseFloat(tr.querySelector("input[class~='precio']").value);
        let subtotal = (cantidad * precioUnitario);

        if (isGift == 'true') {
            if (subtotal > 10) {
                Swal.fire(
                    '',
                    'El precio fijado para un obsequio no puede ser mayor a 10.00',
                    'info'
                );
                tr.querySelector("input[class~='precio']").value = 0;
                subtotal = 0;
            }
        }

        tr.querySelector("span[class='subtotal']").textContent = $.number(subtotal, 2);
        this.calcularMontosTotales();
    }

    calcularMontosTotales() {
        let TableTBody = document.querySelector("tbody[id='body_detalle_orden']");
        let childrenTableTbody = TableTBody.children;

        let totalNeto = 0;
        for (let index = 0; index < childrenTableTbody.length; index++) {
  
            if(childrenTableTbody[index].classList.contains("danger") ==false){
                
                let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad_a_comprar']").value ? childrenTableTbody[index].querySelector("input[class~='cantidad_a_comprar']").value : 0);
                let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value ? childrenTableTbody[index].querySelector("input[class~='precio']").value : 0);
                totalNeto += (cantidad * precioUnitario);
            }
        }

        this.updateSimboloMoneda();
        document.querySelector("label[name='montoNeto']").textContent = $.number((totalNeto), 2);

        let incluyeIGV = document.querySelector("input[name='incluye_igv']").checked;
        if (incluyeIGV == true) {
            let igv = (Math.round((totalNeto * 0.18) * 100) / 100).toFixed(2);
            let MontoTotal = (Math.round((parseFloat(totalNeto) + parseFloat(igv)) * 100) / 100).toFixed(2)
            document.querySelector("label[name='igv']").textContent = $.number(igv, 2);
            document.querySelector("label[name='montoTotal']").textContent = $.number(MontoTotal, 2);

        } else {
            let MontoTotal = parseFloat(totalNeto);
            document.querySelector("label[name='igv']").textContent = $.number(0, 2);
            document.querySelector("label[name='montoTotal']").textContent = $.number(MontoTotal, 2);
        }

    }


    updateSimboloMoneda() {
        let simboloMonedaPresupuestoUtilizado = document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if (allSelectorSimboloMoneda.length > 0) {
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent = simboloMonedaPresupuestoUtilizado;
            });
        }
    }


    openModalEliminarItemOrden(obj) {
        // this.calcTotalOrdenDetalleList();
        Swal.fire({
            title: 'Esta seguro?',
            text: "No podrás revertir esta acción",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'cancelar',
            confirmButtonText: 'Si, eliminar'

        }).then((result) => {
            if (result.isConfirmed) {
                let tr = obj.closest("tr");
                var regExp = /[a-zA-Z]/g;

                if(regExp.test(tr.querySelector("input[name='idRegister[]']").value) ==true ){
                    tr.remove();
                    this.calcularMontosTotales();
    
                }else{
                    tr.querySelector("input[class~='idEstado']").value=7;
                    tr.classList.add("danger","textRedStrikeHover");
                    tr.querySelector("button[name='btnOpenModalEliminarItemOrden']").setAttribute("disabled",true);
                    this.calcularMontosTotales(); // considere las filas anuladas
                }

                Lobibox.notify('success', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'El item fue eliminado'
                });
            }
        })


    }





    // modal agregar producto en orden 
    catalogoProductosModal() {
        document.querySelector("div[id='modal-catalogo-items'] h3[class='modal-title']").textContent = "Lista de productos para obsequio";
        $('#modal-catalogo-items').modal({
            show: true,
            backdrop: 'true',
            keyboard: true

        });
        this.limpiarTabla('listaItems');
        this.ocultarBtnCrearProducto();
        this.listarCatalogoProductos();
    

    }

    ocultarBtnCrearProducto() {
        cambiarVisibilidadBtn("btn-crear-producto", "ocultar");
    }

    listarCatalogoProductos(){
        $tablaListaCatalogoProductos = $('#listaCatalogoProductos').DataTable({
            'dom': 'Bfrtip',
            'language': vardataTables[0],
            'order': [[5, 'asc']],
            'serverSide': true,
            'processing': false,
            'destroy': true,
            'ajax': {
                'url': 'mostrar-catalogo-productos',
                'type': 'POST',
                beforeSend: data => {

                    $("#listaCatalogoProductos").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                }

            },
            'columns': [
                { 'data': 'codigo', 'name': 'alm_prod.codigo' },
                { 'data': 'cod_softlink', 'name': 'alm_prod.cod_softlink' },
                { 'data': 'part_number', 'name': 'alm_prod.part_number' },
                { 'data': 'descripcion', 'name':'alm_prod.descripcion' },
                { 'data': 'abreviatura_unidad_medida', 'name': 'alm_und_medida.abreviatura' },
                { 'data': 'id_producto', 'name': 'alm_prod.id_producto', "searchable": false }

            ],
            'initComplete': function () {
                //Boton de busqueda
                const $filter = $('#listaCatalogoProductos_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaCatalogoProductos.search($input.val()).draw();
                })
                //Fin boton de busqueda
            },
            "drawCallback": function (settings) {
                //Botón de búsqueda
                $('#listaCatalogoProductos_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaCatalogoProductos_filter input').trigger('focus');
                //fin botón búsqueda
                if ($tablaListaCatalogoProductos.rows().data().length == 0) {
                    Lobibox.notify('info', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: `No se encontro data disponible para mostrar`
                    });
                }
                //Botón de búsqueda
                $('#listaCatalogoProductos_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaCatalogoProductos_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaCatalogoProductos").LoadingOverlay("hide", true);
            },
            'columnDefs': [
                { 'aTargets': [0], 'className': "text-center", 'sWidth': '5%' },
                { 'aTargets': [1], 'className': "text-center", 'sWidth': '5%' },
                { 'aTargets': [2], 'className': "text-center", 'sWidth': '5%' },
                { 'aTargets': [3], 'className': "text-left", 'sWidth': '40%' },
                { 'aTargets': [4], 'className': "text-center", 'sWidth': '5%' },
                {
                    'render':
                    function (data, type, row) {
                        if (row.id_unidad_medida == 1) {
                        let btnSeleccionar = `<button class="btn btn-success btn-xs handleClickSelectObsequio" 
                        data-id-producto="${row.id_producto}"
                        data-codigo="${row.codigo}"
                        data-codigo-softlink="${row.cod_softlink}"
                        data-part-number="${row.part_number}"
                        data-descripcion="${row.descripcion}"
                        data-unidad-medida="${row.abreviatura_unidad_medida}"
                        data-id-unidad-medida="${row.id_unidad_medida}"
                        
                        >Seleccionar</button>`;
                        // let btnVerSaldo = `<button class="btn btn-sm btn-info" onClick="verSaldoProducto('${row.id_producto}');">Stock</button>')`;
                        return btnSeleccionar;

                        } else {
                        return '';
                        }
                    },targets: 5, className: "text-center", sWidth: '8%'
                }
            ]

        });
    }



    calcTotalDetalleRequerimiento(id) {
        let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;
        let sizeInputTotal = document.querySelectorAll("input[name='subtotal']").length;
        for (let index = 0; index < sizeInputTotal; index++) {
            let idElement = document.querySelectorAll("input[name='subtotal']")[index].dataset.id;
            if (idElement == id) {
                let precio = document.querySelectorAll("input[name='precio']")[index].value ? document.querySelectorAll("input[name='precio']")[index].value : 0;
                let cantidad = (document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value) > 0 ? document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value : document.querySelectorAll("input[name='cantidad']")[index].value;
                let calSubtotal = (parseFloat(precio) * parseFloat(cantidad));

                let subtotal = formatDecimalDigitos(calSubtotal, 2);
                // console.log(subtotal);
                document.querySelectorAll("input[name='subtotal']")[index].value = subtotal;
                this.updateInObjSubtotal(id, subtotal);
            }
        }
        let total = 0;
        for (let index = 0; index < sizeInputTotal; index++) {
            let num = document.querySelectorAll("input[name='subtotal']")[index].value ? document.querySelectorAll("input[name='subtotal']")[index].value : 0;
            total += parseFloat(num);
        }

        let montoNeto = total;
        let igv = (total * 0.18);
        let montoTotal = parseFloat(montoNeto) + parseFloat(igv);
        document.querySelector("tfoot span[name='simboloMoneda']").textContent = simbolo_moneda_selected;
        document.querySelector("label[name='montoNeto']").textContent = $.number(montoNeto, 2);
        document.querySelector("label[name='igv']").textContent = $.number(igv, 2);
        document.querySelector("label[name='montoTotal']").textContent = $.number(montoTotal, 2);
    }

    updateInObjSubtotal(id, valor) {
        detalleOrdenList.forEach((element, index) => {
            if (element.id == id) {
                detalleOrdenList[index].subtotal = valor;
            }
        });
    }



    selectObsequio(obj) {
        let tr = obj.closest('tr');

        this.agregarProducto([{
            'id': this.makeId(),
            'cantidad': null,
            'cantidad_a_comprar': 1,
            'codigo_producto': obj.dataset.codigo,
            'codigo_softlink': obj.dataset.codigoSoftlink,
            'codigo_requerimiento': null,
            'descripcion_producto': obj.dataset.descripcion,
            'descripcion': null,
            'estado': 0,
            'garantia': null,
            'id_detalle_orden': null,
            'id_detalle_requerimiento': null,
            'id_tipo_item': 1,
            'id_producto': obj.dataset.idProducto,
            'id_requerimiento': null,
            'id_unidad_medida': obj.dataset.idUnidadMedida,
            'lugar_despacho': null,
            'part_number': obj.dataset.partNumber+'<br><span class="label label-default">Obsequio</span>',
            'precio_unitario': 0,
            'id_moneda': 1,
            'stock_comprometido': null,
            'subtotal': 0,
            'tiene_transformacion': false,
            'producto_regalo': true,
            'unidad_medida': obj.dataset.unidadMedida
        }],'OBSEQUIO');
        $('#modal-catalogo-items').modal('hide');

    }

    UpdateSelectUnidadMedida() {
        const AllTrTbodyListaDetalleOrden = document.querySelectorAll("table[id='listaDetalleOrden'] tbody tr");
        AllTrTbodyListaDetalleOrden.forEach(fila => {
            if(fila.querySelector("select[class~='unidadMedida']")){
                let valorUnidad = fila.querySelector("select[class~='unidadMedida']").dataset.valor;
                fila.querySelector("select[class~='unidadMedida']").value = valorUnidad;
            }
        });
    }

    agregarProducto(data, tipo) { // tipo puede ser OBSEQUIO, DETALLE_REQUERIMIENTO
        vista_extendida();
        document.querySelector("tbody[id='body_detalle_orden']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;">
        <td class="text-center">${data[0].codigo_requerimiento ? data[0].codigo_requerimiento : ''} <input type="hidden"  name="idRegister[]" value="${data[0].id_detalle_orden ? data[0].id_detalle_orden : this.makeId()}"> <input type="hidden"  class="idEstado" name="idEstado[]"> <input type="hidden"  name="idDetalleRequerimiento[]" value="${data[0].id_detalle_requerimiento ? data[0].id_detalle_requerimiento : ''}"> <input type="hidden"  name="idTipoItem[]" value="1"> </td>
        <td class="text-center">${data[0].codigo_producto ? data[0].codigo_producto : ''} </td>
        <td class="text-center">${data[0].codigo_softlink ? data[0].codigo_softlink : ''} </td>
        <td class="text-center">${data[0].part_number ? data[0].part_number : ''} <input type="hidden"  name="idProducto[]" value="${(data[0].id_producto ? data[0].id_producto : data[0].id_producto)}"> </td>
        <td class="text-left">${(data[0].descripcion_producto ? data[0].descripcion_producto : (data[0].descripcion ? data[0].descripcion : ''))}  <input type="hidden"  name="descripcion[]" value="${(data[0].descripcion_producto ? data[0].descripcion_producto : data[0].descripcion)} "></td>
        <td><select name="unidad[]" class="form-control ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : 'activation')} input-sm unidadMedida" data-valor="${data[0].id_unidad_medida}" >${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
        <td>${(data[0].cantidad ? data[0].cantidad : '')}</td>
        <td>${(data[0].cantidad_atendido_almacen ? data[0].cantidad_atendido_almacen : '')}</td>
        <td>${(data[0].cantidad_atendido_orden ? data[0].cantidad_atendido_orden : '')}</td>
        <td>
            <div class="input-group">
                <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</div>
                <input class="form-control precio input-sm text-right ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : 'activation')}  handleBurUpdateSubtotal" data-id-tipo-item="1" data-producto-regalo="${(data[0].producto_regalo ? data[0].producto_regalo : false)}" type="number" min="0" name="precioUnitario[]"  placeholder="" value="${data[0].precio_unitario ? data[0].precio_unitario : 0}" >
            </div>
        </td>
        <td>
            <input class="form-control cantidad_a_comprar input-sm text-right ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : 'activation')}  handleBurUpdateSubtotal"  data-id-tipo-item="1" type="number" min="0" name="cantidadAComprarRequerida[]"  placeholder="" value="${data[0].cantidad_a_comprar ? data[0].cantidad_a_comprar : ''}" >
        </td>
        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm ${(data[0].estado_guia_com_det > 0 && data[0].estado_guia_com_det != 7 ? '' : 'activation')} handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" >
            <i class="fas fa-trash fa-sm"></i>
            </button>
        </td>
     </tr>`);

        this.autoUpdateSubtotal();
        this.UpdateSelectUnidadMedida();
        if (data.length > 0 && tipo =='OBSEQUIO') {
            Lobibox.notify('success', {
                title: false,
                size: 'mini',
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Producto para obsequio agregado'
            });

        }
    }

    agregarServicio() {
        vista_extendida();

        document.querySelector("tbody[id='body_detalle_orden']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;">
        <td><input type="hidden"  name="idRegister[]" value="${this.makeId()}"> <input type="hidden"  class="idEstado" name="idEstado[]"> <input type="hidden"  name="idDetalleRequerimiento[]" value=""> <input type="hidden"  name="idTipoItem[]" value="2"></td>
        <td>(No aplica) <input type="hidden"  name="idProducto[]" value=""></td>
        <td>(No aplica) <input type="hidden"  name="idProducto[]" value=""></td>
        <td>(No aplica) <input type="hidden"  name="idProducto[]" value=""></td>
        <td><textarea name="descripcion[]" placeholder="Descripción" class="form-control descripcion_servicio activation" value="" style="width:100%;height: 60px;overflow: scroll;"> </textarea>  </td>
        <td>Servicio<input type="hidden"  name="unidad[]" value="38"></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            <div class="input-group">
                <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</div>
                <input class="form-control precio input-sm text-right activation handleBurUpdatePrecio handleBurUpdateSubtotal" data-id-tipo-item="2" type="number" min="0" name="precioUnitario[]"  placeholder="" value="0" >
            </div>
        </td>
        <td>
            <input class="form-control cantidad_a_comprar input-sm text-right activation  handleBurUpdateSubtotal" data-id-tipo-item="2" type="number" min="0" name="cantidadAComprarRequerida[]"  placeholder="" value="" >
        </td>
        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm activation handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" >
            <i class="fas fa-trash fa-sm"></i>
            </button>
        </td>
    </tr>`);
    }


    makeId() {
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for (var i = 0; i < 12; i++) {
            ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }



    vincularRequerimientoAOrdenModal() {
        $('#modal-vincular-requerimiento-orden').modal({
            show: true,
            backdrop: 'true',
            keyboard: true

        });

        this.ConstruirlistarRequerimientosPendientesParaVincularConOrden();
  

    }

    ConstruirlistarRequerimientosPendientesParaVincularConOrden() {
        tablaListaRequerimientosParaVincular = $('#listaRequerimientosParaVincular').DataTable({
            'dom': 'Bfrtip',
            'language': vardataTables[0],
            'order': [[9, 'desc']],
            'serverSide': true,
            'processing': false,
            'destroy': true,
            'ajax': {
                'url': 'requerimientos-pendientes',
                'type': 'POST',
                'data': { 'idEmpresa': 'SIN_FILTRO', 'idSede': 'SIN_FILTRO', 'fechaRegistroDesde': 'SIN_FILTRO', 'fechaRegistroHasta': 'SIN_FILTRO', 'reserva': 'SIN_FILTRO', 'orden': 'SIN_FILTRO' },
                beforeSend: data => {

                    $("#listaRequerimientosParaVincular").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                }

            },
            'columns': [
                { 'data': 'codigo', 'name': 'alm_req.codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'alm_req.concepto', 'className': 'text-left' },
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro', 'className': 'text-center' },
                { 'data': 'tipo_req_desc', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'moneda','name':'sis_moneda.descripcion','className': 'text-center' },
                { 'data': 'cliente_razon_social', 'name':'contri_cliente.razon_social','className': 'text-left' },
                { 'data': 'empresa_sede', 'name': 'sis_sede.descripcion', 'className': 'text-center' },
                { 'data': 'cc_solicitado_por', 'name': 'cc_view.name', 'className': 'text-center' },
                { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc', 'className': 'text-center' },
                { 'data': 'id_requerimiento', 'name': 'alm_req.id_requerimiento', "searchable": false }

            ],
            'initComplete': function () {
                //Boton de busqueda
                const $filter = $('#listaRequerimientosParaVincular_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    tablaListaRequerimientosParaVincular.search($input.val()).draw();
                })
                //Fin boton de busqueda
            },
            "drawCallback": function (settings) {
                //Botón de búsqueda
                $('#listaRequerimientosParaVincular_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaRequerimientosParaVincular_filter input').trigger('focus');
                //fin botón búsqueda
                if (tablaListaRequerimientosParaVincular.rows().data().length == 0) {
                    Lobibox.notify('info', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: `No se encontro data disponible para mostrar`
                    });
                }
                //Botón de búsqueda
                $('#listaRequerimientosParaVincular_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaRequerimientosParaVincular_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaRequerimientosParaVincular").LoadingOverlay("hide", true);
            },
            'columnDefs': [
                { 'aTargets': [0], 'className': "text-left", 'sWidth': '7%' },
                { 'aTargets': [1], 'className': "text-left", 'sWidth': '30%' },
                { 'aTargets': [2], 'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [3], 'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [4], 'className': "text-center", 'sWidth': '5%' },
                { 'aTargets': [5], 'className': "text-left", 'sWidth': '8%' },
                { 'aTargets': [6], 'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [7], 'className': "text-center", 'sWidth': '4%' },
                { 'aTargets': [8], 'className': "text-center", 'sWidth': '4%' },
                {
                    'render':
                    function (data, type, row) {
                        let containerOpenBrackets = `<div class="btn-group" role="group" style="display: flex;flex-direction: row;flex-wrap: nowrap;">`;
                        let btnVerDetalle = `<button type="button" class="ver-detalle btn btn-default boton handleClickVerDetalleRequerimientoModalVincularRequerimiento" data-id-requerimiento="${row.id_requerimiento}"  data-toggle="tooltip" data-placement="bottom" title="Ver detalle requerimiento" data-id="${row.id_orden_compra}"> <i class="fas fa-chevron-down fa-sm"></i> </button>`;
                        let btnSeleccionar = `<button type="button" class="ver-detalle btn btn-${row.count_pendientes >0?'default':'success'} boton handleClickVincularRequerimiento" data-toggle="tooltip" data-placement="bottom" title="Seleccionar" data-id-requerimiento="${row.id_requerimiento}" data-id="${row.id_orden_compra}"> Seleccionar </button>`;
                        let containerCloseBrackets = `</div>`;
                        let infoPorMapear =`<small class="text-${row.count_pendientes >0?'danger':'success'}">${row.count_pendientes >0?('Mapeos pendientes: '+row.count_pendientes):''}</small>
                        `;
                        return (containerOpenBrackets + btnVerDetalle + btnSeleccionar + containerCloseBrackets+infoPorMapear);
                    },targets: 9, className: "text-center", sWidth: '10%'
                }
            ]

        });

    }


    verDetalleRequerimientoModalVincularRequerimiento(obj) {
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
        this.ordenCtrl.obtenerDetalleRequerimientos(id).then((res) => {
            this.construirDetalleRequerimientoModalVincularRequerimiento(table_id, row, res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirDetalleRequerimientoModalVincularRequerimiento(table_id, row, response) {
        var html = '';
        if (response.length > 0) {
            response.forEach((element) => {
                if (element.tiene_transformacion == false) {
                    let cantidad_atendido_almacen = 0;
                    if (element.reserva.length > 0) {
                        (element.reserva).forEach(reserva => {
                            if (reserva.estado == 1) {
                                cantidad_atendido_almacen += parseFloat(reserva.stock_comprometido);
                            }
                        });
                    }

                    html += `<tr>
                        <td style="border: none; text-align:center;">${(element.producto_part_number != null ? element.producto_part_number :'')}</td>
                        <td style="border: none; text-align:center;">${(element.producto_codigo != null ? element.producto_codigo : '')}</td>
                        <td style="border: none; text-align:center;">${(element.producto_codigo_softlink != null ? element.producto_codigo_softlink : '')}</td>
                        <td style="border: none; text-align:left;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}</td>
                        <td style="border: none; text-align:center;">${element.abreviatura != null ? element.abreviatura : ''}</td>
                        <td style="border: none; text-align:center;">${element.cantidad > 0 ? element.cantidad : ''}</td>
                        <td style="border: none; text-align:center;">${element.precio_unitario > 0 ? element.precio_unitario : ''}</td>
                        <td style="border: none; text-align:center;">${parseFloat(element.subtotal) > 0 ? $.number(element.subtotal, 2) : $.number((element.cantidad * element.precio_unitario), 2)}</td>
                        <td style="border: none; text-align:center;">${element.motivo != null ? element.motivo : ''}</td>
                        <td style="border: none; text-align:center;">${cantidad_atendido_almacen != null ? cantidad_atendido_almacen : ''}</td>
                        <td style="border: none; text-align:center;">${element.estado_doc != null ? element.estado_doc : ''}</td>
                        </tr>`;
                }
            });
            var tabla = `<table class="table table-condensed table-bordered" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none; text-align:center;">Part number</th>
                        <th style="border: none; text-align:center;">Código Producto</th>
                        <th style="border: none; text-align:center;">Código Softlink</th>
                        <th style="border: none; text-align:center;">Descripcion</th>
                        <th style="border: none; text-align:center;">Unidad medida</th>
                        <th style="border: none; text-align:center;">cantidad</th>
                        <th style="border: none; text-align:center;">precio_unitario</th>
                        <th style="border: none; text-align:center;">subtotal</th>
                        <th style="border: none; text-align:center;">motivo</th>
                        <th style="border: none; text-align:center;">Stock comprometido</th>
                        <th style="border: none; text-align:center;">Estado</th>
                    </tr>
                </thead>
                <tbody style="background: #e7e8ea;">${html}</tbody>
                </table>`;
        } else {
            var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
        }
        row.child(tabla).show();
    }

    vincularRequerimiento(obj) {
        obj.setAttribute("disabled",true);
        let idRequerimiento=obj.dataset.idRequerimiento;
        let i = 0;
        let cantidadItemSinMapear = 0;
        this.ordenCtrl.obtenerRequerimiento(idRequerimiento).then((res) => {
            this.loadHeadRequerimiento([res], 2);
            obj.removeAttribute("disabled");

            (res.detalle).forEach((element) => {
                if (element.tiene_transformacion == false) {
                    if (element.id_producto > 0) {
                        i++;

                        let cantidad_atendido_almacen = 0;
                        if (element.reserva.length > 0) {
                            (element.reserva).forEach(reserva => {
                                if (reserva.estado == 1) {
                                    cantidad_atendido_almacen += parseFloat(reserva.stock_comprometido);
                                }
                            });
                        }
                        let cantidad_atendido_orden = 0;
                        if (element.ordenes_compra.length > 0) {
                            (element.ordenes_compra).forEach(orden => {
                                cantidad_atendido_orden += parseFloat(orden.cantidad);
                            });
                        }
                        let cantidad_a_comprar = parseFloat(element.cantidad > 0 ? element.cantidad : 0) - parseFloat(cantidad_atendido_almacen) - parseFloat(cantidad_atendido_orden);
                        console.log(element);
                        this.agregarProducto([{
                            'id': this.makeId(),
                            'cantidad': element.cantidad ?? 0,
                            'cantidad_atendido_almacen': cantidad_atendido_almacen,
                            'cantidad_atendido_orden': cantidad_atendido_orden,
                            'cantidad_a_comprar': !(parseFloat(cantidad_a_comprar) >= 0) ? '' : cantidad_a_comprar,
                            'codigo_item': null,
                            'codigo_producto': element.producto.codigo != null ? element.producto.codigo : '',
                            'codigo_softlink': element.producto.cod_softlink != null ? element.producto.cod_softlink : '',
                            'codigo_requerimiento': element.codigo_requerimiento,
                            'descripcion': null,
                            'descripcion_producto': element.producto.descripcion != null ? element.producto.descripcion : '',
                            'estado': 0,
                            'garantia': null,
                            'id_detalle_orden': null,
                            'id_detalle_requerimiento': element.id_detalle_requerimiento,
                            'id_item': null,
                            'id_tipo_item': 1,
                            'id_producto': element.id_producto,
                            'id_requerimiento': element.id_requerimiento,
                            'id_unidad_medida': element.unidad_medida.id_unidad_medida,
                            'lugar_despacho': null,
                            'part_number': (!element.id_producto > 0 ? '(Sin mapear)' : ((element.producto.part_number != null ? element.producto.part_number : ''))),
                            'precio_unitario': element.precio_unitario ?? 0,
                            'id_moneda': 1,
                            'stock_comprometido': null,
                            'subtotal': $.number(parseFloat(element.precio_unitario * element.cantidad), 2),
                            'producto_regalo': false,
                            'tiene_transformacion': element.tiene_transformacion,
                            'unidad_medida': element.unidad_medida.descripcion
                        }],'DETALLE_REQUERIMIENTO');

                    } else {
                        cantidadItemSinMapear++;
                    }
                }
            });

            if (i > 0) {
                this.estadoVinculoRequerimiento({ 'mensaje': `Se agregó ${i} Item(s) a la orden`, 'estado': '200' })

            } else {
                if (cantidadItemSinMapear > 0) {
                    this.estadoVinculoRequerimiento({ 'mensaje': `No se puede agregar item(s) a la orden, tiene ${cantidadItemSinMapear} items sin mapear`, 'estado': '204' })
                } else {
                    this.estadoVinculoRequerimiento({ 'mensaje': `No se puede agregar item(s) a la orden`, 'estado': '204' })

                }

            }



        }).catch(function (err) {
            console.log(err)

        })

    }

    estadoVinculoRequerimiento(resolve) {
        let tipoMensaje = '';
        if (resolve.estado == '200') {
            tipoMensaje = 'success'
            $('#modal-vincular-requerimiento-orden').modal('hide');
        } else {
            tipoMensaje = 'warning'

        }

        Lobibox.notify(tipoMensaje, {
            title: false,
            size: 'mini',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: resolve.mensaje
        });
    }


    // mostrar info si esta vinculado con un requerimiento
    eliminarVinculoReq() {
        sessionStorage.removeItem('reqCheckedList');
        sessionStorage.removeItem('tipoOrden');
        window.location.reload();
    }



    // get_header_orden_requerimiento() {
    //     let id_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_orden']").value;
    //     let tipo_cambio_compra = document.querySelector("div[type='crear-orden-requerimiento'] input[name='tipo_cambio_compra']").value;
    //     let id_tp_documento = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_tp_documento']").value;

    //     let id_moneda = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_moneda']").value;
    //     let codigo_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='codigo_orden']").value;
    //     let fecha_emision = document.querySelector("div[type='crear-orden-requerimiento'] input[name='fecha_emision']").value;
    //     let incluye_igv = document.querySelector("div[type='crear-orden-requerimiento'] input[name='incluye_igv']").checked;

    //     let id_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_proveedor']").value;
    //     let id_contrib = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contrib']").value;
    //     let id_contacto_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value;
    //     let id_cuenta_principal_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value;

    //     let id_condicion = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_condicion']").value;
    //     let plazo_dias = document.querySelector("div[type='crear-orden-requerimiento'] input[name='plazo_dias']").value;
    //     let plazo_entrega = document.querySelector("div[type='crear-orden-requerimiento'] input[name='plazo_entrega']").value;
    //     let id_cc = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_cc']").value;
    //     let id_tp_doc = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_tp_doc']").value;

    //     let id_sede = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_sede']").value;
    //     let direccion_destino = document.querySelector("div[type='crear-orden-requerimiento'] input[name='direccion_destino']").value;
    //     let id_ubigeo_destino = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value;

    //     let personal_autorizado_1 = document.querySelector("div[type='crear-orden-requerimiento'] input[name='personal_autorizado_1']").value;
    //     let personal_autorizado_2 = document.querySelector("div[type='crear-orden-requerimiento'] input[name='personal_autorizado_2']").value;
    //     let observacion = document.querySelector("div[type='crear-orden-requerimiento'] textarea[name='observacion']").value;

    //     let data = {
    //         'id_orden': id_orden,
    //         'tipo_cambio_compra': tipo_cambio_compra,
    //         'id_tp_documento': id_tp_documento,
    //         'id_moneda': id_moneda,
    //         'codigo_orden': codigo_orden,
    //         'fecha_emision': fecha_emision,
    //         'incluye_igv': incluye_igv,

    //         'id_proveedor': id_proveedor,
    //         'id_contrib': id_contrib,
    //         'id_contacto_proveedor': id_contacto_proveedor,
    //         'id_cuenta_principal_proveedor': id_cuenta_principal_proveedor,

    //         'id_condicion': id_condicion,
    //         'plazo_dias': plazo_dias,
    //         'plazo_entrega': plazo_entrega,
    //         'id_tp_doc': id_tp_doc,
    //         'id_cc': id_cc,

    //         'id_sede': id_sede,
    //         'direccion_destino': direccion_destino,
    //         'id_ubigeo_destino': id_ubigeo_destino,

    //         'personal_autorizado_1': personal_autorizado_1,
    //         'personal_autorizado_2': personal_autorizado_2,
    //         'observacion': observacion,

    //         'detalle': []
    //     }

    //     return data;
    // }

    // incluyeIGV(e) {
    // this.calcTotalOrdenDetalleList(e.currentTarget.checked);
    // }

    save_orden(data, action) {
        // let payload_orden = this.get_header_orden_requerimiento();
        // payload_orden.detalle = (typeof detalleOrdenList != 'undefined') ? detalleOrdenList : detalleOrdenList;
        this.guardar_orden_requerimiento(action);
    }

    validaOrdenRequerimiento() {
        var id_proveedor = $('[name=id_proveedor]').val();
        var id_condicion_softlink = $('[name=id_condicion_softlink]').val();
        var plazo_entrega = $('[name=plazo_entrega]').val();
        var id_tp_documento = $('[name=id_tp_documento]').val();
        // var id_cuenta_principal_proveedor = $('[name=id_cuenta_principal_proveedor]').val();
        var msj = '';

        if (!id_tp_documento > 0) {
            msj += 'Es necesario que seleccione un tipo de orden.<br>';
        }
        if (!id_condicion_softlink > 0) {
            msj += 'Es necesario que seleccione una forma de pago.<br>';
        }
        if (!id_proveedor >0) {
            msj += 'Es necesario que seleccione un Proveedor.<br>';
        }
        if (id_tp_documento != '3' && plazo_entrega == '') {
            msj += 'Es necesario que ingrese un plazo de entrega.<br>';
        }
        // if (!id_cuenta_principal_proveedor >0) {
        //     msj += 'Es necesario que seleccione una cuenta bancaria del proveedor.<br>';
        // }
        let cantidadInconsistenteInputPrecio = 0;

        let allInputPrecio = document.querySelectorAll("table[id='listaDetalleOrden'] input[class~='precio']");
        allInputPrecio.forEach((element) => {
            if (!parseFloat(element.value) > 0) {
                cantidadInconsistenteInputPrecio++;
            }
        })

        if (cantidadInconsistenteInputPrecio > 0) {
            msj += 'Debe ingresar un precio mayor a cero.<br>';
        }


        let cantidadInconsistenteInputCantidadAComprar = 0;
        let inputCantidadAComprar = document.querySelectorAll("table[id='listaDetalleOrden'] input[class~='cantidad_a_comprar']");
        inputCantidadAComprar.forEach((element) => {
            if (element.value == null || element.value == '' || element.value <= 0) {
                cantidadInconsistenteInputCantidadAComprar++;
            }
        })
        if (parseInt(cantidadInconsistenteInputCantidadAComprar) > 0) {
            msj += 'Debe ingresar una cantidad mayor a cero.<br>';

        }

        let servicioVacio = 0;
        let inputServicio = document.querySelectorAll("table[id='listaDetalleOrden'] textarea[class~='descripcion_servicio']");
        inputServicio.forEach((element) => {
            if (element.value == null || element.value == '' || element.value <= 0) {
                servicioVacio++;
            }
        })
        if (parseInt(servicioVacio) > 0) {
            msj += 'Debe ingresar una descripción al servicio.<br>';

        }
        return msj;
    }


    guardar_orden_requerimiento(action) {
        // console.log(action);
        let that= this;
        let formData = new FormData($('#form-crear-orden-requerimiento')[0]);
        var msj = this.validaOrdenRequerimiento();
        if (msj.length > 0) {
            Swal.fire({
                title: '',
                html: msj,
                icon: 'warning'
            }
            );
        } else {
            if (action == 'register') {
                $.ajax({
                    type: 'POST',
                    url: 'guardar',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: data => {
    
                        $("#wrapper-okc").LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.id_orden_compra > 0) {
                            $("#wrapper-okc").LoadingOverlay("hide", true);

                            that.mostrarOrden(response.id_orden_compra);
                            actionPage = 'historial';

                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Orden ${response.codigo} creada.`
                            });
                            
                            if(response.status_migracion_softlink != null){
                                
                                $('[name=codigo_orden]').val(response.status_migracion_softlink.orden_softlink??"");

                                Lobibox.notify(response.status_migracion_softlink.tipo, {
                                    title: false,
                                    size: 'mini',
                                    rounded: true,
                                    sound: false,
                                    delayIndicator: false,
                                    msg: response.status_migracion_softlink.mensaje
                                });

                            }
                            changeStateButton('guardar');
                            $('#form-crear-orden-requerimiento').attr('type', 'register');
                            changeStateInput('form-crear-orden-requerimiento', true);

                            sessionStorage.removeItem('reqCheckedList');
                            sessionStorage.removeItem('tipoOrden');
                            // window.open("generar-orden-pdf/"+response.id_orden_compra, '_blank');
                            document.querySelector("span[name='codigo_orden_interno']").textContent = response.codigo;
                            document.querySelector("input[name='id_orden']").value = response.id_orden_compra;
                            document.querySelector("button[name='btn-imprimir-orden-pdf']").removeAttribute("disabled");
                            document.querySelector("button[name='btn-enviar-softlink']").removeAttribute("disabled");

                            // finalidados
                            if(response.lista_finalizados.length > 0){
                                response.lista_finalizados.forEach(element => {
                                    Swal.fire({
                                        title: '',
                                        html: `Se finalizó el cuadro de presupuesto ${element.cuadro_presupuesto.oportunidad.codigo_oportunidad} del requerimiento ${element.requerimiento.codigo}`,
                                        icon: 'info'
                                    });
                                });
                            }

                        } else {
                            $("#wrapper-okc").LoadingOverlay("hide", true);
                            Swal.fire(
                                '',
                                'Lo sentimos hubo un error en el servidor al intentar guardar la orden, por favor vuelva a intentarlo',
                                'error'
                            );
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    $("#wrapper-okc").LoadingOverlay("hide", true);

                    Swal.fire(
                        '',
                        'Hubo un problema al intentar guardar la orden, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });


            } else if (action == 'edition') {
                $.ajax({
                    type: 'POST',
                    url: 'actualizar',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: data => {
    
                        $("#wrapper-okc").LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.id_orden_compra > 0) {

                            $("#wrapper-okc").LoadingOverlay("hide", true);

                            that.mostrarOrden(response.id_orden_compra);
                            actionPage = 'historial';

                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Orden ${response.codigo} actualizada`
                            });

                            if(response.status_migracion_softlink != null){
                                
                                $('[name=codigo_orden]').val(response.status_migracion_softlink.orden_softlink??"");

                                Lobibox.notify(response.status_migracion_softlink.tipo, {
                                    title: false,
                                    size: 'mini',
                                    rounded: true,
                                    sound: false,
                                    delayIndicator: false,
                                    msg: response.status_migracion_softlink.mensaje
                                });

                            }
                            
                            changeStateButton('guardar');
                            $('#form-crear-orden-requerimiento').attr('type', 'register');
                            changeStateInput('form-crear-orden-requerimiento', true);
                        }else{
                            $("#wrapper-okc").LoadingOverlay("hide", true);

                            Lobibox.notify('danger', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Orden no se puedo actualizada`
                            });

                            if(response.status_migracion_softlink != null){
                                
                                Lobibox.notify(response.status_migracion_softlink.tipo, {
                                    title: false,
                                    size: 'mini',
                                    rounded: true,
                                    sound: false,
                                    delayIndicator: false,
                                    msg: response.status_migracion_softlink.mensaje
                                });

                            }
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    $("#wrapper-okc").LoadingOverlay("hide", true);

                    Swal.fire(
                        '',
                        'Hubo un problema al intentar actualizar la orden, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            } else {
                $("#wrapper-okc").LoadingOverlay("hide", true);

                Swal.fire(
                    '',
                    'Hubo un error en la acción de la botonera, el action no esta definido',
                    'error'
                );
            }

        }
    }

    fechaHoy() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value = now.toISOString().slice(0, -1);
    };


    nuevaOrden() {
        this.restablecerFormularioOrden();
    }




    //  modal ordenes elaboradas 

    ordenesElaboradasModal() {
        // changeStateButton('inicio'); //init.js

        $('#modal-ordenes-elaboradas').modal({
            show: true,
            backdrop: 'true'
        });
        this.listarOrdenesElaboradas();

    }

    listarOrdenesElaboradas() {
        var vardataTables = funcDatatables();
        $tablaHistorialOrdenesElaboradas= $('#listaOrdenesElaboradas').DataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'listar-historial-ordenes-elaboradas',
                'type': 'POST',
                beforeSend: data => {
    
                    $("#listaOrdenesElaboradas").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },

            },
            'columns': [
                { 'data': 'id_orden_compra', 'name': 'log_ord_compra.id_orden_compra', 'visible': false },
                { 'data': 'fecha', 'name': 'log_ord_compra.fecha', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'log_ord_compra.codigo', 'className': 'text-center' },
                { 'data': 'codigo_softlink', 'name': 'log_ord_compra.codigo_softlink', 'className': 'text-center' },
                { 'data': 'nro_documento', 'name': 'adm_contri.nro_documento', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
                { 'data': 'moneda_descripcion', 'name': 'sis_moneda.descripcion', 'className': 'text-center' },
                { 'data': 'descripcion_sede_empresa', 'name': 'sis_sede.descripcion', 'className': 'text-center' },
                { 'data': 'estado_doc', 'name': 'estados_compra.descripcion', 'className': 'text-center' },
                { 'data': 'id_orden_compra' , 'name': 'log_ord_compra.id_orden_compra','className': 'text-center'}
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                        <button type="button" class="btn btn-xs btn-success handleClickSelectOrden" data-id-orden="${row.id_orden_compra}" title="Seleccionar" >Seleccionar</button>
                        </div></center>`;
                    }, targets: 9
                },

            ],
            'initComplete': function () {
                //Boton de busqueda
                const $filter = $('#listaOrdenesElaboradas_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaHistorialOrdenesElaboradas.search($input.val()).draw();
                })
                //Fin boton de busqueda
                
            },
            "drawCallback": function( settings ) {
                if($tablaHistorialOrdenesElaboradas.rows().data().length==0){
                    Lobibox.notify('info', {
                        title:false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: `No se encontro data disponible para mostrar`
                        }); 
                }
                //Botón de búsqueda
                $('#listaOrdenesElaboradas_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaOrdenesElaboradas_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaOrdenesElaboradas").LoadingOverlay("hide", true);
            }
        });
    }


    selectOrden(idOrden) {
        this.mostrarOrden(idOrden);
        actionPage = 'historial';
        $('#modal-ordenes-elaboradas').modal('hide');
    }

    mostrarOrden(id) {
        $.ajax({
            type: 'GET',
            url: 'mostrar-orden/' + id,
            dataType: 'JSON',
            success: (response) => {
                this.construirFormularioOrden(response);
                detalleOrdenList = response.detalle;
                this.setStatusPage();
            }
        }).fail((jqXHR, textStatus, errorThrown) => {
            Swal.fire(
                '',
                'Hubo un problema al intentar mostrar la orden, por favor vuelva a intentarlo.',
                'error'
            );
            // sessionStorage.removeItem('idOrden');
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });


    }


    construirFormularioOrden(data) {
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_orden']").value = data.id_orden_compra ? data.id_orden_compra : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_documento']").value = data.id_tp_documento ? data.id_tp_documento : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_moneda']").value = data.id_moneda ? data.id_moneda : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent = data.codigo_orden ? data.codigo_orden : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='estado_cuadro_costo']").textContent = data.cuadro_costo !=null && data.cuadro_costo.length>0 ? data.cuadro_costo.map(e => ('('+e.codigo+': '+e.estado_aprobacion+')')).join(",") : '';

        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='codigo_orden']").value = data.codigo_softlink ? data.codigo_softlink : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value = data.fecha ? moment(data.fecha, 'DD-MM-YYYY h:m').format('YYYY-MM-DDThh:mm') : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_sede']").value = data.id_sede ? data.id_sede : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] img[id='logo_empresa']").setAttribute("src", data.logo_empresa);
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='incluye_igv']").checked = data.incluye_igv;

        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value = data.id_proveedor ? data.id_proveedor : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value = data.id_contribuyente ? data.id_contribuyente : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='razon_social']").value = data.razon_social ? data.razon_social : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value = data.direccion_fiscal ? data.direccion_fiscal : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value = data.ubigeo ? data.ubigeo : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value = data.ubigeo_proveedor ? data.ubigeo_proveedor : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value = data.id_contacto ? data.id_contacto : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value = data.nombre_contacto ? data.nombre_contacto : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value = data.telefono_contacto ? data.telefono_contacto : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value = data.id_cta_principal ? data.id_cta_principal : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nro_cuenta_principal_proveedor']").value = data.nro_cuenta ? data.nro_cuenta : '';

        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_condicion_softlink']").value = data.id_condicion_softlink ? data.id_condicion_softlink : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_condicion']").value = data.id_condicion ? data.id_condicion : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_dias']").value = data.plazo_dias ? data.plazo_dias : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_entrega']").value = data.plazo_entrega ? data.plazo_entrega : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='cdc_req']").value = data.oportunidad.length>0 ? ((data.oportunidad).map(x=>x.codigo_oportunidad).toString()) : ((data.requerimientos).map(x=>x.codigo).toString());
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ejecutivo_responsable']").value = data.oportunidad.length>0 ? ((data.oportunidad).map(x=>x.responsable).toString()) : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_doc']").value = data.id_tp_doc ? data.id_tp_doc : '';

        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_destino']").value = data.direccion_destino ? data.direccion_destino : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value = data.ubigeo_destino_id ? data.ubigeo_destino_id : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_destino']").value = data.ubigeo_destino ? data.ubigeo_destino : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_1']").value = data.personal_autorizado_1 ? data.personal_autorizado_1 : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_2']").value = data.personal_autorizado_2 ? data.personal_autorizado_2 : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_1']").value = data.nombre_personal_autorizado_1 ? data.nombre_personal_autorizado_1 : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_2']").value = data.nombre_personal_autorizado_2 ? data.nombre_personal_autorizado_2 : '';
        document.querySelector("form[id='form-crear-orden-requerimiento'] textarea[name='observacion']").value = data.observacion ? data.observacion : '';

        document.querySelector("button[name='btn-imprimir-orden-pdf']").removeAttribute("disabled");
        document.querySelector("button[name='btn-enviar-softlink']").removeAttribute("disabled");


        // construir detalle 

        this.limpiarTabla('listaDetalleOrden');
        vista_extendida();
        let detalle =data.detalle;

        for (let i = 0; i < detalle.length; i++) {

            let cantidad_atendido_almacen = 0;
            if (detalle[i].detalle_requerimiento && detalle[i].detalle_requerimiento.reserva.length > 0) {
                (detalle[i].detalle_requerimiento.reserva).forEach(reserva => {
                    if (reserva.estado == 1) {
                        cantidad_atendido_almacen += parseFloat(reserva.stock_comprometido);
                    }
                });
            }
            let cantidad_atendido_orden = 0;
            if (detalle[i].detalle_requerimiento && detalle[i].detalle_requerimiento.ordenes_compra.length > 0) {
                (detalle[i].detalle_requerimiento.ordenes_compra).forEach(orden => {
                    if (orden.estado == 1) {
                    cantidad_atendido_orden += parseFloat(orden.cantidad);
                    }
                });
            }

            if (detalle[i].tipo_item_id == 1) { // producto
                if (detalle[i].id_producto > 0) { // TO-DO  falta mostrar cantidad_atendido_almacen y cantidad_atendido_orden
                    document.querySelector("tbody[id='body_detalle_orden']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;" class="${detalle[i].estado == 7 ? 'danger textRedStrikeHover' : ''}">
                        <td class="text-center">${detalle[i].codigo_requerimiento ? detalle[i].codigo_requerimiento : ''} <input type="hidden"  name="idRegister[]" value="${detalle[i].id_detalle_orden ? detalle[i].id_detalle_orden : this.makeId()}"> <input type="hidden"  class="idEstado" name="idEstado[]"> <input type="hidden"  name="idDetalleRequerimiento[]" value="${detalle[i].id_detalle_requerimiento ? detalle[i].id_detalle_requerimiento : ''}">  <input type="hidden"  name="idTipoItem[]" value="1"></td>
                        <td class="text-center">${detalle[i].producto.codigo ? detalle[i].producto.codigo : ''} </td>
                        <td class="text-center">${detalle[i].producto.cod_softlink ? detalle[i].producto.cod_softlink : ''} </td>
                        <td class="text-center">${detalle[i].producto.part_number ? detalle[i].producto.part_number : ''} <input type="hidden"  name="idProducto[]" value="${(detalle[i].id_producto ? detalle[i].id_producto : detalle[i].id_producto)} "></td>
                        <td class="text-left">${(detalle[i].producto.descripcion ? detalle[i].producto.descripcion : (detalle[i].descripcion != null ? detalle[i].descripcion : ''))} <input type="hidden"  name="descripcion[]" value="${(detalle[i].producto.descripcion ? detalle[i].producto.descripcion : detalle[i].descripcion)} "></td>
                        <td><select name="unidad[]" class="form-control ${(detalle[i].guia_compra_detalle !=null && detalle[i].guia_compra_detalle.length > 0 ? '' : 'activation')} input-sm unidadMedida" data-valor="${detalle[i].id_unidad_medida}" disabled>${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                        <td>${(detalle[i].detalle_requerimiento ? detalle[i].detalle_requerimiento.cantidad : '')}</td>
                        <td>${(cantidad_atendido_almacen >0 ? cantidad_atendido_almacen : '')}</td> 
                        <td>${(cantidad_atendido_orden >0 ? cantidad_atendido_orden : '')}</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</div>
                                <input class="form-control precio input-sm text-right ${(detalle[i].guia_compra_detalle !=null && detalle[i].guia_compra_detalle.length > 0 ? '' : 'activation')}  handleBurUpdateSubtotal" data-id-tipo-item="1" data-producto-regalo="${(detalle[i].producto_regalo ? detalle[i].producto_regalo : false)}" type="number" min="0" name="precioUnitario[]"  placeholder="" value="${detalle[i].precio ? detalle[i].precio : 0}" disabled>
                            </div>
                        </td>
                        <td>
                            <input class="form-control cantidad_a_comprar input-sm text-right ${(detalle[i].guia_compra_detalle !=null && detalle[i].guia_compra_detalle.length > 0 ? '' : 'activation')}  handleBurUpdateSubtotal"  data-id-tipo-item="1" type="number" min="0" name="cantidadAComprarRequerida[]"  placeholder="" value="${detalle[i].cantidad ? detalle[i].cantidad : 0}" disabled>
                        </td>
                        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm ${(detalle[i].guia_compra_detalle !=null && detalle[i].guia_compra_detalle.length > 0 ? '' : 'activation')} handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" disabled>
                            <i class="fas fa-trash fa-sm"></i>
                            </button>
                        </td>
                    </tr>`);

                }
            } else { //servicio
                document.querySelector("tbody[id='body_detalle_orden']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;" class="${detalle[i].estado == 7 ? 'danger textRedStrikeHover' : ''};">
                    <td>${detalle[i].codigo_requerimiento ? detalle[i].codigo_requerimiento : ''} <input type="hidden"  name="idRegister[]" value="${detalle[i].id_detalle_orden ? detalle[i].id_detalle_orden : this.makeId()}"> <input type="hidden"  class="idEstado" name="idEstado[]"> <input type="hidden"  name="idDetalleRequerimiento[]" value="${detalle[i].id_detalle_requerimiento ? detalle[i].id_detalle_requerimiento : ''}"> <input type="hidden"  name="idTipoItem[]" value="2"></td>
                    <td>(No aplica) <input type="hidden" value=""></td>
                    <td>(No aplica) <input type="hidden" value=""></td>
                    <td>(No aplica) <input type="hidden"  name="idProducto[]" value=""></td>
                    <td><textarea name="descripcion[]" placeholder="Descripción" class="form-control descripcion_servicio activation" value="${(detalle[i].descripcion_adicional ? detalle[i].descripcion_adicional : '')}" style="width:100%;height: 60px;overflow: scroll;"> ${(detalle[i].descripcion_adicional ? detalle[i].descripcion_adicional : '')}</textarea> </td>
                    <td><select name="unidad[]" class="form-control activation input-sm" value="${detalle[i].id_unidad_medida}" disabled>${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                    <td>${(detalle[i].cantidad ? detalle[i].cantidad : '')}</td>
                    <td></td>
                    <td></td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</div>
                            <input class="form-control precio input-sm text-right activation  handleBurUpdateSubtotal" data-id-tipo-item="2" type="number" min="0" name="precioUnitario[]"  placeholder="" value="${detalle[i].precio ? detalle[i].precio : 0}" disabled>
                        </div>
                    </td>
                    <td>
                        <input class="form-control cantidad_a_comprar input-sm text-right activation handleBurUpdateSubtotal" data-id-tipo-item="2" type="number" min="0" name="cantidadAComprarRequerida[]"  placeholder="" value="${detalle[i].cantidad ? detalle[i].cantidad : ''}" disabled>
                    </td>
                    <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='id_moneda']").options[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm activation handleClickOpenModalEliminarItemOrden" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" disabled>
                        <i class="fas fa-trash fa-sm"></i>
                        </button>
                    </td>
                </tr>`);
            }

        }
        this.autoUpdateSubtotal();
        this.UpdateSelectUnidadMedida();

    }


    anularOrden(id) {

        let sustentoAnularOrden='';
        Swal.fire({
            title: 'Sustente el movivo de la anulación de orden',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Registrar',
      
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                sustentoAnularOrden =result.value;
                // inicio enviar anular orden
                this.ordenCtrl.anularOrden(id,sustentoAnularOrden).then((response) => {
                    $("#wrapper-okc").LoadingOverlay("hide", true);
        
                    console.log(response);
                    if (response.status == 200) {
                        this.restablecerFormularioOrden();
                        Lobibox.notify(response.status == 200 ? 'success' : 'warning', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje.toString()
                        });
                        
        
                        if(response.status_migracion_softlink != null){
                                        
                            $('[name=codigo_orden]').val(response.status_migracion_softlink.orden_softlink??"");
        
                            Lobibox.notify(response.status_migracion_softlink.tipo, {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.status_migracion_softlink.mensaje
                            });
        
                        }
        
                    } else {
                        $("#wrapper-okc").LoadingOverlay("hide", true);
        
                        Lobibox.notify('warning', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje.toString()
                        });
                        console.log(response);
        
                        if(response.status_migracion_softlink != null){
                                        
                            Lobibox.notify(response.status_migracion_softlink.tipo, {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.status_migracion_softlink.mensaje
                            });
        
                        }
        
                    }
                }).catch((err) => {
                    $("#wrapper-okc").LoadingOverlay("hide", true);
                    console.log(err)
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor, por favor vuelva a intentarlo',
                        'error'
                    );
                });
                // fon enviar anular orden
            }
        })
        

    }

    restablecerFormularioOrden() {
        $('#form-crear-orden-requerimiento')[0].reset();
        this.limpiarTabla("listaDetalleOrden");
        this.calcularMontosTotales();
        this.fechaHoy();
        document.querySelector("span[name='codigo_orden_interno']").textContent = '';
        document.querySelector("button[name='btn-imprimir-orden-pdf']").setAttribute("disabled", true);
        document.querySelector("button[name='btn-enviar-softlink']").setAttribute("disabled", true);
        sessionStorage.removeItem('reqCheckedList');
        sessionStorage.removeItem('tipoOrden');
        sessionStorage.removeItem('action');
    }
}


function cancelarOrden() {
    const ordenModel = new OrdenModel();
    const ordenController = new OrdenCtrl(ordenModel);
    const ordenView = new OrdenView(ordenController);
    ordenView.restablecerFormularioOrden();
    document.querySelector("span[id='text-info-req-vinculado']") ? document.querySelector("span[id='text-info-req-vinculado']").remove() : false;

}