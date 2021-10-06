var rutaListaOrdenesPropias;
function inicializarRutasListadoOrdenesPropias(
    _rutaListaOrdenesPropias
    ) {
    
    rutaListaOrdenesPropias = _rutaListaOrdenesPropias;
    vista_extendida();

    mostrar_ordenes_propias_pendientes();
}

function mostrar_ordenes_propias_pendientes(){
    
    let año_publicacion = document.querySelector("div[id='ocams_pendientes'] select[id='descripcion_año_publicacion_op_pendientes']").value;
    listar_ordenes_propias('ListaOrdenesPropiasPendientes',null,año_publicacion,'PENDIENTES');
}
function mostrar_ordenes_propias_vinculadas(){
    
    let año_publicacion = document.querySelector("div[id='ocams_vinculadas'] select[id='descripcion_año_publicacion_op_vinculadas']").value;
    listar_ordenes_propias('ListaOrdenesPropiasVinculadas',null,año_publicacion,'VINCULADAS');

}


function listar_ordenes_propias(tabla,id_empresa= null,year_publicacion =null, condicion=null){
    // let id_empresa = document.querySelector("form[id='form-ordenesPropias'] select[id='id_empresa_select']").value;

    $('#'+tabla).DataTable({
        'processing': true,
        'serverSide': true,
        'bDestroy': true,
        // bInfo:     false,
        'paging':   true,
        'searching': true,
        'bLengthChange': false,

        'iDisplayLength':50,
        'ajax': {
            // url:'/logistica/requerimiento/lista/'+id_empresa+'/'+id_sede+'/'+id_grupo,
            url:rutaListaOrdenesPropias+'/'+id_empresa+'/'+year_publicacion+'/'+condicion,
            type:'GET'
            // data: {_token: "{{csrf_token()}}"}
        },
        'columns':[
            {'render': function (data, type, row,meta){
                return meta.row +1;
                }
            },
            {'render': function (data, type, row){
                return `${row['orden_am']}`;
                }
            },
            {'data':'empresa', 'name':'empresas.empresa'},
            {'data':'am', 'name':'acuerdo_marco.descripcion_corta'},
            {'data':'entidad', 'name':'entidades.nombre'},
            {'data':'fecha_publicacion', 'name':'fecha_publicacion'},
            {'data':'estado_oc', 'name':'estado_oc'},
            {'data':'fecha_estado', 'name':'fecha_estado'},
            {'data':'estado_entrega', 'name':'estado_entrega'},
            {'data':'fecha_entrega', 'name':'fecha_entrega'},
            {'data':'monto_total', 'name':'monto_total'},
            {'render': function (data, type, row){
                let estado_cc ='';
                if(row['id_estado_aprobacion_cc']==1){
                    estado_cc = '<p class="text-muted">'+row['estado_aprobacion_cc']+'</p>';
                }else if(row['id_estado_aprobacion_cc']==2){
                    estado_cc = '<p class="text-primary">'+row['estado_aprobacion_cc']+'</p>';
                }else if(row['id_estado_aprobacion_cc']==3){
                    estado_cc = '<p class="text-success">'+row['estado_aprobacion_cc']+'</p>';
                }else if(row['id_estado_aprobacion_cc']==4){
                    estado_cc = '<p class="text-danger">'+row['estado_aprobacion_cc']+'</p>';
                }
                return estado_cc;

            }
            },
            {'render': function (data, type, row){
                if(row['tipo_cuadro'] == 0){
                    return 'Venta';

                }else if(row['tipo_cuadro'] ==1){
                    return 'Acuerdo Marco';

                }else{
                    return '';
                }
                }
            },
            {'render': function (data, type, row){
                let containerOpenBrackets='<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                let containerCloseBrackets='</div></center>';
                let btnVerOrdenFisica='';
                let btnVerOrdenElectronica='';
                let btnGenerarRequerimiento='';
                let btnIrRequerimiento='';
                let btnVerTransformacion='';
                // let btnEditar='<button type="button" class="btn btn-sm btn-log bg-primary" title="Ver o editar" onClick="editarListaReq(' +row['id_requerimiento']+ ');"><i class="fas fa-edit fa-xs"></i></button>';
                // let btnDetalleRapido='<button type="button" class="btn btn-default" title="Ver OC Fisica" onclick="location.href='+row['url_oc_fisica']+';"><i class="fas fa-eye fa-xs"></i></button>';
                btnVerOrdenElectronica='<a class="btn btn-sm btn-default" title="O/C electrónica" href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra='+row['id']+'&ImprimirCompleto=1" target="_blank"><i class="far fa-file-pdf"></i></a>';
                btnVerOrdenFisica='<a class="btn btn-sm btn-default" title="O/C escaneada" href="'+row['url_oc_fisica']+'" target="_blank"><i class="far fa-file-alt"></i></a>';
                if(row['id_estado_aprobacion_cc'] ==3){
                    if(row['id_requerimiento'] >0){
                        btnGenerarRequerimiento='';
                    }else{
                        btnGenerarRequerimiento='<button type="button" class="btn btn-sm btn-success" title="Generar Requerimiento" onClick="generarRequerimientoByOrdenCompraPropia('+row['tipo_cuadro']+','+row['id_cc']+','+row['id_estado_aprobacion_cc']+')"><i class="fas fa-registered"></i></button>';
                    }
                }
                else if(row['id_estado_aprobacion_cc'] ==2){
                    if(row['id_requerimiento'] >0){
                        btnGenerarRequerimiento='<button type="button" class="btn btn-sm bg-blue" title="Generar Requerimiento" disabled><i class="fas fa-registered"></i></button>';
                    }else{
                        btnGenerarRequerimiento='<button type="button" class="btn btn-sm bg-blue" title="Generar Requerimiento" onClick="generarRequerimientoByOrdenCompraPropia('+row['tipo_cuadro']+','+row['id_cc']+','+row['id_estado_aprobacion_cc']+')"><i class="fas fa-registered"></i></button>';
                    }
                }
                else{
                    btnGenerarRequerimiento='';
                }
                if(row['id_requerimiento'] >0){
                    if(row.cantidad_producto_con_transformacion != null){
                        if( row.cantidad_producto_con_transformacion >0  ){
                            btnVerTransformacion='<a type="button" class="btn btn-sm btn-default" style="background:#d8c74ab8;" title="Transformación" onClick="verTransformacion('+row['id_requerimiento'] +')"><i class="fas fa-exchange-alt"></i></a>';
                        }else{

                            btnVerTransformacion='<a type="button" class="btn btn-sm btn-default" style=" background:#b498d0;" title="Transformación" onClick="verTransformacion('+row['id_requerimiento'] +')"><i class="fas fa-exchange-alt"></i></a>';
                        }
                    }
                    btnIrRequerimiento='<a type="button" class="btn btn-sm btn-info" title="Ir Requerimiento '+row['codigo_requerimiento']+'" onClick="irRequerimientoByOrdenCompraPropia('+row['id_requerimiento'] +')"><i class="fas fa-file-prescription"></i></a>';
                }else{
                    btnIrRequerimiento='';
                }
                return containerOpenBrackets+btnVerOrdenElectronica+btnVerOrdenFisica+btnGenerarRequerimiento+btnVerTransformacion+btnIrRequerimiento+containerCloseBrackets;
                }
            },
        ],
        "createdRow": function( row, data, dataIndex){
            // console.log(row.childNodes[0]);
            if(data.cantidad_producto_con_transformacion != null){
                if( data.cantidad_producto_con_transformacion >0  ){
                    $(row.childNodes[0]).css('background-color', '#d8c74ab8');
                    $(row.childNodes[0]).css('font-weight', 'bold');
                }
                else if( data.cantidad_producto_con_transformacion == 0  ){
        
                    $(row.childNodes[0]).css('background-color', '#b498d0');
                    $(row.childNodes[0]).css('font-weight', 'bold');
                }
            }else{
                $(row).css('background-color', '#b498d0');

            }
        },
        initComplete: function () {
            let ListaOrdenesPropias_wrapper = document.getElementById(
                tabla+'_wrapper'
            )
            ListaOrdenesPropias_wrapper.childNodes[0].childNodes[0].hidden = true;
        
            let ListaOrdenesPropias_filter = document.getElementById(
                tabla+'_filter'
            )
            ListaOrdenesPropias_filter.children[0].children[0].style.width = '95%'; 
        
            document.querySelector("table[id='"+tabla+"']").tHead.style.fontSize = '11px',
            document.querySelector("table[id='"+tabla+"']").tBodies[0].style.fontSize = '11px';

        },
        columnDefs: [
            {
                "targets": [ 0 ],
                "visible": false
            },
            { "width": 150, "targets": 13 }

        ],
        'order': [
            [5, 'desc']
        ]
    });

    $('#'+tabla).DataTable().on("draw", function(){
        resizeSide();
    });



}



