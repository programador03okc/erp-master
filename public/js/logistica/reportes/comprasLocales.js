
// ============== View =========================
var vardataTables = funcDatatables();
var $tablaListaComprasLocales;
var iTableCounter = 1;
var oInnerTable;
var actionPage = null;


class ComprasLocales {
    constructor() {
        this.ActualParametroEmpresa= 'SIN_FILTRO';
        this.ActualParametroSede= 'SIN_FILTRO';
        this.ActualParametroFechaDesde= 'SIN_FILTRO';
        this.ActualParametroFechaHasta= 'SIN_FILTRO';

        this.ActualParametroFechaDesdeCancelacion= 'SIN_FILTRO';
        this.ActualParametroFechaHastaCancelacion= 'SIN_FILTRO';
        this.ActualParametroRazonSocialProveedor = 'SIN_FILTRO';

        this.ActualParametroGrupo = 'SIN_FILTRO';
        this.ActualParametroProyecto = 'SIN_FILTRO';
        this.ActualParametroObservacionOrden = 'SIN_FILTRO';
        this.ActualParametroEstadoPago = 'SIN_FILTRO';
    }

    initializeEventHandler() {
        $('#modal-filtro-reporte-transito-ordenes-compra').on("change", "select.handleChangeFiltroEmpresa", (e) => {
            this.handleChangeFiltroEmpresa(e);
        });
        $('#modal-filtro-reporte-transito-ordenes-compra').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltroOrdenesCompra(e);
        });
        $('#modal-filtro-reporte-transito-ordenes-compra').on("change", "select.handleUpdateValorFiltro", (e) => {
            this.updateValorFiltro();
        });

        $('#modal-filtro-reporte-compra-locales').on("change", "input.handleUpdateValorFiltro", (e) => {
            this.updateValorFiltro();
        });
        $('#modal-filtro-reporte-compra-locales').on("change", "select.handleUpdateValorFiltro", (e) => {
            this.updateValorFiltro();
        });

        $('#modal-filtro-reporte-compra-locales').on('hidden.bs.modal', ()=> {
            this.updateValorFiltro();

            if(this.updateContadorFiltro() ==0){
                this.mostrar('SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO');

            }else{

                this.mostrar(
                    this.ActualParametroEmpresa,
                    this.ActualParametroSede,
                    this.ActualParametroFechaDesde,
                    this.ActualParametroFechaHasta,
                    this.ActualParametroFechaDesdeCancelacion,
                    this.ActualParametroFechaHastaCancelacion,
                    this.ActualParametroRazonSocialProveedor,
                    this.ActualParametroGrupo,
                    this.ActualParametroProyecto,
                    this.ActualParametroObservacionOrden,
                    this.ActualParametroEstadoPago
                );
            }
        });

        $('#modal-filtro-reporte-compra-locales').on("change", "select.handleChangeFiltroEmpresa", (e) => {
            this.handleChangeFiltroEmpresa(e);
        });
        $('#modal-filtro-reporte-compra-locales').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltro(e);
        });
        $('#listaComprasLocales').on("click", "label.handleClickVerAdjuntosLogisticos", (e) => {
            this.verAdjuntosLogisticos(e.currentTarget);
        });
        $('#listaComprasLocales').on("click", "label.handleClickVerAdjuntosDePago", (e) => {
            this.verAdjuntosDePago(e.currentTarget);
        });
    }

    abrirModalFiltrosListaComprasLocales(){
        $('#modal-filtro-reporte-compra-locales').modal({
            show: true,
            backdrop: 'true'
        });
    }

    getDataSelectSede(id_empresa){

        return new Promise(function(resolve, reject) {
            if(id_empresa >0){
                $.ajax({
                    type: 'GET',
                    url: `listar-sedes-por-empresa/` + id_empresa,
                    dataType: 'JSON',
                    success(response) {
                        resolve(response) // Resolve promise and go to then()
                    },
                    error: function(err) {
                    reject(err) // Reject the promise and go to catch()
                    }
                    });
                }else{
                    resolve(false);
                }
            });
    }

    handleChangeFiltroEmpresa(event) {
        let id_empresa = event.target.value;
        this.getDataSelectSede(id_empresa).then((res) => {
            this.llenarSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })

    }

    llenarSelectSede(array) {
        let selectElement = document.querySelector("div[id='modal-filtro-reporte-compra-locales'] select[name='sede']");

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

    actualizarEstadoCheckDeFiltros(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde='SIN_FILTRO',fechaRegistroHasta='SIN_FILTRO', fechaRegistroDesdeCancelacion='SIN_FILTRO',fechaRegistroHastaCancelacion='SIN_FILTRO',razonSocialProveedor='SIN_FILTRO',idGrupo='SIN_FILTRO',idProyecto='SIN_FILTRO',observacionOrden='SIN_FILTRO',estadoPago='SIN_FILTRO'){

        const modalFiltro =document.querySelector("div[id='modal-filtro-reporte-compra-locales']");
        if(idEmpresa!='SIN_FILTRO' && idEmpresa>0){
            modalFiltro.querySelector("select[name='empresa']").value= idEmpresa;
            modalFiltro.querySelector("select[name='empresa']").removeAttribute("readOnly");
            modalFiltro.querySelector("input[type='checkbox'][name='chkEmpresa']").setAttribute("checked",true);
        }
        if(idGrupo!='SIN_FILTRO' && idGrupo>0){
            modalFiltro.querySelector("select[name='grupo']").value= idGrupo;
            modalFiltro.querySelector("select[name='grupo']").removeAttribute("readOnly");
            modalFiltro.querySelector("input[type='checkbox'][name='chkGrupo']").setAttribute("checked",true);
        }
        if(observacionOrden!='SIN_FILTRO' && observacionOrden.length >0){
            modalFiltro.querySelector("input[name='observacionOrden']").value= observacionOrden;
            modalFiltro.querySelector("input[name='observacionOrden']").removeAttribute("readOnly");
            modalFiltro.querySelector("input[type='checkbox'][name='chkObservacionOrden']").setAttribute("checked",true);
        }
        if(estadoPago!='SIN_FILTRO' && estadoPago >0){
            modalFiltro.querySelector("select[name='estadoPago']").value= estadoPago;
            modalFiltro.querySelector("select[name='estadoPago']").removeAttribute("readOnly");
            modalFiltro.querySelector("input[type='checkbox'][name='chkEstadoPago']").setAttribute("checked",true);
        }

        this.updateContadorFiltro();
    }

    estadoCheckFiltro(e){
        const modalFiltro =document.querySelector("div[id='modal-filtro-reporte-compra-locales']");
        switch (e.currentTarget.getAttribute('name')) {
            case 'chkEmpresa':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("select[name='empresa']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("select[name='empresa']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("select[name='empresa']").value = 'SIN_FILTRO';
                }
                break;
            case 'chkSede':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("select[name='sede']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("select[name='sede']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("select[name='sede']").value = 'SIN_FILTRO';

                }
                break;
            case 'chkGrupo':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("select[name='grupo']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("select[name='grupo']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("select[name='grupo']").value = 'SIN_FILTRO';

                }
                break;
            case 'chkProyecto':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("select[name='proyecto']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("select[name='proyecto']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("select[name='proyecto']").value = 'SIN_FILTRO';

                }
            case 'chkObservacionOrden':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("input[name='observacionOrden']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("input[name='observacionOrden']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("input[name='observacionOrden']").value = 'SIN_FILTRO';
                }
                break;
            case 'chkEstadoPago':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("select[name='estadoPago']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("select[name='estadoPago']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("select[name='estadoPago']").value = 'SIN_FILTRO';

                }
                break;
            case 'chkFechaRegistro':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("input[name='fechaRegistroDesde']").removeAttribute("readOnly");
                    modalFiltro.querySelector("input[name='fechaRegistroHasta']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("input[name='fechaRegistroDesde']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("input[name='fechaRegistroHasta']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("input[name='fechaRegistroDesde']").value = 'SIN_FILTRO';
                    modalFiltro.querySelector("input[name='fechaRegistroHasta']").value = 'SIN_FILTRO';

                }
                break;
            case 'chkFechaCancelacion':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("input[name='fechaCancelacionDesde']").removeAttribute("readOnly");
                    modalFiltro.querySelector("input[name='fechaCancelacionHasta']").removeAttribute("readOnly");
                } else {
                    modalFiltro.querySelector("input[name='fechaCancelacionDesde']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("input[name='fechaCancelacionHasta']").setAttribute("readOnly", true);
                    modalFiltro.querySelector("input[name='fechaCancelacionDesde']").value = 'SIN_FILTRO';
                    modalFiltro.querySelector("input[name='fechaCancelacionHasta']").value = 'SIN_FILTRO';
                }
                break;
            case 'chkRazonSocialProveedor':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("input[name='razon_social_proveedor']").removeAttribute("readOnly")
                } else {
                    modalFiltro.querySelector("input[name='razon_social_proveedor']").setAttribute("readOnly", true)
                    modalFiltro.querySelector("input[name='razon_social_proveedor']").value = 'SIN_FILTRO';
                }
                break;
            default:
                break;
        }
    }
    updateValorFiltro(){
        const modalFiltro = document.querySelector("div[id='modal-filtro-reporte-compra-locales']");
        if(modalFiltro.querySelector("select[name='empresa']").getAttribute("readonly") ==null){
            this.ActualParametroEmpresa=modalFiltro.querySelector("select[name='empresa']").value;
        }
        if(modalFiltro.querySelector("select[name='sede']").getAttribute("readonly") ==null){
            this.ActualParametroSede=modalFiltro.querySelector("select[name='sede']").value;
        }

        if(modalFiltro.querySelector("input[name='fechaRegistroDesde']").getAttribute("readonly") ==null){
            this.ActualParametroFechaDesde=modalFiltro.querySelector("input[name='fechaRegistroDesde']").value.length>0?modalFiltro.querySelector("input[name='fechaRegistroDesde']").value:'SIN_FILTRO';
        }
        if(modalFiltro.querySelector("input[name='fechaRegistroHasta']").getAttribute("readonly") ==null){
            this.ActualParametroFechaHasta=modalFiltro.querySelector("input[name='fechaRegistroHasta']").value.length>0?modalFiltro.querySelector("input[name='fechaRegistroHasta']").value:'SIN_FILTRO';
        }

        if(modalFiltro.querySelector("input[name='fechaCancelacionDesde']").getAttribute("readonly") ==null){
            this.ActualParametroFechaDesdeCancelacion=modalFiltro.querySelector("input[name='fechaCancelacionDesde']").value.length>0?modalFiltro.querySelector("input[name='fechaCancelacionDesde']").value:'SIN_FILTRO';
        }
        if(modalFiltro.querySelector("input[name='fechaCancelacionHasta']").getAttribute("readonly") ==null){
            this.ActualParametroFechaHastaCancelacion=modalFiltro.querySelector("input[name='fechaCancelacionHasta']").value.length>0?modalFiltro.querySelector("input[name='fechaCancelacionHasta']").value:'SIN_FILTRO';
        }
        if(modalFiltro.querySelector("input[name='razon_social_proveedor']").getAttribute("readonly") ==null){
            this.ActualParametroRazonSocialProveedor=modalFiltro.querySelector("input[name='razon_social_proveedor']").value.length>0?modalFiltro.querySelector("input[name='razon_social_proveedor']").value:'SIN_FILTRO';
        }
        if(modalFiltro.querySelector("select[name='grupo']").getAttribute("readonly") ==null){
            this.ActualParametroGrupo=modalFiltro.querySelector("select[name='grupo']").value.length>0?modalFiltro.querySelector("select[name='grupo']").value:'SIN_FILTRO';
        }
        if(modalFiltro.querySelector("select[name='proyecto']").getAttribute("readonly") ==null){
            this.ActualParametroProyecto=modalFiltro.querySelector("select[name='proyecto']").value.length>0?modalFiltro.querySelector("select[name='proyecto']").value:'SIN_FILTRO';
        }
        if(modalFiltro.querySelector("input[name='observacionOrden']").getAttribute("readonly") ==null){
            this.ActualParametroObservacionOrden=modalFiltro.querySelector("input[name='observacionOrden']").value.length>0?modalFiltro.querySelector("input[name='observacionOrden']").value:'SIN_FILTRO';
        }
        if(modalFiltro.querySelector("select[name='estadoPago']").getAttribute("readonly") ==null){
            this.ActualParametroEstadoPago=modalFiltro.querySelector("select[name='estadoPago']").value.length>0?modalFiltro.querySelector("select[name='estadoPago']").value:'SIN_FILTRO';
        }
    }

    updateContadorFiltro(){
        let contadorCheckActivo= 0;
        const allCheckBoxFiltro = document.querySelectorAll("div[id='modal-filtro-reporte-compra-locales'] input[type='checkbox']");
        allCheckBoxFiltro.forEach(element => {
            if(element.checked==true){
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltrosListaComprasLocales'] span")?(document.querySelector("button[id='btnFiltrosListaComprasLocales'] span").innerHTML ='<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : '+contadorCheckActivo):false
        return contadorCheckActivo;
    }

    mostrar(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde='SIN_FILTRO',fechaRegistroHasta='SIN_FILTRO', fechaRegistroDesdeCancelacion='SIN_FILTRO',fechaRegistroHastaCancelacion='SIN_FILTRO',razonSocialProveedor='SIN_FILTRO',idGrupo='SIN_FILTRO',idProyecto='SIN_FILTRO',observacionOrden='SIN_FILTRO',estadoPago='SIN_FILTRO') {

        this.actualizarEstadoCheckDeFiltros(idEmpresa,idSede, fechaRegistroDesde,fechaRegistroHasta, fechaRegistroDesdeCancelacion,fechaRegistroHastaCancelacion,razonSocialProveedor,idGrupo,idProyecto,observacionOrden,estadoPago);
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables();
        $tablaListaComprasLocales= $('#listaComprasLocales').DataTable({
            'dom': vardataTables[1],
            'buttons': [
                {
                    text: '<i class="fas fa-filter"></i> Filtros : 0',
                    attr: {
                        id: 'btnFiltrosListaComprasLocales'
                    },
                    action: () => {
                        this.abrirModalFiltrosListaComprasLocales();

                    },
                    className: 'btn-default btn-sm'
                },
                {
                    text: '<i class="far fa-file-excel"></i> Descargar',
                    attr: {
                        id: 'btnDescargarListaComprasLocales'
                    },
                    action: () => {
                        this.DescargarListaComprasLocales();

                    },
                    className: 'btn-default btn-sm'
                }
            ],
            'language': vardataTables[0],
            'order': [[10, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-compras-locales',
                'type': 'POST',
                'data':{
                    'idEmpresa':idEmpresa,
                    'idSede':idSede,
                    'fechaRegistroDesde':fechaRegistroDesde,
                    'fechaRegistroHasta':fechaRegistroHasta,
                    'fechaRegistroDesdeCancelacion':fechaRegistroDesdeCancelacion,'fechaRegistroHastaCancelacion':fechaRegistroHastaCancelacion,'razon_social_proveedor':razonSocialProveedor,
                    'idGrupo':idGrupo,
                    'idProyecto':idProyecto,
                    'observacionOrden':observacionOrden,
                    'estadoPago':estadoPago

                },

                beforeSend: data => {

                    $("#listaComprasLocales").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                // data: function (params) {
                //     return Object.assign(params, Util.objectifyForm($('#form-requerimientosElaborados').serializeArray()))
                // }

            },
            'columns': [
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center','render': function (data, type, row){
                    return `<a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${row.id_orden_compra}" target="_blank">${row.codigo}</a>`;
                }},
                { 'data': 'codigo_requerimiento', 'name': 'codigo_requerimiento', 'className': 'text-center','render': function (data, type, row){
                    return `<a href="/necesidades/requerimiento/elaboracion/imprimir-requerimiento-pdf/${row.id_requerimiento}/0" target="_blank">${row.codigo_requerimiento}</a>`;
                }},
                { 'data': 'codigo_producto', 'name': 'codigo_producto', 'className': 'text-center' },
                { 'data': 'descripcion', 'name': 'descripcion', 'className': 'text-center' },
                { 'data': 'rubro_contribuyente', 'name': 'rubro_contribuyente', 'className': 'text-center' },
                { 'data': 'razon_social_contribuyente', 'name': 'razon_social_contribuyente', 'className': 'text-center' },
                { 'data': 'nro_documento_contribuyente', 'name': 'nro_documento_contribuyente', 'className': 'text-center' },
                { 'data': 'direccion_contribuyente', 'name': 'direccion_contribuyente', 'className': 'text-center' },
                { 'data': 'ubigeo_contribuyente', 'name': 'ubigeo_contribuyente', 'className': 'text-center' },
                { 'data': 'fecha_emision_comprobante_contribuyente', 'name': 'fecha_emision_comprobante_contribuyente', 'className': 'text-center','render': function (data, type, row){
                    return `<label class="lbl-codigo handleClickVerAdjuntosLogisticos" data-id-orden="${row.id_orden_compra}">${row.fecha_emision_comprobante_contribuyente}</label>`;
                } },
                { 'data': 'fecha_pago', 'name': 'fecha_pago', 'className': 'text-center','render': function (data, type, row){
                    return `<label class="lbl-codigo handleClickVerAdjuntosDePago" data-id-orden="${row.id_orden_compra}">${row.fecha_pago}</label>`;
                }  },
                { 'data': 'tiempo_cancelacion', 'name': 'tiempo_cancelacion', 'className': 'text-center' },
                { 'data': 'cantidad', 'name': 'cantidad', 'className': 'text-center' },
                { 'data': 'moneda_orden', 'name': 'moneda_orden', 'className': 'text-center' },
                { 'data': 'total_precio_soles_item', 'name': 'total_precio_soles_item', 'className': 'text-center' },
                { 'data': 'total_precio_dolares_item', 'name': 'total_precio_dolares_item', 'className': 'text-center' },
                { 'data': 'total_a_pagar_soles', 'name': 'total_a_pagar_soles', 'className': 'text-center' },
                { 'data': 'total_a_pagar_dolares', 'name': 'total_a_pagar_dolares', 'className': 'text-center' },
                { 'data': 'tipo_doc_com', 'name': 'tipo_doc_com', 'className': 'text-center' },
                { 'data': 'nro_comprobante', 'name': 'nro_comprobante', 'className': 'text-center'},
                { 'data': 'descripcion_sede_empresa', 'name': 'descripcion_sede_empresa', 'className': 'text-center' },
                { 'data': 'descripcion_grupo', 'name': 'descripcion_grupo', 'className': 'text-center' },
                { 'data': 'descripcion_proyecto', 'name': 'descripcion_proyecto', 'className': 'text-left' }
            ],
            'columnDefs': [

            ],
            'initComplete': function () {
                that.updateContadorFiltro();

                //Boton de busqueda
                const $filter = $('#listaComprasLocales_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaComprasLocales.search($input.val()).draw();
                })
                //Fin boton de busqueda

            },
            "drawCallback": function( settings ) {

                //Botón de búsqueda
                $('#listaComprasLocales_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaComprasLocales_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaComprasLocales").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaComprasLocales.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

    }


    DescargarListaComprasLocales(){
        window.open(`reporte-compras-locales-excel/${this.ActualParametroEmpresa}/${this.ActualParametroSede}/${this.ActualParametroFechaDesde}/${this.ActualParametroFechaHasta}/${this.ActualParametroFechaDesdeCancelacion}/${this.ActualParametroFechaHastaCancelacion}/${this.ActualParametroRazonSocialProveedor}/${this.ActualParametroGrupo}/${this.ActualParametroProyecto}/${this.ActualParametroObservacionOrden}/${this.ActualParametroEstadoPago}`);
    }
    

    verAdjuntosLogisticos(obj){
        console.log(obj.dataset.idOrden);
        document.querySelector("div[id='modal-lista-adjuntos'] span[id='modal-title']").textContent = "logísticos";
        $('#modal-lista-adjuntos #listaAdjuntos').html(`<tr> <td style="text-align:center;" colspan="3"></td></tr>`);

        $('#modal-lista-adjuntos').modal({
            show: true,
            backdrop: 'true'
        });

        this.obteneAdjuntosLogisticos(obj.dataset.idOrden).then((res) => {

            let htmlAdjunto = '';
            if (res.length > 0) {
                (res).forEach(element => {

                        htmlAdjunto+= '<tr id="'+element.id_adjunto+'">'
                            htmlAdjunto+='<td>'
                                htmlAdjunto+='<a href="/files/logistica/comporbantes_proveedor/'+element.archivo+'" target="_blank">'+element.archivo+'</a>'
                            htmlAdjunto+='</td>'
                        htmlAdjunto+= '</tr>'

                });
            }else{
                htmlAdjunto = `<tr>
                <td style="text-align:center;" colspan="3">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            $('#modal-lista-adjuntos #listaAdjuntos').html(htmlAdjunto)


        }).catch(function (err) {
            console.log(err)
        })
    }

    verAdjuntosDePago(obj){

        document.querySelector("div[id='modal-lista-adjuntos'] span[id='modal-title']").textContent = "de pago";
        $('#modal-lista-adjuntos #listaAdjuntos').html(`<tr> <td style="text-align:center;" colspan="3"></td></tr>`);
        $('#modal-lista-adjuntos').modal({
            show: true,
            backdrop: 'true'
        });

        this.obteneAdjuntosPago(obj.dataset.idOrden).then((res) => {

            let htmlPago = '';
            console.log(res.data);
            if (res.data.length > 0) {
                (res.data).forEach(element => {

                        htmlPago+= '<tr id="'+element.id_orden+'">'

                            element.adjuntos.forEach(nombreAdjunto => {
                                htmlPago+='<td>'
                                    htmlPago+='<a href="/files/tesoreria/pagos/'+nombreAdjunto+'" target="_blank">'+nombreAdjunto+'</a>'
                                htmlPago+='</td>'
                                
                            });
                        htmlPago+= '</tr>'

                });
            }else{
                htmlPago = `<tr>
                <td style="text-align:center;" colspan="3">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            $('#modal-lista-adjuntos #listaAdjuntos').html(htmlPago)


        }).catch(function (err) {
            console.log(err)
        })
    }


    obteneAdjuntosPago(idOrden) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-archivos-adjuntos-pago-requerimiento/${idOrden}`,
                dataType: 'JSON',
                beforeSend: (data) => {
                $('#modal-lista-adjuntos').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
                success(response) {
                    $('#modal-lista-adjuntos').LoadingOverlay("hide", true);
                    resolve(response);
                },
                error: function (err) {
                    $('#modal-lista-adjuntos').LoadingOverlay("hide", true);
                    reject(err)
                }
            });
        });
    }

    obteneAdjuntosLogisticos(id_orden) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-archivos-adjuntos-orden/${id_orden}`,
                dataType: 'JSON',
                beforeSend: (data) => {
                // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("show", {
                //     imageAutoResize: true,
                //     progress: true,
                //     imageColor: "#3c8dbc"
                // });
            },
                success(response) {
                    // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                    resolve(response);
                },
                error: function (err) {
                    // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                    reject(err)
                }
            });
        });
    }



}
