let json_series = [];
let cant_items = null;

function agrega_series(id_oc_det){
    $('#modal-guia_com_barras').modal({
        show: true
    });
    $('#listaBarras tbody').html('');
    json_series = [];

    var json = oc_det_seleccionadas.find(element => element.id_oc_det == id_oc_det);
    console.log(json);
    
    if (json !== null){
        if (json.series.length > 0){
            json_series = json.series;
            cargar_series();
        }
    }

    cant_items = $("#"+id_oc_det+"cantidad").val();

    $('[name=id_oc_det]').val(id_oc_det);
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_producto]').val('');
    $('[name=serie_prod]').val('');
    $('#cabecera').show();
}

function agrega_series_transformacion(id){
    console.log('agrega_series_transformacion');
    $('#modal-guia_com_barras').modal({
        show: true
    });
    $('#listaBarras tbody').html('');
    json_series = [];

    var json = series_transformacion.find(element => element.id == id);
    console.log(json);
    
    if (json !== null){
        if (json.series.length > 0){
            json_series = json.series;
            cargar_series();
        }
    }
    cant_items = (json !== null ? json.cantidad : 0);

    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val(id);
    $('[name=id_producto]').val('');
    $('[name=serie_prod]').val('');
    $('#cabecera').show();
}

function agrega_series_producto(id){
    console.log('agrega_series_producto'+id);
    $('#modal-guia_com_barras').modal({
        show: true
    });
    $('#listaBarras tbody').html('');
    json_series = [];

    var json = oc_det_seleccionadas.find(element => element.id_producto == id);
    console.log(json);
    
    if (json !== null){
        
        if (json.series.length > 0){
            json_series = json.series;
            cargar_series();
        }
    }

    cant_items = $("#p"+id+"cantidad").val();

    $('[name=id_oc_det]').val('');
    $('[name=id_detalle_transformacion]').val('');
    $('[name=id_producto]').val(id);
    $('[name=serie_prod]').val('');
    $('#cabecera').show();
}

function cargar_series(){
    var tr = '';
    var i = 1;
    
    json_series.forEach(serie => {
        
        tr +=`<tr id="reg-${serie}">
                <td hidden>0</td>
                <td class="numero">${i}</td>
                <td><input type="text" class="oculto" name="series" value="${serie}"/>${serie}</td>
                <td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('${serie}');"></i></td>
            </tr>`;
        i++;
    });
    console.log(tr);
    $('#listaBarras tbody').html(tr);
    $('[name=serie_prod]').focus();
}

function handleKeyPress(event){
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
        
        var agrega = false;

        if (json_series.length > 0){
            const found = json_series.find(element => element == serie);
            console.log('found'+found);

            if (found == undefined){
                agrega = true;
            }
        } else {
            agrega = true;
        }
            
        if (agrega){
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
            $('[name=serie_prod]').val('');
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
    var id_guia_com_det = $('[name=id_guia_com_det]').val();
    var id_oc_det = $('[name=id_oc_det]').val();
    var id_producto = $('[name=id_producto]').val();
    var id_detalle_transformacion = $('[name=id_detalle_transformacion]').val();
    
    if (id_oc_det !== ''){
        var json = oc_det_seleccionadas.find(element => element.id_oc_det == id_oc_det);
        
        if (json !== null){
            json.series = json_series;
        }
        console.log(json);
        console.log(oc_det_seleccionadas);
        mostrar_ordenes_seleccionadas();
    }
    else if (id_detalle_transformacion !== ''){
        var json = series_transformacion.find(element => element.id == id_detalle_transformacion);
        
        if (json !== null){
            json.series = json_series;
        }
        console.log(json);
        console.log(series_transformacion);
        mostrar_detalle_transformacion();
    }
    else if (id_producto !== ''){
        var json = oc_det_seleccionadas.find(element => element.id_producto == id_producto);
        
        if (json !== null){
            json.series = json_series;
        }
        console.log(json);
        console.log(oc_det_seleccionadas);
        mostrar_ordenes_seleccionadas();
    }
    else if (id_guia_com_det !== ''){
        let series = [];
        $('#listaBarras tbody tr').each(function(index) {
            let serie = $(this).find('[name=series]').val();
            let id = $(this)[0].id;
            series.push({
                'id_guia_com_det':id_guia_com_det,
                'id_prod_serie':id,
                'serie':serie
            });
        });
        var data = 'series='+JSON.stringify(series);
        console.log(data);
        $.ajax({
            type: 'POST',
            url: 'actualizar_series',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var id = $('[name=id_guia_com_detalle]').val();
                listar_detalle_movimiento(id);
                alert('Se actualizaron las series con éxito.');
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
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
                for(i=0; i<result.Hoja1.length; i++){
                    console.log(result.Hoja1[i].serie);
                    var serie = result.Hoja1[i].serie;
                    var agrega = false;

                    if (json_series.length > 0){
                        const found = json_series.find(element => element == serie);
                        console.log('found'+found);
                        
                        if (found == undefined){
                            agrega = true;
                        }
                    } else {
                        agrega = true;
                    }

                    if (agrega){
                        cant++;
                        td = '<tr id="reg-'+serie+'"><td hidden>0</td><td class="numero">'+cant+'</td><td><input type="text" class="oculto" name="series" value="'+serie+'"/>'+serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg " onClick="eliminar_serie('+serie+');"></i></td></tr>';
                        if (cant <= items){
                            $('#listaBarras tbody').append(td);
                            json_series.push(serie);
                        } else {
                            msj = true;
                        }
                    }
                }
                if (msj){
                    alert('No se cargaron todas las series porque superan a la cantidad del producto.');
                }
            }
        };
        fileReader.readAsArrayBuffer(file);
    });
});