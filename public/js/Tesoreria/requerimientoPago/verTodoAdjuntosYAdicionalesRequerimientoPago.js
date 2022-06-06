var tempArchivoAdjuntoRequerimientoPagoCabeceraList=[];

$('#ListaRequerimientoPago').on("click", "button.handleClickVerAgregarAdjuntosRequerimiento", (e) => {
    verAgregarAdjuntosRequerimientoPago(e.currentTarget.dataset.idRequerimientoPago);
});

$('#modal-ver-agregar-adjuntos-requerimiento-pago').on("change", "input.handleChangeAgregarAdjuntoRequerimientoPagoCabecera", (e) => {
    agregarAdjuntoRequerimientoPagoCabecera(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-pago').on("click", "button.handleClickEliminarArchivoCabeceraRequerimientoPago", (e) => {
    eliminarAdjuntoRequerimientoPagoCabecera(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-pago').on("click", "button.handleClickAnularAdjuntoPagoCabecera", (e) => {
    anularAdjuntoPagoCabecera(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-pago').on("click", "button.handleClickAnularAdjuntoPagoDetalle", (e) => {
    anularAdjuntoPagoDetalle(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-pago').on("change", "select.handleChangeCategoriaAdjunto", (e) => {
    actualizarCategoriaDeAdjunto(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-pago').on("click", "button.handleClickGuardarAdjuntosAdicionales", (e) => {
    guardarAdjuntos();
});

function limpiarTabla(idElement) {
    let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
    if (nodeTbody != null) {
        while (nodeTbody.children.length > 0) {
            nodeTbody.removeChild(nodeTbody.lastChild);
        }
    }
}

function obteneTodoAdjuntosRequerimientoPago(idRequerimientoPago) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-todo-archivos-adjuntos-requerimiento-pago/${idRequerimientoPago}`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function (err) {
                reject(err)
            }
        });
    });
}

function obtenerListaAdjuntosPago(idRequerimientoPago) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `lista-adjuntos-pago/${idRequerimientoPago}`,
            dataType: 'JSON',
            beforeSend: function (data) {

                $('#adjuntosPago').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success(response) {
                resolve(response);
                $('#adjuntosPago').LoadingOverlay("hide", true);

            },
            fail: function (jqXHR, textStatus, errorThrown) {
                $('#adjuntosPago').LoadingOverlay("hide", true);
                alert("Hubo un problema al cargar los adjuntos. Por favor actualice la página e intente de nuevo");
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    });
}


function verAgregarAdjuntosRequerimientoPago(idRequerimientoPago) {
    $('#modal-ver-agregar-adjuntos-requerimiento-pago').modal({
        show: true,
        backdrop: 'static'
    });
    $(":file").filestyle('clear');
    tempArchivoAdjuntoRequerimientoPagoCabeceraList=[];
    calcTamañoTotalAdjuntoPagoParaSubir();
    document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-pago'] input[name='id_requerimiento_pago']").value =idRequerimientoPago;
    if (idRequerimientoPago > 0) {
        limpiarTabla('adjuntosPago');
        limpiarTabla('adjuntosCabecera');
        limpiarTabla('adjuntosDetalle');

        obtenerListaAdjuntosPago(idRequerimientoPago).then((res) => {
            // console.log(res);
            if (res.length > 0) {
                for (let i = 0; i < res.length; i++) {
                    document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-pago'] tbody[id='body_archivos_pago']").insertAdjacentHTML('beforeend', `<tr>
                    <td><a href="/files/tesoreria/pagos/${res[i].adjunto}" target="_blank">${res[i].adjunto ? res[i].adjunto : ''}</a></td>
                    <td>${res[i].fecha_pago ? res[i].fecha_pago : ''}</td>
                    <td>${res[i].observacion ? res[i].observacion : ''}</td>
                    </tr>`);
                }
    
            }

        }).catch(function (err) {
            console.log(err)
        })

        obteneTodoAdjuntosRequerimientoPago(idRequerimientoPago).then((res) => {
            // llenar tabla cabecera
            let htmlCabecera = '';
            let tieneAccesoParaEliminarAdjuntos=false;
            if(res.id_usuario_propietario_requerimiento >0 && res.id_usuario_propietario_requerimiento == auth_user.id_usuario){
                tieneAccesoParaEliminarAdjuntos= true;
            }
            if (res.adjuntos_cabecera.length > 0) {
                (res.adjuntos_cabecera).forEach(element => {
                  
                    if (element.id_estado != 7) {
                        htmlCabecera += `<tr>
                        <td style="text-align:left;"><a href="/files/necesidades/requerimientos/pago/cabecera/${element.archivo}" target="_blank">${element.archivo}</a></td>
                        <td style="text-align:left;">${element.categoria_adjunto.descripcion}</td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-xs btn-danger btnAnularAdjuntoPagoCabecera handleClickAnularAdjuntoPagoCabecera" data-id-adjunto="${element.id_requerimiento_pago_adjunto}" title="Anular adjunto" ${tieneAccesoParaEliminarAdjuntos==true?'':'disabled'}><i class="fas fa-times fa-xs"></i></button>
                        </td>
                        </tr>`;

                    }
                });
            }
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-pago'] tbody[id='body_archivos_requerimiento_pago_cabecera']").insertAdjacentHTML('beforeend', htmlCabecera);

            // llenar tabla detalle
            let htmlDetalle = '';
            if (res.adjuntos_detalle.length > 0) {
                (res.adjuntos_detalle).forEach(element => {
                    if (element.id_estado != 7) {
                        htmlDetalle += `<tr>
                                        <td style="text-align:left;"><a href="/files/necesidades/requerimientos/pago/detalle/${element.archivo}" target="_blank">${element.archivo}</a></td>
                                        <td style="text-align:center;">
                                        <button type="button" class="btn btn-xs btn-danger btnAnularAdjuntoPagoDetalle handleClickAnularAdjuntoPagoDetalle" data-id-adjunto="${element.id_requerimiento_pago_detalle_adjunto}" title="Anular adjunto" ${tieneAccesoParaEliminarAdjuntos==true?'':'disabled'}><i class="fas fa-times fa-xs"></i></button>
                                    </td>
                                        </tr>`;

                    }
                });
            }
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-pago'] tbody[id='body_archivos_requerimiento_pago_detalle']").insertAdjacentHTML('beforeend', htmlDetalle);


        }).catch(function (err) {
            console.log(err)
        })
    }

}


function estaHabilitadoLaExtension(file) {
    let extension = (file.name.match(/(?<=\.)\w+$/g) !=null)?file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase():''; // assuming that this file has any extension
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
        || extension === ''
    ) {
        return false;
    } else {
        return true;
    }
}

function makeId() {
    let ID = "";
    let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for (let i = 0; i < 12; i++) {
        ID += characters.charAt(Math.floor(Math.random() * 36));
    }
    return ID;
}


function agregarAdjuntoRequerimientoPagoCabecera(obj){
    if (obj.files != undefined && obj.files.length > 0) {
        // console.log(obj.files);
        if((obj.files.length + tempArchivoAdjuntoRequerimientoPagoCabeceraList.length)>5){
            Swal.fire(
                '',
                'Solo puedes subir un máximo de 5 archivos',
                'warning'
            );
        }else{
            Array.prototype.forEach.call(obj.files, (file) => {
                
                if (estaHabilitadoLaExtension(file) == true) {
                    let payload = {
                        id: makeId(),
                        category: 1, //default: otros adjuntos
                        size: file.size,
                        nameFile: file.name,
                        action: 'GUARDAR',
                        file: file
                    };
                    addToTablaArchivosRequerimientoPagoCabecera(payload);
                    
                    tempArchivoAdjuntoRequerimientoPagoCabeceraList.push(payload);
                } else {
                    Swal.fire(
                        'Este tipo de archivo no esta permitido adjuntar',
                        file.name,
                        'warning'
                        );
                    }
                });
        }
            
        }

        calcTamañoTotalAdjuntoPagoParaSubir();
        return false;
        
    }
    
function calcTamañoTotalAdjuntoPagoParaSubir(){
    let tamañoTotalArchivoParaSubir=0;

    tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach(element => {
        tamañoTotalArchivoParaSubir+=element.size;
        
    });
        document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-pago'] span[id='tamaño_total_archivos_para_subir']").textContent= $.number((tamañoTotalArchivoParaSubir/1000000),2)+'MB';
}

function getcategoriaAdjunto() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-categoria-adjunto`,
            dataType: 'JSON',
            success(response) {
                resolve(response);
            },
            error: function (err) {
                reject(err)
            }
        });
    });
}

