
var tempObjectBtnPartida;
var tempObjectBtnCentroCostos;
var tempObjectBtnInputFile;

class RequerimientoView {
    init() {
        this.agregarFilaEvent();
        // $('[name=periodo]').val(today.getFullYear());

    }
    // cabecera requerimiento
    changeMonedaSelect(e) {
        if (e.target.value == 1) {
            document.querySelector("form[id='form-requerimiento'] span[name='simboloMoneda']").textContent = 'S/';
            document.querySelector("div[id='montoMoneda']").textContent = 'S/';
            document.querySelector("form[id='form-requerimiento'] table span[class='moneda']")?document.querySelector("form[id='form-requerimiento'] table span[class='moneda']").textContent = 'S/':null;
            document.querySelector("form[id='form-requerimiento'] table span[name='simbolo_moneda']").textContent = 'S/';
            
        } else if (e.target.value == 2) {
            document.querySelector("form[id='form-requerimiento'] span[name='simboloMoneda']").textContent = '$';
            document.querySelector("div[id='montoMoneda']").textContent = '$';
            document.querySelector("form[id='form-requerimiento'] table span[class='moneda']")?document.querySelector("form[id='form-requerimiento'] table span[class='moneda']").textContent = '$':null;
            document.querySelector("form[id='form-requerimiento'] table span[name='simbolo_moneda']").textContent = '$';
        } 
    }

    changeOptEmpresaSelect(e){
        let id_empresa = e.target.value;
        this.getDataSelectSede(id_empresa);
    }

