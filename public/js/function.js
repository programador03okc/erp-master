function openModal(type, active){
    // clearDataTable();
    // console.log(type);
    
    switch (type){
        /* rrhh */
        case 'persona':
            modalPersona();
        break;
        case 'postulante':
            modalPostulante();
        break;
        case 'trabajador':
            modalTrabajador();
        break;
        case 'horario':
            modalHorarios();
        break;
        /////////////////////
        case 'categoria':
            categoriaModal();
        break;
        case 'subcategoria':
            subCategoriaModal();
        break;
        case 'producto':
            productoModal();
        break;
        case 'insumo':
            insumoModal();
        break;
        case 'ubicacion':
            if (active == 'form-estante'){
                almacenModal();
            } else if (active == 'form-nivel'){
                estanteModal();
            } else if (active == 'form-posicion'){
                nivelModal();
            }
        break;
        case 'guia_compra':
            guia_compraModal();
        break;
        case 'guia_venta':
            guia_ventaModal();
        break;
        case 'doc_compra':
            doc_compraModal();
        break;
        case 'doc_venta':
            doc_ventaModal();
        break;
        case 'transformacion':
            transformacionModal();
        break;
        /////Proyectos
        case 'presint':
            presintModal('');
        break;
        case 'propuesta':
            propuestaModal('');
        break;
        case 'preseje':
            presejeModal('');
        break;
        case 'cronoint':
            presintModal('modal');
        break;
        case 'cronovalint':
            presintModal('cronomodal');
        break;
        case 'cronoeje':
            presejeModal('modal');
        break;
        case 'cronovaleje':
            presejeModal('cronomodal');
        break;
        case 'cronopro':
            propuestaModal('modal');
        break;
        case 'cronovalpro':
            propuestaModal('modalval');
        break;
        case 'valorizacion':
            valorizacionModal();
        break;
        case 'presEstructura':
            presEstructuraModal();
        break;

        case 'equi_tipo':
            
        break;
        case 'equi_cat':
            equi_catModal();
        break;
        case 'equipo':
            equipoModal();
        break;
        case 'tp_combustible':
        break;
        case 'mtto':
            mttoModal();
        break;
        case 'equi_sol':
            equi_solModal();
        break;
        
        /* logistica */
        case 'requerimiento':
            modalRequerimiento();
        break;
        case 'cuadro_comparativo':
            modalCuadroComparativo();
        break;
        case 'cotizacion':
            cotizacionModal();

        break;
        case 'orden':
            ordenModal();
        break;
        
        /* administracion */
        case 'empresa':
            modalEmpresa();
        break;
        case 'proveedores':
            ModalListaProveedores();
        break;
    }
}

