class TrazabilidadRequerimiento{
    constructor(requerimientoCtrl) {
        this.requerimientoCtrl = requerimientoCtrl;
        this.initializeEventHandler();

    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if(nodeTbody!=null){
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }

        }
    }

    verTrazabilidadRequerimientoModal(data,that){
        let idRequerimiento = data.id_requerimiento;

        $('#modal-trazabilidad').modal({
            show: true
        });
        // $('#modal-trazabilidad-requerimiento').modal({
        //     show: true
        // });
        this.mostrarRequerimiento(idRequerimiento);
        this.mostrarHistorialAprobacion(idRequerimiento);
        this.mostrarTrazabilidadDetalleRequerimiento(idRequerimiento);
    }
    initializeEventHandler(){

        $('#listaTrazabilidadDetalleRequerimiento tbody').on("click","label.handleClickAbrirOrden", (e)=>{
            console.log(e.currentTarget.dataset.idOrden);
            this.abrirOrden(e.currentTarget.dataset.idOrden);
        });
        // $('#listaTrazabilidadDetalleRequerimiento tbody').on("click","label.handleClickAbrirIngreso", (e)=>{
        //     this.abrirIngreso(e.currentTarget.dataset.idGuia);
        // });
    }

    mostrarRequerimiento(idRequerimiento){
        this.requerimientoCtrl.getCabeceraRequerimiento(idRequerimiento).then( (res)=> {
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='codigo_requerimiento']").textContent= res.codigo;
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='requerimiento_creado_por']").textContent= res.nombre_completo_usuario;
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='fecha_registro_requerimiento']").textContent= res.fecha_registro;
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='estado_actual_requerimiento']").textContent= res.nombre_estado;
        }).catch(function (err) {
            console.log(err)
        })
    }

    mostrarHistorialAprobacion(idRequerimiento){
        this.requerimientoCtrl.getHistorialAprobacion(idRequerimiento).then((res) =>{
            let html ='';
            if(res.length >0){
                res.forEach(element => {
                html +=`
                <div class="stepper-item completed">
                    <div class="step-counter" tabindex="0" data-container="body" data-toggle="popover" data-trigger="focus"  data-html="true" data-placement="bottom" data-content="
                    <dl>
                        <dt>Usuario</dt>
                        <dd>${element.nombre_usuario}</dd>
                        <dt>Comentario/Observación</dt>
                        <dd>${element.detalle_observacion}</dd>
                        <dt>Fecha registro</dt>
                        <dd>${element.fecha_vobo}</dd>
                    </dl>
                " style="cursor:pointer;">
 
                    </div>
                    <div class="step-name">${element.accion}</div>
                </div>
                `;       
                });

                document.querySelector("div[class='stepper-wrapper']").innerHTML=html;
                $(function () {
                    $('[data-toggle="popover"]').popover()
                  })
            }else{
                html +=`
                <div class="stepper-item ">
                    <div class="step-counter" tabindex="0" data-container="body" data-toggle="popover" data-trigger="focus"  data-html="true" data-placement="bottom">
 
                    </div>
                    <div class="step-name">Sin historial de aprobación</div>
                </div>
                `;       
                
                document.querySelector("div[class='stepper-wrapper']").innerHTML=html;
            }

        }).catch(function (err) {
            console.log(err)
        })
    }

    mostrarTrazabilidadDetalleRequerimiento(idRequerimiento){
        this.requerimientoCtrl.getTrazabilidadDetalleRequerimiento(idRequerimiento).then( (res)=> {
            this.construirTablaTrazabilidadDetalleRequerimiento(res);
        }).catch(function (err) {
            console.log(err)
        })
    }
    
    construirTablaTrazabilidadDetalleRequerimiento(data){

        this.limpiarTabla('listaTrazabilidadDetalleRequerimiento');
        data.forEach(element => { 
            let labelOrdenes='';
            (element.ordenes_compra).forEach(value => {
                labelOrdenes += `<label class="lbl-codigo handleClickAbrirOrden" title="Abrir orden" data-id-orden="${value.id_orden_compra}" >${value.codigo}</label>`;
            });

            let labelGuiaIngreso='';
            (element.guias_ingreso).forEach(item => {
                labelGuiaIngreso += `<label class="" title="Guia Ingreso" data-id-guia="${item.id_guia}">${item.codigo_guia}</label>`;
            });

            let labelFacturas='';
            (element.facturas).forEach(item => {
                labelFacturas += `<label>${item.codigo_factura}</label>`;
            });

            document.querySelector("tbody[id='body_lista_trazabilidad_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td style="text-align:center;">${element.codigo_producto??''}</td>
                <td style="text-align:center;">${(element.id_tipo_item ==1)?(element.part_number_producto?element.part_number_producto:(element.part_number?element.part_number:'')):'servicio'}</td>
                <td style="text-align:left;">${element.descripcion_producto?element.descripcion_producto:(element.descripcion?element.descripcion:'')}</td>
                <td style="text-align:center;">${element.cantidad??''}</td>
                <td style="text-align:center;">${element.unidad_medida??''}</td>
                <td style="text-align:center;">${labelOrdenes}</td>
                <td style="text-align:center;">${labelGuiaIngreso}</td>
                <td style="text-align:center;">${labelFacturas}</td>
                <td style="text-align:center;">${element.nombre_estado??''}</td>
 
            </tr>`);

        });

    }

    abrirOrden(idOrden){
        // sessionStorage.setItem('idOrden', idOrden);
        let url =`/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${idOrden}`;
        var win = window.open(url, "_blank");
        win.focus(); 
    }

    abrirIngreso(idIngreso){
        var id = encode5t(idIngreso);
        let url =`/almacen/movimientos/pendientes-ingreso/imprimir_ingreso/${id}`;
        var win = window.open(url, "_blank");
        win.focus(); 
    }
}



