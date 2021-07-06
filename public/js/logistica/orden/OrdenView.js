// ============== View =========================
var vardataTables = funcDatatables();
var simboloMoneda='';
class OrdenView {
    init() {
        var reqTrueList = JSON.parse(sessionStorage.getItem('reqCheckedList'));
        var tipoOrden = sessionStorage.getItem('tipoOrden');
        if (reqTrueList !=null && (reqTrueList.length > 0)) {
            // ordenView.changeStateInput('form-crear-orden-requerimiento', false);
            // ordenView.changeStateButton('editar');
            ordenCtrl.obtenerRequerimiento(reqTrueList,tipoOrden);
            let btnVinculoAReq= `<span class="text-info" id="text-info-req-vinculado" > <a onClick="window.location.reload();" style="cursor:pointer;" title="Recargar con Valores Iniciales del Requerimiento">(vinculado a un Requerimiento)</a> <span class="badge label-danger" onClick="ordenView.eliminarVinculoReq();" style="position: absolute;margin-top: -5px;margin-left: 5px; cursor:pointer" title="Eliminar vínculo">×</span></span>`;
            document.querySelector("section[class='content-header']").children[0].innerHTML+=btnVinculoAReq;
    
        }  
        var idOrden = sessionStorage.getItem('idOrden');
        if(idOrden >0){
            mostrarOrden(idOrden);
            ordenView.changeStateButton('historial');

        }
        this.getTipoCambioCompra();

    }
    getTipoCambioCompra(){

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        let fechaHoy =now.toISOString().slice(0, 10)
        
        ordenCtrl.getTipoCambioCompra(fechaHoy).then(function(tipoCambioCompra) {
                document.querySelector("input[name='tipo_cambio_compra']").value= tipoCambioCompra;
        }).catch(function(err) {
            console.log(err)
        })
    }
    changeMoneda(event){
        simboloMoneda = event.options[event.selectedIndex].dataset.simboloMoneda;

        this.updateAllSimboloMoneda();
    }

    updateAllSimboloMoneda(){

        if(simboloMoneda ==''){
            let selectMoneda= document.querySelector("select[name='id_moneda']");
            simboloMoneda = selectMoneda.options[selectMoneda.selectedIndex].dataset.simboloMoneda;
            
        }
        let simboloMonedaAll=document.querySelectorAll("var[name='simboloMoneda']");
        simboloMonedaAll.forEach((element,indice) => {
            simboloMonedaAll[indice].textContent=simboloMoneda;
        });
        
    }

    changeSede(obj){
        ordenCtrl.changeSede(obj);
    }

    changeLogoEmprsa(id_empresa){
        switch (id_empresa) {
            case '1':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/logo_okc.png');
                break;
            case '2':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/logo_proyectec.png');
                break;
            case '3':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/logo_smart.png');
                break;
            case '4':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/jedeza_logo.png');
                break;
            case '5':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/rbdb_logo.png');
                break;
            case '6':
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/protecnologia_logo.png');
                break;
            default:
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/img-wide.png');
                break;
        }
    }

    handlechangeCondicion(event){
        let condicion= document.getElementsByName('id_condicion')[0];
        let text_condicion = condicion.options[condicion.selectedIndex].text;
        
        if(text_condicion == 'CONTADO CASH' || text_condicion=='Contado cash'){
            document.getElementsByName('plazo_dias')[0].value = null;
            document.getElementsByName('plazo_dias')[0].setAttribute('class','form-control activation group-elemento invisible');
            document.getElementsByName('text_dias')[0].setAttribute('class','form-control group-elemento invisible');
        }else if(text_condicion =='CREDITO' || text_condicion=='Crédito' ){
            document.getElementsByName('plazo_dias')[0].setAttribute('class','form-control activation group-elemento');
            document.getElementsByName('text_dias')[0].setAttribute('class','form-control group-elemento');

        }
    }