function eventRegister(type, data, action, frm_active){
    switch (type){
        /* rrhh */
        case 'persona':
            save_persona(data, action);
        break;
        case 'periodo':
            save_periodo(data, action);
        break;
        case 'horario':
            save_horario(data, action);
        break;
        case 'tolerancia':
            save_tolerancia(data, action);
        break;
        case 'est_civil':
            save_estado_civil(data, action);
        break;
        case 'con_derecho_hab':
            save_cond_derecho_hab(data, action);
        break;
        case 'niv_estudios':
            save_nivel_estudio(data, action);
        break;
        case 'carrera':
            save_carrera(data, action);
        break;
        case 'tipo_trabajador':
            save_tipo_trabajador(data, action);
        break;
        case 'tipo_contrato':
            save_tipo_contrato(data, action);
        break;
        case 'modalidad':
            save_modalidad(data, action);
        break;
        case 'concepto_rol':
            save_concepto_rol(data, action);
        break;
        case 'cat_ocupacional':
            save_categoria_ocupacional(data, action);
        break;
        case 'tipo_planilla':
            save_tipo_planilla(data, action);
        break;
        case 'tipo_merito':
            save_tipo_merito(data, action);
        break;
        case 'tipo_demerito':
            save_tipo_demerito(data, action);
        break;
        case 'tipo_bonificacion':
            save_tipo_bonificacion(data, action);
        break;
        case 'tipo_descuento':
            save_tipo_descuento(data, action);
        break;
        case 'tipo_retencion':
            save_tipo_retencion(data, action);
        break;
        case 'tipo_aportes':
            save_tipo_aporte(data, action);
        break;
        case 'cargo':
            save_cargo(data, action);
        break;
        case 'postulante':
            save_postulante(data, action, frm_active);
        break;
        case 'trabajador':
            save_trabajador(data, action, frm_active);
        break;
        case 'pension':
            save_pension(data, action);
        break;
        case 'merito':
            save_merito(data, action);
        break;
        case 'sancion':
            save_sancion(data, action);
        break;
        case 'derecho_hab':
            save_derecho_hab(data, action);
        break;
        case 'salida':
            save_salidas(data, action);
        break;
        case 'prestamo':
            save_prestamo(data, action);
        break;
        case 'vacaciones':
            save_vacaciones(data, action);
        break;
        case 'horas_ext':
            save_horas_ext(data, action);
        break;
        case 'bonificacion':
            save_bonificacion(data, action);
        break;
        case 'descuento':
            save_descuento(data, action);
        break;
        case 'retencion':
            save_retencion(data, action);
        break;
        case 'aportacion':
            save_aportacion(data, action);
        break;
        case 'reintegro':
            save_reintegro(data, action);
        break;
        case 'cese':
            save_cese(data, action);
        break;
        ////////////////////////// CONFIGURACION //////////////////////////
        case 'modulo':
            save_modulo(data, action);
        break;
        case 'aplicaciones':
            save_aplicaciones(data, action);
        break;
        case 'correo_coorporativo':
            save_correo_coorporativo(data, action);
        break;
        case 'configuracion_socket':
            save_configuracion_socket(data, action);
        break;
        case 'documento':
            save_documento(data, action);
        break;
        ////////////////////////// ALMACEN //////////////////////////
        case 'tipo':
            save_tipo(data, action);
        break;
        case 'categoria':
            save_categoria(data, action);
        break;
        case 'subcategoria':
            save_subcategoria(data, action);
        break;
        case 'clasificacion':
            save_clasificacion(data, action);
        break;
        case 'producto':
            if (frm_active == "form-general"){
                save_producto(data, action);
            } else if (frm_active == "form-ubicacion"){
                save_ubicacion(data, action);
            } else if (frm_active == "form-serie"){
                save_serie(data, action);
            }
        break;
        case 'sis_contrato':
            save_sis_contrato(data, action);
        break;
        case 'tipo_insumo':
            save_tipo_insumo(data, action);
        break;
        case 'cat_insumo':
            save_cat_insumo(data, action);
        break;
        case 'iu':
            save_iu(data, action);
        break;
        case 'insumo':
            save_insumo(data, action);
        break;
        case 'cat_acu':
            save_cat_acu(data, action);
        break;
        case 'acu':
            save_acu(data, action);
        break;
        case 'tipoServ':
            save_tipoServ(data, action);
        break;
        case 'servicio':
            save_servicio(data, action);
        break;
        case 'tipoMov':
            save_tipoMov(data, action);
        break;
        case 'unidmed':
            save_unidmed(data, action);
        break;
        case 'tipo_almacen':
            save_tipo_almacen(data, action);
        break;
        case 'almacenes':
            save_almacen(data, action);
        break;
        case 'ubicacion':
            if (frm_active == "form-estante"){
                save_estante(data, action);
            } else if (frm_active == "form-nivel"){
                save_nivel(data, action);
            } else if (frm_active == "form-posicion"){
                save_posicion(data, action);
            }
        break;
        case 'guia_compra':
            if (frm_active == 'form-general'){
                save_guia_compra(data, action);
            } else if (frm_active == 'form-transportista'){
                save_transportista(data, action);
            }
        break;
        case 'guia_venta':
            if (frm_active == 'form-general'){
                save_guia_venta(data, action);
            } else if (frm_active == 'form-transportista'){
                save_transportista(data, action);
            }
        break;
        case 'doc_compra':
            save_doc_compra(data, action);
        break;
        case 'doc_venta':
            save_doc_venta(data, action);
        break;
        case 'transformacion':
            save_transformacion(data, action);
        break;
        case 'tipo_doc':
            save_tipo_doc(data, action);
        break;
        case 'serie_numero':
            save_serie_numero(data, action);
        break;
        case 'presint':
            save_presint(data, action);
        break;
        case 'propuesta':
            save_propuesta(data, action);
        break;
        case 'preseje':
            save_preseje(data, action);
        break;
        case 'cronoint':
            save_cronoint();
        break;
        case 'cronovalint':
            save_cronovalint();
        break;
        case 'cronoeje':
            save_cronoeje();
        break;
        case 'cronoval':
            save_cronoval();
        break;
        case 'cronopro':
            save_cronopro();
        break;
        case 'cronovaleje':
            save_cronovaleje();
        break;
        case 'cronovalpro':
            save_cronovalpro();
        break;
        case 'presEstructura':
            save_pres_estructura(data, action);
        break;
        case 'valorizacion':
            save_valorizacion();
        break;
        /////////////////Equipos
        case 'equi_tipo':
            save_equi_tipo(data, action);
        break;
        case 'equi_cat':
            save_equi_cat(data, action);
        break;
        case 'equipo':
            save_equipo(data, action);
        break;
        case 'tp_combustible':
            save_tp_combustible(data, action);
        break;
        case 'mtto':
            save_mtto(data, action);
        break;
        case 'equi_sol':
            save_equi_sol(data, action);
        break;
        //Tesoreria
        case 'tesoreria_solicitudes':
            guardarSolicitud(data, action);
        break;
        //Logistica
        case 'requerimiento':
            save_requerimiento(action);
        break;
        case 'orden':
            save_orden_compra(data, action);
        break;
        case 'proveedores':
            save_form(data, action, frm_active);
        break;
        /* administracion */
        case 'empresa':
            save_empresa(data, action, frm_active);
        break;
        case 'sede':
            save_sede(data, action);
        break;
        case 'grupo':
            save_grupo(data, action);
        break;
        case 'area':
            save_area(data, action);
        break;

        default:
        break;
    }
}

