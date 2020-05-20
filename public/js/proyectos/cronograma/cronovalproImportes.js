let periodo_importes = [];

function openImportesModal(id_pcrono){
    $('#modal-cronovalproImportes').modal({
        show: true
    });
    var sel;
    lista.forEach(element => {
        if (element.partidas !== undefined){
            if (sel == undefined){
                sel = element.partidas.find(part => part.id_pcrono == id_pcrono);
            }
        }
        else {
            if (sel == undefined && element.id_pcrono == id_pcrono){
                sel = element;
            }
        }
    });
    console.log(sel);
    $('#partida').text(sel.codigo+' - '+sel.descripcion+' Total : '+sel.importe_total);
    $('[name=importe_total]').val(sel.importe_total);
    periodo_importes = sel.periodos;
    mostrarPeriodos();    
}

function mostrarPeriodos(){
    var html = '';
    var total = 0;
    periodo_importes.forEach(element => {
        html+='<tr id="'+element.periodo+'">'+
        '<td>'+element.periodo+'° Val.</td>'+
        '<td><input type="number" class="form-control right" name="porcentaje" onBlur="changePorcentaje('+element.periodo+');" value="'+formatDecimalDigitos(element.porcentaje,0)+'" /></td>'+
        '<td><input type="number" class="form-control right" name="importe" onBlur="changeImporte('+element.periodo+');" value="'+formatDecimal(element.importe)+'" /></td>'+
        '</tr>';
        total +=parseFloat(element.importe);
    });
    $('#importes tbody').html(html);
    var html_foot = '<tr>'+
    '<td></td>'+
    '<th class="right">Total</th>'+
    '<th class="right">'+formatNumber.decimal(total,'',-2)+'</th>'+
    '</tr>';
    $('#importes tfoot').html(html_foot);
}

function changePorcentaje(periodo){
    var por = $("#"+periodo+" td").find("input[name=porcentaje]").val();
    var tot = $("[name=importe_total]").val();
    if (por >= 0){
        var imp = parseFloat(por) * parseFloat(tot) / 100;
        var p = periodo_importes.find(per => per.periodo == periodo);
        p.importe = imp;
        p.porcentaje = por;
        $("#"+periodo+" td").find("input[name=importe]").val(formatDecimal(imp));
        changeTotal();
    } else {
        alert('El porcentaje debe ser mayor a cero');
    }
}

function changeImporte(periodo){
    var imp = $("#"+periodo+" td").find("input[name=importe]").val();
    var tot = $("[name=importe_total]").val();
    if (imp >= 0){
        var por = parseFloat(imp) * 100 / parseFloat(tot);
        var p = periodo_importes.find(per => per.periodo == periodo);
        p.porcentaje = por;
        p.importe = imp;
        $("#"+periodo+" td").find("input[name=porcentaje]").val(formatDecimalDigitos(por,0));
        changeTotal();
    } else {
        alert('El porcentaje debe ser mayor a cero');
    }
}

function changeTotal(){
    var total = 0;
    periodo_importes.forEach(element => {
        total += parseFloat(element.importe);
    });
    var html_foot = '<tr>'+
    '<td></td>'+
    '<th class="right">Total</th>'+
    '<th class="right">'+formatNumber.decimal(total,'',-2)+'</th>'+
    '</tr>';
    $('#importes tfoot').html(html_foot);
}

function copiarPeriodos(){
    $('#modal-cronovalproImportes').modal('hide');
    mostrar_tabla();
}