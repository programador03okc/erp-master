var facturaList=[];

class FacturaView {

    constructor() {
        this.factura = {};
        this.facturaList = [];
        this.subtotalFactura = 0;
        this.totalIGVFactura = 0;
        this.totalAPagarFactura = 0;
        this.objSeleccionado = {};
    }


    eventos() {
        $('#modal-adjuntar-archivos-requerimiento-pago').on("click", "button.handleClickVincularFacturaRequerimientoPago", (e) => {
            this.vincularFacturaRequerimientoPago(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago').on("click", "button.handleClickVerVinculoConFactura", (e) => {
            this.verVinculoFacturaRequerimientoPago(e.currentTarget);
        });
        $('#modal-factura-requerimiento-pago').on("click", "button.handleClickConfirmarCrearFactura", (e) => {
            this.confirmarCrearFactura(e.currentTarget);
        });
        $('#modal-factura-requerimiento-pago').on("click", "button.handleClickCopiarDataActualDeRequerimientoAFactura", (e) => {
            this.copiarDataActualDeRequerimientoAFactura();
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

    vincularFacturaRequerimientoPago(obj) {
        this.objSeleccionado=obj;
        document.querySelector("div[id='modal-factura-requerimiento-pago'] button[id='btnConfirmarCrearFactura']").removeAttribute("disabled");
        document.querySelector("button[id='btnCopiarDataActualDeRequerimientoAFactura']").classList.add("oculto");
        this.subtotalFactura = 0;
        this.totalIGVFactura = 0;
        this.totalAPagarFactura = 0;
        let nroComprobanteCompletado = false;
        let tieneItems = false;
        let mensajeList = [];

        if (document.querySelector("tbody[id='body_detalle_requerimiento_pago']").childElementCount > 0) {
            tieneItems = true;
        } else {
            mensajeList.push('Primero debe agregar los items al requerimiento de pago');
            tieneItems = false;
        }

        if (obj.closest('tr').querySelector("input[name='serie']").value != '' && obj.closest('tr').querySelector("input[name='numero']").value != '') {
            nroComprobanteCompletado = true;
        } else {
            nroComprobanteCompletado = false;
            mensajeList.push('Debe llenar el campo serie y número');
        }

        if (nroComprobanteCompletado * tieneItems) {
            $('#modal-factura-requerimiento-pago').modal({
                show: true
            });
            document.querySelector("div[id='modal-factura-requerimiento-pago'] span[name='codigo']").textContent = document.querySelector("div[id='modal-requerimiento-pago'] span[name='codigo']").textContent;
            this.limpiarTabla('ListaDetalleRequerimientoPagoYFactura');
            // copiar data de modal adjuntos y de modal de requerimiento de pago
            // si es una factura se obtiene el IGV y si es otro tipo Ejm boleta, el monto subtotal es el mismo al total_a_pagar
            const tipoDoc = document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] select[name='categoriaAdjunto']").value;
            let subtotal = 0;
            let total_igv = 0;
            let total_a_pagar = 0;
            const monto_total_req = parseFloat(document.querySelector("input[name='monto_total']").value)
            if (tipoDoc == 2) { //factura
                subtotal = monto_total_req / 1.18;
                total_igv = monto_total_req - subtotal;
                total_a_pagar = monto_total_req;
            } else {
                subtotal = document.querySelector("input[name='monto_total']").value;
                total_a_pagar = document.querySelector("input[name='monto_total']").value;
            }

            this.factura =
            {
                'id_adjunto': obj.dataset.id,
                'id_doc_com': document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_doc_com']").value,
                'fecha_emision': document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] input[name='fecha_emision']").value,
                'serie': document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] input[name='serie']").value,
                'numero': document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] input[name='numero']").value,
                'id_tp_doc': tipoDoc,
                'subtotal': subtotal,
                'total_igv': total_igv,
                'total_a_pagar': total_a_pagar,
                'id_moneda': document.querySelector("div[id='modal-requerimiento-pago'] select[name='moneda']").value,
                'id_sede': document.querySelector("div[id='modal-requerimiento-pago'] select[name='empresa']").value,
                'id_condicion': 1,

            };

            let copyItemsDetalleReqPago = [];
            (document.querySelectorAll("tbody[id='body_detalle_requerimiento_pago'] tr")).forEach(element => {
                let SerieNumerofactura = this.obtenerFacturasVinculadasEnAdjuntoList(element.querySelector("input[name='idRegister[]']").value);
                copyItemsDetalleReqPago.push(
                    {
                        'id': element.querySelector("input[name='idRegister[]']").value,
                        'descripcion_partida': element.querySelector("p[class='descripcion-partida']").textContent,
                        'descripcion_centro_costo': element.querySelector("p[class='descripcion-centro-costo']").textContent,
                        'descripcion_item': element.querySelector("textarea[name='descripcion[]']").value,
                        'unidad': element.querySelector("select[name='unidad[]']").options[element.querySelector("select[name='unidad[]']").selectedIndex].textContent,
                        'cantidad': element.querySelector("input[name='cantidad[]']").value,
                        'condicion': 1,
                        'precio_unitario': element.querySelector("input[name='precioUnitario[]']").value,
                        'subtotal': element.querySelector("span[name='subtotal[]']").textContent,
                        'motivo': element.querySelector("textarea[name='motivo[]']").value,
                        'factura': SerieNumerofactura
                    }
                )

            });
            // console.log(copyItemsDetalleReqPago);
            // pasar a modal vincular items de requerimiento con factura
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_requerimiento_pago']").value = document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_adjunto']").value = obj.dataset.id;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_tp_doc']").value = this.factura.id_tp_doc;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='serie_doc']").value = this.factura.serie;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='numero_doc']").value = this.factura.numero;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='fecha_emision_doc']").value = this.factura.fecha_emision;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_condicion']").value = this.factura.id_condicion;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='moneda']").value = this.factura.id_moneda;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='simbolo']").value = (this.factura.id_moneda == 1 ? 'S/' : (this.factura.id_moneda == 2 ? '$' : ''));
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='importe']").value = this.factura.total_a_pagar;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_sede']").value = this.factura.id_sede;

            let html = '';
            copyItemsDetalleReqPago.forEach((data, index) => {
                html += `<tr>
                            <td><input type="checkbox" data-id="${data.id}" checked/></td>
                            <td class="descripcion_item">${data.descripcion_item}</td>
                            <td class="unidad">${data.unidad.toUpperCase()}</td>
                            <td class="cantidad">${data.cantidad}</td>
                            <td class="precio_unitario">${data.precio_unitario}</td>
                            <td style="text-align:right;"><input class="hidden subtotal" value="${data.subtotal}" >${(this.factura.id_moneda == 1 ? 'S/' : (this.factura.id_moneda == 2 ? '$' : ''))}${$.number(data.subtotal, 2, '.', ',')}</td>
                            <td>${data.factura}</td>
                        </tr>`;
            });
            document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tbody").innerHTML = html;
            this.calcularTotalVincularItemsRequerimientoPagoConFactura();

        } else {
            Swal.fire(
                '',
                mensajeList.toString(),
                'warning'
            );
        }
    }

    verVinculoFacturaRequerimientoPago(obj) {
        this.objSeleccionado = obj;
        document.querySelector("button[id='btnCopiarDataActualDeRequerimientoAFactura']").classList.remove("oculto");
        document.querySelector("div[id='modal-factura-requerimiento-pago'] button[id='btnConfirmarCrearFactura']").setAttribute("disabled", true);
        document.querySelector("div[id='modal-factura-requerimiento-pago'] span[name='codigo']").textContent = document.querySelector("div[id='modal-requerimiento-pago'] span[name='codigo']").textContent;
        $('#modal-factura-requerimiento-pago').modal({
            show: true
        });

        this.facturaList.forEach(fac => {
            if(fac.id_adjunto == obj.dataset.id){
                this.factura = fac;
                // console.log("se tomó la reciente factura almacenada del listado");
            }
        });

        if(!this.factura.hasOwnProperty("detalle")){ // si no existe un objecto factura, crea a apartir de la variable del adjunto
            this.crearObjetoFacturaSegunAdjunto(obj.dataset.idDocCom)
            // console.log("crea objecto fac segun adjunto");
        }
        // console.log(this.factura);
        // console.log(this.facturaList);
        this.imprimirObjetoFacturaEnModalDeVinculoConRequerimientoPago(); //imprimer el contenido del objecto factura 
        
    }


    crearObjetoFacturaSegunAdjunto(idDocCom) {
        tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach(element => {
            if (element.documento_compra != null && element.documento_compra.id_doc_com == idDocCom) {

                let subtotal = 0;
                let total_igv = 0;
                let total_a_pagar = 0;
                let sumatotalItem = element.documento_compra.total_a_pagar;
                if (element.documento_compra.id_tp_doc == 2) { //si es factura
                    subtotal = sumatotalItem / 1.18;
                    total_igv = sumatotalItem - subtotal;
                    total_a_pagar = sumatotalItem;
                } else {
                    total_a_pagar = sumatotalItem;
                }

                this.subtotalFactura = subtotal;
                this.totalIGVFactura = total_igv;
                this.totalAPagarFactura = total_a_pagar;


                this.factura =
                {
                    'id_adjunto': element.id,
                    'id_doc_com': idDocCom,
                    'id_requerimiento_pago': document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value,
                    'fecha_emision': element.documento_compra.fecha_emision,
                    'serie': element.documento_compra.serie,
                    'numero': element.documento_compra.numero,
                    'id_tp_doc': element.documento_compra.id_tp_doc,
                    'subtotal': this.subtotalFactura,
                    'total_igv': this.totalIGVFactura,
                    'total_a_pagar': this.totalAPagarFactura,
                    'id_moneda': element.documento_compra.moneda,
                    'id_sede': element.documento_compra.id_sede,
                    'id_condicion': element.documento_compra.id_condicion,
                    'confirmada':false
                };
                // detalle
                this.factura.items = [];
                this.factura.detalle = [];
                (element.documento_compra.documento_compra_detalle).forEach((data, index) => {
                    let SerieNumerofactura = this.obtenerFacturasVinculadasEnAdjuntoList(data.id_requerimiento_pago_detalle);
                    this.factura.detalle.push({
                        'id': data.id_requerimiento_pago_detalle,
                        'descripcion_item': data.servicio_descripcion.toUpperCase(),
                        'unidad': data.unidad_medida != null ? (data.unidad_medida.descripcion).toUpperCase() : '',
                        'cantidad': data.cantidad,
                        'id_moneda': data.moneda,
                        'precio_unitario': data.precio_unitario,
                        'facturas': SerieNumerofactura
                    });
                });
            }
        });

    }

    imprimirObjetoFacturaEnModalDeVinculoConRequerimientoPago() {
        if (this.factura != null) {
            // cabecera
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_doc_com']").value = this.factura.id_doc_com;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_requerimiento_pago']").value = this.factura.id_requerimiento_pago;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_adjunto']").value = this.factura.id_adjunto;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_tp_doc']").value = this.factura.id_tp_doc;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='serie_doc']").value = this.factura.serie;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='numero_doc']").value = this.factura.numero;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='fecha_emision_doc']").value = this.factura.fecha_emision;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_condicion']").value = this.factura.id_condicion;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='moneda']").value = this.factura.id_moneda;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='simbolo']").value = (this.factura.id_moneda == 1 ? 'S/' : (this.factura.id_moneda == 2 ? '$' : ''));
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='importe']").value = this.factura.total_a_pagar;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_sede']").value = this.factura.id_sede;

            // detalle
            let html = '';
            this.factura.detalle.forEach(data => {

                html += `<tr>
                    <td><input type="checkbox" data-id="${data.id}" /></td>
                    <td class="descripcion_item">${data.descripcion_item.toUpperCase()}</td>
                    <td class="unidad">${data.unidad != null ? (data.unidad).toUpperCase() : ''}</td>
                    <td class="cantidad">${data.cantidad}</td>
                    <td class="precio_unitario">${data.precio_unitario}</td>
                    <td style="text-align:right;"><input class="hidden subtotal" value="${data.precio_unitario * data.cantidad}" >${(data.id_moneda == 1 ? 'S/' : (data.id_moneda == 2 ? '$' : ''))}${$.number((data.precio_unitario * data.cantidad), 2, '.', ',')}</td>
                    <td>${data.facturas??''}</td>
                </tr>`;
            });

            document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tbody").innerHTML = html;

        }
        this.calcularTotalVincularItemsRequerimientoPagoConFactura();

    }

    confirmarCrearFactura(obj) {
        this.factura.confirmada = true;
        this.factura.items = [];
        this.factura.detalle = [];
        (document.querySelectorAll("table[id='ListaDetalleRequerimientoPagoYFactura'] tbody")).forEach(element => {
            if (Boolean(element.querySelector("input[type='checkbox']").checked) === true) {
                this.factura.items.push(element.querySelector("input[type='checkbox']").dataset.id);
                this.factura.detalle.push({
                    'id': element.querySelector("input[type='checkbox']").dataset.id,
                    'descripcion_item': element.querySelector("td[class='descripcion_item']").textContent,
                    'unidad': element.querySelector("td[class='unidad']").textContent,
                    'cantidad': element.querySelector("td[class='cantidad']").textContent,
                    'precio_unitario': element.querySelector("td[class='precio_unitario']").textContent
                });
            }
        });

        
        let idAdjuntoEncontrado=false;
        this.facturaList.forEach((fac,keyFac) => {
            if(fac.id_adjunto!='' && fac.id_adjunto ==this.factura.id_adjunto){
                idAdjuntoEncontrado=true;
                this.facturaList[keyFac]=this.factura;
            }
        });
        
        if(idAdjuntoEncontrado ==false){
            this.facturaList.push(this.factura);
        }


        Swal.fire(
            '',
            'Factura confirmada',
            'success'
        );
        facturaList = this.facturaList;

        this.objSeleccionado.closest("tr").querySelector("select[name='categoriaAdjunto']").setAttribute("disabled",true);

        tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach((element,key) => {
            if(element.id == this.factura.id_adjunto){
                tempArchivoAdjuntoRequerimientoPagoCabeceraList[key].confirmada = true;
            }
        });

        // console.log(tempArchivoAdjuntoRequerimientoPagoCabeceraList);
        this.factura = [];

        $('#modal-factura-requerimiento-pago').modal('hide');

    }

    obtenerFacturasVinculadasEnAdjuntoList(idRequerimienoPagoDetalle) {
        let comprobanteList = [];
        tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach(cab => {
            if (cab.documento_compra != null && cab.documento_compra.hasOwnProperty("documento_compra_detalle")) {
                if (cab.documento_compra.documento_compra_detalle.length > 0) {
                    (cab.documento_compra.documento_compra_detalle).forEach(det => {
                        if (det.id_requerimiento_pago_detalle > 0) {
                            if (det.id_requerimiento_pago_detalle == idRequerimienoPagoDetalle) {
                                comprobanteList.push(cab.documento_compra.serie + '-' + cab.documento_compra.numero);
                            }
                        }
                    });
                }
            }
        });

        let SerieNumerofactura = '';
        if (comprobanteList.length > 0) {
            SerieNumerofactura = comprobanteList.toString();
        }
        return SerieNumerofactura;
    }

    copiarDataActualDeRequerimientoAFactura() { // copiar toda la data del requerimiento de pago (cabecera y detalle) al objecto y formulario factura 
        $('#ListaDetalleRequerimientoPagoYFactura').LoadingOverlay("show", {
            imageAutoResize: true,
            progress: true,
            imageColor: "#3c8dbc"
        });

        // copiar dat de detalle requerimiento de pago a detalle de lista detalle req pago de factura
        let copyItemsDetalleReqPago = [];
        (document.querySelectorAll("tbody[id='body_detalle_requerimiento_pago'] tr")).forEach(element => {

            // if(!idItemEnFacturaList.includes(element.querySelector("input[name='idRegister[]']").value)){
            let SerieNumerofactura = this.obtenerFacturasVinculadasEnAdjuntoList(element.querySelector("input[name='idRegister[]']").value);
            copyItemsDetalleReqPago.push(
                {
                    'id': element.querySelector("input[name='idRegister[]']").value,
                    'descripcion_partida': element.querySelector("p[class='descripcion-partida']").textContent,
                    'descripcion_centro_costo': element.querySelector("p[class='descripcion-centro-costo']").textContent,
                    'descripcion_item': element.querySelector("textarea[name='descripcion[]']").value,
                    'unidad': element.querySelector("select[name='unidad[]']").options[element.querySelector("select[name='unidad[]']").selectedIndex].textContent,
                    'cantidad': element.querySelector("input[name='cantidad[]']").value,
                    'condicion': 1,
                    'precio_unitario': element.querySelector("input[name='precioUnitario[]']").value,
                    'subtotal': element.querySelector("span[name='subtotal[]']").textContent,
                    'motivo': element.querySelector("textarea[name='motivo[]']").value,
                    'factura': SerieNumerofactura
                }
            )
            // }            
        });
        if (this.tieneCamposAlterados(copyItemsDetalleReqPago, this.factura.detalle)) { // revisar si en el detalle de requerimiento de pago cambio algun campo detalle que es distinto al de la factura actual
            let html = '';
            document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tbody").innerHTML = html;
            copyItemsDetalleReqPago.forEach((data, index) => {
                html += `<tr>
                            <td><input type="checkbox" data-id="${data.id}" checked /></td>
                            <td class="descripcion_item">${data.descripcion_item}</td>
                            <td class="unidad">${data.unidad.toUpperCase()}</td>
                            <td class="cantidad">${data.cantidad}</td>
                            <td class="precio_unitario">${data.precio_unitario}</td>
                            <td style="text-align:right;"><input class="hidden subtotal" value="${data.subtotal}" >${(this.factura.id_moneda == 1 ? 'S/' : (this.factura.id_moneda == 2 ? '$' : ''))}${$.number(data.subtotal, 2, '.', ',')}</td>
                            <td>${data.factura}</td>
                        </tr>`;
            });
            document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tbody").insertAdjacentHTML('beforeend', html)
            this.calcularTotalVincularItemsRequerimientoPagoConFactura();
            this.factura =
            {
                'id_adjunto': document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_adjunto']").value,
                'id_doc_com': document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='id_doc_com']").value,
                'fecha_emision': document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='fecha_emision_doc']").value,
                'serie': document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='serie_doc']").value,
                'numero': document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='numero_doc']").value,
                'id_tp_doc': document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_tp_doc']").value,
                'subtotal': this.subtotalFactura,
                'total_igv': this.totalIGVFactura,
                'total_a_pagar': this.totalAPagarFactura,
                'id_moneda': document.querySelector("div[id='modal-requerimiento-pago'] select[name='moneda']").value,
                'id_sede': document.querySelector("div[id='modal-requerimiento-pago'] select[name='empresa']").value,
                'id_condicion': document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_condicion']").value
            };
            this.factura.detalle = [];
            copyItemsDetalleReqPago.forEach((data, index) => {
                this.factura.detalle.push({
                    'id': data.id,
                    'descripcion_item': data.descripcion_item,
                    'unidad': data.unidad,
                    'cantidad': data.cantidad,
                    'id_moneda': this.factura.id_moneda,
                    'precio_unitario': data.precio_unitario,
                    'facturas': ''
                });
            });
            
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='moneda']").value = this.factura.id_moneda;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='simbolo']").value = (this.factura.id_moneda == 1 ? 'S/' : (this.factura.id_moneda == 2 ? '$' : ''));
            document.querySelector("div[id='modal-factura-requerimiento-pago'] input[name='importe']").value = this.factura.total_a_pagar;
            document.querySelector("div[id='modal-factura-requerimiento-pago'] select[name='id_sede']").value = this.factura.id_sede;
            
        }
        document.querySelector("div[id='modal-factura-requerimiento-pago'] button[id='btnConfirmarCrearFactura']").removeAttribute("disabled");
        $("#ListaDetalleRequerimientoPagoYFactura").LoadingOverlay("hide", true);

        // console.log(this.factura);
    }

    calcularTotalVincularItemsRequerimientoPagoConFactura() {
        let TableTBody = document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tbody");
        let sumatotalItem = 0;
        let childrenTableTbody = TableTBody.children;
        for (let index = 0; index < childrenTableTbody.length; index++) {
            sumatotalItem += parseFloat(childrenTableTbody[index].querySelector("input[class~='subtotal']").value ? childrenTableTbody[index].querySelector("input[class~='subtotal']").value : 0);
        }

        const tipoDoc = document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] select[name='categoriaAdjunto']").value;
        let subtotal = 0;
        let total_igv = 0;
        let total_a_pagar = 0;
        if (tipoDoc == 2) { //factura
            subtotal = sumatotalItem / 1.18;
            total_igv = sumatotalItem - subtotal;
            total_a_pagar = sumatotalItem;
        } else {
            total_a_pagar = sumatotalItem;
        }

        this.subtotalFactura = subtotal;
        this.totalIGVFactura = total_igv;
        this.totalAPagarFactura = total_a_pagar;
        document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tfoot label[name='subtotal']").textContent = $.number(subtotal, 2, '.', ',');
        document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tfoot label[name='totalIgv']").textContent = $.number(total_igv, 2, '.', ',');
        document.querySelector("table[id='ListaDetalleRequerimientoPagoYFactura'] tfoot label[name='total']").textContent = $.number(total_a_pagar, 2, '.', ',');
    }

    tieneCamposAlterados(itemsRquerimientoPago, ItemsFactura) {

        let tieneCamposAlterados = false;
        let camposAlterados = 0;
        let itemRevisadosReq = [];
        let itemRevisadosFac = [];
        itemsRquerimientoPago.forEach(itemReQ => {
            ItemsFactura.forEach(itemFact => {
                if (itemReQ.id == itemFact.id) {
                    itemRevisadosReq.push(itemReQ.id)
                    itemRevisadosFac.push(itemFact.id)
                    if (itemReQ.descripcion_item != itemFact.descripcion_item) {
                        camposAlterados++;
                    }
                    if (itemReQ.unidad != itemFact.unidad) {
                        camposAlterados++;
                    }
                    if (itemReQ.cantidad != itemFact.cantidad) {
                        camposAlterados++;
                    }
                    if (itemReQ.precio_unitario != itemFact.precio_unitario) {
                        camposAlterados++;
                    }
                }

            });
        });

        itemRevisadosReq.sort(function (a, b) {
            return a - b;
        });

        itemRevisadosFac.sort(function (a, b) {
            return a - b;
        });

        if (JSON.stringify(itemRevisadosReq) !== JSON.stringify(itemRevisadosFac) || camposAlterados > 0) {
            tieneCamposAlterados = true;
        }



        return tieneCamposAlterados;

    }

 
}


