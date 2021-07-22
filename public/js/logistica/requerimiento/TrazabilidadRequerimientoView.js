class TrazabilidadRequerimiento{

    verTrazabilidadRequerimientoModal(idRequerimiento){

        $('#modal-trazabilidad-requerimiento').modal({
            show: true
        });
        this.mostrarRequerimiento(idRequerimiento);
        this.mostrarHistorialAprobacion(idRequerimiento);
        this.mostrarTrazabilidadDetalleRequerimiento(idRequerimiento);

    }
    

    mostrarRequerimiento(idRequerimiento){
        requerimientoCtrl.getCabeceraRequerimiento(idRequerimiento).then(function (res) {
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='codigo_requerimiento']").textContent= res.codigo;
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='requerimiento_creado_por']").textContent= res.nombre_completo_usuario;
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='fecha_registro_requerimiento']").textContent= res.fecha_registro;
            document.querySelector("div[id='modal-trazabilidad-requerimiento'] ul[id='head_requerimiento'] span[id='estado_actual_requerimiento']").textContent= res.nombre_estado;
        }).catch(function (err) {
            console.log(err)
        })
    }

    mostrarHistorialAprobacion(idRequerimiento){
        requerimientoCtrl.getHistorialAprobacion(idRequerimiento).then(function (res) {
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
            }

        }).catch(function (err) {
            console.log(err)
        })
    }

    mostrarTrazabilidadDetalleRequerimiento(idRequerimiento){
        requerimientoCtrl.getTrazabilidadDetalleRequerimiento(idRequerimiento).then(function (res) {
            trazabilidadRequerimientoView.construirTablaTrazabilidadDetalleRequerimiento(res);
        }).catch(function (err) {
            console.log(err)
        })
    }
    
    construirTablaTrazabilidadDetalleRequerimiento(data){
        var vardataTables = funcDatatables();
        $('#listaTrazabilidadDetalleRequerimiento').DataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'bDestroy': true,
            'info':     false,
            'paging':   false,
            'searching': false,
            'language': vardataTables[0],
            'order': [[2, 'asc']],
            'bLengthChange': false,
            'serverSide': false,
            'destroy': true,
            'data': data,
            'columns': [

                {
                    'render': function (data, type, row, meta) {
                        return  meta.row +1;
                    },'className': 'text-center'
                },
                {
                    'render': function (data, type, row) {
                        if(row.id_tipo_item ==1){
                            return row.part_number_producto?row.part_number_producto:(row.part_number?row.part_number:'');
                        }else{
                            return "(Servicio)";
                        }

                    },'className': 'text-center' 
                },
                {
                    'render': function (data, type, row) {
                        return  row.descripcion_producto?row.descripcion_producto:(row.descripcion?row.descripcion:'');
                    },'className': 'text-left'
                },
                { 'data': 'id_detalle_requerimiento' },
                { 'data': 'id_detalle_requerimiento' },
                { 'data': 'id_detalle_requerimiento' }
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        let labelOrdenes='';
                        (row['ordenes_compra']).forEach(element => {
                            labelOrdenes += `<label class="lbl-codigo" title="Abrir orden" onclick="trazabilidadRequerimientoView.abrirOrden(${element.id_orden_compra})">${element.codigo}</label>`;
                        });
                        return labelOrdenes;
                        
                    }, targets: 3
                },
                {
                    'render': function (data, type, row) {
                        let labelGuiaIngreso='';
                        (row['guias_ingreso']).forEach(element => {
                            labelGuiaIngreso += `<label class="lbl-codigo" title="Abrir Guia Ingreso" onclick="trazabilidadRequerimientoView.abrirIngreso(${element.id_mov_alm})">${element.codigo}</label>`;
                        });
                        return labelGuiaIngreso;
                        
                    }, targets: 4
                },
                {
                    'render': function (data, type, row) {
                        let labelFacturas='';
                        (row['facturas']).forEach(element => {
                            labelFacturas += `<label>${element.codigo_factura}</label>`;
                        });
                        return labelFacturas;
                        
                    }, targets: 5
                },
  
    
            ]
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

const trazabilidadRequerimientoView = new TrazabilidadRequerimiento();


