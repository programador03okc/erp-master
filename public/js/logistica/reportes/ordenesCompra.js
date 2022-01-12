
// ============== View =========================
var vardataTables = funcDatatables();
var $tablaListaOrdenesCompra;
var iTableCounter = 1;
var oInnerTable;
var actionPage = null;

class OrdenesCompra {
    constructor() {
        this.ActualParametroEmpresa= 'SIN_FILTRO';
        this.ActualParametroSede= 'SIN_FILTRO';
        this.ActualParametroFechaDesde= 'SIN_FILTRO';
        this.ActualParametroFechaHasta= 'SIN_FILTRO';
    }

    initializeEventHandler() {
        $('#modal-filtro-reporte-ordenes-compra').on("change", "select.handleChangeFiltroEmpresa", (e) => {
            this.handleChangeFiltroEmpresa(e);
        });
        $('#modal-filtro-reporte-ordenes-compra').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltroOrdenesCompra(e);
        });
        $('#modal-filtro-reporte-ordenes-compra').on("change", "select.handleUpdateValorFiltro", (e) => {
            this.updateValorFiltro();
        });
        $('#modal-filtro-reporte-ordenes-compra').on('hidden.bs.modal', ()=> {
            this.updateValorFiltro();
            if(this.updateContadorFiltro() ==0){
                this.mostrar('SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO','SIN_FILTRO');
            }else{
                this.mostrar(this.ActualParametroEmpresa,this.ActualParametroSede,this.ActualParametroFechaDesde,this.ActualParametroFechaHasta);
            }
        });
    }

    abrirModalFiltrosListaOrdenesCompra(){
        $('#modal-filtro-reporte-ordenes-compra').modal({
            show: true,
            backdrop: 'true'
        });
    }
    
    descargarListaOrdenesCompra(){
        window.open(`reporte-ordenes-compra-excel/${this.ActualParametroEmpresa}/${this.ActualParametroSede}/${this.ActualParametroFechaDesde}/${this.ActualParametroFechaHasta}`);
        // $.ajax({
        //     type: 'POST',
        //     url: `reporte-ordenes-compra-excel`,
        //     dataType: 'JSON',
        //     data:{'idSede':1},
        //     success(response) {
        //         resolve(response) // Resolve promise and go to then() 
        //     },
        //     error: function(err) {
        //     reject(err) // Reject the promise and go to catch()
        //     }
        //     });

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
        let selectElement = document.querySelector("div[id='modal-filtro-reporte-ordenes-compra'] select[name='sede']");

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
        const modalFiltro =document.querySelector("div[id='modal-filtro-reporte-ordenes-compra']");
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
        const modalFiltro = document.querySelector("div[id='modal-filtro-reporte-ordenes-compra']");
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
        const allCheckBoxFiltro = document.querySelectorAll("div[id='modal-filtro-reporte-ordenes-compra'] input[type='checkbox']");
        allCheckBoxFiltro.forEach(element => {
            if(element.checked==true){
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltrosListaOrdenesCompra'] span")?document.querySelector("button[id='btnFiltrosListaOrdenesCompra'] span").innerHTML ='<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : '+contadorCheckActivo:false
        return contadorCheckActivo;
    }

    mostrar(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde='SIN_FILTRO',fechaRegistroHasta='SIN_FILTRO') {
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables();
        $tablaListaOrdenesCompra= $('#listaOrdenesCompra').DataTable({
            'dom': vardataTables[1],
            'buttons': [
                {
                    text: '<i class="fas fa-filter"></i> Filtros : 0',
                    attr: {
                        id: 'btnFiltrosListaOrdenesCompra'
                    },
                    action: () => {
                        this.abrirModalFiltrosListaOrdenesCompra();

                    },
                    className: 'btn-default btn-sm'
                },
                {
                    text: '<i class="far fa-file-excel"></i> Descargar',
                    attr: {
                        id: 'btnDescargarListaOrdenesCompra'
                    },
                    action: () => {
                        this.descargarListaOrdenesCompra();

                    },
                    className: 'btn-default btn-sm'
                }
            ],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-ordenes-compra',
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
                { 'data': 'codigo', 'name': 'log_ord_compra.codigo', 'className': 'text-center' },
                { 'data': 'sede.descripcion', 'name': 'sede.descripcion',  'defaultContent':'' ,'className': 'text-center' },
                { 'data': 'estado.descripcion', 'name': 'estado.descripcion', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'fecha', 'name': 'fecha', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
            ],
            'columnDefs': [

                {
                    'render': function (data, type, row) {
                        // console.log((row.cuadro_costo));
                        return (row.cuadro_costo)!=null && ((row.cuadro_costo)).length >0 && (row.cuadro_costo)[0].codigo_oportunidad !=null ?(row.cuadro_costo)[0].codigo_oportunidad:'(No aplica)';
                    }, targets: 0
                },
                {
                    'render': function (data, type, row) {
                        return  (row.cuadro_costo)!=null && ((row.cuadro_costo)).length >0 ? (moment((row.cuadro_costo)[0].fecha_limite, "DD-MM-YYYY").format("DD-MM-YYYY").toString() ):'';
                    }, targets: 4
                },
                {
                    'render': function (data, type, row) {
                        return (row.cuadro_costo)!=null && ((row.cuadro_costo)).length >0 && (row.cuadro_costo)[0].estado_aprobacion_cuadro != null?(row.cuadro_costo)[0].estado_aprobacion_cuadro:'(No aplica)';
                    }, targets: 5
                },
                {
                    'render': function (data, type, row) {
                        return (row.cuadro_costo)!=null && ((row.cuadro_costo)).length >0 ?(moment((row.cuadro_costo)[0].fecha_aprobacion, "DD-MM-YYYY").format("DD-MM-YYYY").toString()):'';
                    }, targets: 6
                },
                {
                    'render': function (data, type, row) {
                        // console.log(row.cuadro_costo);
                        let fecha_aprobacion_cc = (row.cuadro_costo)!=null && ((row.cuadro_costo).length) >0 ?(row.cuadro_costo)[0].fecha_aprobacion:'';
                        let fecha_oc = row.fecha != null ?row.fecha:'';
                        let dias_restantes = moment(fecha_aprobacion_cc, 'DD-MM-YYYY').diff(moment(fecha_oc, 'DD-MM-YYYY'), 'days');
                        // console.log(dias_restantes);
                        return dias_restantes;
                    }, targets: 7
                },

                {
                    'render': function (data, type, row) {
                        let fecha_aprobacion_cc = (row.cuadro_costo)!=null && ((row.cuadro_costo)).length >0 ?(row.cuadro_costo)[0].fecha_aprobacion:'';
                        let fecha_oc = row.fecha != null ?row.fecha:'';
                        let dias_restantes = moment(fecha_aprobacion_cc, 'DD-MM-YYYY').diff(moment(fecha_oc, 'DD-MM-YYYY'), 'days');

                        
                        return (dias_restantes <=1?'ATENDIDO A TIEMPO':'ATENDIDO FUERA DE TIEMPO');
                    }, targets: 8
                },
                {
                    'render': function (data, type, row) {
                        return moment(row['fecha'], "DD-MM-YYYY").format("DD-MM-YYYY").toString();
                    }, targets: 9
                },
                {
                    'render': function (data, type, row) {
                        let fechaPlazoEntrega = moment(row['fecha'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                        // console.log(row['fecha']);
                        // console.log(row['plazo_entrega']);
                        // console.log(fechaPlazoEntrega);
                        let dias_restantes = moment(fechaPlazoEntrega, 'DD-MM-YYYY').diff(moment( row['fecha'], 'DD-MM-YYYY'), 'days');
                        // console.log(dias_restantes);

                        // var fecha = row['fecha'];
                        // var fecha_formt = moment(row['fecha'],"DD-MM-YYYY").format("DD-MM-YYYY");
                        // var plazo = row['plazo_entrega'];
                        // var fechaPlazo = moment(fecha_formt).add(plazo, 'days')._d;
                        // var nuevoformatoFechaFechaPlazo=moment(fechaPlazo).format("DD-MM-YYYY");
                        // var fechaPlazo_format = moment(fecha).add(fecha_formt, 'days').format("DD-MM-YYYY").toString();
                        
                        // var fechaDif = moment(fechaPlazo).diff(fecha,'days');
                        // console.log(fecha);
                        // console.log(fecha_formt);
                        // console.log(plazo);
                        // console.log(fechaPlazo);
                        // console.log(nuevoformatoFechaFechaPlazo);
                        // console.log(fechaPlazo_format);
                        // console.log(fechaDif);

                        return dias_restantes;
                    }, targets: 10
                },
                {
                    'render': function (data, type, row) {
                        let fechaPlazoEntrega = moment(row['fecha'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                        let dias_restantes = moment(fechaPlazoEntrega, 'DD-MM-YYYY').diff(moment( row['fecha'], 'DD-MM-YYYY'), 'days');

                        return (dias_restantes <=2?'ATENDIDO A TIEMPO':(dias_restantes>=15?'IMPORTACIÓN':'ATENDIDO FUERA DE TIEMPO'));
                    }, targets: 11
                },
                {
                    'render': function (data, type, row) {
                        let fechaPlazoEntrega = moment(row['fecha'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                        return fechaPlazoEntrega;
                    }, targets: 12
                },
   
                // {
                //     'render': function (data, type, row) {
                //         // return `<label class="lbl-codigo handleClickAbrirRequerimiento" title="Abrir Requerimiento">${row.codigo}</label>`;
                //         return `<a href="/necesidades/requerimiento/elaboracion/index?id=${row.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${row.codigo}</a> ${row.tiene_transformacion==true?'<i class="fas fa-random text-danger" title="Con transformación"></i>':''} `;
                //     }, targets: 2
                // },
 
                // {
                //     'render': function (data, type, row) {
                //         switch (row['estado']) {
                //             case 1:
                //                 return '<span class="labelEstado label label-default">' + row['estado_doc'] + '</span>';
                //                 break;
                //             case 2:
                //                 return '<span class="labelEstado label label-success">' + row['estado_doc'] + '</span>';
                //                 break;
                //             case 3:
                //                 return '<span class="labelEstado label label-warning">' + row['estado_doc'] + '</span>';
                //                 break;
                //             case 5:
                //                 return '<span class="labelEstado label label-primary">' + row['estado_doc'] + '</span>';
                //                 break;
                //             case 7:
                //                 return '<span class="labelEstado label label-danger">' + row['estado_doc'] + '</span>';
                //                 break;
                //             default:
                //                 return '<span class="labelEstado label label-default">' + row['estado_doc'] + '</span>';
                //                 break;

                //         }
                //     }, targets: 12, className: 'text-center'
                // },
 
                // {
                //     'render': function (data, type, row) {
                //         let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                //         let containerCloseBrackets = '</div></center>';
                //         let btnEditar = '';
                //         let btnAnular = '';
                //         // let btnMandarAPago = '';
                //         let btnDetalleRapido = '<button type="button" class="btn btn-xs btn-info btnVerDetalle handleClickVerDetalleRequerimientoSoloLectura" data-id-requerimiento="' + row['id_requerimiento'] + '" title="Ver detalle" ><i class="fas fa-eye fa-xs"></i></button>';
                //         let btnTrazabilidad = '<button type="button" class="btn btn-xs btn-default btnVerTrazabilidad handleClickVerTrazabilidadRequerimiento" title="Trazabilidad"><i class="fas fa-route fa-xs"></i></button>';
                //         // if(row.estado ==2){
                //         //         btnMandarAPago = '<button type="button" class="btn btn-xs btn-success" title="Mandar a pago" onClick="listarRequerimientoView.requerimientoAPago(' + row['id_requerimiento'] + ');"><i class="fas fa-hand-holding-usd fa-xs"></i></button>';
                //         //     }
                //         if (row.id_usuario == auth_user.id_usuario && (row.estado == 1 || row.estado == 3)) {
                //             btnEditar = '<button type="button" class="btn btn-xs btn-warning btnEditarRequerimiento handleClickAbrirRequerimiento" title="Editar" ><i class="fas fa-edit fa-xs"></i></button>';
                //             btnAnular = '<button type="button" class="btn btn-xs btn-danger btnAnularRequerimiento handleClickAnularRequerimiento" title="Anular" ><i class="fas fa-times fa-xs"></i></button>';
                //         }
                //         let btnVerDetalle= `<button type="button" class="btn btn-xs btn-primary desplegar-detalle handleClickDesplegarDetalleRequerimiento" data-toggle="tooltip" data-placement="bottom" title="Ver Detalle" data-id="${row.id_requerimiento}">
                //         <i class="fas fa-chevron-down"></i>
                //         </button>`;


                //         return containerOpenBrackets +btnVerDetalle+ btnDetalleRapido + btnTrazabilidad + btnEditar + btnAnular + containerCloseBrackets;
                //     }, targets: 14
                // },

            ],
            'initComplete': function () {
                that.updateContadorFiltro();
                //Boton de busqueda
                const $filter = $('#listaOrdenesCompra_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaOrdenesCompra.search($input.val()).draw();
                })
                //Fin boton de busqueda
                
            },
            "drawCallback": function( settings ) {
                // if($tablaListaOrdenesCompra.rows().data().length==0){
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
                $('#listaOrdenesCompra_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaOrdenesCompra_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaOrdenesCompra").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaOrdenesCompra.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

    }

}