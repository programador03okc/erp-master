var vardataTables = funcDatatables();

class ListaOrdenView {
    init() {
        this.vista_extendida()
        listaOrdenView.tipoVistaPorCabecera();
    }

    vista_extendida(){
        let body=document.getElementsByTagName('body')[0];
        body.classList.add("sidebar-collapse"); 
    }

    filtroTablaListaOrdenes(){
        $('#modal-filtro-lista-ordenes-elaboradas').modal({
            show: true,
            backdrop: 'true'
        });
    }

    // botonera secundaria 
    tipoVistaPorCabecera(){
        document.querySelector("button[id='btnTipoVistaPorCabecera'").classList.add('active');
        document.querySelector("button[id='btnTipoVistaPorItemPara'").classList.remove('active');
        document.querySelector("div[id='contenedor-tabla-nivel-cabecera']").classList.remove('oculto');
        document.querySelector("div[id='contenedor-tabla-nivel-item']").classList.add('oculto');

        
        listaOrdenView.obtenerListaOrdenesElaboradas();

    }
    tipoVistaPorItem(){
        document.querySelector("button[id='btnTipoVistaPorItemPara'").classList.add('active');
        document.querySelector("button[id='btnTipoVistaPorCabecera'").classList.remove('active');
        document.querySelector("div[id='contenedor-tabla-nivel-cabecera']").classList.add('oculto');
        document.querySelector("div[id='contenedor-tabla-nivel-item']").classList.remove('oculto');

        listaOrdenView.obtenerListaDetalleOrdenesElaboradas();

    }
    // 

