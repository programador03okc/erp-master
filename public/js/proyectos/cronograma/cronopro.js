var partidas = [];
var lista = [];
var tp_pred = [];

$(function(){
    vista_extendida();

    $("#tab-cronoeje section:first form").attr('form', 'formulario');
    
    /* Efecto para los tabs */
    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').attr('hidden', true);
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);

        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);

        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });

});

function nuevo_cronopro(){
    console.log('nuevo_cronopro');
    $('#form-cronopro')[0].reset();
    $('#codigo').text('');
    $('#listaPartidas tbody').html('');
    propuestaModal('nuevo');
}

function listar_crono_propuesta(id_presupuesto){
    console.log('listar_crono_propuesta id_presupuesto: '+id_presupuesto);
    $.ajax({
        type: 'GET',
        url: 'listar_crono_propuesta/'+id_presupuesto,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            lista = response['lista'];
            tp_pred = response['tp_pred'];
            mostrar_lista();
            $('[name=unid_program]').val(response['unid_program']);

            var unid = '';
            switch (response['unid_program']) {
                case 1:
                    unid = 'day';
                    break;
                case 2:
                    unid = 'week';
                    break;
                case 4:
                    unid = 'month';
                    break;
                default:
                    break;
            }
            $('[name=unid_program_gantt]').val(unid);
            $('[name=fecha_inicio_crono]').val(response['fecha_inicio_crono']);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_cronograma_propuesta(id_presupuesto){
    console.log('listar_cronograma_propuesta id_presup: '+id_presupuesto);
    $.ajax({
        type: 'GET',
        url: 'listar_cronograma_propuesta/'+id_presupuesto,
        dataType: 'JSON',
        success: function(response){
            lista = response['lista'];
            tp_pred = response['tp_pred'];
            mostrar_lista();
            
            $('[name=unid_program]').val(response['unid_program']);

            var unid = '';
            switch (response['unid_program']) {
                case 1:
                    unid = 'day';
                    break;
                case 2:
                    unid = 'week';
                    break;
                case 4:
                    unid = 'month';
                    break;
                default:
                    break;
            }
            $('[name=unid_program_gantt]').val(unid);
            $('[name=fecha_inicio_crono]').val(response['fecha_inicio_crono']);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_lista(){
    var html = '';
    var disabled = '';
    var type = $("#form-cronopro").attr('type');
    var modo = $("[name=modo]").val();

    if (modo !== "new"){
        disabled = (type == "edition" ? "" : 'disabled="true"');
    }

    lista.forEach(element => {
        if (element.partidas !== undefined){
            html += '<tr id="com-'+element.id_titulo+'">'+
            '<td></td>'+
            '<td><strong>'+element.codigo+'</strong></td>'+
            '<td><strong>'+element.descripcion+'</strong></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '</tr>';
            element.partidas.forEach(partida => {
                partidas.push(partida);
                var sel = '';
                var id = (partida.cod_padre !== null ? partida.id_partida : "'"+partida.codigo+"'");

                var options = '<select class="input-data" name="tp_predecesora" style="width: 100px;" onChange="change_predecesora('+partida.nro_orden+');">';
                tp_pred.forEach(tp => {
                    sel = (tp.id_tp_predecesora == partida.tp_predecesora ? 'selected' : "");
                    options +='<option value="'+tp.id_tp_predecesora+'" '+sel+'>'+tp.descripcion+'</option>';
                });
                options +='</select>';

                html+='<tr id="'+partida.id_partida+'">'+
                '<td>'+partida.nro_orden+'</td>'+
                '<td>'+partida.codigo+'</td>'+
                '<td>'+partida.descripcion+'</td>'+
                '<td>'+partida.abreviatura+'</td>'+
                '<td class="right">'+partida.metrado+'</td>'+
                // '<td class="right">'+partida.rendimiento+'</td>'+
                '<td><input type="number" class="input-data right activation" '+disabled+' style="width: 70px;" name="dias" value="'+partida.dias+'" onBlur="change_dias('+partida.nro_orden+');"/></td>'+
                '<td><input type="date" class="input-data right activation" '+disabled+' name="fecha_inicio" value="'+partida.fecha_inicio+'" onBlur="change_fini('+partida.nro_orden+');"/></td>'+
                '<td><input type="date" class="input-data right activation" '+disabled+' name="fecha_fin" value="'+partida.fecha_fin+'" onBlur="change_ffin('+partida.nro_orden+');"/></td>'+
                '<td>'+options+'</td>'+
                '<td><input type="number" class="input-data right activation" '+disabled+' style="width: 50px;" name="dias_pos" onBlur="change_dpos('+partida.nro_orden+');" value="'+partida.dias_pos+'"/></td>'+
                '<td><input type="text" class="input-data activation" '+disabled+' style="width: 70px;" name="predecesora" value="'+partida.predecesora+'" onBlur="change_predecesora('+partida.nro_orden+');" onKeyPress="handleKeyPress(event);"/></td>'+
                '</tr>';
            });
        }
        else {
            var sel = '';
            var options = '<select class="input-data" name="tp_predecesora" style="width: 100px;" onChange="change_predecesora('+element.nro_orden+');">';
            tp_pred.forEach(tp => {
                sel = (tp.id_tp_predecesora == element.tp_predecesora ? 'selected' : "");
                options +='<option value="'+tp.id_tp_predecesora+'" '+sel+'>'+tp.descripcion+'</option>';
            });
            options +='</select>';

            html+='<tr id="'+element.id_partida+'">'+
            '<td>'+element.nro_orden+'</td>'+
            '<td>'+element.codigo+'</td>'+
            '<td>'+element.descripcion+'</td>'+
            '<td></td>'+
            '<td class="right">'+element.metrado+'</td>'+
            '<td><input type="number" class="input-data right activation" '+disabled+' style="width: 70px;" name="dias" value="'+element.dias+'" onBlur="change_dias('+element.nro_orden+');"/></td>'+
            '<td><input type="date" class="input-data right activation" '+disabled+' name="fecha_inicio" value="'+element.fecha_inicio+'" onBlur="change_fini('+element.nro_orden+');"/></td>'+
            '<td><input type="date" class="input-data right activation" '+disabled+' name="fecha_fin" value="'+element.fecha_fin+'" onBlur="change_ffin('+element.nro_orden+');"/></td>'+
            '<td>'+options+'</td>'+
            '<td><input type="number" class="input-data right activation" '+disabled+' style="width: 50px;" name="dias_pos" onBlur="change_dpos('+element.nro_orden+');" value="'+element.dias_pos+'"/></td>'+
            '<td><input type="text" class="input-data activation" '+disabled+' style="width: 70px;" name="predecesora" value="'+element.predecesora+'" onBlur="change_predecesora('+element.nro_orden+');" onKeyPress="handleKeyPress(event);"/></td>'+
            '</tr>';
        }
    });

    $('#listaPartidas tbody').html(html);

}

function save_cronopro(){
    var id_partida = [];
    var nro_orden = [];
    var dias = [];
    var fini = [];
    var ffin = [];
    var tp_pred = [];
    var dias_pos = [];
    var predecesora = [];
    var id_pres = $('[name=id_presupuesto]').val();
    var modo = $('[name=modo]').val();
    var unid = $('[name=unid_program]').val();
    var msj = '';
    var i = 0;
    listar_partidas();

    if (unid == '' || unid == 0 || unid == null){
        msj +='Debe ingresar una unidad de programacion';
    }

    partidas.forEach(function(element) {
        id_partida[i] = element.id_partida;
        nro_orden[i] = parseInt(element.nro_orden);
        dias[i] = element.dias;
        fini[i] = element.fecha_inicio;
        ffin[i] = element.fecha_fin;
        tp_pred[i] = element.tp_predecesora;
        dias_pos[i] = element.dias_pos;
        predecesora[i] = element.predecesora;
        ++i;
        if (element.dias <= 0 || element.dias % 1 !== 0){
            msj +='\nDebe ingresar un numero de días válido';
        }
    });
    var data = 'id_partida='+id_partida+
               '&id_presupuesto='+id_pres+
               '&nro_orden='+nro_orden+
               '&dias='+dias+
               '&fini='+fini+
               '&ffin='+ffin+
               '&tp_pred='+tp_pred+
               '&dias_pos='+dias_pos+
               '&predecesora='+predecesora+
               '&modo='+modo+
               '&unid_program='+unid;
            //    '&fecha_inicio_crono='+inicio;
    console.log(data);
    if (msj.length > 0){
        alert(msj);
    } 
    else {
        $.ajax({
            type: 'POST',
            url: 'guardar_crono_propuesta',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    if (modo == 'nuevo'){
                        alert('Cronograma registrado con éxito');
                    } else {
                        alert('Cronograma actualizado con éxito');
                    }
                    changeStateButton('guardar');
					$('#form-cronopro').attr('type', 'register');
					changeStateInput('form-cronopro', true);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function obtener_partida(nro_orden){
    var part;
    lista.forEach(function(element) {
        if (element.partidas !== undefined){
            if (part == undefined){
                part = element.partidas.find(partida => partida.nro_orden == nro_orden);
            }
        } else {
            if (part == undefined){
                if (element.nro_orden == nro_orden){
                    part = element;
                }
            }
        }
    });
    console.log(part);
    return part;
}

//Actualizar los datos de partidas segun lista
function listar_partidas(){
    partidas = [];
    lista.forEach(function(element) {
        if (element.partidas !== undefined){
            element.partidas.forEach(function(partida) {
                partidas.push(partida);
            });
        } else {
            partidas.push(element);
        }
    });
}

//Actualizar los datos de lista segun partidas
function actualizar_lista(){
    lista.forEach(function(element) {
        if (element.partidas !== undefined){
            element.partidas.forEach(function(partida) {
                //copia los datos de partidas
                var nro_orden = partida.nro_orden;
                partida = partidas.find(p => p.nro_orden == nro_orden);
            });
        } else {
            if (element.nro_orden !== undefined){
                var nro_orden = element.nro_orden;
                element = partidas.find(p => p.nro_orden == nro_orden);
            }
        }
    });
}

function actualizar_lista_part(part){
    lista.forEach(function(element) {
        if (element.partidas !== undefined){
            element.partidas.forEach(function(partida) {
                //copia los datos de part
                if (partida.nro_orden == part.nro_orden){
                    partida = part;
                }
            });
        } else {
            if (element.nro_orden == part.nro_orden){
                element = part;
            }
        }
    });
}

function change_dpos(nro_orden){
    var part = obtener_partida(nro_orden);
    var dias_pos = $("#"+part.id_partida+" td").find("input[name=dias_pos]").val();
    var fini = suma_fecha(parseFloat(dias_pos), part.fecha_inicio);
    part.fecha_inicio = fini;
    part.dias_pos = dias_pos;
    var ffin = suma_fecha(parseFloat(part.dias), fini);
    part.fecha_fin = ffin;
    actualizar_lista_part(part);
    actualizarTareas(part.nro_orden);
}

function change_dias(nro_orden){
    var part = obtener_partida(nro_orden);
    var id = part.id_partida;
    var dias = $("#"+id+" td").find("input[name=dias]").val();
    var ffin = suma_fecha(parseFloat(dias), part.fecha_inicio);

    part.fecha_fin = ffin;
    part.dias = dias;
    actualizar_lista_part(part);
    actualizarTareas(part.nro_orden);
}

function change_fini(nro_orden){
    var part = obtener_partida(nro_orden);
    var id = part.id_partida;
    var fini = $("#"+id+" td").find("input[name=fecha_inicio]").val();
    var ffin = suma_fecha(parseFloat(part.dias), fini);

    part.fecha_fin = ffin;
    part.fecha_inicio = fini;
    actualizar_lista_part(part);
    actualizarTareas(part.nro_orden);
}

function change_ffin(nro_orden){
    var part = obtener_partida(nro_orden);
    var id = part.id_partida;
    var ffin = $("#"+id+" td").find("input[name=fecha_fin]").val();
    var dias = restarFechas(part.fecha_inicio, ffin);

    console.log('change_ffin dias:'+dias);
    part.dias = dias;
    part.fecha_fin = ffin;
    actualizar_lista_part(part);
    actualizarTareas(part.nro_orden);
}

function actualizarTareas(nro_orden){
    var pred = '';
    var obj = '';
    var actual = obtener_partida(nro_orden);
    // var actual = partidas.find(element => element.nro_orden == nro_orden);
    var ffin = suma_fecha(actual.dias_pos, actual.fecha_fin);
    var lista = [];
    listar_partidas();

    do {
        if (lista.length > 0){
            //si la lista tiene datos, actualizo mis variables locales
            nro_orden = lista[0];
        }
        if (nro_orden !== undefined){
            partidas.forEach(function(value) {
                //obtiene predecesora
                pred = value.predecesora.split(";");
                obj = pred.find(o => o == nro_orden);
                //si existe, actualizo value
                if (obj !== undefined){
                    //obtener fecha_fin mayor
                    pred.forEach(function(p){
                        var part = obtener_partida(p);
                        var fin = suma_fecha(value.dias_pos, part.fecha_fin);
                        if (fin > ffin){
                            ffin = fin;
                        }
                    });
                    value.fecha_inicio = ffin;
                    value.fecha_fin = suma_fecha(value.dias, ffin);
                    //agrego nro_orden a la lista
                    if (!lista.includes(value.nro_orden)){
                        lista.push(value.nro_orden);
                    }
                }
            });
            const index = lista.indexOf(nro_orden);
            if (index > -1) {
                //borro de la lista nro_orden usado
                lista.splice(index, 1);
            }
        } else {
            lista = [];
        }
    } while (lista.length > 0);
    actualizar_lista();
    mostrar_lista();
}

function handleKeyPress(event){
    var admitidos = ['1','2',"3",'4','5','6','7','8','9','0',';']
    if (!admitidos.includes(event.key)){
        event.returnValue = false;
    }
}

function change_predecesora(nro_orden){
    var actual = obtener_partida(nro_orden);
    var id = actual.id_partida;
    var tp_pred = actual.tp_predecesora;
    var predecesoras = $("#"+id+" td").find("input[name=predecesora]").val();
    actual.predecesora = predecesoras;
    actualiza_predecesoras(predecesoras, tp_pred, actual);
}

function change_tp_predecesora(nro_orden){
    var actual = obtener_partida(nro_orden);
    var id = actual.id_partida;
    var predecesoras = actual.predecesora;
    var tp_pred = $("#"+id+" td").find("select[name=tp_predecesora]").val();
    actual.tp_predecesora = tp_pred;
    actualiza_predecesoras(predecesoras, tp_pred, actual);
}

function actualiza_predecesoras(predecesoras, tp_pred, actual){
    console.log(predecesoras);
    console.log(tp_pred);

    if (predecesoras !== '' && predecesoras !== '0'){
        var array_pred = predecesoras.split(";");
        console.log(array_pred);
        if (array_pred !== undefined && array_pred.length > 0){
            // var id_pred = '';
            var ffin = null;
            var fini = null;
            var part;
            var inicio;
            listar_partidas();

            switch(tp_pred){
                case 1:
                    //Fin a comienzo
                    array_pred.forEach(function(p){
                        part = partidas.find(element => element.nro_orden == String(p));
                        if (part !== undefined){
                            inicio = suma_fecha(parseFloat(actual.dias_pos), part.fecha_fin);
                            if (fini == null){
                                fini = inicio;
                            } else if (inicio > fini){//la mayor
                                fini = inicio;
                            }
                        }
                    });
                    if (fini !== null){
                        actual.fecha_inicio = fini;
                        actual.fecha_fin = suma_fecha(actual.dias, fini);
                        actualizar_lista_part(actual);
                        actualizarTareas(actual.nro_orden);
                    }
                    break;
                case 2:
                    //Comienzo a comienzo
                    array_pred.forEach(function(p){
                        part = partidas.find(element => element.nro_orden == String(p));
                        inicio = suma_fecha(parseFloat(actual.dias_pos), part.fecha_inicio);
                        if (fini == null){
                            fini = inicio;
                        } else if (inicio < fini){//la menor
                            fini = inicio;
                        }
                    });
                    if (fini !== null){
                        actual.fecha_inicio = fini;
                        actual.fecha_fin = suma_fecha(parseFloat(actual.dias), fini);
                        actualizar_lista_part(actual);
                        actualizarTareas(actual.nro_orden);
                    }
                    break;
                case 3:
                    //Fin a fin
                    array_pred.forEach(function(p){
                        part = partidas.find(element => element.nro_orden == p);
                        if (ffin == null){
                            ffin = part.fecha_fin;
                        } else if (part.fecha_fin > ffin){//la mayor
                            ffin = part.fecha_fin;
                        }
                    });
                    if (ffin !== null){
                        actual.fecha_inicio = suma_fecha(-parseFloat(actual.dias), ffin);
                        actualizar_lista_part(actual);
                        actualizarTareas(actual.nro_orden);
                    }
                    break;
                case 4:
                    //Comienzo a fin
                    array_pred.forEach(function(p){
                        part = partidas.find(element => element.nro_orden == p);
                        inicio = suma_fecha(parseFloat(actual.dias_pos), part.fecha_fin);
                        if (fini == null){
                            fini = inicio;
                        } else if (inicio > fini){//la mayor
                            fini = inicio;
                        }
                    });
                    if (fini !== null){
                        actual.fecha_inicio = fini;
                        actual.fecha_fin = suma_fecha(actual.dias, fini);
                        actualizar_lista_part(actual);
                        actualizarTareas(actual.nro_orden);
                    }
                    break;
                default:
                    break; 
            }
        }
    }
}

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}

//Ver Diagrama de Gantt

var tasks = '';
var isEnabled = false;

function mostrar_gant_propuesta(id_presupuesto){
    var _data = [];
    var _links = [];
    console.log('id_presupuesto: '+id_presupuesto);

    $.ajax({
        type: 'GET',
        url: 'ver_gant_propuesta/'+id_presupuesto,
        dataType: 'JSON',
        success: function(response){
            var i = 0;
            var j = 0;

            response['titulos'].forEach(function(element){
                i++;
                var fini = '';
                var ffin = null;
                var dura = 0;
                var parti = [];
                
                response['partidas'].forEach(function(part){
                    if (element.codigo == part.cod_padre){
                        if (ffin == null){
                            ffin = part.fecha_fin;
                        } else {
                            if (ffin < part.fecha_fin){
                                ffin = part.fecha_fin;
                            }
                        }
                        if (fini == ''){
                            fini = format2Date(part.fecha_inicio);
                        }
                        j++;
                        var p = {
                            id: i+j, 
                            text: part.descripcion, 
                            start_date: format2Date(part.fecha_inicio), 
                            end_date: format2Date(part.fecha_fin), 
                            duration: restarFechas(part.fecha_inicio, part.fecha_fin), 
                            // order: 10,
                            progress: 1, 
                            open: false,
                            parent: i,
                            type: (part.tp_predecesora == 2 ? "1" : "0"),
                            predecesora: part.predecesora,
                            nro_orden: part.nro_orden
                        }
                        parti.push(p);
                        
                    }
                });
                var padre = _data.find(e => e.nro_orden == element.cod_padre);
                dura = restarFechas(fini, ffin);

                var d = {
                        id: i,
                        text: element.descripcion,
                        start_date: (padre !== undefined ? padre.start_date : fini),
                        end_date: (padre !== undefined ? padre.end_date : ffin),
                        duration: (padre !== undefined ? padre.duration : dura),
                        // order: 10,
                        // progress: 1, 
                        open: true,
                        color: "#34c461",
                        parent: (padre !== undefined ? padre.id : ''),
                        type: "0",
                        predecesora: element.cod_padre,
                        nro_orden: element.codigo
                    }
                _data.push(d);

                parti.forEach(element => {
                    _data.push(element);
                });

                i = i+j;

            });
            var z = _data.length;

            response['partidas'].forEach(function(part){
                if (part.cod_padre == null){
                    z++;
                    var n = {
                        id: z, 
                        text: (part.codigo == 'CI' ? 'Costos Indirectos' : (part.codigo == 'GG' ? 'Gastos Generales' : '')), 
                        start_date: format2Date(part.fecha_inicio), 
                        end_date: format2Date(part.fecha_fin), 
                        duration: restarFechas(part.fecha_inicio, part.fecha_fin), 
                        // order: 10,
                        progress: 1,                         
                        open: false,
                        // parent: i,
                        type: (part.tp_predecesora == 2 ? "1" : "0"),
                        predecesora: part.predecesora,
                        nro_orden: part.nro_orden
                    }
                    _data.push(n);
                }
            });
            console.log(_data);
            var i_link = 1;

            _data.forEach(element => {
                if (element.open == false){
                    //es partida? revisa si esa partida tiene predecesora
                    var pred = element.predecesora.split(";");
                    console.log(pred);

                    if (pred.length > 0){
                        for (var i=0; i < pred.length; i++) {
                            var p = String(pred[i]);
                            if (p !== '0'){
                                var tar = (_data.find(e => e.nro_orden === p));
                                console.log(tar);
                                if (tar !== undefined){
                                    var li = {
                                        id: i_link, 
                                        source: tar.id, 
                                        target: element.id, 
                                        type: (element.tp_predecesora == 2 ? "1" : "0")
                                    }
                                    _links.push(li);
                                    i_link++;
                                }
                            }
                        }
                    }
                }
            });
            console.log(_links);
            rspta = false;
            //es la primera vez?
            if (tasks == ''){
                rspta = true;
                // gantt.config.highlight_critical_path = true;
                gantt.config.work_time = true;
	            gantt.config.details_on_create = false;
                gantt.config.row_height = 30;
                gantt.config.min_column_width = 40;

                gantt.templates.grid_row_class = function( start, end, task ){
                    return "nested_task"
                };
            }

            tasks = {
                data: _data,
                links: _links
            };

            if (rspta){
                gantt.init("gantt_here");
                gantt.parse(tasks);
            } else {
                gantt.parse(tasks);
            }

            changeUnidProgram();

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    console.log('tasks');
    console.log(tasks);

}

function reinit() {
    var id = $('[name=id_presupuesto]').val();
    mostrar_gant_propuesta(id);
}

function changeUnidProgram(){
    var unid = $('[name=unid_program_gantt]').val();

    if (unid !== '' && tasks !== ''){
        gantt.config.scale_unit = unid;
    
        if (unid == 'month'){
            gantt.config.date_scale = "%F, %Y";
        } 
        else if (unid == 'week'){
            gantt.config.date_scale = "%W";
        }
        else {
            gantt.config.date_scale = "%d %M";
        }
        gantt.parse(tasks);
    }
}

var rutas = [];
var ganadora = null;

function calculaRutaCritica(){
    isEnabled = !isEnabled;

    if (isEnabled){
        tasks.data.forEach(function(inicial){
            if ((inicial.predecesora == "0" || inicial.predecesora == null || inicial.predecesora == "") && inicial.open == false){
                rutas.push(inicial);
            }
        });
        
        var rutas_criticas = [];
    
        rutas.forEach(function(element){
            var ruta_probable = [];
            var pred_ini = element.nro_orden;
    
            tasks.data.forEach(function(task){
                if (task.open == false){
                    //revisa si esa partida tiene predecesora
                    var pred = task.predecesora.split(";");
                    if (pred.length > 0){
                        //recorre las predecesoras
                        for (var x=0; x < pred.length; x++) {
                            var p = String(pred[x]);
                            //si la predecesora es != 0
                            if (p !== '0'){
                                if (pred_ini == p){
                                    if (ruta_probable.length == 0){
                                        ruta_probable.push(element);
                                    }
                                    //agrega el task y actualiza la predecesora
                                    ruta_probable.push(task);
                                    pred_ini = task.nro_orden;
                                }
                            }
                        }
                    }
                }
            });
            rutas_criticas.push(ruta_probable);
        });
        
        var suma_ganadora = 0;
        
        for (var i=0; i < rutas_criticas.length; i++) {
            var suma = 0;
            for (var j=0; j < rutas_criticas[i].length; j++){
                suma += rutas_criticas[i][j].duration;
            }
    
            if (suma > suma_ganadora){
                ganadora = rutas_criticas[i];
                suma_ganadora = suma;
            }
        }
    
        console.log(ganadora);
        console.log(suma_ganadora);
    
        //recorre las tareas y le asigna color red
        for (var i=0; i < ganadora.length; i++){
            console.log('id: '+ganadora[i].id);
            tarea = gantt.getTask(ganadora[i].id);
            tarea.color = "#f61a13";
        }
        gantt.parse(tasks);
    }
    else {
        //recorre las tareas y le asigna color blue
        for (var i=0; i < ganadora.length; i++){
            console.log('id: '+ganadora[i].id);
            tarea = gantt.getTask(ganadora[i].id);
            tarea.color = "#3db9d3";
        }
        gantt.parse(tasks);
    }
}
