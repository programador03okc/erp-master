class AprobarRequerimientoView {
    mostrar(idEmpresa, idSede, idGrupo, idPrioridad) {
        requerimientoCtrl.getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad).then(function (res) {
            aprobarRequerimientoView.construirTablaListaRequerimientosPendientesAprobacion(res['data']);
        }).catch(function (err) {
            console.log(err)
        })

    }


    construirTablaListaRequerimientosPendientesAprobacion(data) {
        console.log(data);
        let disabledBtn = true;
        let vardataTables = funcDatatables();
        $('#ListaReqPendienteAprobacion').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            "order": [[4, "desc"]],
            'data': data,
            'columns': [
                {
                    'render': function (data, type, row) {
                        let prioridad = '';
                        let thermometerNormal = '<center><i class="fas fa-thermometer-empty green fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad '+row.descripcion_prioridad+'" ></i></center>';
                        let thermometerAlta = '<center> <i class="fas fa-thermometer-half orange fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad '+row.descripcion_prioridad+'"  ></i></center>';
                        let thermometerCritica = '<center> <i class="fas fa-thermometer-full red fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad '+row.descripcion_prioridad+'"  ></i></center>';
                        if (row.id_prioridad == 1) {
                            prioridad = thermometerNormal
                        } else if (row.id_prioridad == 2) {
                            prioridad = thermometerAlta
                        } else if (row.id_prioridad == 3) {
                            prioridad = thermometerCritica
                        }
                        return prioridad;
                    }
                },
                { 'data': 'codigo', 'name': 'codigo' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'tipo_requerimiento', 'name': 'tipo_requerimiento' },
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega' },
                { 'data': 'razon_social_empresa', 'name': 'razon_social_empresa' },
                { 'data': 'division', 'name': 'division' },
                { 'data': 'observacion', 'name': 'alm_req.observacion' },
                { 'data': 'usuario', 'name': 'usuario' },
                {
                    'render': function (data, type, row) {
                        if(row['estado']==1){
                            return '<span class="label label-default">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==2){
                            return '<span class="label label-success">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==3){
                            return '<span class="label label-warning">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==5){
                            return '<span class="label label-primary">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==7){
                            return '<span class="label label-danger">'+row['estado_doc']+'</span>';
                        }else{
                            return '<span class="label label-default">'+row['estado_doc']+'</span>';

                        }
                    }
                },
                { 'data': 'cantidad_aprobados_total_flujo', 'name': 'cantidad_aprobados_total_flujo' },
                {
                    'render': function (data, type, row) {
                        var list_id_rol_aprob = [];
                        var hasAprobacion = 0;
                        var cantidadObservaciones = 0;
                        var hasObservacionSustentadas = 0;



                        if (row.aprobaciones.length > 0) {
                            row.aprobaciones.forEach(element => {
                                list_id_rol_aprob.push(element.id_rol)
                            });

                            roles.forEach(element => {
                                if (list_id_rol_aprob.includes(element.id_rol) == true) {
                                    hasAprobacion += 1;
                                }

                            });
                        }
                        if (row.observaciones.length > 0) {
                            row.observaciones.forEach(element => {
                                cantidadObservaciones += 1;
                                if (element.id_sustentacion > 0) {
                                    hasObservacionSustentadas += 1;
                                }
                            });
                        }


                        if (hasAprobacion == 0) {
                            disabledBtn = '';
                        } else if (hasAprobacion > 0) {
                            disabledBtn = 'disabled';
                        }
                        if (hasObservacionSustentadas != cantidadObservaciones) {
                            disabledBtn = 'disabled';
                        }

                        if (row.estado == 7) {
                            disabledBtn = 'disabled';
                        }
                        let first_aprob = {};
                        // console.log(row.pendiente_aprobacion);
                        if (row.pendiente_aprobacion.length > 0) {
                            first_aprob = row.pendiente_aprobacion.reduce(function (prev, curr) {
                                return prev.orden < curr.orden ? prev : curr;
                            });

                        }
                        // buscar si la primera aprobación su numero de orden se repite en otro pendiente_aprobacion
                        let aprobRolList = [];
                        // console.log(row.pendiente_aprobacion);
                        let pendAprob = row.pendiente_aprobacion;
                        pendAprob.forEach(element => {
                            if (element.orden == first_aprob.orden) {
                                aprobRolList.push(element.id_rol);
                            }
                        });

                        // si el usuario actual su rol le corresponde aprobar
                        // console.log(row.rol_aprobante_id);
                        // console.log(aprobRolList);

                        // si existe varios con mismo orden 
                        if (aprobRolList.length > 1) {
                            // si existe un rol aprobante ya definido en el requerimiento
                            if (row.rol_aprobante_id > 0) {
                                roles.forEach(element => {
                                    if (row.rol_aprobante_id == element.id_rol) {
                                        // if(aprobRolList.includes(element.id_rol)){
                                        disabledBtn = '';
                                    } else {
                                        disabledBtn = 'disabled';

                                    }

                                });
                            } else {
                                roles.forEach(element => {
                                    if (aprobRolList.includes(element.id_rol)) {
                                        disabledBtn = '';
                                    } else {
                                        disabledBtn = 'disabled';

                                    }

                                });
                            }

                        } else {

                            roles.forEach(element => {
                                if (first_aprob.id_rol == element.id_rol) {
                                    disabledBtn = '';
                                } else {
                                    disabledBtn = 'disabled';

                                }

                            });

                        }

                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnDetalleRapido = `<button type="button" class="btn btn-xs btn-info" title="Ver detalle"   onClick="aprobarRequerimientoView.verDetalleRequerimiento('${row['id_requerimiento']}', '${row['id_doc_aprob']}','${row['id_usuario_aprobante']}','${row['id_rol_aprobante']}','${row['id_flujo']}','${row['aprobacion_final_o_pendiente']}');"><i class="fas fa-eye fa-xs"></i></button>`;
                        // let btnTracking = '<button type="button" class="btn btn-xs bg-primary" title="Explorar Requerimiento" onClick="aprobarRequerimientoView.tracking_requerimiento(' + row['id_requerimiento'] + ');"><i class="fas fa-globe fa-xs"></i></button>';
                        // let btnAprobar = '<button type="button" class="btn btn-xs btn-success" title="Aprobar Requerimiento" onClick="aprobarRequerimientoView.aprobarRequerimientoView(' + row['id_doc_aprob'] + ');" ' + disabledBtn + '><i class="fas fa-check fa-xs"></i></button>';
                        // let btnObservar = '<button type="button" class="btn btn-xs btn-warning" title="Observar Requerimiento" onClick="aprobarRequerimientoView.observarRequerimiento(' + row['id_doc_aprob'] + ');" ' + disabledBtn + '><i class="fas fa-exclamation-triangle fa-xs"></i></button>';
                        // let btnAnular = '<button type="button" class="btn btn-xs bg-maroon" title="Anular Requerimiento" onClick="aprobarRequerimientoView.anularRequerimiento(' + row['id_doc_aprob'] + ');" ' + disabledBtn + '><i class="fas fa-ban fa-xs"></i></button>';
                        return containerOpenBrackets + btnDetalleRapido + containerCloseBrackets;
                    }
                },
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.estado == 2) {
                    $(row.childNodes[9]).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row.childNodes[9]).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row.childNodes[9]).css('color', '#d92b60');
                }

            }
        });
        let tablelistaitem = document.getElementById(
            'ListaReqPendienteAprobacion_wrapper'
        )
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    }

    handleChangeFilterEmpresaListReqByEmpresa(event) {
        this.handleChangeFiltroListado();
        requerimientoCtrl.getSedesPorEmpresa(event.target.value).then(function (res) {
            aprobarRequerimientoView.construirSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirSelectSede(data) {
        let selectSede = document.querySelector('div[type="aprobar_requerimiento"] select[name="id_sede_select"]');
        let html = '<option value="0">Todas</option>';
        data.forEach(element => {
            html += '<option value="' + element.id_sede + '">' + element.codigo + '</option>'
        });

        selectSede.innerHTML = html;
        document.querySelector('div[type="aprobar_requerimiento"] select[name="id_sede_select"]').removeAttribute('disabled');

    }

    handleChangeFiltroListado() {
        this.mostrar(document.querySelector("select[name='id_empresa_select']").value, document.querySelector("select[name='id_sede_select']").value, document.querySelector("select[name='id_grupo_select']").value, document.querySelector("select[name='id_prioridad_select']").value);

    }

    editarRequerimiento(idRequerimiento) {
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url = "/logistica/gestion-logistica/requerimiento/elaboracion/index";
        var win = window.open(url, "_self");
        win.focus();
    }

    verDetalleRequerimientoSoloLectura(idRequerimiento) {
        $('#modal-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        document.querySelector("div[id='modal-requerimiento'] fieldset[id='group-acciones']").classList.add("oculto");
        document.querySelector("div[id='modal-requerimiento'] button[id='btnRegistrarRespuesta']").classList.add("oculto");

        requerimientoCtrl.getRequerimiento(idRequerimiento).then(function (res) {
            aprobarRequerimientoView.construirSeccionDatosGenerales(res['requerimiento'][0]);
            aprobarRequerimientoView.construirSeccionItemsDeRequerimiento(res['det_req']);
            aprobarRequerimientoView.construirSeccionHistorialAprobacion(res['historial_aprobacion']);
            $('#modal-requerimiento').LoadingOverlay("hide", true);

        }).catch(function (err) {
            console.log(err)
        })
    }

    verDetalleRequerimiento(idRequerimiento, idDocumento, idUsuario, idRolAprobante, idFlujo, aprobacionFinalOPendiente) {
        $('#modal-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        document.querySelector("div[id='modal-requerimiento'] fieldset[id='group-acciones']").classList.remove("oculto");
        document.querySelector("div[id='modal-requerimiento'] button[id='btnRegistrarRespuesta']").classList.remove("oculto");

        document.querySelector("div[id='modal-requerimiento'] textarea[id='comentario']").value = '';

        document.querySelector("div[id='modal-requerimiento'] input[name='idRequerimiento']").value = idRequerimiento;
        document.querySelector("div[id='modal-requerimiento'] input[name='idDocumento']").value = idDocumento;
        document.querySelector("div[id='modal-requerimiento'] input[name='idUsuario']").value = idUsuario;
        document.querySelector("div[id='modal-requerimiento'] input[name='idRolAprobante']").value = idRolAprobante;
        document.querySelector("div[id='modal-requerimiento'] input[name='idFlujo']").value = idFlujo;
        document.querySelector("div[id='modal-requerimiento'] input[name='aprobacionFinalOPendiente']").value = aprobacionFinalOPendiente;

        requerimientoCtrl.getRequerimiento(idRequerimiento).then(function (res) {
            aprobarRequerimientoView.construirSeccionDatosGenerales(res['requerimiento'][0]);
            aprobarRequerimientoView.construirSeccionItemsDeRequerimiento(res['det_req']);
            aprobarRequerimientoView.construirSeccionHistorialAprobacion(res['historial_aprobacion']);
            $('#modal-requerimiento').LoadingOverlay("hide", true);

        }).catch(function (err) {
            console.log(err)
        })
    }

    construirSeccionDatosGenerales(data) {
        // console.log(data);
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = data.codigo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = data.concepto;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = data.razon_social_empresa;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='division']").textContent = data.division;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent = data.tipo_requerimiento;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = data.prioridad;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='fecha_entrega']").textContent = data.fecha_entrega;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='solicitado_por']").textContent = (data.para_stock_almacen == true ? 'Para stock almacén' : (data.nombre_trabajador ? data.nombre_trabajador : '-'));
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = data.periodo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = data.persona;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='observacion']").textContent = data.observacion;
        document.querySelector("div[id='modal-requerimiento'] span[name='simboloMoneda']").textContent = data.simbolo_moneda;

        tempArchivoAdjuntoRequerimientoList = [];
        if (data.adjuntos.length > 0) {
            document.querySelector("td[id='adjuntosRequerimiento']").innerHTML = `<a title="Ver archivos adjuntos de requerimiento" style="cursor:pointer;" onClick="aprobarRequerimientoView.verAdjuntosRequerimiento();" >
            Ver (<span name="cantidadAdjuntosRequerimiento">${data.adjuntos.length}</span>)
            </a>`;
            (data.adjuntos).forEach(element => {
                tempArchivoAdjuntoRequerimientoList.push({
                    'id': element.id_adjunto,
                    'id_requerimiento': element.id_requerimiento,
                    'archivo': element.archivo,
                    'nameFile': element.archivo,
                    'categoria_adjunto_id': element.categoria_adjunto_id,
                    'categoria_adjunto': element.categoria_adjunto,
                    'fecha_registro': element.fecha_registro,
                    'estado': element.estado
                });

            });
        }

        let tamañoSelectAccion = document.querySelector("div[id='modal-requerimiento'] select[id='accion']").length;
        if (data.estado == 3) {
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value == 1) {
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].setAttribute('disabled', true)
                }
            }
        } else {
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value == 1) {
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].removeAttribute('disabled')
                }
            }
        }
    }

    verAdjuntosRequerimiento() {
        $('#modal-adjuntar-archivos-requerimiento').modal({
            show: true
        });

        requerimientoView.limpiarTabla('listaArchivosRequerimiento');
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');

        let html = '';
        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            tempArchivoAdjuntoRequerimientoList.forEach(element => {
                if (element.estado == 1) {
                    html += `<tr>
                    <td style="text-align:left;">${element.archivo}</td>
                    <td style="text-align:left;">${element.categoria_adjunto}</td>
                    <td style="text-align:center;">
                        <div class="btn-group" role="group">`;
                    html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoItem" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoRequerimiento('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
                    html += `</div>
                    </td>
                    </tr>`;

                }
            });
        }
        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html)

    }

    construirSeccionItemsDeRequerimiento(data) {

        requerimientoView.limpiarTabla('listaDetalleRequerimientoModal');
        tempArchivoAdjuntoItemList = [];
        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                let cantidadAdjuntosItem = 0;
                cantidadAdjuntosItem = data[i].adjuntos.length;
                if (cantidadAdjuntosItem > 0) {
                    (data[i].adjuntos).forEach(element => {
                        if (element.estado == 1) {
                            tempArchivoAdjuntoItemList.push(
                                {
                                    id: element.id_adjunto,
                                    idRegister: element.id_detalle_requerimiento,
                                    nameFile: element.archivo,
                                    dateFile: element.fecha_registro,
                                    estado: element.estado
                                }
                            );
                        }

                    });
                }
                html += `<tr>
                            <td>${i + 1}</td>
                            <td>${data[i].descripcion_partida ? data[i].descripcion_partida : ''}</td>
                            <td>${data[i].descripcion_centro_costo ? data[i].descripcion_centro_costo : ''}</td>
                            <td>${data[i].id_tipo_item == 1 ? (data[i].producto_part_number ? data[i].producto_part_number : data[i].part_number) : '(Servicio)'}</td>
                            <td>${data[i].producto_descripcion ? data[i].producto_descripcion : (data[i].descripcion ? data[i].descripcion : '')} </td>
                            <td>${data[i].unidad_medida}</td>
                            <td>${data[i].cantidad}</td>
                            <td>${Util.formatoNumero(data[i].precio_unitario, 2)}</td>
                            <td>${(data[i].subtotal ? Util.formatoNumero(data[i].subtotal, 2) : (Util.formatoNumero((data[i].cantidad*data[i].precio_unitario),2)))}</td>
                            <td>${data[i].motivo ? data[i].motivo : ''}</td>
                            <td style="text-align: center;"> 
                                ${cantidadAdjuntosItem>0?'<a title="Ver archivos adjuntos de item" style="cursor:pointer;" onClick="aprobarRequerimientoView.verAdjuntosItem('+data[i].id_detalle_requerimiento+')">Ver (<span name="cantidadAdjuntosItem">'+cantidadAdjuntosItem+'</span>)</a>':'-'}
                            </td>
                        </tr>`;
            }


        }
        document.querySelector("tbody[id='body_item_requerimiento']").insertAdjacentHTML('beforeend', html)
 

    }

    construirSeccionHistorialAprobacion(data) {
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

    verAdjuntosItem(idDetalleRequerimiento) {
        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        requerimientoView.limpiarTabla('listaArchivos');
        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');
        let html = '';
        tempArchivoAdjuntoItemList.forEach(element => {
            if (element.idRegister == idDetalleRequerimiento) {
                html += `<tr>
                <td style="text-align:left;">${element.nameFile}</td>
                <td style="text-align:center;">
                    <div class="btn-group" role="group">`;
                if (Number.isInteger(element.id)) {
                    html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoItem" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoItem('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
                }
                html += `</div>
                </td>
                </tr>`;
            }
        });
        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);


    }

    updateAccion(obj) {
        if (obj.value > 0) {
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.remove("has-error")
            if (obj.closest('div[class~="form-group"]').querySelector("span")) {
                obj.closest('div[class~="form-group"]').querySelector("span").remove();
            }
        } else {
            obj.closest('div[class~="form-group"]').classList.add("has-error")
            if (obj.closest('div[class~="form-group"]').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una acción)';
                obj.closest('div[class~="form-group"]').appendChild(newSpanInfo);
            }
        }
    }

    registrarRespuesta() {

        if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").value > 0) {
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.remove("has-error")
            if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span")) {
                document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span").remove();
            }

            let payload = {
                'accion': document.querySelector("div[id='modal-requerimiento'] select[id='accion']").value,
                'comentario': document.querySelector("div[id='modal-requerimiento'] textarea[id='comentario']").value,
                'idRequerimiento': document.querySelector("div[id='modal-requerimiento'] input[name='idRequerimiento']").value,
                'idDocumento': document.querySelector("div[id='modal-requerimiento'] input[name='idDocumento']").value,
                'idUsuario': document.querySelector("div[id='modal-requerimiento'] input[name='idUsuario']").value,
                'idRolAprobante': document.querySelector("div[id='modal-requerimiento'] input[name='idRolAprobante']").value,
                'idFlujo': document.querySelector("div[id='modal-requerimiento'] input[name='idFlujo']").value,
                'aprobacionFinalOPendiente': document.querySelector("div[id='modal-requerimiento'] input[name='aprobacionFinalOPendiente']").value
            };

            requerimientoCtrl.guardarRespuesta(payload).then(function (res) {
                if (res.id_aprobacion > 0) {
                    if(res.notificacion_por_emial==false){
                        alert(`Respuesta registrada con éxito.(NOTA: No se cuenta con información de email del usuario que corresponde notificar, ergo no se enviara ningun email). La página se recargara para actualizar el listado.`);
                    }else{
                        alert(`Respuesta registrada con éxito. La página se recargara para actualizar el listado.`);

                    }
                    $('#modal-requerimiento').modal('hide');
                    location.reload();


                } else {
                    alert(res.mensaje);
                    $('#modal-requerimiento').LoadingOverlay("hide", true);
                }

            }).catch(function (err) {
                console.log(err)
            });

        } else {
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.add("has-error")
            if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una acción)';
                document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').appendChild(newSpanInfo);
            }

        }
    }
}

const aprobarRequerimientoView = new AprobarRequerimientoView(); 
