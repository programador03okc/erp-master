let json_series = [];
let cant_items = null;

function agrega_series(id_oc_det){
    $('#modal-guia_com_barras').modal({
        show: true
    });
    var json = oc_det_seleccionadas.find(element => element.id_oc_det == id_oc_det);
    console.log(json);
    
    if (json !== null){
        if (json.series.length > 0){
            json_series = json.series;
            cargar_series();
        }
    }

    // listarSeries(id_guia_det);
    cant_items = $("#"+id_oc_det+" td").find("input[id=cantidad]").val();

    $('[name=id_oc_det]').val(id_oc_det);
    // $('#descripcion').text(descripcion);
    $('[name=serie_prod]').val('');
    $('#listaBarras tbody').val('');
    
}

function cargar_series(){
    var tr = '';
    var i = 1;
    json_series.forEach(serie => {
        tr+=`<tr id="reg-${serie}">
                <td hidden>0</td>
                <td class="numero">${i}</td>
                <td><input type="text" class="oculto" name="series" value="${serie}"/>${serie}</td>
                <td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('${serie}');"></i></td>
            </tr>`;
        i++;
    });
    $('#listaBarras tbody').html(tr);
    $('[name=serie_prod]').focus();
}

function handleKeyPress(event){
    console.log(event);
    console.log('key:'+event.which);

    var exeptuados = ['/','"',"'",'*','+','#','$','%','&','(',')','=','?','¿','¡','!','.','¨','^','´','`','_',',',';','>','<','|','°','¬'];

    if (event.which == 13) {
        agregar_serie();
    } 
    else if (exeptuados.includes(event.key)){
        var valor = $('[name=serie_prod]').val();
        valor = valor.substring(0,valor.length-1);

        $('[name=serie_prod]').val(valor);
        // event.returnValue = false;
        alert('Valor No Permitido: '+valor);
    }
}

function agregar_serie(){
    var serie = $('[name=serie_prod]').val().trim();
    
    if (serie !== '') {
        var cant = $('#listaBarras tbody tr').length + 1;
        var td = '<tr id="reg-'+serie+'"><td hidden>0</td><td class="numero">'+cant+'</td><td><input type="text" class="oculto" name="series" value="'+serie+'"/>'+serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('+"'"+serie+"'"+');"></i></td></tr>';
        console.log('cant:'+cant+' items:'+cant_items);
        
        if (cant <= cant_items){
            $('#listaBarras tbody').append(td);
            $('[name=serie_prod]').val('');
            // var id_oc_det = $('[name=id_oc_det]').val();
            json_series.push(serie);
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
        var index = json_series.findIndex(function(item, i){
            return item == serie;
        });
        console.log(json_series);
        json_series.splice(index,1);
        cargar_series();
    }
}

function guardar_series(){
    var id_oc_det = $('[name=id_oc_det]').val();
    var json = oc_det_seleccionadas.find(element => element.id_oc_det == id_oc_det);
    
    if (json !== null){
        json.series = json_series;
    }
    console.log(json);
    console.log(oc_det_seleccionadas);
    $('#modal-guia_com_barras').modal('hide');
}

$(document).ready(function(){
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
            var items = cant_items;
            var cant = $('#listaBarras tbody tr').length;
            var msj = false;
            var imp = cant + result.Hoja1.length;
            console.log('items'+items+' imp'+imp+' length'+result.Hoja1.length);
            console.log(result.Hoja1);
            var rspta = true;

            if (imp > items){
                rspta = confirm('Las series importadas superan la cantidad. Solo se agregaran hasta que complete la cantidad de '+cant_items+'. ¿Desea continuar?');
                // rspta = confirm('Las series importadas superan la cantidad. ¿Desea agregarlas de todos modos?');
            }
            if (rspta){
                for(i=0;i<result.Hoja1.length;i++){
                    console.log(result.Hoja1[i].serie);
                    cant++;
                    td = '<tr id="reg-'+result.Hoja1[i].serie+'"><td hidden>0</td><td class="numero">'+cant+'</td><td><input type="text" class="oculto" name="series" value="'+result.Hoja1[i].serie+'"/>'+result.Hoja1[i].serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg " onClick="eliminar_serie('+result.Hoja1[i].serie+');"></i></td></tr>';
                    // if (rspta){
                    //     $('#listaBarras tbody').append(td);
                    // } else {
                        if (cant <= items){
                            $('#listaBarras tbody').append(td);
                        } else {
                            msj = true;
                        }
                    // }
                }
                if (msj){
                    alert('No se cargaron todas las series porque superan a la cantidad del producto.');
                }
            }
        };
        fileReader.readAsArrayBuffer(file);
    });
});