function anularRegister(type, ids, active){
    switch (type){
        /* rrhh */
        case 'persona':
            anular_persona(ids);
        break;
        case 'horario':
            anular_horario(ids);
        break;
        case 'tolerancia':
            anular_tolerancia(ids);
        break;
        case 'est_civil':
            anular_estado_civil(ids);
        break;
        case 'con_derecho_hab':
            anular_cond_derecho_hab(ids);
        break;
        case 'niv_estudios':
            anular_nivel_estudio(ids);
        break;
        case 'carrera':
            anular_carrera(ids);
        break;
        case 'tipo_trabajador':
            anular_tipo_trabajador(ids);
        break;
        case 'tipo_contrato':
            anular_tipo_contrato(ids);
        break;
        case 'modalidad':
            anular_modalidad(ids);
        break;
        case 'concepto_rol':
            anular_concepto_rol(ids);
        break;
        case 'cat_ocupacional':
            anular_categoria_ocupacional(ids);
        break;
        case 'tipo_planilla':
            anular_tipo_planilla(ids);
        break;
        case 'tipo_merito':
            anular_tipo_merito(ids);
        break;
        case 'tipo_demerito':
            anular_tipo_demerito(ids);
        break;
        case 'tipo_bonificacion':
            anular_tipo_bonificacion(ids);
        break;
        case 'tipo_descuento':
            anular_tipo_descuento(ids);
        break;
        case 'tipo_retencion':
            anular_tipo_retencion(ids);
        break;
        case 'tipo_aportes':
            anular_tipo_aporte(ids);
        break;
        case 'cargo':
            anular_cargo(ids);
        break;
        case 'postulante':
            anular_postulante(active);
        break;
        case 'trabajador':
            anular_trabajador(active);
        break;
        case 'pension':
            anular_pension(ids);
        break;
        case 'merito':
            anular_merito(ids);
        break;
        case 'sancion':
            anular_sancion(ids);
        break;
        case 'derecho_hab':
            anular_derecho_hab(ids);
        break;
        case 'salida':
            anular_salidas(ids);
        break;
        case 'prestamo':
            anular_prestamo(ids);
        break;
        case 'vacaciones':
            anular_vacaciones(ids);
        break;
        case 'horas_ext':
            anular_horas_ext(ids);
        break;
        case 'bonificacion':
            anular_bonificacion(ids);
        break;
        case 'descuento':
            anular_descuento(ids);
        break;
        case 'retencion':
            anular_retencion(ids);
        break;
        case 'aportacion':
            anular_aportacion(ids);
        break;
        case 'reintegro':
            anular_reintegro(ids);
        break;
        ////////////////////////// CONFIGURACION //////////////////////////
        case 'modulo':
            anular_modulo(ids);
        break;
        case 'aplicaciones':
            anular_aplicaciones(ids);
        break;
        case 'correo_coorporativo':
            anular_correo_coorporativo(ids);
        break;
        case 'configuracion_socket':
            anular_configuracion_socket(ids);
        break;
        case 'documento':
            anular_documento(ids);
        break;
        ////////////////////////// ALMACEN //////////////////////////
        case 'tipo':
            anular_tipo(ids);
        break;
        case 'categoria':
            anular_categoria(ids);
        break;
        case 'subcategoria':
            anular_subcategoria(ids);
        break;
        case 'clasificacion':
            anular_clasificacion(ids);
        break;
        case 'producto':
            if (active == "form-general"){
                anular_producto(ids);
            } else if (active == "form-ubicacion"){
                anular_ubicacion(ids);
            } else if (active == "form-serie"){
                anular_serie(ids);
            }
        break;
        case 'sis_contrato':
            anular_sis_contrato(ids);
        break;
        case 'tipo_insumo':
            anular_tipo_insumo(ids);
        break;
        case 'cat_insumo':
            anular_cat_insumo(ids);
        break;
        case 'iu':
            anular_iu(ids);
        break;
        case 'insumo':
            anular_insumo(ids);
        break;
        case 'cat_acu':
            anular_cat_acu(ids);
        break;
        case 'acu':
            anular_acu(ids);
        break;
        case 'tipoServ':
            anular_tipoServ(ids);
        break;
        case 'servicio':
            anular_servicio(ids);
        break;
        case 'tipoMov':
            anular_tipoMov(ids);
        break;
        case 'unidmed':
            anular_unidmed(ids);
        break;
        case 'tipo_almacen':
            anular_tipo_almacen(ids);
        break;
        case 'almacenes':
            anular_almacen(ids);
        break;
        case 'ubicacion':
            if (active == "form-estante"){
                anular_estante(ids);
            } else if (active == "form-nivel"){
                anular_nivel(ids);
            } else if (active == "form-posicion"){
                anular_posicion(ids);
            }
        break;
        case 'guia_compra':
            if (active == "form-general"){
                anular_guia_compra(ids);
            } else if (active == "form-transportista"){
                anular_transportista(ids);
            }
        break;
        case 'guia_venta':
            if (active == "form-general"){
                anular_guia_venta(ids);
            } else if (active == "form-transportista"){
                anular_transportista(ids);
            }
        break;
        case 'doc_compra':
            anular_doc_compra(ids);
        break;
        case 'doc_venta':
            anular_doc_venta(ids);
        break;
        case 'transformacion':
            anular_transformacion(ids);
        break;
        case 'tipo_doc':
            anular_tipo_doc(ids);
        break;
        case 'serie_numero':
            anular_serie_numero(ids);
        break;
        case 'equi_tipo':
            anular_equi_tipo(ids);
        break;
        case 'equi_cat':
            anular_equi_cat(ids);
        break;
        case 'equipo':
            anular_equipo(ids);
        break;
        case 'tp_combustible':
            anular_tp_combustible(ids);
        break;
        case 'mtto':
            anular_mtto(ids);
        break;
        case 'equi_sol':
            anular_equi_sol(ids);
        break;
        //Logistica
        case 'requerimiento':
            anular_requerimiento(ids);
        break;
        case 'orden':
            anular_orden_compra(ids);
        break;
        //Tesoreria
        case 'tesoreria_solicitudes':
            anularSolicitud(ids);
            break;
        // administracion
        case 'empresa':
            anular_empresa(frm_active);
        break;
        case 'sede':
            anular_sede(ids);
        break;
        case 'grupo':
            anular_grupo(ids);
        break;
        case 'area':
            anular_area(ids);
        break;
        /** Proyectos */
        case 'presint':
            anular_presint(ids);
        break;
        case 'preseje':
            anular_preseje(ids);
        break;
        case 'propuesta':
            anular_propuesta(ids);
        break;
        case 'valorizacion':
            anular_valorizacion(ids);
        break;
        default:
        break;
    }
}

