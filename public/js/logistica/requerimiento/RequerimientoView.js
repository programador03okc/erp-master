var tempObjectBtnPartida;
var tempObjectBtnCentroCostos;
class RequerimientoView {
    init() {
        this.agregarFilaEvent();
    }

    agregarFilaEvent() {
        document.querySelector("button[id='btn-add-producto']").addEventListener('click', (event) => {

            vista_extendida();

            let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend',`<tr style="text-align:center">
            <td></td>
            <td><input class="form-control input-sm" type="text" name="part-number[]" placeholder="Part number"></td>
            <td><textarea class="form-control input-sm" name="descripcion[]" placeholder="Descripción"></textarea></td>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td><input class="form-control input-sm cantidad" type="number" min="1" name="cantidad[]" onkeyup ="requerimientoView.updateSubtotal(this);"  placeholder="0"></td>
            <td><input class="form-control input-sm precio" type="number" min="0" name="precio-unitario[]" onkeyup="requerimientoView.updateSubtotal(this)" placeholder="0.00"></td>
            <td style="text-align:right;"><span class="moneda" name="simbolo_moneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info" name="partida" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> <input type="text" name="id-partida[]" hidden></td>
            <td><p class="descripcion-centro-costo">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-primary" name="centro-costos" onclick="requerimientoView.cargarModalCentroCostos(this)">Seleccionar</button> <input type="text" name="id-centro-costo[]" hidden></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item"></textarea></td>
            <td>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)" ><i class="fas fa-paperclip"></i></button>
                    <button type="button" class="btn btn-danger btn-xs" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
            </tr>`);

            this.updateContadorItem();

        });
        document.querySelector("button[id='btn-add-servicio']").addEventListener('click', (event) => {

            vista_extendida();

            let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend',`<tr style="text-align:center">
            <td></td>
            <td></td>
            <td><textarea class="form-control input-sm" name="descripcion[]" placeholder="Descripción"></textarea></td>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td><input class="form-control input-sm cantidad" type="number" min="1" name="cantidad[]" onkeyup ="requerimientoView.updateInputCantidadItem(this);"  placeholder="0"></td>
            <td><input class="form-control input-sm precio" type="number" min="0" name="precio-unitario[]" onkeyup="requerimientoView.updateInputPrecioUnitarioItem(this)" placeholder="0.00"></td>
            <td style="text-align:right;"><span class="moneda" name="simbolo_moneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><button type="button" class="btn btn-xs btn-info" name="partida" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> <input type="text" name="id-partida[]" hidden></td>
            <td><button type="button" class="btn btn-xs btn-primary" name="centro-costos" onclick="requerimientoView.cargarModalCentroCostos(this)">Seleccionar</button> <input type="text" name="id-centro-costo[]" hidden></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item"></textarea></td>
            <td>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-warning btn-xs" name="btnAdjuntarArchivoItem[]" title="Adjuntos" onclick="requerimientoView.adjuntarArchivoItem(this)" ><i class="fas fa-paperclip"></i></button>
                    <button type="button" class="btn btn-danger btn-xs" name="btnEliminarItem[]" title="Eliminar" onclick="requerimientoView.eliminarItem(this)" ><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
            </tr>`);

            this.updateContadorItem();

        });
    }

    updateContadorItem(){
        let TableTBody =document.querySelector("tbody[id='body_detalle_requerimiento']");
        // let TableTbodySize= TableTbody.childElementCount;
        let childrenTableTbody= TableTBody.children;

        for (let index = 0; index < childrenTableTbody.length; index++) {
            childrenTableTbody[index].firstElementChild.textContent=index+1
        }
    }

    updateSubtotal(obj){
        let tr =obj.closest("tr");
        let cantidad = parseFloat(tr.querySelector("input[class~='cantidad']").value);
        let precioUnitario = parseFloat(tr.querySelector("input[class~='precio']").value);
        let subtotal = ( cantidad* precioUnitario);
        tr.querySelector("span[class='subtotal']").textContent= Util.formatoNumero(subtotal,2);
        this.calcularTotal();
        
    }

    calcularTotal(){
        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento']");
        let childrenTableTbody= TableTBody.children;
        let total =0;
        for (let index = 0; index < childrenTableTbody.length; index++) {
            console.log(childrenTableTbody[index]);
            let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad']").value);
            let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value);
            total+= (cantidad*precioUnitario);
        }
        document.querySelector("label[name='total']").textContent= Util.formatoNumero(total,2);
    }

    // partidas 
    cargarModalPartidas(obj) {
        tempObjectBtnPartida = obj;
        var id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
        var id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
        var usuarioProyectos = false;
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
        requerimientoCtrl.obtenerListaPartidas(idGrupo, idProyecto).then(function (res) {
            requerimientoView.construirListaPartidas(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirListaPartidas(data) {
        document.querySelector("div[id='listaPartidas']").innerHTML = data;
    }

    selectPartida(idPartida) {
        var codigo = $("#par-" + idPartida + " ").find("td[name=codigo]")[0].innerHTML;
        var descripcion = $("#par-" + idPartida + " ").find("td[name=descripcion]")[0].innerHTML;
        // var importe_total = $("#par-"+idPartida+" ").find("td[name=importe_total]")[0].innerHTML;
        tempObjectBtnPartida.nextElementSibling.value = idPartida;
        tempObjectBtnPartida.textContent = 'Cambiar';

        let tr =tempObjectBtnPartida.closest("tr");
        tr.querySelector("p[class='descripcion-partida']").textContent= descripcion
        tr.querySelector("p[class='descripcion-partida']").setAttribute('title',codigo);

        $('#modal-partidas').modal('hide');
        tempObjectBtnPartida = null;
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
        var html = '';
        data.forEach((padre, index) => {
            if (padre.id_padre == null) {
                html += `
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading${index}">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}" >
                                ${padre.descripcion} 
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" style="position:absolute; right:20px; margin-top:-5px;" data-toggle="collapse">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse${index}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading${index}" >   
                        <div class="box-body" style="display: block;">`;
                data.forEach(hijo => {
                    if (padre.id_centro_costo == hijo.id_padre) {
                        if ((hijo.id_padre > 0) && (hijo.estado == 1)) {
                            if (hijo.nivel == 2) {
                                html += `<div class="okc-cc okc-niv-2" onClick="requerimientoView.selectCentroCosto(${hijo.id_centro_costo} , '${hijo.codigo}' ,'${hijo.descripcion}');"> ${hijo.codigo} - ${hijo.descripcion} </div>`;
                            }
                        }
                        data.forEach(hijo3 => {
                            if (hijo.id_centro_costo == hijo3.id_padre) {
                                if ((hijo3.id_padre > 0) && (hijo3.estado == 1)) {
                                    if (hijo3.nivel == 3) {
                                        html += `<div class="okc-cc okc-niv-3" onClick="requerimientoView.selectCentroCosto(${hijo3.id_centro_costo} , '${hijo3.codigo}','${hijo3.descripcion}');"> ${hijo3.codigo} - ${hijo3.descripcion} </div>`;
                                    }
                                }
                            }
                        });
                    }


                });

                html += `</div></div></div>`;
            }
        });
        document.querySelector("div[name='centro-costos-panel']").innerHTML = html;

    }


    selectCentroCosto(idCentroCosto,codigo,descripcion){


        tempObjectBtnCentroCostos.nextElementSibling.value = idCentroCosto;
        tempObjectBtnCentroCostos.textContent = 'Cambiar';

        let tr =tempObjectBtnCentroCostos.closest("tr");
        tr.querySelector("p[class='descripcion-centro-costo']").textContent= descripcion
        tr.querySelector("p[class='descripcion-centro-costo']").setAttribute('title',codigo);

        $('#modal-centro-costos').modal('hide');
        tempObjectBtnCentroCostos = null;
        // componerTdItemDetalleRequerimiento();
    }

    eliminarItem(obj){
        let tr =obj.closest("tr");
        tr.remove();

        this.updateContadorItem();

    }

}

const requerimientoView = new RequerimientoView();