function addToTablaArchivosRequerimientoPagoCabecera(payload) {
    getcategoriaAdjunto().then((categoriaAdjuntoList) => {
        // console.log(categoriaAdjuntoList);
        let html = '';
        html = `<tr id="${payload.id}" style="text-align:center">
        <td style="text-align:left;">${payload.nameFile}</td>
        <td>
            <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto">
        `;
        categoriaAdjuntoList.forEach(element => {
            if (element.id_requerimiento_pago_categoria_adjunto == payload.category) {
                html += `<option value="${element.id_requerimiento_pago_categoria_adjunto}" selected>${element.descripcion}</option>`
            } else {
                html += `<option value="${element.id_requerimiento_pago_categoria_adjunto}">${element.descripcion}</option>`

            }
        });
        html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimientoPago" name="btnEliminarArchivoRequerimientoPago" title="Eliminar" data-id="${payload.id}" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-pago'] tbody[id='body_archivos_requerimiento_pago_cabecera']").insertAdjacentHTML('beforeend', html);

    }).catch(function (err) {
        console.log(err)
    })
}
function actualizarCategoriaDeAdjunto(obj){

    if (tempArchivoAdjuntoRequerimientoPagoCabeceraList.length > 0) {
        let indice = tempArchivoAdjuntoRequerimientoPagoCabeceraList.findIndex(elemnt => elemnt.id == obj.closest('tr').id);
        tempArchivoAdjuntoRequerimientoPagoCabeceraList[indice].category = parseInt(obj.value) > 0 ? parseInt(obj.value) : 1;
        // tempArchivoAdjuntoRequerimientoPagoCabeceraList[indice].action = 'ACTUALIZAR';
    } else {
        Swal.fire(
            '',
            'Hubo un error inesperado al intentar cambiar la categoría del adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
            'error'
        );
    }
}

