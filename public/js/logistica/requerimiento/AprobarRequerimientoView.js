
var tempArchivoAdjuntoRequerimientoList = [];
var tempArchivoAdjuntoRequerimientoToDeleteList = [];
var tempArchivoAdjuntoItemList=[];
class AprobarRequerimientoView {

    constructor(requerimientoCtrl){
        this.requerimientoCtrl = requerimientoCtrl;
        this.$fila=null;
        // this.verDetallesEvent();
    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if(nodeTbody!=null){
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }
        }
    }

    addEventToFilterButtons(){
        document.querySelector("select[class~='handleChangeFilterEmpresaListReqByEmpresa']").addEventListener("change", this.handleChangeFilterEmpresaListReqByEmpresa.bind(this), false);
        document.querySelector("select[class~='handleChangeFiltroListadoByEmpresa']").addEventListener("change", this.handleChangeFiltroListado.bind(this), false);
        document.querySelector("select[class~='handleChangeFiltroListadoBySede']").addEventListener("change", this.handleChangeFiltroListado.bind(this), false);
        document.querySelector("select[class~='handleChangeFiltroListadoByGrupo']").addEventListener("change", this.handleChangeFiltroListado.bind(this), false);
        document.querySelector("select[class~='handleChangeFiltroListadoByPrioridad']").addEventListener("change", this.handleChangeFiltroListado.bind(this), false);

        $('#listaDetalleRequerimientoModal').on("click","a.handleClickVerAdjuntosItem", (e)=>{
            this.verAdjuntosItem(e.currentTarget.dataset.idDetalleRequerimiento);
        });
 
        $('#modal-requerimiento').on("click","button.handleClickImprimirRequerimientoPdf", (e)=>{
            this.imprimirRequerimientoPdf(e);
        });
 
        $('#modal-requerimiento').on("change","select.handleChangeUpdateAccion", (e)=>{
            this.updateAccion(e.currentTarget);
        });
        $('#modal-requerimiento').on("click","button.handleClickRegistrarRespuesta", ()=>{
            this.registrarRespuesta();
        });
 
 
    }

    imprimirRequerimientoPdf(){
        var id = document.getElementsByName("id_requerimiento")[0].value;
        window.open('imprimir-requerimiento-pdf/'+id+'/0');
    
    }

    mostrar(idEmpresa, idSede, idGrupo, idPrioridad) {
        this.requerimientoCtrl.getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad).then((res) =>{
            this.construirTablaListaRequerimientosPendientesAprobacion(res['data']);
            console.log(res);
            if(res['mensaje'].length>0){
                console.warn(res['mensaje']);
                    Lobibox.notify('warning', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: res['mensaje'].toString()
                    }); 
            }
            
        }).catch(function (err) {
            console.log(err)
        })

    }


    construirTablaListaRequerimientosPendientesAprobacion(data) {
        // console.log(data);
        let disabledBtn = true;
        let vardataTables = funcDatatables();
        $('#ListaReqPendienteAprobacion').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'destroy': true,
            "order": [[4, "desc"]],
            'data': data,
            'columns': [
                {
                    'render': function (data, type, row) {
                        return row['termometro'];

                        // let prioridad = '';
                        // let thermometerNormal = '<center><i class="fas fa-thermometer-empty green fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad '+row.descripcion_prioridad+'" ></i></center>';
                        // let thermometerAlta = '<center> <i class="fas fa-thermometer-half orange fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad '+row.descripcion_prioridad+'"  ></i></center>';
                        // let thermometerCritica = '<center> <i class="fas fa-thermometer-full red fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad '+row.descripcion_prioridad+'"  ></i></center>';
                        // if (row.id_prioridad == 1) {
                        //     prioridad = thermometerNormal
                        // } else if (row.id_prioridad == 2) {
                        //     prioridad = thermometerAlta
                        // } else if (row.id_prioridad == 3) {
                        //     prioridad = thermometerCritica
                        // }
                        // return prioridad;
                    }
                },
                { 'data': 'codigo', 'name': 'codigo','className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'tipo_requerimiento', 'name': 'tipo_requerimiento' ,'className': 'text-center'},
                { 'data': 'fecha_registro', 'name': 'fecha_registro' ,'className': 'text-center'},
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega' ,'className': 'text-center'},
                { 'data': 'razon_social_empresa', 'name': 'razon_social_empresa' ,'className': 'text-center'},
                { 'data': 'division', 'name': 'division','className': 'text-center' },
                {
                    'render': function (data, type, row) {
                        return (row['simbolo_moneda'])+(Util.formatoNumero(row['monto_total'],2));
                    },'className': 'text-right'
                },
                { 'data': 'observacion', 'name': 'alm_req.observacion' },
                { 'data': 'nombre_usuario', 'name': 'usuario','className': 'text-left' },
                {
                    'render': function (data, type, row) {

                        //switch
                        
                        if(row['estado']==1){
                            return '<span class="label label-default">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==2){
                            return '<span class="label label-success">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==3){
                            return '<span class="label label-warning">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==5){
                            return '<span class="label label-primary">'+row['estado_doc']+'</span>';
                        }else if(row['estado']==7){
                            return '<span class="label label-danger">'+row['estado_doc']+'</span>';
                        }else{
                            return '<span class="label label-default">'+row['estado_doc']+'</span>';

                        }
                    },'className': 'text-center'
                },
                { 'data': 'cantidad_aprobados_total_flujo', 'name': 'cantidad_aprobados_total_flujo','className': 'text-center' },
                {
                    'render': function (data, type, row) {
                        var list_id_rol_aprob = [];
                        var hasAprobacion = 0;
                        var cantidadObservaciones = 0;
                        var hasObservacionSustentadas = 0;



                        if (row.aprobaciones.length > 0) {
                            row.aprobaciones.forEach(element => {
                                list_id_rol_aprob.push(element.id_rol)
                            });

                            roles.forEach(element => {
                                if (list_id_rol_aprob.includes(element.id_rol) == true) {
                                    hasAprobacion += 1;
                                }

                            });
                        }
                        if (row.observaciones.length > 0) {
                            row.observaciones.forEach(element => {
                                cantidadObservaciones += 1;
                                if (element.id_sustentacion > 0) {
                                    hasObservacionSustentadas += 1;
                                }
                            });
                        }


                        if (hasAprobacion == 0) {
                            disabledBtn = '';
                        } else if (hasAprobacion > 0) {
                            disabledBtn = 'disabled';
                        }
                        if (hasObservacionSustentadas != cantidadObservaciones) {
                            disabledBtn = 'disabled';
                        }

                        if (row.estado == 7) {
                            disabledBtn = 'disabled';
                        }
                        let first_aprob = {};
                        // console.log(row.pendiente_aprobacion);
                        if (row.pendiente_aprobacion.length > 0) {
                            first_aprob = row.pendiente_aprobacion.reduce(function (prev, curr) {
                                return prev.orden < curr.orden ? prev : curr;
                            });

                        }
                        // buscar si la primera aprobación su numero de orden se repite en otro pendiente_aprobacion
                        let aprobRolList = [];
                        // console.log(row.pendiente_aprobacion);
                        let pendAprob = row.pendiente_aprobacion;
                        pendAprob.forEach(element => {
                            if (element.orden == first_aprob.orden) {
                                aprobRolList.push(element.id_rol);
                            }
                        });

                        // si el usuario actual su rol le corresponde aprobar
                        // console.log(row.rol_aprobante_id);
                        // console.log(aprobRolList);

                        // si existe varios con mismo orden 
                        if (aprobRolList.length > 1) {
                            // si existe un rol aprobante ya definido en el requerimiento
                            if (row.rol_aprobante_id > 0) {
                                roles.forEach(element => {
                                    if (row.rol_aprobante_id == element.id_rol) {
                                        // if(aprobRolList.includes(element.id_rol)){
                                        disabledBtn = '';
                                    } else {
                                        disabledBtn = 'disabled';

                                    }

                                });
                            } else {
                                roles.forEach(element => {
                                    if (aprobRolList.includes(element.id_rol)) {
                                        disabledBtn = '';
                                    } else {
                                        disabledBtn = 'disabled';

                                    }

                                });
                            }

                        } else {

                            roles.forEach(element => {
                                if (first_aprob.id_rol == element.id_rol) {
                                    disabledBtn = '';
                                } else {
                                    disabledBtn = 'disabled';

                                }

                            });

                        }

                        // onClick="aprobarRequerimientoView.verDetalleRequerimiento('${row['id_requerimiento']}', '${row['id_doc_aprob']}','${row['id_usuario_aprobante']}','${row['id_rol_aprobante']}','${row['id_flujo']}','${row['aprobacion_final_o_pendiente']}');"

                        return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                                    <button type="button" class="btn btn-xs btn-info ver-detalles" title="Ver detalle" 
                                        data-id-requerimiento="${row['id_requerimiento']}"
                                        data-id-doc-aprob="${row['id_doc_aprob']}"
                                        data-id-usuario-aprobante="${row['id_usuario_aprobante']}"
                                        data-id-rol-aprobante="${row['id_rol_aprobante']}"
                                        data-id-flujo="${row['id_flujo']}"
                                        data-aprobacion-final-o-pendiente="${row['aprobacion_final_o_pendiente']}"
                                        data-id-operacion="${row['id_operacion']}"
                                        data-tiene-rol-con-siguiente-aprobacion="${row['tiene_rol_con_siguiente_aprobacion']}"
                                        >
                                        <i class="fas fa-eye fa-xs"></i>
                                    </button>
                                </div></center> `;
                    }
                },
            ],
 
            "createdRow": function (row, data, dataIndex) {
                //switch
                if (data.estado == 2) {
                    $(row.childNodes[9]).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row.childNodes[9]).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row.childNodes[9]).css('color', '#d92b60');
                }

            }
        });
        let tablelistaitem = document.getElementById(
            'ListaReqPendienteAprobacion_wrapper'
        )
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    }

    handleChangeFilterEmpresaListReqByEmpresa(event) {
        this.handleChangeFiltroListado();
        this.requerimientoCtrl.getSedesPorEmpresa(event.target.value).then((res)=> {
            this.construirSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirSelectSede(data) {
        let selectSede = document.querySelector('div[type="aprobar_requerimiento"] select[name="id_sede_select"]');
        let html = '<option value="0">Todas</option>';
        data.forEach(element => {
            html += '<option value="' + element.id_sede + '">' + element.codigo + '</option>'
        });

        selectSede.innerHTML = html;
        document.querySelector('div[type="aprobar_requerimiento"] select[name="id_sede_select"]').removeAttribute('disabled');

    }

    handleChangeFiltroListado() {
        this.mostrar(document.querySelector("select[name='id_empresa_select']").value, document.querySelector("select[name='id_sede_select']").value, document.querySelector("select[name='id_grupo_select']").value, document.querySelector("select[name='id_prioridad_select']").value);

    }





    verDetallesEvent(){
        

        // $('#ListaReqPendienteAprobacion').on('click','.ver-detalles',(event)=>{
        //     this.$fila=$(event.currentTarget).closest("tr");

        //     const $modalRequerimiento=$('#modal-requerimiento');
        //         $modalRequerimiento.find("input[name='idRequerimiento']").val(event.currentTarget.dataset.idRequerimiento);
        //         $modalRequerimiento.find("input[name='idDocumento']").val(event.currentTarget.dataset.idDocAprob);
        //         $modalRequerimiento.find("input[name='idUsuario']").val(event.currentTarget.dataset.idUsuarioAprobante);
        //         $modalRequerimiento.find("input[name='idRolAprobante']").val(event.currentTarget.dataset.idRolAprobante);
        //         $modalRequerimiento.find("input[name='idFlujo']").val(event.currentTarget.dataset.idFlujo);
        //         $modalRequerimiento.find("input[name='aprobacionFinalOPendiente']").val(event.currentTarget.dataset.aprobacionFinalOPendiente);
        //         $modalRequerimiento.find("input[name='idOperacion']").val(event.currentTarget.dataset.idOperacion);
        //         $modalRequerimiento.find("input[name='tieneRolConSiguienteAprobacion']").val(event.currentTarget.dataset.tieneRolConSiguienteAprobacion);
        //         $modalRequerimiento.find("textarea[id='comentario']").val('');
        //         $modalRequerimiento.find("select[id='accion']").val(0);


        //         var customElement = $("<div>", {
        //             "css": {
        //                 "font-size": "24px",
        //                 "text-align": "center",
        //                 "padding": "0px",
        //                 "margin-top": "-400px"
        //             },
        //             "class": "your-custom-class"
        //         });
    
        //         $('#modal-requerimiento div.modal-body').LoadingOverlay("show", {
        //             imageAutoResize: true,
        //             progress: true,
        //             custom: customElement,
        //             imageColor: "#3c8dbc"
        //         });

        //         this.requerimientoCtrl.getRequerimiento(event.currentTarget.dataset.idRequerimiento).done( (res)=> {
        //             this.construirSeccionDatosGenerales(res['requerimiento'][0]);
        //             this.construirSeccionItemsDeRequerimiento(res['det_req'],res['requerimiento'][0]['simbolo_moneda']);
        //             this.construirSeccionHistorialAprobacion(res['historial_aprobacion']);


        //             $('#modal-requerimiento div.modal-body').LoadingOverlay("hide", true);
        
        //         }).catch(function (err) {
        //             //mostrar notificacion de eeror
        //             console.log(err)

        //         }).always(()=>{
        //             $('#modal-requerimiento div.modal-body').LoadingOverlay("hide", true);

        //             $('#modal-requerimiento').modal({
        //                 show: true,
        //                 backdrop: 'true'
        //             });    
        //         });

        // });
 

    }


    construirSeccionDatosGenerales(data) {
        // console.log(data);
        document.querySelector("div[id='modal-requerimiento'] input[name='id_requerimiento']").value = data.id_requerimiento;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = data.codigo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = data.concepto;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = data.razon_social_empresa;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='division']").textContent = data.division;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent = data.tipo_requerimiento;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = data.prioridad;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='fecha_entrega']").textContent = data.fecha_entrega;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='solicitado_por']").textContent = (data.para_stock_almacen == true ? 'Para stock almacén' : (data.nombre_trabajador ? data.nombre_trabajador : '-'));
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = data.periodo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = data.persona;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='observacion']").textContent = data.observacion;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='tipo_impuesto']").textContent = data.tipo_impuesto==1?'Detracción':data.tipo_impuesto ==2?'Renta':'No aplica';
        document.querySelector("div[id='modal-requerimiento'] span[name='simboloMoneda']").textContent = data.simbolo_moneda;
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] span[name='simbolo_moneda']").textContent = data.simbolo_moneda;
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] label[name='total']").textContent = data.monto_total;

        tempArchivoAdjuntoRequerimientoList = [];
        if (data.adjuntos.length > 0) {
            document.querySelector("td[id='adjuntosRequerimiento']").innerHTML = `<a title="Ver archivos adjuntos de requerimiento" style="cursor:pointer;"  class="handleClickVerAdjuntosRequerimiento" >
            Ver (<span name="cantidadAdjuntosRequerimiento">${data.adjuntos.length}</span>)
            </a>`;
            (data.adjuntos).forEach(element => {
                tempArchivoAdjuntoRequerimientoList.push({
                    'id': element.id_adjunto,
                    'id_requerimiento': element.id_requerimiento,
                    'archivo': element.archivo,
                    'nameFile': element.archivo,
                    'categoria_adjunto_id': element.categoria_adjunto_id,
                    'categoria_adjunto': element.categoria_adjunto,
                    'fecha_registro': element.fecha_registro,
                    'estado': element.estado
                });

            });

            document.querySelector("a[class~='handleClickVerAdjuntosRequerimiento']").addEventListener("click", this.verAdjuntosRequerimiento.bind(this), false);

        }

        let tamañoSelectAccion = document.querySelector("div[id='modal-requerimiento'] select[id='accion']").length;
        if (data.estado == 3) {
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value == 1) {
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].setAttribute('disabled', true)
                }
            }
        } else {
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value == 1) {
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].removeAttribute('disabled')
                }
            }
        }
    }

  
    verAdjuntosRequerimiento() {

        this.limpiarTabla('listaArchivosRequerimiento');
        $('#modal-adjuntar-archivos-requerimiento').modal({
            show: true
        });

        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');

        let html = '';
        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            tempArchivoAdjuntoRequerimientoList.forEach(element => {
                if (element.estado == 1) {
                    html += `<tr>
                    <td style="text-align:left;">${element.archivo}</td>
                    <td style="text-align:left;">${element.categoria_adjunto}</td>
                    <td style="text-align:center;">
                        <div class="btn-group" role="group">`;
                    html += `<button type="button" class="btn btn-info btn-md handleClickDescargarArchivoRequerimientoCabecera" name="btnDescargarArchivoCabecera" title="Descargar" data-id="('${element.id}')" ><i class="fas fa-paperclip"></i></button>`;
                    html += `</div>
                    </td>
                    </tr>`;

                }
            });
        }
        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html)

    }

    construirSeccionItemsDeRequerimiento(data, simboloMoneda) {
        this.limpiarTabla('listaDetalleRequerimientoModal');
        tempArchivoAdjuntoItemList = [];
        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                let cantidadAdjuntosItem = 0;
                cantidadAdjuntosItem = data[i].adjuntos.length;
                if (cantidadAdjuntosItem > 0) {
                    (data[i].adjuntos).forEach(element => {
                        if (element.estado == 1) {
                            tempArchivoAdjuntoItemList.push(
                                {
                                    id: element.id_adjunto,
                                    idRegister: element.id_detalle_requerimiento,
                                    nameFile: element.archivo,
                                    dateFile: element.fecha_registro,
                                    estado: element.estado
                                }
                            );
                        }

                    });
                }
            document.querySelector("tbody[id='body_item_requerimiento']").insertAdjacentHTML('beforeend', `<tr>
                <td>${i + 1}</td>
                <td title="${data[i].id_partida >0 ?data[i].descripcion_partida.toUpperCase() :(data[i].id_partida_pi >0?data[i].descripcion_partida_presupuesto_interno.toUpperCase() : '')}" >${data[i].id_partida >0 ?data[i].codigo_partida :(data[i].id_partida_pi >0?data[i].codigo_partida_presupuesto_interno : '')}</td>
                <td title="${data[i].id_centro_costo>0?data[i].descripcion_centro_costo.toUpperCase():''}">${data[i].codigo_centro_costo ? data[i].codigo_centro_costo : ''}</td>
                <td>${data[i].id_tipo_item == 1 ? (data[i].producto_part_number ? data[i].producto_part_number : data[i].part_number) : '(Servicio)'}${data[i].tiene_transformacion==true?'<br><span class="label label-default">Con Transformación</span>':''} </td>
                <td>${data[i].producto_descripcion ? data[i].producto_descripcion : (data[i].descripcion ? data[i].descripcion : '')} </td>
                <td>${data[i].unidad_medida !=null?data[i].unidad_medida:''}</td>
                <td style="text-align:center;">${data[i].cantidad>=0?data[i].cantidad:''}</td>
                <td style="text-align:right;">${simboloMoneda}${Util.formatoNumero(data[i].precio_unitario, 2)}</td>
                <td style="text-align:right;">${simboloMoneda}${(data[i].subtotal ? Util.formatoNumero(data[i].subtotal, 2) : (Util.formatoNumero((data[i].cantidad*data[i].precio_unitario),2)))}</td>
                <td>${data[i].motivo !=null? data[i].motivo : ''}</td>
                <td>${data[i].estado_doc !=null ? data[i].estado_doc : ''}</td>
                <td style="text-align: center;"> 
                    ${cantidadAdjuntosItem>0?'<a title="Ver archivos adjuntos de item" style="cursor:pointer;" class="handleClickVerAdjuntosItem" data-id-detalle-requerimiento="'+data[i].id_detalle_requerimiento+'" >Ver (<span name="cantidadAdjuntosItem">'+cantidadAdjuntosItem+'</span>)</a>':'-'}
                </td>
            </tr>`);
                
            }


        }
 

    }

    construirSeccionHistorialAprobacion(data) {
        this.limpiarTabla('listaHistorialRevision');
        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                html += `<tr>
                    <td style="text-align:center;">${data[i].nombre_corto ? data[i].nombre_corto : ''}</td>
                    <td style="text-align:center;">${data[i].accion ? data[i].accion : ''}${data[i].tiene_sustento ==true ? ' ': ''}</td>
                    <td style="text-align:left;">${data[i].detalle_observacion ? data[i].detalle_observacion : ''}</td>
                    <td style="text-align:center;">${data[i].fecha_vobo ? data[i].fecha_vobo : ''}</td>
                </tr>`;
            }
        }
        document.querySelector("tbody[id='body_historial_revision']").insertAdjacentHTML('beforeend', html)

    }

    verAdjuntosItem(idDetalleRequerimiento) {
        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        this.limpiarTabla('listaArchivos');
        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');
        let html = '';
        tempArchivoAdjuntoItemList.forEach(element => {
            if (element.idRegister == idDetalleRequerimiento) {
                html += `<tr>
                <td style="text-align:left;">${element.nameFile}</td>
                <td style="text-align:center;">
                    <div class="btn-group" role="group">`;
                if (Number.isInteger(element.id)) {
                    html += `<button type="button" class="btn btn-info btn-md handleClickDescargarArchivoRequerimientoDetalle" name="btnDescargarArchivoDetalle" title="Descargar"  data-id="('${element.id}')"><i class="fas fa-paperclip"></i></button>`;
                }
                html += `</div>
                </td>
                </tr>`;
            }
        });
        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);


    }

    updateAccion(obj) {
        if (obj.value > 0) {
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.remove("has-error")
            if (obj.closest('div[class~="form-group"]').querySelector("span")) {
                obj.closest('div[class~="form-group"]').querySelector("span").remove();
            }
        } else {
            obj.closest('div[class~="form-group"]').classList.add("has-error")
            if (obj.closest('div[class~="form-group"]').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una acción)';
                obj.closest('div[class~="form-group"]').appendChild(newSpanInfo);
            }
        }
    }

    registrarRespuesta() {
        
        if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").value > 0) {
            document.getElementById('btnRegistrarRespuesta').setAttribute('disabled',true);
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.remove("has-error")
            if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span")) {
                document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span").remove();
            }

            let payload = {
                'accion': document.querySelector("div[id='modal-requerimiento'] select[id='accion']").value,
                'comentario': document.querySelector("div[id='modal-requerimiento'] textarea[id='comentario']").value,
                'idRequerimiento': document.querySelector("div[id='modal-requerimiento'] input[name='idRequerimiento']").value,
                'idDocumento': document.querySelector("div[id='modal-requerimiento'] input[name='idDocumento']").value,
                'idUsuario': document.querySelector("div[id='modal-requerimiento'] input[name='idUsuario']").value,
                'idRolAprobante': document.querySelector("div[id='modal-requerimiento'] input[name='idRolAprobante']").value,
                'idFlujo': document.querySelector("div[id='modal-requerimiento'] input[name='idFlujo']").value,
                'aprobacionFinalOPendiente': document.querySelector("div[id='modal-requerimiento'] input[name='aprobacionFinalOPendiente']").value,
                'idOperacion': document.querySelector("div[id='modal-requerimiento'] input[name='idOperacion']").value,
                'tieneRolConSiguienteAprobacion': document.querySelector("div[id='modal-requerimiento'] input[name='tieneRolConSiguienteAprobacion']").value,
            };
            $('#modal-requerimiento').animate({ scrollTop: 0 }, 'slow')

            // loading
            var customElement = $("<div>", {
                "css": {
                    "font-size": "24px",
                    "text-align": "center",
                    "padding": "0px",
                    "margin-top": "-400px"
                },
                "class": "your-custom-class",
                "text": "Registrando respuesta..."
            });

            $('#modal-requerimiento div.modal-body').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                custom: customElement,
                imageColor: "#3c8dbc"
            });
            // end loading 

            //  enviar request
            this.requerimientoCtrl.guardarRespuesta(payload).done((res) =>{
                console.log(res);
                if (res.id_aprobacion > 0) {
                    Lobibox.notify('success', {
                        title:false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Se ha registrado su respuesta.'
                    });

                    $('#modal-requerimiento').modal('hide');
                    this.$fila.fadeOut(300,function(){
                        this.remove();
                    })

                } else {
                    Swal.fire(
                        'Lo sentimos hubo un error en el servidor al intentar guardar la respuesta, por favor vuelva a intentarlo',
                        res.mensaje,
                        'error'
                    );
                    $('#modal-requerimiento div.modal-body').LoadingOverlay("hide", true);
                }

            }).fail(function (err) {
                console.log(err)
                Swal.fire(
                    'Error en el servidor',
                    err,
                    'error'
                );
            }).always(()=>{

                $('#modal-requerimiento div.modal-body').LoadingOverlay("hide", true);
                document.getElementById('btnRegistrarRespuesta').removeAttribute('disabled');

            });

        } else {
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.add("has-error")
            if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una acción)';
                document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').appendChild(newSpanInfo);
            }

        }
    }


}


