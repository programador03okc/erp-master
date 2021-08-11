
var tempObjectBtnPartida;
var tempObjectBtnCentroCostos;
var tempObjectBtnInputFile;
var tempIdRegisterActive;
var tempCentroCostoSelected;
var tempArchivoAdjuntoItemList = [];
var tempArchivoAdjuntoItemToDeleteList = [];
var tempArchivoAdjuntoRequerimientoList = [];
var tempArchivoAdjuntoRequerimientoToDeleteList = [];
class RequerimientoView {
    constructor(requerimientoCtrl){
        this.requerimientoCtrl = requerimientoCtrl;
    }
    init() {
        this.agregarFilaEvent();
        this.initializeEventHandler();
        // $('[name=periodo]').val(today.getFullYear());
        this.getTipoCambioCompra();
        var idRequerimiento = localStorage.getItem("idRequerimiento");
        if (idRequerimiento !== null){
            this.cargarRequerimiento(idRequerimiento)
            localStorage.removeItem("idRequerimiento");
            vista_extendida();
        }

    }

    initializeEventHandler(){
        document.querySelector("button[class~='handleClickImprimirRequerimientoPdf']").addEventListener("click", this.imprimirRequerimientoPdf.bind(this), false);
        document.querySelector("button[class~='handleClickAdjuntarArchivoRequerimiento']").addEventListener("click", this.adjuntarArchivoRequerimiento.bind(this), false);

        document.querySelector("input[class~='handleChangeUpdateConcepto']").addEventListener("keyup", this.updateConcepto.bind(this), false);
        document.querySelector("select[class~='handleChangeUpdateMoneda']").addEventListener("change", this.changeMonedaSelect.bind(this), false);
        document.querySelector("select[class~='handleChangeOptEmpresa']").addEventListener("change", this.changeOptEmpresaSelect.bind(this), false);
        document.querySelector("select[class~='handleChangeUpdateEmpresa']").addEventListener("change", this.updateEmpresa.bind(this), false);
        document.querySelector("select[class~='handleChangeOptUbigeo']").addEventListener("change", this.changeOptUbigeo.bind(this), false);
        document.querySelector("select[class~='handleChangeUpdateSede']").addEventListener("change", this.updateSede.bind(this), false);
        document.querySelector("input[class~='handleChangeFechaLimite']").addEventListener("change", this.updateFechaLimite.bind(this), false);

        $('#modal-adjuntar-archivos-requerimiento').one("change","input.handleChangeAgregarAdjuntoRequerimiento", (e)=>{
            this.agregarAdjuntoRequerimiento(e.target);
        });

        $('#ListaDetalleRequerimiento tbody').on("click","button.handleClickCargarModalPartidas", (e)=>{
            this.cargarModalPartidas(e);
        });

        $('#modal-partidas').on("click","button.handleClickSelectPartida", (e)=>{
            this.selectPartida(e.target.dataset.idPartida);
        });

        $('#modal-partidas').on("click","h5.handleClickapertura", (e)=>{
            this.apertura(e.target.dataset.idPresup);
            this.changeBtnIcon(e);
        });

        $('#modal-centro-costos').on("click","h5.handleClickapertura", (e)=>{
            this.apertura(e.target.dataset.idPresup);
            this.changeBtnIcon(e);
        });

        $('#modal-centro-costos').on("click","button.handleClickSelectCentroCosto", (e)=>{
            this.selectCentroCosto(e.target.dataset.idCentroCosto,e.target.dataset.codigo,e.target.dataset.descripcionCentroCosto);
        });
            
        $('#ListaDetalleRequerimiento tbody').on("click","button.handleClickCargarModalCentroCostos", (e)=>{
            this.cargarModalCentroCostos(e);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur","textarea.handleBlurUpdateDescripcionItem", (e)=>{
            this.updateDescripcionItem(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur","input.handleBurUpdateSubtotal", (e)=>{
            this.updateSubtotal(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur","input.handleBlurUpdateCantidadItem", (e)=>{
            this.updateCantidadItem(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur","input.handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida", ()=>{
            this.calcularPresupuestoUtilizadoYSaldoPorPartida();
        });
        $('#ListaDetalleRequerimiento tbody').on("blur","input.handleBlurUpdatePrecioItem", (e)=>{
            this.updatePrecioItem(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("click","button.handleClickAdjuntarArchivoItem", (e)=>{
            this.adjuntarArchivoItem(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("click","button.handleClickEliminarItem", (e)=>{
            this.eliminarItem(e);
        });
    }

    editRequerimiento(){
        if(parseInt(document.querySelector("input[name='id_requerimiento']").value) > 0){
            $("#form-requerimiento .activation").attr('disabled', false);
        }
    }

    mostrarHistorial() {
        changeStateButton('inicio');


        $('#modal-historial-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        this.requerimientoCtrl.getListadoElaborados("ME", null, null, null, null, null).then((res)=> {
            this.construirTablaHistorialRequerimientosElaborados(res['data']);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaHistorialRequerimientosElaborados(data) {
        // console.log(data);
        let that = this;

        var vardataTables = funcDatatables();
        $('#listaRequerimiento').DataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            'order': [[10, 'desc']],
            'bLengthChange': false,
            'serverSide': false,
            'destroy': true,
            'data': data,
            'columns': [
                { 'data': 'priori', 'name': 'adm_prioridad.descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega', 'className': 'text-center' },
                { 'data': 'tipo_requerimiento', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
                { 'data': 'grupo', 'name': 'adm_grupo.descripcion' },
                { 'data': 'division', 'name': 'adm_flujo.nombre' },
                {'render':
                    function (data, type, row){
                        switch (row['estado']) {
                            case 1:
                                return '<span class="label label-default">' + row['estado_doc'] + '</span>';
                                break;
                            case 2:
                                return '<span class="label label-success">' + row['estado_doc'] + '</span>';
                                break;
                            case 3:
                                return '<span class="label label-warning">' + row['estado_doc'] + '</span>';
                                break;
                            case 5:
                                return '<span class="label label-primary">' + row['estado_doc'] + '</span>';
                                break;
                            case 7:
                                return '<span class="label label-danger">' + row['estado_doc'] + '</span>';
                                break;
                            default:
                                return '<span class="label label-default">' + row['estado_doc'] + '</span>';
                                break;

                        }
                    }
                },
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro' }
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        return row['termometro'];

                        // if (row['priori'] == 'Normal') {
                        //     return '<center> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal" ></i></center>';
                        // } else if (row['priori'] == 'Media') {
                        //     return '<center> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"  ></i></center>';
                        // } else if (row['priori']=='Alta') {
                        //     return '<center> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítico"  ></i></center>';
                        // } else {
                        //     return '';
                        // }
                    }, targets: 0
                },
                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnSeleccionar = '<button type="button" class="btn btn-xs btn-success handleClickCargarRequerimiento" title="Seleccionar" >Seleccionar</button>';
                        return containerOpenBrackets + btnSeleccionar + containerCloseBrackets;
                    }, targets: 10
                },
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.estado == 2) {
                    $(row.childNodes[8]).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row.childNodes[8]).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row.childNodes[8]).css('color', '#d92b60');
                }
            },
            'initComplete': function () {
                $('#listaRequerimiento tbody').on("click","button.handleClickCargarRequerimiento", function(){
                    var data = $('#listaRequerimiento').DataTable().row($(this).parents("tr")).data();
                    that.cargarRequerimiento(data.id_requerimiento);
                });
            }
        });

        $('#ListaReq').DataTable().on("draw", function () {
            resizeSide();
        });
    }

    cargarRequerimiento(idRequerimiento) {
        $('#modal-historial-requerimiento').modal('hide');
        const objecto= this;
        this.requerimientoCtrl.getRequerimiento(idRequerimiento).then((res)=> {
            objecto.mostrarRequerimiento(res);

        }).catch(function (err) {
            console.log(err)
        });
    }


    mostrarRequerimiento(data) {
        if (data.hasOwnProperty('requerimiento')) {
            document.querySelector("input[name='nombre_archivo']").removeAttribute("disabled");
            this.RestablecerFormularioRequerimiento();
            var btnImprimirRequerimiento = document.getElementsByName("btn-imprimir-requerimento-pdf");
            var btnAdjuntosRequerimiento = document.getElementsByName("btn-adjuntos-requerimiento");
            let allButtonAdjuntarNuevo = document.querySelectorAll("input[name='nombre_archivo']");

            disabledControl(btnImprimirRequerimiento, false);
            disabledControl(btnAdjuntosRequerimiento, false);

            var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
            disabledControl(btnTrazabilidadRequerimiento, false);

            // construir select con todas la divisiones
            let optionSelectDivisionHTML='';
            this.requerimientoCtrl.getDivisiones().then((res)=> {
                res.forEach(element => {
                        optionSelectDivisionHTML += `<option value="${element.id_division}" selected>${element.descripcion}</option> `;
                });
                document.querySelector("select[name='division']").innerHTML=optionSelectDivisionHTML;
            }).catch(function (err) {
                console.log(err)
                Swal.fire(
                    '',
                    'Hubo un error al intentar cargar todo las divisiones',
                    'error'
                );
            })
            // 

            this.mostrarCabeceraRequerimiento(data['requerimiento'][0]);
            if (data.hasOwnProperty('det_req')) {
                if(data['requerimiento'][0].estado == 7 || data['requerimiento'][0].estado == 2){
                    changeStateButton('cancelar'); //init.js
                    $("#form-requerimiento .activation").attr('disabled', true);

                }else if(data['requerimiento'][0].estado ==1  && data['requerimiento'][0].id_usuario == auth_user.id_usuario){
                    
                    document.querySelector("form[id='form-requerimiento']").setAttribute('type','edition');
                    changeStateButton('historial'); //init.js

                    allButtonAdjuntarNuevo.forEach(element => {
                        element.removeAttribute("disabled");
                    });


                    $("#form-requerimiento .activation").attr('disabled', true);

                }else if((data['requerimiento'][0].estado ==1 || data['requerimiento'][0].estado ==3)  && data['requerimiento'][0].id_usuario == auth_user.id_usuario){
                    document.querySelector("div[id='group-historial-revisiones']").removeAttribute('hidden');
                    this.mostrarHistorialRevisionAprobacion(data['historial_aprobacion']);
                    document.querySelector("form[id='form-requerimiento']").setAttribute('type','edition');
                    changeStateButton('editar'); //init.js
                    disabledControl(btnAdjuntosRequerimiento, false);


                    allButtonAdjuntarNuevo.forEach(element => {
                        element.removeAttribute("disabled");
                    });

                    $("#form-requerimiento .activation").attr('disabled', false);



                }else{
                    document.querySelector("div[id='group-historial-revisiones']").setAttribute('hidden',true);
                    allButtonAdjuntarNuevo.forEach(element => {
                        element.setAttribute("disabled",true);
                    });


                }
                this.mostrarDetalleRequerimiento(data['det_req'],data['requerimiento'][0]['estado']);
            }

        } else {
            Swal.fire(
                '',
                "El requerimiento que intenta cargar no existe",
                'waning'
            );
        }
    }

    
    mostrarCabeceraRequerimiento(data) {

        
        // console.log(auth_user);
        // document.querySelector("input[name='id_usuario_session']").value =data.
        document.querySelector("input[name='id_usuario_req']").value = data.id_usuario;
        document.querySelector("input[name='id_requerimiento']").value = data.id_requerimiento;
        document.querySelector("span[id='codigo_requerimiento']").textContent = data.codigo;
        document.querySelector("input[name='id_grupo']").value = data.id_grupo;
        document.querySelector("input[name='estado']").value = data.estado;
        document.querySelector("span[id='estado_doc']").textContent = data.estado_doc;
        document.querySelector("input[name='fecha_requerimiento']").value = data.fecha_requerimiento;
        document.querySelector("input[name='concepto']").value = data.concepto;
        document.querySelector("select[name='moneda']").value = data.id_moneda;
        document.querySelector("select[name='periodo']").value = data.id_periodo;
        document.querySelector("select[name='prioridad']").value = data.id_prioridad;
        document.querySelector("select[name='rol_usuario']").value = data.id_rol;
        document.querySelector("select[name='empresa']").value = data.id_empresa;
        this.getDataSelectSede(data.id_empresa);
        document.querySelector("select[name='sede']").value = data.id_sede;
        document.querySelector("input[name='fecha_entrega']").value = moment(data.fecha_entrega, "DD-MM-YYYY").format("YYYY-MM-DD");
        document.querySelector("select[name='division']").value = data.division_id;
        document.querySelector("select[name='tipo_requerimiento']").value = data.id_tipo_requerimiento;
        document.querySelector("input[name='id_trabajador']").value = data.trabajador_id;
        document.querySelector("input[name='nombre_trabajador']").value = data.nombre_trabajador;
        document.querySelector("select[name='fuente_id']").value = data.fuente_id;
        document.querySelector("select[name='fuente_det_id']").value = data.fuente_det_id;
        // document.querySelector("input[name='montoMoneda']").textContent =data.
        document.querySelector("input[name='monto']").value = data.monto;
        document.querySelector("select[name='id_almacen']").value = data.id_almacen;
        // document.querySelector("input[name='descripcion_grupo']").value =data.
        document.querySelector("input[name='codigo_proyecto']").value = data.codigo_proyecto;
        document.querySelector("select[name='id_proyecto']").value = data.id_proyecto;
        document.querySelector("select[name='tipo_cliente']").value = data.tipo_cliente;
        document.querySelector("input[name='id_cliente']").value = data.id_cliente;
        document.querySelector("input[name='cliente_ruc']").value = data.cliente_ruc;
        document.querySelector("input[name='cliente_razon_social']").value = data.cliente_razon_social;
        document.querySelector("input[name='id_persona']").value = data.id_persona;
        document.querySelector("input[name='dni_persona']").value = data.dni_persona;
        document.querySelector("input[name='nombre_persona']").value = data.nombre_persona;
        document.querySelector("input[name='ubigeo']").value = data.id_ubigeo_entrega;
        document.querySelector("input[name='name_ubigeo']").value = data.name_ubigeo;
        document.querySelector("input[name='telefono_cliente']").value = data.telefono;
        document.querySelector("input[name='email_cliente']").value = data.email;
        document.querySelector("input[name='direccion_entrega']").value = data.direccion_entrega;
        // document.querySelector("input[name='nombre_contacto']").value =data.
        // document.querySelector("input[name='cargo_contacto']").value =data.
        // document.querySelector("input[name='email_contacto']").value =data.
        // document.querySelector("input[name='telefono_contacto']").value =data.
        // document.querySelector("input[name='direccion_contacto']").value =data.
        document.querySelector("textarea[name='observacion']").value = data.observacion;
        tempArchivoAdjuntoRequerimientoList=[];
        if ((data.adjuntos).length > 0) {
            (data.adjuntos).forEach(element => {
                tempArchivoAdjuntoRequerimientoList.push({
                    id: element.id_adjunto,
                    category: element.categoria_adjunto_id,
                    nameFile: element.archivo,
                    typeFile: null,
                    sizeFile: null,
                    file: []
                });

            });
            ArchivoAdjunto.updateContadorTotalAdjuntosRequerimiento();

        }
        let simboloMonedaPresupuestoUtilizado =document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo;
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if(allSelectorSimboloMoneda.length >0){
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent=simboloMonedaPresupuestoUtilizado;
            });
        }

    }


    mostrarHistorialRevisionAprobacion(data){
        this.limpiarTabla('listaHistorialRevision');

        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                html += `<tr>
                    <td style="text-align:center;">${data[i].nombre_usuario ? data[i].nombre_usuario : ''}</td>
                    <td style="text-align:center;">${data[i].accion ? data[i].accion : ''}${data[i].tiene_sustento ==true ? ' (Tiene sustento)': ''}</td>
                    <td style="text-align:left;">${data[i].detalle_observacion ? data[i].detalle_observacion : ''}</td>
                    <td style="text-align:center;">${data[i].fecha_vobo ? data[i].fecha_vobo : ''}</td>
                </tr>`;
            }
        }
        document.querySelector("tbody[id='body_historial_revision']").insertAdjacentHTML('beforeend', html)
    }


    mostrarDetalleRequerimiento(data,estado) {
        let hasDisabledInput= 'disabled';
        if(estado == 3){
            hasDisabledInput= '';
        }

        this.limpiarTabla('ListaDetalleRequerimiento');
        vista_extendida();

        for (let i = 0; i < data.length; i++) {

                if (data[i].id_tipo_item == 1) { // producto
                document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center; background-color:${data[i].estado ==7?'#f5e4e4':''}; ">
                <td></td>
                <td><p class="descripcion-partida" data-id-partida="${data[i].id_partida}" data-presupuesto-total="${data[i].presupuesto_total_partida}" title="${data[i].codigo_partida != null ? data[i].codigo_partida : ''}" >${data[i].descripcion_partida != null ? data[i].descripcion_partida : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-info activation handleClickCargarModalPartidas" name="partida" ${hasDisabledInput}>Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="partida" name="idPartida[]" value="${data[i].id_partida}" hidden>
                    </div>
                </td>
                <td><p class="descripcion-centro-costo" title="${data[i].codigo_centro_costo != null ? data[i].codigo_centro_costo : ''}">${data[i].descripcion_centro_costo != null ? data[i].descripcion_centro_costo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary activation handleClickCargarModalCentroCostos" name="centroCostos"  ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" ${hasDisabledInput} >Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="centroCosto" name="idCentroCosto[]" value="${data[i].id_centro_costo}" hidden>
                    </div>
                </td>
                <td><input class="form-control activation input-sm" type="text" name="partNumber[]" placeholder="Part number" value="${data[i].part_number != null ? data[i].part_number : ''}" ${hasDisabledInput}></td>
                <td>
                    <div class="form-group">
                        <textarea class="form-control activation input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción" value="${data[i].descripcion != null ? data[i].descripcion : ''}"   ${hasDisabledInput} >${data[i].descripcion != null ? data[i].descripcion : ''}</textarea></td>
                    </div>
                <td><select name="unidad[]" class="form-control activation input-sm" value="${data[i].id_unidad_medida}" ${hasDisabledInput} >${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                <td>
                    <div class="form-group">
                        <input class="form-control activation input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]"  value="${data[i].cantidad}"   placeholder="Cantidad" ${hasDisabledInput}>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control activation input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdatePrecioItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]" value="${data[i].precio_unitario}" placeholder="Precio U." ${hasDisabledInput}>
                    </div>
                </td>  
                <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                <td><textarea class="form-control activation input-sm" name="motivo[]"  value="${data[i].motivo != null ? data[i].motivo : ''}" placeholder="Motivo de requerimiento de item (opcional)" ${hasDisabledInput} >${data[i].motivo != null ? data[i].motivo : ''}</textarea></td>
                <td>
                    <div class="btn-group" role="group">
                        <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                        <input type="hidden" class="idRegister" name="idRegister[]" value="${data[i].id_detalle_requerimiento}">
                        <button type="button" class="btn btn-warning btn-xs handleClickAdjuntarArchivoItem" name="btnAdjuntarArchivoItem[]" title="Adjuntos" >
                            <i class="fas fa-paperclip"></i>
                            <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                        </button> 
                        <button type="button" class="btn btn-danger btn-xs activation handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar" ${hasDisabledInput}><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
                </tr>`);
                } else { // servicio
                    document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center;  background-color:${data[i].estado ==7?'#f5e4e4':''};">
                    <td></td>
                    <td><p class="descripcion-partida" data-id-partida="${data[i].id_partida}" data-presupuesto-total="${data[i].presupuesto_total_partida}" title="${data[i].codigo_partida != null ? data[i].codigo_partida : ''}" >${data[i].descripcion_partida != null ? data[i].descripcion_partida : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-info activation handleClickCargarModalPartidas" name="partida" ${hasDisabledInput}>Seleccionar</button> 
                        <div class="form-group">
                            <input type="text" class="partida" name="idPartida[]" value="${data[i].id_partida}" hidden>
                        </div>
                    </td>
                    <td><p class="descripcion-centro-costo" title="${data[i].codigo_centro_costo != null ? data[i].codigo_centro_costo : ''}">${data[i].descripcion_centro_costo != null ? data[i].descripcion_centro_costo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary activation handleClickCargarModalCentroCostos" name="centroCostos" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" ${hasDisabledInput} >Seleccionar</button> 
                        <div class="form-group">
                            <input type="text" class="centroCosto" name="idCentroCosto[]" value="${data[i].id_centro_costo}" hidden>
                        </div>
                    </td>
                    <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
                    <td>
                        <div class="form-group">
                        <textarea class="form-control activation input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción" value="${data[i].descripcion != null ? data[i].descripcion : ''}" ${hasDisabledInput} >${data[i].descripcion != null ? data[i].descripcion : ''}</textarea></td>
                        </div>
                    <td><select name="unidad[]" class="form-control activation input-sm" value="${data[i].id_unidad_medida}"  ${hasDisabledInput}>${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                    <td>
                        <div class="form-group">
                            <input class="form-control activation input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]"  value="${data[i].cantidad}"  placeholder="Cantidad" ${hasDisabledInput}>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input class="form-control activation input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]" value="${data[i].precio_unitario}"  placeholder="Precio U." ${hasDisabledInput}>
                        </div>  
                    </td>
                    <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                    <td><textarea class="form-control activation input-sm" name="motivo[]"  value="${data[i].motivo != null ? data[i].motivo : ''}" placeholder="Motivo de requerimiento de item (opcional)" ${hasDisabledInput} >${data[i].motivo != null ? data[i].motivo : ''}</textarea></td>
                    <td>
                        <div class="btn-group" role="group">
                            <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                            <input type="hidden" class="idRegister" name="idRegister[]" value="${data[i].id_detalle_requerimiento}">
                            <button type="button" class="btn btn-warning btn-xs handleClickAdjuntarArchivoItem" name="btnAdjuntarArchivoItem[]" title="Adjuntos" >
                                <i class="fas fa-paperclip"></i>
                                <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                            </button> 
                            <button type="button" class="btn btn-danger btn-xs activation handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar" ${hasDisabledInput} ><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                    </tr>`);
                }
            
        }
        this.updateContadorItem();
        this.autoUpdateSubtotal();
        this.calcularTotal();
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();
        tempArchivoAdjuntoItemList=[];
        data.forEach(element => {
            if (element.adjuntos.length > 0) {
                (element.adjuntos).forEach(adjunto => {
                    tempArchivoAdjuntoItemList.push({
                        id: adjunto.id_adjunto,
                        idRegister: adjunto.id_detalle_requerimiento,
                        nameFile: adjunto.archivo,
                        typeFile: null,
                        sizeFile: null,
                        file: []
                    });
                });

            }

        });

        ArchivoAdjunto.updateContadorTotalAdjuntosPorItem();

    }

    getTipoCambioCompra(){
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        let fechaHoy =now.toISOString().slice(0, 10)
        
        this.requerimientoCtrl.getTipoCambioCompra(fechaHoy).then((tipoCambioCompra)=> {
                document.querySelector("span[id='tipo_cambio_compra']").textContent= tipoCambioCompra;
        }).catch(function(err) {
            console.log(err)
        })
    }

    imprimirRequerimientoPdf(){
        var id = document.getElementsByName("id_requerimiento")[0].value;
        window.open('imprimir-requerimiento-pdf/'+id+'/0');
    
    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if(nodeTbody!=null){
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }

        }
    }
    // cabecera requerimiento
    changeMonedaSelect(e) {
        let simboloMonedaPresupuestoUtilizado =document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if(allSelectorSimboloMoneda.length >0){
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent=simboloMonedaPresupuestoUtilizado;
            });
        }

        // let moneda = e.target.value == 1 ? 'S/' : '$';

        document.querySelector("div[name='montoMoneda']").textContent = simboloMonedaPresupuestoUtilizado;
        // if (document.querySelector("form[id='form-requerimiento'] table span[class='moneda']")) {
        //     document.querySelectorAll("form[id='form-requerimiento'] span[class='moneda']").forEach(element => {
        //         element.textContent = moneda;
        //     });
        // }
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();

    }

    changeOptEmpresaSelect(obj) {
        this.getDataSelectSede(obj.target.value);
    }

    getDataSelectSede(idEmpresa = null) {
        if (idEmpresa > 0) {
            this.requerimientoCtrl.obtenerSede(idEmpresa).then((res)=> {
                this.llenarSelectSede(res);
                this.seleccionarAlmacen(res)
                this.llenarUbigeo();
            }).catch(function (err) {
                console.log(err)
            })
        }
        return false;
    }

    llenarSelectSede(array) {

        let selectElement = document.querySelector("div[id='input-group-sede'] select[name='sede']");
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
            if (element.codigo == 'LIMA' || element.codigo == 'Lima') { // default sede lima
                option.setAttribute('selected', 'selected');

            }
            option.setAttribute('data-ubigeo', element.id_ubigeo);
            option.setAttribute('data-name-ubigeo', element.ubigeo_descripcion);
            selectElement.add(option);
        });

        if (array.length > 0) {
            this.updateSedeByPassingElement(selectElement);
        }

    }

    seleccionarAlmacen(data) {
        // let firstSede = data[0].id_sede;
        let selectAlmacen = document.querySelector("div[id='input-group-almacen'] select[name='id_almacen']");
        if (selectAlmacen.options.length > 0) {
            let i, L = selectAlmacen.options.length - 1;
            for (i = L; i > 0; i--) {
                if (selectAlmacen.options[i].dataset.idEmpresa == document.querySelector("select[id='empresa']").value) {
                    if ([4, 10, 11, 12, 13, 14].includes(parseInt(selectAlmacen.options[i].dataset.idSede)) == true) { ///default almacen lima
                        selectAlmacen.options[i].setAttribute('selected', true);
                    }
                }
            }
        }
    }

    llenarUbigeo() {
        let ubigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        let nameUbigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
        document.querySelector("input[name='ubigeo']").value = ubigeo;
        document.querySelector("input[name='name_ubigeo']").value = nameUbigeo;
        //let sede = $('[name=sede]').val();
    }

    changeOptUbigeo(e) {
        let ubigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        let nameUbigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;

        document.querySelector("input[name='ubigeo']").value = ubigeo;
        document.querySelector("input[name='name_ubigeo']").value = nameUbigeo;
        this.cargarAlmacenes($('[name=sede]').val());
    }

    cargarAlmacenes(sede) {
        if (sede !== '') {
            this.requerimientoCtrl.obtenerAlmacenes(sede).then((res)=> {
                let option = '';
                for (let i = 0; i < res.length; i++) {
                    if (res.length == 1) {
                        option += '<option data-id-sede="' + res[i].id_sede + '" data-id-empresa="' + res[i].id_empresa + '" value="' + res[i].id_almacen + '" selected>' + res[i].codigo + ' - ' + res[i].descripcion + '</option>';

                    } else {
                        option += '<option data-id-sede="' + res[i].id_sede + '" data-id-empresa="' + res[i].id_empresa + '" value="' + res[i].id_almacen + '">' + res[i].codigo + ' - ' + res[i].descripcion + '</option>';

                    }
                }
                $('[name=id_almacen]').html('<option value="0" disabled selected>Elija una opción</option>' + option);
            }).catch(function (err) {
                console.log(err)
            })
        }
    }

    changeStockParaAlmacen(event) {

        if (event.target.checked) {
            document.querySelector("div[id='input-group-asignar_trabajador']").classList.add("oculto");
        } else {
            document.querySelector("div[id='input-group-asignar_trabajador']").classList.remove("oculto");
        }
    }

    changeProyecto(event) {

        tempCentroCostoSelected = {
            'id': event.target.options[event.target.selectedIndex].getAttribute('data-id-centro-costo'),
            'codigo': event.target.options[event.target.selectedIndex].getAttribute('data-codigo-centro-costo'),
            'descripcion': event.target.options[event.target.selectedIndex].getAttribute('data-descripcion-centro-costo')
        };
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        if (tempCentroCostoSelected.id > 0) {
            if (tbodyChildren.length > 0) {
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("input[class='centroCosto']").value = tempCentroCostoSelected.id;
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").setAttribute('title', tempCentroCostoSelected.codigo);
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").textContent = tempCentroCostoSelected.descripcion;
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('disabled', true);
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('title', 'El centro de costo esta asignado a un proyecto');
                }
            }

        } else {
            Swal.fire(
                '',
                'El proyecto seleccionado no tiene un centro de costo preasignado, puede seleccionar manualmente',
                'info'
            );
            if (tbodyChildren.length > 0) {
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("input[class='centroCosto']").value = '';
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").setAttribute('title', '');
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").textContent = '';
                    tbodyChildren[i].querySelector("button[name='centroCostos']").removeAttribute('disabled');
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('title', '');
                }
            }
        }


        let codigoProyecto = event.target.options[event.target.selectedIndex].getAttribute('data-codigo');

        document.querySelector("form[id='form-requerimiento'] input[name='codigo_proyecto']").value = codigoProyecto;
    }

    updateConcepto(obj) {
    
        if (obj.target.value.length > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }
    updateEmpresa(obj) {

        if (obj.target.value.length > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();

            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }
    updateSede(obj) {
        if (obj.target.value.length > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }
    updateSedeByPassingElement(obj) {
        if (obj.value.length > 0) {
            obj.closest('div').classList.remove("has-error");
            if (obj.closest("div").querySelector("span")) {
                obj.closest("div").querySelector("span").remove();
            }
        } else {
            obj.closest('div').classList.add("has-error");
        }
    }
    updateFechaLimite(obj) {
        if (obj.target.value.length > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }


    // detalle requerimiento

    makeId() {
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for (let i = 0; i < 12; i++) {
            ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }


    agregarFilaEvent() {
        document.querySelector("button[id='btn-add-producto']").addEventListener('click', (event) => {

            vista_extendida();

            let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td></td>
            <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info handleClickCargarModalPartidas" name="partida">Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="partida" name="idPartida[]" hidden>
                </div>
            </td>
            <td><p class="descripcion-centro-costo" title="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.codigo : ''}">${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.descripcion : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary handleClickCargarModalCentroCostos" name="centroCostos"  ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="centroCosto" name="idCentroCosto[]" value="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.id : ''}" hidden>
                </div>
            </td>
            <td><input class="form-control input-sm" type="text" name="partNumber[]" placeholder="Part number"></td>
            <td>
                <div class="form-group">
                    <textarea class="form-control input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción" ></textarea></td>
                </div>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]" placeholder="Cantidad">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdatePrecioItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]" placeholder="Precio U."></td>
                </div>  
            <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
            <td>
                <div class="btn-group" role="group">
                    <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                    <input type="hidden" class="idRegister" name="idRegister[]" value="${this.makeId()}">
                    <button type="button" class="btn btn-warning btn-xs handleClickAdjuntarArchivoItem" name="btnAdjuntarArchivoItem[]" title="Adjuntos" >
                        <i class="fas fa-paperclip"></i>
                        <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                    </button> 
                    <button type="button" class="btn btn-danger btn-xs handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar"  ><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
            </tr>`);

            this.updateContadorItem();

        });
        document.querySelector("button[id='btn-add-servicio']").addEventListener('click', (event) => {

            vista_extendida();

            // let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            // let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td></td>
            <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info handleClickCargarModalPartidas" name="partida">Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="partida" name="idPartida[]" hidden>
                </div>
                </td>
                <td><p class="descripcion-centro-costo" title="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.codigo : ''}">${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.descripcion : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary handleClickCargarModalCentroCostos" name="centroCostos"  ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="centroCosto" name="idCentroCosto[]" value="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.id : ''}" hidden>
                </div>
            </td>
            <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
            <td>
                <div class="form-group">
                    <textarea class="form-control input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción"></textarea>
                </div>
            </td>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]"  placeholder="Cantidad">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdatePrecioItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]"  placeholder="Precio U.">
                </div>
            </td>
            <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
            <td>
                <div class="btn-group" role="group">
                    <input type="hidden" class="tipoItem" name="tipoItem[]" value="2">
                    <input type="hidden" class="idRegister" name="idRegister[]" value="${this.makeId()}">
                    <button type="button" class="btn btn-warning btn-xs handleClickAdjuntarArchivoItem" name="btnAdjuntarArchivoItem[]" title="Adjuntos" >
                        <i class="fas fa-paperclip"></i>
                        <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                    </button>
                    <button type="button" class="btn btn-danger btn-xs handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar" ><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
            </tr>`);

 
            this.updateContadorItem();

        });
    }

    updateContadorItem() {
        let childrenTableTbody = document.querySelector("tbody[id='body_detalle_requerimiento']").children;

        for (let index = 0; index < childrenTableTbody.length; index++) {
            childrenTableTbody[index].firstElementChild.textContent = index + 1
        }
    }
    autoUpdateSubtotal() {

        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let i = 0; i < tbodyChildren.length; i++) {
            this.updateSubtotal(tbodyChildren[i]);
        }
    }

    updateSubtotal(obj) {
        // console.log(obj);
        let tr = obj.closest("tr");
        let cantidad = parseFloat(tr.querySelector("input[class~='cantidad']").value);
        let precioUnitario = parseFloat(tr.querySelector("input[class~='precio']").value);
        let subtotal = (cantidad * precioUnitario);
        tr.querySelector("span[class='subtotal']").textContent = Util.formatoNumero(subtotal, 2);
        this.calcularTotal();
    }


    updatePartidaItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }
    }
    updateCentroCostoItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }
    }

    updateCantidadItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }
    updatePrecioItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }
    updateDescripcionItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }

    calcularTotal() {
        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento']");
        let childrenTableTbody = TableTBody.children;
        let total = 0;
        for (let index = 0; index < childrenTableTbody.length; index++) {
            // console.log(childrenTableTbody[index]);
            let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad']").value ? childrenTableTbody[index].querySelector("input[class~='cantidad']").value : 0);
            let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value ? childrenTableTbody[index].querySelector("input[class~='precio']").value : 0);
            total += (cantidad * precioUnitario);
        }
        document.querySelector("label[name='total']").textContent = Util.formatoNumero(total, 2);
    }

    // partidas 
    cargarModalPartidas(obj) {
    
        tempObjectBtnPartida = obj.target;
        let id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
        let id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
        let usuarioProyectos = false;
        grupos.forEach(element => {
            if (element.id_grupo == 3) { // proyectos
                usuarioProyectos = true
            }
        });
        if (id_grupo > 0) {
            $('#modal-partidas').modal({
                show: true,
                backdrop: 'true'
            });
            this.listarPartidas(id_grupo, id_proyecto > 0 ? id_proyecto : null);
        } else {
            Swal.fire(
                '',
                'No se puedo seleccionar el grupo al que pertence el usuario.',
                'warning'
            );
        }
    }

    listarPartidas(idGrupo, idProyecto) {
        this.limpiarTabla('listaPartidas');

        this.requerimientoCtrl.obtenerListaPartidas(idGrupo, idProyecto).then((res) => {
            this.construirListaPartidas(res);

        }).catch(function (err) {
            console.log(err)
        })
    }

    construirListaPartidas(data) {

        let html = '';
        let isVisible = '';
        data['presupuesto'].forEach(resup => {
            html += ` 
            <div id='${resup.codigo}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading handleClickapertura" data-id-presup="${resup.id_presup}" style="margin: 0; cursor: pointer;">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${resup.descripcion} 
                </h5>
                <div id="pres-${resup.id_presup}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id="listaPartidas" width="100%" style="font-size:0.9em">
                        <tbody> 
            `;



            data['titulos'].forEach(titulo => {
                html += `
                <tr id="com-${titulo.id_titulo}">
                    <td><strong>${titulo.codigo}</strong></td>
                    <td><strong>${titulo.descripcion}</strong></td>
                    <td class="right ${isVisible}"><strong>S/${Util.formatoNumero(titulo.total, 2)}</strong></td>
                </tr> `;

                data['partidas'].forEach(partida => {
                    if (titulo.codigo == partida.cod_padre) {
                        html += `<tr id="par-${partida.id_partida}">
                            <td style="width:15%; text-align:left;" name="codigo">${partida.codigo}</td>
                            <td style="width:75%; text-align:left;" name="descripcion">${partida.des_pardet}</td>
                            <td style="width:15%; text-align:right;" name="importe_total" class="right ${isVisible}" data-presupuesto-total="${partida.importe_total}" >S/${Util.formatoNumero(partida.importe_total, 2)}</td>
                            <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs handleClickSelectPartida" data-id-partida="${partida.id_partida}">Seleccionar</button></td>
                        </tr>`;
                    }
                });

                
            });
            html += `
                    </tbody>
                </table>
            </div>
        </div>`;
        });
        document.querySelector("div[id='listaPartidas']").innerHTML = html;
 

 
 

        $('#modal-partidas div.modal-body').LoadingOverlay("hide", true);
 
    }
    

    apertura(idPresup) {
        // let idPresup = e.target.dataset.idPresup;
        if ($("#pres-" + idPresup + " ").hasClass('oculto')) {
            $("#pres-" + idPresup + " ").removeClass('oculto');
            $("#pres-" + idPresup + " ").addClass('visible');
        } else {
            $("#pres-" + idPresup + " ").removeClass('visible');
            $("#pres-" + idPresup + " ").addClass('oculto');
        }
 

    }

    changeBtnIcon(obj) {
        
        if (obj.currentTarget.children[0].className == 'fas fa-chevron-right') {

            obj.currentTarget.children[0].classList.replace('fa-chevron-right', 'fa-chevron-down')
        } else {
            obj.currentTarget.children[0].classList.replace('fa-chevron-down', 'fa-chevron-right')
        }
    }

    selectPartida(idPartida) {
        // console.log(idPartida);
        let codigo = $("#par-" + idPartida + " ").find("td[name=codigo]")[0].innerHTML;
        let descripcion = $("#par-" + idPartida + " ").find("td[name=descripcion]")[0].innerHTML;
        let presupuestoTotal = $("#par-" + idPartida + " ").find("td[name=importe_total]")[0].dataset.presupuestoTotal;
        
        tempObjectBtnPartida.nextElementSibling.querySelector("input").value = idPartida;
        tempObjectBtnPartida.textContent = 'Cambiar';

        let tr = tempObjectBtnPartida.closest("tr");
        tr.querySelector("p[class='descripcion-partida']").dataset.idPartida = idPartida;
        tr.querySelector("p[class='descripcion-partida']").textContent = descripcion
        tr.querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal = presupuestoTotal;
        tr.querySelector("p[class='descripcion-partida']").setAttribute('title', codigo);

        this.updatePartidaItem(tempObjectBtnPartida.nextElementSibling.querySelector("input"));
        $('#modal-partidas').modal('hide');
        // tempObjectBtnPartida = null;  debe estar

        this.calcularPresupuestoUtilizadoYSaldoPorPartida();
    }

    calcularPresupuestoUtilizadoYSaldoPorPartida() {
        let tempPartidasActivas = [];
        let partidaAgregadas = [];
        let subtotalItemList = [];
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;

        let idMonedaPresupuestoUtilizado= document.querySelector("select[name='moneda']").value;
        let simboloMonedaPresupuestoUtilizado= document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo;
        let actualTipoCambioCompra=document.querySelector("span[id='tipo_cambio_compra']").textContent;


        
        for (let index = 0; index < tbodyChildren.length; index++) {
            if (tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida > 0) {
                if (!partidaAgregadas.includes(tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida)) {
                    partidaAgregadas.push(tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida);
                    tempPartidasActivas.push({
                        'id_partida': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida,
                        'codigo': tbodyChildren[index].querySelector("p[class='descripcion-partida']").title,
                        'descripcion': tbodyChildren[index].querySelector("p[class='descripcion-partida']").textContent,
                        'presupuesto_total': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal,
                        'id_moneda_presupuesto_utilizado': idMonedaPresupuestoUtilizado,
                        'simbolo_moneda_presupuesto_utilizado': simboloMonedaPresupuestoUtilizado,
                        'presupuesto_utilizado_al_cambio': 0,
                        'presupuesto_utilizado': 0,
                        'saldo': 0
                    });
                }

                subtotalItemList.push({
                    'id_partida': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida,
                    'subtotal': (tbodyChildren[index].querySelector("input[class~='cantidad']").value > 0 ? tbodyChildren[index].querySelector("input[class~='cantidad']").value : 0) * (tbodyChildren[index].querySelector("input[class~='precio']").value > 0 ? tbodyChildren[index].querySelector("input[class~='precio']").value : 0)
                });

            }
        }


        for (let p = 0; p < tempPartidasActivas.length; p++) {
            for (let i = 0; i < subtotalItemList.length; i++) {
                if (tempPartidasActivas[p].id_partida == subtotalItemList[i].id_partida) {
                    tempPartidasActivas[p].presupuesto_utilizado += subtotalItemList[i].subtotal;
                }
            }
        }

        for (let p = 0; p < tempPartidasActivas.length; p++) {
            if(tempPartidasActivas[p].id_moneda_presupuesto_utilizado==2){ // moneda dolares
                let alCambio=tempPartidasActivas[p].presupuesto_utilizado * actualTipoCambioCompra;
                tempPartidasActivas[p].presupuesto_utilizado_al_cambio= alCambio;
                tempPartidasActivas[p].saldo = tempPartidasActivas[p].presupuesto_total - (alCambio > 0 ? alCambio : 0);
            }else{
                tempPartidasActivas[p].saldo = tempPartidasActivas[p].presupuesto_total - (tempPartidasActivas[p].presupuesto_utilizado > 0 ? tempPartidasActivas[p].presupuesto_utilizado : 0);

            }
        }

        for (let p = 0; p < tempPartidasActivas.length; p++) {

        }


        this.validarPresupuestoUtilizadoYSaldoPorPartida(tempPartidasActivas);
        this.construirTablaPresupuestoUtilizadoYSaldoPorPartida(tempPartidasActivas);
        // console.log(tempPartidasActivas);
    }
    validarPresupuestoUtilizadoYSaldoPorPartida(data) {


        let mensajeAlerta = '';
 
        data.forEach(partida => {
            if (partida.saldo < 0) {
                
                mensajeAlerta += `La partida ${partida.codigo} - ${partida.descripcion} a excedido el presupuesto asignado, tiene un saldo actual de ${Util.formatoNumero(partida.saldo, 2)}. \n`
            }
        });
        if (mensajeAlerta.length > 0) {

            Lobibox.notify('info', {
                title:false,
                size: 'normal',
                width: 500,  
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: mensajeAlerta
            });


        }
    }

    construirTablaPresupuestoUtilizadoYSaldoPorPartida(data) {
        this.limpiarTabla('listaPartidasActivas');
        data.forEach(element => { 

            document.querySelector("tbody[id='body_partidas_activas']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td>${element.codigo}</td>
                <td>${element.descripcion}</td>
                <td style="text-align:right;"><span>S/</span>${Util.formatoNumero(element.presupuesto_total, 2)}</td>
                <td style="text-align:right;"><span class="simboloMoneda">${element.simbolo_moneda_presupuesto_utilizado}</span>${element.presupuesto_utilizado_al_cambio>0?(Util.formatoNumero(element.presupuesto_utilizado, 2)+' (S/'+Util.formatoNumero(element.presupuesto_utilizado_al_cambio, 2)+')'):(Util.formatoNumero(element.presupuesto_utilizado, 2))}</td>
                <td style="text-align:right; color:${element.saldo >= 0 ? '#333' : '#dd4b39'}"><span>S/</span>${Util.formatoNumero(element.saldo, 2)}</td>
            </tr>`);

        });

    }

    //centro de costos
    cargarModalCentroCostos(obj) {
        tempObjectBtnCentroCostos = obj.target;

        $('#modal-centro-costos').modal({
            show: true
        });
        this.listarCentroCostos();
    }

    listarCentroCostos() {
        this.limpiarTabla('listaCentroCosto');

        this.requerimientoCtrl.obtenerCentroCostos().then( (res)=> {
            this.construirCentroCostos(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirCentroCostos(data) {
        let html = '';
        data.forEach((padre, index) => {
            if (padre.id_padre == null) {
                html += `
                <div id='${index}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading handleClickapertura" style="margin: 0; cursor: pointer;" data-id-presup="${index}">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${padre.descripcion} 
                </h5>
                <div id="pres-${index}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id='listaCentroCosto' width="" style="font-size:0.9em">
                        <thead>
                            <tr>
                            <td style="width:5%"></td>
                            <td style="width:90%"></td>
                            <td style="width:5%"></td>
                            </tr>
                        </thead>
                        <tbody>`;

                data.forEach(hijo => {
                    if (padre.id_centro_costo == hijo.id_padre) {
                        if ((hijo.id_padre > 0) && (hijo.estado == 1)) {
                            if (hijo.nivel == 2) {
                                html += `
                                <tr id="com-${hijo.id_centro_costo}">
                                    <td><strong>${hijo.codigo}</strong></td>
                                    <td><strong>${hijo.descripcion}</strong></td>
                                    <td style="width:5%; text-align:center;"></td>
                                </tr> `;
                            }
                        }
                        data.forEach(hijo3 => {
                            if (hijo.id_centro_costo == hijo3.id_padre) {
                                if ((hijo3.id_padre > 0) && (hijo3.estado == 1)) {
                                    // console.log(hijo3);
                                    if (hijo3.nivel == 3) {
                                        html += `
                                        <tr id="com-${hijo3.id_centro_costo}">
                                            <td>${hijo3.codigo}</td>
                                            <td>${hijo3.descripcion}</td>
                                            <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs handleClickSelectCentroCosto" data-id-centro-costo="${hijo3.id_centro_costo}" data-codigo="${hijo3.codigo}" data-descripcion-centro-costo="${hijo3.descripcion}" >Seleccionar</button></td>
                                        </tr> `;
                                    }
                                }
                                data.forEach(hijo4 => {
                                    if (hijo3.id_centro_costo == hijo4.id_padre) {
                                        console.log(hijo4);
                                        if ((hijo4.id_padre > 0) && (hijo4.estado == 1)) {
                                            if (hijo4.nivel == 4) {
                                                html += `
                                                <tr id="com-${hijo4.id_centro_costo}">
                                                    <td>${hijo4.codigo}</td>
                                                    <td>${hijo4.descripcion}</td>
                                                    <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs handleClickSelectCentroCosto" data-id-centro-costo="${hijo4.id_centro_costo}" data-codigo="${hijo4.codigo}" data-descripcion-centro-costo="${hijo4.descripcion}">Seleccionar</button></td>
                                                </tr> `;
                                            }
                                        }
                                    }
                                });
                            }

                        });
                    }


                });
                html += `
                </tbody>
            </table>
        </div>
    </div>`;
            }
        });
        document.querySelector("div[name='centro-costos-panel']").innerHTML = html;

    
 
        $('#modal-centro-costos div.modal-body').LoadingOverlay("hide", true);

    }


    selectCentroCosto(idCentroCosto, codigo, descripcion) {
        // console.log(idCentroCosto);
        tempObjectBtnCentroCostos.nextElementSibling.querySelector("input").value = idCentroCosto;
        tempObjectBtnCentroCostos.textContent = 'Cambiar';

        let tr = tempObjectBtnCentroCostos.closest("tr");
        tr.querySelector("p[class='descripcion-centro-costo']").textContent = descripcion
        tr.querySelector("p[class='descripcion-centro-costo']").setAttribute('title', codigo);
        this.updateCentroCostoItem(tempObjectBtnCentroCostos.nextElementSibling.querySelector("input"));
        $('#modal-centro-costos').modal('hide');
        tempObjectBtnCentroCostos = null;
        // componerTdItemDetalleRequerimiento();
    }

    eliminarItem(obj) {
        let tr = obj.target.closest("tr");
        tr.remove();
        this.updateContadorItem();
        this.calcularTotal();
    }

    //adjunto cabecera requerimiento 

    adjuntarArchivoRequerimiento() {
        $('#modal-adjuntar-archivos-requerimiento').modal({
            show: true
        });


        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[class='bootstrap-filestyle input-group'] input[type='text']").classList.add('oculto');
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] span[class='buttonText']").textContent = "Agregar archivo";
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');

        this.limpiarTabla('listaArchivosRequerimiento');

        this.listarAdjuntosDeCabecera();
    }

    listarAdjuntosDeCabecera() {

        this.requerimientoCtrl.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
            this.construirTablaAdjuntosRequerimiento(tempArchivoAdjuntoRequerimientoList, categoriaAdjuntoList);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaAdjuntosRequerimiento(data, categoriaAdjuntoList) {
        let html = '';
        let hasDisableBtnEliminarArchivoRequerimiento= '';
        let estadoActualRequerimiento = document.querySelector("input[name='estado']").value;
        if( estadoActualRequerimiento !=1 && estadoActualRequerimiento !=3){
            hasDisableBtnEliminarArchivoRequerimiento = 'disabled';
        }
        data.forEach(element => {
            html += `<tr id="${element.id}" style="text-align:center">
        <td style="text-align:left;">${element.nameFile}</td>
        <td>
            <select class="form-control" name="categoriaAdjunto" onChange="ArchivoAdjunto.changeCategoriaAdjunto(this)" ${hasDisableBtnEliminarArchivoRequerimiento}>
        `;
            categoriaAdjuntoList.forEach(categoria => {
                if (element.category == categoria.id_categoria_adjunto) {
                    html += `<option value="${categoria.id_categoria_adjunto}" selected >${categoria.descripcion}</option>`

                } else {
                    html += `<option value="${categoria.id_categoria_adjunto}">${categoria.descripcion}</option>`
                }
            });
            html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">`;
            if (Number.isInteger(element.id)) {
                html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoRequerimiento" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoRequerimiento('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
            }
            html += `<button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoRequerimiento" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoRequerimiento(this,'${element.id}');" ${hasDisableBtnEliminarArchivoRequerimiento} ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;
        });
        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html);
    }

    agregarAdjuntoRequerimiento(event) {
        let archivoAdjunto = new ArchivoAdjunto(event.files,this);
        archivoAdjunto.addFileLevelRequerimiento();
    }

    // adjuntos detalle requerimiento

    adjuntarArchivoItem(obj) {

        tempIdRegisterActive = obj.closest('td').querySelector("input[class~='idRegister']").value;
        tempObjectBtnInputFile = obj;

        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] span[class='buttonText']").textContent = 'Agregar archivo';
        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[class='bootstrap-filestyle input-group'] input[type='text']").classList.add('oculto');

        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');

        this.limpiarTabla('listaArchivos');
        this.listarAdjuntosDeItem();
        
        $('#modal-adjuntar-archivos-detalle-requerimiento').one("change","input.handleChangeAgregarAdjuntoItem", (e)=>{
            this.agregarAdjuntoItem(e);
        });

    }

    listarAdjuntosDeItem() {
        let html = '';
        let hasDisableBtnEliminarArchivoRequerimiento= '';
        let estadoActualRequerimiento = document.querySelector("input[name='estado']").value;
        if( estadoActualRequerimiento !=1 && estadoActualRequerimiento !=3){
            hasDisableBtnEliminarArchivoRequerimiento = 'disabled';
        }
        tempArchivoAdjuntoItemList.forEach(element => {
            if (tempIdRegisterActive == element.idRegister) {
                html += `<tr>
                <td style="text-align:left;">${element.nameFile}</td>
                <td style="text-align:center;">
                <div class="btn-group" role="group">`;
                if (Number.isInteger(element.id)) {
                    html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoItem" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoItem('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
                }
                html += `<button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoItem" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoItem(this,'${element.id}');" ${hasDisableBtnEliminarArchivoRequerimiento}><i class="fas fa-trash-alt"></i></button>`;
                html += `</div>
                </td>
                </tr>`;
            }
        });
        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);
    }

    agregarAdjuntoItem(event) {
        let archivoAdjunto = new ArchivoAdjunto(event.target.files,this);
        archivoAdjunto.addFileLevelItem();
    }

    // guardar requerimiento

    actionGuardarEditarRequerimiento() {

        let continuar = true;
        if (document.querySelector("tbody[id='body_detalle_requerimiento']").childElementCount == 0) {
            Swal.fire(
                '',
                'Ingrese por lo menos un producto/servicio',
                'warning'
            );
            return false;
        }

        if (document.querySelector("input[name='concepto']").value == '') {
            continuar = false;
            if (document.querySelector("input[name='concepto']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Ingrese un concepto/motivo)';
                document.querySelector("input[name='concepto']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("input[name='concepto']").closest('div').classList.add('has-error');
            }

        }

        if (document.querySelector("select[name='empresa']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='empresa']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una empresa)';
                document.querySelector("select[name='empresa']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='empresa']").closest('div').classList.add('has-error');
            }
        }

        if (document.querySelector("select[name='sede']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='sede']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una sede)';
                document.querySelector("select[name='sede']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='sede']").closest('div').classList.add('has-error');
            }

        }

        if (document.querySelector("input[name='fecha_entrega']").value == '') {
            continuar = false;
            if (document.querySelector("input[name='fecha_entrega']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una fecha de entrega)';
                document.querySelector("input[name='fecha_entrega']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("input[name='fecha_entrega']").closest('div').classList.add('has-error');
            }

        }

        if (document.querySelector("select[name='tipo_requerimiento']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='tipo_requerimiento']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione un tipo)';
                document.querySelector("select[name='tipo_requerimiento']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='tipo_requerimiento']").closest('div').classList.add('has-error');
            }

        }

        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let index = 0; index < tbodyChildren.length; index++) {

            // if (tbodyChildren[index].querySelector("input[class~='partida']").value == '') {
            //     continuar = false;
            //     if (tbodyChildren[index].querySelector("input[class~='partida']").closest('td').querySelector("span") == null) {
            //         let newSpanInfo = document.createElement("span");
            //         newSpanInfo.classList.add('text-danger');
            //         newSpanInfo.textContent = 'Ingrese una partida';
            //         tbodyChildren[index].querySelector("input[class~='partida']").closest('td').appendChild(newSpanInfo);
            //         tbodyChildren[index].querySelector("input[class~='partida']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
            //     }

            // }
            if (tbodyChildren[index].querySelector("input[class~='centroCosto']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese un centro de costo';
                    tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }

            if (tbodyChildren[index].querySelector("input[class~='cantidad']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese una cantidad';
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }

            if (tbodyChildren[index].querySelector("input[class~='precio']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese un precio';
                    tbodyChildren[index].querySelector("input[class~='precio']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }

            if (tbodyChildren[index].querySelector("textarea[class~='descripcion']")) {
                if (tbodyChildren[index].querySelector("textarea[class~='descripcion']").value == '') {
                    continuar = false;
                    if (tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("span") == null) {
                        let newSpanInfo = document.createElement("span");
                        newSpanInfo.classList.add('text-danger');
                        newSpanInfo.textContent = 'Ingrese una descripción';
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').appendChild(newSpanInfo);
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                    }
                }


            }
        }

        if (continuar) {
            let formData = new FormData($('#form-requerimiento')[0]);
            let ItemWithIdRegisterList = [];
            if (tempArchivoAdjuntoItemList.length > 0) {
                const inputIdRegister = document.querySelectorAll("input[class~='idRegister']");
                inputIdRegister.forEach(element => {
                    ItemWithIdRegisterList.push(element.value);
                });
                tempArchivoAdjuntoItemList.forEach(element => {
                    if(ItemWithIdRegisterList.includes((element.idRegister).toString()) == true) {
                        // formData.append(`archivoAdjuntoItem${element.idRegister}[]`, element.file, element.nameFile);
                        formData.append(`archivoAdjuntoItem${element.idRegister}[]`, element.file);
                    }
                });

            }

            formData.append(`archivoAdjuntoItemToDelete[]`, tempArchivoAdjuntoItemToDeleteList);


            if (tempArchivoAdjuntoRequerimientoList.length > 0) {
                tempArchivoAdjuntoRequerimientoList.forEach(element => {
                    formData.append(`archivoAdjuntoRequerimiento${element.category}[]`, element.file);
                });

            }

            formData.append(`archivoAdjuntoRequerimientoToDelete[]`, tempArchivoAdjuntoRequerimientoToDeleteList);


            let typeActionForm = document.querySelector("form[id='form-requerimiento']").getAttribute("type"); //  register | edition

            if (typeActionForm == 'register') {
                $.ajax({
                    type: 'POST',
                    url: 'guardar-requerimiento',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend:  (data)=> { // Are not working with dataType:'jsonp'

                        // $('#modal-loader').modal({backdrop: 'static', keyboard: false});
                        var customElement = $("<div>", {
                            "css": {
                                "font-size": "24px",
                                "text-align": "center",
                                "padding": "0px",
                                "margin-top": "-400px"
                            },
                            "class": "your-custom-class",
                            "text": "Guardando requerimiento..."
                        });

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            custom: customElement,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) =>{
                        if (response.id_requerimiento > 0) {
                            $('#wrapper-okc').LoadingOverlay("hide", true);

                            Lobibox.notify('success', {
                                title:false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Se ha creado el requerimiento ${response.codigo}`
                            });
                            // location.reload();
                            this.RestablecerFormularioRequerimiento();
                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            console.log(response.mensaje,);
                            Swal.fire(
                                '',
                                'Lo sentimos hubo un error en el servidor al intentar guardar el requerimiento, por favor vuelva a intentarlo',
                                'error'
                            );
                        }
                    },
                    fail:  (jqXHR, textStatus, errorThrown) =>{
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un error en el servidor al intentar guardar el requerimiento, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
            if (typeActionForm == 'edition') {
                $.ajax({
                    type: 'POST',
                    url: 'actualizar-requerimiento',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend:  (data)=> {
                        var customElement = $("<div>", {
                            "css": {
                                "font-size": "24px",
                                "text-align": "center",
                                "padding": "0px",
                                "margin-top": "-400px"
                            },
                            "class": "your-custom-class",
                            "text": "Actualizando requerimiento..."
                        });

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            custom: customElement,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) =>{
                        if (response.id_requerimiento > 0) {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            Lobibox.notify('success', {
                                title:false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: `Requerimiento actualizado`
                            });
                            this.cargarRequerimiento(response.id_requerimiento);
                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            console.log(response.mensaje);
                            Swal.fire(
                                '',
                                'Lo sentimos hubo un error en el servidor al intentar guardar el requerimiento, por favor vuelva a intentarlo',
                                'error'
                            );

                        }
                        changeStateButton('historial'); //init.js
                    },
                    fail:   (jqXHR, textStatus, errorThrown)=> {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un error en el servidor al intentar guardar el requerimiento, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });


            }


        } else {
            Swal.fire(
                '',
                'Por favor ingrese los datos faltantes en el formulario',
                'warning'
            );
            console.log("no se va a guardar");
        }
    }

    RestablecerFormularioRequerimiento(){
        $('#form-requerimiento')[0].reset();
        this.limpiarTabla('ListaDetalleRequerimiento');
        this.limpiarTabla('listaArchivosRequerimiento');
        this.limpiarTabla('listaArchivos');
        this.limpiarTabla('listaPartidasActivas');
        this.limpiarMesajesValidacion();
        tempArchivoAdjuntoItemList = [];
        tempArchivoAdjuntoRequerimientoList = [];
        tempCentroCostoSelected=null;
        tempIdRegisterActive=null
        this.restaurarTotalMonedaDefault();
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();
        document.querySelector("div[id='group-historial-revisiones']").setAttribute("hidden",true);
        document.querySelector("span[name='cantidadAdjuntosRequerimiento']").textContent=0;
        disabledControl(document.getElementsByName("btn-imprimir-requerimento-pdf"), true);
        disabledControl(document.getElementsByName("btn-adjuntos-requerimiento"), true);

    
    }

    limpiarMesajesValidacion(){
        let allDivError = document.querySelectorAll("div[class='form-group has-error']");
        let allSpanDanger =document.querySelectorAll("span[class~='text-danger']");
        if(allDivError.length >0){
            allDivError.forEach(element => {
                element.classList.remove('has-error');
            });
        }
        if(allSpanDanger.length >0){
            allSpanDanger.forEach(element => {
                element.remove();
            });
        }

    }

    restaurarTotalMonedaDefault(){
        let allSelectorTotal= document.getElementsByName("total");
        let simboloMonedaPresupuestoUtilizado =document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if(allSelectorSimboloMoneda.length >0){
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent=simboloMonedaPresupuestoUtilizado;
            });
        }
        if(allSelectorTotal.length >0){
            allSelectorTotal.forEach(element => {
                element.textContent='0.00';
            });
        }
    }

}