function changePassword(){
    $('#modal-settings').modal({
        show: true,
        backdrop: 'static'
    });
    $('#modal-settings').on('shown.bs.modal', function(){
        $('[name=pass_old]').focus();
    });
}

function execSetting(){
    var question = confirm('¿Desea actualizar su contraseña?');
    if(question == true){
        var pass = $('[name=pass_new]').val();
        var repass = $('[name=pass_renew]').val();
        if(pass == repass){
            var data = $('#formSettingsPassword').serialize();
            var baseUrl = '/update_password';
            console.log(data);
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl,
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    $('.loading').remove();
                    if(response > 0){
                        alert('Contraseña correctamente actualizada.');
                        $('#formSettingsPassword')[0].reset();
                    }else if(response == 0){
                        alert('La contraseña actual no es correcta.');
                        $('[name=pass_old]').focus();
                    }else{
                        alert('Problemas al actualizar la contraseña, intentelo mas tarde.');
                    }
                }
            });
            return false;
        }else{
            alert('Las contraseñas no son iguales, confirme porfavor.');
            $('[name=pass_renew]').focus();
            return false;
        }
    }else{
        return false;
    }
}

/* Limpiar formularios */
function clearForm(form){
    // $("#"+form)[0].reset();
    $("#"+form+" input.activation").val('');
    $("#"+form+" textarea.activation").val('');
    $("#"+form+" select.activation").val(0);
    $("#"+form+" #fecha_registro label").text('');
    $("#"+form+" .select2-selection__rendered").text('');
}