    getDataSelectSede(idEmpresa = null){
        if(idEmpresa >0){
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

    llenarSelectSede(array){

        let selectElement = document.querySelector("div[id='input-group-sede'] select[name='sede']");
        
        if(selectElement.options.length>0){
            var i, L = selectElement.options.length - 1;
            for(i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }
    
        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_sede;
            if(element.codigo == 'LIMA' || element.codigo == 'Lima'){ // default sede lima
                option.setAttribute('selected','selected');
    
            }
            option.setAttribute('data-ubigeo',element.id_ubigeo);
            option.setAttribute('data-name-ubigeo',element.ubigeo_descripcion);
            selectElement.add(option);
        });
    
    }
    
    seleccionarAmacen(data){
        // let firstSede = data[0].id_sede;
        let id_empresa_selected =  document.querySelector("select[id='empresa']").value;
        let selectAlmacen = document.querySelector("div[id='input-group-almacen'] select[name='id_almacen']");
        if(selectAlmacen.options.length>0){
            var i, L = selectAlmacen.options.length - 1;
            for(i = L; i > 0; i--) {
                if(selectAlmacen.options[i].dataset.idEmpresa == id_empresa_selected){
                     if( [4,10,11,12,13,14].includes(parseInt(selectAlmacen.options[i].dataset.idSede)) == true){ ///default almacen lima
                        selectAlmacen.options[i].setAttribute('selected',true);
                    }
                }
            }
        }
    }

    llenarUbigeo(){
        var ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        var name_ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
        document.querySelector("input[name='ubigeo']").value=ubigeo;
        document.querySelector("input[name='name_ubigeo']").value=name_ubigeo;        
        var sede = $('[name=sede]').val();
    }

    changeOptUbigeo(e){
        var ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        var name_ubigeo =document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
        var sede = $('[name=sede]').val();
    
        document.querySelector("input[name='ubigeo']").value=ubigeo;
        document.querySelector("input[name='name_ubigeo']").value=name_ubigeo;
        this.cargar_almacenes(sede);
    }

    cargar_almacenes(sede){
        if (sede !== ''){
            requerimientoCtrl.obtenerAlmacenes(sede).then(function (res) {
                var option = '';
                for (var i=0; i<res.length; i++){
                    if (res.length == 1){
                        option+='<option data-id-sede="'+res[i].id_sede+'" data-id-empresa="'+res[i].id_empresa+'" value="'+res[i].id_almacen+'" selected>'+res[i].codigo+' - '+res[i].descripcion+'</option>';

                    } else {
                        option+='<option data-id-sede="'+res[i].id_sede+'" data-id-empresa="'+res[i].id_empresa+'" value="'+res[i].id_almacen+'">'+res[i].codigo+' - '+res[i].descripcion+'</option>';

                    }
                }
                $('[name=id_almacen]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            }).catch(function (err) {
                console.log(err)
            })
        }
    }

    changeStockParaAlmacen(event) {

        switch (event.target.checked) {
            case true:
                document.querySelector("div[id='input-group-asignar_trabajador']").classList.add("oculto");
                break;
                case false:
                document.querySelector("div[id='input-group-asignar_trabajador']").classList.remove("oculto");
                
                break;
        
            default:
                break;
        }
    }

    updateConcepto(obj){
        if(obj.value.length >0){
            obj.closest('div').classList.remove("has-error");
            if(obj.closest("div").querySelector("span")){
                obj.closest("div").querySelector("span").remove();
            }   
        }else{
            obj.closest('div').classList.add("has-error");
        }
    }
    updateEmpresa(obj){
        if(obj.value.length >0){
            obj.closest('div').classList.remove("has-error");
            if(obj.closest("div").querySelector("span")){
                obj.closest("div").querySelector("span").remove();
            }   
        }else{
            obj.closest('div').classList.add("has-error");
        }
    }


    // detalle requerimiento

    agregarFilaEvent() {
        document.querySelector("button[id='btn-add-producto']").addEventListener('click', (event) => {

            vista_extendida();

            let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
            let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td></td>
            <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info" name="partida" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> <input type="text" name="id-partida[]" hidden></td>
            <td><p class="descripcion-centro-costo">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-primary" name="centroCostos" onclick="requerimientoView.cargarModalCentroCostos(this)">Seleccionar</button> <input type="text" name="id-centro-costo[]" hidden></td>
            <td><input class="form-control input-sm" type="text" name="partNumber[]" placeholder="Part number"></td>
            <td>
                <div class="form-group">
                    <textarea class="form-control input-sm descripcion" name="descripcion[]" placeholder="Descripción" onkeyup ="requerimientoView.updateDescripcionItem(this);"></textarea></td>
                </div>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm cantidad text-right" type="number" min="1" name="cantidad[]" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this);" placeholder="Cantidad">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm precio text-right" type="number" min="0" name="precioUnitario[]" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this);" placeholder="Precio U."></td>
                </div>  
            <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
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

            document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td></td>
            <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info" name="centroCostos" onclick="requerimientoView.cargarModalPartidas(this)">Seleccionar</button> <input type="text" name="id-centro-costo[]" hidden></td>
            <td><p class="descripcion-centro-costo">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-primary" name="partida" onclick="requerimientoView.cargarModalCentroCostos(this)">Seleccionar</button> <input type="text" name="id-partida[]" hidden></td>
            <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
            <td>
                <div class="form-group">
                    <textarea class="form-control input-sm descripcion" name="descripcion[]" placeholder="Descripción" onkeyup ="requerimientoView.updateDescripcionItem(this);"></textarea>
                </div>
            </td>
            <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm cantidad text-right" type="number" min="1" name="cantidad[]" onkeyup ="requerimientoView.updateSubtotal(this); requerimientoView.updateCantidadItem(this);" placeholder="Cantidad">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control input-sm precio text-right" type="number" min="0" name="precioUnitario[]" onkeyup="requerimientoView.updateSubtotal(this); requerimientoView.updatePrecioItem(this);" placeholder="Precio U.">
                </div>
            </td>
            <td style="text-align:right;"><span class="moneda" name="simboloMoneda[]">S/</span><span class="subtotal" name="subtotal[]">0.00</span></td>
            <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
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

    updateContadorItem() {
        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento']");
        // let TableTbodySize= TableTbody.childElementCount;
        let childrenTableTbody = TableTBody.children;

        for (let index = 0; index < childrenTableTbody.length; index++) {
            childrenTableTbody[index].firstElementChild.textContent = index + 1
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

    
    updateCantidadItem(obj){
        let text = obj.value;
        if(text.length>0){
            obj.closest("div").classList.remove('has-error');
            if(obj.closest("td").querySelector("span")){
                obj.closest("td").querySelector("span").remove();
            }
        }else{
            obj.closest("div").classList.add('has-error');
        }
    
    }
    updatePrecioItem(obj){
        let text = obj.value;
        if(text.length>0){
            obj.closest("div").classList.remove('has-error');
            if(obj.closest("td").querySelector("span")){
                obj.closest("td").querySelector("span").remove();
            }            
        }else{
            obj.closest("div").classList.add('has-error');
        }
    
    }
    updateDescripcionItem(obj){
        let text = obj.value;
        if(text.length>0){
            obj.closest("div").classList.remove('has-error');
            if(obj.closest("td").querySelector("span")){
                obj.closest("td").querySelector("span").remove();
            }
        }else{
            obj.closest("div").classList.add('has-error');
        }
    
    }

    calcularTotal() {
        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento']");
        let childrenTableTbody = TableTBody.children;
        let total = 0;
        for (let index = 0; index < childrenTableTbody.length; index++) {
            // console.log(childrenTableTbody[index]);
            let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad']").value?childrenTableTbody[index].querySelector("input[class~='cantidad']").value:0);
            let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value?childrenTableTbody[index].querySelector("input[class~='precio']").value:0);
            total += (cantidad * precioUnitario);
        }
        document.querySelector("label[name='total']").textContent = Util.formatoNumero(total, 2);
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
                    <td class="right ${isVisible}"><strong>${titulo.total}</strong></td>
                </tr> `;

                data['partidas'].forEach(partida => {
                    if (titulo.codigo == partida.cod_padre) {
                        html += `<tr id="par-${partida.id_partida}">
                            <td style="width:15%; text-align:left;" name="codigo">${partida.codigo}</td>
                            <td style="width:75%; text-align:left;" name="descripcion">${partida.des_pardet}</td>
                            <td style="width:15%; text-align:right;" name="importe_total" class="right ${isVisible}">${partida.importe_total}</td>
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

    apertura(id_presup) {
        if ($("#pres-" + id_presup + " ").attr('class') == 'oculto') {
            $("#pres-" + id_presup + " ").removeClass('oculto');
            $("#pres-" + id_presup + " ").addClass('visible');
        } else {
            $("#pres-" + id_presup + " ").removeClass('visible');
            $("#pres-" + id_presup + " ").addClass('oculto');
        }
    }

    changeBtnIcon(obj) {
        let actualClass = obj.children[0].className;
        if (actualClass == 'fas fa-chevron-right') {

            obj.children[0].classList.replace('fa-chevron-right', 'fa-chevron-down')
        } else {
            obj.children[0].classList.replace('fa-chevron-down', 'fa-chevron-right')
        }
    }

    selectPartida(idPartida) {
        var codigo = $("#par-" + idPartida + " ").find("td[name=codigo]")[0].innerHTML;
        var descripcion = $("#par-" + idPartida + " ").find("td[name=descripcion]")[0].innerHTML;
        // var importe_total = $("#par-"+idPartida+" ").find("td[name=importe_total]")[0].innerHTML;
        tempObjectBtnPartida.nextElementSibling.value = idPartida;
        tempObjectBtnPartida.textContent = 'Cambiar';

        let tr = tempObjectBtnPartida.closest("tr");
        tr.querySelector("p[class='descripcion-partida']").textContent = descripcion
        tr.querySelector("p[class='descripcion-partida']").setAttribute('title', codigo);

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
                                    <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs" onclick="requerimientoView.selectCentroCosto(${hijo.id_centro_costo},'${hijo.codigo}','${hijo.descripcion}');">Seleccionar</button></td>
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
    // construirCentroCostos(data) {
    //     var html = '';
    //     data.forEach((padre, index) => {
    //         if (padre.id_padre == null) {
    //             html += `
    //             <div class="panel panel-default">
    //                 <div class="panel-heading" role="tab" id="heading${index}">
    //                     <h4 class="panel-title">
    //                         <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}" >
    //                             ${padre.descripcion} 
    //                             <div class="box-tools pull-right">
    //                                 <button type="button" class="btn btn-box-tool" style="position:absolute; right:20px; margin-top:-5px;" data-toggle="collapse">
    //                                     <i class="fa fa-plus"></i>
    //                                 </button>
    //                             </div>
    //                         </a>
    //                     </h4>
    //                 </div>
    //                 <div id="collapse${index}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading${index}" >   
    //                     <div class="box-body" style="display: block;">`;
    //             data.forEach(hijo => {
    //                 if (padre.id_centro_costo == hijo.id_padre) {
    //                     if ((hijo.id_padre > 0) && (hijo.estado == 1)) {
    //                         if (hijo.nivel == 2) {
    //                             html += `<div class="okc-cc okc-niv-2" onClick="requerimientoView.selectCentroCosto(${hijo.id_centro_costo} , '${hijo.codigo}' ,'${hijo.descripcion}');"> ${hijo.codigo} - ${hijo.descripcion} </div>`;
    //                         }
    //                     }
    //                     data.forEach(hijo3 => {
    //                         if (hijo.id_centro_costo == hijo3.id_padre) {
    //                             if ((hijo3.id_padre > 0) && (hijo3.estado == 1)) {
    //                                 if (hijo3.nivel == 3) {
    //                                     html += `<div class="okc-cc okc-niv-3" onClick="requerimientoView.selectCentroCosto(${hijo3.id_centro_costo} , '${hijo3.codigo}','${hijo3.descripcion}');"> ${hijo3.codigo} - ${hijo3.descripcion} </div>`;
    //                                 }
    //                             }
    //                         }
    //                     });
    //                 }


    //             });

    //             html += `</div></div></div>`;
    //         }
    //     });
    //     document.querySelector("div[name='centro-costos-panel']").innerHTML = html;

    // }


    selectCentroCosto(idCentroCosto, codigo, descripcion) {


        tempObjectBtnCentroCostos.nextElementSibling.value = idCentroCosto;
        tempObjectBtnCentroCostos.textContent = 'Cambiar';

        let tr = tempObjectBtnCentroCostos.closest("tr");
        tr.querySelector("p[class='descripcion-centro-costo']").textContent = descripcion
        tr.querySelector("p[class='descripcion-centro-costo']").setAttribute('title', codigo);

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

    // adjuntos detalle requerimiento

    adjuntarArchivoItem(obj) {

        tempObjectBtnInputFile = obj;
        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
    }

    agregarAdjunto(event){
        //  console.log(event.target.value);
        let fileList = event.target.files;
        let file = fileList[0];
        let extension = file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase(); // assuming that this file has any extension
        //  console.log(extension);
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
                alert('Extensión de archivo incorrecta (NO se permite .'+extension+').  La entrada del archivo se borra.');
                event.target.value = '';
            }
            else {
                // let archivo ={
                //     id_adjunto: 0,
                //     id_requerimiento: id_req,
                //     id_detalle_requerimiento: id_detalle_requerimiento,
                //     archivo:file.name,
                //     fecha_registro: new Date().toJSON().slice(0, 10),
                //     estado: 1
                //     // file:event.target.files[0]
                // }
                // let only_file = event.target.files[0]
                // let inputFile = tempObjectBtnInputFile.parentNode.querySelector("input[type='file']");
                // inputFile.value=fileList;
                // nuevoFormulario.append(inputFile.name, event.target.files[0]);

                // console.log(inputFile.name);
                // console.log(inputFile);


                // formData.append('adjunto_item[]', event.target.files[0], file.name);

                
        }
    }

    actionGuardarEditarRequerimiento(){
        let continuar = true;
        if(document.querySelector("tbody[id='body_detalle_requerimiento']").childElementCount==0){
            alert("Ingrese por lo menos un producto/servicio");
            return false;
        }

        if(document.querySelector("input[name='concepto']").value == ''){
            var newSpanInfo = document.createElement("span");
            newSpanInfo.classList.add('text-danger');
            newSpanInfo.textContent = '(Ingrese un concepto/motivo)';
            document.querySelector("input[name='concepto']").closest('div').querySelector("h5").appendChild(newSpanInfo);
            document.querySelector("input[name='concepto']").closest('div').classList.add('has-error');

        }
        if(document.querySelector("select[name='empresa']").value == 0){
            var newSpanInfo = document.createElement("span");
            newSpanInfo.classList.add('text-danger');
            newSpanInfo.textContent = '(Seleccione una empresa)';
            document.querySelector("select[name='empresa']").closest('div').querySelector("h5").appendChild(newSpanInfo);
            document.querySelector("select[name='empresa']").closest('div').classList.add('has-error');

        }

        let tbodyChildren=document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let index = 0; index < tbodyChildren.length; index++) {

            if(tbodyChildren[index].querySelector("input[class~='cantidad']").value == ''){
                var newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = 'Ingrese una cantidad';
                tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').appendChild(newSpanInfo);
                tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');

            }
            if(tbodyChildren[index].querySelector("input[class~='precio']").value == ''){
                var newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = 'Ingrese un precio';
                tbodyChildren[index].querySelector("input[class~='precio']").closest('td').appendChild(newSpanInfo);
                tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');

            }
            if(tbodyChildren[index].querySelector("textarea[class~='descripcion']")){
                if(tbodyChildren[index].querySelector("textarea[class~='descripcion']").value == ''){
                    var newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese una descripción';
                    tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
    
                }
                

            }
        }
     
        if(continuar){
            console.log("se va a guardar");
        }else{
            console.log("no se va a guardar");
        }
    }

}

const requerimientoView = new RequerimientoView();
