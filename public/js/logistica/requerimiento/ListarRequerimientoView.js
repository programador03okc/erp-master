class ListarRequerimientoView{

    mostrar(meOrAll, idEmpresa, idSede, idGrupo, division, idPrioridad) {
        requerimientoCtrl.getListadoElaborados(meOrAll, idEmpresa, idSede, idGrupo, division, idPrioridad).then(function (res) {
            listarRequerimientoView.construirTablaListadoRequerimientosElaborados(res['data']);
        }).catch(function (err) {
            console.log(err)
        })

    }

    construirTablaListadoRequerimientosElaborados(data) {
        vista_extendida();
        var vardataTables = funcDatatables();
        $('#ListaRequerimientosElaborados').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': false,
            'destroy': true,
            'data': data,
            'columns': [
                { 'data': 'id_requerimiento', 'name': 'alm_req.id_requerimiento', 'visible': false },
                { 'data': 'priori', 'name': 'adm_prioridad.descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega', 'className': 'text-center' },
                { 'data': 'tipo_requerimiento', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
                { 'data': 'grupo', 'name': 'adm_grupo.descripcion', 'className':'text-center' },
                { 'data': 'division', 'name': 'division.descripcion' },
                { 'data': 'monto_total', 'name': 'monto_total' },
                { 'data': 'nombre_usuario', 'name': 'nombre_usuario' },
                { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc' },
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro' },
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
                        return `<label class="lbl-codigo" title="Abrir Requerimiento" onClick="listarRequerimientoView.abrirRequerimiento(${row.id_requerimiento})">${row.codigo}</label>`;
                    }, targets: 2
                },
                {
                    'render': function (data, type, row) {
                        return (row['simbolo_moneda'])+(Util.formatoNumero(row['monto_total'],2));
                    }, targets: 9
                },
                {
                    'render': function (data, type, row) {
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
                    }, targets: 11, className:'text-center'
                },
                {
                    'render': function (data, type, row) {
                        let labelOrdenes='';
                        (row['ordenes_compra']).forEach(element => {
                            labelOrdenes += `<label class="lbl-codigo" title="Abrir orden" onclick="trazabilidadRequerimientoView.abrirOrden(${element.id_orden_compra})">${element.codigo}</label>`;
                        });
                        return labelOrdenes;
                    }, targets: 13, className:'text-center'
                },
                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnEditar = '';
                        let btnAnular = '';
                        // let btnMandarAPago = '';
                        let btnDetalleRapido = '<button type="button" class="btn btn-xs btn-info" title="Ver detalle" onClick="aprobarRequerimientoView.verDetalleRequerimientoSoloLectura(' + row['id_requerimiento'] + ');"><i class="fas fa-eye fa-xs"></i></button>';
                        let btnTrazabilidad = '<button type="button" class="btn btn-xs btn-primary" title="Trazabilidad" onClick="trazabilidadRequerimientoView.verTrazabilidadRequerimientoModal(' + row['id_requerimiento'] + ');"><i class="fas fa-route fa-xs"></i></button>';
                        // if(row.estado ==2){
                        //         btnMandarAPago = '<button type="button" class="btn btn-xs btn-success" title="Mandar a pago" onClick="listarRequerimientoView.requerimientoAPago(' + row['id_requerimiento'] + ');"><i class="fas fa-hand-holding-usd fa-xs"></i></button>';
                        //     }
                        if (row.id_usuario == auth_user.id_usuario && (row.estado == 1 || row.estado == 3)) {
                            btnEditar = '<button type="button" class="btn btn-xs btn-warning" title="Editar" onClick="listarRequerimientoView.abrirRequerimiento(' + row['id_requerimiento'] + ');"><i class="fas fa-edit fa-xs"></i></button>';
                            btnAnular = '<button type="button" class="btn btn-xs btn-danger" title="Anular" onClick="listarRequerimientoView.anularRequerimiento(' + row['id_requerimiento'] + ');"><i class="fas fa-trash fa-xs"></i></button>';
                        }
                        return containerOpenBrackets + btnDetalleRapido + btnTrazabilidad + btnEditar + btnAnular + containerCloseBrackets;
                    }, targets: 14
                },

            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.estado == 2) {
                    $(row.childNodes[9]).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row.childNodes[9]).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row.childNodes[9]).css('color', '#d92b60');
                }
                // if (data.cantidad_sustentos > 0) {
                //     $(row.childNodes[9]).css('color', '#337ab7');
                // }
            },
            'initComplete': function () {
            }
        });

        $('#ListaReq').DataTable().on("draw", function () {
            resizeSide();
        });

        $('#ListaReq tbody').on('click', 'tr', function () {
            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#ListaReq').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
        });
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

    abrirRequerimiento(idRequerimiento){
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url = "/logistica/gestion-logistica/requerimiento/elaboracion/index";
        var win = window.open(url, "_self");
        win.focus(); 
    }

    
    anularRequerimiento(idRequerimiento){
        requerimientoCtrl.anularRequerimiento(idRequerimiento).then(function (res) {
            if(res.estado==7){
                alert(`${res.mensaje}`);
                location.reload();
                $('#wrapper-okc').LoadingOverlay("hide", true);
            }else{
                $('#wrapper-okc').LoadingOverlay("hide", true);
                alert(`${res.mensaje}`);
            }
        }).catch(function (err) {
            console.log(err)
        })
    }

    

    handleChangeFilterEmpresaListReqByEmpresa(event) {
        this.handleChangeFiltroListado();
        requerimientoCtrl.getSedesPorEmpresa(event.target.value).then(function (res) {
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
        requerimientoCtrl.getListaDivisionesDeGrupo(event.target.value).then(function (res) {
            listarRequerimientoView.construirSelectDivision(res);
        }).catch(function (err) {
            console.log(err)
        })
    }
    construirSelectDivision(data) {
        let selectSede = document.querySelector('div[type="lista_requerimiento"] select[name="division_select"]');
        let html = '<option value="0">Todas</option>';
        data.forEach(element => {
            html += '<option value="' + element.nombre + '">' + element.nombre + '</option>'
        });

        selectSede.innerHTML = html;
        document.querySelector('div[type="lista_requerimiento"] select[name="division_select"]').removeAttribute('disabled');

    }
}

const listarRequerimientoView = new ListarRequerimientoView();