/* Limpiar valores del dataTable */
function clearDataTable(){
    $('.dataTable tbody tr').removeClass('eventClick');
    $('.modal-footer label').text('');
    $('.dataTable').dataTable().fnDestroy();
}

/* Fecha Actual */
function fecha_actual(){
    var fecha = new Date();
    var dd = fecha.getDate();
    var mm = fecha.getMonth() + 1;
    var yy = fecha.getFullYear();
    if (dd < 10) {dd = '0' + dd;}
    if (mm < 10) {mm = '0' + mm;}
    return (yy + "-" + mm + "-" + dd);
}

/* Sumar dias a una fecha dada */
function sumaFecha(d, fecha){

    var Fecha = new Date();
    var sFecha = fecha || (Fecha.getFullYear() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getDate());
    var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
    var aFecha = sFecha.split(sep);
    var fecha = aFecha[0]+'/'+aFecha[1]+'/'+aFecha[2];
    
    fecha= new Date(fecha);
    fecha.setDate(fecha.getDate()+parseInt(d));

    var anno=fecha.getFullYear();
    var mes= fecha.getMonth()+1;
    var dia= fecha.getDate();
    mes = (mes < 10) ? ("0" + mes) : mes;
    dia = (dia < 10) ? ("0" + dia) : dia;
    var fechaFinal = anno+sep+mes+sep+dia;

    console.log(fechaFinal);
    return fechaFinal;
}

/* Sumar dias a una fecha dada */
function suma_fecha(d, fecha){
    var fechaFin = null;
    if (d > 0){
        fechaFin = moment(fecha).add(d, 'days');
    } else {
        fechaFin = moment(fecha).subtract(d, 'days');
    }
    var fechaFinal = moment(fechaFin).format("YYYY-MM-DD");
    return fechaFinal;
}

/* Formato para las fechas dd/mm/YY */
function formatDate(myfecha){
    var nuevo = moment(myfecha).format("DD/MM/YYYY");
    /*var fecha = new Date(myfecha);
    var dd = fecha.getDate();
    var mm = fecha.getMonth() + 1;
    var yy = fecha.getFullYear();

    if (dd < 10){dd = '0' + dd;}
    if (mm < 10){mm = '0' + mm;}
    var nuevo = dd + '/' + mm + '/' + yy;*/
    return nuevo;
}

/* Formato para las fechas dd-mm-YYYY */
function format2Date(myfecha){
    var nuevo = moment(myfecha).format("DD-MM-YYYY");
    return nuevo;
}

/**Restar dos fechas. Devuelve cantidad de dias */
function restarFechas(fechaini, fechafin){
    var dias = 0;
    if (fechaini !== '' && fechafin !== ''){
        var ini = moment(fechaini);
        var fin = moment(fechafin);
        dias = fin.diff(ini, 'days');
    }
	return dias;
}

/* Formato para la fecha y hora dd/mm/YY H:i:s */
function formatDateHour(myfecha){
    var fecha = new Date(myfecha);
    var dd = fecha.getDate();
    var mm = fecha.getMonth() + 1;
    var yy = fecha.getFullYear();
    var hour = fecha.getHours();
    var min = fecha.getMinutes();
    var seg = fecha.getSeconds();

    if (dd < 10) {dd = '0' + dd;}
    if (mm < 10) {mm = '0' + mm;}
    if (hour < 10) {hour = '0' + hour;}
    if (min < 10) {min = '0' + min;}
    if (seg < 10) {seg = '0' + seg;}

    var nuevo = dd + '/' + mm + '/' + yy + ' ' + hour + ':' + min + ':' + seg;
    return nuevo;
}
/* nueva funcion */
function formatHour(horas){
    var hora = new Date('01/01/2019 ' + horas);
    var hour = hora.getHours();
    var min = hora.getMinutes();

    if (hour < 10) {hour = '0' + hour;}
    if (min < 10) {min = '0' + min;}

    var nuevo = hour + ':' + min;
    return nuevo;
}

 /* Formato 2 decimales */
