
// ============== View =========================
var vardataTables = funcDatatables();
var $tablaListaTransitoOrdenesCompra;
var iTableCounter = 1;
var oInnerTable;
var actionPage = null;

class TransitoOrdenesCompra {
    constructor() {
        this.ActualParametroEmpresa= 'SIN_FILTRO';
        this.ActualParametroSede= 'SIN_FILTRO';
        this.ActualParametroFechaDesde= 'SIN_FILTRO';
        this.ActualParametroFechaHasta= 'SIN_FILTRO';
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
        $('#modal-filtro-reporte-transito-ordenes-compra').on('hidden.bs.modal', ()=> {
            this.updateValorFiltro();
            if(this.updateContadorFiltro() ==0){
                this.mostrar('SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO');
            }else{
                this.mostrar(this.ActualParametroEmpresa,this.ActualParametroSede,this.ActualParametroFechaDesde,this.ActualParametroFechaHasta);
            }
        });
    }

    abrirModalFiltrosListaTransitoOrdenesCompra(){
        $('#modal-filtro-reporte-transito-ordenes-compra').modal({
            show: true,
            backdrop: 'true'
        });
    }

    DescargarListaTransitoOrdenesCompra(){
        window.open(`reporte-transito-ordenes-compra-excel/${this.ActualParametroEmpresa}/${this.ActualParametroSede}/${this.ActualParametroFechaDesde}/${this.ActualParametroFechaHasta}`);

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
        let selectElement = document.querySelector("div[id='modal-filtro-reporte-transito-ordenes-compra'] select[name='sede']");

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


    estadoCheckFiltroOrdenesCompra(e){
        const modalFiltro =document.querySelector("div[id='modal-filtro-reporte-transito-ordenes-compra']");
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
            default:
                break;
        }
    }
    updateValorFiltro(){
        const modalFiltro = document.querySelector("div[id='modal-filtro-reporte-transito-ordenes-compra']");
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
    }

    updateContadorFiltro(){
        let contadorCheckActivo= 0;
        const allCheckBoxFiltro = document.querySelectorAll("div[id='modal-filtro-reporte-transito-ordenes-compra'] input[type='checkbox']");
        allCheckBoxFiltro.forEach(element => {
            if(element.checked==true){
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltrosListaTransitoOrdenesCompra'] span")?(document.querySelector("button[id='btnFiltrosListaTransitoOrdenesCompra'] span").innerHTML ='<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : '+contadorCheckActivo):false
        return contadorCheckActivo;
    }

    mostrar(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde='SIN_FILTRO',fechaRegistroHasta='SIN_FILTRO') {
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables(),
            button_filtro=(array_accesos.find(element => element === 280)?{
                text: '<i class="fas fa-filter"></i> Filtros : 0',
                attr: {
                    id: 'btnFiltrosListaTransitoOrdenesCompra'
                },
                action: () => {
                    this.abrirModalFiltrosListaTransitoOrdenesCompra();

                },
                className: 'btn-default btn-sm'
            }:[]),
            button_descargar=(array_accesos.find(element => element === 274)?{
                text: '<i class="far fa-file-excel"></i> Descargar',
                attr: {
                    id: 'btnDescargarListaTransitoOrdenesCompra'
                },
                action: () => {
                    this.DescargarListaTransitoOrdenesCompra();

                },
                className: 'btn-default btn-sm'
            }:[]);
        $tablaListaTransitoOrdenesCompra= $('#listaTransitoOrdenesCompra').DataTable({
            'dom': vardataTables[1],
            'buttons': [button_filtro,button_descargar],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-transito-ordenes-compra',
                'type': 'POST',
                'data':{'idEmpresa':idEmpresa,'idSede':idSede,'fechaRegistroDesde':fechaRegistroDesde,'fechaRegistroHasta':fechaRegistroHasta},

                beforeSend: data => {

                    $("#listaOrdenesCompra").LoadingOverlay("show", {
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
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'proveedor.contribuyente.razon_social', 'name': 'proveedor.contribuyente.razon_social', 'className': 'text-left' },
                { 'data': 'codigo', 'name': 'log_ord_compra.codigo', 'className': 'text-center' },
                { 'data': 'fecha', 'name': 'fecha', 'className': 'text-center' },
                { 'data': 'sede.descripcion', 'name': 'sede.descripcion',  'defaultContent':'' ,'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-right' },
                { 'data': 'estado.descripcion', 'name': 'estado.descripcion', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-left' }
            ],
            'columnDefs': [

                {
                    'render': function (data, type, row) {
                        return row.cuadro_costo != null && row.cuadro_costo.codigo_oportunidad !=null ?row.cuadro_costo.codigo_oportunidad:'(No aplica)';
                    }, targets: 0
                },
                {
                    'render': function (data, type, row) {
                        return moment(row['fecha'], "DD-MM-YYYY").format("DD-MM-YYYY").toString();
                    }, targets: 3
                },
                {
                    'render': function (data, type, row) {
                        return  (row.moneda.simbolo)+($.number(row.monto,2));
                    }, targets: 5
                },
                {
                    'render': function (data, type, row) {
                        let estimatedTimeOfArrive = moment(row['fecha'], 'DD-MM-YYYY').add(row['plazo_entrega'], 'days').format('DD-MM-YYYY');

                        return  estimatedTimeOfArrive;
                    }, targets: 7
                },
                {
                    'render': function (data, type, row) {
                        return  row.tiene_transformacion ==true?'SI':'NO';
                    }, targets: 8
                },
                {
                    'render': function (data, type, row) {
                        return  row.cantidad_equipos;
                    }, targets: 9
                }

            ],
            'initComplete': function () {
                that.updateContadorFiltro();

                //Boton de busqueda
                const $filter = $('#listaTransitoOrdenesCompra_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaTransitoOrdenesCompra.search($input.val()).draw();
                })
                //Fin boton de busqueda

            },
            "drawCallback": function( settings ) {
                // if($tablaListaTransitoOrdenesCompra.rows().data().length==0){
                //     Lobibox.notify('info', {
                //         title:false,
                //         size: 'mini',
                //         rounded: true,
                //         sound: false,
                //         delayIndicator: false,
                //         msg: `No se encontro data disponible para mostrar`
                //         });
                // }
                //Botón de búsqueda
                $('#listaTransitoOrdenesCompra_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaTransitoOrdenesCompra_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaTransitoOrdenesCompra").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaTransitoOrdenesCompra.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

    }

}
