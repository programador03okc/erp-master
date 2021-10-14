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
                        <p><a href="/necesidades/requerimiento/elaboracion/imprimir-requerimiento-pdf/${data.requerimiento.id_requerimiento}/0" target="_blank" title="Abrir requerimiento">${data.requerimiento.codigo}</a></p>
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
                OrdenesCodigo.push(`<a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${element.id_orden_compra}" target="_blank" title="Abrir orden">${element.codigo}</a>`)
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
                ingresosCodigo.push(`<a href onclick="abrirIngresoPDF(${element.id_ingreso})" title="Abrir ingreso">${element.codigo_ingreso ?? ''}</a>`)
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
                transferenciaCodigo.push(`<a href onclick="abrirTransferenciaPDF(${element.id_transferencia})" title="Abrir transferencia">${element.codigo}</a>`)
            }
            if (element.numero_guia_com != null) {
                transferenciaGC.push(`${element.serie_guia_com ?? ''}-${element.numero_guia_com ?? ''}`)
            }
            if (element.numero_guia_com != null) {
                transferenciaGV.push(`${element.serie_guia_ven ?? ''}-${element.numero_guia_ven ?? ''}`)
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
            transformacionCodigo.push(`${element.codigo}`);
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
            <p>${data.despacho.serie ? (data.despacho.serie + '-' + data.despacho.numero) : ''}</p> 
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


function abrirIngresoPDF(idIngreso) {
    if (idIngreso !== "") {
        var id = encode5t(idIngreso);
        window.open("imprimir_ingreso/" + id);
    }
}

function abrirTransferenciaPDF(idTransferencia) {
    if (idTransferencia !== "") {
        window.open("imprimir_transferencia/" + idTransferencia);
    }
}