// controle y acciones 
function handleChangeFilterEmpresaListOrdenesPropiasPendientesByEmpresa(e) {
    let año_publicacion = document.querySelector("div[id='ocams_pendientes'] select[id='descripcion_año_publicacion_op_pendientes']").value;
    listar_ordenes_propias('ListaOrdenesPropiasPendientes',e.target.value,año_publicacion,'PENDIENTES');
}
function handleChangeFilterEmpresaListOrdenesPropiasPendientesByAñoPublicacion(e) {
    let id_empresa = document.querySelector("div[id='ocams_pendientes'] select[id='id_empresa_select_op_pendientes']").value;
    listar_ordenes_propias('ListaOrdenesPropiasPendientes',id_empresa,e.target.value,'PENDIENTES');
}
function handleChangeFilterEmpresaListOrdenesPropiasVinculadasByEmpresa(e) {
    let año_publicacion = document.querySelector("div[id='ocams_vinculadas'] select[id='descripcion_año_publicacion_op_vinculadas']").value;
    listar_ordenes_propias('ListaOrdenesPropiasVinculadas',e.target.value,año_publicacion,'VINCULADAS');
}
function handleChangeFilterEmpresaListOrdenesPropiasVinculadasByAñoPublicacion(e) {
    let id_empresa = document.querySelector("div[id='ocams_vinculadas'] select[id='id_empresa_select_op_vinculadas']").value;
    listar_ordenes_propias('ListaOrdenesPropiasVinculadas',id_empresa,e.target.value,'VINCULADAS');
}
function generarRequerimientoByOrdenCompraPropia(tipo_cuadro,id_cc,id_estado_aprobacion_cc){
    // console.log(tipo_cuadro,id_cc,id_estado_aprobacion_cc);
    sessionStorage.removeItem('ordenP_Cuadroc')

    if(id_estado_aprobacion_cc == 2){
        $('#modal-justificar-generar-requerimiento').modal({
            show: true,
            backdrop: 'static'
        });
        document.querySelector("div[id='modal-justificar-generar-requerimiento'] label[name='id_cc']").textContent = id_cc;
        let data = {
            'tipo_cuadro':tipo_cuadro,
            'id_cc':id_cc,
            'id_estado_aprobacion_cc':id_estado_aprobacion_cc
        };
        // console.log(data);
        sessionStorage.setItem('ordenP_Cuadroc', JSON.stringify(data));
        
    }else{
        let data = {
            'tipo_cuadro':tipo_cuadro,
            'id_cc':id_cc,
            'id_estado_aprobacion_cc':id_estado_aprobacion_cc
        };
        // console.log(data);
        sessionStorage.setItem('ordenP_Cuadroc', JSON.stringify(data));
        let url ="/necesidades/requerimiento/elaboracion/index";
        var win = window.open(url, '_blank');
        win.focus();
    }

}

