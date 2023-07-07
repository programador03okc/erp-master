var tempArchivoAdjuntoRequerimientoCabeceraList=[];

$('table').on("click", "button.handleClickVerAgregarAdjuntosRequerimiento", (e) => {
    verAgregarAdjuntosRequerimiento(e.currentTarget);
});

$('#modal-ver-agregar-adjuntos-requerimiento-compra').on("change", "input.handleChangeAgregarAdjuntoRequerimientoCompraCabecera", (e) => {
    agregarAdjuntoRequerimientoCabeceraCompra(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-compra').on("click", "button.handleClickEliminarArchivoCabeceraRequerimientoCompra", (e) => {
    eliminarAdjuntoRequerimientoCompraCabecera(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-compra').on("click", "button.handleClickAnularAdjuntoCabecera", (e) => {
    anularAdjuntoCabecera(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-compra').on("click", "button.handleClickAnularAdjuntoDetalle", (e) => {
    anularAdjuntoDetalle(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-compra').on("change", "select.handleChangeCategoriaAdjunto", (e) => {
    actualizarCategoriaDeAdjunto(e.currentTarget);
});
$('#modal-ver-agregar-adjuntos-requerimiento-compra').on("click", "button.handleClickGuardarAdjuntosAdicionales", (e) => {
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

function obteneTodoAdjuntosRequerimiento(idRequerimiento) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-todo-archivos-adjuntos-requerimiento-logistico/${idRequerimiento}`,
            dataType: 'JSON',
            beforeSend: (data) => {
            $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosCabecera').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
            $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDetalle').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
        },
            success(response) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosCabecera').LoadingOverlay("hide", true);
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDetalle').LoadingOverlay("hide", true);
                resolve(response);
            },
            error: function (err) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosCabecera').LoadingOverlay("hide", true);
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDetalle').LoadingOverlay("hide", true);
                reject(err)
            }
        });
    });
}
function obteneAdjuntosPago(idRequerimiento) {
    // console.log(idRequerimiento);
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-archivos-adjuntos-pago/${idRequerimiento}`,
            dataType: 'JSON',
            beforeSend: (data) => {
            $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDePagos').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
        },
            success(response) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDePagos').LoadingOverlay("hide", true);
                resolve(response);
            },
            error: function (err) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDePagos').LoadingOverlay("hide", true);
                reject(err)
            }
        });
    });
}
function obtenerOtrosAdjuntosTesoreria(idRequerimiento) {
    // console.log(idRequerimiento);
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-otros-adjuntos-tesoreria-orden-requerimiento/${idRequerimiento}`,
            dataType: 'JSON',
            beforeSend: (data) => {
            $('#modal-ver-agregar-adjuntos-requerimiento-compra #otrosAdjuntosDeTesoreria').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
        },
            success(response) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #otrosAdjuntosDeTesoreria').LoadingOverlay("hide", true);
                resolve(response);
            },
            error: function (err) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #otrosAdjuntosDeTesoreria').LoadingOverlay("hide", true);
                reject(err)
            }
        });
    });
}
function obtenerAdjuntosLogisticos(idRequerimiento) {
    // console.log(idRequerimiento);
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'GET',
            url: `listar-adjuntos-logisticos/${idRequerimiento}`,
            dataType: 'JSON',
            beforeSend: (data) => {
            $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDeLogistica').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
        },
            success(response) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDeLogistica').LoadingOverlay("hide", true);
                resolve(response);
            },
            error: function (err) {
                $('#modal-ver-agregar-adjuntos-requerimiento-compra #adjuntosDeLogistica').LoadingOverlay("hide", true);
                reject(err)
            }
        });
    });
}

function verAgregarAdjuntosRequerimiento(obj) {
    let idRequerimiento= obj.dataset.idRequerimiento;
    let codigoRequerimiento= obj.dataset.codigoRequerimiento;
    tempArchivoAdjuntoRequerimientoCabeceraList=[];
    calcTamañoTotalAdjuntoLogisticoParaSubir();
    $('#modal-ver-agregar-adjuntos-requerimiento-compra').modal({
        show: true,
        backdrop: 'static'
    });
    $(":file").filestyle('clear');

    document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] input[name='id_requerimiento']").value =idRequerimiento;
    document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] span[id='codigo_requerimiento']").textContent =codigoRequerimiento;
    cargarAdjuntosLogisticosYPago(idRequerimiento);

    // console.log(obj.dataset.sustento);
    $('#modal-ver-agregar-adjuntos-requerimiento-compra').find('[name="requerimiento_sustentado"]').attr('data-id',idRequerimiento);
    $('#modal-ver-agregar-adjuntos-requerimiento-compra').find('[name="requerimiento_sustentado"]').prop('checked',false);
    if (obj.dataset.sustento =='true') {
        $('#modal-ver-agregar-adjuntos-requerimiento-compra').find('[name="requerimiento_sustentado"]').prop('checked',true);
    }
}

function cargarAdjuntosLogisticosYPago(idRequerimiento){
    if (idRequerimiento > 0) {
        limpiarTabla('adjuntosCabecera');
        limpiarTabla('adjuntosDetalle');
        limpiarTabla('adjuntosDePagos');

        obteneTodoAdjuntosRequerimiento(idRequerimiento).then((res) => {
            // console.log(res);
            // usuario_propietario_requerimiento
            let tieneAccesoParaEliminarAdjuntos=false;
            if(res.id_usuario_propietario_requerimiento >0 && res.id_usuario_propietario_requerimiento == auth_user.id_usuario){
                tieneAccesoParaEliminarAdjuntos= true;
            }
            // llenar tabla cabecera
            let htmlCabecera = '';
            if (res.adjuntos_cabecera.length > 0) {
                (res.adjuntos_cabecera).forEach(element => {
                    if (element.estado != 7) {
                        htmlCabecera += `<tr>
                        <td style="text-align:left;"><a href="/files/necesidades/requerimientos/bienes_servicios/cabecera/${element.archivo}" target="_blank">${element.archivo}</a></td>
                        <td style="text-align:left;">${element.categoria_adjunto.descripcion}</td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-xs btn-danger btnAnularAdjuntoCabecera handleClickAnularAdjuntoCabecera" data-id-adjunto="${element.id_adjunto}" title="Anular adjunto" ${tieneAccesoParaEliminarAdjuntos==true?'':'disabled'}><i class="fas fa-times fa-xs"></i></button>
                        </td>
                        </tr>`;

                    }
                });
            }else{
                htmlCabecera += `<tr>
                <td style="text-align:center;" colspan="2">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_archivos_requerimiento_compra_cabecera']").insertAdjacentHTML('beforeend', htmlCabecera);

            // llenar tabla detalle
            let htmlDetalle = '';
            if (res.adjuntos_detalle.length > 0) {
                (res.adjuntos_detalle).forEach(element => {
                    if (element.estado != 7) {
                        htmlDetalle += `<tr>
                                        <td style="text-align:left;"><a href="/files/necesidades/requerimientos/bienes_servicios/detalle/${element.archivo}" target="_blank">${element.archivo}</a></td>
                                        <td style="text-align:center;">
                                            <button type="button" class="btn btn-xs btn-danger btnAnularAdjuntoDetalle handleClickAnularAdjuntoDetalle" data-id-adjunto="${element.id_adjunto}" title="Anular adjunto" ${tieneAccesoParaEliminarAdjuntos==true?'':'disabled'}><i class="fas fa-times fa-xs"></i></button>
                                        </td>
                                        </tr>`;

                    }
                });
            }else{
                htmlDetalle += `<tr>
                <td style="text-align:center;" colspan="2">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_archivos_requerimiento_compra_detalle']").insertAdjacentHTML('beforeend', htmlDetalle);


        }).catch(function (err) {
            console.log(err)
        })


        obteneAdjuntosPago(idRequerimiento).then((res) => {
            console.log(res.data);
            let htmlPago = '';
            if (res.data.length > 0) {
                (res.data).forEach(element => {
                        htmlPago += `<tr>
                        <td style="text-align:left;"><a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${element.id_orden}" target="_blank">${element.codigo_orden}</a></td>
                        <td style="text-align:left;">`;
                        (element.adjuntos).forEach((archivo,index) => {
                            htmlPago+=`<p><a href="/files/tesoreria/pagos/${archivo}" target="_blank">${archivo}</a>  <span style="margin-left:4rem;">${element.fecha_adjuntos[index]??''}</span> </p>`;
                        });
                        `</td>
                        </tr>`;

                });
            }else{
                htmlPago += `<tr>
                <td style="text-align:center;" colspan="2">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_archivos_pagos']").insertAdjacentHTML('beforeend', htmlPago);


        }).catch(function (err) {
            console.log(err)
        })

        obtenerOtrosAdjuntosTesoreria(idRequerimiento).then((res) => {
            // console.log(res.data);
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_otros_adjuntos_tesoreria']").innerHTML='';
            let htmlOtrosAdjuntosTesoreria = '';
            if (res.data.length > 0) {
                (res.data).forEach(element => {
                        htmlOtrosAdjuntosTesoreria += `<tr>
                        <td style="text-align:left;"><a href="/files/tesoreria/otros_adjuntos/${element.archivo}" target="_blank">${element.archivo}</a></td>
                        <td style="text-align:left;">${element.fecha_registro}</td>
                        </tr>`;

                });
            }else{
                htmlOtrosAdjuntosTesoreria += `<tr>
                <td style="text-align:center;" colspan="2">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_otros_adjuntos_tesoreria']").insertAdjacentHTML('beforeend', htmlOtrosAdjuntosTesoreria);


        }).catch(function (err) {
            console.log(err)
        })

        obtenerAdjuntosLogisticos(idRequerimiento).then((res) => {
            // console.log(res.data);
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_adjuntos_logisticos']").innerHTML='';
            let htmlOtrosAdjuntosTesoreria = '';
            if (res.data.length > 0) {
                (res.data).forEach(element => {
                        htmlOtrosAdjuntosTesoreria += `<tr>
                        <td style="text-align:left;"><a href="/files/logistica/comporbantes_proveedor/${element.archivo}" target="_blank">${element.archivo}</a></td>
                        <td style="text-align:left;">${element.fecha_registro}</td>
                        </tr>`;

                });
            }else{
                htmlOtrosAdjuntosTesoreria += `<tr>
                <td style="text-align:center;" colspan="2">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_adjuntos_logisticos']").insertAdjacentHTML('beforeend', htmlOtrosAdjuntosTesoreria);


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


function agregarAdjuntoRequerimientoCabeceraCompra(obj){
    if (obj.files != undefined && obj.files.length > 0) {
        // console.log(obj.files);
        if((obj.files.length + tempArchivoAdjuntoRequerimientoCabeceraList.length)>5){
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
                        nameFile: file.name,
                        action: 'GUARDAR',
                        file: file
                    };
                    addToTablaArchivosRequerimientoCabecera(payload);

                    tempArchivoAdjuntoRequerimientoCabeceraList.push(payload);
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
    calcTamañoTotalAdjuntoLogisticoParaSubir();

    return false;

}

function calcTamañoTotalAdjuntoLogisticoParaSubir(){
    let tamañoTotalArchivoParaSubir=0;

    tempArchivoAdjuntoRequerimientoCabeceraList.forEach(element => {
        tamañoTotalArchivoParaSubir+=element.size;

    });
        document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] span[id='tamaño_total_archivos_para_subir']").textContent= $.number((tamañoTotalArchivoParaSubir/1000000),2)+'MB';
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

function addToTablaArchivosRequerimientoCabecera(payload) {
    getcategoriaAdjunto().then((categoriaAdjuntoList) => {
        console.log(categoriaAdjuntoList);
        let html = '';
        html = `<tr id="${payload.id}" style="text-align:center">
        <td style="text-align:left;">${payload.nameFile}</td>
        <td>
            <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto">
        `;
        categoriaAdjuntoList.forEach(element => {
            if (element.id_categoria_adjunto == payload.category) {
                html += `<option value="${element.id_categoria_adjunto}" selected>${element.descripcion}</option>`
            } else {
                html += `<option value="${element.id_categoria_adjunto}">${element.descripcion}</option>`

            }
        });
        html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimientoCompra" name="btnEliminarArchivoRequerimientoPago" title="Eliminar" data-id="${payload.id}" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("div[id='modal-ver-agregar-adjuntos-requerimiento-compra'] tbody[id='body_archivos_requerimiento_compra_cabecera']").insertAdjacentHTML('beforeend', html);

    }).catch(function (err) {
        console.log(err)
    })
}
function actualizarCategoriaDeAdjunto(obj){
    console.log(obj.value);
    if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
        let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.closest('tr').id);
        tempArchivoAdjuntoRequerimientoCabeceraList[indice].category = parseInt(obj.value) > 0 ? parseInt(obj.value) : 1;
        // tempArchivoAdjuntoRequerimientoCabeceraList[indice].action = 'ACTUALIZAR';
    } else {
        Swal.fire(
            '',
            'Hubo un error inesperado al intentar cambiar la categoría del adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
            'error'
        );
    }
}

function eliminarAdjuntoRequerimientoCompraCabecera(obj){
    obj.closest("tr").remove();
    var regExp = /[a-zA-Z]/g; //expresión regular
    if ((regExp.test(obj.dataset.id) == true)) {
        tempArchivoAdjuntoRequerimientoCabeceraList = tempArchivoAdjuntoRequerimientoCabeceraList.filter((element, i) => element.id != obj.dataset.id);
    } else {
        if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.dataset.id);
            tempArchivoAdjuntoRequerimientoCabeceraList[indice].action = 'ELIMINAR';
        } else {
            Swal.fire(
                '',
                'Hubo un error inesperado al intentar eliminar el adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                'error'
            );
        }

    }
}