function eliminarAdjuntoRequerimientoPagoCabecera(obj){
    obj.closest("tr").remove();
    var regExp = /[a-zA-Z]/g; //expresión regular
    if ((regExp.test(obj.dataset.id) == true)) {
        tempArchivoAdjuntoRequerimientoPagoCabeceraList = tempArchivoAdjuntoRequerimientoPagoCabeceraList.filter((element, i) => element.id != obj.dataset.id);
    } else {
        if (tempArchivoAdjuntoRequerimientoPagoCabeceraList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoPagoCabeceraList.findIndex(elemnt => elemnt.id == obj.dataset.id);
            tempArchivoAdjuntoRequerimientoPagoCabeceraList[indice].action = 'ELIMINAR';
        } else {
            Swal.fire(
                '',
                'Hubo un error inesperado al intentar eliminar el adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                'error'
            );
        }

    }
}

function anularAdjuntoPagoCabecera(obj){
    let idAdjunto=obj.dataset.idAdjunto;
    if(idAdjunto>0){
        $.ajax({
            type: 'POST',
            url: 'anular-adjunto-requerimiento-pago-cabecera',
            data: {id_adjunto:idAdjunto},
            dataType: 'JSON',
            beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
                $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) =>{
                if (response.status =='success') {
                    $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);

                    obj.closest('tr').remove();
                    Lobibox.notify('success', {
                        title:false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });

                } else {
                    $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
                    console.log(response);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );
                }
            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar anular los adjuntos, por favor vuelva a intentarlo',
                    'error'
                );
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }else{
        Swal.fire(
            '',
            'No existen un ID adjuntos para continuar con la acción',
            'warning'
        );
    }
}

function anularAdjuntoPagoDetalle(obj){
    let idAdjunto=obj.dataset.idAdjunto;
    if(idAdjunto>0){
        $.ajax({
            type: 'POST',
            url: 'anular-adjunto-requerimiento-pago-detalle',
            data: {id_adjunto:idAdjunto},
            dataType: 'JSON',
            beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
                $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) =>{
                if (response.status =='success') {
                    $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);

                    obj.closest('tr').remove();
                    Lobibox.notify('success', {
                        title:false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });

                } else {
                    $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
                    console.log(response);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );
                }
            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar anular los adjuntos, por favor vuelva a intentarlo',
                    'error'
                );
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }else{
        Swal.fire(
            '',
            'No existen un ID adjuntos para continuar con la acción',
            'warning'
        );
    }
    
}

function guardarAdjuntos(){
    if(tempArchivoAdjuntoRequerimientoPagoCabeceraList.length>0){
        let formData = new FormData($('#form_ver_agregar_adjuntos_requerimiento_pago')[0]);
        formData.append(`archivoAdjuntoRequerimientoPagoObject`, JSON.stringify(tempArchivoAdjuntoRequerimientoPagoCabeceraList));
        
        if (tempArchivoAdjuntoRequerimientoPagoCabeceraList.length > 0) {
            tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach(element => {
                if(element.action =='GUARDAR'){
                    formData.append(`archivoAdjuntoRequerimientoPagoCabeceraFileGuardar${element.category}[]`, element.file);
                }                    
            });
        }

        $.ajax({
            type: 'POST',
            url: 'guardar-adjuntos-adicionales-requerimiento-pago',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
                $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) =>{
                if (response.status =='success') {
                    $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);

                    Lobibox.notify('success', {
                        title:false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });
                    $('#modal-ver-agregar-adjuntos-requerimiento-pago').modal('hide');
                } else {
                    $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
                    console.log(response);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );
                }
                tempArchivoAdjuntoRequerimientoPagoCabeceraList=[];

            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                $('#modal-ver-agregar-adjuntos-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar guardar los adjuntos, por favor vuelva a intentarlo',
                    'error'
                );
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });

    }else{
        Swal.fire(
            '',
            'No existen adjuntos para guardar',
            'warning'
        );
    }
}