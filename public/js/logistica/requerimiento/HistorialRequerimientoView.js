class HistorialRequerimientoView{
    mostrarHistorial() {
        $('#modal-historial-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        requerimientoCtrl.getListadoElaborados("ME", null, null, null, null, null).then(function (res) {
            historialRequerimientoView.construirTablaHistorialRequerimientosElaborados(res['data']);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaHistorialRequerimientosElaborados(data) {
        console.log(data);
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
                { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc' },
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
                        let btnSeleccionar = '<button type="button" class="btn btn-xs btn-success" title="Seleccionar" onClick="historialRequerimientoView.cargarRequerimiento(' + row['id_requerimiento'] + ');">Seleccionar</button>';
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
            }
        });

        $('#ListaReq').DataTable().on("draw", function () {
            resizeSide();
        });

        $('#ListaReq tbody').on('click', 'tr', function () {
            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#ListaReq').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
        });
    }

    cargarRequerimiento(idRequerimiento) {
        $('#modal-historial-requerimiento').modal('hide');
        const objecto= this;
        requerimientoCtrl.getRequerimiento(idRequerimiento).then(function (res) {
            objecto.mostrarRequerimiento(res);

        }).catch(function (err) {
            console.log(err)
        });
    }


    mostrarRequerimiento(data) {
        if (data.hasOwnProperty('requerimiento')) {
            var btnImprimirRequerimiento = document.getElementsByName("btn-imprimir-requerimento-pdf");
            disabledControl(btnImprimirRequerimiento, false);
            var btnAdjuntosRequerimiento = document.getElementsByName("btn-adjuntos-requerimiento");
            disabledControl(btnAdjuntosRequerimiento, false);
            var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
            disabledControl(btnTrazabilidadRequerimiento, false);

            historialRequerimientoView.mostrarCabeceraRequerimiento(data['requerimiento'][0]);
            if (data.hasOwnProperty('det_req')) {
                if(data['requerimiento'][0].estado == 7){
                    changeStateButton('cancelar'); //init.js
                }else if(data['requerimiento'][0].estado ==1  && data['requerimiento'][0].id_usuario == auth_user.id_usuario){
                    changeStateButton('historial'); //init.js
                }else if((data['requerimiento'][0].estado ==1 || data['requerimiento'][0].estado ==3)  && data['requerimiento'][0].id_usuario == auth_user.id_usuario){
                    document.querySelector("div[id='group-historial-revisiones']").removeAttribute('hidden');
                    historialRequerimientoView.mostrarHistorialRevisionAprobacion(data['historial_aprobacion']);
                    changeStateButton('historial'); //init.js
                }else{
                    document.querySelector("div[id='group-historial-revisiones']").setAttribute('hidden',true);

                }
                historialRequerimientoView.mostrarDetalleRequerimiento(data['det_req'],data['requerimiento'][0]['estado']);
            }

        } else {
            alert("El requerimiento que intenta cargar no existe");
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
        requerimientoView.getDataSelectSede(data.id_empresa);
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
    }


    mostrarHistorialRevisionAprobacion(data){
        requerimientoView.limpiarTabla('listaHistorialRevision');

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
        // if(,estado ==3 || estado == 7){
        //     hasDisabledInput= 'disabled';
        // }

        requerimientoView.limpiarTabla('ListaDetalleRequerimiento');
        vista_extendida();
        for (let i = 0; i < data.length; i++) {
            if (data[i].estado != 7) { 
                if (data[i].id_tipo_item == 1) { // producto
                document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td></td>
                <td><p class="descripcion-partida" data-id-partida="${data[i].id_partida}" data-presupuesto-total="${data[i].presupuesto_total_partida}" title="${data[i].codigo_partida != null ? data[i].codigo_partida : ''}" >${data[i].descripcion_partida != null ? data[i].descripcion_partida : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-info activation" name="partida" onclick="requerimientoView.cargarModalPartidas(this)" ${hasDisabledInput}>Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="partida" name="idPartida[]" value="${data[i].id_partida}" hidden>
                    </div>
                </td>
                <td><p class="descripcion-centro-costo" title="${data[i].codigo_centro_costo != null ? data[i].codigo_centro_costo : ''}">${data[i].descripcion_centro_costo != null ? data[i].descripcion_centro_costo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary activation" name="centroCostos" onclick="requerimientoView.cargarModalCentroCostos(this)" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" ${hasDisabledInput} >Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="centroCosto" name="idCentroCosto[]" value="${data[i].id_centro_costo}" hidden>
                    </div>
                </td>
                <td><input class="form-control activation input-sm" type="text" name="partNumber[]" placeholder="Part number" value="${data[i].part_number != null ? data[i].part_number : ''}" ${hasDisabledInput}></td>
                <td>
                    <div class="form-group">
                        <textarea class="form-control activation input-sm descripcion" name="descripcion[]" placeholder="Descripción" value="${data[i].descripcion != null ? data[i].descripcion : ''}" onkeyup ="requerimientoView.updateDescripcionItem(this);" ${hasDisabledInput} >${data[i].descripcion != null ? data[i].descripcion : ''}</textarea></td>
                    </div>
                <td><select name="unidad[]" class="form-control activation input-sm" value="${data[i].id_unidad_medida}" ${hasDisabledInput} >${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                <td>
                    <div class="form-group">
                        <input class="form-control activation input-sm cantidad text-right" type="number" min="1" name="cantidad[]"  value="${data[i].cantidad}" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Cantidad" ${hasDisabledInput}>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control activation input-sm precio text-right" type="number" min="0" name="precioUnitario[]" value="${data[i].precio_unitario}" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Precio U." ${hasDisabledInput}>
                    </div>
                </td>  
                <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                <td><textarea class="form-control activation input-sm" name="motivo[]"  value="${data[i].motivo != null ? data[i].motivo : ''}" placeholder="Motivo de requerimiento de item (opcional)" ${hasDisabledInput} >${data[i].motivo != null ? data[i].motivo : ''}</textarea></td>
                <td>
                    <div class="btn-group" role="group">
                        <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                        <input type="hidden" class="idRegister" name="idRegister[]" value="${data[i].id_detalle_requerimiento}">
                        <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)" >
                            <i class="fas fa-paperclip"></i>
                            <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                        </button> 
                        <button type="button" class="btn btn-danger btn-xs activation" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ${hasDisabledInput}><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
                </tr>`);
                } else { // servicio
                    document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td></td>
                    <td><p class="descripcion-partida" data-id-partida="${data[i].id_partida}" data-presupuesto-total="${data[i].presupuesto_total_partida}" title="${data[i].codigo_partida != null ? data[i].codigo_partida : ''}" >${data[i].descripcion_partida != null ? data[i].descripcion_partida : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-info activation" name="partida" onclick="requerimientoView.cargarModalPartidas(this)" ${hasDisabledInput}>Seleccionar</button> 
                        <div class="form-group">
                            <input type="text" class="partida" name="idPartida[]" value="${data[i].id_partida}" hidden>
                        </div>
                    </td>
                    <td><p class="descripcion-centro-costo" title="${data[i].codigo_centro_costo != null ? data[i].codigo_centro_costo : ''}">${data[i].descripcion_centro_costo != null ? data[i].descripcion_centro_costo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary activation" name="centroCostos" onclick="requerimientoView.cargarModalCentroCostos(this)" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" ${hasDisabledInput} >Seleccionar</button> 
                        <div class="form-group">
                            <input type="text" class="centroCosto" name="idCentroCosto[]" value="${data[i].id_centro_costo}" hidden>
                        </div>
                    </td>
                    <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
                    <td>
                        <div class="form-group">
                        <textarea class="form-control activation input-sm descripcion" name="descripcion[]" placeholder="Descripción" value="${data[i].descripcion != null ? data[i].descripcion : ''}" onkeyup ="requerimientoView.updateDescripcionItem(this);" ${hasDisabledInput} >${data[i].descripcion != null ? data[i].descripcion : ''}</textarea></td>
                        </div>
                    <td><select name="unidad[]" class="form-control activation input-sm" value="${data[i].id_unidad_medida}"  ${hasDisabledInput}>${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                    <td>
                        <div class="form-group">
                            <input class="form-control activation input-sm cantidad text-right" type="number" min="1" name="cantidad[]"  value="${data[i].cantidad}" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Cantidad" ${hasDisabledInput}>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input class="form-control activation input-sm precio text-right" type="number" min="0" name="precioUnitario[]" value="${data[i].precio_unitario}" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Precio U." ${hasDisabledInput}>
                        </div>  
                    </td>
                    <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                    <td><textarea class="form-control activation input-sm" name="motivo[]"  value="${data[i].motivo != null ? data[i].motivo : ''}" placeholder="Motivo de requerimiento de item (opcional)" ${hasDisabledInput} >${data[i].motivo != null ? data[i].motivo : ''}</textarea></td>
                    <td>
                        <div class="btn-group" role="group">
                            <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                            <input type="hidden" class="idRegister" name="idRegister[]" value="${data[i].id_detalle_requerimiento}">
                            <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)">
                                <i class="fas fa-paperclip"></i>
                                <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                            </button> 
                            <button type="button" class="btn btn-danger btn-xs activation" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ${hasDisabledInput} ><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                    </tr>`);
                }
            }
        }
        requerimientoView.updateContadorItem();
        requerimientoView.autoUpdateSubtotal();
        requerimientoView.calcularTotal();
        requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();

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
}

const historialRequerimientoView = new HistorialRequerimientoView();