function guardarJustificacionGenerarRequerimiento(){
    sessionStorage.removeItem('justificacion_generar_requerimiento')

    let data = {
        id_cc: document.querySelector("div[id='modal-justificar-generar-requerimiento'] label[name='id_cc']").textContent,
        contenido:document.querySelector("div[id='modal-justificar-generar-requerimiento'] textarea[id='motivo_generar_requerimiento']").value
    }
    sessionStorage.setItem('justificacion_generar_requerimiento', JSON.stringify(data));
    window.location.href = '/necesidades/requerimiento/elaboracion/index'; //using a named route
}

function irRequerimientoByOrdenCompraPropia(idRequerimiento){

    localStorage.setItem('id_requerimiento', idRequerimiento);
    let url ="/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    win.focus();

}
function verTransformacion(idRequerimiento){
    $('#modal-ver-transformacion').modal({
        show: true,
    });

    listarProductoTransformado(idRequerimiento,true);
    listarProductosBase(idRequerimiento,false);

}


// producto transformado - modal 
function listarProductoTransformado(idRequerimiento,tieneTransformacion){
    getProductosBaseOTransformado(idRequerimiento,tieneTransformacion).then(function(res) {
        if(res.status ==200){
            construirTablaProductoTransformado(res.data);
        }
    }).catch(function(err) {
        console.log(err)
    })
}
function listarProductosBase(idRequerimiento,tieneTransformacion){
    getProductosBaseOTransformado(idRequerimiento,tieneTransformacion).then(function(res) {
        if(res.status ==200){
            construirTablaProductoBase(res.data);
        }
    }).catch(function(err) {
        console.log(err)
    })
}

