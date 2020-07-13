let insumos = [];
let anulados = [];
let jornal = 8; //revisar que sea un dato ingresable
let id_partida_temporal = null;

function open_acu_partida_create(){
    $('#modal-acu_partida_create').modal({
        show: true
    });
    insumos = [];
    id_partida_temporal = null;
    console.log(data);
    
    $('[name=id_cu_partida_cd]').val('');
    $('[name=id_cu]').val('');
    $('[name=cod_acu]').val('');
    $('[name=des_acu]').val('');
    $('[name=rendimiento]').val('');
    $('[name=unid_medida_cu]').val('');
    $('[name=id_categoria]').val('');
    $('[name=total_acu]').val('');
    $('[name=observacion]').val('');
    limpiar_nuevo_cu();
    $('#AcuInsumos tbody').html('');
    
}

function listar_acu_detalle(id){
    $.ajax({
        type: 'GET',
        url: 'listar_acu_detalle/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            response.forEach(element => {
                //agregar item a la coleccion
                let item = {
                    'id_cu_detalle':element.id_cu_detalle,
                    'id_insumo':element.id_insumo,
                    'codigo':element.codigo,
                    'descripcion':element.descripcion,
                    'tp_insumo':element.tp_insumo,
                    // 'id_precio':element.id_precio,
                    'cod_tp_insumo':element.cod_tp_insumo,
                    'unidad':element.abreviatura.trim(),
                    'cuadrilla':element.cuadrilla,
                    'cantidad':element.cantidad,
                    'unitario':element.precio_unit,
                    'total':element.precio_total,
                    'nro':null,
                }
                insumos.push(item);
            });
            listar_insumos();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_acu(){
    var id = $('[name=id_cu_partida_cd]').val();
    var cu = $('[name=id_cu]').val();
    var ren = $('[name=rendimiento]').val();
    var und = $('[name=unid_medida_cu]').val();
    var tot = parseFloat($('[name=total_acu]').val());

    var id_det = [];
    var id_insumo = [];
    var cuadrilla = [];
    var cantidad = [];
    var unitario = [];
    var total = [];
    
    insumos.forEach(element => {
        id_det.push(element.id_cu_detalle);
        id_insumo.push(element.id_insumo);
        cuadrilla.push((element.cuadrilla !== '' ? element.cuadrilla : 0));
        cantidad.push(element.cantidad);
        unitario.push(element.unitario);
        total.push(element.total);
    });
    
    var datax = 'id_cu_partida='+id+
            '&id_cu='+cu+
            '&rendimiento='+ren+
            '&unid_medida='+und+
            '&total_acu='+tot+
            '&insumos='+JSON.stringify(insumos)+
            // '&id_det='+id_det+
            // '&id_insumo='+id_insumo+
            // '&cuadrilla='+cuadrilla+
            // '&cantidad='+cantidad+
            // '&unitario='+unitario+
            // '&total='+total+
            '&det_eliminados='+anulados;

    console.log(datax);
    var baseUrl;
    if (id !== ''){
        baseUrl = 'actualizar_acu';
    } else {
        baseUrl = 'guardar_acu';
    }
    console.log(baseUrl);
    var msj = verificaAcu();
    if (msj.length > 0){
        alert(msj);
    } 
    else {
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: datax,
            dataType: 'JSON',
            success: function(response){
                console.log(response);

                if (response['id_cu_partida'] > 0){
                    alert('Costo Unitario registrado con éxito');
                    let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');    
                    
                    if (formName =='presint' || formName =='preseje'){
                        console.log('id_partida_temporal: '+id_partida_temporal);
                        
                        if (id_partida_temporal !== null){
                            editar_partida_cd(id_partida_temporal);
                            $("#par-"+id_partida_temporal+" td").find("input[name=importe_unitario]").val(tot);
                            calcula_total_cd(id_partida_temporal);
                            update_partida_cd(id_partida_temporal);
                        } else {
                            if (response['partida'] !== null){
                                $('[name=id_cu_partida_cd]').val(response['partida'].id_cu_partida);
                                $('[name=cod_cu]').val(response['partida'].codigo);
                                $('[name=des_cu]').val(response['partida'].descripcion);
                                $('[name=precio_unitario]').val(response['partida'].total);
                                $('[name=unid_medida]').val(response['partida'].abreviatura);
                                $('[name=id_unid_medida]').val(response['partida'].unid_medida);
                            }
                        }
                    } else {
                        $('#listaAcu').DataTable().ajax.reload();
                    }
                    $('#modal-acu_partida_create').modal('hide');
                } else {
                    alert('Ocurrio un error interno!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function calculaCantidad(){
    let tipo = $('[name=tp_insumo]').val();
    let rend = $('[name=rendimiento]').val();
    
    if (rend !== null && rend !== ""){
        if (tipo !== null && tipo !== ""){
            let cuad = $('[name=cuadrilla]').val();
            if (cuad > 0){
                // if (cuad == 'MI'){//MATERIALES
                //     cant = cuad;
                // } else {
                    cant = ((cuad * jornal)/rend).toFixed(6);
                // }
                console.log('cantidad ' + cant);
                $('[name=cantidad_cu]').val(cant);
                calculaPrecioTotalCU();
            } else {
                alert('La cuadrilla debe ser > 0');
                $('[name=cuadrilla]').val('');
            }
        } else {
            alert("Es necesario que seleccione un Insumo");
        }    
    } else {
        alert("Es necesario que ingrese un Rendimiento");
        $('[name=cuadrilla]').val('');
    }
}

function calculaPrecioTotalCU(){
    console.log('calculaPrecioTotalCU');
    var cant = $('[name=cantidad_cu]').val();
    var unit = $('[name=precio_unitario_cu]').val();
    var id_unid = $('[name=id_unidad_medida]').val();
    let precio_tot = 0;
    
    if (cant !== null && unit !== null){
        if (id_unid == 6){// SI ES %MO
            precio_tot = (cant * unit / 100).toFixed(6);
        } else {
            precio_tot = (cant * unit).toFixed(6);//convierte a 6 decimales
        }    
    }
    $('[name=precio_total_cu]').val(precio_tot);
}

function actualizaTotal(){
    var total = 0;
    insumos.forEach(element => {
        total += parseFloat(element.total);
    });
    console.log('total: '+total);
    $('[name=total_acu]').val(formatDecimalDigitos(total,4));
}

function agregar(){
    var id = $('[name=id_insumo]').val();
    var cod = $('[name=cod_insumo]').val();
    var des = $('[name=des_insumo]').val();
    var tipo = $('[name=tp_insumo]').val();
    var unid = $('[name=unidad]').val();
    // var prec = $('[name=id_precio]').val();
    var cuad = $('[name=cuadrilla]').val();
    var cant = $('[name=cantidad_cu]').val();
    var unit = $('[name=precio_unitario_cu]').val();
    var tot = $('[name=precio_total_cu]').val();
    if (cuad == ''){
        cuad = '0';
    }
    var filas = document.querySelectorAll('#AcuInsumos tbody tr');
    var nro = filas.length + 1;

    //agregar item a la coleccion
    let item = {
        'id_cu_detalle':0,
        'id_insumo':id,
        'codigo':cod,
        'descripcion':des,
        'cod_tp_insumo':tipo,
        'unidad':unid.trim(),
        'cuadrilla':cuad,
        'cantidad':cant,
        'unitario':unit,
        // 'id_precio':prec,
        'total':tot,
        'nro':nro,
    }
    insumos.push(item);
    console.log(insumos);

    if (tot !== ''){
        listar_insumos();
        actualizaTotal();
        limpiar_nuevo_cu();
    } else {
        alert('El Total no puede estar vacío!');
    }
}

function listar_insumos(){
    $('#AcuInsumos tbody').html('');
    insumos.sort(function(a, b) {
        if (a.cod_tp_insumo > b.cod_tp_insumo) {
            return 1;
        }
        if (a.cod_tp_insumo < b.cod_tp_insumo) {
            return -1;
        }
        return 0;
    });
    var total_mo = 0;
    insumos.forEach(element => {
        if (element.cod_tp_insumo == 'MO'){
            total_mo += parseFloat(element.total);
        }
    });

    insumos.forEach(element => {
        // var id = (element.nro !== null ? 'n'+element.id_insumo : element.id_insumo);
        if (element.unidad == "%mo"){
            element.unitario = total_mo.toFixed(6);
            element.total = (total_mo * element.cantidad / 100).toFixed(6);
        }
        var fila = '<tr id="'+element.id_cu_detalle+'">'+
        '<td>'+element.codigo+'</td>'+
        '<td>'+element.descripcion+'</td>'+
        '<td>'+element.cod_tp_insumo+'</td>'+
        '<td>'+element.unidad+'</td>'+
        '<td class="right green">'+element.cuadrilla+'</td>'+
        '<td class="right green">'+element.cantidad+'</td>'+
        '<td class="right blue info">'+element.unitario+'</td>'+
        '<td class="right blue info">'+formatNumber.decimal(element.total,'',-6)+'</td>'+
        '<td class="right"><button class="btn btn-primary boton" onClick="editar('+
        (element.id_cu_detalle !== 0 ? element.id_cu_detalle : "'"+'c'+element.codigo+"'")+
        ');" data-toggle="tooltip" data-placement="bottom" title="Editar"><i class="fas fa-edit"></i></button>'+
        '<button class="btn btn-danger boton" onClick="anular('+
        (element.id_cu_detalle !== 0 ? element.id_cu_detalle : "'"+'c'+element.codigo+"'")+
        ');" data-toggle="tooltip" data-placement="bottom" title="Anular"><i class="fas fa-trash-alt"></i></button></td></tr>';
        $('#AcuInsumos tbody').append(fila);
    });
}

function change_rendimiento(){
    $('#AcuInsumos tbody').html('');
    let rend = $('[name=rendimiento]').val();

    if (rend > 0){
        var total_mo = 0;
        insumos.forEach(element => {
            if (element.cod_tp_insumo == 'MO'){
                total_mo += parseFloat(element.total);
            }
        });
        
        var total = 0;
        insumos.forEach(element => {
            var id = (element.nro !== null ? 'n'+element.id_insumo : element.id_insumo);
            if (element.cuadrilla > 0){
                element.cantidad = ((element.cuadrilla * jornal)/rend).toFixed(6);
            }
            if (element.unidad == '%mo'){// SI ES %MO
                element.unitario = total_mo.toFixed(6);
                element.total = (total_mo * element.cantidad / 100).toFixed(6);
            } else {
                element.total = (element.cantidad * element.unitario).toFixed(6);//convierte a 6 decimales
            }
            var fila = '<tr id="'+element.id_cu_detalle+'">'+
            '<td>'+element.codigo+'</td>'+
            '<td>'+element.descripcion+'</td>'+
            '<td>'+element.cod_tp_insumo+'</td>'+
            '<td>'+element.unidad+'</td>'+
            '<td class="right green">'+element.cuadrilla+'</td>'+
            '<td class="right green">'+element.cantidad+'</td>'+
            '<td class="right blue info">'+element.unitario+'</td>'+
            '<td class="right blue info">'+formatNumber.decimal(element.total,'',-6)+'</td>'+
            '<td class="right"><button class="btn btn-primary boton" onClick="editar('+
            (element.id_cu_detalle !== 0 ? element.id_cu_detalle : "'"+'c'+element.codigo+"'")+
            ');" data-toggle="tooltip" data-placement="bottom" title="Editar"><i class="fas fa-edit"></i></button>'+
            '<button class="btn btn-danger boton" onClick="anular('+
            (element.id_cu_detalle !== 0 ? element.id_cu_detalle : "'"+'c'+element.codigo+"'")+
            ');"><i class="fas fa-trash-alt"></i></button></td></tr>';
            $('#AcuInsumos tbody').append(fila);
            total += parseFloat(element.total);
        });
        $('[name=total_acu]').val(formatDecimalDigitos(total,4));
    } else {
        alert('El rendimiento debe ser mayor a 0');
    }
}

function anular(id){
    var elimina = confirm("¿Esta seguro que desea eliminar éste insumo?");
    if (elimina){
        var o = String(id).charAt(0);
        console.log('primer caracter: '+o);
        if (o !== 'c'){
            var inc = anulados.includes(id);
            if (!inc){
                anulados.push(id);
            }
            console.log('anulados: ');
            console.log(anulados);
            var index = insumos.findIndex(function(item, i){
                return item.id_cu_detalle == id;
            });
            console.log('insumos: ');
            console.log(insumos);
            insumos.splice(index,1);
        } else {
            var index = insumos.findIndex(function(item, i){
                console.log('id '+id);
                console.log(('c'+item.codigo == id));
                return 'c'+item.codigo == id;
            });
            insumos.splice(index,1);
        }
        listar_insumos();
        actualizaTotal();
    }
}

function editar(id){
    var o = String(id).charAt(0);
    console.log('primer caracter: '+o);
    if (o !== 'c'){
        var inc = anulados.includes(id);
        if (!inc){
            anulados.push(id);
        }
        var index = insumos.findIndex(function(item, i){
            $('[name=id_insumo]').val(item.id_insumo);
            $('[name=cod_insumo]').val(item.codigo);
            $('[name=des_insumo]').val(item.descripcion);
            $('[name=tp_insumo]').val(item.cod_tp_insumo);
            $('[name=unidad]').val(item.unidad);
            $('[name=cuadrilla]').val(item.cuadrilla);
            $('[name=cantidad_cu]').val(item.cantidad);
            // $('[name=id_precio]').val(item.id_precio);
            $('[name=precio_unitario_cu]').val(item.unitario);
            $('[name=precio_total_cu]').val(item.total);
            return item.id_cu_detalle == id;
        });
        insumos.splice(index,1);
    } else {
        var index = insumos.findIndex(function(item, i){
            $('[name=id_insumo]').val(item.id_insumo);
            $('[name=cod_insumo]').val(item.codigo);
            $('[name=des_insumo]').val(item.descripcion);
            $('[name=tp_insumo]').val(item.cod_tp_insumo);
            $('[name=unidad]').val(item.unidad);
            $('[name=cuadrilla]').val(item.cuadrilla);
            $('[name=cantidad_cu]').val(item.cantidad);
            // $('[name=id_precio]').val(item.id_precio);
            $('[name=precio_unitario_cu]').val(item.unitario);
            $('[name=precio_total_cu]').val(item.total);
            return 'c'+item.codigo == id;
        });
        insumos.splice(index,1);
    }
    listar_insumos();
    actualizaTotal();
}

function limpiar_nuevo_cu(){
    $('[name=id_insumo]').val("");
    $('[name=cod_insumo]').val("");
    $('[name=des_insumo]').val("");
    $('[name=tp_insumo]').val("");
    $('[name=unidad]').val("");
    $('[name=cuadrilla]').val("");
    $('[name=cantidad_cu]').val("");
    // $('[name=id_precio]').val("");
    $('[name=precio_unitario_cu]').val("");
    $('[name=precio_total_cu]').val("");
}
function unid_abrev(){
    var unidad = $('select[name="unid_medida_cu"] option:selected').text();
    var abreviatura = unidad.split(" - ");
    if (abreviatura.length > 0){
        $('[name=abreviatura]').text(abreviatura[1]+' / jornada');
    } else {
        $('[name=abreviatura]').text("");
    }
}

function verificaAcu(){
    var id_cu = $('[name=id_cu]').val();
    var rendimiento = $('[name=rendimiento]').val();
    var total_acu = $('[name=total_acu]').val();
    var unid_medida = $('[name=unid_medida_cu]').val();
    var msj = '';

    if (id_cu == ''){
        msj+='\n Es necesario que seleccione un A.C.U.';
    }
    if (rendimiento == '' || rendimiento == null){
        msj+='\n Es necesario que ingrese un rendimiento';
    }
    if (total_acu == ''){
        msj+='\n Es necesario que ingrese un importe total';
    }
    if (unid_medida == '0' || unid_medida == null){
        msj+='\n Es necesario que elija una unidad de medida';
    }
    console.log('length: '+insumos.length);
    if (insumos.length == 0){
        msj+='\n Debe ingresar los Insumos del A.C.U.';
    }
    return msj;
}
