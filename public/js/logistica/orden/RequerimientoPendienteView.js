
// ============== View =========================
var vardataTables = funcDatatables();

var itemsParaCompraList=[]
var reqTrueList=[]
var listCheckReq=[]
var infoStateInput = [];
var tempDetalleItemsParaCompraCC = [];

var tablaListaRequerimientosPendientes;
var iTableCounter = 1;
var oInnerTable;

var objBtnMapeo;
var trRequerimientosPendientes;
class RequerimientoPendienteView {
    constructor(requerimientoPendienteCtrl){
        this.requerimientoPendienteCtrl = requerimientoPendienteCtrl;
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        
        vista_extendida();
    }

    initializeEventHandler(){
        // $('#modal-atender-con-almacen').on("click","button.handleClickGuardarAtendidoConAlmacen", ()=>{
        //     this.guardarAtendidoConAlmacen();
        // });

        $('#modal-filtro-requerimientos-pendientes').on("click","input[type=checkbox]", (e)=>{
            this.estadoCheckFiltroRequerimientosPendientes(e);
        });

        $('#requerimientos_pendientes').on("click","button.handleClickCrearOrdenCompra", ()=>{
            this.crearOrdenCompra();
        });

        $('#listaRequerimientosPendientes tbody').on("click","label.handleClickAbrirRequerimiento",(e)=>{
            this.abrirRequerimiento(e.currentTarget.dataset.idRequerimiento);
        });
        // $('#listaRequerimientosPendientes tbody').on("click","button.handleClickObservarRequerimientoLogistica",(e)=>{
        //     this.observarRequerimientoLogistica(e.currentTarget.dataset.idRequerimiento);
        // });
        // $('#form-observar-requerimiento-logistica').on("click","button.handleClickRegistrarObservaciónRequerimientoLogistica",()=>{
        //     this.registrarObservaciónRequerimientoLogistica();
        // });

        $('#listaRequerimientosPendientes tbody').on("click","button.handleClickVerDetalleRequerimiento",(e)=>{
            // var data = $('#listaRequerimientosPendientes').DataTable().row($(this).parents("tr")).data();
            this.verDetalleRequerimiento(e.currentTarget);
        });

        $('#listaRequerimientosPendientes tbody').on("click","button.handleClickAtenderConAlmacen",(e)=>{
            this.atenderConAlmacen(e.currentTarget);
        });

        $('#listaRequerimientosPendientes tbody').on("click","button.handleClickOpenModalCuadroCostos",(e)=>{
            this.openModalCuadroCostos(e.currentTarget);
        });

        $('#listaRequerimientosPendientes tbody').on("click","button.handleClickCrearOrdenCompraPorRequerimiento",(e)=>{
            this.crearOrdenCompraPorRequerimiento(e.currentTarget);
        });
        $('#listaRequerimientosPendientes tbody').on("click","button.handleClickCrearOrdenServicioPorRequerimiento",(e)=>{
            this.crearOrdenServicioPorRequerimiento(e.currentTarget);
        });

        // $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("change","select.handleChangeUpdateSelectAlmacenAAtender",(e)=>{
        //     this.updateSelectAlmacenAAtender(e.currentTarget);
        // });

        // $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("blur","input.handleBlurUpdateInputCantidadAAtender", (e)=>{
        //     this.updateInputCantidadAAtender(e.currentTarget);
        // });

        $('#modal-filtro-requerimientos-pendientes').on("click","button.handleClickLimpiarFiltroRequerimientosPendientes",()=>{
            this.limpiarFiltroRequerimientosPendientes();
        });
        $('#modal-filtro-requerimientos-pendientes').on("click","button.handleClickAplicarFiltroRequerimientosPendientes",()=>{
            this.aplicarFiltroRequerimientosPendientes();
        });
        $('#modal-filtro-requerimientos-pendientes').on("change","select.handleChangeFiltroEmpresa",(e)=>{
            this.getDataSelectSede(e.currentTarget.value);
        });


        $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("click","button.handleClickAbrirModalNuevaReserva", (e)=>{
            this.abrirModalNuevaReserva(e.currentTarget);
        });
        $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("click","button.handleClickAbrirModaHistorialReserva", (e)=>{
            this.abrirModalHistorialReserva(e.currentTarget);
        });
        $('#modal-nueva-reserva').on("click","button.handleClickAgregarReserva", (e)=>{
            e.currentTarget.setAttribute("disabled",true);
            this.agregarReserva(e.currentTarget);
        });
        $('#modal-nueva-reserva').on("click","button.handleClickAnularReserva", (e)=>{
            this.anularReserva(e.currentTarget);
        });

        
    }

    estadoCheckFiltroRequerimientosPendientes(e){
        switch (e.currentTarget.getAttribute('name')) {
            case 'chkEmpresa':
                if(e.currentTarget.checked == true){
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='empresa']").removeAttribute("disabled")
                }else{
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='empresa']").setAttribute("disabled",true)
                }
                break;
        
