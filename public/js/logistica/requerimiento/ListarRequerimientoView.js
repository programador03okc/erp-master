class ListarRequerimientoView{

    mostrar(meOrAll, idEmpresa, idSede, idGrupo, division, idPrioridad) {
        requerimientoCtrl.getListadoElaborados(meOrAll, idEmpresa, idSede, idGrupo, division, idPrioridad).then(function (res) {
            listarRequerimientoView.construirTablaListadoRequerimientosElaborados(res['data']);
        }).catch(function (err) {
            console.log(err)
        })

    }

    construirTablaListadoRequerimientosElaborados(data) {
        var vardataTables = funcDatatables();
        $('#ListaRequerimientosElaborados').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'order': [[10, 'desc']],
            'bLengthChange': false,
            'serverSide': false,
            'destroy': true,
            'data': data,
            'columns': [
                { 'data': 'priori', 'name': 'adm_prioridad.descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega', 'className': 'text-center' },
                { 'data': 'tipo_requerimiento', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
                { 'data': 'grupo', 'name': 'adm_grupo.descripcion' },
                { 'data': 'division', 'name': 'adm_flujo.nombre' },
                { 'data': 'nombre_usuario', 'name': 'nombre_usuario' },
                { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc' },
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro' }
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        if (row['priori'] == 'Normal') {
                            return '<center> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal" ></i></center>';
                        } else if (row['priori'] == 'Media') {
                            return '<center> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"  ></i></center>';
                        } else if (row['Alta']) {
                            return '<center> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítico"  ></i></center>';
                        } else {
                            return '';
                        }
                    }, targets: 0
                },
                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnEditar = '';
                        if (row.id_usuario == auth_user.id_usuario && row.estado == 3) {
                            btnEditar = '<button type="button" class="btn btn-xs btn-warning" title="Editar" onClick="aprobarRequerimientoView.editarRequerimiento(' + row['id_requerimiento'] + ');"><i class="fas fa-edit fa-xs"></i></button>';
                        }
                        let btnDetalleRapido = '<button type="button" class="btn btn-xs btn-info" title="Ver detalle" onClick="aprobarRequerimientoView.verDetalleRequerimientoSoloLectura(' + row['id_requerimiento'] + ');"><i class="fas fa-eye fa-xs"></i></button>';
                        return containerOpenBrackets + btnDetalleRapido + btnEditar + containerCloseBrackets;
                    }, targets: 11
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
