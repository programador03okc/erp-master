function getTrazabilidad(idRequerimiento) {

    return new Promise(function (resolve, reject) {
        if (idRequerimiento > 0) {
            $.ajax({
                type: 'GET',
                url: `mostrarDocumentosByRequerimiento/` + idRequerimiento,
                dataType: 'JSON',
                success(response) {
                    resolve(response) // Resolve promise and go to then() 
                },
                error: function (err) {
                    Swal.fire(
                        '',
                        'Hubo un problema al intentar obtener la trazabilidad, por favor vuelva a intentarlo',
                        'erro'
                    );

                    reject(err); // Reject the promise and go to catch()
                }
            });
        } else {
            resolve(false);
        }
    });
}

function construirModalTrazabilidad(data) {
    document.querySelector("ul[id='stepperTrazabilidad']").innerHTML = '';

    if (data.hasOwnProperty('requerimiento')) {
        if (data.requerimiento.codigo != undefined) {
            document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', `
            
            <li class="timeline-item">
                <div class="timeline-badge danger"><i class="glyphicon glyphicon-check"></i></div>
                <div class="timeline-panel border-danger">
                    <div class="timeline-heading">
                        <h5 class="timeline-title">Requerimiento</h5>
                        <p><small class="text-muted"><i class="glyphicon glyphicon-calendar"></i> ${data.requerimiento.fecha_requerimiento}</small></p>
                    </div>
                    <div class="timeline-body">
                        <strong>Código: </strong>
                        <p><a href="/necesidades/requerimiento/elaboracion/imprimir-requerimiento-pdf/${data.requerimiento.id_requerimiento}/0" target="_blank" title="Abrir Requerimiento">${data.requerimiento.codigo}</a></p>
                    </div>
                </div>
            </li>`);

        }
        let htmlGestionLogistica = '';
        let OrdenesCodigo = [];
        if (data.ordenes.length > 0) {

            htmlGestionLogistica = `<li class="timeline-item">
            <div class="timeline-badge info"><i class="glyphicon glyphicon-check"></i></div>
            <div class="timeline-panel border-info">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Gestion Logística</h5>
                </div>`;
            (data.ordenes).forEach(element => {
                OrdenesCodigo.push(`<a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${element.id_orden_compra}" target="_blank" title="Abrir Orden">${element.codigo}</a> <span>(${element.estado_descripcion})</span>`)
            });

            htmlGestionLogistica += `
                <div class="timeline-body">
                <strong>Ordenes C/S:</strong>
                <p>${OrdenesCodigo.join('<br>')}</p>
                <strong>Reservas almacén:</strong>
                <p>${data.reservado == true ? 'Si' : 'No'} </p>

                </div>
            </div>
        </li>`;
            document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlGestionLogistica);

        }

        let htmlTesoreriaPago = '';
        let adjuntoPagoList = [];
        if (data.pagos.length > 0) {

            htmlTesoreriaPago = `<li class="timeline-item">
            <div class="timeline-badge success"><i class="glyphicon glyphicon-check"></i></div>
            <div class="timeline-panel border-success">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Atención Tesorería</h5>
                </div>`;
            (data.pagos).forEach(element => {
                if((element.adjunto).hasOwnProperty('adjunto')){
                    adjuntoPagoList.push(`<a href="/files/tesoreria/pagos/${element.adjunto.adjunto}" target="_blank" title="Abrir Orden">${element.adjunto.adjunto}</a> <span>(${element.observacion})</span>`)
                }
            });

            htmlTesoreriaPago += `
                <div class="timeline-body">
                <strong>Pagos:</strong>
                <p>${adjuntoPagoList.join('<br>')}</p>

                </div>
            </div>
        </li>`;
            document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlTesoreriaPago);

        }
    }

    let htmlIngresosAlmacen = '';
    let ingresosCodigo = [];
    let ingresosGC = [];
    let ingresosFC = [];
    if (data.ingresos.length > 0) {

        htmlIngresosAlmacen = `<li class="timeline-item">
        <div class="timeline-badge success"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-success">
            <div class="timeline-heading">
                <h5 class="timeline-title">Ingresos Almacén</h5>
            </div>`;
        (data.ingresos).forEach(element => {
            if (element.id_ingreso > 0) {
                ingresosCodigo.push(`<a href="imprimir_ingreso/${(element.id_ingreso)}" target="_blank" title="Abrir Ingreso">${element.codigo_ingreso ?? ''}</a>`)
            }
            if (element.numero_guia != null) {
                ingresosGC.push(`${element.serie_guia ?? ''}-${element.numero_guia ?? ''}`)
            }
        });
        (data.docs).forEach(element => {
            if (element.numero_doc != null) {
                ingresosFC.push(`${element.serie_doc ?? ''}-${element.numero_doc ?? ''}`)
            }
        });

        htmlIngresosAlmacen += `
            <div class="timeline-body">
            <strong>Código: </strong>
            <p>${ingresosCodigo.join('<br>')}</p>
            <strong>Guia compra: </strong>
            <p>${ingresosGC.join('<br>')}</p>
            <strong>Factura compra: </strong>
            <p>${ingresosFC.join('<br>')}</p>
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlIngresosAlmacen);

    }
    let transferenciaCodigo = [];
    let transferenciaGC = [];
    let transferenciaGV = [];
    let htmlTransferencias = '';
    console.log('trans: ' + data.transferencias.length);
    if (data.transferencias.length > 0) {

        htmlTransferencias += `<li class="timeline-item">
        <div class="timeline-badge default"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-default">
            <div class="timeline-heading">
                <h5 class="timeline-title">Transferencias</h5>
            </div>`;
        (data.transferencias).forEach(element => {
            if (element.id_transferencia !== null) {
                transferenciaCodigo.push(`<a href="imprimir_transferencia/${element.id_transferencia}" target="_blank" title="Abrir Transferencia">${element.codigo}</a>`)
            }
            if (element.numero_guia_com != null) {
                transferenciaGC.push(`<a href="imprimir_ingreso/${element.id_ingreso}"  target="_blank" title="Abrir Ingreso">${element.serie_guia_com ?? ''}-${element.numero_guia_com ?? ''}</a>`)
            }
            if (element.numero_guia_ven != null) {
                var idSalidaEncode = encode5t(element.id_salida);
                transferenciaGV.push(`<a href="imprimir_salida/${idSalidaEncode}" target="_blank" title="Abrir Salida">${element.serie_guia_ven ?? ''}-${element.numero_guia_ven ?? ''}</a>`)
            }
        });

        htmlTransferencias += `
            <div class="timeline-body">
            <strong>Código:</strong>
            <p> ${transferenciaCodigo.join('<br>')}</p>
            <strong>Guia compra:</strong>
            <p>${transferenciaGC.join('<br>')}</p>
            <strong>Guia venta:</strong>
            <p>${transferenciaGV.join('<br>')}</p>
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlTransferencias);
    }

    let htmlTransformaciones = '';
    let transformacionCodigo = [];
    if (data.transformaciones.length > 0) {

        htmlTransformaciones += `<li class="timeline-item">
        <div class="timeline-badge warning"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-warning">
            <div class="timeline-heading">
                <h5 class="timeline-title">Transformaciones</h5>
            </div>`;
        (data.transformaciones).forEach(element => {
            transformacionCodigo.push(`<a href="imprimir_transformacion/${(element.id_transformacion)}" target="_blank" title="Abrir Salida">${element.codigo}</a>`);
            transformacionCodigo.push(`${element.serie} - ${element.numero}`);
        });

        htmlTransformaciones += `
            <div class="timeline-body">
            <strong>Codigo tranformación:</strong>
            <p>${transformacionCodigo.join('<br>')}</p>
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlTransformaciones);
    }



    let htmlDespacho = '';
    if (data.despacho != null) {
        var idSalidaEncode = encode5t(data.despacho.id_salida);

        htmlDespacho += `<li class="timeline-item">
        <div class="timeline-badge purple"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-purple">
            <div class="timeline-heading">
                <h5 class="timeline-title">Despacho</h5>
                <p><small class="text-muted"><i class="glyphicon glyphicon-calendar"></i> ${data.despacho.fecha_despacho ?? ''}</small></p>
            </div> 
            <div class="timeline-body">
            <strong>Codigo:</strong>
            <p>${data.despacho.codigo ?? ''}</p> 
            <strong>Guía venta:</strong>
            <p><a href='imprimir_salida/${idSalidaEncode}' target="_blank" title="Abrir Salida">${data.despacho.serie ? (data.despacho.serie + '-' + data.despacho.numero) : ''}</a></p> 
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlDespacho);

    }

    let htmlReparto = '';
    let repartoAccion = [];
    if (data.estados_envio.length > 0) {

        htmlReparto += `<li class="timeline-item">
        <div class="timeline-badge primary"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-primary">
            <div class="timeline-heading">
                <h5 class="timeline-title">Reparto</h5>
            </div>`;
        (data.estados_envio).forEach(element => {
            repartoAccion.push(`${element.accion_descripcion ?? ''}`);
        });

        htmlReparto += `
            <div class="timeline-body">
            <strong>Guía transportista:</strong>
            <p>${data.guia_transportista.serie ? ((data.guia_transportista.serie ?? '') + '-' + (data.guia_transportista.numero ?? '')) : ''}<br>
            ${data.guia_transportista.fecha_transportista ? data.guia_transportista.fecha_transportista : ''}<br>
            ${data.guia_transportista.codigo_envio ? 'Cód. envio: ' + data.guia_transportista.codigo_envio : ''} ${data.guia_transportista.importe_flete ? 'S/' + data.guia_transportista.importe_flete : ''}</p>
            <strong>Acciónes Reparto:</strong>
            <p>${repartoAccion.join('<br>')}</p>
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlReparto);

    }

}


function mostrarTrazabilidad(idRequerimiento) {
    $('#modal-trazabilidad').modal({
        show: true
    });

    getTrazabilidad(idRequerimiento).then((res) => {
        construirModalTrazabilidad(res);
    }).catch(function (err) {
        console.log(err)
    })
}