    aplicarFiltros(){
        let ChkEtapa =document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEtapa']").checked;
        let ChkEstado = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEstado']").checked;

        if(ChkEtapa == true){
            let inputEtapa= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='etapa']");
            let textInputEtapa= inputEtapa.options[inputEtapa.selectedIndex].text;
            if(document.getElementById("badge-etapa") == undefined){
                let elementoBadge = listaOrdenView.crearElementoBadge("badge-etapa","Etapa: "+textInputEtapa);
                document.querySelector("div[id='contenedor-acciones-activas']").appendChild(elementoBadge);

            }else{
                listaOrdenView.actualizarElementoBadge("badge-etapa","Etapa: "+textInputEtapa);
            }
        }
        if(ChkEstado == true){

        }

        
    }

    obtenerListaOrdenesElaboradas(){
        listaOrdenCtrl.obtenerListaOrdenesElaboradas().then(function(res) {
            listaOrdenView.construirTablaListaOrdenesElaboradas(res);
        }).catch(function(err) {
            console.log(err)
        })
    }

    construirTablaListaOrdenesElaboradas(data){
        tablaListaOrdenes = $('#listaOrdenes').DataTable({
            'processing':true,
            'destroy':true,
            'language' : vardataTables[0],
            'data': data,
            // "dataSrc":'',
            'dom': 'Bfrtip',
            'scrollX': false,
            'columns': [
                {'data': 'id_orden_compra'},
                {'render':
                function (data, type, row, meta){
                    return '<label class="lbl-codigo" title="Abrir Orden" onClick="listaOrdenView.abrirOrden('+row.id_orden_compra+')">'+row.codigo+'</label>';
                    }
                },
                {'render':
                function (data, type, row, meta){
                    return (row.codigo_requerimiento)
                    }
                },
                {'data': 'fecha'},
                {'data': 'descripcion_sede_empresa'},
                {'render':
                    function (data, type, row, meta){
                        return (row.razon_social+' - RUC:'+row.nro_documento)
                    }
                },
                {'data': 'moneda_simbolo'},
                {'data': 'condicion'},
                {'render':
                    function (data, type, row, meta){
                        let output='No aplica';
                        if(row.id_tp_documento ==2){ // orden de compra
                            var estimatedTimeOfArrive= moment(row['fecha']).add(row['plazo_entrega'], 'days').format('YYYY-MM-DD');
                            let dias_restantes = restarFechas(fecha_actual(), sumaFecha(row['plazo_entrega'], row['fecha']));
                            var porc = dias_restantes * 100 / (parseFloat(row['plazo_entrega'])).toFixed(2);
                            var color = (porc > 50 ? 'success' : ((porc <= 50 && porc > 20) ? 'warning' : 'danger'));
                            output= `<div class="progress-group">
                            <span class="progress-text">${estimatedTimeOfArrive} <br> Nro días Restantes</span>
                            <span class="float-right"><b>${dias_restantes?dias_restantes:''}</b> / ${row.plazo_entrega?row.plazo_entrega:''}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-${color}" style="width: ${(porc<1)?'100':porc}%"></div>
                            </div>
                        </div>`;
    
                        }
                        return output;
                    }
                },
                {'render':
                    function (data, type, row, meta){
                        return '<center><span class="label label-'+row.bootstrap_color+'">'+row.estado_doc+'</span></center>';
                    }
                },
                {'data': 'detalle_pago'},
                {'data': 'archivo_adjunto'},
                {'render':
                    function (data, type, row, meta){
                        let containerOpenBrackets='<div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let btnImprimirOrden= '<button type="button" onClick="listaOrdenView.imprimir_orden(event)" title="Imprimir Orden" class="imprimir_orden btn btn-md btn-warning boton" data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="'+row.id_orden_compra+'"  data-id-pago=""> <i class="fas fa-file-pdf"></i> </button>';
                        let btnVerDetalle= `<button type="button" class="ver-detalle btn btn-primary boton" onclick="listaOrdenView.verDetalleOrden(this)" data-toggle="tooltip" data-placement="bottom" title="Ver Detalle" data-id="${row.id_orden_compra}">
                        <i class="fas fa-chevron-down"></i>
                        </button>`;
                        let containerCloseBrackets='</div>';
                        return (containerOpenBrackets+btnVerDetalle+btnImprimirOrden+containerCloseBrackets);
                    }
                }
                
            ],
            'columnDefs': [{ className: "text-right", 'aTargets': [0], 'sClass': 'invisible'}]
            ,"initComplete": function(settings, json) {
                
            }
            // "drawCallback": function (settings) { 
                // Here the response
                // var response = settings.json;
                // console.log(response);
                
                // if(response == undefined || response.data.length ==0){
                //         // alert("No hay ordenes registradas para mostrar");
                // }
            // },
        });


    }

    construirDetalleOrdenElaboradas(table_id,row,response){
        var html = '';
        if (response.length > 0) {
            response.forEach(function (element) {
                html += `<tr>
                    <td style="border: none;">${(element.orden_am !== null ? element.orden_am + ` <a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${element.id_oc_propia}&ImprimirCompleto=1">
                    <span class="label label-success">Ver O.E.</span></a>
                    <a href="${element.url_oc_fisica}">
                    <span class="label label-warning">Ver O.F.</span></a>`:'')}</td>
                    <td style="border: none;">${element.codigo_oportunidad !== null ? element.codigo_oportunidad : ''}</td>
                    <td style="border: none;">${element.oportunidad !== null ? element.oportunidad : ''}</td>
                    <td style="border: none;">${element.nombre !== null ? element.nombre : ''}</td>
                    <td style="border: none;">${element.user_name !== null ? element.user_name : ''}</td>
                    <td style="border: none;"><label class="lbl-codigo" title="Abrir Requerimiento" onClick="listaOrdenView.abrirRequerimiento(${element.id_requerimiento})">${element.codigo_req}</label> ${element.sede_req}</td>
                    <td style="border: none;">${element.codigo}</td>
                    <td style="border: none;">${element.part_number !== null ? element.part_number : ''}</td>
                    <td style="border: none;">${element.descripcion}</td>
                    <td style="border: none;">${element.cantidad}</td>
                    <td style="border: none;">${element.abreviatura}</td>
                    <td style="border: none;">${formatNumber.decimal(element.precio, '', 2)}</td>
                    <td style="border: none;">${formatNumber.decimal(element.subtotal, '', 2)}</td>
                    </tr>`;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">Orden Elec.</th>
                        <th style="border: none;">Cod.CC</th>
                        <th style="border: none;">Oportunidad</th>
                        <th style="border: none;">Entidad</th>
                        <th style="border: none;">Corporativo</th>
                        <th style="border: none;">Cod.Req.</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">PartNumber</th>
                        <th style="border: none;">Descripción</th>
                        <th style="border: none;">Cantidad</th>
                        <th style="border: none;">Und.Med</th>
                        <th style="border: none;">Unitario</th>
                        <th style="border: none;">Total</th>
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

    abrirRequerimiento(idRequerimiento){
        listaOrdenCtrl.abrirRequerimiento(idRequerimiento);

    }

    abrirOrden(idOrden){
        console.log(idOrden);
        console.log('abrir orden');
    }

    imprimir_orden(event){
        if (event.currentTarget.dataset.idOrdenCompra > 0){
            window.open('generar-orden-pdf/'+event.currentTarget.dataset.idOrdenCompra);
        }
    }

    verDetalleOrden(obj){
        listaOrdenCtrl.verDetalleOrden(obj);
    }   




    // vista nivel de items

    obtenerListaDetalleOrdenesElaboradas(){
        listaOrdenCtrl.obtenerListaDetalleOrdenesElaboradas().then(function(res) {
            listaOrdenView.construirTablaListaDetalleOrdenesElaboradas(res);
        }).catch(function(err) {
            console.log(err)
        })
    }

    construirTablaListaDetalleOrdenesElaboradas(data){
        $('#listaDetalleOrden').DataTable({
            'processing':true,
            'destroy':true,
            'language' : vardataTables[0],
            'dom': 'Bfrtip',
            'scrollX': false,
            'order': [10, 'desc'],
            'data': data,
            'columns': [
                { render: function (data, type, row) {     
                    return `<span class="label label-primary" onClick="listaOrdenView.verOrdenModal(this);" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}"  data-codigo-requerimiento="${row.codigo_requerimiento}" data-id-requerimiento="${row.orden_id_requerimiento}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Ver Orden">${row.orden_codigo}</span>`;
                    }
                },
                { render: function (data, type, row) {   
                    return `${row.codigo_requerimiento?row.codigo_requerimiento:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.orden_codigo_softlink?row.orden_codigo_softlink:''}`;
    
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.concepto?row.concepto:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.razon_social_cliente?row.razon_social_cliente:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.razon_social?row.razon_social:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.subcategoria?row.subcategoria:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.categoria?row.categoria:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.part_number?row.part_number:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.alm_prod_descripcion?row.alm_prod_descripcion:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.orden_fecha?moment(row.orden_fecha).format('YYYY-MM-DD'):''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.orden_plazo_entrega?row.orden_plazo_entrega+' días':''}`;
                    }
                },
                { render: function (data, type, row) {     
                    
                    let output='No aplica';
                    if(row['orden_id_tp_documento'] ==2){ // orden de compra
                    var estimatedTimeOfArrive= moment(row['orden_fecha']).add(row['orden_plazo_entrega'], 'days').format('YYYY-MM-DD');
                    let dias_restantes = restarFechas(fecha_actual(), sumaFecha(row['orden_plazo_entrega'], row['orden_fecha']));
                    var porc = dias_restantes * 100 / (parseFloat(row['orden_plazo_entrega'])).toFixed(2);
                    var color = (porc > 50 ? 'success' : ((porc <= 50 && porc > 20) ? 'warning' : 'danger'));
                    output= `<div class="progress-group">
                        <span class="progress-text">${estimatedTimeOfArrive} <br> Nro días Restantes</span>
                        <span class="float-right"><b>${dias_restantes?dias_restantes:''}</b> / ${row['orden_plazo_entrega']?row['orden_plazo_entrega']:''}</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-${color}" style="width: ${(porc<1)?'100':porc}%"></div>
                        </div>
                    </div>`;

                    }
                    return output;
                    }
                },
                {'data': 'empresa_sede'},
                { render: function (data, type, row) {    
                    let estadoDetalleOrdenHabilitadasActualizar=[17,20,26,30,31];
                    if(estadoDetalleOrdenHabilitadasActualizar.includes(row.id_detalle_orden_estado) ==true){
                        return `<span class="label label-${row.detalle_orden_estado_bootstrap_color}" onClick="listaOrdenView.editarEstadoItemOrden(this);" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.detalle_orden_estado}</span>`;
                    }else{
                        return `<span class="label label-${row.detalle_orden_estado_bootstrap_color}" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: default;">${row.detalle_orden_estado}</span>`;
                    }
    
                    }
                },
                { render: function (data, type, row) {               
                        return ('<div class="btn-group btn-group-sm" role="group">'+
                                '<button type="button" class="btn btn-default btn-xs" name="btnGenerarOrdenRequerimientoPDF" title="Descargar Orden" data-id-requerimiento="'+row.orden_id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo_requerimiento+'" data-id-orden-compra="'+row.orden_id_orden_compra+'" onclick="listaOrdenView.generarOrdenRequerimientoPDF(this);">'+
                                '<i class="fas fa-file-download fa-xs"></i>'+
                                '<button type="button" class="btn btn-danger btn-xs" name="btnEliminarAtencionOrdenRequerimiento" title="Revertir Atención" data-id-requerimiento="'+row.orden_id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo_requerimiento+'" data-id-orden-compra="'+row.orden_id_orden_compra+'" onclick="listaOrdenView.eliminarAtencionOrdenRequerimiento(this);">'+
                                '<i class="fas fa-backspace fa-xs"></i>'+
                                '</button>'+
                                '<button type="button" class="btn btn-primary btn-xs" name="btnDocumentosVinculados" title="Ver Documento Vinculados" data-id-requerimiento="'+row.orden_id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo_requerimiento+'" data-id-orden-compra="'+row.orden_id_orden_compra+'" onclick="listaOrdenView.documentosVinculados(this);">'+
                                '<i class="fas fa-folder fa-xs"></i>'+
                                '</button>'+
                                '</div>');
                    }   
                }   
            ]
            // 'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }

    verOrdenModal(obj){
        // let codigo = obj.dataset.codigoOrdenCompra;
        let id_orden = obj.dataset.idOrdenCompra;
        // let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
        // console.log(id_orden);
    
        $('#modal-ver-orden').modal({
            show: true,
            backdrop: 'true'
        });
        listaOrdenCtrl.ver_orden(id_orden).then(function(res) {
            if (res.status ==200){
                listaOrdenView.llenarCabeceraOrden(res.data.orden);
                listaOrdenView.llenarTablaItemsOrden(res.data.detalle_orden);
            }else{
                alert("sin data");
            }
        }).catch(function(err) {
            console.log(err)
        })
    }

    llenarTablaItemsOrden(data){
        $('#tablaItemOrdenCompra').dataTable({
            bDestroy: true,
            order: [[0, 'asc']],
            info:     true,
            iDisplayLength:2,
            paging:   true,
            searching: false,
            language: vardataTables[0],
            processing: true,
            bDestroy: true,
            data:data ,
            columns: [
                {'render':
                    function (data, type, row, meta){
                        return meta.row +1;
                    }
                },
     
                { data: 'codigo_item' },
                { data: 'part_number' },
                { data: 'categoria' },
                { data: 'subcategoria' },
                { data: 'descripcion' },
                { data: 'unidad_medida' },
                { data: 'cantidad' },
                { data: 'precio_unitario' },
                { data: 'subtotal' },
                {'render':
                    function (data, type, row, meta){
                        let estadoDetalleOrdenHabilitadasActualizar=[17,20,26,30,31];
    
                        if(estadoDetalleOrdenHabilitadasActualizar.includes(row.id_estado_detalle_orden)==true){
                            return `<span class="label label-${row.bootstrap_color_estado_detalle_orden}" onClick="listaOrdenView.editarEstadoItemOrden(this);" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.estado_detalle_orden}</span>`;
                        }else{
                            return `<span class="label label-${row.bootstrap_color_estado_detalle_orden}" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: default;" >${row.estado_detalle_orden}</span>`;
                        }
                    }
                }
            ],
    
        })
    
        let tablelistaitem = document.getElementById('tablaItemOrdenCompra_wrapper');
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    }

    llenarCabeceraOrden(data){
        // console.log(data);
        document.querySelector("span[id='inputCodigo']").textContent = data.codigo;
        document.querySelector("p[id='inputProveedor']").textContent = data.razon_social+' RUC: '+data.nro_documento;
        document.querySelector("p[id='inputFecha']").textContent = data.fecha;
        document.querySelector("p[id='inputMoneda']").textContent = data.simbolo;
        document.querySelector("p[id='inputCondicion']").textContent = data.condicion+' '+data.plazo_dias+' días';
        document.querySelector("p[id='inputPlazoEntrega']").textContent = data.plazo_entrega;
        document.querySelector("p[id='inputCodigoSoftlink']").textContent = data.codigo_softlink;
        let estadoOrdenHabilitadasActualizar=[17,20,26,30,31];
    
        if(estadoOrdenHabilitadasActualizar.includes(data.id_estado)==true){
            document.querySelector("p[id='inputEstado']").innerHTML = `<span class="label label-${data.bootstrap_color}" id="estado_orden" onClick="listaOrdenView.editarEstadoOrden(this);" data-id-estado-orden-compra="${data.id_estado}" data-id-orden-compra="${data.id_orden_compra}" data-codigo-orden-compra="${data.codigo_softlink}" style="cursor: pointer;" title="Cambiar Estado de Orden">${data.estado_doc}</span>`
        }else{
            document.querySelector("p[id='inputEstado']").innerHTML = `<span class="label label-${data.bootstrap_color}" id="estado_orden" data-id-estado-orden-compra="${data.id_estado}" data-id-orden-compra="${data.id_orden_compra}" data-codigo-orden-compra="${data.codigo_softlink}" style="cursor: default;">${data.estado_doc}</span>`
        }
    }

    editarEstadoOrden(obj){
        let id_orden = obj.dataset.idOrdenCompra;
        let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
        let codigo = obj.dataset.codigoOrdenCompra;
    
        $('#modal-editar-estado-orden').modal({
            show: true,
            backdrop: 'true'
        });
    
        document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value = id_orden;
        document.querySelector("div[id='modal-editar-estado-orden'] span[name='codigo_orden_compra'").textContent = codigo;
    
        this.fillEstados(id_estado_actual);
    }

    editarEstadoItemOrden(obj){
        let id_orden_compra = obj.dataset.idOrdenCompra;
        let id_detalle_orden = obj.dataset.idDetalleOrdenCompra;
        let id_estado_actual = obj.dataset.idEstadoDetalleOrdenCompra;
        let codigo_item = obj.dataset.codigoItem;
    
        $('#modal-editar-estado-detalle-orden').modal({
            show: true,
            backdrop: 'true'
        });
    
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value = id_orden_compra;
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value = id_detalle_orden;
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] span[name='codigo_item_orden_compra'").textContent = codigo_item;
    
        this.fillEstados(id_estado_actual);
    }

    fillEstados(id_estado_actual){
        // console.log(id_estado_actual);
        let html ='';
        let estados = [
                {'17':'ENVIADO'},
                {'30':'CONFIRMADA'},
                {'31':'FACTURADA'},
                {'20':'DESPACHADO'},
                {'26':'EN TRANSITO'},
                {'15':'ATENCIÓN PARCIAL'}
        ];
    
    
        if(id_estado_actual > 0 ){
        estados.forEach(element => {
                if(id_estado_actual == (Object.keys(element)[0])){
                    html+=`<option value="${Object.keys(element)[0]}" selected>${Object.values(element)[0]}</option>`;
                }else{
                    html+=`<option value="${Object.keys(element)[0]}">${Object.values(element)[0]}</option>`;
                }
                
            });
            document.querySelector("select[name='estado_orden']").innerHTML = html;
            document.querySelector("select[name='estado_detalle_orden']").innerHTML = html;
        }else{
            estados.forEach(element => {
                html+=`<option value="${Object.keys(element)[0]}">${Object.values(element)[0]}</option>`;
                
            });
            document.querySelector("select[name='estado_orden']").innerHTML = html;
            document.querySelector("select[name='estado_detalle_orden']").innerHTML = html;
        }
    
    
    }

    updateEstadoOrdenCompra(){
        let id_orden_compra = document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value;
        let id_estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").value;
        let estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'")[document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").selectedIndex].textContent;

        listaOrdenCtrl.actualizarEstadoOrdenPorRequerimiento(id_orden_compra,id_estado_orden_selected).then(function(res){
            listaOrdenView.tipoVistaPorItem();

            if(res ==1){
                alert('El estado fue Actualizado');
                document.querySelector("span[id='estado_orden']").textContent = estado_orden_selected;
                $('#modal-editar-estado-orden').modal('hide');
            }else{
                alert('Hubo un problema al intentar Actualizado');
                
            }
        }).catch(function(err){
            console.log(err)
        })
        
    }

    updateEstadoDetalleOrdenCompra(){
        let id_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value;
        let id_detalle_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value;
        let id_estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").value;
        let estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'")[document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").selectedIndex].textContent;

        listaOrdenCtrl.actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra,id_estado_detalle_orden_selected).then(function(res){
            listaOrdenView.tipoVistaPorItem();
            if(res ==1){
                alert('El estado del item fue actualizado');
                listaOrdenCtrl.ver_orden(id_orden_compra).then(function(res) {
                    if (res.status ==200){
                        listaOrdenView.llenarCabeceraOrden(res.data.orden);
                        listaOrdenView.llenarTablaItemsOrden(res.data.detalle_orden);
                    }else{
                        alert("sin data");
                    }
                }).catch(function(err) {
                    console.log(err)
                })
                $('#modal-editar-estado-detalle-orden').modal('hide');
            }else{
                alert('Hubo un problema al intentar Actualizado');
                
            }
        }).catch(function(err){
            console.log(err)
        })
        
    }

    generarOrdenRequerimientoPDF(obj){
        let id_orden = obj.dataset.idOrdenCompra;
        window.open('generar-orden-pdf/'+id_orden);
    }

    eliminarAtencionOrdenRequerimiento(obj){
        let codigo_requerimiento = obj.dataset.codigoRequerimiento;
        let id_orden = obj.dataset.idOrdenCompra;

        var ask = confirm('¿Desea revertir el requerimiento '+codigo_requerimiento+'?');
        if (ask == true){
            listaOrdenCtrl.eliminarAtencionOrdenRequerimiento(id_orden).then(function(res) {
                    if (res.status == 200) {
                        alert(response.mensaje);
                        listaOrdenView.tipoVistaPorItem();
                    }else {
                        console.log(res);
                        alert(res.mensaje);
                        
                    }
            }).catch(function(err) {
                console.log(err)
            })
        }


    }

    documentosVinculados(obj){
        $('#modal-documentos-vinculados').modal({
            show: true,
            backdrop: 'static'
        });

        let id_orden_compra = obj.dataset.idOrdenCompra;
        listaOrdenCtrl.listarDocumentosVinculados(id_orden_compra).then(function(res) {
            listaOrdenView.llenarTablaDocumentosVinculados(res.data);
        }).catch(function(err) {
        console.log(err)
        })
    }

    llenarTablaDocumentosVinculados(data){
        var vardataTables = funcDatatables();
        $('#tablaDocumentosVinculados').dataTable({
            'info':     false,
            'searching': false,
            'paging':   false,
            'language' : vardataTables[0],
            'processing': true,
            "bDestroy": true,
            'data':data,
            'columns': [
                {'render':
                    function (data, type, row){
                        return `<a href="${row.orden_fisica}" target="_blank"><span class="label label-warning">Orden Física</span></a> 
                        <a href="${row.orden_electronica}" target="_blank"><span class="label label-info">Orden Electrónica</span></a>`;
                    }
                }
            ]
        });
        let tableDocumentosVinculados = document.getElementById(
            'tablaDocumentosVinculados_wrapper'
        )
        tableDocumentosVinculados.childNodes[0].childNodes[0].hidden = true;
    }
    
    // 
}

const listaOrdenView = new ListaOrdenView();
