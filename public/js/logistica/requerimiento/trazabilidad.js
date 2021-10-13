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
                        <p><a href="/necesidades/requerimiento/elaboracion/index?id=${data.requerimiento.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${data.requerimiento.codigo}</a></p>
                        <strong>Estado: </strong>
                         <p>${data.requerimiento.estado_descripcion}</p>
                    </div>
                </div>
            </li>`);

        }
        let htmlGestionLogistica='';
        let OrdenesCodigo=[];
        if(data.ordenes.length >0){

            htmlGestionLogistica = `<li class="timeline-item">
            <div class="timeline-badge info"><i class="glyphicon glyphicon-check"></i></div>
            <div class="timeline-panel border-info">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Gestion Logística</h5>
                </div>`;
                (data.ordenes).forEach(element => {
                    OrdenesCodigo.push(`<a href="/necesidades/requerimiento/elaboracion/index?id=${element.id_orden_compra}" target="_blank" title="Abrir Requerimiento">${element.codigo}</a>`)
                });

            htmlGestionLogistica += `
                <div class="timeline-body">
                <strong>Ordenes C/S:</strong>
                <p>${OrdenesCodigo.join(',')}</p>
                <strong>Reservas almacén:</strong>
                <p>${data.reservado ==true?'Si':'No'} </p>

                </div>
            </div>
        </li>`;
            document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlGestionLogistica);

        }
    }

    let htmlIngresosAlmacen='';
    let ingresosCodigo=[];
    let ingresosGC=[];
    let ingresosFC=[];
    if(data.ingresos.length >0){

        htmlIngresosAlmacen = `<li class="timeline-item">
        <div class="timeline-badge success"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-success">
            <div class="timeline-heading">
                <h5 class="timeline-title">Ingresos Almacén</h5>
            </div>`;
            (data.ingresos).forEach(element => {
                // ingresosCodigo.push(`<a onclick="abrirIngreso(${element.id_ingreso})" title="Abrir Ingreso">${element.codigo_ingreso}</a>`)
                ingresosGC.push(`${element.serie_guia}-${element.numero_guia}`)
                ingresosFC.push(`${element.serie_doc}-${element.numero_doc}`)
            });

        htmlIngresosAlmacen += `
            <div class="timeline-body">
            <strong>Código ingreso: </strong>
            <strong>Guia compra: </strong>
            <p>${ingresosGC.join(',')}</p>
            <strong>Factura compra: </strong>
            <p>${ingresosFC.join(',')}</p>
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlIngresosAlmacen);

    }
    let transferenciaGC=[];
    let transferenciaGV=[];
    if(data.transferencias.length >0){

            (data.transferencias).forEach(ingreso => {
                transferenciaGC.push(`${ingreso.serie_guia_com}-${ingreso.numero_guia_com}`)
                transferenciaGV.push(`${ingreso.serie_guia_ven}-${ingreso.numero_guia_ven}`)
            });

        htmlTransferencias += `
            <div class="timeline-body">
            <p>Guia compra: ${transferenciaGC.join(',')}</p>
            <p>Guia venta: ${transferenciaGV.join(',')}</p>
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlTransferencias);
    }

    let htmlTransformaciones='';
    let transformacionCodigo=[];
    if(data.transformaciones.length >0){

        htmlTransformaciones = `<li class="timeline-item">
        <div class="timeline-badge success"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-success">
            <div class="timeline-heading">
                <h5 class="timeline-title">Transformaciones</h5>
            </div>`;
            (data.transformaciones).forEach(element => {
                transformacionCodigo.push(`${element.codigo}`);
            });

        htmlTransformaciones += `
            <div class="timeline-body">
            <p>Codigo tranformación: ${transformacionCodigo.join(',')}</p>
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlTransformaciones);
    }



    let htmlDespacho = '';
    if (data.despacho != null) {

        htmlDespacho = `<li class="timeline-item">
        <div class="timeline-badge warning"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-warning">
            <div class="timeline-heading">
                <h5 class="timeline-title">Despacho</h5>
                <p><small class="text-muted"><i class="glyphicon glyphicon-calendar"></i> ${data.despacho.fecha_despacho}</small></p>
            </div> 
            <div class="timeline-body">
            <p>Codigo: ${data.despacho.codigo} </p> 
            </div>
        </div>
    </li>`;
        document.querySelector("ul[id='stepperTrazabilidad']").insertAdjacentHTML('beforeend', htmlDespacho);

    }

    let htmlReparto='';
    let repartoAccion=[];
    if(data.estados_envio.length >0){

        htmlReparto = `<li class="timeline-item">
        <div class="timeline-badge primary"><i class="glyphicon glyphicon-check"></i></div>
        <div class="timeline-panel border-primary">
            <div class="timeline-heading">
                <h5 class="timeline-title">Reparto</h5>
                <p><small class="text-muted"><i class="glyphicon glyphicon-calendar"></i> </small></p>
            </div>`;
            (data.estados_envio).forEach(element => {
                repartoAccion.push(`${element.accion_descripcion}`);
            });

        htmlReparto+=`
            <div class="timeline-body">
            <p>Acciónes Reparto: ${repartoAccion.join(',')}</p>
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


function abrirIngreso(idIngreso){
    if (idIngreso !== "") {
        var id = encode5t(idIngreso);
        window.open("imprimir_ingreso/" + id);
    }
}