    loadHeadRequerimiento(data,idTipoOrden){
        if(idTipoOrden==3){ // orden de servicio
            this.ocultarBtnCrearProducto();
        }
        document.querySelector("select[name='id_tp_documento']").value=idTipoOrden;
        document.querySelector("img[id='logo_empresa']").setAttribute("src",data.logo_empresa);
        document.querySelector("input[name='cdc_req']").value=data.codigo_oportunidad?data.codigo_oportunidad:data.codigo;
        document.querySelector("input[name='ejecutivo_responsable']").value=data.nombre_ejecutivo_responsable?data.nombre_ejecutivo_responsable:'';
        document.querySelector("input[name='direccion_destino']").value=data.direccion_fiscal_empresa_sede?data.direccion_fiscal_empresa_sede:'';
        document.querySelector("input[name='id_ubigeo_destino']").value=data.id_ubigeo_empresa_sede?data.id_ubigeo_empresa_sede:'';
        document.querySelector("input[name='ubigeo_destino']").value=data.ubigeo_empresa_sede?data.ubigeo_empresa_sede:'';
        document.querySelector("select[name='id_sede']").value=data.id_sede?data.id_sede:'';
        document.querySelector("input[name='id_cc']").value=data.id_cc?data.id_cc:'';
        document.querySelector("textarea[name='observacion']").value=data.observacion?data.observacion:'';
        
        this.updateAllSimboloMoneda();
   
    }


