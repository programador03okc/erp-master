
var tempObjectBtnPartida;
var tempObjectBtnCentroCostos;
var tempObjectBtnInputFile;
var tempIdRegisterActive;
var tempCentroCostoSelected;
var tempArchivoAdjuntoItemList = [];
var tempArchivoAdjuntoRequerimientoList = [];
class RequerimientoView {
    init() {
        this.agregarFilaEvent();
        // $('[name=periodo]').val(today.getFullYear());

    }
    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        while (nodeTbody.children.length > 0) {
            nodeTbody.removeChild(nodeTbody.lastChild);
        }
    }
    // cabecera requerimiento
    changeMonedaSelect(e) {
        let moneda = e.target.value == 1 ? 'S/' : '$';

        document.querySelector("form[id='form-requerimiento'] span[name='simboloMoneda']").textContent = moneda;
        document.querySelector("div[name='montoMoneda']").textContent = moneda;
        if (document.querySelector("form[id='form-requerimiento'] table span[class='moneda']")) {
            document.querySelectorAll("form[id='form-requerimiento'] span[class='moneda']").forEach(element => {
                element.textContent = moneda;
            });
        }
        // document.querySelector("form[id='form-requerimiento'] table span[class='moneda']") ? document.querySelector("form[id='form-requerimiento'] table span[class='moneda']").textContent = moneda : null;
        document.querySelector("form[id='form-requerimiento'] table span[name='simbolo_moneda']").textContent = moneda;
    }

    changeOptEmpresaSelect(e) {
        this.getDataSelectSede(e.target.value);
    }

    getDataSelectSede(idEmpresa = null) {
        if (idEmpresa > 0) {
            requerimientoCtrl.obtenerSede(idEmpresa).then(function (res) {
                requerimientoView.llenarSelectSede(res);
                requerimientoView.seleccionarAmacen(res)
                requerimientoView.llenarUbigeo();
            }).catch(function (err) {
                console.log(err)
            })
        }
        return false;
    }

    llenarSelectSede(array) {

        let selectElement = document.querySelector("div[id='input-group-sede'] select[name='sede']");

        if (selectElement.options.length > 0) {
            let i, L = selectElement.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }


        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_sede;
            if (element.codigo == 'LIMA' || element.codigo == 'Lima') { // default sede lima
                option.setAttribute('selected', 'selected');

            }
            option.setAttribute('data-ubigeo', element.id_ubigeo);
            option.setAttribute('data-name-ubigeo', element.ubigeo_descripcion);
            selectElement.add(option);
        });

        if (array.length > 0) {
            this.updateSede(selectElement);
        }

    }

    seleccionarAmacen(data) {
        // let firstSede = data[0].id_sede;
        let selectAlmacen = document.querySelector("div[id='input-group-almacen'] select[name='id_almacen']");
        if (selectAlmacen.options.length > 0) {
            let i, L = selectAlmacen.options.length - 1;
            for (i = L; i > 0; i--) {
                if (selectAlmacen.options[i].dataset.idEmpresa == document.querySelector("select[id='empresa']").value) {
                    if ([4, 10, 11, 12, 13, 14].includes(parseInt(selectAlmacen.options[i].dataset.idSede)) == true) { ///default almacen lima
                        selectAlmacen.options[i].setAttribute('selected', true);
                    }
                }
            }
        }
    }

    llenarUbigeo() {
        let ubigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        let nameUbigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
        document.querySelector("input[name='ubigeo']").value = ubigeo;
        document.querySelector("input[name='name_ubigeo']").value = nameUbigeo;
        //let sede = $('[name=sede]').val();
    }

    changeOptUbigeo(e) {
        let ubigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        let nameUbigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;

        document.querySelector("input[name='ubigeo']").value = ubigeo;
        document.querySelector("input[name='name_ubigeo']").value = nameUbigeo;
        this.cargarAlmacenes($('[name=sede]').val());
    }

    cargarAlmacenes(sede) {
        if (sede !== '') {
            requerimientoCtrl.obtenerAlmacenes(sede).then(function (res) {
                let option = '';
                for (let i = 0; i < res.length; i++) {
                    if (res.length == 1) {
                        option += '<option data-id-sede="' + res[i].id_sede + '" data-id-empresa="' + res[i].id_empresa + '" value="' + res[i].id_almacen + '" selected>' + res[i].codigo + ' - ' + res[i].descripcion + '</option>';

                    } else {
                        option += '<option data-id-sede="' + res[i].id_sede + '" data-id-empresa="' + res[i].id_empresa + '" value="' + res[i].id_almacen + '">' + res[i].codigo + ' - ' + res[i].descripcion + '</option>';

                    }
                }
                $('[name=id_almacen]').html('<option value="0" disabled selected>Elija una opción</option>' + option);
            }).catch(function (err) {
                console.log(err)
            })
        }
    }

    changeStockParaAlmacen(event) {

        if (event.target.checked) {
            document.querySelector("div[id='input-group-asignar_trabajador']").classList.add("oculto");
        } else {
            document.querySelector("div[id='input-group-asignar_trabajador']").classList.remove("oculto");
        }
    }

    changeProyecto(event) {

        tempCentroCostoSelected = {
            'id': event.target.options[event.target.selectedIndex].getAttribute('data-id-centro-costo'),
            'codigo': event.target.options[event.target.selectedIndex].getAttribute('data-codigo-centro-costo'),
            'descripcion': event.target.options[event.target.selectedIndex].getAttribute('data-descripcion-centro-costo')
        };
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        if (tempCentroCostoSelected.id > 0) {
            if (tbodyChildren.length > 0) {
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("input[class='centroCosto']").value = tempCentroCostoSelected.id;
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").setAttribute('title', tempCentroCostoSelected.codigo);
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").textContent = tempCentroCostoSelected.descripcion;
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('disabled', true);
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('title', 'El centro de costo esta asignado a un proyecto');
                }
            }

        } else {
            alert("El proyecto seleccionado no tiene un centro de costo preasignado, puede seleccionar manualmente")
            if (tbodyChildren.length > 0) {
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("input[class='centroCosto']").value = '';
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").setAttribute('title', '');
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").textContent = '';
                    tbodyChildren[i].querySelector("button[name='centroCostos']").removeAttribute('disabled');
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('title', '');
                }
            }
        }


        let codigoProyecto = event.target.options[event.target.selectedIndex].getAttribute('data-codigo');

        document.querySelector("form[id='form-requerimiento'] input[name='codigo_proyecto']").value = codigoProyecto;
    }

    updateConcepto(obj) {
        if (obj.value.length > 0) {
            obj.closest('div').classList.remove("has-error");
            if (obj.closest("div").querySelector("span")) {
                obj.closest("div").querySelector("span").remove();
            }
        } else {
            obj.closest('div').classList.add("has-error");
        }
    }
    updateEmpresa(obj) {
        if (obj.value.length > 0) {
            obj.closest('div').classList.remove("has-error");
            if (obj.closest("div").querySelector("span")) {
                obj.closest("div").querySelector("span").remove();

            }
        } else {
            obj.closest('div').classList.add("has-error");
        }
    }
    updateSede(obj) {
        if (obj.value.length > 0) {
            obj.closest('div').classList.remove("has-error");
            if (obj.closest("div").querySelector("span")) {
                obj.closest("div").querySelector("span").remove();
            }
        } else {
            obj.closest('div').classList.add("has-error");
        }
    }
    updateFechaLimite(obj) {
        if (obj.value.length > 0) {
            obj.closest('div').classList.remove("has-error");
            if (obj.closest("div").querySelector("span")) {
                obj.closest("div").querySelector("span").remove();
            }
        } else {
            obj.closest('div').classList.add("has-error");
        }
    }


    // detalle requerimiento

    makeId() {
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for (let i = 0; i < 12; i++) {
            ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }


    agregarFilaEvent() {
        document.querySelector("button[id='btn-add-producto']").addEventListener('click', (event) => {

            vista_extendida();

            let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td></td>
            <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info" name="partida" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="partida" name="idPartida[]" hidden>
                </div>
            </td>
            <td><p class="descripcion-centro-costo" title="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.codigo : ''}">${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.descripcion : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary" name="centroCostos" onclick="requerimientoView.cargarModalCentroCostos(this)" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="centroCosto" name="idCentroCosto[]" value="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.id : ''}" hidden>
                </div>
            </td>
            <td><input class="form-control input-sm" type="text" name="partNumber[]" placeholder="Part number"></td>
            <td>
                <div class="form-group">
                    <textarea class="form-control input-sm descripcion" name="descripcion[]" placeholder="Descripción" onkeyup ="requerimientoView.updateDescripcionItem(this);"></textarea></td>
                </div>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm cantidad text-right" type="number" min="1" name="cantidad[]" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Cantidad">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm precio text-right" type="number" min="0" name="precioUnitario[]" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Precio U."></td>
                </div>  
            <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
            <td>
                <div class="btn-group" role="group">
                    <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                    <input type="hidden" class="idRegister" name="idRegister[]" value="${this.makeId()}">
                    <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)" >
                        <i class="fas fa-paperclip"></i>
                        <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                    </button> 
                    <button type="button" class="btn btn-danger btn-xs" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
            </tr>`);

            this.updateContadorItem();

        });
        document.querySelector("button[id='btn-add-servicio']").addEventListener('click', (event) => {

            vista_extendida();

            // let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            // let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td></td>
            <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info" name="partida" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="partida" name="idPartida[]" hidden>
                </div>
                </td>
                <td><p class="descripcion-centro-costo" title="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.codigo : ''}">${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.descripcion : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary" name="centroCostos" onclick="requerimientoView.cargarModalCentroCostos(this)" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button> 
                <div class="form-group">
                    <input type="text" class="centroCosto" name="idCentroCosto[]" value="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.id : ''}" hidden>
                </div>
            </td>
            <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
            <td>
                <div class="form-group">
                    <textarea class="form-control input-sm descripcion" name="descripcion[]" placeholder="Descripción" onkeyup ="requerimientoView.updateDescripcionItem(this);"></textarea>
                </div>
            </td>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm cantidad text-right" type="number" min="1" name="cantidad[]" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Cantidad">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm precio text-right" type="number" min="0" name="precioUnitario[]" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Precio U.">
                </div>
            </td>
            <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
            <td>
                <div class="btn-group" role="group">
                    <input type="hidden" class="tipoItem" name="tipoItem[]" value="2">
                    <input type="hidden" class="idRegister" name="idRegister[]" value="${this.makeId()}">
                    <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)" >
                        <i class="fas fa-paperclip"></i>
                        <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                    </button>
                    <button type="button" class="btn btn-danger btn-xs" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
            </tr>`);

            this.updateContadorItem();

        });
    }

    updateContadorItem() {
        let childrenTableTbody = document.querySelector("tbody[id='body_detalle_requerimiento']").children;

        for (let index = 0; index < childrenTableTbody.length; index++) {
            childrenTableTbody[index].firstElementChild.textContent = index + 1
        }
    }
    autoUpdateSubtotal() {

        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let i = 0; i < tbodyChildren.length; i++) {
            requerimientoView.updateSubtotal(tbodyChildren[i]);
        }
    }

    updateSubtotal(obj) {
        let tr = obj.closest("tr");
        let cantidad = parseFloat(tr.querySelector("input[class~='cantidad']").value);
        let precioUnitario = parseFloat(tr.querySelector("input[class~='precio']").value);
        let subtotal = (cantidad * precioUnitario);
        tr.querySelector("span[class='subtotal']").textContent = Util.formatoNumero(subtotal, 2);
        this.calcularTotal();
    }


    updatePartidaItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }
    }
    updateCentroCostoItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }
    }

    updateCantidadItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }
    updatePrecioItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }
    updateDescripcionItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }

    calcularTotal() {
        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento']");
        let childrenTableTbody = TableTBody.children;
        let total = 0;
        for (let index = 0; index < childrenTableTbody.length; index++) {
            // console.log(childrenTableTbody[index]);
            let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad']").value ? childrenTableTbody[index].querySelector("input[class~='cantidad']").value : 0);
            let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value ? childrenTableTbody[index].querySelector("input[class~='precio']").value : 0);
            total += (cantidad * precioUnitario);
        }
        document.querySelector("label[name='total']").textContent = Util.formatoNumero(total, 2);
    }

    // partidas 
    cargarModalPartidas(obj) {
        tempObjectBtnPartida = obj;
        let id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
        let id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
        let usuarioProyectos = false;
        grupos.forEach(element => {
            if (element.id_grupo == 3) { // proyectos
                usuarioProyectos = true
            }
        });
        if (id_grupo > 0) {
            $('#modal-partidas').modal({
                show: true,
                backdrop: 'true'
            });
            this.listarPartidas(id_grupo, id_proyecto > 0 ? id_proyecto : null);
        } else {
            alert("Ocurrio un problema, no se puedo seleccionar el grupo al que pertence el usuario.");
        }
    }

    listarPartidas(idGrupo, idProyecto) {
        requerimientoCtrl.obtenerListaPartidas(idGrupo, idProyecto).then((res) => {
            this.construirListaPartidas(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirListaPartidas(data) {
        let html = '';
        let isVisible = '';
        data['presupuesto'].forEach(resup => {
            html += ` 
            <div id='${resup.codigo}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading" style="margin: 0; cursor: pointer;" onclick="requerimientoView.apertura(${resup.id_presup}); requerimientoView.changeBtnIcon(this);">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${resup.descripcion} 
                </h5>
                <div id="pres-${resup.id_presup}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" width="100%" style="font-size:0.9em">
                        <tbody> 
            `;

            data['titulos'].forEach(titulo => {
                html += `
                <tr id="com-${titulo.id_titulo}">
                    <td><strong>${titulo.codigo}</strong></td>
                    <td><strong>${titulo.descripcion}</strong></td>
                    <td class="right ${isVisible}"><strong>S/${Util.formatoNumero(titulo.total, 2)}</strong></td>
                </tr> `;

                data['partidas'].forEach(partida => {
                    if (titulo.codigo == partida.cod_padre) {
                        html += `<tr id="par-${partida.id_partida}">
                            <td style="width:15%; text-align:left;" name="codigo">${partida.codigo}</td>
                            <td style="width:75%; text-align:left;" name="descripcion">${partida.des_pardet}</td>
                            <td style="width:15%; text-align:right;" name="importe_total" class="right ${isVisible}" data-presupuesto-total="${partida.importe_total}" >S/${Util.formatoNumero(partida.importe_total, 2)}</td>
                            <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs" onclick="requerimientoView.selectPartida(${partida.id_partida});">Seleccionar</button></td>
                        </tr>`;
                    }
                });
            });
            html += `
                    </tbody>
                </table>
            </div>
        </div>`;
        });
        document.querySelector("div[id='listaPartidas']").innerHTML = html;
    }

    apertura(idPresup) {
        if ($("#pres-" + idPresup + " ").hasClass('oculto')) {
            $("#pres-" + idPresup + " ").removeClass('oculto');
            $("#pres-" + idPresup + " ").addClass('visible');
        } else {
            $("#pres-" + idPresup + " ").removeClass('visible');
            $("#pres-" + idPresup + " ").addClass('oculto');
        }
    }

    changeBtnIcon(obj) {
        if (obj.children[0].className == 'fas fa-chevron-right') {

            obj.children[0].classList.replace('fa-chevron-right', 'fa-chevron-down')
        } else {
            obj.children[0].classList.replace('fa-chevron-down', 'fa-chevron-right')
        }
    }

    selectPartida(idPartida) {
        let codigo = $("#par-" + idPartida + " ").find("td[name=codigo]")[0].innerHTML;
        let descripcion = $("#par-" + idPartida + " ").find("td[name=descripcion]")[0].innerHTML;
        let presupuestoTotal = $("#par-" + idPartida + " ").find("td[name=importe_total]")[0].dataset.presupuestoTotal;

        tempObjectBtnPartida.nextElementSibling.querySelector("input").value = idPartida;
        tempObjectBtnPartida.textContent = 'Cambiar';

        let tr = tempObjectBtnPartida.closest("tr");
        tr.querySelector("p[class='descripcion-partida']").dataset.idPartida = idPartida;
        tr.querySelector("p[class='descripcion-partida']").textContent = descripcion
        tr.querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal = presupuestoTotal;
        tr.querySelector("p[class='descripcion-partida']").setAttribute('title', codigo);

        this.updatePartidaItem(tempObjectBtnPartida.nextElementSibling.querySelector("input"));
        $('#modal-partidas').modal('hide');
        tempObjectBtnPartida = null;

        this.calcularPresupuestoUtilizadoYSaldoPorPartida();
    }

    calcularPresupuestoUtilizadoYSaldoPorPartida() {
        let tempPartidasActivas = [];
        let partidaAgregadas = [];
        let subtotalItemList = [];
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;

        for (let index = 0; index < tbodyChildren.length; index++) {
            if (tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida > 0) {
                if (!partidaAgregadas.includes(tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida)) {
                    partidaAgregadas.push(tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida);
                    tempPartidasActivas.push({
                        'id_partida': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida,
                        'codigo': tbodyChildren[index].querySelector("p[class='descripcion-partida']").title,
                        'descripcion': tbodyChildren[index].querySelector("p[class='descripcion-partida']").textContent,
                        'presupuesto_total': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal,
                        'presupuesto_utilizado': 0,
                        'saldo': 0
                    });
                }

                subtotalItemList.push({
                    'id_partida': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida,
                    'subtotal': (tbodyChildren[index].querySelector("input[class~='cantidad']").value > 0 ? tbodyChildren[index].querySelector("input[class~='cantidad']").value : 0) * (tbodyChildren[index].querySelector("input[class~='precio']").value > 0 ? tbodyChildren[index].querySelector("input[class~='precio']").value : 0)
                });

            }
        }


        for (let p = 0; p < tempPartidasActivas.length; p++) {
            for (let i = 0; i < subtotalItemList.length; i++) {
                if (tempPartidasActivas[p].id_partida == subtotalItemList[i].id_partida) {
                    tempPartidasActivas[p].presupuesto_utilizado += subtotalItemList[i].subtotal;
                }
            }
        }

        for (let p = 0; p < tempPartidasActivas.length; p++) {
            tempPartidasActivas[p].saldo = tempPartidasActivas[p].presupuesto_total - (tempPartidasActivas[p].presupuesto_utilizado > 0 ? tempPartidasActivas[p].presupuesto_utilizado : 0);
        }


        this.validarPresupuestoUtilizadoYSaldoPorPartida(tempPartidasActivas);
        this.construirTablaPresupuestoUtilizadoYSaldoPorPartida(tempPartidasActivas);
        // console.log(tempPartidasActivas);
    }
    validarPresupuestoUtilizadoYSaldoPorPartida(data) {
        let mensajeAlerta = '';
        data.forEach(partida => {
            if (partida.saldo < 0) {
                mensajeAlerta += `La partida ${partida.codigo} - ${partida.descripcion} a excedido el presupuesto asignado, tiene un saldo actual de ${Util.formatoNumero(partida.saldo, 2)}. \n`
            }
        });
        if (mensajeAlerta.length > 0) {
            alert(mensajeAlerta);
        }
    }

    construirTablaPresupuestoUtilizadoYSaldoPorPartida(data) {
        requerimientoView.limpiarTabla('listaPartidasActivas');
        data.forEach(element => {
            document.querySelector("tbody[id='body_partidas_activas']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td>${element.codigo}</td>
                <td>${element.descripcion}</td>
                <td style="text-align:right;"><span>S/</span>${Util.formatoNumero(element.presupuesto_total, 2)}</td>
                <td style="text-align:right;"><span>S/</span>${Util.formatoNumero(element.presupuesto_utilizado, 2)}</td>
                <td style="text-align:right; color:${element.saldo >= 0 ? '#333' : '#dd4b39'}"><span>S/</span>${Util.formatoNumero(element.saldo, 2)}</td>
            </tr>`);

        });

    }

    //centro de costos
    cargarModalCentroCostos(obj) {
        tempObjectBtnCentroCostos = obj;

        $('#modal-centro-costos').modal({
            show: true
        });
        this.listarCentroCostos();
    }

    listarCentroCostos() {
        requerimientoCtrl.obtenerCentroCostos().then(function (res) {
            requerimientoView.construirCentroCostos(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirCentroCostos(data) {
        let html = '';
        data.forEach((padre, index) => {
            if (padre.id_padre == null) {
                html += `
                <div id='${index}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading" style="margin: 0; cursor: pointer;" onclick="requerimientoView.apertura(${index}); requerimientoView.changeBtnIcon(this);">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${padre.descripcion} 
                </h5>
                <div id="pres-${index}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" width="100%" style="font-size:0.9em">
                        <tbody>`;

                data.forEach(hijo => {
                    if (padre.id_centro_costo == hijo.id_padre) {
                        if ((hijo.id_padre > 0) && (hijo.estado == 1)) {
                            if (hijo.nivel == 2) {
                                html += `
                                <tr id="com-${hijo.id_centro_costo}">
                                    <td><strong>${hijo.codigo}</strong></td>
                                    <td><strong>${hijo.descripcion}</strong></td>
                                    <td style="width:5%; text-align:center;"></td>
                                </tr> `;
                            }
                        }
                        data.forEach(hijo3 => {
                            if (hijo.id_centro_costo == hijo3.id_padre) {
                                if ((hijo3.id_padre > 0) && (hijo3.estado == 1)) {
                                    if (hijo3.nivel == 3) {
                                        html += `
                                        <tr id="com-${hijo3.id_centro_costo}">
                                            <td>${hijo3.codigo}</td>
                                            <td>${hijo3.descripcion}</td>
                                            <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs" onclick="requerimientoView.selectCentroCosto(${hijo3.id_centro_costo},'${hijo3.codigo}','${hijo3.descripcion}');">Seleccionar</button></td>
                                        </tr> `;
                                    }
                                }
                            }
                        });
                    }


                });
                html += `
                </tbody>
            </table>
        </div>
    </div>`;
            }
        });
        document.querySelector("div[name='centro-costos-panel']").innerHTML = html;

    }


    selectCentroCosto(idCentroCosto, codigo, descripcion) {


        tempObjectBtnCentroCostos.nextElementSibling.querySelector("input").value = idCentroCosto;
        tempObjectBtnCentroCostos.textContent = 'Cambiar';

        let tr = tempObjectBtnCentroCostos.closest("tr");
        tr.querySelector("p[class='descripcion-centro-costo']").textContent = descripcion
        tr.querySelector("p[class='descripcion-centro-costo']").setAttribute('title', codigo);
        this.updateCentroCostoItem(tempObjectBtnCentroCostos.nextElementSibling.querySelector("input"));
        $('#modal-centro-costos').modal('hide');
        tempObjectBtnCentroCostos = null;
        // componerTdItemDetalleRequerimiento();
    }

    eliminarItem(obj) {
        let tr = obj.closest("tr");
        tr.remove();
        this.updateContadorItem();
        this.calcularTotal();
    }

    //adjunto cabecera requerimiento 

    adjuntarArchivoRequerimiento() {
        $('#modal-adjuntar-archivos-requerimiento').modal({
            show: true
        });

        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[class='bootstrap-filestyle input-group'] input[type='text']").classList.add('oculto');
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] span[class='buttonText']").textContent = "Agregar archivo";
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');

        requerimientoView.limpiarTabla('listaArchivosRequerimiento');

        this.listarAdjuntosDeCabecera();

    }

    listarAdjuntosDeCabecera() {

        requerimientoCtrl.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
            requerimientoView.construirTablaAdjuntosRequerimiento(tempArchivoAdjuntoRequerimientoList, categoriaAdjuntoList);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaAdjuntosRequerimiento(data, categoriaAdjuntoList) {
        let html = '';
        data.forEach(element => {
            html += `<tr id="${element.id}" style="text-align:center">
        <td style="text-align:left;">${element.nameFile}</td>
        <td>
            <select class="form-control" name="categoriaAdjunto" onChange="ArchivoAdjunto.changeCategoriaAdjunto(this)">
        `;
            categoriaAdjuntoList.forEach(categoria => {
                if (element.category == categoria.id_categoria_adjunto) {
                    html += `<option value="${categoria.id_categoria_adjunto}" selected >${categoria.descripcion}</option>`

                } else {
                    html += `<option value="${categoria.id_categoria_adjunto}">${categoria.descripcion}</option>`
                }
            });
            html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">`;
            if (Number.isInteger(element.id)) {
                html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoRequerimiento" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoRequerimieto('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
            }
            html += `<button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoRequerimiento" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoRequerimiento(this,'${element.id}');" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;
        });
        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html);
    }

    agregarAdjuntoRequerimiento(event) {
        let archivoAdjunto = new ArchivoAdjunto(event.target.files);
        archivoAdjunto.addFileLevelRequerimiento();
    }

    // adjuntos detalle requerimiento

    adjuntarArchivoItem(obj) {

        tempIdRegisterActive = obj.closest('td').querySelector("input[class~='idRegister']").value;
        tempObjectBtnInputFile = obj;

        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] span[class='buttonText']").textContent = 'Agregar archivo';
        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[class='bootstrap-filestyle input-group'] input[type='text']").classList.add('oculto');

        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');

        requerimientoView.limpiarTabla('listaArchivos');
        this.listarAdjuntosDeItem();

    }

    listarAdjuntosDeItem() {
        let html = '';
        tempArchivoAdjuntoItemList.forEach(element => {
            if (tempIdRegisterActive == element.idRegister) {
                html += `<tr>
                <td style="text-align:left;">${element.nameFile}</td>
                <td style="text-align:center;">
                <div class="btn-group" role="group">`;
                if (Number.isInteger(element.id)) {
                    html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoItem" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoItem('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
                }
                html += `<button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoItem" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoItem(this,'${element.id}');" ><i class="fas fa-trash-alt"></i></button>`;
                html += `</div>
                </td>
                </tr>`;
            }
        });
        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);
    }

    agregarAdjuntoItem(event) {
        let archivoAdjunto = new ArchivoAdjunto(event.target.files);
        archivoAdjunto.addFileLevelItem();
    }

    // guardar requerimiento

    actionGuardarEditarRequerimiento() {

        let continuar = true;
        if (document.querySelector("tbody[id='body_detalle_requerimiento']").childElementCount == 0) {
            alert("Ingrese por lo menos un producto/servicio");
            return false;
        }

        if (document.querySelector("input[name='concepto']").value == '') {
            continuar = false;
            if (document.querySelector("input[name='concepto']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Ingrese un concepto/motivo)';
                document.querySelector("input[name='concepto']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("input[name='concepto']").closest('div').classList.add('has-error');
            }

        }

        if (document.querySelector("select[name='empresa']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='empresa']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una empresa)';
                document.querySelector("select[name='empresa']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='empresa']").closest('div').classList.add('has-error');
            }
        }

        if (document.querySelector("select[name='sede']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='sede']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una sede)';
                document.querySelector("select[name='sede']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='sede']").closest('div').classList.add('has-error');
            }

        }

        if (document.querySelector("input[name='fecha_entrega']").value == '') {
            continuar = false;
            if (document.querySelector("input[name='fecha_entrega']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una fecha de entrega)';
                document.querySelector("input[name='fecha_entrega']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("input[name='fecha_entrega']").closest('div').classList.add('has-error');
            }

        }

        if (document.querySelector("select[name='tipo_requerimiento']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='tipo_requerimiento']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione un tipo)';
                document.querySelector("select[name='tipo_requerimiento']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='tipo_requerimiento']").closest('div').classList.add('has-error');
            }

        }

        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let index = 0; index < tbodyChildren.length; index++) {

            if (tbodyChildren[index].querySelector("input[class~='partida']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='partida']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese una partida';
                    tbodyChildren[index].querySelector("input[class~='partida']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='partida']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }
            if (tbodyChildren[index].querySelector("input[class~='centroCosto']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese un centro de costo';
                    tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }

            if (tbodyChildren[index].querySelector("input[class~='cantidad']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese una cantidad';
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }

            if (tbodyChildren[index].querySelector("input[class~='precio']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese un precio';
                    tbodyChildren[index].querySelector("input[class~='precio']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }

            if (tbodyChildren[index].querySelector("textarea[class~='descripcion']")) {
                if (tbodyChildren[index].querySelector("textarea[class~='descripcion']").value == '') {
                    continuar = false;
                    if (tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("span") == null) {
                        let newSpanInfo = document.createElement("span");
                        newSpanInfo.classList.add('text-danger');
                        newSpanInfo.textContent = 'Ingrese una descripción';
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').appendChild(newSpanInfo);
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                    }
                }


            }
        }

        if (continuar) {
            console.log("se va a guardar");

            let formData = new FormData($('#form-requerimiento')[0]);
            let ItemWithIdRegisterList = [];
            if (tempArchivoAdjuntoItemList.length > 0) {
                const inputIdRegister = document.querySelectorAll("input[class~='idRegister']");
                inputIdRegister.forEach(element => {
                    ItemWithIdRegisterList.push(element.value);
                });
                tempArchivoAdjuntoItemList.forEach(element => {
                    if (ItemWithIdRegisterList.includes(element.idRegister) == true) {
                        formData.append(`archivoAdjuntoItem${element.idRegister}[]`, element.file, element.nameFile);
                    }
                });

            }

            if (tempArchivoAdjuntoRequerimientoList.length > 0) {
                tempArchivoAdjuntoRequerimientoList.forEach(element => {
                    formData.append(`archivoAdjuntoRequerimiento${element.category}[]`, element.file, element.nameFile);
                });

            }

            let typeActionForm = document.querySelector("form[id='form-requerimiento']").getAttribute("type"); //  register | edition

            if (typeActionForm == 'register') {
                $.ajax({
                    type: 'POST',
                    url: 'guardar-requerimiento',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: function (data) { // Are not working with dataType:'jsonp'

                        // $('#modal-loader').modal({backdrop: 'static', keyboard: false});
                        var customElement = $("<div>", {
                            "css": {
                                "font-size": "24px",
                                "text-align": "center",
                                "padding": "0px",
                                "margin-top": "-400px"
                            },
                            "class": "your-custom-class",
                            "text": "Guardando requerimiento..."
                        });

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            custom: customElement,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: function (response) {
                        if (response.id_requerimiento > 0) {
                            alert(`Requerimiento guardado con código: ${response.codigo}. La página se recargará para que pueda volver a crear un requerimiento.`);
                            location.reload();
                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            alert(response.mensaje);
                        }
                    },
                    fail: function (jqXHR, textStatus, errorThrown) {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        alert("Hubo un problema al guardar el requerimiento. Por favor actualice la página e intente de nuevo");
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
            if (typeActionForm == 'edition') {
                $.ajax({
                    type: 'POST',
                    url: 'actualizar-requerimiento',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: function (data) {
                        var customElement = $("<div>", {
                            "css": {
                                "font-size": "24px",
                                "text-align": "center",
                                "padding": "0px",
                                "margin-top": "-400px"
                            },
                            "class": "your-custom-class",
                            "text": "Actualizando requerimiento..."
                        });

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            custom: customElement,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: function (response) {
                        if (response.id_requerimiento > 0) {
                            alert(`Requerimiento actualizado.`);
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            alert(response.mensaje);

                        }
                        changeStateButton('historial'); //init.js
                    },
                    fail: function (jqXHR, textStatus, errorThrown) {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        alert("Hubo un problema al actualizar el requerimiento. Por favor actualice la página e intente de nuevo");
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });


            }


        } else {
            alert("Por favor ingrese los datos faltantes en el formulario");
            console.log("no se va a guardar");
        }
    }

    mostrarRequerimiento(data) {
        console.log(data);

        if (data.hasOwnProperty('requerimiento')) {
            var btnImprimirRequerimiento = document.getElementsByName("btn-imprimir-requerimento-pdf");
            disabledControl(btnImprimirRequerimiento, false);
            var btnAdjuntosRequerimiento = document.getElementsByName("btn-adjuntos-requerimiento");
            disabledControl(btnAdjuntosRequerimiento, false);
            var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
            disabledControl(btnTrazabilidadRequerimiento, false);

            requerimientoView.mostrarCabeceraRequerimiento(data['requerimiento'][0]);
            if (data.hasOwnProperty('det_req')) {
                requerimientoView.mostrarDetalleRequerimiento(data['det_req']);
            }
        } else {
            alert("El requerimiento que intenta cargar no existe");
        }
    }

    mostrarCabeceraRequerimiento(data) {
        // console.log(auth_user);
        // document.querySelector("input[name='id_usuario_session']").value =data.
        document.querySelector("input[name='id_usuario_req']").value = data.id_usuario;
        document.querySelector("input[name='id_estado_doc']").value = data.id_estado_doc;
        document.querySelector("input[name='id_requerimiento']").value = data.id_requerimiento;
        document.querySelector("span[id='codigo_requerimiento']").textContent = data.codigo;
        // document.querySelector("input[name='cantidad_aprobaciones']").value =data.
        // document.querySelector("input[name='confirmacion_pago']").value =data.
        // document.querySelector("input[name='fecha_creacion_cc']").value =data.
        // document.querySelector("input[name='id_cc']").value =data.
        // document.querySelector("input[name='tipo_cuadro']").value =data.
        // document.querySelector("input[name='tiene_transformacion']").value =data.
        // document.querySelector("input[name='justificacion_generar_requerimiento']").value =data.
        document.querySelector("input[name='id_grupo']").value = data.id_grupo;
        document.querySelector("input[name='estado']").value = data.id_estado_doc;
        document.querySelector("span[id='estado_doc']").textContent = data.estado_doc;
        document.querySelector("input[name='fecha_requerimiento']").value = data.fecha_requerimiento;
        document.querySelector("input[name='concepto']").value = data.concepto;
        document.querySelector("select[name='moneda']").value = data.id_moneda;
        document.querySelector("select[name='periodo']").value = data.id_periodo;
        document.querySelector("select[name='prioridad']").value = data.id_prioridad;
        document.querySelector("select[name='rol_usuario']").value = data.id_rol;
        document.querySelector("select[name='empresa']").value = data.id_empresa;
        requerimientoView.getDataSelectSede(data.id_empresa);
        document.querySelector("select[name='sede']").value = data.id_sede;
        document.querySelector("input[name='fecha_entrega']").value = moment(data.fecha_entrega, "DD-MM-YYYY").format("YYYY-MM-DD");
        document.querySelector("select[name='division']").value = data.division_id;
        document.querySelector("select[name='tipo_requerimiento']").value = data.id_tipo_requerimiento;
        document.querySelector("input[name='id_trabajador']").value = data.trabajador_id;
        document.querySelector("input[name='nombre_trabajador']").value = data.nombre_trabajador;
        document.querySelector("select[name='fuente_id']").value = data.fuente_id;
        document.querySelector("select[name='fuente_det_id']").value = data.fuente_det_id;
        // document.querySelector("input[name='montoMoneda']").textContent =data.
        document.querySelector("input[name='monto']").value = data.monto;
        document.querySelector("select[name='id_almacen']").value = data.id_almacen;
        // document.querySelector("input[name='descripcion_grupo']").value =data.
        document.querySelector("input[name='codigo_proyecto']").value = data.codigo_proyecto;
        document.querySelector("select[name='id_proyecto']").value = data.id_proyecto;
        document.querySelector("select[name='tipo_cliente']").value = data.tipo_cliente;
        document.querySelector("input[name='id_cliente']").value = data.id_cliente;
        document.querySelector("input[name='cliente_ruc']").value = data.cliente_ruc;
        document.querySelector("input[name='cliente_razon_social']").value = data.cliente_razon_social;
        document.querySelector("input[name='id_persona']").value = data.id_persona;
        document.querySelector("input[name='dni_persona']").value = data.dni_persona;
        document.querySelector("input[name='nombre_persona']").value = data.nombre_persona;
        document.querySelector("input[name='ubigeo']").value = data.id_ubigeo_entrega;
        document.querySelector("input[name='name_ubigeo']").value = data.name_ubigeo;
        document.querySelector("input[name='telefono_cliente']").value = data.telefono;
        document.querySelector("input[name='email_cliente']").value = data.email;
        document.querySelector("input[name='direccion_entrega']").value = data.direccion_entrega;
        // document.querySelector("input[name='nombre_contacto']").value =data.
        // document.querySelector("input[name='cargo_contacto']").value =data.
        // document.querySelector("input[name='email_contacto']").value =data.
        // document.querySelector("input[name='telefono_contacto']").value =data.
        // document.querySelector("input[name='direccion_contacto']").value =data.
        document.querySelector("textarea[name='observacion']").value = data.observacion;


        if ((data.adjuntos).length > 0) {
            (data.adjuntos).forEach(element => {
                tempArchivoAdjuntoRequerimientoList.push({
                    id: element.id_adjunto,
                    category: element.categoria_adjunto_id,
                    nameFile: element.archivo,
                    typeFile: null,
                    sizeFile: null,
                    file: []
                });

            });
            ArchivoAdjunto.updateContadorTotalAdjuntosRequerimiento();

        }
    }



    mostrarDetalleRequerimiento(data) {
        requerimientoView.limpiarTabla('ListaDetalleRequerimiento');
        vista_extendida();
        for (let i = 0; i < data.length; i++) {
            if (data[i].id_tipo_item == 1) { // producto
                document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td></td>
                <td><p class="descripcion-partida" data-id-partida="${data[i].id_partida}" data-presupuesto-total="${data[i].presupuesto_total_partida}" title="${data[i].codigo_partida != null ? data[i].codigo_partida : ''}" >${data[i].descripcion_partida != null ? data[i].descripcion_partida : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-info" name="partida" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="partida" name="idPartida[]" value="${data[i].id_partida}" hidden>
                    </div>
                </td>
                <td><p class="descripcion-centro-costo" title="${data[i].codigo_centro_costo != null ? data[i].codigo_centro_costo : ''}">${data[i].descripcion_centro_costo != null ? data[i].descripcion_centro_costo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary" name="centroCostos" onclick="requerimientoView.cargarModalCentroCostos(this)" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="centroCosto" name="idCentroCosto[]" value="${data[i].id_centro_costo}" hidden>
                    </div>
                </td>
                <td><input class="form-control input-sm" type="text" name="partNumber[]" placeholder="Part number" value="${data[i].part_number != null ? data[i].part_number : ''}"></td>
                <td>
                    <div class="form-group">
                        <textarea class="form-control input-sm descripcion" name="descripcion[]" placeholder="Descripción" value="${data[i].descripcion != null ? data[i].descripcion : ''}" onkeyup ="requerimientoView.updateDescripcionItem(this);">${data[i].descripcion != null ? data[i].descripcion : ''}</textarea></td>
                    </div>
                <td><select name="unidad[]" class="form-control input-sm" value="${data[i].id_unidad_medida}" >${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                <td>
                    <div class="form-group">
                        <input class="form-control input-sm cantidad text-right" type="number" min="1" name="cantidad[]"  value="${data[i].cantidad}" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Cantidad">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control input-sm precio text-right" type="number" min="0" name="precioUnitario[]" value="${data[i].precio_unitario}" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Precio U."></td>
                    </div>  
                <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                <td><textarea class="form-control input-sm" name="motivo[]"  value="${data[i].motivo != null ? data[i].motivo : ''}" placeholder="Motivo de requerimiento de item (opcional)">${data[i].motivo != null ? data[i].motivo : ''}</textarea></td>
                <td>
                    <div class="btn-group" role="group">
                        <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                        <input type="hidden" class="idRegister" name="idRegister[]" value="${data[i].id_detalle_requerimiento}">
                        <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)" >
                            <i class="fas fa-paperclip"></i>
                            <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                        </button> 
                        <button type="button" class="btn btn-danger btn-xs" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
                </tr>`);
            } else { // servicio
                document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td></td>
                <td><p class="descripcion-partida" data-id-partida="${data[i].id_partida}" data-presupuesto-total="${data[i].presupuesto_total_partida}" title="${data[i].codigo_partida != null ? data[i].codigo_partida : ''}" >${data[i].descripcion_partida != null ? data[i].descripcion_partida : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-info" name="partida" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="partida" name="idPartida[]" value="${data[i].id_partida}" hidden>
                    </div>
                </td>
                <td><p class="descripcion-centro-costo" title="${data[i].codigo_centro_costo != null ? data[i].codigo_centro_costo : ''}">${data[i].descripcion_centro_costo != null ? data[i].descripcion_centro_costo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary" name="centroCostos" onclick="requerimientoView.cargarModalCentroCostos(this)" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button> 
                    <div class="form-group">
                        <input type="text" class="centroCosto" name="idCentroCosto[]" value="${data[i].id_centro_costo}" hidden>
                    </div>
                </td>
                <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
                <td>
                    <div class="form-group">
                    <textarea class="form-control input-sm descripcion" name="descripcion[]" placeholder="Descripción" value="${data[i].descripcion != null ? data[i].descripcion : ''}" onkeyup ="requerimientoView.updateDescripcionItem(this);">${data[i].descripcion != null ? data[i].descripcion : ''}"</textarea></td>
                    </div>
                <td><select name="unidad[]" class="form-control input-sm" value="${data[i].id_unidad_medida}" >${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
                <td>
                    <div class="form-group">
                        <input class="form-control input-sm cantidad text-right" type="number" min="1" name="cantidad[]"  value="${data[i].cantidad}" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Cantidad">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control input-sm precio text-right" type="number" min="0" name="precioUnitario[]" value="${data[i].precio_unitario}" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this); requerimientoView.calcularPresupuestoUtilizadoYSaldoPorPartida();" placeholder="Precio U."></td>
                    </div>  
                <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                <td><textarea class="form-control input-sm" name="motivo[]"  value="${data[i].motivo != null ? data[i].motivo : ''}" placeholder="Motivo de requerimiento de item (opcional)">${data[i].motivo != null ? data[i].motivo : ''}</textarea></td>
                <td>
                    <div class="btn-group" role="group">
                        <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                        <input type="hidden" class="idRegister" name="idRegister[]" value="${data[i].id_detalle_requerimiento}">
                        <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)" >
                            <i class="fas fa-paperclip"></i>
                            <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>    
                        </button> 
                        <button type="button" class="btn btn-danger btn-xs" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
                </tr>`);
            }

        }
        this.updateContadorItem();
        this.autoUpdateSubtotal();
        this.calcularTotal();
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();

        data.forEach(element => {
            if (element.adjuntos.length > 0) {
                (element.adjuntos).forEach(adjunto => {
                    tempArchivoAdjuntoItemList.push({
                        id: adjunto.id_adjunto,
                        idRegister: adjunto.id_detalle_requerimiento,
                        nameFile: adjunto.archivo,
                        typeFile: null,
                        sizeFile: null,
                        file: []
                    });
                });

            }

        });

        ArchivoAdjunto.updateContadorTotalAdjuntosPorItem();

    }

}

const requerimientoView = new RequerimientoView();


class ArchivoAdjunto {

    constructor(file) {
        this.file = file[0];
    }

    getType() {
        return this.file.type;
    }

    getSize() {
        return this.file.size;
    }

    getName() {
        return this.file.name;
    }

    isAllowedFile() {
        let extension = this.getName().match(/(?<=\.)\w+$/g)[0].toLowerCase(); // assuming that this file has any extension
        if (extension === 'dwg'
            || extension === 'dwt'
            || extension === 'cdr'
            || extension === 'back'
            || extension === 'backup'
            || extension === 'psd'
            || extension === 'sql'
            || extension === 'exe'
            || extension === 'html'
            || extension === 'js'
            || extension === 'php'
            || extension === 'ai'
            || extension === 'mp4'
            || extension === 'mp3'
            || extension === 'avi'
            || extension === 'mkv'
            || extension === 'flv'
            || extension === 'mov'
            || extension === 'wmv'
        ) {
            return false;
        } else {
            return true;
        }
    }

    addToTablaArchivosRequerimiento(id, nameFile) {

        requerimientoCtrl.getcategoriaAdjunto().then((res) => {
            this.construirRegistroEnTablaAdjuntosRequerimiento(id, nameFile, res);

        }).catch(function (err) {
            console.log(err)
        })

    }

    construirRegistroEnTablaAdjuntosRequerimiento(id, nameFile, data) {
        let html = '';
        html = `<tr id="${id}" style="text-align:center">
        <td style="text-align:left;">${nameFile}</td>
        <td>
            <select class="form-control" name="categoriaAdjunto" onChange="ArchivoAdjunto.changeCategoriaAdjunto(this)">
        `;
        data.forEach(element => {
            html += `<option value="${element.id_categoria_adjunto}">${element.descripcion}</option>`
        });
        html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoRequerimiento" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoRequerimiento(this,'${id}');" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html);

    }

    static changeCategoriaAdjunto(obj) {
        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoList.findIndex(elemnt => elemnt.id === obj.closest('tr').id);
            tempArchivoAdjuntoRequerimientoList[indice].category = obj.value;
        } else {
            alert("Hubo un error inesperado en la lista de adjuntos por requerimiento, la cantidad de adjuntos es cero");
        }
    }


    addToTablaArchivosItem(id, nameFile) {

        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', `<tr id="${id}" style="text-align:center">
        <td  style="text-align:left;">${nameFile}</td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-md" name="btnEliminarArchivoItem" title="Eliminar" onclick="ArchivoAdjunto.eliminarArchivoItem(this,'${id}');" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>
        `);
    }

    static descargarArchivoRequerimieto(id) {
        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            tempArchivoAdjuntoRequerimientoList.forEach(element => {
                if (element.id == id) {
                    window.open("/files/logistica/requerimiento/" + element.nameFile);
                }
            });
        }
    }
    static eliminarArchivoRequerimiento(obj, id) {
        // console.log('eliminar archivo ' + idRegister + nameFile);
        obj.closest("tr").remove();
        tempArchivoAdjuntoRequerimientoList = tempArchivoAdjuntoRequerimientoList.filter((element, i) => element.id != id);
        ArchivoAdjunto.updateContadorTotalAdjuntosRequerimiento();
    }

    static eliminarArchivoItem(obj, id) {
        obj.closest("tr").remove();
        tempArchivoAdjuntoItemList = tempArchivoAdjuntoItemList.filter((element, i) => element.id != id);
        ArchivoAdjunto.updateContadorTotalAdjuntosPorItem();
    }

    static descargarArchivoItem(id) {
        if (tempArchivoAdjuntoItemList.length > 0) {
            tempArchivoAdjuntoItemList.forEach(element => {
                if (element.id == id) {
                    window.open("/files/logistica/detalle_requerimiento/" + element.nameFile);
                }
            });
        }
    }

    static updateContadorTotalAdjuntosRequerimiento() {

        document.querySelector("span[name='cantidadAdjuntosRequerimiento']").textContent = tempArchivoAdjuntoRequerimientoList.length;
    }

    static updateContadorTotalAdjuntosPorItem() {
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let i = 0; i < tbodyChildren.length; i++) {
            if (tempArchivoAdjuntoItemList.length > 0) {
                for (let j = 0; j < tempArchivoAdjuntoItemList.length; j++) {
                    // if(tbodyChildren[i].querySelector("input[class~='idRegister']").value == tempArchivoAdjuntoItemList.idRegister){
                    const cantidad = tempArchivoAdjuntoItemList.filter(function (element) { return element.idRegister == tbodyChildren[i].querySelector("input[class~='idRegister']").value; }).length;
                    tbodyChildren[i].querySelector("span[name='cantidadAdjuntosItem']").textContent = cantidad;
                    // } 

                }

            } else {
                tbodyChildren[i].querySelector("span[name='cantidadAdjuntosItem']").textContent = 0;
            }

        }

    }


    addFileLevelRequerimiento() {
        if (this.isAllowedFile() == true) {

            const nameFile = this.getName();
            const typeFile = this.getType();
            const sizeFile = this.getSize();
            const id = requerimientoView.makeId();
            tempArchivoAdjuntoRequerimientoList.push({
                id: id,
                category: 1, //default
                nameFile: nameFile,
                typeFile: typeFile,
                sizeFile: sizeFile,
                file: this.file
            });

            ArchivoAdjunto.updateContadorTotalAdjuntosRequerimiento();
            this.addToTablaArchivosRequerimiento(id, nameFile);

        } else {
            alert(`La extensión del archivo .${typeFile} no esta permitido`);
        }
        return false;
    }

    addFileLevelItem() {
        if (this.isAllowedFile() == true) {

            const nameFile = this.getName();
            const typeFile = this.getType();
            const sizeFile = this.getSize();
            const id = requerimientoView.makeId();

            tempArchivoAdjuntoItemList.push({
                id: id,
                idRegister: tempIdRegisterActive,
                nameFile: nameFile,
                typeFile: typeFile,
                sizeFile: sizeFile,
                file: this.file
            });

            ArchivoAdjunto.updateContadorTotalAdjuntosPorItem();
            this.addToTablaArchivosItem(id, nameFile)

        } else {
            alert(`La extensión del archivo .${typeFile} no esta permitido`);
        }
        return false;
    }
    // doUpload(){
    //     let formData = new FormData();
    //     formData.append("file", this.file, this.getName());

    // }
}

class Historial extends RequerimientoView {
    mostrarHistorial() {
        $('#modal-historial-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        requerimientoCtrl.getListadoElaborados("ME", null, null, null, null, null).then(function (res) {
            historialRequerimiento.construirTablaHistorialRequerimientosElaborados(res['data']);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaHistorialRequerimientosElaborados(data) {
        var vardataTables = funcDatatables();
        $('#listaRequerimiento').DataTable({
            'dom': vardataTables[1],
            'buttons': [],
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
                        let btnSeleccionar = '<button type="button" class="btn btn-xs btn-success" title="Seleccionar" onClick="historialRequerimiento.cargarRequerimiento(' + row['id_requerimiento'] + ');">Seleccionar</button>';
                        return containerOpenBrackets + btnSeleccionar + containerCloseBrackets;
                    }, targets: 10
                },
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.estado == 2) {
                    $(row).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row).css('color', '#d92b60');
                }
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

    cargarRequerimiento(idRequerimiento) {
        $('#modal-historial-requerimiento').modal('hide');
        requerimientoCtrl.getRequerimiento(idRequerimiento).then(function (res) {
            requerimientoView.mostrarRequerimiento(res);

        }).catch(function (err) {
            console.log(err)
        });
    }
}

const historialRequerimiento = new Historial();


class Listado extends RequerimientoView {
    mostrar(meOrAll, idEmpresa, idSede, idGrupo, division, idPrioridad) {
        requerimientoCtrl.getListadoElaborados(meOrAll, idEmpresa, idSede, idGrupo, division, idPrioridad).then(function (res) {
            listadoRequerimiento.construirTablaListadoRequerimientosElaborados(res['data']);
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
                            btnEditar = '<button type="button" class="btn btn-xs bg-default" title="Editar" onClick="editarListaReq(' + row['id_requerimiento'] + ');"><i class="fas fa-edit fa-xs"></i></button>';
                        }
                        let btnDetalleRapido = '<button type="button" class="btn btn-xs btn-info" title="Ver detalle" onClick="aprobarRequerimiento.viewFlujo(' + row['id_requerimiento'] + ', ' + row['id_doc_aprob'] + ');"><i class="fas fa-eye fa-xs"></i></button>';
                        let btnTracking = '<button type="button" class="btn btn-xs bg-primary" title="Explorar Requerimiento" onClick="aprobarRequerimiento.tracking_requerimiento(' + row['id_requerimiento'] + ');"><i class="fas fa-globe fa-xs"></i></button>';
                        return containerOpenBrackets + btnDetalleRapido + btnEditar + btnTracking + containerCloseBrackets;
                    }, targets: 11
                },
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.estado == 2) {
                    $(row).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row).css('color', '#d92b60');
                }
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
            listadoRequerimiento.construirSelectSede(res);
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
            listadoRequerimiento.construirSelectDivision(res);
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

const listadoRequerimiento = new Listado();


class Aprobar extends RequerimientoView {
    mostrar(idEmpresa, idSede, idGrupo, idPrioridad) {
        requerimientoCtrl.getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad).then(function (res) {
            aprobarRequerimiento.construirTablaListaRequerimientosPendientesAprobacion(res['data']);
        }).catch(function (err) {
            console.log(err)
        })

    }


    construirTablaListaRequerimientosPendientesAprobacion(data) {
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
                        let prioridad = '';
                        let thermometerNormal = '<center><i class="fas fa-thermometer-empty green fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad Normal" ></i></center>';
                        let thermometerAlta = '<center> <i class="fas fa-thermometer-half orange fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad Alta"  ></i></center>';
                        let thermometerCritica = '<center> <i class="fas fa-thermometer-full red fa-lg"  data-toggle="tooltip" data-placement="right" title="Prioridad Crítico"  ></i></center>';
                        if (row.id_prioridad == 1) {
                            prioridad = thermometerNormal
                        } else if (row.id_prioridad == 2) {
                            prioridad = thermometerAlta
                        } else if (row.id_prioridad == 3) {
                            prioridad = thermometerCritica
                        }
                        return prioridad;
                    }
                },
                { 'data': 'codigo', 'name': 'codigo' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'tipo_requerimiento', 'name': 'tipo_requerimiento' },
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega' },
                { 'data': 'razon_social_empresa', 'name': 'razon_social_empresa' },
                { 'data': 'division', 'name': 'division' },
                { 'data': 'observacion', 'name': 'alm_req.observacion' },
                { 'data': 'usuario', 'name': 'usuario' },
                { 'data': 'estado_doc', 'name': 'estado_doc' },
                { 'data': 'cantidad_aprobados_total_flujo', 'name': 'cantidad_aprobados_total_flujo' },
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

                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnDetalleRapido = `<button type="button" class="btn btn-xs btn-info" title="Ver detalle"   onClick="aprobarRequerimiento.verDetalleRequerimiento('${row['id_requerimiento']}', '${row['id_doc_aprob']}','${row['id_usuario_aprobante']}','${row['id_rol_aprobante']}','${row['id_flujo']}','${row['aprobacion_final_o_pendiente']}');"><i class="fas fa-eye fa-xs"></i></button>`;
                        // let btnTracking = '<button type="button" class="btn btn-xs bg-primary" title="Explorar Requerimiento" onClick="aprobarRequerimiento.tracking_requerimiento(' + row['id_requerimiento'] + ');"><i class="fas fa-globe fa-xs"></i></button>';
                        // let btnAprobar = '<button type="button" class="btn btn-xs btn-success" title="Aprobar Requerimiento" onClick="aprobarRequerimiento.aprobarRequerimiento(' + row['id_doc_aprob'] + ');" ' + disabledBtn + '><i class="fas fa-check fa-xs"></i></button>';
                        // let btnObservar = '<button type="button" class="btn btn-xs btn-warning" title="Observar Requerimiento" onClick="aprobarRequerimiento.observarRequerimiento(' + row['id_doc_aprob'] + ');" ' + disabledBtn + '><i class="fas fa-exclamation-triangle fa-xs"></i></button>';
                        // let btnAnular = '<button type="button" class="btn btn-xs bg-maroon" title="Anular Requerimiento" onClick="aprobarRequerimiento.anularRequerimiento(' + row['id_doc_aprob'] + ');" ' + disabledBtn + '><i class="fas fa-ban fa-xs"></i></button>';
                        return containerOpenBrackets + btnDetalleRapido + containerCloseBrackets;
                    }
                },
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.estado == 2) {
                    $(row).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row).css('color', '#d92b60');
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
        requerimientoCtrl.getSedesPorEmpresa(event.target.value).then(function (res) {
            aprobarRequerimiento.construirSelectSede(res);
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

    verDetalleRequerimiento(idRequerimiento, idDocumento,idUsuario, idRolAprobante, idFlujo, aprobacionFinalOPendiente) {
        $('#modal-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("div[id='modal-requerimiento'] textarea[id='comentario']").value='';

        document.querySelector("div[id='modal-requerimiento'] input[name='idRequerimiento']").value = idRequerimiento;
        document.querySelector("div[id='modal-requerimiento'] input[name='idDocumento']").value = idDocumento;
        document.querySelector("div[id='modal-requerimiento'] input[name='idUsuario']").value = idUsuario;
        document.querySelector("div[id='modal-requerimiento'] input[name='idRolAprobante']").value = idRolAprobante;
        document.querySelector("div[id='modal-requerimiento'] input[name='idFlujo']").value = idFlujo;
        document.querySelector("div[id='modal-requerimiento'] input[name='aprobacionFinalOPendiente']").value = aprobacionFinalOPendiente;

        requerimientoCtrl.getRequerimiento(idRequerimiento).then(function (res) {
            aprobarRequerimiento.construirSeccionDatosGenerales(res['requerimiento'][0]);
            aprobarRequerimiento.construirSeccionItemsDeRequerimiento(res['det_req']);
            aprobarRequerimiento.construirSeccionHistorialAprobacion(res['historial_aprobacion']);

        }).catch(function (err) {
            console.log(err)
        })
    }

    construirSeccionDatosGenerales(data) {
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = data.codigo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = data.concepto;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = data.razon_social_empresa;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='division']").textContent = data.division;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = data.prioridad;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='fecha_entrega']").textContent = data.fecha_entrega;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='solicitado_por']").textContent = (data.para_stock_almacen == true ? 'Para stock almacén' : (data.nombre_trabajador ? data.nombre_trabajador : '-'));
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = data.periodo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = data.persona;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='observacion']").textContent = data.observacion;

        tempArchivoAdjuntoRequerimientoList = [];
        if (data.adjuntos.length > 0) {
            document.querySelector("span[name='cantidadAdjuntosRequerimiento']").textContent = data.adjuntos.length;
            (data.adjuntos).forEach(element => {
                tempArchivoAdjuntoRequerimientoList.push({
                    'id': element.id_adjunto,
                    'id_requerimiento': element.id_requerimiento,
                    'archivo': element.archivo,
                    'categoria_adjunto_id': element.categoria_adjunto_id,
                    'categoria_adjunto': element.categoria_adjunto,
                    'fecha_registro': element.fecha_registro,
                    'estado': element.estado
                });

            });
        }

        let tamañoSelectAccion = document.querySelector("div[id='modal-requerimiento'] select[id='accion']").length;
        if(data.estado==3){
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if(document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value ==1){
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].setAttribute('disabled',true)
                }   
            }
        }else{
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if(document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value ==1){
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].removeAttribute('disabled')
                }   
            }
        }
    }

    verAdjuntosRequerimiento() {
        $('#modal-adjuntar-archivos-requerimiento').modal({
            show: true
        });

        requerimientoView.limpiarTabla('listaArchivosRequerimiento');
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
                    html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoItem" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoItem('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
                    html += `</div>
                    </td>
                    </tr>`;

                }
            });
        }
        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html)

    }

    construirSeccionItemsDeRequerimiento(data) {
        requerimientoView.limpiarTabla('listaDetalleRequerimientoModal');
        tempArchivoAdjuntoItemList = [];
        let html = '';
        let cantidadAdjuntosItem = 0;
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
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
                html = `<tr>
                            <td>${i + 1}</td>
                            <td>${data[i].descripcion_partida ? data[i].descripcion_partida : ''}</td>
                            <td>${data[i].descripcion_centro_costo ? data[i].descripcion_centro_costo : ''}</td>
                            <td>${data[i].id_tipo_item == 1 ? (data[i].part_number ? data[i].part_number : '') : '(Servicio)'}</td>
                            <td>${data[i].descripcion ? data[i].descripcion : (data[i].descripcion_adicional ? data[i].descripcion_adicional : '')} </td>
                            <td>${data[i].unidad_medida}</td>
                            <td>${data[i].cantidad}</td>
                            <td>${data[i].simbolo_moneda ? data[i].simbolo_moneda : ''} ${Util.formatoNumero(data[i].precio_unitario, 2)}</td>
                            <td>${(data[i].subtotal ? Util.formatoNumero(data[i].subtotal, 2) : '')}</td>
                            <td>${data[i].motivo ? data[i].motivo : ''}</td>
                            <td style="text-align: center;"> 
                                <a title="Ver archivos adjuntos de item" style="cursor:pointer;" onClick="aprobarRequerimiento.verAdjuntosItem(${data[i].id_detalle_requerimiento})">
                                    Ver adjuntos: <span name="cantidadAdjuntosItem">0</span>
                                </a>
                            </td>
                        </tr>`;
            }


        }
        document.querySelector("tbody[id='body_item_requerimiento']").insertAdjacentHTML('beforeend', html)
        document.querySelector("span[name='cantidadAdjuntosItem']").textContent = cantidadAdjuntosItem;

    }

    construirSeccionHistorialAprobacion(data) {
        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                html = `<tr>
                    <td>${data[i].nombre_usuario ? data[i].nombre_usuario : ''}</td>
                    <td>${data[i].accion ? data[i].accion : ''}</td>
                    <td>${data[i].detalle_observacion ? data[i].detalle_observacion : ''}</td>
                    <td>${data[i].fecha_vobo ? data[i].fecha_vobo : ''}</td>
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
        requerimientoView.limpiarTabla('listaArchivos');
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

    updateAccion(obj){
        if(obj.value >0){
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.remove("has-error")
            if(obj.closest('div[class~="form-group"]').querySelector("span")){
                obj.closest('div[class~="form-group"]').querySelector("span").remove();
            }
        }else{
            obj.closest('div[class~="form-group"]').classList.add("has-error")
            if(obj.closest('div[class~="form-group"]').querySelector("span") ==null){
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una acción)';
                obj.closest('div[class~="form-group"]').appendChild(newSpanInfo);
            }
        }
    }

    registrarRespuesta() {

        if(document.querySelector("div[id='modal-requerimiento'] select[id='accion']").value >0){
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.remove("has-error")
            if(document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span")){
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
                'aprobacionFinalOPendiente': document.querySelector("div[id='modal-requerimiento'] input[name='aprobacionFinalOPendiente']").value
            };
    
            requerimientoCtrl.guardarRespuesta(payload).then(function(res) {
                if(res.id_aprobacion >0){
                    alert(`Respuesta registrada con éxito. La página se recargara para actualizar el listado.`);
                    $('#modal-requerimiento').modal('hide'); 
                    location.reload();
    
    
                }else{
                    alert(res.mensaje);
                }
    
            }).catch(function (err) {
                console.log(err)
            });

        }else{
            document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').classList.add("has-error")
            if(document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').querySelector("span") ==null){
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una acción)';
                document.querySelector("div[id='modal-requerimiento'] select[id='accion']").closest('div[class~="form-group"]').appendChild(newSpanInfo);
            }

        }
    }
    // tracking requerimiento modal 
    // tracking_requerimiento(id_req) {
    //     $('#modal-tracking-requerimiento').modal({
    //         show: true,
    //         backdrop: 'static'
    //     });
    //     this.get_data_tracking(id_req);
    // }

    // get_data_tracking(id_req) {
    //     $.ajax({
    //         type: 'GET',
    //         url: 'explorar-requerimiento' + '/' + id_req,
    //         dataType: 'JSON',
    //         success: function (response) {
    //             aprobarRequerimiento.llenar_tabla_historial_aprobaciones(response.historial_aprobacion);
    //             aprobarRequerimiento.llenar_tabla_flujo_aprobacion(response.flujo_aprobacion);
    //             aprobarRequerimiento.llenar_tabla_cotizaciones(response.solicitud_cotizaciones);
    //             aprobarRequerimiento.llenar_tabla_cuadro_comparativo(response.cuadros_comparativos);
    //             aprobarRequerimiento.llenar_tabla_ordenes(response.ordenes);
    //         }
    //     }).fail(function (jqXHR, textStatus, errorThrown) {
    //         console.log(jqXHR);
    //         console.log(textStatus);
    //         console.log(errorThrown);
    //     });
    // }

    // llenar_tabla_historial_aprobaciones(data) {
    //     requerimientoView.limpiarTabla('listaHistorialAprobacion');
    //     let htmls = '<tr></tr>';
    //     $('#listaHistorialAprobacion tbody').html(htmls);
    //     var table = document.getElementById("listaHistorialAprobacion");
    //     if (data.length > 0) {
    //         for (var a = 0; a < data.length; a++) {
    //             var row = table.insertRow(a + 1);
    //             row.insertCell(0).innerHTML = data[a].estado ? data[a].estado.toUpperCase() : '-';
    //             row.insertCell(1).innerHTML = data[a].nombre_usuario ? data[a].nombre_usuario : '-';
    //             row.insertCell(2).innerHTML = data[a].obs ? data[a].obs : '-';
    //             row.insertCell(3).innerHTML = data[a].fecha ? data[a].fecha : '-';
    //         }
    //     }
    // }

    // llenar_tabla_flujo_aprobacion(data) {
    //     // console.log(data);
    //     requerimientoView.limpiarTabla('listaFlujoAprobacion');
    //     let htmls = '<tr></tr>';
    //     $('#listaFlujoAprobacion tbody').html(htmls);
    //     var table = document.getElementById("listaFlujoAprobacion");
    //     if (data.length > 0) {
    //         for (var a = 0; a < data.length; a++) {
    //             var row = table.insertRow(a + 1);
    //             row.insertCell(0).innerHTML = data[a].orden ? data[a].orden : '-';
    //             row.insertCell(1).innerHTML = data[a].nombre_fase ? data[a].nombre_fase : '-';
    //             row.insertCell(2).innerHTML = data[a].nombre_responsable ? data[a].nombre_responsable : '-';
    //             row.insertCell(3).innerHTML = data[a].criterio_monto.length > 0 ? data[a].criterio_monto.map(item => item.descripcion) : '';
    //             row.insertCell(4).innerHTML = data[a].criterio_prioridad.length > 0 ? data[a].criterio_prioridad.map(item => item.descripcion) : '';
    //         }
    //     }
    // }

    // llenar_tabla_cotizaciones(data) {
    //     requerimientoView.limpiarTabla('listaCotizaciones');
    //     let htmls = '<tr></tr>';
    //     $('#listaCotizaciones tbody').html(htmls);
    //     var table = document.getElementById("listaCotizaciones");

    //     let cantidad_cotizaciones = data.length;
    //     document.getElementById('cantidad_cotizaciones').innerHTML = cantidad_cotizaciones;

    //     if (cantidad_cotizaciones > 0) {
    //         for (var a = 0; a < data.length; a++) {
    //             var row = table.insertRow(a + 1);
    //             row.insertCell(0).innerHTML = a + 1;
    //             row.insertCell(1).innerHTML = data[a].codigo_cotizacion ? data[a].codigo_cotizacion : '-';
    //             row.insertCell(2).innerHTML = data[a].razon_social ? data[a].razon_social : '-' + data[a].nombre_doc_identidad ? data[a].nombre_doc_identidad : '-' + data[a].nro_documento ? data[a].nro_documento : '-';
    //             row.insertCell(3).innerHTML = data[a].email_proveedor ? data[a].email_proveedor : '-';
    //             row.insertCell(4).innerHTML = data[a].razon_social_empresa ? data[a].razon_social_empresa : '-' + data[a].nombre_doc_idendidad_empresa ? data[a].nombre_doc_idendidad_empresa : '-' + data[a].nro_documento_empresa ? data[a].nro_documento_empresa : '-';
    //             row.insertCell(5).innerHTML = data[a].fecha_registro ? data[a].fecha_registro : '-';
    //             row.insertCell(6).innerHTML = data[a].estado_envio ? data[a].estado_envio : '-';
    //             if (disabledBtn == false) {
    //                 row.insertCell(7).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-sm btn-log bg-maroon"' +
    //                     '    name="btnVerDetalleCotizacion"' +
    //                     '    title="Ver detalle"' +
    //                     '   onClick="detalleCotizacionModal(' + data[a].id_cotizacion + ');"' +
    //                     '   >' +
    //                     '    <i class="fas fa-eye fa-xs"></i>' +
    //                     '</button>' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-success"' +
    //                     '    name="btnDownloadExcelDirectSolicitudCotizacion"' +
    //                     '    title="Descargar en Excel"' +
    //                     '   onClick="downloadDirectSolicitudCotizacion(' + data[a].id_cotizacion + ');"' +
    //                     '   >' +
    //                     '    <i class="fas fa-file-excel fa-xs"></i>' +
    //                     '</button>' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-default"' +
    //                     '    name="btnIrDirectSolicitudCotizacion"' +
    //                     '    title="Ir a Gestión de Solicitudes de Cotización"' +
    //                     '   onClick="irDirectSolicitudCotizacion(' + data[a].requerimiento[0].id_requerimiento + ');"' +
    //                     '   >' +
    //                     '    <i class="fas fa-compass fa-xs"></i>' +
    //                     '</button>' +
    //                     '</div>';
    //             } else {
    //                 row.insertCell(7).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-sm btn-log bg-maroon"' +
    //                     '    name="btnVerDetalleCotizacion"' +
    //                     '    title="Ver detalle"' +
    //                     '   onClick="detalleCotizacionModal(' + data[a].id_cotizacion + ');"' +
    //                     '   >' +
    //                     '    <i class="fas fa-eye fa-xs"></i>' +
    //                     '</button>' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-success"' +
    //                     '    name="btnDownloadExcelDirectSolicitudCotizacion"' +
    //                     '    title="Descargar en Excel"' +
    //                     '   onClick="downloadDirectSolicitudCotizacion(' + data[a].id_cotizacion + ');"' +
    //                     '   >' +
    //                     '    <i class="fas fa-file-excel fa-xs"></i>' +
    //                     '</button>' +
    //                     '</div>';
    //             }
    //         }
    //     }
    // }

    // llenar_tabla_cuadro_comparativo(data) {
    //     // console.log(data);

    //     requerimientoView.limpiarTabla('listaCuadroComparativo');
    //     let htmls = '<tr></tr>';
    //     $('#listaCuadroComparativo tbody').html(htmls);
    //     var table = document.getElementById("listaCuadroComparativo");

    //     let cantidad_cuadros = data.length;
    //     let cantidad_buena_pro = 0;
    //     document.getElementById('cantidad_cuadros_comparativos').innerHTML = cantidad_cuadros;
    //     if (cantidad_cuadros > 0) {
    //         for (var a = 0; a < data.length; a++) {
    //             var row = table.insertRow(a + 1);
    //             row.insertCell(0).innerHTML = a + 1;
    //             row.insertCell(1).innerHTML = data[a].codigo_grupo ? data[a].codigo_grupo : '-';
    //             row.insertCell(2).innerHTML = data[a].cotizaciones.map((item, index) => {
    //                 cantidad_buena_pro += item.total_buena_pro;
    //                 return item.codigo_cotizacion + ' [ ' + item.razon_social + ' - ' + item.nombre_doc_identidad + ': ' + item.nro_documento + ' ]'

    //             });
    //             row.insertCell(3).innerHTML = cantidad_buena_pro ? cantidad_buena_pro : '-';
    //             row.insertCell(4).innerHTML = data[a].fecha_inicio ? data[a].fecha_inicio : '-';
    //             if (disabledBtn == false) {
    //                 row.insertCell(5).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-success"' +
    //                     '    name="btnDownloadExcelDirectCuadroComparativo"' +
    //                     '    title="Descargar en Excel"' +
    //                     '   onClick="downloadDirectCuadroComparativo(' + data[a].id_grupo_cotizacion + ');"' +
    //                     '  >' +
    //                     '    <i class="fas fa-file-excel"></i>' +
    //                     '</button>' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-default"' +
    //                     '    name="btnIrDirectSolicitudCotizacion"' +
    //                     '    title="Ir a Cuadro Comparativo"' +
    //                     '   onClick="irDirectCuadroComparativo(3,' + data[a].id_grupo_cotizacion + ');"' +
    //                     '>' +
    //                     '    <i class="fas fa-compass fa-xs"></i>' +
    //                     '</button>' +
    //                     '</div>';
    //             } else {
    //                 row.insertCell(5).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-success"' +
    //                     '    name="btnDownloadExcelDirectCuadroComparativo"' +
    //                     '    title="Descargar en Excel"' +
    //                     '   onClick="downloadDirectCuadroComparativo(' + data[a].id_grupo_cotizacion + ');"' +
    //                     '  >' +
    //                     '    <i class="fas fa-file-excel"></i>' +
    //                     '</button>' +
    //                     '</div>';
    //             }
    //         }
    //     }
    // }

    // llenar_tabla_ordenes(data) {
    //     // console.log(data);

    //     requerimientoView.limpiarTabla('listaOrdenes');
    //     let htmls = '<tr></tr>';
    //     $('#listaOrdenes tbody').html(htmls);
    //     var table = document.getElementById("listaOrdenes");

    //     let cantidad = data.length;
    //     document.getElementById('cantidad_ordenes').innerHTML = cantidad;
    //     let cantidad_buena_pro = 0;
    //     if (cantidad > 0) {
    //         for (var a = 0; a < data.length; a++) {
    //             var row = table.insertRow(a + 1);
    //             row.insertCell(0).innerHTML = a + 1;
    //             row.insertCell(1).innerHTML = data[a].codigo ? data[a].codigo : '-';
    //             row.insertCell(2).innerHTML = data[a].razon_social_proveedor ? data[a].razon_social_proveedor : '-' + ' [' + tipo_doc_proveedor ? tipo_doc_proveedor : '-' + ' ' + nro_documento_proveedor ? nro_documento_proveedor : '-' + ' ]';
    //             row.insertCell(3).innerHTML = data[a].cotizaciones.map((item, index) => {
    //                 cantidad_buena_pro += item.total_buena_pro;
    //                 return '[ ' + item.razon_social_empresa + ' - ' + item.tipo_documento_empresa + ': ' + item.nro_documento_empresa + ' ]'

    //             });
    //             row.insertCell(4).innerHTML = data[a].monto_total ? data[a].monto_total : '-';
    //             row.insertCell(5).innerHTML = data[a].fecha ? data[a].fecha : '-';
    //             if (disabledBtn == false) {
    //                 row.insertCell(6).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
    //                     '</button>' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-danger"' +
    //                     '    name="btnDownloadExcelDirectOrden"' +
    //                     '    title="Descargar"' +
    //                     '   onClick="downloadDirectOrden(' + data[a].id_orden_compra + ');"' +
    //                     '>' +
    //                     '    <i class="fas fa-file-pdf"></i>' +
    //                     '</button>' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-default"' +
    //                     '    name="btnIrDirectOrden"' +
    //                     '    title="Ir a Orden"' +
    //                     '   onClick="irDirectOrden(' + data[a].id_orden_compra + ');"' +
    //                     ' >' +
    //                     '    <i class="fas fa-compass fa-xs"></i>' +
    //                     '</button>' +
    //                     '</div>';
    //             } else {
    //                 row.insertCell(6).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
    //                     '</button>' +
    //                     '<button type="button"' +
    //                     '    class="btn btn-xs btn-danger"' +
    //                     '    name="btnDownloadExcelDirectOrden"' +
    //                     '    title="Descargar"' +
    //                     '   onClick="downloadDirectOrden(' + data[a].id_orden_compra + ');"' +
    //                     '>' +
    //                     '    <i class="fas fa-file-pdf"></i>' +
    //                     '</button>' +
    //                     '</div>';
    //             }
    //         }
    //     }
    // }



    // aprobar, observar y anular requerimiento

    // aprobarRequerimiento(id_doc_aprob) {
    //     $('#modal-aprobacion-docs').modal({
    //         show: true,
    //         backdrop: 'static',
    //         keyboard: false
    //     });
    //     document.querySelector("form[id='form-aprobacion'] input[name='id_doc_aprob']").value = id_doc_aprob;
    // }

    // grabarAprobacion() {
    //     let id_doc_aprob = document.querySelector("form[id='form-aprobacion'] input[name='id_doc_aprob']").value;
    //     let id_rol_usuario = document.querySelector("form[id='form-aprobacion'] select[name='rol_usuario']").value;
    //     let detalle_observacion = document.querySelector("form[id='form-aprobacion'] textarea[name='detalle_observacion']").value;

    //     $.ajax({
    //         type: 'POST',
    //         url: 'aprobar-documento',
    //         data: { 'id_doc_aprob': id_doc_aprob, 'detalle_observacion': detalle_observacion, 'id_rol': id_rol_usuario },
    //         dataType: 'JSON',
    //         success: function (response) {
    //             if (response.status == 200) {
    //                 $('#modal-aprobacion-docs').modal('hide');
    //                 aprobarRequerimiento.mostrar();
    //                 alert("Requerimiento Aprobado");
    //             } else {
    //                 alert("Hubo un problema, no se puedo aprobar el requerimiento");
    //                 console.log(response);
    //             }

    //         }
    //     }).fail(function (jqXHR, textStatus, errorThrown) {
    //         console.log(jqXHR);
    //         console.log(textStatus);
    //         console.log(errorThrown);
    //     });

    // }


    // observarRequerimiento(id_doc_aprob) {
    //     $('#modal-obs-req').modal({
    //         show: true,
    //         backdrop: 'static',
    //         keyboard: false
    //     });
    //     document.querySelector("form[id='form-obs-requerimiento'] input[name='id_doc_aprob']").value = id_doc_aprob;
    // }

    // grabarObservacion() {
    //     let id_doc_aprob = document.querySelector("form[id='form-obs-requerimiento'] input[name='id_doc_aprob']").value;
    //     let id_rol_usuario = document.querySelector("form[id='form-obs-requerimiento'] select[name='rol_usuario']").value;
    //     let detalle_observacion = document.querySelector("form[id='form-obs-requerimiento'] textarea[name='motivo_req']").value;

    //     // console.log(id_doc_aprob);
    //     // console.log(id_rol_usuario);
    //     // console.log(detalle_observacion);
    //     $.ajax({
    //         type: 'POST',
    //         url: 'observar-documento',
    //         data: { 'id_doc_aprob': id_doc_aprob, 'detalle_observacion': detalle_observacion, 'id_rol': id_rol_usuario },
    //         dataType: 'JSON',
    //         success: function (response) {
    //             if (response.status == 200) {
    //                 $('#modal-obs-req').modal('hide');
    //                 aprobarRequerimiento.mostrar();
    //                 alert("Requerimiento Observado");
    //             } else {
    //                 alert("Hubo un problema, no se puedo observar el requerimiento");
    //                 console.log(response);
    //             }

    //         }
    //     }).fail(function (jqXHR, textStatus, errorThrown) {
    //         console.log(jqXHR);
    //         console.log(textStatus);
    //         console.log(errorThrown);
    //     });
    // }

    // anularRequerimiento(id_doc_aprob) {
    //     $('#modal-anular-req').modal({
    //         show: true,
    //         backdrop: 'static',
    //         keyboard: false
    //     });
    //     document.querySelector("form[id='form-anular-requerimiento'] input[name='id_doc_aprob']").value = id_doc_aprob;
    // }

    // grabarAnular() {
    //     let id_doc_aprob = document.querySelector("form[id='form-anular-requerimiento'] input[name='id_doc_aprob']").value;
    //     let id_rol_usuario = document.querySelector("form[id='form-anular-requerimiento'] select[name='rol_usuario']").value;
    //     let motivo = document.querySelector("form[id='form-anular-requerimiento'] textarea[name='motivo_req']").value;
    //     $.ajax({
    //         type: 'POST',
    //         url: 'anular-documento',
    //         data: { 'id_doc_aprob': id_doc_aprob, 'motivo': motivo, 'id_rol': id_rol_usuario },
    //         dataType: 'JSON',
    //         success: function (response) {
    //             if (response.status == 200) {
    //                 $('#modal-anular-req').modal('hide');
    //                 aprobarRequerimiento.mostrar();
    //                 alert("El requerimiento cambio su estado a denegado");
    //             } else {
    //                 alert("Hubo un problema, no se puedo denegar el requerimiento");
    //                 console.log(response);
    //             }

    //         }
    //     }).fail(function (jqXHR, textStatus, errorThrown) {
    //         console.log(jqXHR);
    //         console.log(textStatus);
    //         console.log(errorThrown);
    //     });

    // }

}

const aprobarRequerimiento = new Aprobar(); 