function formatDecimal(number){
    var newNumber = Number(number).toFixed(2);
    return newNumber;
}

function formatDecimalDigitos(number,digitos){
    var newNumber = Number(number).toFixed(digitos);
    return newNumber;
}

 /* Formato miles y decimales 000,000.00 */
var formatNumber = {
    separador: ",", // separador para los miles
    sepDecimal: '.', // separador para los decimales
    formatear:function (num, digitos){
        num +='';
        var splitStr = num.split('.');
        var splitLeft = splitStr[0];
        var dig = Math.abs(digitos);
        var ceros = '';
        if (splitStr[1] !== undefined){
            //completa los ceros en decimales
            var decimales = dig - splitStr[1].length;
            if (decimales > 0){
                while (ceros.length < decimales){
                    ceros += '0';
                }
            }
        }
        var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
        var regx = /(\d+)(\d{3})/;
        while (regx.test(splitLeft)) {
            splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
        }
        return this.simbol + splitLeft + splitRight + ceros;
    },
    new:function(num, simbol, digitos){ //agrega string (puede ser la moneda) delante del nro
        this.simbol = simbol ||'';
        return this.formatear(num, digitos);
    },
    decimal:function(num, simbol, digitos){
        var nro = Math.round10(num, digitos);
        return this.new(nro, simbol, digitos);
    }
}

//Math.round10(55.55, -1);   // 55.6
Math.round10 = function(value, exp) {
    return decimalAdjust('round', value, exp);
};

function decimalAdjust(type, value, exp) {
    // Si el exp no está definido o es cero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un número o el exp no es un entero...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
}

/* Formato para calcular la edad */
function calcularEdad(fecha){
    var hoy = new Date();
    var cumpleanos = new Date(fecha);
    var edad = hoy.getFullYear() - cumpleanos.getFullYear();
    var m = hoy.getMonth() - cumpleanos.getMonth();

    if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
        edad--;
    }

    return edad + ' años';
}

function leftZero(canti, number){
    let vLen = number.toString();
    let nLen = vLen.length;
    let zeros = '';
    for(var i=0; i<(canti-nLen); i++){
        zeros = zeros+'0';
    }
    return zeros+number;
}

function textReverse(str){
    let revstr = "";
    for(let i = str.length-1; i>=0; i--){
        revstr = revstr+ str[i];
    }
    return revstr;
}

function encode5t(str){
    // console.log('str'+str);
    for(var i=0; i<5; i++){
        str=this.textReverse(btoa(str));
      }
    return str;
}

function sololetras(e) {
    var key = e.keyCode || e.which;
    var teclado = String.fromCharCode(key).toUpperCase();
    var letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var especiales = "8-37-38-46-164";
    var teclado_especial = false;
    for (var i in especiales){
        if (key == especiales[i]){
            teclado_especial = true;
            break;
        }
    }
    if (letras.indexOf(teclado)==-1 && !teclado_especial){
        return false;
    }
}

//Se utiliza para que el campo de texto solo acepte numeros
function SoloNumeros(evt){
    if (window.event){//asignamos el valor de la tecla a keynum
        keynum = evt.keyCode; //IE
    }
    else {
        keynum = evt.which; //FF
    } 
    //comprobamos si se encuentra en el rango numérico y que teclas no recibirá.
    if ((keynum > 46 && keynum < 58) || keynum == 8 || keynum == 13 || keynum == 6 ){
        return true;
    }
    else {
        return false;
    }
}

/* Variables de configuración del dataTable */
function funcDatatables() {
    var idioma = {
        "sProcessing":     "<div class='spinner'></div>",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate":
        {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria":
        {
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    }
    var dtdom = 'lBfrtip'; //l=lenght / B=button / f=filter / rt=read table
    var dtbuttons = [
        {extend: 'copy', text: '<i class="fas fa-copy"></i>'},
        {extend: 'excel', text: '<i class="fas fa-file-excel"></i>'},
        {extend: 'pdf', text: '<i class="fas fa-file-pdf"></i>'},
        {extend: 'print', text: '<i class="fas fa-print"></i>'}
        //falta agregar titulos a los export
    ];

    var array = [idioma, dtdom, dtbuttons];
    return array;
}

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}