    listar_detalle_orden_requerimiento(data){
        $('#listaDetalleOrden').DataTable({
            'bInfo':     false,
            // 'scrollCollapse': true,
            'serverSide': false,
            'processing': false,
            'paging':   false,
            'searching': false,
            'language' : vardataTables[0],
            'destroy' : true,
            'dom': 'Bfrtip',
            'order': false,
            'data': data,
            'bDestroy': true,
            'columns': [

                {'render':
                    function (data, type, row, meta){
                        return row.codigo_requerimiento;
                    }, 'name':'codigo_requerimiento'
                },
                {'render':
                    function (data, type, row, meta){
                        return row.part_number;
                    }, 'name':'codigo_item'
                },
                {'render':
                    function (data, type, row, meta){
                        return row.descripcion_producto?row.descripcion_producto:row.descripcion_adicional;
                    }, 'name':'descripcion_adicional'
                },
                {'render':
                    function (data, type, row, meta){
                        return row.unidad_medida;
                    }, 'name':'unidad_medida'
                },            
                {'render':
                    function (data, type, row, meta){
                        // return '<input type="text" class="form-control" name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+row.cantidad+'" onChange="updateInputCantidad(event);" style="width: 70px;" disabled/>';
                        return '<span name="cantidad" data-id="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+row.cantidad+'</span>';
                    
                    }, 'name':'cantidad'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado ==7){
                            return '<input type="number" name="precio" data-id="'+(row.id)+'" placeholder="0.00" min="0"  class="form-control" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="ordenCtrl.updateInputPrecio(event);" style="width:90px;" disabled/>';
                        }else{
                            return '<input type="number" name="precio" data-id="'+(row.id)+'" placeholder="0.00" min="0" class="form-control" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="ordenCtrl.updateInputPrecio(event);" style="width:90px;"/>';
                        }
                    } , 'name':'precio'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado == 7){
                            return '<input type="number" data-id="'+(row.id)+'" min="0" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="ordenCtrl.updateInputStockComprometido(event);" onfocusin ="ordenCtrl.updateInputStockComprometido(event);" style="width: 70px;" disabled />';
                        }else{
                            return '<input type="number" data-id="'+(row.id)+'" min="0" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="ordenCtrl.updateInputStockComprometido(event);" onfocusin ="ordenCtrl.updateInputStockComprometido(event);" style="width: 70px;"/>';
                        }
                    }, 'name':'stock_comprometido'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado == 7){
                            return '<input type="number" name="cantidad_a_comprar" data-id="'+(row.id)+'" min="0" class="form-control" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="ordenCtrl.updateInputCantidadAComprar(event);" value="'+(row.cantidad_a_comprar?row.cantidad_a_comprar:row.cantidad)+'" style="width:70px;" disabled />';
                        }else{
                            ordenCtrl.updateInObjCantidadAComprar((row.row+1),(row.id_requerimiento),(row.id_detalle_requerimiento),(row.cantidad));
    
                            return '<input type="number" name="cantidad_a_comprar" data-id="'+(row.id)+'" min="0" class="form-control" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="ordenCtrl.updateInputCantidadAComprar(event);" value="'+(row.cantidad_a_comprar?row.cantidad_a_comprar:row.cantidad)+'" style="width:70px;"/>';
                        }
                    } , 'name':'cantidad_a_comprar'
                },
                {'render':
                    function (data, type, row, meta){
                        // return '<div style="display:flex;"><var name="simboloMoneda"></var> <div name="subtotal" data-id="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+((Math.round((row.cantidad*row.precio_unitario) * 100) / 100).toFixed(2))+'</div></div>';
                        return '<input type="number" name="subtotal" data-id="'+(row.id)+'" min="0" class="form-control" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="ordenCtrl.updateInputSubtotal(event);" value="'+((Math.round((row.cantidad*row.precio_unitario) * 100) / 100).toFixed(2))+'" style="width:90px;"/>';

                    } , 'name':'subtotal'
                },
                {'render':
                    function (data, type, row, meta){
                
                        let action = `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-danger btn-sm activation" name="btnOpenModalEliminarItemOrden" title="Eliminar Item"  data-id="${(row.id)}" data-key="${(row.id)}" data-row="${(meta.row)}" data-id_requerimiento="${(row.id_requerimiento?row.id_requerimiento:0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)}"  onclick="ordenCtrl.openModalEliminarItemOrden(this);">
                                <i class="fas fa-trash fa-sm"></i>
                                </button>
                            </div>
                            `;
                        
                        return action;
                    }
                }
            ],
            "initComplete": function() {
                ordenView.updateAllSimboloMoneda();
                ordenCtrl.calcTotalOrdenDetalleList();
            },
            'rowCallback': function( row, data ) {
                if ( data.estado == '7' )
                { 
                    $('td', row).css({'background-color': 'mistyrose', 'color': 'indianred'});
                }
            },
   
            'columnDefs': [
                {
                    'targets': "_all",
                    'orderable': false
                },
                { width: '10px', targets: 0 },
                { width: '20px', targets: 1 },
                { width: '50px', targets: 2 },
                { width: '10px', targets: 3 },
                { width: '10px', targets: 4 },
                { width: '10px', targets: 5 },
                { width: '10px', targets: 6, sClass: 'invisible'},
                { width: '10px', targets: 7 },
                { width: '10px', targets: 8 },
                { width: '5px', targets: 9, sClass:'text-center' }
            ],
            'order': [[1, "asc"]]
    
    
        });

        $('#listaDetalleOrden thead th').off('click')
        document.querySelector("table[id='listaDetalleOrden']").tHead.style.fontSize = '11px',
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].style.fontSize = '11px';
        document.querySelector("table[id='listaDetalleOrden'] thead").style.backgroundColor = "grey";
        $('#listaDetalleOrden tr').css('cursor', 'default');
    
        let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    
    }
    

    eliminadoFilaTablaListaDetalleOrden(obj){
        let tr =obj.parentNode.parentNode.parentNode;
        tr.remove();
    }
    
    
    // modal agregar producto en orden 
    catalogoProductosModal(){
        $('#modal-catalogo-items').modal({
            show: true,
            backdrop: 'true',
            keyboard: true
    
        });
        this.ocultarBtnCrearProducto();
        ordenCtrl.getcatalogoProductos().then(function(res) {
            ordenView.listarItems(res);
        }).catch(function(err) {
            console.log(err)
        })

    }
    ocultarBtnCrearProducto(){
        cambiarVisibilidadBtn("btn-crear-producto","ocultar");
    }

    listarItems(data){
        var tablaListaItems =  $('#listaItems').dataTable({
            'language' : vardataTables[0],
            'processing': true,
            "bDestroy": true,
            // "scrollX": true,
            'data': data,
            'columns': [
                {'data': 'id_item'},
                {'data': 'id_producto'},
                {'data': 'id_servicio'},
                {'data': 'id_equipo'},
                {'data': 'codigo'},
                {'data': 'part_number'},
                {'data': 'categoria'},
                {'data': 'subcategoria'},
                {'data': 'descripcion'},
                {'data': 'unidad_medida_descripcion'},
                {'data': 'id_unidad_medida'},
                {'render':
                    function (data, type, row){
                        if(row.id_unidad_medida == 1){
                            return ('<button class="btn btn-sm btn-info" onClick="verSaldoProducto('+row.id_producto+ ');">Stock</button>');
                        }else{ 
                            return '';
                        }
    
                    }
                }
            ],
            'columnDefs': [
                { 'aTargets': [0], 'sClass': 'invisible'},
                { 'aTargets': [1], 'sClass': 'invisible'},
                { 'aTargets': [2], 'sClass': 'invisible'},
                { 'aTargets': [3], 'sClass': 'invisible'},
                { 'aTargets': [10], 'sClass': 'invisible'}
                        ],
            'order': [
                [8, 'asc']
            ],
            "initComplete": function(settings, json) {
                var trs = this.$('tr');
           
                for (let i = 0; i < trs.length; i++) {
                    trs[i].addEventListener('click', handleTrClick);
                }

                function handleTrClick(){
                    if(this.classList.contains('eventClick')){
                        this.classList.remove('eventClick');
                    }else{
                        const rows = Array.from(document.querySelectorAll('tr.eventClick'));
                        rows.forEach(row => {
                            row.classList.remove('eventClick');
                        });
                        
                        this.classList.add('eventClick');

                    }
                    var idItem = this.children[0].innerHTML;
                    var idProd = this.children[1].innerHTML;
                    var idServ = this.children[2].innerHTML;
                    var idEqui = this.children[3].innerHTML;
                    var codigo = this.children[4].innerHTML;
                    var partNum = this.children[5].innerHTML;
                    var categoria = this.children[6].innerHTML;
                    var subcategoria = this.children[7].innerHTML;
                    var descri = this.children[8].innerHTML;
                    var unidad = this.children[9].innerHTML;
                    var id_unidad = this.children[10].innerHTML;

                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent =idItem;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent =codigo;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent =partNum;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent =descri;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent =idProd;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_servicio']").textContent =idServ;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_equipo']").textContent =idEqui;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='unidad_medida']").textContent =unidad;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent =id_unidad;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='categoria']").textContent =categoria;
                    document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='subcategoria']").textContent =subcategoria;
                }
            } 
        });
    
     
    
        let tablelistaitem = document.getElementById(
            'listaItems_wrapper'
        )
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
        
        let listaItems_filter = document.getElementById(
            'listaItems_filter'
        )
        listaItems_filter.querySelector("input[type='search']").style.width='100%';
    }

    selectItem(){
        ordenCtrl.selectItem();
    }

 
 
    openModalEliminarItemOrden(obj){
        ordenCtrl.openModalEliminarItemOrden(obj);

    }

    // mostrar info si esta vinculado con un requerimiento
    eliminarVinculoReq(){
        sessionStorage.removeItem('reqCheckedList');
        sessionStorage.removeItem('tipoOrden');
        window.location.reload();
    }


    // guardar orden
    hasCheckedGuardarEnRequerimiento(){
        let hasCheck = document.querySelector("input[name='guardarEnRequerimiento']").checked;
        return hasCheck;
    }

    get_header_orden_requerimiento(){
        let id_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_orden']").value;
        let tipo_cambio_compra = document.querySelector("div[type='crear-orden-requerimiento'] input[name='tipo_cambio_compra']").value;
        let id_tp_documento = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_tp_documento']").value;
    
        let id_moneda = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_moneda']").value;
        let codigo_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='codigo_orden']").value;
        let fecha_emision = document.querySelector("div[type='crear-orden-requerimiento'] input[name='fecha_emision']").value;
        let incluye_igv = document.querySelector("div[type='crear-orden-requerimiento'] input[name='incluye_igv']").checked;
    
        let id_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_proveedor']").value;
        let id_contrib = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contrib']").value;
        let id_contacto_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value;
    
        let id_condicion = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_condicion']").value;
        let plazo_dias = document.querySelector("div[type='crear-orden-requerimiento'] input[name='plazo_dias']").value;
        let plazo_entrega = document.querySelector("div[type='crear-orden-requerimiento'] input[name='plazo_entrega']").value;
        let id_cc = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_cc']").value;
        let id_tp_doc = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_tp_doc']").value;
    
        let id_sede = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_sede']").value;
        let direccion_destino = document.querySelector("div[type='crear-orden-requerimiento'] input[name='direccion_destino']").value;
        let id_ubigeo_destino = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value;
    
        let personal_autorizado_1 = document.querySelector("div[type='crear-orden-requerimiento'] input[name='personal_autorizado_1']").value;
        let personal_autorizado_2 = document.querySelector("div[type='crear-orden-requerimiento'] input[name='personal_autorizado_2']").value;
        let observacion = document.querySelector("div[type='crear-orden-requerimiento'] textarea[name='observacion']").value;
    
        let data = {
            'id_orden':id_orden,
            'tipo_cambio_compra':tipo_cambio_compra,
            'id_tp_documento':id_tp_documento,
            'id_moneda':id_moneda, 
            'codigo_orden':codigo_orden, 
            'fecha_emision':fecha_emision, 
            'incluye_igv':incluye_igv, 
            
            'id_proveedor':id_proveedor, 
            'id_contrib':id_contrib,
            'id_contacto_proveedor':id_contacto_proveedor,
            
            'id_condicion':id_condicion, 
            'plazo_dias':plazo_dias, 
            'plazo_entrega':plazo_entrega, 
            'id_tp_doc':id_tp_doc, 
            'id_cc':id_cc,
    
            'id_sede':id_sede, 
            'direccion_destino':direccion_destino, 
            'id_ubigeo_destino':id_ubigeo_destino, 
            
            'personal_autorizado_1':personal_autorizado_1, 
            'personal_autorizado_2':personal_autorizado_2, 
            'observacion':observacion, 
    
            'detalle':[]
        }
        
        return data;  
    }

    incluyeIGVHandle(e){
        ordenCtrl.calcTotalOrdenDetalleList(e.target.checked);
    }
}

