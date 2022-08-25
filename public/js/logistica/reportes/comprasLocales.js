
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
                this.mostrar('SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO');

            }else{

                this.mostrar(
                    this.ActualParametroEmpresa,
                    this.ActualParametroSede,
                    this.ActualParametroFechaDesde,
                    this.ActualParametroFechaHasta,
                    this.ActualParametroFechaDesdeCancelacion,
                    this.ActualParametroFechaHastaCancelacion,
                    this.ActualParametroRazonSocialProveedor
                );
            }
        });

        $('#modal-filtro-reporte-compra-locales').on("change", "select.handleChangeFiltroEmpresa", (e) => {
            this.handleChangeFiltroEmpresa(e);
        });
        $('#modal-filtro-reporte-compra-locales').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltro(e);
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


    estadoCheckFiltro(e){
        const modalFiltro =document.querySelector("div[id='modal-filtro-reporte-compra-locales']");
        switch (e.currentTarget.getAttribute('name')) {
            case 'chkEmpresa':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("select[name='empresa']").removeAttribute("readOnly")
                } else {
                    modalFiltro.querySelector("select[name='empresa']").setAttribute("readOnly", true)
                }
                break;
            case 'chkSede':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("select[name='sede']").removeAttribute("readOnly")
                } else {
                    modalFiltro.querySelector("select[name='sede']").setAttribute("readOnly", true)
                }
                break;
            case 'chkFechaRegistro':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("input[name='fechaRegistroDesde']").removeAttribute("readOnly")
                    modalFiltro.querySelector("input[name='fechaRegistroHasta']").removeAttribute("readOnly")
                } else {
                    modalFiltro.querySelector("input[name='fechaRegistroDesde']").setAttribute("readOnly", true)
                    modalFiltro.querySelector("input[name='fechaRegistroHasta']").setAttribute("readOnly", true)
                }
                break;
            case 'chkFechaCancelacion':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("input[name='fechaCancelacionDesde']").removeAttribute("readOnly")
                    modalFiltro.querySelector("input[name='fechaCancelacionHasta']").removeAttribute("readOnly")
                } else {
                    modalFiltro.querySelector("input[name='fechaCancelacionDesde']").setAttribute("readOnly", true)
                    modalFiltro.querySelector("input[name='fechaCancelacionHasta']").setAttribute("readOnly", true)
                }
                break;
            case 'chkRazonSocialProveedor':
                if (e.currentTarget.checked == true) {
                    modalFiltro.querySelector("input[name='razon_social_proveedor']").removeAttribute("readOnly")
                } else {
                    modalFiltro.querySelector("input[name='razon_social_proveedor']").setAttribute("readOnly", true)
                }
                break;
            default:
                break;
        }
    }
    updateValorFiltro(){
        const modalFiltro = document.querySelector("div[id='modal-filtro-reporte-compra-locales']");
        // if(modalFiltro.querySelector("select[name='empresa']").getAttribute("readonly") ==null){
        //     this.ActualParametroEmpresa=modalFiltro.querySelector("select[name='empresa']").value;
        // }
        // if(modalFiltro.querySelector("select[name='sede']").getAttribute("readonly") ==null){
        //     this.ActualParametroSede=modalFiltro.querySelector("select[name='sede']").value;
        // }

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
    }

    updateContadorFiltro(){
        let contadorCheckActivo= 0;
        const allCheckBoxFiltro = document.querySelectorAll("div[id='modal-filtro-reporte-compra-locales'] input[type='checkbox']");
        allCheckBoxFiltro.forEach(element => {
            if(element.checked==true){
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltrosListaTransitoOrdenesCompra'] span")?(document.querySelector("button[id='btnFiltrosListaTransitoOrdenesCompra'] span").innerHTML ='<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : '+contadorCheckActivo):false
        return contadorCheckActivo;
    }

    mostrar(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde='SIN_FILTRO',fechaRegistroHasta='SIN_FILTRO', fechaRegistroDesdeCancelacion='SIN_FILTRO',fechaRegistroHastaCancelacion='SIN_FILTRO',razonSocialProveedor='SIN_FILTRO') {
        console.log(
            razonSocialProveedor
        );
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
                    'fechaRegistroDesdeCancelacion':fechaRegistroDesdeCancelacion,'fechaRegistroHastaCancelacion':fechaRegistroHastaCancelacion,'razon_social_proveedor':razonSocialProveedor},

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
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'codigo_requerimiento', 'name': 'codigo_requerimiento', 'className': 'text-center' },
                { 'data': 'codigo_producto', 'name': 'codigo_producto', 'className': 'text-center' },
                { 'data': 'descripcion', 'name': 'descripcion', 'className': 'text-center' },
                { 'data': 'rubro_contribuyente', 'name': 'rubro_contribuyente', 'className': 'text-center' },
                { 'data': 'razon_social_contribuyente', 'name': 'razon_social_contribuyente', 'className': 'text-center' },
                { 'data': 'nro_documento_contribuyente', 'name': 'nro_documento_contribuyente', 'className': 'text-center' },
                { 'data': 'direccion_contribuyente', 'name': 'direccion_contribuyente', 'className': 'text-center' },
                { 'data': 'ubigeo_contribuyente', 'name': 'ubigeo_contribuyente', 'className': 'text-center' },
                { 'data': 'fecha_emision_comprobante_contribuyente', 'name': 'fecha_emision_comprobante_contribuyente', 'className': 'text-center' },
                { 'data': 'fecha_pago', 'name': 'fecha_pago', 'className': 'text-center' },
                { 'data': 'tiempo_cancelacion', 'name': 'tiempo_cancelacion', 'className': 'text-center' },
                { 'data': 'moneda_doc_com', 'name': 'moneda_doc_com', 'className': 'text-center' },
                { 'data': 'total_a_pagar_soles', 'name': 'total_a_pagar_soles', 'className': 'text-center' },
                { 'data': 'total_a_pagar_dolares', 'name': 'total_a_pagar_dolares', 'className': 'text-center' },
                { 'data': 'tipo_doc_com', 'name': 'tipo_doc_com', 'className': 'text-center' },
                { 'data': 'nro_doc_com', 'name': 'nro_doc_com', 'className': 'text-center' },
                { 'data': 'descripcion_sede_empresa', 'name': 'descripcion_sede_empresa', 'className': 'text-center' },
                { 'data': 'descripcion_grupo', 'name': 'descripcion_grupo', 'className': 'text-center' }
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
        window.open(`reporte-compras-locales-excel/${this.ActualParametroEmpresa}/${this.ActualParametroSede}/${this.ActualParametroFechaDesde}/${this.ActualParametroFechaHasta}/${this.ActualParametroFechaDesdeCancelacion}/${this.ActualParametroFechaHastaCancelacion}/${this.ActualParametroRazonSocialProveedor}`);
        // this.ActualParametroFechaDesdeCancelacion= 'SIN_FILTRO';
        // this.ActualParametroFechaHastaCancelacion= 'SIN_FILTRO';
        // this.ActualParametroRazonSocialProveedor = 'SIN_FILTRO';
    }

}