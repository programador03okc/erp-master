


class PresupuestoInternoView{
    constructor(model) {
        this.model = model;
    }

    eventos = ()=>{
        $('#ListaDetalleRequerimiento tbody').on("click", "button.handleClickCargarModalPartidas", (e) => {
            let id_presupuesto_interno = document.querySelector("form[id='form-requerimiento'] select[name='id_presupuesto_interno']").value;
            if(id_presupuesto_interno>0){
                this.cargarPresupuestoDetalle(id_presupuesto_interno);
            }else{
                Swal.fire(
                    '',
                    'No se puedo seleccionar el id de presupuesto para obtener su detalle, vuelva a intentar seleccionar un presupuesto interno.',
                    'warning'
                );
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

        let html = '';

        data.forEach(presupuesto => {
            html += `
            <div id='${presupuesto.codigo}' class="panel panel-info" style="width:100%; overflow: auto;">
                <h5 class="panel-heading handleClickaperturaPresupuesto" data-id-presupuesto-interno="${presupuesto.id_presupuesto_interno}" style="margin: 0; cursor: pointer;">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${presupuesto.descripcion}
                </h5>
                <div id="presupuesto-interno-${presupuesto.id_presupuesto_interno}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id="listaPartidas" width="100%" style="font-size:0.9em">
                        <tbody>
            `;

            let sumaMonto = 0; 
            presupuesto['detalle'].forEach(detalle => {
                // if (detalle.id_presupuesto_interno == presupuesto.id_presupuesto_interno) {

                    if(detalle.registro==1){

                        sumaMonto=$.number((parseFloat(detalle.enero.replace(",",""))
                        +parseFloat(detalle.febrero.replace(",",""))
                        +parseFloat(detalle.marzo.replace(",",""))
                        +parseFloat(detalle.abril.replace(",",""))
                        +parseFloat(detalle.mayo.replace(",",""))
                        +parseFloat(detalle.junio.replace(",",""))
                        +parseFloat(detalle.julio.replace(",",""))
                        +parseFloat(detalle.agosto.replace(",",""))
                        +parseFloat(detalle.setiembre.replace(",",""))
                        +parseFloat(detalle.octubre.replace(",",""))
                        +parseFloat(detalle.noviembre.replace(",",""))
                        +parseFloat(detalle.diciembre.replace(",","")))
                        ,2,".",",");

                        html += `
                        <tr id="com-${detalle.id_presupuesto_interno_detalle}">
                        <td><strong>${detalle.partida}</strong></td>
                        <td><strong>${detalle.descripcion}</strong></td>
                        <td class="right" style="text-align:right;" ><strong>S/${sumaMonto}</strong></td>
                        </tr> `;
                    }else{
                        html += `<tr id="par-${detalle.id_presupuesto_interno_detalle}">
                        <td style="width:15%; text-align:left;" name="partida">${detalle.partida}</td>
                        <td style="width:75%; text-align:left;" name="descripcion">${detalle.descripcion}</td>
                        <td style="width:15%; text-align:right;" name="importe_total" class="right" >S/${sumaMonto}</td>
                        <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs handleClickSelectDetallePresupuesto" 
                            data-id-presupuesto-interno-detalle="${detalle.id_presupuesto_interno_detalle}"
                            data-partida="${detalle.partida}"
                            data-descripcion="${detalle.descripcion}"
                            data-monto-total="${sumaMonto}"
                            >Seleccionar</button></td>
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
}