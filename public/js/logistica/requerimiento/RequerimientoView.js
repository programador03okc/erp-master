
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

        var idRequerimiento = localStorage.getItem("idRequerimiento");
        if (idRequerimiento !== null){
            historialRequerimientoView.cargarRequerimiento(idRequerimiento)
            localStorage.removeItem("idRequerimiento");
            vista_extendida();

        }

    }

    imprimirRequerimientoPdf(){
        var id = document.getElementsByName("id_requerimiento")[0].value;
        window.open('imprimir-requerimiento-pdf/'+id+'/0');
    
    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if(nodeTbody!=null){
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }

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
                requerimientoView.seleccionarAlmacen(res)
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

    seleccionarAlmacen(data) {
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
        requerimientoView.limpiarTabla('listaPartidas');

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
                    <table class="table table-bordered table-condensed partidas" id="listaPartidas" width="100%" style="font-size:0.9em">
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
        $('#modal-partidas').LoadingOverlay("hide", true);

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
        requerimientoView.limpiarTabla('listaCentroCosto');

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
                    <table class="table table-bordered table-condensed partidas" id='listaCentroCosto' width="100%" style="font-size:0.9em">
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
        $('#modal-centro-costos').LoadingOverlay("hide", true);

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
                html += `<button type="button" class="btn btn-info btn-md" name="btnDescargarArchivoRequerimiento" title="Descargar" onclick="ArchivoAdjunto.descargarArchivoRequerimiento('${element.id}');" ><i class="fas fa-file-archive"></i></button>`;
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
                            historialRequerimientoView.cargarRequerimiento(response.id_requerimiento);
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


}

const requerimientoView = new RequerimientoView();