function anularAdjuntoCabecera(obj){
    // console.log(idAdjunto);
    let idAdjunto=obj.dataset.idAdjunto;
    if(idAdjunto>0){
        $.ajax({
            type: 'POST',
            url: 'anular-adjunto-requerimiento-logístico-cabecera',
            data: {id_adjunto:idAdjunto},
            dataType: 'JSON',
            beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
                $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) =>{
                if (response.status =='success') {
                    $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);

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
                    $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);
                    console.log(response);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );
                }
            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);
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
function anularAdjuntoDetalle(obj){
    let idAdjunto=obj.dataset.idAdjunto;
    if(idAdjunto>0){
        $.ajax({
            type: 'POST',
            url: 'anular-adjunto-requerimiento-logístico-detalle',
            data: {id_adjunto:idAdjunto},
            dataType: 'JSON',
            beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
                $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) =>{
                if (response.status =='success') {
                    $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);

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
                    $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);
                    console.log(response);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );
                }
            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);
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
    if(tempArchivoAdjuntoRequerimientoCabeceraList.length>0){
        let formData = new FormData($('#form_ver_agregar_adjuntos_requerimiento_compra')[0]);
        formData.append(`archivoAdjuntoRequerimientoObject`, JSON.stringify(tempArchivoAdjuntoRequerimientoCabeceraList));

        if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
            tempArchivoAdjuntoRequerimientoCabeceraList.forEach(element => {
                if(element.action =='GUARDAR'){
                    formData.append(`archivoAdjuntoRequerimientoCabeceraFileGuardar${element.category}[]`, element.file);
                }
            });
        }

        $.ajax({
            type: 'POST',
            url: 'guardar-adjuntos-adicionales-requerimiento-compra',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
                $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) =>{
                if (response.status =='success') {
                    $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);

                    Lobibox.notify('success', {
                        title:false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });
                    $('#modal-ver-agregar-adjuntos-requerimiento-compra').modal('hide');

                } else {
                    $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);
                    console.log(response);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );
                }
            },
            fail:  (jqXHR, textStatus, errorThrown) =>{
                $('#modal-ver-agregar-adjuntos-requerimiento-compra .modal-content').LoadingOverlay("hide", true);
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

$('[name="requerimiento_sustentado"]').click(function (e) {
    // e.preventDefault();
    let id = $(this).attr('data-id');

    if ($(this).is(':checked') ) {
        value = 't';
    } else {
        value = 'f';
    }
    // $("#ListaRequerimientosElaborados").LoadingOverlay("hide", true);
    $.ajax({
        type: 'POST',
        url: 'requerimiento-sustentado',
        data: {
            requerimiento_sustentado:value,
            id:id
        },
        dataType: 'JSON',
        success: (response) =>{
            console.log(response);
            if (response.status =='success') {

                Lobibox.notify('success', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Se realizo con éxito el su proceso de requerimiento sustentado'
                });
                $("#ListaRequerimientosElaborados").DataTable().ajax.reload();
                // $('#modal-ver-agregar-adjuntos-requerimiento-compra').modal('hide');
            }
        },
        fail:  (jqXHR, textStatus, errorThrown) =>{
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });

});
