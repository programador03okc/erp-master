function limpiarVistaRapidaRequerimientoPago() {
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_estado']").value = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_usuario']").value = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='grupo_division']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='fecha_registro']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_destinatario']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='destinatario']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='banco']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_cuenta']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='moneda']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cuenta']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cci']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='comentario']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_impuesto']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] span[name='simboloMoneda']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] span[name='simbolo_moneda']").textContent = '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] label[name='total']").textContent = '';
    document.querySelector("td[id='adjuntosRequerimientoPago']").innerHTML = '';
    limpiarTabla('listaDetalleRequerimientoPago');
    limpiarTabla('listaHistorialRevision');

}

function cargarDataRequerimientoPago(idRequerimientoPago) {
    if (idRequerimientoPago > 0) {
        $('#modal-vista-rapida-requerimiento-pago .modal-content').LoadingOverlay("show", {
            imageAutoResize: true,
            progress: true,
            imageColor: "#3c8dbc"
        });
        obtenerRequerimientoPago(idRequerimientoPago).then((res) => {
            $('#modal-vista-rapida-requerimiento-pago .modal-content').LoadingOverlay("hide", true);

            mostrarDataEnVistaRapidaRequerimientoPago(res)

        }).catch(function (err) {
            $('#modal-vista-rapida-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
            console.log(err)
            Swal.fire(
                '',
                'Hubo un error al tratar de obtener la data',
                'error'
            );
        });
    } else {
        Swal.fire(
            '',
            'Lo sentimos no se encontro un ID valido para cargar el requerimiento de pago seleccionado, por favor vuelva a intentarlo',
            'error'
        );
    }
}

function obtenerRequerimientoPago(id) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `mostrar-requerimiento-pago/${id}`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function (err) {
                reject(err)
            }
        });
    });
}