const ordenView = new OrdenView();



function save_orden(data, action){
    let hasCheck = ordenView.hasCheckedGuardarEnRequerimiento();
    payload_orden =ordenView.get_header_orden_requerimiento();
    if(hasCheck == true){
        let coutReqInObj =ordenCtrl.countRequirementsInObj();
        if(coutReqInObj == 1){
            // console.log(listCheckReq);
            // console.log(detalleOrdenList);
            // vincultar item con req unico
            let id_req = listCheckReq[0].id_req;
            detalleOrdenList.forEach(drs => {
                if(drs.id>0){
                    drs.id_requerimiento= id_req;
                }
            });

            payload_orden.detalle= detalleOrdenList;
            // payload_orden += '&detalle_requerimiento='+JSON.stringify(detalleOrdenList);
            ordenCtrl.guardar_orden_requerimiento(action,payload_orden);

        }else if(coutReqInObj >1){
            // console.log('open modal to select item/req');
            $('#modal-vincular-item-requerimiento').modal({
                show: true,
                backdrop: 'static'
            });
            // fillListaRequerimientosVinculados();

            
        }else{ //no existen nuevos item argregados, guardar nromal (no habra que guardar en req)
            payload_orden.detalle= detalleOrdenList;
            ordenCtrl.guardar_orden_requerimiento(action,payload_orden);
    
        }
    }else{ // sin guardar en req
        payload_orden =ordenView.get_header_orden_requerimiento();
        payload_orden.detalle= (typeof detalleOrdenList !='undefined')?detalleOrdenList:detalleOrdenList;
        ordenCtrl.guardar_orden_requerimiento(action,payload_orden);
    }
}

function anular_orden(id){
    baseUrl = 'anular/'+id;
    $.ajax({
        type: 'PUT',
        url: baseUrl,
        dataType: 'JSON',
        success: function(res){
            if (res.status == 200) {
                alert(res.mensaje);
                let url ="/logistica/gestion-logistica/compras/ordenes/listado/index";
                window.location.replace(url);
            }else {
                console.log(res);
                alert(res.mensaje);
                
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function fechaHoy(){
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value = now.toISOString().slice(0, -1);
};

function nueva_orden(){
    fechaHoy();
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='cdc_req']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ejecutivo_responsable']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_destino']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_1']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_2']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_1']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_2']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] textarea[name='observacion']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='incluye_igv']").checked=true;
    // document.querySelector("var[name='total']").textContent= '';


    limpiarTabla('listaDetalleOrden');
}

function limpiarTabla(idElement){
    // console.log("limpiando tabla....");
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--)
    {
        table.deleteRow(i);
    }
    return null;
}