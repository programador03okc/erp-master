// ============== View =========================
var vardataTables = funcDatatables();

class OrdenView {
    init() {
        // this.renderCrearOrdenModule(null,null);
        var reqTrueList = JSON.parse(sessionStorage.getItem('reqCheckedList'));
        if (reqTrueList !=null && (reqTrueList.length > 0)) {
            ordenCtrl.obtenerRequerimiento(reqTrueList);
            changeStateButton('editar');
            // changeStateButton('historial');
            changeStateInput('form-crear-orden-requerimiento', false);
            let btnVinculoAReq= `<span class="text-info" id="text-info-req-vinculado" > <a onClick="window.location.reload();" style="cursor:pointer;" title="Recargar con Valores Iniciales del Requerimiento">(vinculado a un Requerimiento)</a> <span class="badge label-danger" onClick="ordenView.eliminarVinculoReq();" style="position: absolute;margin-top: -5px;margin-left: 5px; cursor:pointer" title="Eliminar vínculo">×</span></span>`;
            document.querySelector("section[class='content-header']").children[0].innerHTML+=btnVinculoAReq;
    
        }  
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
                document.querySelector("div[id='group-datos_para_despacho-logo_empresa'] img[id='logo_empresa']").setAttribute('src','/images/img-default.jpg');
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

    loadHeadRequerimiento(data){
        document.querySelector("img[id='logo_empresa']").setAttribute("src",data.logo_empresa);
        document.querySelector("input[name='cdc_req']").value=data.codigo_oportunidad?data.codigo_oportunidad:data.codigo;
        document.querySelector("input[name='ejecutivo_responsable']").value=data.nombre_ejecutivo_responsable?data.nombre_ejecutivo_responsable:'';
        document.querySelector("input[name='direccion_destino']").value=data.direccion_fiscal_empresa_sede?data.direccion_fiscal_empresa_sede:'';
        document.querySelector("input[name='id_ubigeo_destino']").value=data.id_ubigeo_empresa_sede?data.id_ubigeo_empresa_sede:'';
        document.querySelector("input[name='ubigeo_destino']").value=data.ubigeo_empresa_sede?data.ubigeo_empresa_sede:'';
        // document.querySelector("select[name='id_empresa']").value=data.id_empresa?data.id_empresa:'';
        document.querySelector("select[name='id_sede']").value=data.id_sede?data.id_sede:'';
        document.querySelector("input[name='id_cc']").value=data.id_cc?data.id_cc:'';
    }

    listar_detalle_orden_requerimiento(data){
 
        $('#listaDetalleOrden').DataTable({
            'info':     false,
            'scrollCollapse': true,
            'paging':   false,
            'searching': false,
            'language' : vardataTables[0],
            'destroy' : true,
            'data': data,
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
                        return '<span name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+row.cantidad+'</span>';
                    
                    }, 'name':'cantidad'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado ==7){
                            return '<input type="text" class="form-control" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="ordenCtrl.updateInputPrecio(event);" style="width:70px;" disabled/>';
                        }else{
                            return '<input type="text" class="form-control" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="ordenCtrl.updateInputPrecio(event);" style="width:70px;"/>';
                        }
                    } , 'name':'precio'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado == 7){
                            return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="ordenCtrl.updateInputStockComprometido(event);" onfocusin ="ordenCtrl.updateInputStockComprometido(event);" style="width: 70px;" disabled />';
                        }else{
                            return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="ordenCtrl.updateInputStockComprometido(event);" onfocusin ="ordenCtrl.updateInputStockComprometido(event);" style="width: 70px;"/>';
                        }
                    }, 'name':'stock_comprometido'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado == 7){
                            return '<input type="text" class="form-control" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="ordenCtrl.updateInputCantidadAComprar(event);" value="'+(row.cantidad_a_comprar?row.cantidad_a_comprar:row.cantidad)+'" style="width:70px;" disabled />';
                        }else{
                            ordenCtrl.updateInObjCantidadAComprar((meta.row+1),(row.id_requerimiento),(row.id_detalle_requerimiento),(row.cantidad));
    
                            return '<input type="text" class="form-control" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="ordenCtrl.updateInputCantidadAComprar(event);" value="'+(row.cantidad_a_comprar?row.cantidad_a_comprar:row.cantidad)+'" style="width:70px;"/>';
                        }
                    } , 'name':'cantidad_a_comprar'
                },
                {'render':
                    function (data, type, row, meta){
                        return '<div name="subtotal" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"></div>';
                    } , 'name':'subtotal'
                },
                {'render':
                    function (data, type, row, meta){
                        let action ='';
                        if(row.estado ==7){
                            action = `
                            <div class="btn-group btn-group-sm" role="group" style="cursor: default;">
                                <i class="fas fa-sticky-note fa-2x" style="color:orange" title="${(row.observacion?row.observacion:'Sin Observación')}" ></i>
                            </div>
                            `;
                        }else{
                            action = `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-danger btn-sm activation" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" data-key="${(row.id)}" data-row="${(meta.row)}" data-id_requerimiento="${(row.id_requerimiento?row.id_requerimiento:0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)}"  onclick="ordenCtrl.openModalEliminarItemOrden(this);">
                                <i class="fas fa-trash fa-sm"></i>
                                </button>
                            </div>
                            `;
                        }
                        return action;
                    }
                }
            ],
            'rowCallback': function( row, data ) {
                if ( data.estado == '7' )
                { 
                    $('td', row).css({'background-color': 'mistyrose', 'color': 'indianred'});
                }
            },
   
            'columnDefs': [
                { width: '10px', targets: 0 },
                { width: '20px', targets: 1 },
                { width: '20px', targets: 2 },
                { width: '40px', targets: 3 },
                { width: '50px', targets: 4 },
                { width: '20px', targets: 5 },
                { width: '20px', targets: 6, sClass: 'invisible'},
                { width: '15px', targets: 7 },
                { width: '20px', targets: 8 },
                { width: '30px', targets: 9, sClass:'text-center' }
            ],
            'order': [[1, "asc"]]
    
    
        });
    
        let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    
    }
    

    afectarEstadoEliminadoFilaTablaListaDetalleOrden(rowSelected,motivo){
        let sizeTableListaDetalleOrden = document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children.length;
        for (let index = 0; index < sizeTableListaDetalleOrden; index++) {
            let row = document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[9].children[0].children[0].dataset.row;
            if(row ==rowSelected){
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[5].children[0].disabled = true;
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[6].children[0].disabled = true;
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[7].children[0].disabled = true;
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[8].children[0].disabled = true;
    
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[9].children[0].children[0].disabled = true;
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[9].children[0].children[0].remove();
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[9].children[0].innerHTML=`
                <div class="btn-group btn-group-sm" role="group" style="cursor: default;">
                    <i class="fas fa-sticky-note fa-2x" style="color:orange" title="${(motivo?motivo:'Sin Observación')}" ></i>
                </div>
                `;
                document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].setAttribute("style","background:mistyrose; color:indianred;");
            }
            
        }
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
                var trs = document.querySelectorAll('#listaItems tr');
                trs.forEach(function(tr){
                    tr.addEventListener('click', handleTrClick);
                    });
                function handleTrClick(){
                    if(this.classList.contains('eventClick')){
                        this.classList.remove('eventClick');
                    }else{
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

    loadDetailOrden(data){
        var hasAttrDisabled ='';
        if(document.querySelector("button[id='btnEditar']").hasAttribute('disabled')== false){
            hasAttrDisabled ='disabled';
        }else{
            hasAttrDisabled = '';
        }
    
        var vardataTables = funcDatatables();
        $('#listaDetalleOrden').DataTable({
            bDestroy: true,
            order: [[0, 'asc']],
            info:     false,
            scrollCollapse: true,
            paging:   false,
            searching: false,
            language: vardataTables[0],
            processing: true,
            responsive: true,
            bDestroy: true,
            data:data,
            columns: [
     
                {'render':
                    function (data, type, row, meta){
                        return row.codigo_requerimiento;
                    }, 'name':'codigo_requerimiento'
                },
                {'render':
                    function (data, type, row, meta){
                        return row.part_number;
                    }, 'name':'part_number'
                },
                {'render':
                    function (data, type, row, meta){
                        return row.descripcion_producto;
                    }, 'name':'descripcion_producto'
                },
                {'render':
                    function (data, type, row, meta){
                        return row.unidad_medida;
                    }, 'name':'unidad_medida'
                },            
                {'render':
                    function (data, type, row, meta){
                        return '<span name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+row.cantidad+'</span>';
                    
                    }, 'name':'cantidad'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado ==7){
                            return '<input type="text" class="form-control activation" name="precio" data-key="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="ordenView.updateDetalleOrdenListPrecio(event);" style="width:70px;" disabled/>';
                        }else{
                            return '<input type="text" class="form-control activation" name="precio" data-key="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_unitario?row.precio_unitario:"")+'" onChange="ordenView.updateDetalleOrdenListPrecio(event);" style="width:70px;" '+hasAttrDisabled+'/>';
                        }
                    } , 'name':'precio'
                },
                {'render':
                    function (data, type, row, meta){
                        if(row.estado == 7){
                            return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="ordenView.updateInputStockComprometido(event);" onfocusin ="ordenView.updateInputStockComprometido(event);" style="width: 70px;" disabled />';
                        }else{
                            return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="ordenView.updateInputStockComprometido(event);" onfocusin ="ordenView.updateInputStockComprometido(event);" style="width: 70px;" '+hasAttrDisabled+'/>';
                        }
                    }, 'name':'stock_comprometido'
                },
                {'render':
                    function (data, type, row, meta){
    
                        if(row.estado == 7){
                            return '<input type="text" class="form-control activation" name="cantidad_a_comprar" data-key="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="ordenView.updateDetalleOrdenListCantidadAComprar(event);" value="'+(row.cantidad?row.cantidad:'')+'" style="width:70px;" disabled />';
                        }else{
                            // updateInObjCantidadAComprar((meta.row+1),(row.id_requerimiento),(row.id_detalle_requerimiento),(row.cantidad));
    
                            return '<input type="text" class="form-control activation" name="cantidad_a_comprar" data-key="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="ordenView.updateDetalleOrdenListCantidadAComprar(event);" value="'+(row.cantidad_a_comprar?row.cantidad_a_comprar:'')+'" style="width:70px;"'+hasAttrDisabled+'/>';
                        }
                    } , 'name':'cantidad_a_comprar'
                },
                {'render':
                    function (data, type, row, meta){
                        return '<div name="subtotal" data-key="'+(row.id)+'" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'">'+(row.subtotal?((parseFloat(row.subtotal).toFixed(2))):'')+'</div>';
                    } , 'name':'subtotal'
                },
                {'render':
                    function (data, type, row, meta){
                        let action ='';
    
                        if(row.estado ==7){
                            action = `
                            <div class="btn-group btn-group-sm" role="group" style="cursor: default;">
                                <i class="fas fa-sticky-note fa-2x" style="color:orange" title="${(row.observacion?row.observacion:'Sin Observación')}" ></i>
                            </div>
                            `;
                        }else{
                            action = `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-danger btn-sm activation" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" data-key="${(row.id)}" data-row="${(meta.row)}" data-id_requerimiento="${(row.id_requerimiento?row.id_requerimiento:0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)}"  onclick="ordenView.openModalEliminarItemOrden(this);" ${hasAttrDisabled}>
                                <i class="fas fa-trash fa-sm"></i>
                                </button>
                            </div>
                            `;
                        }
                        return action;
                    }
                }
            ],
            rowCallback: function( row, data ) {
                
                if ( data.estado == '7' )
                { 
                    $('td', row).css({'background-color': 'mistyrose', 'color': 'indianred'});
                }
            },
            "initComplete": function(settings, json) {
                ordenCtrl.calcTotalOrdenDetalleList();
    
            },
            columnDefs: [
                { width: '20px', targets: 0 },
                { width: '20px', targets: 1 },
                { width: '40px', targets: 2 },
                { width: '50px', targets: 3 },
                { width: '20px', targets: 4 ,sClass: 'invisible'},
                { width: '15px', targets: 5 },
                { width: '20px', targets: 6 , sClass: 'invisible'},
                { width: '20px', targets: 7 },
                { width: '20px', targets: 8 },
                { width: '30px', targets: 9, sClass:'text-center' }
            ],
        
            order: [[1, "asc"]]
    
    
        });
    
        let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    }


    updateDetalleOrdenListPrecio(event){
        ordenCtrl.updateDetalleOrdenListPrecio(event)
    }
    updateInputStockComprometido(event){
        // deprecated
    }
    updateDetalleOrdenListCantidadAComprar(event){
        ordenCtrl.updateDetalleOrdenListCantidadAComprar(event)
    }
    openModalEliminarItemOrden(obj){
        ordenCtrl.openModalEliminarItemOrden(obj);

    }

    // mostrar info si esta vinculado con un requerimiento
    eliminarVinculoReq(){
        sessionStorage.removeItem('reqCheckedList');
        window.location.reload();
    }


    // guardar orden
    hasCheckedGuardarEnRequerimiento(){
        let hasCheck = document.querySelector("input[name='guardarEnRequerimiento']").checked;
        return hasCheck;
    }

    get_header_orden_requerimiento(){
        let id_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_orden']").value;
        let id_tp_documento = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_tp_documento']").value;
    
        let id_moneda = document.querySelector("div[type='crear-orden-requerimiento'] select[name='id_moneda']").value;
        let codigo_orden = document.querySelector("div[type='crear-orden-requerimiento'] input[name='codigo_orden']").value;
        let fecha_emision = document.querySelector("div[type='crear-orden-requerimiento'] input[name='fecha_emision']").value;
    
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
    
        let id_trabajador = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_trabajador']").value;
    
        let data = {
            'id_orden':id_orden,
            'id_tp_documento':id_tp_documento,
            'id_moneda':id_moneda, 
            'codigo_orden':codigo_orden, 
            'fecha_emision':fecha_emision, 
            
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
            
            'id_trabajador':id_trabajador, 
    
            'detalle':[]
        }
        
        return data;  
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

function anular_orden_compra(ids){
    baseUrl = '/anular_orden_compra/'+ids;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Orden de Compra anulada con éxito');
                changeStateButton('anular');
                $('#estado label').text('Anulado');
                $('[name=cod_estado]').val('2');
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
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_trabajador']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado']").value='';
    document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent='';
    document.querySelector("var[name='total']").textContent= '';


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