function mostrarDataEnVistaRapidaRequerimientoPago(data) {
    console.log(data);
    // ### ==================== cabecera ====================== ###
    var destinatario,tipo_documento_destinatario,nro_documento_destinatario, banco, tipo_cuenta, tipo_cuenta, moneda, nro_cuenta, nro_cci = '';
    if (data.id_tipo_destinatario == 1 || data.id_persona > 0) {
        destinatario = data.persona != null ? ((data.persona.nombres).concat(' ', data.persona.apellido_paterno).concat(' ', data.persona.apellido_materno)) : '';
        tipo_documento_destinatario = data.persona != null ? (data.persona.tipo_documento_identidad !=null?data.persona.tipo_documento_identidad.descripcion:''): '';
        nro_documento_destinatario = data.persona != null ? data.persona.nro_documento : '';
        banco = data.cuenta_persona != null ? (data.cuenta_persona.banco != null && data.cuenta_persona.banco.contribuyente != null ? data.cuenta_persona.banco.contribuyente.razon_social : '') : '';
        tipo_cuenta = data.cuenta_persona != null ? (data.cuenta_persona.tipo_cuenta != null ? data.cuenta_persona.tipo_cuenta.descripcion : '') : '';
        moneda = data.cuenta_persona != null ? (data.cuenta_persona.moneda != null ? data.cuenta_persona.moneda.descripcion : '') : '';
        nro_cuenta = data.cuenta_persona != null ? data.cuenta_persona.nro_cuenta : '';
        nro_cci = data.cuenta_persona != null ? data.cuenta_persona.nro_cci : '';
    } else if (data.id_tipo_destinatario == 2 || data.id_contribuyente > 0) {
        destinatario = data.contribuyente != null ? data.contribuyente.razon_social : '';
        tipo_documento_destinatario = data.contribuyente != null ? (data.contribuyente.tipo_documento_identidad !=null?data.contribuyente.tipo_documento_identidad.descripcion:''): '';
        nro_documento_destinatario = data.contribuyente != null ? data.contribuyente.nro_documento : '';
        banco = data.cuenta_contribuyente != null ? (data.cuenta_contribuyente.banco != null && data.cuenta_contribuyente.banco.contribuyente != null ? data.cuenta_contribuyente.banco.contribuyente.razon_social : '') : '';
        tipo_cuenta = data.cuenta_contribuyente != null ? (data.cuenta_contribuyente.tipo_cuenta != null ? data.cuenta_contribuyente.tipo_cuenta.descripcion : '') : '';
        moneda = data.cuenta_contribuyente != null ? (data.cuenta_contribuyente.moneda != null ? data.cuenta_contribuyente.moneda.descripcion : '') : '';;
        nro_cuenta = data.cuenta_contribuyente != null ? data.cuenta_contribuyente.nro_cuenta : '';
        nro_cci = data.cuenta_contribuyente != null ? data.cuenta_contribuyente.nro_cuenta_interbancaria : '';
    }
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value = data.id_requerimiento_pago;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_estado']").value = data.id_estado;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_usuario']").value = data.id_usuario;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = data.codigo;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = data.concepto;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = data.sede != null ? data.sede.descripcion : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='grupo_division']").textContent = (data.grupo != null && data.grupo.descripcion != undefined ? data.grupo.descripcion : '') + (data.grupo != null && data.division != null ? '/' : '') + (data.division != null && data.division.descripcion != undefined ? data.division.descripcion : '');
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent = data.tipo_requerimiento_pago != null && data.tipo_requerimiento_pago.descripcion != undefined ? data.tipo_requerimiento_pago.descripcion : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = data.prioridad != null && data.prioridad.descripcion != undefined ? data.prioridad.descripcion : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='fecha_registro']").textContent = data.fecha_registro;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = data.creado_por != null && data.creado_por.nombre_corto != undefined ? data.creado_por.nombre_corto : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = data.periodo != null && data.periodo.descripcion != undefined ? data.periodo.descripcion : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='comentario']").textContent = data.comentario;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_impuesto']").textContent = data.tipo_impuesto==1?'Detracción':data.tipo_impuesto ==2?'Renta':'No aplica';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_destinatario']").textContent = data.tipo_destinatario != null ? data.tipo_destinatario.descripcion : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='destinatario']").textContent = destinatario;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_documento_destinatario']").textContent = tipo_documento_destinatario;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_documento_destinatario']").textContent = nro_documento_destinatario;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='banco']").textContent = banco;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_cuenta']").textContent = tipo_cuenta;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='moneda']").textContent = moneda;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cuenta']").textContent = nro_cuenta;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cci']").textContent = nro_cci;
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] span[name='simboloMoneda']").textContent = data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] span[name='simbolo_moneda']").textContent = data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : '';
    document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] label[name='total']").textContent = $.number(data.monto_total, 2);


    if (data.id_presupuesto_interno > 0) {
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='presupuesto_interno']").textContent = (data.presupuesto_interno !=null ?data.presupuesto_interno.codigo: '')+' - '+(data.presupuesto_interno !=null ? data.presupuesto_interno.descripcion: '');
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_interno']").classList.remove("oculto");
    } else {
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_interno']").classList.add("oculto");

    }

    if(data.id_cc>0){
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='codigo_cdp']").textContent = data.cuadro_presupuesto != null ? data.cuadro_presupuesto.codigo_oportunidad:'';;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_cdp']").classList.remove("oculto");
    }else{
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_cdp']").classList.add("oculto");

    }
    if(data.id_proyecto>0){
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='proyecto_presupuesto']").textContent = data.proyecto.descripcion??'';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_proyecto']").classList.remove("oculto");
    }else{
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_proyecto']").classList.add("oculto");

    }


    if (data.adjunto.length > 0) {
        document.querySelector("td[id='adjuntosRequerimientoPago']").innerHTML = `<a title="Ver archivos adjuntos de requerimiento pago" style="cursor:pointer;" data-tipo-modal="lectura" class="handleClickAdjuntarArchivoCabecera"  data-id-requerimiento-pago="">
        Ver (<span>${(data.adjunto).filter((element, i) => element.id_estado != 7).length}</span>)
        </a>`;
    }


    // ### ==================== Detalle ====================== ###

    limpiarTabla('listaDetalleRequerimientoPago');
    if (data.detalle.length > 0) {
        for (let i = 0; i < data.detalle.length; i++) {
            let cantidadAdjuntosItem = 0;
            cantidadAdjuntosItem = (data.detalle[i].adjunto).filter((element, i) => element.id_estado != 7).length;
            console.log(cantidadAdjuntosItem);
            // cantidadAdjuntosItem = data.detalle[i].adjunto.length;

            document.querySelector("tbody[id='body_requerimiento_pago_detalle']").insertAdjacentHTML('beforeend', `<tr style="background-color:${data.detalle[i].id_estado == '7' ? '#f1d7d7' : ''}">
            <td>${i + 1}</td>
            <td>${data.detalle[i].partida ? data.detalle[i].partida.codigo : ''} ${data.detalle[i].presupuesto_interno_detalle ?data.detalle[i].presupuesto_interno_detalle.partida:''}</td>
            <td>${data.detalle[i].centro_costo ? data.detalle[i].centro_costo.codigo : ''}</td>
            <td name="descripcion_servicio">${data.detalle[i].descripcion != null ? data.detalle[i].descripcion : ''} </td>
            <td>${data.detalle[i].unidad_medida != null ? data.detalle[i].unidad_medida.descripcion : ''}</td>
            <td style="text-align:center;">${data.detalle[i].cantidad >= 0 ? data.detalle[i].cantidad : ''}</td>
            <td style="text-align:right;">${data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : ''}${formatNumber.decimal(data.detalle[i].precio_unitario, '', -2)}</td>
            <td style="text-align:right;">${data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : ''}${(data.detalle[i].subtotal ? formatNumber.decimal(data.detalle[i].subtotal, '', -2) : (formatNumber.decimal((data.detalle[i].cantidad * data.detalle[i].precio_unitario), '', -2)))}</td>
            <td style="text-align:center;">${data.detalle[i].estado != null ? data.detalle[i].estado.estado_doc : ''}</td>
            <td style="text-align: center;"> 
            ${cantidadAdjuntosItem > 0 ? '<a title="Ver archivos adjuntos de item" style="cursor:pointer;" class="handleClickAdjuntarArchivoDetalle" data-tipo-modal="lectura" data-id="' + data.detalle[i].id_requerimiento_pago_detalle + '" >Ver (<span>' + cantidadAdjuntosItem + '</span>)</a>' : '-'}
            </td>
            </tr>`);



        }
        // ### ==================== Detalle ====================== ###

        // ### ==================== Historia aprobación ====================== ###
        limpiarTabla('listaHistorialRevision');
        data.aprobacion.forEach(data => {
            // this.agregarHistorialAprobacion(element);

            document.querySelector("tbody[id='body_requerimiento_pago_historial_revision']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td>${data.usuario != null ? data.usuario.nombre_corto : ''}</td>
                <td>${data.vo_bo != null ? data.vo_bo.descripcion : ''}</td>
                <td>${data.detalle_observacion != null ? data.detalle_observacion : ''}</td>
                <td>${data.fecha_vobo != null ? data.fecha_vobo : ''}</td>
                </tr>`);

        });
        // ### ==================== Historia aprobación ====================== ###

    }

}
function limpiarTabla(idElement) {
    let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
    if (nodeTbody != null) {
        while (nodeTbody.children.length > 0) {
            nodeTbody.removeChild(nodeTbody.lastChild);
        }

    }
}

function modalAdjuntarArchivosCabecera(obj) { // TODO pasar al btn el id y no usar de un input para ambos casos de mostrar solo lectura y mostrar con carga
    $('#modal-adjuntar-archivos-requerimiento-pago').modal({
        show: true
    });
    // this.limpiarTabla('listaArchivosRequerimientoPagoCabecera');

    let idRequerimientoPago = null;
    if (obj.dataset.tipoModal == "lectura") {
        idRequerimientoPago = document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']") != null ? document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value : null;
        listarArchivosAdjuntosCabecera(idRequerimientoPago, obj.dataset.tipoModal);
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] div[id='group-action-upload-file']").classList.add("oculto");
    } else {
        idRequerimientoPago = document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']") != null ? document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value : null
        listarArchivosAdjuntosCabecera(idRequerimientoPago);

        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] div[id='group-action-upload-file']").classList.remove("oculto");
    }
}

function listarArchivosAdjuntosCabecera(idRequerimientoPago, tipoModal = null) {
    // let idRequerimientoPago = document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value.length > 0 ? document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value : document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value;
    if (idRequerimientoPago.length > 0) {

        var regExp = /[a-zA-Z]/g; //expresión regular

        getcategoriaAdjunto().then((categoriaAdjuntoList) => {

            construirTablaAdjuntosRequerimientoPagoCabecera(tempArchivoAdjuntoRequerimientoPagoCabeceraList, categoriaAdjuntoList, tipoModal);
        });
    }
}

function getcategoriaAdjunto() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-categoria-adjunto`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function (err) {
                reject(err)
            }
        });
    });
}