function getProductosBaseOTransformado(idRequerimiento,tieneTransformacion){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url:`listado/producto-base-o-transformado/${idRequerimiento}/${tieneTransformacion}`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function(err) {
            reject(err) // Reject the promise and go to catch()
            }
            });
        });
}

function construirTablaProductoTransformado(data){
    var vardataTables = funcDatatables();

    $('#tableProductoTransformado').DataTable({
        'dom': vardataTables[1],
        'buttons': [
        ],
        'language' : vardataTables[0],
        'serverSide' : false,
        'bInfo': false,
        "bLengthChange" : false,
        "scrollCollapse": true,
        'paging': false,
        'searching': false,
        'bDestroy' : true,
        'data': data,
        'columns': [
            {
                'render': function (data, type, row) {
                    return `${row['part_number']?row['part_number']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['descripcion_adicional']?row['descripcion_adicional']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['unidad_medida']?row['unidad_medida']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['cantidad']? row['cantidad']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['precio_unitario']?(row['simbolo_moneda']+row['precio_unitario']):''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['subtotal']?(row['simbolo_moneda']+row['subtotal']):''}`;
                }
            }
        ]
    });
}
function construirTablaProductoBase(data){
    var vardataTables = funcDatatables();

    $('#tableProductoBase').DataTable({
        'dom': vardataTables[1],
        'buttons': [
        ],
        'language' : vardataTables[0],
        'serverSide' : false,
        'bInfo': false,
        "bLengthChange" : false,
        "scrollCollapse": true,
        'paging': false,
        'searching': false,
        'bDestroy' : true,
        'data': data,
        'columns': [
            {
                'render': function (data, type, row) {
                    return `${row['part_number']?row['part_number']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['descripcion_adicional']?row['descripcion_adicional']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['unidad_medida']?row['unidad_medida']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['cantidad']? row['cantidad']:''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['precio_unitario']?(row['simbolo_moneda']+row['precio_unitario']):''}`;
                }
            },
            {
                'render': function (data, type, row) {
                    return `${row['subtotal']?(row['simbolo_moneda']+row['subtotal']):''}`;
                }
            }
        ]
    });
}