            default:
                break;
        }
    }

    renderRequerimientoPendienteList(empresa,sede,fechaRegistroDesde,fechaRegistroHasta, reserva, orden) {
        this.requerimientoPendienteCtrl.getRequerimientosPendientes(empresa,sede,fechaRegistroDesde,fechaRegistroHasta, reserva, orden).then( (res) =>{
            if(res.length){
                this.construirTablaListaRequerimientosPendientes(res);
                $('#requerimientos_pendientes').LoadingOverlay("hide", true);
            } else {
                $('#requerimientos_pendientes').LoadingOverlay("hide", true);
                console.log(res);
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar traer la lista de requerimientos pendientes, por favor vuelva a intentarlo',
                    'error'
                );
            }
        }).catch( (err) =>{
            console.log(err)
        })
    }

    getDataSelectSede(idEmpresa){

        if (idEmpresa > 0) {
            this.requerimientoPendienteCtrl.obtenerSede(idEmpresa).then((res)=> {
                this.llenarSelectFiltroSede(res);
            }).catch(function (err) {
                console.log(err)
            })
        }else{
            let selectElement = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']");
            if (selectElement.options.length > 0) {
                let i, L = selectElement.options.length - 1;
                for (i = L; i >= 0; i--) {
                    selectElement.remove(i);
                }
                let option = document.createElement("option");
    
                option.value='SIN_FILTRO';
                option.text='-----------------';
                selectElement.add(option);
            }
        }
        return false;
    }

    llenarSelectFiltroSede(array) {
        let selectElement = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']");
        if (selectElement.options.length > 0) {
            let i, L = selectElement.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }
        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_sede;
            option.setAttribute('data-ubigeo', element.id_ubigeo);
            option.setAttribute('data-name-ubigeo', element.ubigeo_descripcion);
            if (element.codigo == 'LIMA' || element.codigo == 'Lima') { // default sede lima
                option.selected=true;

            }

            selectElement.add(option);
        });

    }

    // observarRequerimientoLogistica(idRequerimiento){
    //     $('#modal-observar-requerimiento-logistica').modal({
    //         show: true
    //     });
    // }
    // registrarObservaciónRequerimientoLogistica(){
    //         Lobibox.notify('success', {
    //             title:false,
    //             size: 'mini',
    //             rounded: true,
    //             sound: false,
    //             delayIndicator: false,
    //             msg: `Observación registrada`
    //         });
    // }




    abrirRequerimiento(idRequerimiento){
        // Abrir nuevo tab
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
        // var win = window.open(url, '_blank');
        var win = location.href=url;
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();
    }

    construirTablaListaRequerimientosPendientes(data) {
        let that =this;
        that.requerimientoPendienteCtrl.limpiarTabla('listaRequerimientosPendientes');

        tablaListaRequerimientosPendientes= $('#listaRequerimientosPendientes').DataTable({
            'dom': vardataTables[1],
            'buttons': [
                {
                    text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo',
                    attr:  {
                        disabled: true,
                        id: 'btnCrearOrdenCompra'
                    },
                    action: ()=>{
                        this.crearOrdenCompra();

                    },
                    className: 'btn-warning btn-sm'
                },
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
                    attr:  {
                        id: 'btnFiltrosRequerimientosPendientes'
                    },
                    action: ()=>{
                        this.abrirModalFiltrosRequerimientosPendientes();

                    },
                    className: 'btn-default btn-sm'
                }
            ],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'destroy': true,
            "bInfo": true,
            "bLengthChange": false,

            'data': data,
            'columns': [
                { 'data': 'id_requerimiento' },
                {
                    render: function (data, type, row) {
                        return `<div class="text-center"><input type="checkbox" data-mapeos-pendientes="${row.count_pendientes}" data-mapeados="${row.count_mapeados}" data-id-requerimiento="${row.id_requerimiento}" /></div>`;
                    }
                },
                { 'data': 'empresa_sede' },
                {
                    render: function (data, type, row) {
                        return `<label class="lbl-codigo handleClickAbrirRequerimiento" title="Abrir Requerimiento" data-id-requerimiento="${row.id_requerimiento}">${row.codigo}</label>`;
                    }
                },

                { 'data': 'fecha_registro' },
                { 'data': 'fecha_entrega' },
                { 'data': 'concepto' },
                { 'data': 'tipo_req_desc' },
                {
                    render: function (data, type, row) {
                        return row.division !=null?eval(row.division).join():'';
                    }
                },
                {
                    render: function (data, type, row) {
                        return row.cc_solicitado_por!=null?row.cc_solicitado_por:(row.solicitado_por!=null?row.solicitado_por:'');
                    }
                },
                {'render':
                    function (data, type, row){
                        return '<span class="label label-default estadoRequerimiento">' + row['estado_doc'] + '</span>';

                    }
                },
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
                            // let btnObservarRequerimientoLogistica= '<button type="button" class="btn btn-default btn-xs handleClickObservarRequerimientoLogistica" name="btnObservarRequerimientoLogistica" title="Observar requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" style="background: gold;" ><i class="fas fa-exclamation-triangle fa-sm"></i></button>';

                            // let btnAgregarItemBase = '<button type="button" class="btn btn-success btn-xs" name="btnAgregarItemBase" title="Mapear productos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalAgregarItemBase(this);"  ><i class="fas fa-sign-out-alt"></i></button>';
                            let btnMapearProductos = '<button type="button" class="mapeo btn btn-success btn-xs" title="Mapear productos" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo="' + row.codigo + '"  ><i class="fas fa-sign-out-alt"></i> <span class="badge" title="Cantidad items sin mapear" name="cantidadAdjuntosRequerimiento" style="position:absolute;border: solid 0.1px;z-index: 9;top: -9px;left: 0px;font-size: 0.9rem;">'+row.count_pendientes+'</span></button>';
                            let btnAtenderAlmacen='';
                            let btnCrearOrdenCompra = '';
                                // if(row.count_pendientes ==0){
                                if(row.count_mapeados > 0){
                                    btnAtenderAlmacen = '<button type="button" class="btn btn-primary btn-xs handleClickAtenderConAlmacen" name="btnOpenModalAtenderConAlmacen" title="Reserva en almacén" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '"><i class="fas fa-dolly fa-sm"></i></button>';
                                    btnCrearOrdenCompra = '<button type="button" class="btn btn-warning btn-xs handleClickCrearOrdenCompraPorRequerimiento" name="btnCrearOrdenCompraPorRequerimiento" title="Crear Orden de Compra" data-id-requerimiento="' + row.id_requerimiento + '"  ><i class="fas fa-file-invoice"></i></button>';
                                }
                            let btnCrearOrdenServicio = '<button type="button" class="btn btn-danger btn-xs handleClickCrearOrdenServicioPorRequerimiento" name="btnCrearOrdenServicioPorRequerimiento" title="Crear Orden de Servicio" data-id-requerimiento="' + row.id_requerimiento + '"  ><i class="fas fa-file-invoice fa-sm"></i></button>';
                            let btnVercuadroCostos ='';
                            if(row.id_tipo_requerimiento ==1){
                                btnVercuadroCostos= '<button type="button" class="btn btn-info btn-xs handleClickOpenModalCuadroCostos" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-eye fa-sm"></i></button>';
                            }

                            let closeDiv = '</div>';

                            if (row.cantidad_tipo_servicio > 0) {
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
                    trs[i].childNodes[1].childNodes[0].childNodes[0].addEventListener('click', handleTrClick);
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
                    if(this.dataset.mapeados == 0){
                        this.checked=false;
                        Swal.fire(
                            '',
                            'No puede generar una orden si tiene aun productos sin mapear',
                            'warning'
                        );
                    }else{
                        let id = this.dataset.idRequerimiento
                        let stateCheck = this.checked
                        that.requerimientoPendienteCtrl.controlListCheckReq(id, stateCheck);

                    }
                }

            },
            'columnDefs': [
                { 'aTargets': [0], 'sClass': 'invisible','sWidth': '0%' },
                { 'aTargets': [1], 'sWidth': '3%' },
                { 'aTargets': [2], 'sWidth': '5%' },
                { 'aTargets': [3], 'sWidth': '5%' },
                { 'aTargets': [4], 'sWidth': '5%', 'className': 'text-center' },
                { 'aTargets': [5], 'sWidth': '5%', 'className': 'text-center' },
                { 'aTargets': [6], 'sWidth': '20%', 'className': 'text-left' },
                { 'aTargets': [7], 'sWidth': '5%', 'className': 'text-center'},
                { 'aTargets': [8], 'sWidth': '5%', 'className': 'text-center' },
                { 'aTargets': [9], 'sWidth': '10%','className': 'text-left' },
                { 'aTargets': [10], 'sWidth': '5%','className': 'text-center' },
                { 'aTargets': [11], 'sWidth': '5%', 'className': 'text-center' }
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.tiene_transformacion == true) {
                    $(row.childNodes[3]).css('background-color', '#d8c74ab8');
                    $(row.childNodes[3]).css('font-weight', 'bold');
                }
                else if (data.tiene_transformacion == false) {
                    $(row.childNodes[3]).css('background-color', '#b498d0');
                    $(row.childNodes[3]).css('font-weight', 'bold');
                }

            }
        });
        
        $('#listaRequerimientosPendientes tbody').on("click","button.mapeo", function(e){
            var id_requerimiento = $(this).data('idRequerimiento');
            var codigo = $(this).data('codigo');
            objBtnMapeo= e.currentTarget;
            // console.log(objBtnMapeo);
            
            $('#modal-mapeoItemsRequerimiento').modal({
                show: true
            });
            $('[name=id_requerimiento]').val(id_requerimiento);
            $('#cod_requerimiento').text(codigo);
            listarItemsRequerimientoMapeo(id_requerimiento);
            
            $('#submit_mapeoItemsRequerimiento').removeAttr('disabled');
        });
    }
    limpiarFiltroRequerimientosPendientes(){
        let allSelectFiltroRequerimientosPendientes= document.querySelectorAll("div[id='formFiltroListaRequerimientosPendientes'] select");
        allSelectFiltroRequerimientosPendientes.forEach(element => {
            element.value='SIN_FILTRO';
        });
        
        let allInputFiltroRequerimientosPendientes= document.querySelectorAll("div[id='formFiltroListaRequerimientosPendientes'] input");
        allInputFiltroRequerimientosPendientes.forEach(element => {
            element.value='';
        });

        let selectElement = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']");
        if (selectElement.options.length > 0) {
            let i, L = selectElement.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
            let option = document.createElement("option");

            option.value='SIN_FILTRO';
            option.text='-----------------';
            selectElement.add(option);
        }
    }

    aplicarFiltroRequerimientosPendientes(){
            let empresa = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='empresa']").value;
            let sede = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']").value;
            let fechaRegistroDesde = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] input[name='fechaRegistroDesde']").value;
            let fechaRegistroHasta = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] input[name='fechaRegistroHasta']").value;
            let reserva = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='reserva']").value;
            let orden = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='orden']").value;
 
            this.renderRequerimientoPendienteList(empresa,sede,(fechaRegistroDesde==''?'SIN_FILTRO':fechaRegistroDesde),(fechaRegistroHasta==''?'SIN_FILTRO':fechaRegistroHasta),reserva,orden);
            $('#modal-filtro-requerimientos-pendientes').modal('hide');

    }

    verDetalleRequerimiento(obj){
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
            this.buildFormatListaRequerimientosPendientes(obj,iTableCounter, id, row);
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

    buildFormatListaRequerimientosPendientes(obj,table_id, id, row) {
        obj.setAttribute('disabled',true);
        
        $(obj).LoadingOverlay("show", {
            imageAutoResize: true,
            progress: true,
            imageColor: "#3c8dbc"
        });
        this.requerimientoPendienteCtrl.obtenerDetalleRequerimientos(id).then((res) =>{
            obj.removeAttribute('disabled');
            $(obj).LoadingOverlay("hide", true);

            this.construirDetalleRequerimientoListaRequerimientosPendientes(table_id,row,res);
        }).catch((err)=> {
            console.log(err)
        })
    }

    construirDetalleRequerimientoListaRequerimientosPendientes(table_id,row,response){
        var html = '';
        // console.log(response);
        if (response.length > 0) {
            response.forEach(function (element) {
                // if(element.tiene_transformacion==false){
                let stock_comprometido = 0;
                (element.reserva).forEach(reserva => {
                    if(reserva.estado ==1){
                        stock_comprometido+= parseFloat(reserva.stock_comprometido);
                    }
                });

                    html += `<tr>
                        <td style="border: none; text-align:center;" data-part-number="${element.part_number}" data-producto-part-number="${element.producto_part_number}">${(element.producto_part_number != null ? element.producto_part_number :(element.part_number !=null ?element.part_number:''))} ${element.tiene_transformacion ==true?'<span class="label label-default">Transformado</span>':''}</td>
                        <td style="border: none; text-align:left;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion?element.descripcion:'')}</td>
                        <td style="border: none; text-align:center;">${element.abreviatura != null ? element.abreviatura : ''}</td>
                        <td style="border: none; text-align:center;">${element.cantidad >0 ? element.cantidad : ''}</td>
                        <td style="border: none; text-align:center;">${(element.precio_unitario >0 ? ((element.moneda_simbolo?element.moneda_simbolo:((element.moneda_simbolo?element.moneda_simbolo:'')+'0.00')) + $.number(element.precio_unitario,2)) : (element.moneda_simbolo?element.moneda_simbolo:'')+'0.00')}</td>
                        <td style="border: none; text-align:center;">${(parseFloat(element.subtotal) > 0 ? ((element.moneda_simbolo?element.moneda_simbolo:'') + $.number(element.subtotal,2)) :((element.moneda_simbolo?element.moneda_simbolo:'')+$.number((element.cantidad * element.precio_unitario),2)))}</td>
                        <td style="border: none; text-align:center;">${element.motivo != null ? element.motivo : ''}</td>
                        <td style="border: none; text-align:center;">${stock_comprometido != null ? stock_comprometido : ''}</td>
                        <td style="border: none; text-align:center;">${element.estado_doc != null && element.tiene_transformacion ==false ? element.estado_doc : ''}</td>
                        </tr>`;
                    // }
                });
                var tabla = `<table class="table table-condensed table-bordered" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none; text-align:center;">Part number</th>
                        <th style="border: none; text-align:center;">Descripcion</th>
                        <th style="border: none; text-align:center;">Unidad medida</th>
                        <th style="border: none; text-align:center;">Cantidad</th>
                        <th style="border: none; text-align:center;">Precio unitario</th>
                        <th style="border: none; text-align:center;">Subtotal</th>
                        <th style="border: none; text-align:center;">Motivo</th>
                        <th style="border: none; text-align:center;">Stock comprometido</th>
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

    // chkEmpresa(e) {

    //     if (e.target.checked == true) {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").removeAttribute('readOnly');

    //     } else {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").setAttribute('readOnly', true);

    //     }
    // }

    // chkSede(e) {

    //     if (e.target.checked == true) {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").removeAttribute('readOnly');
    //     } else {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").setAttribute('readOnly', true);

    //     }
    // }


    // handleChangeFilterReqByEmpresa(event) {
    //     let id_empresa = event.target.value;
    //     requerimientoPendienteCtrl.getDataSelectSede(id_empresa).then(function (res) {
    //         requerimientoPendienteView.llenarSelectSede(res);
    //     }).catch(function (err) {
    //         console.log(err)
    //     })

    // }

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

    // aplicarFiltros() {
    //     let idEmpresa = null;
    //     let idSede = null;

    //     let chkEmpresa = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] input[name='chkEmpresa']").checked;
    //     let chkSede = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] input[name='chkSede']").checked;

    //     if (chkEmpresa == true) {
    //         idEmpresa = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").value;

    //     }
    //     if (chkSede == true) {
    //         idSede = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").value;
    //     }

    //     $('#modal-filtro-requerimientos-pendientes').modal('hide');

    //     this.renderRequerimientoPendienteListModule(idEmpresa > 0 ? idEmpresa : null, idSede > 0 ? idSede : null);

    // }






    // atender con almacen
    atenderConAlmacen(obj) {
        $('#modal-atender-con-almacen').modal({
            show: true,
            backdrop: 'true'
        });
        
        trRequerimientosPendientes= obj.closest("tr");

        document.querySelector("form[id='form-reserva-almacen'] input[name='id_requerimiento']").value= obj.dataset.idRequerimiento;
        // let codigoRequerimiento =obj.dataset.codigo;
        document.querySelector("span[id='codigo_requerimiento']").textContent=obj.dataset.codigoRequerimiento;
        this.llenarTablaModalAtenderConAlmacen(obj.dataset.idRequerimiento);

    }

    llenarTablaModalAtenderConAlmacen(idRequerimiento){
        this.requerimientoPendienteCtrl.limpiarTabla('listaItemsRequerimientoParaAtenderConAlmacen');
        this.requerimientoPendienteCtrl.openModalAtenderConAlmacen(idRequerimiento).then((res)=> {
            this.construirTablaListaItemsRequerimientoParaAtenderConAlmacen(res.data);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaListaItemsRequerimientoParaAtenderConAlmacen(data) { 
        console.log(data);
        $('#listaItemsRequerimientoParaAtenderConAlmacen').dataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            "bDestroy": true,
            "bInfo": false,
            // 'paging': true,
            "bLengthChange": false,
            // "pageLength": 3,
            'data': data,
          
            // 'order': [[0, 'desc']],
            // "scrollY": 200,
            // "scrollX": true,

            // 'searching': false,
            // 'scrollCollapse': true,
            // 'processing': true,
            'columns': [
         
                {
                    render: function (data, type, row) {
                        return (row.producto != null?row.producto.codigo:'');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.producto!= null?row.producto.part_number:'');
                    }
                },
                {
                    render: function (data, type, row) {
                        return ((row.producto && row.producto.descripcion!= null && row.producto.descripcion!= '')?row.producto.descripcion:(row.descripcion !=null?row.descripcion:''));
                    }
                },
                {
                    render: function (data, type, row) {
                        return row.unidad_medida !=null?row.unidad_medida.descripcion:'';
                    }
                },
                {
                    render: function (data, type, row) {
                        return row.cantidad != null?row.cantidad:'';
                    }
                },
                {
                    render: function (data, type, row) {
                        return row.proveedor_seleccionado != null?row.proveedor_seleccionado:'';
                    }
                },
                {
                    render: function (data, type, row) {
                        let estado =(row.estado.estado_doc != null?row.estado.estado_doc:'');
                        let productoTransformado =row.tiene_transformacion == true?'<br><span class="label label-default">Producto Transformado</span>':'';
                        return (estado+productoTransformado);
                    }
                },
                {
                    render: function (data, type, row) {
                        let cantidadReservada = 0;
                        if(row.reserva !=null){
                            (row.reserva).forEach(element => {
                                cantidadReservada+=parseFloat(element.stock_comprometido);
                            });
                        }
                        return cantidadReservada; //cantidad reservada
                    }
                },
                {
                    render: function (data, type, row) {
                        let codigoReserva=[];
                        if(row.reserva !=null){
                            (row.reserva).forEach(element => {
                                codigoReserva.push(element.codigo?element.codigo:(element.id_reserva?element.id_reserva:''));
                            });
                        }
                        return codigoReserva.length>0?codigoReserva:'(Sin reserva)'; //codigo o id reservada
                    }
                },
                {
                    render: function (data, type, row) {
                        if(row.id_producto >0){
                            return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                            <button type="button" class="btn btn-xs btn-success btnNuevaReserva handleClickAbrirModalNuevaReserva" 
                                data-codigo-requerimiento="${document.querySelector("span[id='codigo_requerimiento']").textContent}" 
                                data-id-detalle-requerimiento="${row.id_detalle_requerimiento}" 
                                title="Nueva reserva" ><i class="fas fa-box fa-xs"></i></button>
                            <button type="button" class="btn btn-xs btn-info btnHistorialReserva handleClickAbrirModaHistorialReserva" 
                                data-codigo-requerimiento="${document.querySelector("span[id='codigo_requerimiento']").textContent}" 
                                data-id-detalle-requerimiento="${row.id_detalle_requerimiento}" 
                                title="Historial reserva" ><i class="fas fa-eye fa-xs"></i></button>
                            </div></center>`;  

                        }else{
                            return '(Sin mapear)';
                        }
                    }
                },
           
                // {
                //     'render':
                //         function (data, type, row, meta) {
                //             let select = '';
                //             if (row.tiene_transformacion == false) {
                //                 select = `<input type="hidden" name="idDetalleRequerimiento[]" value="${row.id_detalle_requerimiento}"><select class="form-control selectAlmacenReserva" name="almacenReserva[]" >`;
                //                 select += `<option value ="0">Sin selección</option>`;
                //                 data_almacenes.forEach(element => {
                //                     if (row.id_almacen_reserva == element.id_almacen) {
                //                         select += `<option value="${element.id_almacen}" data-id-empresa="${element.id_empresa}" selected>${element.descripcion}</option> `;

                //                     } else {
                //                         select += `<option value="${element.id_almacen}" data-id-empresa="${element.id_empresa}">${element.descripcion}</option> `;
                //                     }
                //                 });
                //                 select += `</select>`;
                //             }


                //             return select;
                //         }
                // },
                // {
                //     'render':
                //         function (data, type, row, meta) {
                //             let action = '';
                //             if (row.tiene_transformacion == false) {
                //                 action = `<input type="number" min="0" name="cantidadReserva[]" class="form-control inputCantidadArReservar handleBlurUpdateInputCantidadAAtender"  data-cantidad="${row.cantidad}" style="width: 70px;" data-indice="${meta.row}" value="${parseInt(row.stock_comprometido ? row.stock_comprometido : 0)}" />`;

                //                 that.updateObjCantidadAAtender(meta.row, row.stock_comprometido);

                //             }
                //             return action;
                //         }
                // }
            ],

            'columnDefs': [
                { 'targets': 0,'className': "text-center" },
                { 'targets': 1,'className': "text-center" },
                { 'targets': 2,'className': "text-left", "width": "280px"},
                { 'targets': 3,'className': "text-center" },
                { 'targets': 4,'className': "text-center" },
                { 'targets': 5,'className': "text-left" },
                { 'targets': 6,'className': "text-center" },
                { 'targets': 7,'className': "text-center" },
                { 'targets': 8,'className': "text-center" },
                { 'targets': 9,'className': "text-center" }
           
            ],
            'initComplete': function () {


            },
            "createdRow": function (row, data, dataIndex) {
                $(row.childNodes[2]).css('width', '280px');  

                // $(row.childNodes[7]).css('background-color', '#586c86');  
                // $(row.childNodes[7]).css('font-weight', 'bold');
                // $(row.childNodes[8]).css('background-color', '#586c86');  
                // $(row.childNodes[8]).css('font-weight', 'bold');

            }
        });
    }

    abrirModalHistorialReserva(obj){
        $('#modal-historial-reserva').modal({
            show: true,
            backdrop: 'true'
        });

        if(parseInt(obj.dataset.idDetalleRequerimiento) >0){
            this.requerimientoPendienteCtrl.obtenerHistorialDetalleRequerimientoParaReserva(obj.dataset.idDetalleRequerimiento).then((res) =>{
                $('#modal-historial-reserva .modal-content').LoadingOverlay("hide", true);                
                if (res.status == 200) {
                    this.llenarModalHistorialReserva(res.data);
                }
            }).catch(function (err) {
                Swal.fire(
                    '',
                    'Hubo un problema al intentar obtener la data del producto',
                    'error'
                );
            })

        }
    }

    llenarModalHistorialReserva(data){
        if(data.id_producto>0){
            document.querySelector("div[id='modal-historial-reserva'] label[id='partNumber']").textContent= data.producto.part_number !=null?data.producto.part_number:(data.part_number!=null?data.part_number:'');
            document.querySelector("div[id='modal-historial-reserva'] label[id='descripcion']").textContent= data.producto.descripcion !=null?data.producto.descripcion:(data.descripcion!=null?data.descripcion:'');
            document.querySelector("div[id='modal-historial-reserva'] label[id='cantidad']").textContent= data.cantidad;
            document.querySelector("div[id='modal-historial-reserva'] label[id='unidadMedida']").textContent= data.unidad_medida.descripcion;
            this.listarTablaHistorialReservaProducto(data.reserva);
        }else{
            $('#modal-historial-reserva').modal('hide');
            Swal.fire(
                '',
                'Lo sentimos no se encontro que el producto seleccionado este mapeado, debe mapear el producto antes de realizar una reseva',
                'warning'
            );
            
        }
    }

    listarTablaHistorialReservaProducto(data){
        this.requerimientoPendienteCtrl.limpiarTabla('listaHistorialReserva');
        // let cantidadTotalStockComprometido=0;
        if(data.length >0){
            (data).forEach(element => {
                // cantidadTotalStockComprometido+= element.stock_comprometido;
                document.querySelector("tbody[id='bodyListaHistorialReservaProducto']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td>${(element.codigo !=null && element.codigo !='')?element.codigo:(element.id_reserva)}</td>
                <td>${element.almacen.descripcion}</td>
                <td>${element.stock_comprometido}</td>
                <td>${element.usuario.trabajador.postulante.persona.nombres.concat(' ', element.usuario.trabajador.postulante.persona.apellido_paterno??'')}</td>
                <td>${element.estado.estado_doc}</td>
                </tr>`);
            });
            // document.querySelector("table[id='listaHistorialReserva'] label[name='totalReservado']").textContent=cantidadTotalStockComprometido;
        }else{
            document.querySelector("tbody[id='bodyListaHistorialReservaProducto']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td colspan="5" style="text-align:center;">(Sin reservas)</td>
    
            </tr>`);
        }
    }

    abrirModalNuevaReserva(obj){

        this.limpiarModalNuevaReserva();
        $('#modal-nueva-reserva').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("div[id='modal-nueva-reserva'] span[id='codigoRequerimiento']").textContent=obj.dataset.codigoRequerimiento;
        // console.log(obj);
        if(parseInt(obj.dataset.idDetalleRequerimiento) >0){
            this.requerimientoPendienteCtrl.obtenerDetalleRequerimientoParaReserva(obj.dataset.idDetalleRequerimiento).then((res) =>{
                $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);                
                if (res.status == 200) {
                    this.llenarModalNuevaReserva(res.data)
                }
            }).catch(function (err) {
                Swal.fire(
                    '',
                    'Hubo un problema al  intentarobtener la data del producto',
                    'error'
                );
            })

        }
    }

    llenarModalNuevaReserva(data){
        if(data.id_producto>0){
            document.querySelector("form[id='form-nueva-reserva'] input[name='idProducto']").value= data.id_producto;
            document.querySelector("form[id='form-nueva-reserva'] input[name='idRequerimiento']").value= data.id_requerimiento;
            document.querySelector("form[id='form-nueva-reserva'] input[name='idDetalleRequerimiento']").value= data.id_detalle_requerimiento;
            document.querySelector("form[id='form-nueva-reserva'] label[id='partNumber']").textContent= data.producto.part_number !=null?data.producto.part_number:(data.part_number!=null?data.part_number:'');
            document.querySelector("form[id='form-nueva-reserva'] label[id='descripcion']").textContent= data.producto.descripcion !=null?data.producto.descripcion:(data.descripcion!=null?data.descripcion:'');
            document.querySelector("form[id='form-nueva-reserva'] label[id='cantidad']").textContent= data.cantidad;
            document.querySelector("form[id='form-nueva-reserva'] label[id='unidadMedida']").textContent= data.unidad_medida.descripcion;
            this.listarTablaListaConReserva(data.reserva);
        }else{
            $('#modal-nueva-reserva').modal('hide');
            Swal.fire(
                '',
                'Lo sentimos no se encontro que el producto seleccionado este mapeado, debe mapear el producto antes de realizar una reseva',
                'warning'
            );
            
        }

    }

    listarTablaListaConReserva(data){
        this.requerimientoPendienteCtrl.limpiarTabla('listaConReserva');
        let cantidadTotalStockComprometido=0;
        if(data.length >0){
            (data).forEach(element => {
                cantidadTotalStockComprometido+= parseFloat(element.stock_comprometido);
                document.querySelector("tbody[id='bodyListaConReserva']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td>${(element.codigo !=null && element.codigo !='')?element.codigo:(element.id_reserva)}</td>
                <td>${element.almacen.descripcion}</td>
                <td>${element.stock_comprometido}</td>
                <td>${element.usuario.trabajador.postulante.persona.nombres.concat(' ', element.usuario.trabajador.postulante.persona.apellido_paterno??'')}</td>
                <td>${element.estado.estado_doc}</td>
                <td><button type="button" class="btn btn-xs btn-danger btnAnularReserva handleClickAnularReserva" data-codigo-reserva="${element.codigo}" data-id-reserva="${element.id_reserva}"  data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" title="Anular"><i class="fas fa-times fa-xs"></i></button></td>
                </tr>`);
            });
            document.querySelector("table[id='listaConReserva'] label[name='totalReservado']").textContent=cantidadTotalStockComprometido;
        }else{
            document.querySelector("tbody[id='bodyListaConReserva']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td colspan="5" style="text-align:center;">(Sin reservas)</td>
    
            </tr>`);
            document.querySelector("table[id='listaConReserva'] label[name='totalReservado']").textContent=0;

        }
    }

    limpiarModalNuevaReserva(){
        document.querySelector("form[id='form-nueva-reserva'] input[name='idProducto']").value= '';
        document.querySelector("form[id='form-nueva-reserva'] input[name='idDetalleRequerimiento']").value= '';
        document.querySelector("form[id='form-nueva-reserva'] label[id='partNumber']").textContent='';
        document.querySelector("form[id='form-nueva-reserva'] label[id='descripcion']").textContent='';
        document.querySelector("form[id='form-nueva-reserva'] label[id='cantidad']").textContent='';
        document.querySelector("form[id='form-nueva-reserva'] label[id='unidadMedida']").textContent='';
        document.querySelector("form[id='form-nueva-reserva'] input[name='cantidadReserva']").value= '';
        document.querySelector("form[id='form-nueva-reserva'] select[name='almacenReserva']").value= 0;
        this.requerimientoPendienteCtrl.limpiarTabla('listaConReserva');
        // document.querySelector("form[id='form-nueva-reserva'] label[id='totalCantidadAtendidoConOrden']").textContent='';
        // document.querySelector("form[id='form-nueva-reserva'] label[id='totalCantidadConReserva']").textContent='';
        // document.querySelector("form[id='form-nueva-reserva'] label[id='total']").textContent='';
    }

    validarModalNuevaReserva(){
        let mensaje='';
        let idProducto = document.querySelector("form[id='form-nueva-reserva'] input[name='idProducto']").value;
        let idDetalleRequerimiento = document.querySelector("form[id='form-nueva-reserva'] input[name='idDetalleRequerimiento']").value;
        let cantidadReserva = document.querySelector("form[id='form-nueva-reserva'] input[name='cantidadReserva']").value;
        let almacenReserva = document.querySelector("form[id='form-nueva-reserva'] select[name='almacenReserva']").value;
        if(!idProducto,!idDetalleRequerimiento >0 ){
            mensaje+='<li style="text-align: left;">El producto / item de requerimiento no tiene un ID valido.</li>';
        }
        if(!parseFloat(cantidadReserva)>0 || parseFloat(cantidadReserva)<0){
            mensaje+='<li style="text-align: left;">Debe ingresar una cantidad a reservar mayor a cero.</li>';
        }
        if((parseFloat(cantidadReserva)+ parseFloat(document.querySelector("form[id='form-nueva-reserva'] label[name='totalReservado']").textContent)) > parseFloat(document.querySelector("form[id='form-nueva-reserva'] label[id='cantidad']").textContent)){
            mensaje+='<li style="text-align: left;">La cantidad a reservar con la cantidad total reservada supera la cantidad solicitada, debe Ingresar un valor menor.</li>';
        }
        if(!parseFloat(almacenReserva)>0){
            mensaje+='<li style="text-align: left;">Debe seleccionar un almacén.</li>';
        }
        return mensaje;
    }

    anularReserva(obj){
        Swal.fire({
            title: 'Esta seguro que desea anular la reserva '+(obj.dataset.codigoReserva!=''?obj.dataset.codigoReserva:obj.dataset.idReserva)+'?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, anular'

        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append(`idReserva`, obj.dataset.idReserva);
                formData.append(`idDetalleRequerimiento`, obj.dataset.idDetalleRequerimiento);

                $.ajax({
                    type: 'POST',
                    url: 'anular-reserva-almacen',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
    
                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) =>{
                        // console.log(response);
                        if (response.status == 200) {
                            $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
    
                            Lobibox.notify('success', {
                                title:false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Reserva anulada`
                            });
    
                            this.listarTablaListaConReserva(response.data)
                            this.llenarTablaModalAtenderConAlmacen(document.querySelector("form[id='form-nueva-reserva'] input[name='idRequerimiento']").value);
    
                            
                        } else {
                            $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                            console.log(response);
                            Swal.fire(
                                '',
                                'Lo sentimos hubo un problema al intentar anular la reserva, por favor vuelva a intentarlo',
                                'error'
                            );
                        }
                    },
                    fail:  (jqXHR, textStatus, errorThrown) =>{
                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un problema en el servidor al intentar anular la reserva, por favor vuelva a intentarlo',
                            'error'
                        );    
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
        })

    }

    agregarReserva(obj){
        let mensajeValidacion = this.validarModalNuevaReserva();
        if((mensajeValidacion.length >0)){
            Swal.fire({
                title:'',
                html:'<ol>'+mensajeValidacion+'</ol>',
                icon:'warning'}
            );
            obj.removeAttribute("disabled");

        }else{
            let formData = new FormData($('#form-nueva-reserva')[0]);
            $.ajax({
                type: 'POST',
                url: 'guardar-reserva-almacen',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend:  (data)=> { // Are not working with dataType:'jsonp'

                    $('#modal-nueva-reserva .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) =>{
                    if (response.id_reserva > 0) {
                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);

                        Lobibox.notify('success', {
                            title:false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `${response.mensaje}`
                        });
                        obj.removeAttribute("disabled");

                        this.listarTablaListaConReserva(response.data)
                        this.llenarTablaModalAtenderConAlmacen(document.querySelector("form[id='form-nueva-reserva'] input[name='idRequerimiento']").value);

                        
                    } else {
                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                        console.log(response);
                        if(response.mensaje.length >0){
                            Swal.fire(
                                '',
                                response.mensaje,
                                'warning'
                            );
                        }else{
                            Swal.fire(
                                '',
                                'Lo sentimos hubo un problema en el servidor al intentar guardar la reserva, por favor vuelva a intentarlo',
                                'error'
                            );
                        }
                        obj.removeAttribute("disabled");

                    }
                },
                fail:  (jqXHR, textStatus, errorThrown) =>{
                    $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar guardar la reserva, por favor vuelva a intentarlo',
                        'error'
                    );
                    obj.removeAttribute("disabled");

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }

 
    updateObjCantidadAAtender(indice, valor) {
        itemsParaAtenderConAlmacenList.forEach((element, index) => {
            if (index == indice) {
                itemsParaAtenderConAlmacenList[index].cantidad_a_atender = valor;
            }
        });
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

    // agregarItemsBaseParaCompraFinalizado(response) {

    //     if (response.status == 200) {
    //         alert(response.mensaje);
    //         $('#modal-agregar-items-para-compra').modal('hide');
    //         requerimientoPendienteView.renderRequerimientoPendienteList(null, null);
    //     } else {
    //         alert(response.mensaje);
    //     }

    // }

    // totalItemsAgregadosParaCompraCompletada() {

    //     alert('Ya fueron agregados todos los items disponibles del Cuadro de Costos al Requerimiento');
    //     document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('disabled', true);
    //     let btnEliminarItem = document.querySelectorAll("div[id='modal-agregar-items-para-compra'] button[name='btnEliminarItem']");
    //     for (var i = 0; i < btnEliminarItem.length; i++) {
    //         btnEliminarItem[i].setAttribute('disabled', true);
    //     }

    // }
    // totalItemsAgregadosParaCompraPendiente() {

    //     document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").removeAttribute('disabled');
    //     let btnEliminarItem = document.querySelectorAll("div[id='modal-agregar-items-para-compra'] button[name='btnEliminarItem']");
    //     for (var i = 0; i < btnEliminarItem.length; i++) {
    //         btnEliminarItem[i].removeAttribute('disabled');
    //     }

    // }


    // ver detalle cuadro de costos
    openModalCuadroCostos(obj) {
        $('#modal-ver-cuadro-costos').modal({
            show: true,
            backdrop: 'true'
        });
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
                        return `${row['pvu_oc']>0? 'S/'+row['pvu_oc']:''}`;
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

                        return `${simboloMoneda}${row['costo_unitario_proveedor'] ? $.number(row['costo_unitario_proveedor'],2) : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['plazo_proveedor'] ? row['plazo_proveedor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `S/${row['flete_proveedor'] ? $.number(row['flete_proveedor'],2) : ''}`;
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

                    //    let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                       let costoUnitario = $.number((row.cantidad*row.costo_unitario_proveedor),2);
                        return `${simboloMoneda}${costoUnitario}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        // let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        let costoUnitario = row.cantidad*row.costo_unitario_proveedor;
                        let tipoCambio = row.tipo_cambio;
                        let costoUnitarioSoles = costoUnitario * tipoCambio;
                        return `S/${$.number(costoUnitarioSoles,2)}`;
                    }
                },
                {
                    'render': function (data, type, row) {

                        // let totalFleteProveedor= (Math.round((row.cantidad*row.flete_proveedor) * 100) / 100).toFixed(2);
                        let totalFleteProveedor= $.number((row.cantidad*row.flete_proveedor),2);
                        return `S/${(totalFleteProveedor)}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        // let simboloMoneda=( row.moneda_costo_unitario_proveedor == 's')?'S/':(row.moneda_costo_unitario_proveedor=='d')?'$':row.moneda_costo_unitario_proveedor;

                        // let totalFleteProveedor= (Math.round((row.cantidad*row.flete_proveedor) * 100) / 100).toFixed(2);
                        let totalFleteProveedor=  row.cantidad*row.flete_proveedor;
                        // let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        let costoUnitario = row.cantidad*row.costo_unitario_proveedor;
                        let tipoCambio = row.tipo_cambio;
                        let costoUnitarioSoles = costoUnitario * tipoCambio;
                        let costoCompraMasFlete = costoUnitarioSoles + totalFleteProveedor;
                        return `S/${$.number(costoCompraMasFlete,2)}`;
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
            ],
            'columnDefs': [
                { 'aTargets': [0],'className': "text-center" },
                { 'aTargets': [1],'className': "text-left" },
                { 'aTargets': [2],'className': "text-right" },
                { 'aTargets': [3],'className': "text-right" },
                { 'aTargets': [4],'className': "text-center" },
                { 'aTargets': [5],'className': "text-center" },
                { 'aTargets': [6],'className': "text-center" },
                { 'aTargets': [7],'className': "text-center" },
                { 'aTargets': [8],'className': "text-left" },
                { 'aTargets': [9],'className': "text-right" },
                { 'aTargets': [10],'className': "text-center" },
                { 'aTargets': [11],'className': "text-right" },
                { 'aTargets': [12],'className': "text-right" },
                { 'aTargets': [13],'className': "text-right" },
                { 'aTargets': [14],'className': "text-right" },
                { 'aTargets': [15],'className': "text-right" },
                { 'aTargets': [16],'className': "text-center" },
                { 'aTargets': [17],'className': "text-center" }
            ],
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

