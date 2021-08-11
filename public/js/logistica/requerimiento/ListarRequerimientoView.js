var tempArchivoAdjuntoRequerimientoList = [];
var tempArchivoAdjuntoRequerimientoToDeleteList = [];
var tempArchivoAdjuntoItemList = [];
class ListarRequerimientoView {

    constructor(requerimientoCtrl) {
        this.requerimientoCtrl = requerimientoCtrl;
        this.trazabilidadRequerimiento = new TrazabilidadRequerimiento(requerimientoCtrl);

    }

    // mostrar(meOrAll, idEmpresa=null, idSede=null, idGrupo=null, division=null, idPrioridad=null) {
    //     this.requerimientoCtrl.getListadoElaborados(meOrAll, idEmpresa, idSede, idGrupo, division, idPrioridad).then( (res) =>{
    //         this.construirTablaListadoRequerimientosElaborados(res['data']);
    //     }).catch(function (err) {
    //         console.log(err)
    //         // SWEETALERT 
    //     })

    // }

    initializeEventHandler() {
        document.querySelector("button[class~='handleClickImprimirRequerimientoPdf']").addEventListener("click", this.imprimirRequerimientoPdf.bind(this), false);

    }


    imprimirRequerimientoPdf() {
        var id = document.getElementsByName("id_requerimiento")[0].value;
        window.open('imprimir-requerimiento-pdf/' + id + '/0');

    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if (nodeTbody != null) {
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }
        }
    }

    mostrar() {
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables();
        $('#ListaRequerimientosElaborados').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'elaborados',
                'type': 'POST',
                data: function (params) {
                    return Object.assign(params, Util.objectifyForm($('#form-requerimientosElaborados').serializeArray()))
                }

            },
            'columns': [
                { 'data': 'id_requerimiento', 'name': 'alm_req.id_requerimiento', 'visible': false },
                { 'data': 'priori', 'name': 'adm_prioridad.descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega', 'className': 'text-center' },
                { 'data': 'tipo_requerimiento', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
                { 'data': 'grupo', 'name': 'sis_grupo.descripcion', 'className': 'text-center' },
                { 'data': 'division', 'name': 'division.descripcion', 'className': 'text-center' },
                { 'data': 'monto_total', 'name': 'monto_total', 'className': 'text-right', 'searchable': false },
                { 'data': 'nombre_usuario', 'name': 'nombre_usuario' },
                { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc' },
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro', 'className': 'text-center' },
                { 'data': 'id_requerimiento' }
            ],
            'columnDefs': [

                {
                    'render': function (data, type, row) {
                        return row['termometro'];
                    }, targets: 1
                },
                {
                    'render': function (data, type, row) {
                        return `<label class="lbl-codigo handleClickAbrirRequerimiento" title="Abrir Requerimiento">${row.codigo}</label>`;
                    }, targets: 2
                },
                {
                    'render': function (data, type, row) {
                        return (row['simbolo_moneda']) + (Util.formatoNumero(row['monto_total'], 2));
                    }, targets: 9
                },
                {
                    'render': function (data, type, row) {
                        switch (row['estado']) {
                            case 1:
                                return '<span class="label label-default">' + row['estado_doc'] + '</span>';
                                break;
                            case 2:
                                return '<span class="label label-success">' + row['estado_doc'] + '</span>';
                                break;
                            case 3:
                                return '<span class="label label-warning">' + row['estado_doc'] + '</span>';
                                break;
                            case 5:
                                return '<span class="label label-primary">' + row['estado_doc'] + '</span>';
                                break;
                            case 7:
                                return '<span class="label label-danger">' + row['estado_doc'] + '</span>';
                                break;
                            default:
                                return '<span class="label label-default">' + row['estado_doc'] + '</span>';
                                break;

                        }
                    }, targets: 11, className: 'text-center'
                },
                {
                    'render': function (data, type, row) {
                        let labelOrdenes = '';
                        (row['ordenes_compra']).forEach(element => {
                            labelOrdenes += `<label class="lbl-codigo handleClickAbrirOrden" data-id-orden-compra=${element.id_orden_compra} title="Abrir orden">${element.codigo}</label>`;
                        });
                        return labelOrdenes;
                    }, targets: 13, className: 'text-center'
                },
                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnEditar = '';
                        let btnAnular = '';
                        // let btnMandarAPago = '';
                        let btnDetalleRapido = '<button type="button" class="btn btn-xs btn-info handleClickVerDetalleRequerimientoSoloLectura" data-id-requerimiento="' + row['id_requerimiento'] + '" title="Ver detalle" ><i class="fas fa-eye fa-xs"></i></button>';
                        let btnTrazabilidad = '<button type="button" class="btn btn-xs btn-primary handleClickVerTrazabilidadRequerimiento" title="Trazabilidad"><i class="fas fa-route fa-xs"></i></button>';
                        // if(row.estado ==2){
                        //         btnMandarAPago = '<button type="button" class="btn btn-xs btn-success" title="Mandar a pago" onClick="listarRequerimientoView.requerimientoAPago(' + row['id_requerimiento'] + ');"><i class="fas fa-hand-holding-usd fa-xs"></i></button>';
                        //     }
                        if (row.id_usuario == auth_user.id_usuario && (row.estado == 1 || row.estado == 3)) {
                            btnEditar = '<button type="button" class="btn btn-xs btn-warning handleClickAbrirRequerimiento" title="Editar" ><i class="fas fa-edit fa-xs"></i></button>';
                            btnAnular = '<button type="button" class="btn btn-xs btn-danger handleClickAnularRequerimiento" title="Anular" ><i class="fas fa-times fa-xs"></i></button>';
                        }


                        return containerOpenBrackets + btnDetalleRapido + btnTrazabilidad + btnEditar + btnAnular + containerCloseBrackets;
                    }, targets: 14
                },

            ],
            'initComplete': function () {

                // var table = document.querySelector("table[id='ListaRequerimientosElaborados'] tbody")
                // var buttons = table.querySelectorAll(".handleClickVerDetalleRequerimientoSoloLectura");
                // var i = 0, length = buttons.length;
                // for (i; i < length; i++) {
                //         buttons[i].addEventListener("click", that.verDetalleRequerimientoSoloLectura.bind(this,that), false);
                // }

                $('#ListaRequerimientosElaborados tbody').on("click", "label.handleClickAbrirOrden", function () {
                    let idOrdenCompra = $('#ListaRequerimientosElaborados').DataTable().row($(this).parents("tr")).node().querySelector("label[class~='handleClickAbrirOrden']").dataset.idOrdenCompra;
                    // console.log(idOrdenCompra);
                    that.trazabilidadRequerimiento.abrirOrden(idOrdenCompra);
                });
                $('#ListaRequerimientosElaborados tbody').on("click", "label.handleClickAbrirRequerimiento", function () {
                    let data = $('#ListaRequerimientosElaborados').DataTable().row($(this).parents("tr")).data();
                    that.abrirRequerimiento(data.id_requerimiento);
                });
                $('#ListaRequerimientosElaborados tbody').on("click", "button.handleClickAbrirRequerimiento", function () {
                    let data = $('#ListaRequerimientosElaborados').DataTable().row($(this).parents("tr")).data();
                    that.abrirRequerimiento(data.id_requerimiento);
                });
                $('#ListaRequerimientosElaborados tbody').on("click", "button.handleClickAnularRequerimiento", function () {
                    let data = $('#ListaRequerimientosElaborados').DataTable().row($(this).parents("tr")).data();
                    that.anularRequerimiento($(this),data.id_requerimiento,data.codigo);
                });

                $('#ListaRequerimientosElaborados tbody').on("click", "button.handleClickVerTrazabilidadRequerimiento", function () {
                    let data = $('#ListaRequerimientosElaborados').DataTable().row($(this).parents("tr")).data();
                    that.trazabilidadRequerimiento.verTrazabilidadRequerimientoModal(data, that);
                });

                $('#ListaRequerimientosElaborados tbody').on("click", "button.handleClickVerDetalleRequerimientoSoloLectura", function () {
                    let data = $('#ListaRequerimientosElaborados').DataTable().row($(this).parents("tr")).data();
                    that.verDetalleRequerimientoSoloLectura(data, that);
                });
            }
        });

        $('#ListaReq').DataTable().on("draw", function () {
            resizeSide();
        });
    }

    verDetalleRequerimientoSoloLectura(data, that) {
        let idRequerimiento = data.id_requerimiento;
        $('#modal-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        document.querySelector("div[id='modal-requerimiento'] fieldset[id='group-acciones']").classList.add("oculto");
        document.querySelector("div[id='modal-requerimiento'] button[id='btnRegistrarRespuesta']").classList.add("oculto");

        that.requerimientoCtrl.getRequerimiento(idRequerimiento).then((res) => {
            that.construirSeccionDatosGenerales(res['requerimiento'][0]);
            that.construirSeccionItemsDeRequerimiento(res['det_req'], res['requerimiento'][0]['simbolo_moneda']);
            that.construirSeccionHistorialAprobacion(res['historial_aprobacion']);
            $('#modal-requerimiento div.modal-body').LoadingOverlay("hide", true);

        }).catch(function (err) {
            console.log(err)
        })
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

            document.querySelector("a[class~='handleClickVerAdjuntosRequerimiento']") ? (document.querySelector("a[class~='handleClickVerAdjuntosRequerimiento']").addEventListener("click", this.verAdjuntosRequerimiento.bind(this), false)) : false;

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
                    html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoItem" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoRequerimiento('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
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
                <td>${data[i].descripcion_partida ? data[i].descripcion_partida : ''}</td>
                <td>${data[i].descripcion_centro_costo ? data[i].descripcion_centro_costo : ''}</td>
                <td>${data[i].id_tipo_item == 1 ? (data[i].producto_part_number ? data[i].producto_part_number : data[i].part_number) : '(Servicio)'}</td>
                <td>${data[i].producto_descripcion ? data[i].producto_descripcion : (data[i].descripcion ? data[i].descripcion : '')} </td>
                <td>${data[i].unidad_medida}</td>
                <td style="text-align:center;">${data[i].cantidad}</td>
                <td style="text-align:right;">${simboloMoneda}${Util.formatoNumero(data[i].precio_unitario, 2)}</td>
                <td style="text-align:right;">${simboloMoneda}${(data[i].subtotal ? Util.formatoNumero(data[i].subtotal, 2) : (Util.formatoNumero((data[i].cantidad * data[i].precio_unitario), 2)))}</td>
                <td>${data[i].motivo ? data[i].motivo : ''}</td>
                <td style="text-align: center;"> 
                    ${cantidadAdjuntosItem > 0 ? '<a title="Ver archivos adjuntos de item" style="cursor:pointer;" class="handleClickVerAdjuntosItem' + i + '" >Ver (<span name="cantidadAdjuntosItem">' + cantidadAdjuntosItem + '</span>)</a>' : '-'}
                </td>
            </tr>`);

                document.querySelector("a[class='handleClickVerAdjuntosItem" + i + "']") ? document.querySelector("a[class~='handleClickVerAdjuntosItem" + i + "']").addEventListener("click", this.verAdjuntosItem.bind(this, data[i].id_detalle_requerimiento), false) : false;


            }


        }


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
                    html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoItem" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoItem('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
                }
                html += `</div>
                </td>
                </tr>`;
            }
        });
        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);


    }

    construirSeccionHistorialAprobacion(data) {
        this.limpiarTabla('listaHistorialRevision');
        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                html += `<tr>
                    <td style="text-align:center;">${data[i].nombre_usuario ? data[i].nombre_usuario : ''}</td>
                    <td style="text-align:center;">${data[i].accion ? data[i].accion : ''}${data[i].tiene_sustento == true ? ' (Tiene sustento)' : ''}</td>
                    <td style="text-align:left;">${data[i].detalle_observacion ? data[i].detalle_observacion : ''}</td>
                    <td style="text-align:center;">${data[i].fecha_vobo ? data[i].fecha_vobo : ''}</td>
                </tr>`;
            }
        }
        document.querySelector("tbody[id='body_historial_revision']").insertAdjacentHTML('beforeend', html)

    }
    // requerimientoAPago(idRequerimiento){
    //     requerimientoCtrl.enviarRequerimientoAPago(idRequerimiento).then(function (res) {
    //         if(res >0){
    //             alert('Se envió correctamente a Pago');
    //             listarRequerimientoView.mostrar('ALL');

    //         }
    //     }).catch(function (err) {
    //         console.log(err)
    //     })
    // }

    abrirRequerimiento(idRequerimiento) {
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url = "/logistica/gestion-logistica/requerimiento/elaboracion/index";
        var win = window.open(url, "_self");
        win.focus();
    }


    anularRequerimiento(obj,idRequerimiento,codigo) {
        Swal.fire({
            title: 'Esta seguro que desea anular el requerimiento '+codigo+'?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, anular'

        }).then((result) => {
            if (result.isConfirmed) {
                this.requerimientoCtrl.anularRequerimiento(idRequerimiento).then(function (res) {
                    if (res.estado == 7) {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        obj.closest('tr').fadeOut(500,function(){
                            $(this).remove();
                        });
                        Swal.fire(
                            'Anulado',
                            res.mensaje,
                            'success'
                        );
                    } else {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            'Hubo un problema',
                            res.mensaje,
                            'error'
                        );
                    }
                }).catch(function (err) {
                    console.log(err)
                })


            }
        })


    }



    handleChangeFilterEmpresaListReqByEmpresa(event) {
        this.handleChangeFiltroListado();
        this.requerimientoCtrl.getSedesPorEmpresa(event.target.value).then(function (res) {
            listarRequerimientoView.construirSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirSelectSede(data) {
        let selectSede = document.querySelector('div[type="lista_requerimiento"] select[name="id_sede_select"]');
        let html = '<option value="0">Todas</option>';
        data.forEach(element => {
            html += '<option value="' + element.id_sede + '">' + element.codigo + '</option>'
        });

        selectSede.innerHTML = html;
        document.querySelector('div[type="lista_requerimiento"] select[name="id_sede_select"]').removeAttribute('disabled');

    }

    handleChangeFiltroListado() {
        this.mostrar(document.querySelector("select[name='mostrar_me_all']").value, document.querySelector("select[name='id_empresa_select']").value, document.querySelector("select[name='id_sede_select']").value, document.querySelector("select[name='id_grupo_select']").value, document.querySelector("select[name='division_select']").value, document.querySelector("select[name='id_prioridad_select']").value);

    }

    handleChangeGrupo(event) {
        this.requerimientoCtrl.getListaDivisionesDeGrupo(event.target.value).then(function (res) {
            listarRequerimientoView.construirSelectDivision(res);
        }).catch(function (err) {
            console.log(err)
        })
    }
    construirSelectDivision(data) {
        let selectSede = document.querySelector('div[type="lista_requerimiento"] select[name="division_select"]');
        let html = '<option value="0">Todas</option>';
        data.forEach(element => {
            html += '<option value="' + element.id_division + '">' + element.descripcion + '</option>'
        });

        selectSede.innerHTML = html;
        document.querySelector('div[type="lista_requerimiento"] select[name="division_select"]').removeAttribute('disabled');

    }
}

// const listarRequerimientoView = new ListarRequerimientoView();
