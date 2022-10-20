

$(document).ready(function() {
    $('#form-procesarPago').on("click", "label.handleClickVerAdjuntosLogisticos", (e) => {
        verAdjuntosLogisticos();
    });
    
});



function verAdjuntosLogisticos(obj){
    let idOrden = parseInt(document.querySelector("div[id='modal-procesarPago'] input[name='id_oc']").value >0? parseInt(document.querySelector("input[name='id_oc']").value):"");
    document.querySelector("div[id='modal-lista-adjuntos'] span[id='modal-title']").textContent = "logísticos";
    $('#modal-lista-adjuntos #listaAdjuntos').html(`<tr> <td style="text-align:center;" colspan="3"></td></tr>`);

    $('#modal-lista-adjuntos').modal({
        show: true,
        backdrop: 'true'
    });

    this.obteneAdjuntosLogisticos(idOrden).then((res) => {

        let htmlAdjunto = '';
        if (res.length > 0) {
            (res).forEach(element => {

                    htmlAdjunto+= '<tr id="'+element.id_adjunto+'">'
                        htmlAdjunto+='<td>'
                            htmlAdjunto+='<a href="/files/logistica/comporbantes_proveedor/'+element.archivo+'" target="_blank">'+element.archivo+'</a>'
                        htmlAdjunto+='</td>'
                    htmlAdjunto+= '</tr>'

            });
        }else{
            htmlAdjunto = `<tr>
            <td style="text-align:center;" colspan="3">Sin adjuntos para mostrar</td>
            </tr>`;
        }
        $('#modal-lista-adjuntos #listaAdjuntos').html(htmlAdjunto)


    }).catch(function (err) {
        console.log(err)
    })
}


function obteneAdjuntosLogisticos(id_orden) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-archivos-adjuntos-orden/${id_orden}`,
            dataType: 'JSON',
            beforeSend: (data) => {
                
        },
            success(response) {
                // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                resolve(response);
            },
            error: function (err) {
                // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                reject(err)
            }
        });
    });
}