function construirTablaAdjuntosRequerimientoPagoCabecera(adjuntoList, categoriaAdjuntoList, tipoModal) {
    // console.log(adjuntoList,categoriaAdjuntoList);
    limpiarTabla('listaArchivosRequerimientoPagoCabecera');

    let html = '';
    let hasHiddenBtnEliminarArchivo = '';
    let hasDisabledSelectTipoArchivo = '';
    let estadoActual = document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_estado']").value;

    if (estadoActual == 1 || estadoActual == 3 || estadoActual == '') {
        if (document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_usuario']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
            hasHiddenBtnEliminarArchivo = '';
        } else {
            hasHiddenBtnEliminarArchivo = 'oculto';
            hasDisabledSelectTipoArchivo = 'disabled';
        }
    }

    adjuntoList.forEach(element => {
        html += `<tr id="${element.id}" style="text-align:center">
    <td style="text-align:left;">${element.nameFile}</td>
    <td>
        <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto" ${hasDisabledSelectTipoArchivo}>
    `;
        categoriaAdjuntoList.forEach(categoria => {
            if (element.category == categoria.id_requerimiento_pago_categoria_adjunto) {
                html += `<option value="${categoria.id_requerimiento_pago_categoria_adjunto}" selected >${categoria.descripcion}</option>`

            } else {
                html += `<option value="${categoria.id_requerimiento_pago_categoria_adjunto}">${categoria.descripcion}</option>`
            }
        });
        html += `</select>
    </td>
    <td style="text-align:center;">
        <div class="btn-group" role="group">`;
        if (Number.isInteger(element.id)) {
            html += `<button type="button" class="btn btn-info btn-xs handleClickDescargarArchivoCabeceraRequerimientoPago" name="btnDescargarArchivoCabeceraRequerimientoPago" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
        }
        if (tipoModal != 'lectura') {
            html += `<button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimientoPago ${hasHiddenBtnEliminarArchivo}" name="btnEliminarArchivoRequerimientoPago" title="Eliminar" data-id="${element.id}" ><i class="fas fa-trash-alt"></i></button>`;
        }
        html += `</div>
    </td>
    </tr>`;
    });
    document.querySelector("tbody[id='body_archivos_requerimiento_pago_cabecera']").insertAdjacentHTML('beforeend', html);

}