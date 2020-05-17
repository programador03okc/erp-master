// import { normalize } from "path";
// $(function(){
//     console.log('function');
//     $('[name=serie_prod]').focus();
//     $('[name=serie_prod]').attr('autofocus','true');
// });

function agrega_series(id_guia_det, descripcion){
    var canti = $("#reg-"+id_guia_det+" td").find("input[name=cantidad]").val();
    console.log("agrega_series()");
    $('#modal-guia_com_barras').modal({
        show: true
    });
    // clearDataTable();
    listarSeries(id_guia_det);
    $('[name=id_guia_det]').val(id_guia_det);
    $('[name=cant_items]').val(canti);
    $('#descripcion').text(descripcion);
    $('[name=serie_prod]').val('');
    $('#listaBarras tbody').val('');
    
}
function listarSeries(id_guia_det){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_series/'+id_guia_det,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var tr = '';
            for (var i=0;i<response.length;i++){
                tr+='<tr id="reg-'+response[i].serie+'"><td hidden>'+response[i].id_prod_serie+'</td><td class="numero">'+(i+1)+'</td><td><input type="text" class="oculto" name="series" value="'+response[i].serie+'"/>'+response[i].serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('+"'"+response[i].serie+"'"+');"></i></td></tr>';
            }
            $('#listaBarras tbody').html(tr);
            $('[name=serie_prod]').focus();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function handleKeyPress(event){
    console.log(event);
    console.log('key:'+event.which);
    var exeptuados = ['/','"',"'",'*','+','#','$','%','&','(',')','=','?','¿','¡','!','.','¨','^','´','`','_',',',';','>','<','|','°','¬']
    if (event.which == 13) {
        agregar_serie();
    } else if (exeptuados.includes(event.key)){
        var valor = $('[name=serie_prod]').val();
        console.log('1 valor:'+valor);
        valor = valor.substring(0,valor.length-1);
        console.log('2 valor:'+valor);
        $('[name=serie_prod]').val(valor);
        // event.returnValue = false;
        alert('Valor No Permitido: '+valor);
    }
}
function agregar_serie(){
    var serie = $('[name=serie_prod]').val().trim();
    if (serie !== '') {
        var items = $('[name=cant_items]').val();
        var cant = $('#listaBarras tbody tr').length + 1;
        var td = '<tr id="reg-'+serie+'"><td hidden>0</td><td class="numero">'+cant+'</td><td><input type="text" class="oculto" name="series" value="'+serie+'"/>'+serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('+"'"+serie+"'"+');"></i></td></tr>';
        console.log('cant:'+cant+' items:'+items);
        if (cant <= items){
            $('#listaBarras tbody').append(td);
            $('[name=serie_prod]').val('');
        } else {
            alert('Ha superado la cantidad del producto!\nYa no puede agregar mas series.');
        }
    } else {
        alert('El campo serie esta vacío!');
    }
}

function eliminar_serie(serie){
    var elimina = confirm("¿Esta seguro que desea eliminar la serie "+serie);
    if (elimina){
        var id = $("#reg-"+serie)[0].firstChild.innerHTML;
        if (id !== '0'){
            var a = $('[name=anulados]').val();
            if (a == ''){
                a +=id;
            } else {
                a +=','+id;
            }
            $('[name=anulados]').val(a);
        }
        $("#reg-"+serie).remove();

        var i = 1;
        $(".numero").each(function(){
            console.log('dentro');
            console.log($(this).html());
            $(this).html(i);
            i++;
        });
    }
}

function guardar_series(){
    var guarda = confirm("¿Esta seguro que desea guardar las serie(s)?");
    if (guarda){
        var series = new Array();
        $('input[name*="series"]').each(function(){
            var id = $(this).parents("tr").find("td").eq(0).text();
            console.log('id: '+id);
            if (id == '0'){
                // var val = $(this).parents("tr").find("td").eq(1).text();
                // console.log('val:'+val);
                console.log($(this).val());
                series.push($(this).val());
            }
        });
        
        var id_guia_det = $('[name=id_guia_det]').val();
        var anulados = $('[name=anulados]').val();
    
        var data = 'id_guia_det='+id_guia_det+'&series='+series+'&anulados='+anulados;
        console.log(data);

        $.ajax({
            type: 'POST',
            url: 'guardar_series',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log('response'+response);
                if (response > 0){
                    alert('Series registradas con éxito');
                    var id_guia = $('[name=id_guia]').val();
                    if (id_guia !== ''){
                        listar_detalle(id_guia);
                    }
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

        $('#modal-guia_com_barras').modal('hide');
        var items = $('[name=cant_items]').val();
        var cant = $('#listaBarras tbody tr').length;
        console.log('items'+items+' cant'+cant);
        if (cant > items){
            var rspta = confirm("¿Desea actualizar la cantidad del detalle?");
            if (rspta == true){
                var id_guia_det = $('[name=id_guia_det]').val();
                console.log('id_guia'+id_guia_det+' cant'+cant);
                console.log($("#reg-"+id_guia_det+" ").find('td').eq(4).find('input').val());
                // $("#reg-"+id_guia_det+" td").find("input[name=cantidad]").removeAttr('disabled');
                editar_detalle(id_guia_det, 1);
                $('#reg-'+id_guia_det+' input[name=cantidad]').val(cant);
            }
        }
    }
}

// $('#reset').on('click', function(e) {
    // var $el = $('#importar');
    // $el.wrap('<form>').closest('form').get(0).reset();
    // $el.unwrap();
//     $('#importar').replaceWith( $('#importar').val('').clone( true ) );
//  });

function limpiar_serie(){
    // $('#importar').replaceWith( $('#importar').val('').clone( true ) );
    // var $el = $('#importar');
    // $el.wrap('<form>').closest('form').get(0).reset();
    // $el.unwrap();
    var $el = $('#importar');
    $el.wrap('<form>').closest('#frm-example').get(0).reset();
    $el.unwrap();
}

$(function(){
    document.getElementById('importar').addEventListener("change", function(e) {
        var files = e.target.files,file;
        if (!files || files.length == 0) return;
        file = files[0];
        var fileReader = new FileReader();
        fileReader.onload = function (e) {
            var filename = file.name;
            // pre-process data
            var binary = "";
            var bytes = new Uint8Array(e.target.result);
            var length = bytes.byteLength;
            for (var i = 0; i < length; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            // call 'xlsx' to read the file
            var oFile = XLSX.read(binary, {type: 'binary', cellDates:true, cellStyles:true});
            var result = {};
            oFile.SheetNames.forEach(function(sheetName) {
                var roa = XLS.utils.sheet_to_row_object_array(oFile.Sheets[sheetName]);
                if(roa.length > 0){
                result[sheetName] = roa;
                }
            });
            var td = '';
            var i = 0;
            var items = $('[name=cant_items]').val();
            var cant = $('#listaBarras tbody tr').length;
            var msj = false;
            var imp = cant + result.Hoja1.length;
            console.log('items'+items+' imp'+imp);
            var rspta = false;

            if (imp > items){
                rspta = confirm('Las series importadas superan la cantidad. ¿Desea agregarlas de todos modos?');
            }

            for(i=0;i<result.Hoja1.length;i++){
                console.log(result.Hoja1[i].serie);
                cant++;
                td = '<tr id="reg-'+result.Hoja1[i].serie+'"><td hidden>0</td><td class="numero">'+cant+'</td><td><input type="text" class="oculto" name="series" value="'+result.Hoja1[i].serie+'"/>'+result.Hoja1[i].serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg " onClick="eliminar_serie('+result.Hoja1[i].serie+');"></i></td></tr>';
                if (rspta){
                    $('#listaBarras tbody').append(td);
                } else {
                    if (cant <= items){
                        $('#listaBarras tbody').append(td);
                    } else {
                        msj = true;
                    }
                }
            }
            if (msj){
                alert('No se cargaron todas las series porque superan a la cantidad del producto.');
            }
        };
        fileReader.readAsArrayBuffer(file);
    });
});
