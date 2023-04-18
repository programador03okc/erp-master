


class PresupuestoInternoView{
    constructor(model) {
        this.model = model;
    }

    eventos = ()=>{

        // $('body').on("change", "select.handleChangePresupuestoInterno", (e) => {
        //     this.seleccionarPresupuestoInterno(e.currentTarget);
        // });

        $('tbody').on("click", "button.handleClickCargarModalPartidas", (e) => {
            let id_presupuesto_interno = document.querySelector("select[name='id_presupuesto_interno']").value;
            if(id_presupuesto_interno>0){
                this.cargarPresupuestoDetalle(id_presupuesto_interno);
            }else{
                // Swal.fire(
                //     '',
                //     'No se puedo seleccionar el id de presupuesto para obtener su detalle, vuelva a intentar seleccionar un presupuesto interno.',
                //     'warning'
                // );
            }
        });

        $('#modal-partidas').on("click", "h5.handleClickaperturaPresupuesto", (e) => {
            this.apertura(e.currentTarget.dataset.idPresupuestoInterno);
            this.changeBtnIcon(e);
        });
        $('#modal-partidas').on("click", "button.handleClickSelectDetallePresupuesto", (e) => {
            this.selectPresupuestoInternoDetalle(e.currentTarget);

        });

    }

    selectPresupuestoInternoDetalle(obj) {
        // console.log(idPartida);
        let idPresupuestoInternoDetalle= obj.dataset.idPresupuestoInternoDetalle;
        let partida= obj.dataset.partida;
        let descripcion= obj.dataset.descripcion;
        let montoTotal= obj.dataset.montoTotal;

        tempObjectBtnPartida.nextElementSibling.querySelector("input[class='partida']").value = idPresupuestoInternoDetalle;
        tempObjectBtnPartida.textContent = 'Cambiar';

        let tr = tempObjectBtnPartida.closest("tr");
        tr.querySelector("p[class='descripcion-partida']").dataset.idPartida = idPresupuestoInternoDetalle;
        tr.querySelector("p[class='descripcion-partida']").textContent = partida
        tr.querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal = montoTotal;
        tr.querySelector("p[class='descripcion-partida']").setAttribute('title', descripcion);

        this.updatePartidaItem(tempObjectBtnPartida.nextElementSibling.querySelector("input[class='partida']"));
        $('#modal-partidas').modal('hide');

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

    cargarPresupuestoDetalle(idPresupuestoIterno){

        this.model.obtenerListaDetallePrespuestoInterno(idPresupuestoIterno).then((res) => {
            this.construirListaDetallePrespuestoInterno(res);

        }).catch(function (err) {
            console.log(err)
        })
    }

    construirListaDetallePrespuestoInterno(data){
        console.log(data);

        let html='';

        data.forEach(presupuesto => {
            html += `
            <div id='${presupuesto.codigo}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading handleClickaperturaPresupuesto" data-id-presupuesto-interno="${presupuesto.id_presupuesto_interno}" style="margin: 0; cursor: pointer;">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${presupuesto.descripcion}
                </h5>
                <div id="presupuesto-interno-${presupuesto.id_presupuesto_interno}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id="listaPartidas" width="100%" style="font-size:0.9em">
                        <tbody>
            `;

            
            html += `
            <tr>
            <td><strong>PARTIDA</strong></td>
            <td><strong>DESCRIPCIÓN</strong></td>
            <td style="background-color: #ddeafb;"><strong>INICIAL</strong></td>
            <td style="background-color: #fbdddd;"><strong>CONSUMIDO</strong></td>
            <td style="background-color: #e5fbdd;"><strong>SALDO</strong></td>
            </tr> `;
            let montoInicial = 0; 
            let montoConsumido = 0; 
            let montoSaldo = 0; 
            presupuesto['detalle'].forEach(detalle => {
                // if (detalle.id_presupuesto_interno == presupuesto.id_presupuesto_interno) {
                    montoInicial=$.number((parseFloat(detalle.monto_inicial)),2,".",",");
                    montoConsumido=$.number((parseFloat(detalle.monto_consumido)),2,".",",");
                    montoSaldo=$.number((parseFloat(detalle.monto_saldo)),2,".",",");

                    if(detalle.registro==1){


                        html += `
                        <tr id="com-${detalle.id_presupuesto_interno_detalle}">
                        <td><strong>${detalle.partida}</strong></td>
                        <td><strong>${detalle.descripcion}</strong></td>
                        <td class="right" style="text-align:right; background-color: #ddeafb;" ><strong>S/${montoInicial}</strong></td>
                        <td class="right" style="text-align:right; background-color: #fbdddd;" ><strong>S/${montoConsumido}</strong></td>
                        <td class="right" style="text-align:right; background-color: #e5fbdd;" ><strong>S/${montoSaldo}</strong></td>
                        </tr> `;
                    }else{
                        html += `<tr id="par-${detalle.id_presupuesto_interno_detalle}">
                        <td style="width:15%; text-align:left;" name="partida">${detalle.partida}</td>
                        <td style="width:75%; text-align:left;" name="descripcion">${detalle.descripcion}</td>
                        <td style="width:15%; text-align:right; background-color: #ddeafb;" name="monto_total" class="right" >S/${montoInicial}</td>
                        <td style="width:15%; text-align:right; background-color: #fbdddd;" name="monto_consumido" class="right" >S/${montoConsumido}</td>
                        <td style="width:15%; text-align:right; background-color: #e5fbdd;" name="monto_saldo" class="right" >S/${montoSaldo}</td>
                        <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs handleClickSelectDetallePresupuesto" 
                            data-id-presupuesto-interno-detalle="${detalle.id_presupuesto_interno_detalle}"
                            data-partida="${detalle.partida}"
                            data-descripcion="${detalle.descripcion}"
                            data-monto-total="${montoInicial}"
                            data-monto-consumido="${montoConsumido}"
                            data-monto-saldo="${montoSaldo}"
                            ><i class="fas fa-check"></i></button></td>
                    </tr>`;

                    }

                // }


            });
            html += `
                    </tbody>
                </table>
            </div>
        </div>`;
        });
        document.querySelector("div[id='listaPresupuesto']").innerHTML = html;


    }

    
    apertura(idPresupuestoInterno) {
        if ($("#presupuesto-interno-" + idPresupuestoInterno + " ").hasClass('oculto')) {
            $("#presupuesto-interno-" + idPresupuestoInterno + " ").removeClass('oculto');
            $("#presupuesto-interno-" + idPresupuestoInterno + " ").addClass('visible');
        } else {
            $("#presupuesto-interno-" + idPresupuestoInterno + " ").removeClass('visible');
            $("#presupuesto-interno-" + idPresupuestoInterno + " ").addClass('oculto');
        }


    }

    changeBtnIcon(obj) {

        if (obj.currentTarget.children[0].className == 'fas fa-chevron-right') {

            obj.currentTarget.children[0].classList.replace('fa-chevron-right', 'fa-chevron-down')
        } else {
            obj.currentTarget.children[0].classList.replace('fa-chevron-down', 'fa-chevron-right')
        }
    }



    llenarComboPresupuestoInterno(idGrupo,idArea){
        this.model.comboPresupuestoInterno(idGrupo, idArea).then((res) => {
            // console.log(res);
            let selectElement = document.querySelector("select[name='id_presupuesto_interno']");
            $("input[name='codigo_presupuesto_interno']").val("");


            if (selectElement.options.length > 0) {
                let i, L = selectElement.options.length - 1;
                for (i = L; i >= 0; i--) {
                    selectElement.remove(i);
                }
            }
            
            let optionDefault = document.createElement("option");
            optionDefault.text = "selecciona un presupuesto interno";
            optionDefault.value = "";
            optionDefault.setAttribute('data-codigo', "");
            optionDefault.setAttribute('data-id-grupo', "");
            optionDefault.setAttribute('data-id-area', "");
            selectElement.add(optionDefault);

            res.forEach(element => {
                let option = document.createElement("option");
                option.text = element.descripcion+(element.estado !=2?'(NO APROBADO)':'');
                option.value = element.id_presupuesto_interno;
                option.setAttribute('data-codigo', element.codigo);
                option.setAttribute('data-id-grupo', element.id_grupo);
                option.setAttribute('data-id-area', element.id_area);
                selectElement.add(option);
            });

        }).catch(function (err) {
            console.log(err)
        });
    }

    // seleccionarPresupuestoInterno(obj){
    //     if(obj.value >0){
    //         const codigoPresupuestoInterno=  obj.options[obj.selectedIndex].dataset.codigo;
    //         $("input[name='codigo_presupuesto_interno']").val(codigoPresupuestoInterno);
    //         if( document.querySelector("select[name='division").options[document.querySelector("select[name='division").selectedIndex].dataset.idGrupo == 3){
    //             this.ocultarOpcionCentroDeCosto();
    //         }else{
    //             this.mostrarOpcionCentroDeCosto();
    //         }
    //     }else{
    //         this.mostrarOpcionCentroDeCosto();

    //     }

    // }

    // ocultarOpcionCentroDeCosto(){
    //     $("button[name=centroCostos]").addClass("oculto");
    //     $("p[class=descripcion-centro-costo]").attr("hidden",true);
    // }
    // mostrarOpcionCentroDeCosto(){
    //     $("button[name=centroCostos]").removeClass("oculto");
    //     $("p[class=descripcion-centro-costo]").removeAttr("hidden");
    // }
}